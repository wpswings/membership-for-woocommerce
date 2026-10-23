<?php
/**
 * Taxonomy term restriction field (WPS-7880 gap-analysis item 4: taxonomy/category restriction).
 *
 * @package    Membership_For_Woocommerce
 * @subpackage Membership_For_Woocommerce/includes/role-based/admin/partials
 *
 * @var array[]    $levels      Rows from Mfw_Role_Based_Repository::get_levels().
 * @var array|null $restriction Mfw_Role_Based_Repository::get_restriction() result for this term.
 * @var string     $nonce       Not used directly here (this is a plain form field saved with the term).
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$allowed_level_ids = $restriction ? array_map( 'absint', $restriction['level_ids'] ) : array();
?>
<tr class="form-field">
	<th scope="row"><label><?php esc_html_e( 'Role-Based Membership Restriction', 'membership-for-woocommerce' ); ?></label></th>
	<td>
		<?php wp_nonce_field( 'mfw_role_based_admin', 'mfw_role_based_term_nonce' ); ?>
		<?php if ( ! empty( $levels ) ) : ?>
			<p>
				<a href="#" id="mfw-term-restriction-select-all"><?php esc_html_e( 'Select All', 'membership-for-woocommerce' ); ?></a>
				&nbsp;|&nbsp;
				<a href="#" id="mfw-term-restriction-deselect-all"><?php esc_html_e( 'Deselect All', 'membership-for-woocommerce' ); ?></a>
			</p>
		<?php endif; ?>
		<div id="mfw-term-restriction-levels">
			<?php foreach ( (array) $levels as $level ) : ?>
				<label style="display:block;margin-bottom:4px;">
					<input
						type="checkbox"
						name="mfw_role_membership_term_level_ids[]"
						value="<?php echo esc_attr( $level['id'] ); ?>"
						<?php checked( in_array( (int) $level['id'], $allowed_level_ids, true ) ); ?>
					/>
					<?php echo esc_html( $level['name'] ); ?>
				</label>
			<?php endforeach; ?>
			<?php if ( empty( $levels ) ) : ?>
				<p><em><?php esc_html_e( 'No role-membership levels yet.', 'membership-for-woocommerce' ); ?></em></p>
			<?php endif; ?>
		</div>
		<p class="description"><?php esc_html_e( 'Only members with a checked level can view this term\'s archive. Leave unchecked for everyone.', 'membership-for-woocommerce' ); ?></p>
		<script>
		( function () {
			var wrap        = document.getElementById( 'mfw-term-restriction-levels' );
			var selectAll   = document.getElementById( 'mfw-term-restriction-select-all' );
			var deselectAll = document.getElementById( 'mfw-term-restriction-deselect-all' );

			if ( selectAll ) {
				selectAll.addEventListener( 'click', function ( e ) {
					e.preventDefault();
					wrap.querySelectorAll( 'input[type="checkbox"]' ).forEach( function ( el ) { el.checked = true; } );
				} );
			}

			if ( deselectAll ) {
				deselectAll.addEventListener( 'click', function ( e ) {
					e.preventDefault();
					wrap.querySelectorAll( 'input[type="checkbox"]' ).forEach( function ( el ) { el.checked = false; } );
				} );
			}
		} )();
		</script>
	</td>
</tr>
