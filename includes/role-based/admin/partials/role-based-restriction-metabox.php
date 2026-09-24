<?php
/**
 * Restriction metabox shown on post/page/product edit screens (WPS-7880).
 *
 * Supports either an explicit list of allowed levels, or a minimum-rank threshold
 * ("anyone whose highest level's rank is at least N") — mirrors the reference plugin's
 * role-hierarchy concept without a full role-ranking UI.
 *
 * @package    Membership_For_Woocommerce
 * @subpackage Membership_For_Woocommerce/includes/role-based/admin/partials
 *
 * @var array[]    $levels             Rows from Mfw_Role_Based_Repository::get_levels().
 * @var string     $object_type        Current post type.
 * @var array|null $restriction        Mfw_Role_Based_Repository::get_restriction() result.
 * @var string     $restricted_message Per-object override of the restricted-content message (post meta).
 * @var string     $nonce              AJAX nonce for Mfw_Role_Based_Admin::NONCE_ACTION.
 * @var WP_Post    $post               Current post (in scope from the calling render method).
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$allowed_level_ids = $restriction ? array_map( 'absint', $restriction['level_ids'] ) : array();
$min_rank          = $restriction ? $restriction['min_rank'] : null;
?>
<style>
	.mfw-restriction-tabs { margin: 0 0 12px; border-bottom: 1px solid #dcdcde; }
	.mfw-restriction-tabs a { display: inline-block; padding: 8px 12px 10px; margin-bottom: -1px; text-decoration: none; border-bottom: 2px solid transparent; color: #50575e; }
	.mfw-restriction-tabs a.active { color: #2196f3; border-bottom-color: #2196f3; font-weight: 600; }
	.mfw-restriction-panel { display: none; }
	.mfw-restriction-panel.active { display: block; }
</style>

<p class="mfw-restriction-tabs">
	<a href="#" class="active" data-panel="mfw-panel-access"><?php esc_html_e( 'Access', 'membership-for-woocommerce' ); ?></a>
	<a href="#" data-panel="mfw-panel-message"><?php esc_html_e( 'Error Message', 'membership-for-woocommerce' ); ?></a>
</p>

<div id="mfw-panel-access" class="mfw-restriction-panel active">

	<p><?php esc_html_e( 'Only members meeting the rule below can access this content. The post author, anyone who can edit it, and anyone with the bypass capability can always see it.', 'membership-for-woocommerce' ); ?></p>

	<p>
		<label>
			<input type="radio" name="mfw-restriction-mode" value="levels" <?php checked( null === $min_rank ); ?> />
			<?php esc_html_e( 'Specific levels', 'membership-for-woocommerce' ); ?>
		</label>
		&nbsp;
		<label>
			<input type="radio" name="mfw-restriction-mode" value="rank" <?php checked( null !== $min_rank ); ?> />
			<?php esc_html_e( 'Minimum rank', 'membership-for-woocommerce' ); ?>
		</label>
	</p>

	<div id="mfw-restriction-levels" style="<?php echo null !== $min_rank ? 'display:none;' : ''; ?>">
		<?php if ( ! empty( $levels ) ) : ?>
			<p>
				<a href="#" id="mfw-restriction-select-all"><?php esc_html_e( 'Select All', 'membership-for-woocommerce' ); ?></a>
				&nbsp;|&nbsp;
				<a href="#" id="mfw-restriction-deselect-all"><?php esc_html_e( 'Deselect All', 'membership-for-woocommerce' ); ?></a>
			</p>
		<?php endif; ?>
		<?php foreach ( (array) $levels as $level ) : ?>
			<label style="display:block;margin-bottom:4px;">
				<input
					type="checkbox"
					value="<?php echo esc_attr( $level['id'] ); ?>"
					<?php checked( in_array( (int) $level['id'], $allowed_level_ids, true ) ); ?>
				/>
				<?php echo esc_html( $level['name'] ); ?>
				<?php if ( ! empty( $level['price'] ) && function_exists( 'wc_price' ) ) : ?>
					<span class="description">(<?php echo wp_kses_post( wc_price( $level['price'] ) ); ?>)</span>
				<?php endif; ?>
			</label>
		<?php endforeach; ?>
		<?php if ( empty( $levels ) ) : ?>
			<p><em><?php esc_html_e( 'No role-membership levels yet.', 'membership-for-woocommerce' ); ?></em></p>
		<?php endif; ?>
		<p>
			<label>
				<?php esc_html_e( 'Content drip (days after assignment)', 'membership-for-woocommerce' ); ?>
				<input type="number" min="0" id="mfw-drip-days" value="<?php echo esc_attr( $restriction['drip_days'] ?? 0 ); ?>" style="width:80px;" />
			</label>
			<p class="description"><?php esc_html_e( '0 = accessible immediately once a matching level is held.', 'membership-for-woocommerce' ); ?></p>
		</p>
	</div>

	<div id="mfw-restriction-rank" style="<?php echo null === $min_rank ? 'display:none;' : ''; ?>">
		<label>
			<?php esc_html_e( 'Minimum rank', 'membership-for-woocommerce' ); ?>
			<input type="number" min="0" id="mfw-min-rank" value="<?php echo esc_attr( $min_rank ?? 0 ); ?>" style="width:80px;" />
		</label>
	</div>

</div>

<div id="mfw-panel-message" class="mfw-restriction-panel">
	<p><?php esc_html_e( 'Shown instead of the content when a visitor does not have access. Leave blank to use the site-wide default message set in Role-Based Membership → Settings.', 'membership-for-woocommerce' ); ?></p>
	<p>
		<textarea id="mfw-restricted-message" rows="3" style="width:100%;max-width:500px;" placeholder="<?php esc_attr_e( 'This content is restricted to certain membership levels.', 'membership-for-woocommerce' ); ?>"><?php echo esc_textarea( $restricted_message ); ?></textarea>
	</p>
</div>

<p><button type="button" class="button" id="mfw-save-restriction"><?php esc_html_e( 'Save Restriction', 'membership-for-woocommerce' ); ?></button></p>
<script>
( function () {
	var modeRadios = document.querySelectorAll( 'input[name="mfw-restriction-mode"]' );
	var levelsBox  = document.getElementById( 'mfw-restriction-levels' );
	var rankBox    = document.getElementById( 'mfw-restriction-rank' );
	var button     = document.getElementById( 'mfw-save-restriction' );

	if ( ! button ) {
		return;
	}

	document.querySelectorAll( '.mfw-restriction-tabs a' ).forEach( function ( tab ) {
		tab.addEventListener( 'click', function ( e ) {
			e.preventDefault();
			document.querySelectorAll( '.mfw-restriction-tabs a' ).forEach( function ( el ) { el.classList.remove( 'active' ); } );
			document.querySelectorAll( '.mfw-restriction-panel' ).forEach( function ( el ) { el.classList.remove( 'active' ); } );
			tab.classList.add( 'active' );
			document.getElementById( tab.getAttribute( 'data-panel' ) ).classList.add( 'active' );
		} );
	} );

	modeRadios.forEach( function ( radio ) {
		radio.addEventListener( 'change', function ( e ) {
			var isRank = 'rank' === e.target.value;
			levelsBox.style.display = isRank ? 'none' : '';
			rankBox.style.display   = isRank ? '' : 'none';
		} );
	} );

	var selectAll   = document.getElementById( 'mfw-restriction-select-all' );
	var deselectAll = document.getElementById( 'mfw-restriction-deselect-all' );

	if ( selectAll ) {
		selectAll.addEventListener( 'click', function ( e ) {
			e.preventDefault();
			levelsBox.querySelectorAll( 'input[type="checkbox"]' ).forEach( function ( el ) { el.checked = true; } );
		} );
	}

	if ( deselectAll ) {
		deselectAll.addEventListener( 'click', function ( e ) {
			e.preventDefault();
			levelsBox.querySelectorAll( 'input[type="checkbox"]' ).forEach( function ( el ) { el.checked = false; } );
		} );
	}

	button.addEventListener( 'click', function () {
		var mode = document.querySelector( 'input[name="mfw-restriction-mode"]:checked' ).value;
		var body = new FormData();
		body.append( 'action', 'mfw_role_based_save_restriction' );
		body.append( 'nonce', <?php echo wp_json_encode( $nonce ); ?> );
		body.append( 'object_type', <?php echo wp_json_encode( $object_type ); ?> );
		body.append( 'object_id', <?php echo wp_json_encode( $post->ID ); ?> );
		body.append( 'restricted_message', document.getElementById( 'mfw-restricted-message' ).value );

		if ( 'rank' === mode ) {
			body.append( 'min_rank', document.getElementById( 'mfw-min-rank' ).value );
		} else {
			document.querySelectorAll( '#mfw-restriction-levels input[type="checkbox"]:checked' ).forEach( function ( input ) {
				body.append( 'level_ids[]', input.value );
			} );
			body.append( 'drip_days', document.getElementById( 'mfw-drip-days' ).value );
		}

		fetch( ajaxurl, { method: 'POST', credentials: 'same-origin', body: body } );
	} );
} )();
</script>
