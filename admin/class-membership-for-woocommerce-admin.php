<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://makewebbetter.com
 * @since      1.0.0
 *
 * @package    Membership_For_Woocommerce
 * @subpackage Membership_For_Woocommerce/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Membership_For_Woocommerce
 * @subpackage Membership_For_Woocommerce/admin
 * @author     Make Web Better <plugins@makewebbetter.com>
 */
class Membership_For_Woocommerce_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Mwb Membership Plans field.
	 *
	 * @var array
	 */
	public $settings_fields = array();

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string $plugin_name  The name of this plugin.
	 * @param      string $version      The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version     = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Membership_For_Woocommerce_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Membership_For_Woocommerce_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
		$screen = get_current_screen();

		if ( isset( $screen->id ) || isset( $screen->post_type ) ) {

			$pagescreen_id   = $screen->id;
			$pagescreen_post = $screen->post_type;

			if ( 'mwb_cpt_membership' == $pagescreen_post || 'mwb_cpt_membership' == $pagescreen_id ) {

				wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/membership-for-woocommerce-admin.css', array(), $this->version, 'all' );

				wp_enqueue_style( 'mwb_membership_for_woo_select2', plugin_dir_url( __FILE__ ) . 'css/select2.min.css', array(), $this->version, 'all' );

			}

			if ( isset( $_GET['tab'] ) && 'shipping' == $_GET['tab'] ) {
				wp_enqueue_style( 'mwb_membership_for_woo_select2', plugin_dir_url( __FILE__ ) . 'css/select2.min.css', array(), $this->version, 'all' );
			}
		}

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Membership_For_Woocommerce_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Membership_For_Woocommerce_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		$screen = get_current_screen();

		if ( isset( $screen->id ) || isset( $screen->post_type ) ) {

			$pagescreen_post = $screen->post_type;
			$pagescreen_id   = $screen->id;

			if ( 'mwb_cpt_membership' == $pagescreen_post || 'mwb_cpt_membership' == $pagescreen_id ) {

				wp_enqueue_script( 'woocommerce_admin' );

				$locale  = localeconv();
				$decimal = isset( $locale['decimal_point'] ) ? $locale['decimal_point'] : '.';

				$params = array(
					/* translators: %s: decimal */
					'i18n_decimal_error'                => sprintf( __( 'Please enter with one decimal point (%s) without thousand separators.', 'woocommerce' ), $decimal ),
					/* translators: %s: price decimal separator */
					'i18n_mon_decimal_error'            => sprintf( __( 'Please enter with one monetary decimal point (%s) without thousand separators and currency symbols.', 'woocommerce' ), wc_get_price_decimal_separator() ),
					'i18n_country_iso_error'            => __( 'Please enter in country code with two capital letters.', 'woocommerce' ),
					'i18n_sale_less_than_regular_error' => __( 'Please enter in a value less than the regular price.', 'woocommerce' ),
					'i18n_delete_product_notice'        => __( 'This product has produced sales and may be linked to existing orders. Are you sure you want to delete it?', 'woocommerce' ),
					'i18n_remove_personal_data_notice'  => __( 'This action cannot be reversed. Are you sure you wish to erase personal data from the selected orders?', 'woocommerce' ),
					'decimal_point'                     => $decimal,
					'mon_decimal_point'                 => wc_get_price_decimal_separator(),
					'ajax_url'                          => admin_url( 'admin-ajax.php' ),
					'strings'                           => array(
						'import_products' => __( 'Import', 'woocommerce' ),
						'export_products' => __( 'Export', 'woocommerce' ),
					),
					'nonces'                            => array(
						'gateway_toggle' => wp_create_nonce( 'woocommerce-toggle-payment-gateway-enabled' ),
					),
					'urls'                              => array(
						'import_products' => current_user_can( 'import' ) ? esc_url_raw( admin_url( 'edit.php?post_type=product&page=product_importer' ) ) : null,
						'export_products' => current_user_can( 'export' ) ? esc_url_raw( admin_url( 'edit.php?post_type=product&page=product_exporter' ) ) : null,
					),
				);

				wp_localize_script( 'woocommerce_admin', 'woocommerce_admin', $params );

				wp_enqueue_script( 'membership-for-woocommerce-select2', plugin_dir_url( __FILE__ ) . 'js/select2.min.js', array( 'jquery' ), $this->version, false );

				wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/membership-for-woocommerce-admin.js', array( 'jquery' ), $this->version, false );

				wp_enqueue_script( 'mwb_membership_for_woo_add_new_plan_script', plugin_dir_url( __FILE__ ) . 'js/mwb_membership_for_woo_add_new_plan_script.js', array( 'woocommerce_admin', 'wc-enhanced-select' ), $this->version, false );

				wp_localize_script( 'mwb_membership_for_woo_add_new_plan_script', 'ajax_url', admin_url( 'admin-ajax.php' ) );

				wp_enqueue_script( 'wp-color-picker' );

				wp_enqueue_script( 'membership-for-woocommerce-modal', plugin_dir_url( __FILE__ ) . 'js/mwb_membership_for_woo_thickbox.js', array( 'jquery' ), $this->version, false );

				add_thickbox();
			}

			if ( isset( $_GET['tab'] ) && 'shipping' == $_GET['tab'] ) {
				wp_enqueue_script( 'membership-for-woocommerce-select2', plugin_dir_url( __FILE__ ) . 'js/select2.min.js', array( 'jquery' ), $this->version, false );
				wp_enqueue_script( 'mwb_membership_for_woo_add_new_plan_script', plugin_dir_url( __FILE__ ) . 'js/mwb_membership_for_woo_add_new_plan_script.js', array( 'woocommerce_admin', 'wc-enhanced-select' ), $this->version, false );

				wp_localize_script( 'mwb_membership_for_woo_add_new_plan_script', 'ajax_url', admin_url( 'admin-ajax.php' ) );
			}

			if ( isset( $_GET['section'] ) && 'membership-for-woo-paypal-gateway' == $_GET['section'] ) {

				wp_enqueue_script( 'mwb-membership-paypal-script', plugin_dir_url( __FILE__ ) . 'js/membership-for-woocommerce-paypal.js', array( 'jquery' ), $this->version, false );

			} elseif ( isset( $_GET['section'] ) && 'membership-for-woo-stripe-gateway' == $_GET['section'] ) {

				wp_enqueue_script( 'mwb-membership-stripe-script', plugin_dir_url( __FILE__ ) . 'js/membership-for-woocommerce-stripe.js', array( 'jquery' ), $this->version, false );
			}
		}

	}

	/**
	 * Custom post type for membership plans creation and settings.
	 */
	public function mwb_membership_for_woo_cpt_membership() {

		$labels = array(
			'name'               => __( 'Memberships', 'membership-for-woocommerce' ),
			'singular_name'      => __( 'Membership', 'membership-for-woocommerce' ),
			'add_new'            => __( 'Add Plans', 'membership-for-woocommerce' ),
			'all_items'          => __( 'All Plans', 'membership-for-woocommerce' ),
			'add_new_item'       => __( 'Add New Plan', 'membership-for-woocommerce' ),
			'edit_item'          => __( 'Edit Plan', 'membership-for-woocommerce' ),
			'new_item'           => __( 'New Plan', 'membership-for-woocommerce' ),
			'view_item'          => __( 'View Plan', 'membership-for-woocommerce' ),
			'search_item'        => __( 'Search Plan', 'membership-for-woocommerce' ),
			'not_found'          => __( 'No Plans Found', 'membership-for-woocommerce' ),
			'not_found_in_trash' => __( 'No Plans Found In Trash', 'membership-for-woocommerce' ),
		);

		register_post_type(
			'mwb_cpt_membership',
			array(
				'labels'              => $labels,
				'public'              => true,
				'has_archive'         => false,
				'publicly_queryable'  => true,
				'query_var'           => true,
				'capability_type'     => 'post',
				'hierarchical'        => false,
				'show_in_admin_bar'   => true,
				'show_in_menu'        => true,
				'menu_position'       => 56,
				'menu_icon'           => 'dashicons-businessperson',
				'description'         => __( 'Here all the Membership Plans will be created.', 'membership-for-woocommerce' ),
				'supports'            => array(
					'title',
					'editor',
					'excerpt',
				),
				'exclude_from_search' => false,
				'rewrite'             => array(
					'slug' => __( 'membership', 'membership-for-woocommerce' ),
				),
			)
		);
	}

	/**
	 * Adding sub-menu pages to Membership menu.
	 *
	 * @since 1.0.0
	 */
	public function mwb_memberships_for_woo_admin_menu() {

		// Add submenu for Global Settings.
		add_submenu_page( 'edit.php?post_type=mwb_cpt_membership', esc_html__( 'Global Settings', 'membership-for-woocommerce' ), esc_html__( 'Global Settings', 'membership-for-woocommerce' ), 'manage_options', 'mwb-membership-for-woo-global-settings', array( $this, 'mwb_membership_for_woo_global_settings' ) );

		// Add submenu for shortcodes.
		add_submenu_page( 'edit.php?post_type=mwb_cpt_membership', esc_html__( 'Shortcodes', 'membership-for-woocommerce' ), esc_html__( 'Shortcodes', 'membership-for-woocommerce' ), 'manage_options', 'mwb-membership-for-woo-shortcodes', array( $this, 'mwb_membership_for_woo_shortcodes' ) );

		// Add submenu fro supported gateways.
		add_submenu_page( 'edit.php?post_type=mwb_cpt_membership', esc_html__( 'Supported Gateways', 'membership-for-woocommerce' ), esc_html__( 'Supported Gateways', 'membership-for-woocommerce' ), 'manage_options', 'mwb_membership-for-woo-gateways', array( $this, 'mwb_membership_for_woo_gateways' ) );

		// Add submenu for overview.
		add_submenu_page( 'edit.php?post_type=mwb_cpt_membership', esc_html__( 'Overview', 'membership-for-woocommerce' ), esc_html__( 'Overview', 'membership-for-woocommerce' ), 'manage_options', 'mwb-membership-for-woo-overview', array( $this, 'mwb_membership_for_woo_overview' ) );

	}

	/**
	 * Callback function for Global settings sub-menu page.
	 */
	public function mwb_membership_for_woo_global_settings() {

		require_once plugin_dir_path( __FILE__ ) . '/partials/templates/mwb-membership-global-settings.php';

	}

	/**
	 * Callback function for shortcodes sub-menu page.
	 */
	public function mwb_membership_for_woo_overview() {

		require_once plugin_dir_path( __FILE__ ) . '/partials/templates/mwb-membership-overview.php';

	}

	/**
	 * Callback function for shortcodes sub-menu page.
	 */
	public function mwb_membership_for_woo_shortcodes() {

		require_once plugin_dir_path( __FILE__ ) . '/partials/templates/mwb-membership-shortcodes.php';

	}

	/**
	 * Callback function for supported gateways sub-menu page.
	 */
	public function mwb_membership_for_woo_gateways() {

		require_once plugin_dir_path( __FILE__ ) . '/partials/templates/mwb-membership-supported-gateway.php';
	}

	/**
	 * Adding custom column to the custom post type "Membership"
	 *
	 * @param array $columns is an array of deafult columns in custom post type.
	 */
	public function mwb_membership_for_woo_cpt_columns_membership( $columns ) {

		$columns['membership_view']   = '';
		$columns['membership_status'] = __( 'Membership Plan Status', 'membership-for-woocommerce' );
		$columns['membership_cost']   = __( 'Membership Plan Cost', 'membership-for-woocommerce' );

		return $columns;
	}

	/**
	 * Populating custom columns with content.
	 *
	 * @param array   $column is an array of default columns in Custom post type.
	 * @param integer $post_id is the post id.
	 */
	public function mwb_membership_for_woo_fill_columns_membership( $column, $post_id ) {

		switch ( $column ) {

			case 'membership_view':
				?>

				<a title="<?php echo esc_html( 'Membership Id #' ) . esc_html( $post_id ); ?>" href="admin-ajax.php?action=mwb_membership_for_woo_get_content&post_id=<?php echo $post_id; ?>" class="thickbox"><span class="dashicons dashicons-visibility"></span></a>

				<?php
				break;

			case 'membership_status':
				$plan_status = get_post_status( $post_id );

				if ( ! empty( $plan_status ) ) {

					// Display Sandbox mode if visibility is private.
					if ( 'private' == $plan_status ) {

						echo esc_html__( 'Sandbox', 'membership-for-woocommerce' );

					} elseif ( 'draft' == $plan_status || 'pending' == $plan_status ) { // Display sandbox mode if status is draft or pending.

						echo esc_html__( 'Sandbox', 'membership-for-woocommerce' );

					} else { // Display live mode.

						echo esc_html__( 'Live', 'membership-for-woocommerce' );
					}
				}

				break;

			case 'membership_cost':
				$plan_cost = get_post_meta( $post_id, 'mwb_membership_plan_price', true );
				$currency  = get_woocommerce_currency();

				if ( ! empty( $currency ) && ! empty( $plan_cost ) ) {

					echo esc_html( $currency . ' ' . $plan_cost );

				}

				break;
		}

	}

	/**
	 * Get post data ( Ajax handler)
	 *
	 * @return void
	 */
	public function mwb_membership_for_woo_get_content() {

		$plan_id = ! empty( $_GET['post_id'] ) ? sanitize_text_field( wp_unslash( $_GET['post_id'] ) ) : '';

		$output = '';

		if ( ! empty( $plan_id ) ) {

			$plan_title       = get_the_title( $plan_id );
			$plan_price       = get_post_meta( $plan_id, 'mwb_membership_plan_price', true );
			$plan_products    = mwb_membership_csv_get_title( get_post_meta( $plan_id, 'mwb_membership_plan_target_ids', true ) );
			$plan_categories  = mwb_membership_csv_get_cat_title( get_post_meta( $plan_id, 'mwb_membership_plan_target_categories', true ) );
			$plan_description = get_post_field( 'post_content', $plan_id );
			$plan_access_type = get_post_meta( $plan_id, 'mwb_membership_plan_name_access_type', true );
			$plan_user_access = get_post_meta( $plan_id, 'mwb_membership_plan_access_type', true );

			// Html for preview mode.
			$output .= '<h2>' . esc_html( $plan_title ) . '</h2>';
			$output .= '<div class="mwb_membership_preview_table">';
			$output .= '<table class="form-table mwb_membership_preview>"';
			$output .= '<tbody>';

			// Plan Price section.
			$output .= '<tr>
							<th>
								<label>' . __( 'Plan Price', 'membership-for-woocommerce' ) . ' </label>
							</th>

							<td>' . esc_html( $plan_price ) . '</td>
						</tr>';

			// Plan access type section.
			$output .= '<tr>
							<th>
								<label>' . __( 'Plan Access Type', 'membership-for-woocommerce' ) . ' </label>
							</th>

							<td>' . esc_html( $plan_access_type ) . '</td>
						</tr>';

			// Plan user access type.
			$output .= '<tr>
							<th>
								<label>' . __( 'Plan User Access Type', 'membership-for-woocommerce' ) . ' </label>
							</th>

							<td>' . esc_html( $plan_user_access ) . '</td>
						</tr>';

			// Plan offered categories.
			$output .= '<tr>
							<th>
								<label>' . __( 'Plan Offered Categories', 'membership-for-woocommerce' ) . ' </label>
							</th>

							<td>' . esc_html( $plan_categories ) . '</td>
						</tr>';

			// Plan offered products.
			$output .= '<tr>
							<th>
								<label>' . __( 'Plan Offered Products', 'membership-for-woocommerce' ) . ' </label>
							</th>

							<td>' . esc_html( $plan_products ) . '</td>
						</tr>';

			// Plan description.
			$output .= '<tr>
							<th>
								<label>' . __( 'Plan Description', 'membership-for-woocommerce' ) . ' </label>
							</th>

							<td>' . esc_html( $plan_description ) . '</td>
						</tr>';

			$output .= '</tbody>
						</table>
						</div>';
		}

		echo $output;

		wp_die();
	}

	/**
	 * Register Custom Meta box for Membership plans creation.
	 */
	public function mwb_membership_for_woo_meta_box() {

		add_meta_box( 'membership_meta_box', __( 'Create Plan', 'membership-for-woocommerce' ), array( $this, 'mwb_membership_meta_box_callback' ), 'mwb_cpt_membership' );
	}

	/**
	 * Callback funtion for custom meta boxes.
	 *
	 * @param string $post Current post object.
	 */
	public function mwb_membership_meta_box_callback( $post ) {

		$this->set_plan_creation_fields( get_the_ID() );

		require_once plugin_dir_path( __FILE__ ) . '/partials/templates/mwb-membership-plans-creation.php';

	}

	/**
	 * Save meta box fields value.
	 *
	 * @param int $post_id Post ID.
	 */
	public function mwb_membership_for_woo_save_fields( $post_id ) {

		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {

			return;
		}

		if ( ! empty( $this->get_plans_default_value() ) && is_array( $this->get_plans_default_value() ) ) {

			foreach ( $this->get_plans_default_value() as $field => $value ) {

				$default = ! empty( $value['default'] ) ? $value['default'] : '';

				$post_data = '';

				if ( ! empty( $_POST[ $field ] ) ) {

					if ( is_array( $_POST[ $field ] ) ) {

						$post_data = ! empty( $_POST[ $field ] ) ? array_map( 'sanitize_text_field', wp_unslash( $_POST[ $field ] ) ) : $default;

					} else {

						$post_data = ! empty( $_POST[ $field ] ) ? sanitize_text_field( wp_unslash( $_POST[ $field ] ) ) : $default;

					}
				}
				update_post_meta( $post_id, $field, $post_data );

			}
		}

	}

	/**
	 * Define Membership default settings fields.
	 *
	 * @return array
	 */
	public function get_plans_default_value() {

		return array(
			'mwb_membership_plan_price'              => array( 'default' => '0' ),
			'mwb_membership_plan_name_access_type'   => array( 'default' => 'lifetime' ),
			'mwb_membership_plan_duration'           => array( 'default' => '0' ),
			'mwb_membership_plan_duration_type'      => array( 'default' => 'days' ),
			'mwb_membership_plan_start'              => array( 'default' => '' ),
			'mwb_membership_plan_end'                => array( 'default' => '' ),
			'mwb_membership_plan_user_access'        => array( 'default' => 'no' ),
			'mwb_membership_plan_access_type'        => array( 'default' => 'immediate_type' ),
			'mwb_membership_plan_time_duration'      => array( 'default' => '0' ),
			'mwb_membership_plan_time_duration_type' => array( 'default' => 'days' ),
			'mwb_membership_plan_offer_price_type'   => array( 'default' => '%' ),
			'mwb_memebership_plan_discount_price'    => array( 'default' => '10' ),
			'mwb_memebership_plan_free_shipping'     => array( 'default' => 'no' ),
			'mwb_membership_plan_target_categories'  => array( 'default' => array() ),
			'mwb_membership_plan_target_ids'         => array( 'default' => array() ),
		);
	}

	/**
	 * Set default fields  of membership plans
	 *
	 * @param int $post_id Post ID.
	 */
	public function set_plan_creation_fields( $post_id ) {

		if ( ! empty( $this->get_plans_default_value() && is_array( $this->get_plans_default_value() ) ) ) {

			foreach ( $this->get_plans_default_value() as $key => $value ) {

				$default = ! empty( $value['default'] ) ? $value['default'] : '';

				$data                          = get_post_meta( $post_id, $key, true );
				$this->settings_fields[ $key ] = ! empty( $data ) ? $data : $default;

			}
		}
	}

	/**
	 * Add export to csv button on Membership CPT
	 */
	public function mwb_membership_for_woo_export_membership() {

		$screen = get_current_screen();

		if ( isset( $screen->id ) && ( 'edit-mwb_cpt_membership' == $screen->id ) ) {

			?>
			<input type="submit" name="export_all_membership" id="export_all_membership" class="button button-primary" value="Export All Plans">
			<script type="text/javascript">
				jQuery(function($) {
					$('#export_all_membership').insertAfter('#post-query-submit');
				});
			</script>
			<?php
		}
	}

	/**
	 * Export all Members data as CSV from Memberships.
	 */
	public function mwb_membership_for_woo_export_csv_membership() {

		if ( isset( $_GET['export_all_membership'] ) ) {

			global $post;

			$args = array(
				'post_type'   => 'mwb_cpt_membership',
				'post_status' => array( 'private', 'draft', 'pending', 'publish' ),
				'numberposts' => -1,
			);

			$all_posts = get_posts( $args );

			if ( ! empty( $all_posts ) ) {

				header( 'Content-type: text/csv' );
				header( 'Content-Disposition: attachment; filename="mwb_membership.csv"' );
				header( 'Pragma: no-cache' );
				header( 'Expires: 0' );

				$file = fopen( 'php://output', 'w' );

				fputcsv(
					$file,
					array(
						'Plan_id',
						'Plan_title',
						'Plan_price',
						'Plan_status',
						'Plan_products',
						'Plan_categories',
						'Plan_description',
					)
				);

				foreach ( $all_posts as $post ) {

					setup_postdata( $post );
					fputcsv(
						$file,
						array(
							get_the_ID(),
							get_post_field( 'post_title', get_post() ),
							get_post_meta( get_the_ID(), 'mwb_membership_plan_price', true ),
							get_post_field( 'post_status', get_post() ),
							mwb_membership_csv_get_title( get_post_meta( get_the_ID(), 'mwb_membership_plan_target_ids', true ) ),
							mwb_membership_csv_get_cat_title( get_post_meta( get_the_ID(), 'mwb_membership_plan_target_categories', true ) ),
							get_post_field( 'post_content', get_post() ),
						)
					);
				}

				exit();

			}
		}
	}

	/**
	 * Custom post type to display the list of all members.
	 */
	public function mwb_membership_for_woo_cpt_members() {

		$labels = array(
			'name'               => __( 'Members', 'membership-for-woocommerce' ),
			'singular_name'      => __( 'Member', 'membership-for-woocommerce' ),
			'add_new'            => __( 'Add Member', 'membership-for-woocommerce' ),
			'all_items'          => __( 'All Members', 'membership-for-woocommerce' ),
			'add_new_item'       => __( 'Add New Member', 'membership-for-woocommerce' ),
			'edit_item'          => __( 'Edit Member', 'membership-for-woocommerce' ),
			'new_item'           => __( 'New Member', 'membership-for-woocommerce' ),
			'view_item'          => __( 'View Member', 'membership-for-woocommerce' ),
			'search_item'        => __( 'Search Member', 'membership-for-woocommerce' ),
			'not_found'          => __( 'No Members Found', 'membership-for-woocommerce' ),
			'not_found_in_trash' => __( 'No Members Found In Trash', 'membership-for-woocommerce' ),
		);

		register_post_type(
			'mwb_cpt_members',
			array(
				'labels'              => $labels,
				'public'              => true,
				'has_archive'         => false,
				'publicly_queryable'  => true,
				'query_var'           => true,
				'capability_type'     => 'post',
				'hierarchical'        => false,
				'show_in_admin_bar'   => true,
				'show_in_menu'        => 'edit.php?post_type=mwb_cpt_membership',
				'menu_icon'           => 'dashicons-businessperson',
				'description'         => __( 'Displays the list of all members.', 'membership-for-woocommerce' ),
				'supports'            => array(
					'title',
					'editor',
					'excerpt',
				),
				'exclude_from_search' => false,
				'rewrite'             => array(
					'slug' => __( 'members', 'membership-for-woocommerce' ),
				),
			)
		);
	}

	/**
	 * Adding custom columns to the custom post type "Members".
	 *
	 * @param array $columns is an array of deafult columns in custom post type.
	 */
	public function mwb_membership_for_woo_cpt_columns_members( $columns ) {

		// Removing author and comments column.
		unset(
			$columns['wpseo-score'],
			$columns['wpseo-title'],
			$columns['wpseo-metadesc'],
			$columns['wpseo-focuskw']
		);

		// Adding new columns.
		$columns = array(
			'cb'                   => '<input type="checkbox" />',
			'membership_id'        => __( 'Membership ID', 'membership-for-woocommerce' ),
			'membership_status'    => __( 'Membership Status', 'membership-for-woocommerce' ),
			'membership_user'      => __( 'User', 'membership-for-woocommerce' ),
			'membership_user_view' => '',
			'expiration'           => __( 'Expiry Date', 'membership-for-woocommerce' ),
		);

		return $columns;

	}


	/**
	 * Populating custom columns with content.
	 *
	 * @param array   $column is an array of default columns in Custom post type.
	 * @param integer $post_id is the post id.
	 */
	public function mwb_membership_for_woo_fill_columns_members( $column, $post_id ) {

		switch ( $column ) {

			case 'membership_id':
				echo esc_html( get_the_title( $post_id ) );
				break;

			case 'membership_status':
				echo 'status';
				break;

			case 'membership_user':
				$author_id   = get_post_field( 'post_author', $post_id );
				$author_name = get_the_author_meta( 'user_nicename', $author_id );
				echo esc_html( $author_name );
				break;

			case 'membership_user_view':
				add_thickbox();
				?>
				<a title="Your Modal Title" href="#TB_inline?width=600&height=550&inlineId=modal-window-member" class="thickbox"><span class="dashicons dashicons-visibility"></span></a>

				<div id="modal-window-member" style="display:none;">
					<p>Lorem Ipsum sit dolla amet.</p>
				</div>

				<?php

				break;

			case 'expiration':
				echo 'expiry date';
				break;
		}
	}

	/**
	 * Select2 search for membership target products.
	 */
	public function search_products_for_membership() {

		$return         = array();
		$search_results = new WP_Query(
			array(
				's'                   => ! empty( $_GET['q'] ) ? sanitize_text_field( wp_unslash( $_GET['q'] ) ) : '',
				'post_type'           => array( 'product', 'product_variation' ),
				'post_status'         => array( 'publish' ),
				'ignore_sticky_posts' => 1,
				'posts_per_page'      => -1,
			)
		);

		if ( $search_results->have_posts() ) {

			while ( $search_results->have_posts() ) {

				$search_results->the_post();

				$title = ( mb_strlen( $search_results->post->post_title ) > 50 ) ? mb_substr( $search_results->post->post_title, 0, 49 ) . '...' : $search_results->post->post_title;

				/**
				 * Check for post type as query sometimes returns posts even after mentioning post_type.
				 * As some plugins alter query which causes issues.
				 */
				$post_type = get_post_type( $search_results->post->ID );

				if ( 'product' !== $post_type && 'product_variation' !== $post_type ) {

					continue;
				}

				$product      = wc_get_product( $search_results->post->ID );
				$downloadable = $product->is_downloadable();
				$stock        = $product->get_stock_status();
				$product_type = $product->get_type();

				$unsupported_product_types = array(
					'grouped',
					'external',
					'subscription',
					'variable-subscription',
					'subscription_variation',
				);

				if ( in_array( $product_type, $unsupported_product_types ) || 'outofstock' == $stock ) {

					continue;
				}

				$return[] = array( $search_results->post->ID, $title );
			}
		}
		echo json_encode( $return );

		wp_die();
	}

	/**
	 * Select2 search for membership target product categories.
	 */
	public function search_product_categories_for_membership() {

		$return = array();
		$args   = array(
			'search'   => ! empty( $_GET['q'] ) ? sanitize_text_field( wp_unslash( $_GET['q'] ) ) : '',
			'taxonomy' => 'product_cat',
			'orderby'  => 'name',
		);

		$product_categories = get_terms( $args );

		if ( ! empty( $product_categories ) && is_array( $product_categories ) && count( $product_categories ) ) {

			foreach ( $product_categories as $single_product_category ) {

				$cat_name = ( mb_strlen( $single_product_category->name ) > 50 ) ? mb_substr( $single_product_category, 0, 49 ) . '...' : $single_product_category->name;

				$return[] = array( $single_product_category->term_id, $single_product_category->name );

			}
		}
		echo json_encode( $return );

		wp_die();
	}

	/**
	 * Add export to csv button on Members CPT
	 */
	public function mwb_membership_for_woo_export_members() {

		$screen = get_current_screen();

		if ( isset( $screen->id ) && ( 'edit-mwb_cpt_members' == $screen->id ) ) {

			?>
			<input type="submit" name="export_all_members" id="export_all_members" class="button button-primary" value="Export All Members">
			<script type="text/javascript">
				jQuery(function($) {
					$('#export_all_members').insertAfter('#post-query-submit');
				});
			</script>
			<?php
		}
	}

	/**
	 * Export all Members data as CSV from members.
	 */
	public function mwb_membership_for_woo_export_csv_members() {

		if ( isset( $_GET['export_all_members'] ) ) {

			global $post;

			$args = array(
				'post_type'   => 'mwb_cpt_members',
				'numberposts' => -1,
			);

			$all_posts = get_posts( $args );

			if ( ! empty( $all_posts ) ) {

				header( 'Content-type: text/csv' );
				header( 'Content-Disposition: attachment; filename="mwb_members.csv"' );
				header( 'Pragma: no-cache' );
				header( 'Expires: 0' );

				$file = fopen( 'php://output', 'w' );

			}
		}
	}

	/**
	 * Creating shipping method for membership.
	 *
	 * @param array $methods an array of shipping methods.
	 */
	public function mwb_membership_for_woo_create_shipping_method( $methods ) {

		if ( ! class_exists( 'Mwb_Membership_free_shipping_method' ) ) {
			/**
			 * Custom shipping class for membership.
			 */
			require_once plugin_dir_path( __FILE__ ) . '/classes/class-mwb-membership-free-shipping-method.php'; // Including class file.
			new Mwb_Membership_Free_Shipping_Method();
		}
	}

	/**
	 * Adding mmebership shipping method.
	 *
	 * @param array $methods an array of shipping methods.
	 * @return array
	 */
	public function mwb_membership_for_woo_add_shipping_method( $methods ) {

		$methods['mwb_membership_shipping'] = 'Mwb_Membership_Free_Shipping_Method';

		return $methods;
	}

	/**
	 * Add Membership Support Column on payment gateway page.
	 *
	 * @param array $columns An array of default columns.
	 */
	public function mwb_membership_for_woo_gateway_support_column( $columns ) {

		$add_column['mwb_membership_gateways'] = esc_html__( 'Membership Support', 'membership-for-woocommerce' );

		// Position of new column.
		$position = count( $columns ) - 1;

		$columns = array_slice( $columns, 0, $position, true ) + $add_column + array_slice( $columns, $position, count( $columns ) - $position, true );

		return $columns;
	}

	/**
	 * Populating 'Membership support' column on payment gateway page.
	 *
	 * @param object $gateways Object of all payment gateways.
	 */
	public function mwb_membership_for_woo_gateway_column_content( $gateways ) {

		$supported_gateways = mwb_membership_for_woo_supported_gateways();

		echo '<td class="mwb_membership_gateways">';

		if ( in_array( $gateways->id, $supported_gateways ) ) {

			echo '<span class="status-enabled">' . esc_html__( 'Yes', 'membership-for-woocommerce' ) . '</span>';
		} else {

			echo '<span class="status-disabled">' . esc_html__( 'No', 'membership-for-woocommerce' ) . '</span>';
		}

		echo '</td>';

	}

	/**
	 * Add membership supported gateways.
	 *
	 * @param array $gateways An array of wooommerce default gateway classes.
	 * @return array $gateways An array of woocommerce gateway classes along with membership gateways.
	 */
	public function mwb_membership_for_supported_gateways( $gateways ) {

		if ( class_exists( 'Mwb_Membership_For_Woo_Paypal_Gateway' ) ) {

			$gateways[] = 'Mwb_Membership_For_Woo_Paypal_Gateway';
		}

		if ( class_exists( 'Mwb_Membership_For_Woo_Stripe_Gateway' ) ) {

			$gateways[] = 'Mwb_Membership_For_Woo_Stripe_Gateway';
		}

		return $gateways;
	}

	/**
	 * Include Membership supported payment gateway classes after plugins are loaded.
	 *
	 * @since       1.0.0
	 */
	public function mwb_membership_for_woo_plugins_loaded() {

		/**
		 * The class responsible for defining all methods of PayPal payment gateway.
		 */
		require_once MEMBERSHIP_FOR_WOOCOMMERCE_DIRPATH . 'admin/gateways/paypal/class-mwb-membership-for-woo-paypal-gateway.php';

		/**
		 * The class responsible for defining all methods of Stripe payment gateway.
		 */
		require_once MEMBERSHIP_FOR_WOOCOMMERCE_DIRPATH . 'admin/gateways/stripe/class-mwb-membership-for-woo-stripe-gateway.php';

		// Stripe library with composer.
		require_once MEMBERSHIP_FOR_WOOCOMMERCE_DIRPATH . 'admin/gateways/stripe/vendor/autoload.php';

	}
}
