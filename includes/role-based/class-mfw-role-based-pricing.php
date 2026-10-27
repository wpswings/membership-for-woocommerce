<?php
/**
 * Member discount pricing for the role-based module: a per-level percentage discount
 * (`discount_percent`, set on the level in the admin UI) applied to WooCommerce product
 * prices, site-wide, for any user holding that level. Independent of the purchase-based
 * flow's pricing/coupon logic, which is untouched.
 *
 * @package    Membership_For_Woocommerce
 * @subpackage Membership_For_Woocommerce/includes/role-based
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Mfw_Role_Based_Pricing.
 */
class Mfw_Role_Based_Pricing {

	/**
	 * @since 3.1.3
	 */
	public static function init() {
		add_filter( 'woocommerce_get_price_html', array( __CLASS__, 'price_html' ), 10, 2 );
		add_action( 'woocommerce_before_calculate_totals', array( __CLASS__, 'apply_cart_discount' ), 20, 1 );
	}

	/**
	 * @since 3.1.3
	 * @param int      $user_id    User id.
	 * @param int|null $product_id Product id, for category-scoped discounts.
	 * @return float 0-100.
	 */
	private static function discount_for( $user_id, $product_id = null ) {
		if ( ! $user_id ) {
			return 0.0;
		}
		return Mfw_Role_Based_Repository::get_user_discount_percent( $user_id, $product_id );
	}

	/**
	 * Shows the member discount on shop/product page price displays: original price struck
	 * through, discounted price, and a small badge — the same visual language WooCommerce
	 * already uses for sale prices (wc_format_sale_price()).
	 *
	 * @since 3.1.3
	 * @param string     $price_html Existing price HTML.
	 * @param WC_Product $product    Product being displayed.
	 * @return string
	 */
	public static function price_html( $price_html, $product ) {

		$user_id = get_current_user_id();

		if ( ! $user_id || ! $product->is_purchasable() ) {
			return $price_html;
		}

		if ( $product->is_type( 'variable' ) ) {
			return self::variable_price_html( $price_html, $product, $user_id );
		}

		$discount = self::discount_for( $user_id, $product->get_id() );

		if ( $discount <= 0 ) {
			return $price_html;
		}

		$current_price = (float) $product->get_price();
		if ( $current_price <= 0 ) {
			return $price_html;
		}

		// The discount applies to the current (possibly sale) price, but the struck-through
		// "original" shown is the regular price, as WooCommerce does for sales.
		$regular_price    = max( $current_price, (float) $product->get_regular_price() );
		$discounted_price = $current_price - ( $current_price * $discount / 100 );

		$html  = wc_format_sale_price(
			wc_get_price_to_display( $product, array( 'price' => $regular_price ) ),
			wc_get_price_to_display( $product, array( 'price' => $discounted_price ) )
		);

		return $html . self::discount_badge( $discount );
	}

	/**
	 * A variable product shows a price range; each variation may get a different discount
	 * (category scoping is resolved through the parent), so discount every variation and
	 * show the resulting range rather than collapsing it to the minimum.
	 *
	 * @since 3.5.0
	 * @param string              $price_html Existing price HTML.
	 * @param WC_Product_Variable $product    Variable product.
	 * @param int                 $user_id    User id.
	 * @return string
	 */
	private static function variable_price_html( $price_html, $product, $user_id ) {

		$prices = $product->get_variation_prices( true );
		if ( empty( $prices['price'] ) ) {
			return $price_html;
		}

		$original   = array();
		$discounted = array();
		$best       = 0.0;

		foreach ( $prices['price'] as $variation_id => $price ) {
			$price    = (float) $price;
			$regular  = max( $price, (float) ( $prices['regular_price'][ $variation_id ] ?? $price ) );
			$discount = self::discount_for( $user_id, $variation_id );

			$original[]   = $regular;
			$discounted[] = $price - ( $price * $discount / 100 );
			$best         = max( $best, $discount );
		}

		if ( $best <= 0 ) {
			return $price_html;
		}

		$min_original   = min( $original );
		$max_original   = max( $original );
		$min_discounted = min( $discounted );
		$max_discounted = max( $discounted );

		$original_html   = $min_original === $max_original ? wc_price( $min_original ) : wc_format_price_range( $min_original, $max_original );
		$discounted_html = $min_discounted === $max_discounted ? wc_price( $min_discounted ) : wc_format_price_range( $min_discounted, $max_discounted );

		$html = '<del aria-hidden="true">' . $original_html . '</del> <ins>' . $discounted_html . '</ins>';

		return $html . self::discount_badge( $best );
	}

	/**
	 * @since 3.5.0
	 * @param float $discount Discount percent.
	 * @return string
	 */
	private static function discount_badge( $discount ) {
		$html  = '<span class="mfw-role-membership-discount-badge">';
		/* translators: %s: discount percentage. */
		$html .= sprintf( esc_html__( '%s%% member discount', 'membership-for-woocommerce' ), rtrim( rtrim( number_format( $discount, 2 ), '0' ), '.' ) );
		$html .= '</span>';
		return $html;
	}

	/**
	 * Applies the discount to cart item prices. Always recalculates from a freshly fetched
	 * product's price (not the cart item's already-mutated price object), so this hook is
	 * safe to run more than once per request — a well-known WooCommerce quirk — without the
	 * discount compounding.
	 *
	 * @since 3.1.3
	 * @param WC_Cart $cart Cart instance.
	 */
	public static function apply_cart_discount( $cart ) {

		if ( is_admin() && ! wp_doing_ajax() ) {
			return;
		}

		$user_id = get_current_user_id();
		if ( ! $user_id ) {
			return;
		}

		foreach ( $cart->get_cart() as $cart_item ) {

			$fresh_product = wc_get_product( $cart_item['data']->get_id() );
			if ( ! $fresh_product ) {
				continue;
			}

			$discount = self::discount_for( $user_id, $fresh_product->get_id() );
			if ( $discount <= 0 ) {
				continue;
			}

			$base_price = (float) $fresh_product->get_price();
			if ( $base_price <= 0 ) {
				continue;
			}

			$cart_item['data']->set_price( $base_price - ( $base_price * $discount / 100 ) );
		}
	}
}
