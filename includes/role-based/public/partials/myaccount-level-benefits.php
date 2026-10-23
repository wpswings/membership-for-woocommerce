<?php
/**
 * Benefits of one role-membership level, shown on the My Account "Membership" tab for the
 * user's current levels and for every level they can switch to.
 *
 * @package    Membership_For_Woocommerce
 * @subpackage Membership_For_Woocommerce/includes/role-based/public/partials
 *
 * @var array $level Level row.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// How many unlocked items to list by name before collapsing the rest into "and N more".
$mfw_benefit_item_limit = 5;

$mfw_benefits = array();

$mfw_discount = (float) $level['discount_percent'];
if ( $mfw_discount > 0 ) {
	$mfw_category_names = array();
	foreach ( array_map( 'absint', (array) json_decode( $level['discount_category_ids'] ?? '', true ) ) as $mfw_category_id ) {
		$mfw_category = get_term( $mfw_category_id, 'product_cat' );
		if ( $mfw_category && ! is_wp_error( $mfw_category ) ) {
			$mfw_category_names[] = $mfw_category->name;
		}
	}

	$mfw_benefits[] = empty( $mfw_category_names )
		/* translators: %s: discount percent. */
		? sprintf( esc_html__( '%s%% off every product', 'membership-for-woocommerce' ), esc_html( wc_format_localized_decimal( $mfw_discount ) ) )
		/* translators: 1: discount percent, 2: comma-separated product category names. */
		: sprintf( esc_html__( '%1$s%% off products in %2$s', 'membership-for-woocommerce' ), esc_html( wc_format_localized_decimal( $mfw_discount ) ), esc_html( implode( ', ', $mfw_category_names ) ) );
}

$mfw_expiry_days = (int) $level['expiry_days'];
$mfw_benefits[]  = $mfw_expiry_days > 0
	/* translators: %d: number of days. */
	? sprintf( esc_html( _n( '%d day of access', '%d days of access', $mfw_expiry_days, 'membership-for-woocommerce' ) ), $mfw_expiry_days )
	: esc_html__( 'Lifetime access', 'membership-for-woocommerce' );

$mfw_unlocked_links = array();
foreach ( Mfw_Role_Based_Repository::get_level_unlocked_objects( $level ) as $mfw_object ) {
	if ( 'term' === $mfw_object['object_type'] ) {
		$mfw_term = get_term( (int) $mfw_object['object_id'] );
		if ( ! $mfw_term || is_wp_error( $mfw_term ) ) {
			continue;
		}
		$mfw_url   = get_term_link( $mfw_term );
		$mfw_title = $mfw_term->name;
	} else {
		if ( 'publish' !== get_post_status( (int) $mfw_object['object_id'] ) ) {
			continue;
		}
		$mfw_url   = get_permalink( (int) $mfw_object['object_id'] );
		$mfw_title = get_the_title( (int) $mfw_object['object_id'] );
	}

	if ( is_wp_error( $mfw_url ) || '' === $mfw_title ) {
		continue;
	}

	$mfw_unlocked_links[] = '<a href="' . esc_url( $mfw_url ) . '">' . esc_html( $mfw_title ) . '</a>';
}

if ( ! empty( $mfw_unlocked_links ) ) {
	$mfw_shown_links = array_slice( $mfw_unlocked_links, 0, $mfw_benefit_item_limit );
	$mfw_more_count  = count( $mfw_unlocked_links ) - count( $mfw_shown_links );

	$mfw_unlocked_text = sprintf(
		/* translators: %s: comma-separated links to members-only content. */
		esc_html__( 'Members-only access to %s', 'membership-for-woocommerce' ),
		implode( ', ', $mfw_shown_links )
	);
	if ( $mfw_more_count > 0 ) {
		/* translators: %d: number of further members-only items. */
		$mfw_unlocked_text .= ' ' . sprintf( esc_html( _n( 'and %d more', 'and %d more', $mfw_more_count, 'membership-for-woocommerce' ) ), $mfw_more_count );
	}
	$mfw_benefits[] = $mfw_unlocked_text;
}
?>
<ul class="mfw-role-level-benefits">
	<?php foreach ( $mfw_benefits as $mfw_benefit ) : ?>
		<li><?php echo wp_kses_post( $mfw_benefit ); ?></li>
	<?php endforeach; ?>
</ul>
