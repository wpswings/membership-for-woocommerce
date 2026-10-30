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

<input type="hidden" name="mfw_role_based_restriction_nonce" value="<?php echo esc_attr( $nonce ); ?>" />

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
					name="mfw_role_membership_level_ids[]"
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
				<input type="number" min="0" id="mfw-drip-days" name="mfw_role_membership_drip_days" value="<?php echo esc_attr( $restriction['drip_days'] ?? 0 ); ?>" style="width:80px;" />
			</label>
			<p class="description"><?php esc_html_e( '0 = accessible immediately once a matching level is held.', 'membership-for-woocommerce' ); ?></p>
		</p>
	</div>

	<div id="mfw-restriction-rank" style="<?php echo null === $min_rank ? 'display:none;' : ''; ?>">
		<label>
			<?php esc_html_e( 'Minimum rank', 'membership-for-woocommerce' ); ?>
			<input type="number" min="0" id="mfw-min-rank" name="mfw_role_membership_min_rank" value="<?php echo esc_attr( $min_rank ?? 0 ); ?>" style="width:80px;" />
		</label>
	</div>

</div>

<div id="mfw-panel-message" class="mfw-restriction-panel">
	<p><?php esc_html_e( 'Shown instead of the content when a visitor does not have access. Leave blank to use the site-wide default message set in Role-Based Membership → Settings.', 'membership-for-woocommerce' ); ?></p>
	<p>
		<textarea id="mfw-restricted-message" name="mfw_role_membership_restricted_message" rows="3" style="width:100%;max-width:500px;" placeholder="<?php esc_attr_e( 'This content is restricted to certain membership levels.', 'membership-for-woocommerce' ); ?>"><?php echo esc_textarea( $restricted_message ); ?></textarea>
	</p>
</div>

<p>
	<button type="button" class="button" id="mfw-save-restriction"><?php esc_html_e( 'Save Restriction', 'membership-for-woocommerce' ); ?></button>
	<span id="mfw-save-restriction-status" class="description" style="margin-left:6px;" aria-live="polite"></span>
</p>
<p class="description"><?php esc_html_e( 'The restriction is also saved when you save or update this page.', 'membership-for-woocommerce' ); ?></p>
<script>
( function () {
	// The block editor moves (and can re-render) side meta boxes into its sidebar after
	// this script runs, so listeners bound directly to the elements can be lost. Delegate
	// from the document and look every element up at event time instead.
	if ( window.mfwRoleBasedRestrictionMetabox ) {
		return;
	}
	window.mfwRoleBasedRestrictionMetabox = true;

	var config = {
		ajaxUrl: <?php echo wp_json_encode( admin_url( 'admin-ajax.php' ) ); ?>,
		nonce: <?php echo wp_json_encode( $nonce ); ?>,
		objectType: <?php echo wp_json_encode( $object_type ); ?>,
		objectId: <?php echo wp_json_encode( $post->ID ); ?>,
		i18n: {
			saving: <?php echo wp_json_encode( __( 'Saving…', 'membership-for-woocommerce' ) ); ?>,
			saved: <?php echo wp_json_encode( __( 'Restriction saved.', 'membership-for-woocommerce' ) ); ?>,
			failed: <?php echo wp_json_encode( __( 'Could not save the restriction.', 'membership-for-woocommerce' ) ); ?>
		}
	};

	function byId( id ) {
		return document.getElementById( id );
	}

	function setStatus( text, color ) {
		var status = byId( 'mfw-save-restriction-status' );
		if ( status ) {
			status.style.color = color || '';
			status.textContent = text;
		}
	}

	function setLevelCheckboxes( checked ) {
		var levelsBox = byId( 'mfw-restriction-levels' );
		if ( levelsBox ) {
			levelsBox.querySelectorAll( 'input[type="checkbox"]' ).forEach( function ( el ) { el.checked = checked; } );
		}
	}

	function saveRestriction( button ) {
		var modeInput = document.querySelector( 'input[name="mfw-restriction-mode"]:checked' );
		var mode      = modeInput ? modeInput.value : 'levels';
		var message   = byId( 'mfw-restricted-message' );
		var body      = new FormData();

		body.append( 'action', 'mfw_role_based_save_restriction' );
		body.append( 'nonce', config.nonce );
		body.append( 'object_type', config.objectType );
		body.append( 'object_id', config.objectId );
		body.append( 'restricted_message', message ? message.value : '' );

		if ( 'rank' === mode ) {
			body.append( 'min_rank', byId( 'mfw-min-rank' ).value );
		} else {
			document.querySelectorAll( '#mfw-restriction-levels input[type="checkbox"]:checked' ).forEach( function ( input ) {
				body.append( 'level_ids[]', input.value );
			} );
			body.append( 'drip_days', byId( 'mfw-drip-days' ).value );
		}

		button.disabled = true;
		setStatus( config.i18n.saving );

		fetch( config.ajaxUrl, { method: 'POST', credentials: 'same-origin', body: body } )
			.then( function ( response ) {
				return response.json();
			} )
			.then( function ( result ) {
				if ( result && result.success ) {
					setStatus( config.i18n.saved, '#00a32a' );
				} else {
					setStatus( ( result && result.data && result.data.message ) ? result.data.message : config.i18n.failed, '#d63638' );
				}
			} )
			.catch( function () {
				setStatus( config.i18n.failed, '#d63638' );
			} )
			.then( function () {
				button.disabled = false;
			} );
	}

	document.addEventListener( 'click', function ( e ) {
		var target = e.target;

		var tab = target.closest( '.mfw-restriction-tabs a' );
		if ( tab ) {
			e.preventDefault();
			document.querySelectorAll( '.mfw-restriction-tabs a' ).forEach( function ( el ) { el.classList.remove( 'active' ); } );
			document.querySelectorAll( '.mfw-restriction-panel' ).forEach( function ( el ) { el.classList.remove( 'active' ); } );
			tab.classList.add( 'active' );
			byId( tab.getAttribute( 'data-panel' ) ).classList.add( 'active' );
			return;
		}

		if ( target.closest( '#mfw-restriction-select-all' ) ) {
			e.preventDefault();
			setLevelCheckboxes( true );
			return;
		}

		if ( target.closest( '#mfw-restriction-deselect-all' ) ) {
			e.preventDefault();
			setLevelCheckboxes( false );
			return;
		}

		var button = target.closest( '#mfw-save-restriction' );
		if ( button ) {
			e.preventDefault();
			saveRestriction( button );
		}
	} );

	document.addEventListener( 'change', function ( e ) {
		if ( 'mfw-restriction-mode' !== e.target.name ) {
			return;
		}
		var isRank    = 'rank' === e.target.value;
		var levelsBox = byId( 'mfw-restriction-levels' );
		var rankBox   = byId( 'mfw-restriction-rank' );
		if ( levelsBox ) {
			levelsBox.style.display = isRank ? 'none' : '';
		}
		if ( rankBox ) {
			rankBox.style.display = isRank ? '' : 'none';
		}
	} );
} )();
</script>
