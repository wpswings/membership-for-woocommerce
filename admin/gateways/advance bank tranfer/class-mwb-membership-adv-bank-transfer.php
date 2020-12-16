<?php
/**
 * Provides a PayPal Express Gateway for Membership For Woocommerce.
 *
 * @package Membership_For_Woocommerce
 */

if ( ! defined( 'ABSPATH' ) ) {

	die();
}

/**
 * Adding Paypal Payment Gateway support for Membership.
 */
class Mwb_Membership_Adv_Bank_Transfer extends WC_Payment_Gateway {

	/**
	 * Array of locales
	 *
	 * @var array
	 */
	public $locale;

	/**
	 * Constructor
	 */
	public function __construct() {

		$this->id                 = 'membership-adv-bank-transfer';
		$this->has_fields         = false;
		$this->method_title       = __( 'Advance Direct Bank Transfer', 'membership-for-woocommerce' );
		$this->method_description = __( 'Take Payments in person via BACS.', 'membership-for-woocommerce' ); 

		// Load the settings.
		$this->init_form_fields();
		$this->init_settings();

		// Get settings values.
		$this->title        = $this->get_option( 'title' );
		$this->description  = $this->get_option( 'description' );
		$this->instructions = $this->get_option( 'instructions' );

		// BACS account fields shown on purchase completion and emails.
		$this->account_details = get_option(
			'mwb_membership_bacs_accounts',
			array(
				array(
					'account_name'   => $this->get_option( 'account_name' ),
					'account_number' => $this->get_option( 'account_number' ),
					'sort_code'      => $this->get_option( 'sort_code' ),
					'bank_name'      => $this->get_option( 'bank_name' ),
					'iban'           => $this->get_option( 'iban' ),
					'bic'            => $this->get_option( 'bic' ),

				),
			)
		);

		// Actions.
		add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );
		add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'save_account_details' ) );

		// Show Reciept.
		add_filter( 'woocommerce_gateway_description', array( $this, 'add_receipt_fields_html' ), 10, 2 );
	}

	/**
	 * Membership advance bank tranfer gateway form fields.
	 */
	public function init_form_fields() {

		global $woocommerce;

		$countries = new WC_Countries();
		$country   = $countries->__get( 'countries' );

		$this->form_fields = array(

			'enabled'           => array(
				'title'   => __( 'Enable/Disable', 'membership-for-woocommerce' ),
				'type'    => 'checkbox',
				'label'   => __( 'Enable Advance Bank Transfer', 'membership-for-woocommerce' ),
				'default' => 'no',
			),
			'title'             => array(
				'title'       => __( 'Title', 'membership-for-woocommerce' ),
				'type'        => 'text',
				'description' => __( 'This displays the title of the gateway visible to user', 'membership-for-woocommerce' ),
				'desc_tip'    => true,
				'default'     => __( 'Advance Direct Bank Transfer', 'membership-for-woocommerce' ),
			),
			'description'       => array(
				'title'       => __( 'Description', 'membership-for-woocommerce' ),
				'type'        => 'textarea',
				'description' => __( 'Payment method description visible to the user', 'membership-for-woocommerce' ),
				'desc_tip'    => true,
				'default'     => __( 'Make your payment directly into our bank and upload receipt below. Your membership will be on-hold until the funds reflect in our bank' ),
			),
			'instructions'      => array(
				'title'      => __( 'Instructions', 'membership-for-woocommerce' ),
				'type'       => 'textarea',
				'decription' => __( 'Instructions that will be added on membership purchase and emails', 'membership-for-woocommerce' ),
				'desc_tip'   => true,
				'default'    => __( 'Thank You For purchasing the membership plan, We will review your payment and process accordingly.', 'membership-for-woocommerce' ),
			),
			'account_details'   => array(
				'type' => 'account_details',
			),
			'exclude_countries' => array(
				'title'       => __( 'Exclude Countries', 'membership-for-woocommerce' ),
				'type'        => 'multiselect',
				'options'     => $country,
				'description' => __( 'The payment mehtod won\'t be shown if billing country belongs to one of these.', 'membership-for-woocommerce' ),
				'desc_tip'    => true,
				'default'     => '',
			),
			'support_formats'   => array(
				'title'       => __( 'Supported Formats', 'membership-for-woocommerce' ),
				'type'        => 'multiselect',
				'options'     => array(
					'jpeg' => 'JPEG',
					'jpg'  => 'JPG',
					'pdf'  => 'PDF',
					'png'  => 'PNG',
				),
				'description' => __( 'The supported formats for receipt.', 'membership-for-woocommerce' ),
				'desc_tip'    => true,
				'default'     => array( 'jpeg', 'jpg', 'pdf', 'png' ),
			),
		);
	}

	/**
	 * Generate account deatils html.
	 *
	 * @return html
	 * @since 1.0.0
	 */
	public function generate_account_details_html() {

		ob_start();

		$country = WC()->countries->get_base_country();
		$locale  = $this->get_country_locale();

		// Get sortcode label in the $locale array and use appropriate one.
		$sortcode = isset( $locale[ $country ]['sortcode']['label'] ) ? $locale[ $country ]['sortcode']['label'] : __( 'Sort code', 'membership-for-woocommerce' );

		?>

		<tr valign="top">
			<th scope="row" class="titledesc"><?php esc_html_e( 'Account Details', 'membership-for-woocommerce' ); ?></th>
			<td class="forminp" id="adv_bank_transfer_account">
				<div class="wc_input_table_wrapper">
					<table class="widefat wc_input_table sortable" cellspacing="0">
						<thead>
							<tr>
								<th class="sort">&nbsp;</th>
								<th><?php esc_html_e( 'Account Holder\'s Name', 'membership-for-woocommerce' ); ?></th>
								<th><?php esc_html_e( 'Account Number', 'membership-for-woocommerce' ); ?></th>
								<th><?php esc_html_e( 'Bank Name', 'membership-for-woocommerce' ); ?></th>
								<th><?php echo esc_html( $sortcode ); ?></th>
								<th><?php esc_html_e( 'IBAN', 'membership-for-woocommerce' ); ?></th>
								<th><?php esc_html_e( 'BIC / Swift', 'membership-for-woocommerce' ); ?></th>
							</tr>
						</thead>
						<tbody class="accounts">
							<?php
							$i = -1;

							if ( $this->account_details ) {

								foreach ( $this->account_details as $account ) {

									$i++;

									echo '<tr class="account">
											<td class="sort"></td>
											<td><input type="text" value="' . esc_attr( wp_unslash( $account['account_name'] ) ) . '" name="bacs_account_name[' . esc_attr( $i ) . ']" /></td>
											<td><input type="text" value="' . esc_attr( $account['account_number'] ) . '" name="bacs_account_number[' . esc_attr( $i ) . ']" /></td>
											<td><input type="text" value="' . esc_attr( wp_unslash( $account['bank_name'] ) ) . '" name="bacs_bank_name[' . esc_attr( $i ) . ']" /></td>
											<td><input type="text" value="' . esc_attr( $account['sort_code'] ) . '" name="bacs_sort_code[' . esc_attr( $i ) . ']" /></td>
											<td><input type="text" value="' . esc_attr( $account['iban'] ) . '" name="bacs_iban[' . esc_attr( $i ) . ']" /></td>
											<td><input type="text" value="' . esc_attr( $account['account_name'] ) . '" name="bacs_bic[' . esc_attr( $i ) . ']" /></td>
										</tr>';
								}
							}
							?>
						</tbody>
						<tfoot>
							<tr>
								<th colspan="7"><a href="#" class="add button"><?php esc_html_e( '+ Add Account', 'membership-for-woocommerce' ); ?></a> <a href="#" class="remove_rows button"><?php esc_html_e( 'Remove selected account(s)', 'membership-for-woocommerce' ); ?></a></th>
							</tr>
						</tfoot>
					</table>
				</div>
				<script type="text/javascript">
					jQuery(function() {
						jQuery('#adv_bank_transfer_account').on( 'click', 'a.add', function(){

							var size = jQuery('#adv_bank_transfer_account').find('tbody .account').length;

							jQuery('<tr class="account">\
										<td class="sort"></td>\
										<td><input type="text" name="bacs_account_name[' + size + ']" /></td>\
										<td><input type="text" name="bacs_account_number[' + size + ']" /></td>\
										<td><input type="text" name="bacs_bank_name[' + size + ']" /></td>\
										<td><input type="text" name="bacs_sort_code[' + size + ']" /></td>\
										<td><input type="text" name="bacs_iban[' + size + ']" /></td>\
										<td><input type="text" name="bacs_bic[' + size + ']" /></td>\
									</tr>').appendTo('#adv_bank_transfer_account table tbody');

							return false;
						});
					});
				</script>
			</td>
		</tr>
		<?php
		return ob_get_clean();
	}

	/**
	 * Save account details table.
	 */
	public function save_account_details() {

		$accounts = array();

		if ( isset( $_POST['bacs_account_name'] ) && isset( $_POST['bacs_account_number'] ) && isset( $_POST['bacs_bank_name'] ) && isset( $_POST['bacs_sort_code'] ) && isset( $_POST['bacs_iban'] ) && isset( $_POST['bacs_bic'] ) ) {

			$account_names   = wc_clean( wp_unslash( $_POST['bacs_account_name'] ) );
			$account_numbers = wc_clean( wp_unslash( $_POST['bacs_account_number'] ) );
			$bank_names      = wc_clean( wp_unslash( $_POST['bacs_bank_name'] ) );
			$sort_codes      = wc_clean( wp_unslash( $_POST['bacs_sort_code'] ) );
			$ibans           = wc_clean( wp_unslash( $_POST['bacs_iban'] ) );
			$bics            = wc_clean( wp_unslash( $_POST['bacs_bic'] ) );

			foreach ( $account_names as $i => $name ) {

				if ( ! isset( $account_names[ $i ] ) ) {
					continue;
				}

				$accounts[] = array(
					'account_name'   => $account_names[ $i ],
					'account_number' => $account_numbers[ $i ],
					'bank_name'      => $bank_names[ $i ],
					'sort_code'      => $sort_codes[ $i ],
					'iban'           => $ibans[ $i ],
					'bic'            => $bics[ $i ],
				);
			}
		}

		update_option( 'mwb_membership_bacs_accounts', $accounts );
	}

	/**
	 * Unset the gateway according to the country.
	 */
	// public function is_available() {

	// 	// $current_user    = WC()->session->get( 'customer' );
	// 	// $billing_country = ! empty( $current_user['country'] ) ? $current_user['country'] : array();

	// 	// if ( in_array( $billing_country, $this->settings['exclude_countries'] ) ) {

	// 	// 	return false;
	// 	// } else {

	// 	// 	return true;
	// 	// }
	// }

	/**
	 * Output for the receipt fields.
	 *
	 * @param int $desc    Description.
	 * @param int $gate_id Gateway ID.
	 */
	public function add_receipt_fields_html( $desc = '', $gate_id = '' ) {

		if ( ! empty( $gate_id ) && $gate_id == $this->id ) {

			return $desc . $this->return_receipt_fields_html();
		}
	}

	/**
	 * Returns html of receipt fields.
	 */
	public function return_receipt_fields_html() {

		ob_start();
		?>

		<div class="bacs_receipt_wrapper">
			<div class="bacs_receipt_field">
				<input type="file" name="bacs_receipt_file" class="bacs_receipt_file"/>
				<input type="hidden" name="bacs_receipt_attached" class="bacs_receipt_attached">
			</div>
			<div id="progress-wrapper" class="is_hidden">
				<div class="progress-bar"></div>
				<div class="status"><?php esc_html_e( 'Processing', 'membership-for-woocommerce' ); ?></div>
			</div>
			<div class="bacs_receipt_field is_hidden">
				<a href="javascript:void(0);" class="bacs_receipt_remove_file"><?php esc_html_e( 'Remove File', 'membership-for-woocommerce' ); ?></a>
			</div>
		</div>

		<?php
		return ob_get_clean();
	}

	/**
	 * Get country locale if localized.
	 *
	 * @return array
	 */
	public function get_country_locale() {

		if ( empty( $this->locale ) ) {

			// Locale information to be used - only those that are not 'Sort Code'.
			$this->locale = apply_filters(
				'mwb_membership_get_adv_bacs_locale',
				array(
					'AU' => array(
						'sortcode' => array(
							'label' => __( 'BSB', 'membership-for-woocommerce' ),
						),
					),
					'CA' => array(
						'sortcode' => array(
							'label' => __( 'Bank transit number', 'membership-for-woocommerce' ),
						),
					),
					'IN' => array(
						'sortcode' => array(
							'label' => __( 'IFSC', 'membership-for-woocommerce' ),
						),
					),
					'IT' => array(
						'sortcode' => array(
							'label' => __( 'Branch sort', 'membership-for-woocommerce' ),
						),
					),
					'NZ' => array(
						'sortcode' => array(
							'label' => __( 'Bank Code', 'membership-for-woocommerce' ),
						),
					),
					'SE' => array(
						'sortcode' => array(
							'label' => __( 'Bank Code', 'membership-for-woocommerce' ),
						),
					),
					'US' => array(
						'sortcode' => array(
							'label' => __( 'Routing Number', 'membership-for-woocommerce' ),
						),
					),
					'ZA' => array(
						'sortcode' => array(
							'label' => __( 'Branch Code', 'membership-for-woocommerce' ),
						),
					),
				),
			);
		}

		return $this->locale;
	}

}



