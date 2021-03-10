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
 * @author     MakeWebBetter <plugins@makewebbetter.com>
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
	 * Creating Instance of the global functions class.
	 *
	 * @var object
	 */
	public $global_class;

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

		$this->global_class = Membership_For_Woocommerce_Global_Functions::get();

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

			if ( 'mwb_cpt_membership' === $pagescreen_post || 'mwb_cpt_membership' === $pagescreen_id ) {

				wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/membership-for-woocommerce-admin.css', array(), $this->version, 'all' );

				wp_enqueue_style( 'mwb_membership_for_woo_select2', plugin_dir_url( __FILE__ ) . 'css/select2.min.css', array(), $this->version, 'all' );

				wp_enqueue_style( 'wp-jquery-ui-dialog' );

			}

			if ( isset( $_GET['tab'] ) && 'shipping' === $_GET['tab'] ) {

				wp_enqueue_style( 'mwb_membership_for_woo_select2', plugin_dir_url( __FILE__ ) . 'css/select2.min.css', array(), $this->version, 'all' );
			}

			if ( 'mwb_cpt_members' === $pagescreen_post || 'mwb_cpt_members' === $pagescreen_id ) {

				wp_enqueue_style( 'members-admin-css', plugin_dir_url( __FILE__ ) . 'css/membership-for-woo-members-admin.css', array(), $this->version, 'all' );

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

			if ( 'mwb_cpt_membership' === $pagescreen_post || 'mwb_cpt_membership' === $pagescreen_id ) {

				wp_register_script( 'woocommerce_admin', WC()->plugin_url() . '/assets/js/admin/woocommerce_admin.js', array( 'jquery', 'jquery-blockui', 'jquery-ui-sortable', 'jquery-ui-widget', 'jquery-ui-core', 'jquery-tiptip', 'wc-enhanced-select' ), MEMBERSHIP_FOR_WOOCOMMERCE_VERSION, false );

				$locale  = localeconv();
				$decimal = isset( $locale['decimal_point'] ) ? $locale['decimal_point'] : '.';

				$params = array(
					/* translators: %s: decimal */
					'i18n_decimal_error'                => sprintf( esc_html__( 'Please enter with one decimal point (%s) without thousand separators.', 'membership-for-woocommerce' ), $decimal ),
					/* translators: %s: price decimal separator */
					'i18n_mon_decimal_error'            => sprintf( esc_html__( 'Please enter with one monetary decimal point (%s) without thousand separators and currency symbols.', 'membership-for-woocommerce' ), wc_get_price_decimal_separator() ),
					'i18n_country_iso_error'            => esc_html__( 'Please enter in country code with two capital letters.', 'membership-for-woocommerce' ),
					'i18n_sale_less_than_regular_error' => esc_html__( 'Please enter in a value less than the regular price.', 'membership-for-woocommerce' ),
					'i18n_delete_product_notice'        => esc_html__( 'This product has produced sales and may be linked to existing orders. Are you sure you want to delete it?', 'membership-for-woocommerce' ),
					'i18n_remove_personal_data_notice'  => esc_html__( 'This action cannot be reversed. Are you sure you wish to erase personal data from the selected orders?', 'membership-for-woocommerce' ),
					'decimal_point'                     => $decimal,
					'non_decimal_point'                 => wc_get_price_decimal_separator(),
					'ajax_url'                          => admin_url( 'admin-ajax.php' ),
					'strings'                           => array(
						'import_products' => esc_html__( 'Import', 'membership-for-woocommerce' ),
						'export_products' => esc_html__( 'Export', 'membership-for-woocommerce' ),
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

				wp_enqueue_script( 'woocommerce_admin' );

				wp_enqueue_script( 'membership-for-woocommerce-select2', plugin_dir_url( __FILE__ ) . 'js/select2.min.js', array( 'jquery' ), $this->version, false );

				wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/membership-for-woocommerce-admin.js', array( 'jquery' ), $this->version, false );

				wp_localize_script(
					$this->plugin_name,
					'admin_ajax_obj',
					array(
						'ajaxurl' => admin_url( 'admin-ajax.php' ),
						'nonce'   => wp_create_nonce( 'plan-import-nonce' ),
					)
				);

				wp_enqueue_script( 'mwb_membership_for_woo_add_new_plan_script', plugin_dir_url( __FILE__ ) . 'js/mwb_membership_for_woo_add_new_plan_script.js', array( 'woocommerce_admin', 'wc-enhanced-select' ), $this->version, false );

				wp_localize_script( 'mwb_membership_for_woo_add_new_plan_script', 'ajax_url', admin_url( 'admin-ajax.php' ) );

				wp_enqueue_script( 'wp-color-picker' );

				add_thickbox();

				wp_enqueue_media();

				wp_enqueue_script( 'jquery-ui-dialog' );

				wp_enqueue_script( 'mwb_mmebership_sweet_alert', plugin_dir_url( __FILE__ ) . 'js/sweet-alert2.js', array( 'jquery' ), $this->version, false );

			}

			if ( isset( $_GET['section'] ) && 'membership-paypal-gateway' === $_GET['section'] ) {

				wp_enqueue_script( 'mwb-membership-paypal-script', plugin_dir_url( __FILE__ ) . 'js/membership-for-woocommerce-paypal.js', array( 'jquery' ), $this->version, false );

			} elseif ( isset( $_GET['section'] ) && 'membership-for-woo-stripe-gateway' === $_GET['section'] ) {

				wp_enqueue_script( 'mwb-membership-stripe-script', plugin_dir_url( __FILE__ ) . 'js/membership-for-woocommerce-stripe.js', array( 'jquery' ), $this->version, false );

			} elseif ( isset( $_GET['section'] ) && 'membership-adv-bank-transfer' === $_GET['section'] ) {

				wp_enqueue_script( 'mwb-membership-ad-bacs-script', plugin_dir_url( __FILE__ ) . 'js/membership-for-woocommerce-ad-bacs.js', array( 'jquery' ), $this->version, false );

			} elseif ( isset( $_GET['section'] ) && 'membership-paypal-smart-buttons' === $_GET['section'] ) {

				wp_enqueue_script( 'mwb-membership-paypal-sb-script', plugin_dir_url( __FILE__ ) . 'js/membership-paypal-express-checkout.js', array( 'jquery' ), $this->version, false );
			}

			if ( 'mwb_cpt_members' === $pagescreen_post || 'mwb_cpt_members' === $pagescreen_id ) {

				wp_enqueue_script( 'members-admin-script', plugin_dir_url( __FILE__ ) . 'js/membership-for-woo-member-admin.js', array( 'jquery' ), $this->version, false );

				wp_enqueue_script( 'membership-for-woocommerce-select2', plugin_dir_url( __FILE__ ) . 'js/select2.min.js', array( 'jquery' ), $this->version, false );

				wp_localize_script(
					'members-admin-script',
					'members_admin_obj',
					array(
						'ajaxurl' => admin_url( 'admin-ajax.php' ),
						'nonce'   => wp_create_nonce( 'members-nonce' ),
					)
				);

			}
		}

	}

	/**
	 * Register page IDs for Woocommerce.
	 *
	 * @param array $screen Screen ID.
	 */
	public function mwb_membership_set_wc_screen_ids( $screen ) {

		$screen_ids = array(
			'mwb_cpt_membership',
			'mwb_cpt_members',
			'mwb_cpt_membership_page_mwb-membership-for-woo-global-settings',
			'mwb_cpt_membership_page_mwb-membership-for-woo-shortcodes',
			'mwb_cpt_membership_page_mwb_membership-for-woo-gateways',
			'mwb_cpt_membership_page_mwb-membership-for-woo-overview',
		);

		$screen = array_merge( $screen_ids, $screen );

		return $screen;
	}

	/**
	 * Custom post type for membership plans creation and settings.
	 *
	 * @since 1.0.0
	 */
	public function mwb_membership_for_woo_cpt_membership() {

		$labels = array(
			'name'               => esc_html__( 'Memberships', 'membership-for-woocommerce' ),
			'singular_name'      => esc_html__( 'Membership', 'membership-for-woocommerce' ),
			'add_new'            => esc_html__( 'Add Membership plans', 'membership-for-woocommerce' ),
			'all_items'          => esc_html__( 'All Membership plans', 'membership-for-woocommerce' ),
			'add_new_item'       => esc_html__( 'Add New Membership plan', 'membership-for-woocommerce' ),
			'edit_item'          => esc_html__( 'Edit Membership plan', 'membership-for-woocommerce' ),
			'new_item'           => esc_html__( 'New Membership plan', 'membership-for-woocommerce' ),
			'view_item'          => esc_html__( 'View Membership plan', 'membership-for-woocommerce' ),
			'search_item'        => esc_html__( 'Search Membership plan', 'membership-for-woocommerce' ),
			'not_found'          => esc_html__( 'No Membership plans Found', 'membership-for-woocommerce' ),
			'not_found_in_trash' => esc_html__( 'No Membership plans Found In Trash', 'membership-for-woocommerce' ),
		);

		register_post_type(
			'mwb_cpt_membership',
			array(
				'labels'               => $labels,
				'public'               => true,
				'has_archive'          => false,
				'publicly_queryable'   => true,
				'query_var'            => true,
				'capability_type'      => 'post',
				'hierarchical'         => false,
				'show_in_admin_bar'    => true,
				'show_in_menu'         => true,
				'menu_position'        => 56,
				'menu_icon'            => 'dashicons-buddicons-buddypress-logo',
				'description'          => esc_html__( 'Membership Plans will be created here.', 'membership-for-woocommerce' ),
				'register_meta_box_cb' => array( $this, 'mwb_membership_for_woo_meta_box' ),
				'supports'             => array(
					'title',
					'editor',
				),
				'exclude_from_search'  => false,
				'rewrite'              => array(
					'slug' => esc_html__( 'membership', 'membership-for-woocommerce' ),
				),
			)
		);
	}

	/**
	 * Remove "Add Plans" submenu from Membership CPT.
	 */
	public function mwb_membership_remove_submenu() {

		if ( post_type_exists( 'mwb_cpt_membership' ) ) {

			remove_submenu_page( 'edit.php?post_type=mwb_cpt_membership', 'post-new.php?post_type=mwb_cpt_membership' );

		}

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
	 *
	 * @since 1.0.0
	 */
	public function mwb_membership_for_woo_global_settings() {

		$instance = $this->global_class;

		wc_get_template(
			'templates/membership-templates/mwb-membership-global-settings.php',
			array(
				'instance' => $instance,
			),
			'',
			MEMBERSHIP_FOR_WOOCOMMERCE_DIRPATH_ADMIN
		);
	}

	/**
	 * Callback function for shortcodes sub-menu page.
	 *
	 * @since 1.0.0
	 */
	public function mwb_membership_for_woo_overview() {

		wc_get_template( 'templates/membership-templates/mwb-membership-overview.php', array(), '', MEMBERSHIP_FOR_WOOCOMMERCE_DIRPATH_ADMIN );
	}

	/**
	 * Callback function for shortcodes sub-menu page.
	 *
	 * @since 1.0.0
	 */
	public function mwb_membership_for_woo_shortcodes() {

		$instance = $this->global_class;

		wc_get_template(
			'templates/membership-templates/mwb-membership-shortcodes.php',
			array(
				'instance' => $instance,
			),
			'',
			MEMBERSHIP_FOR_WOOCOMMERCE_DIRPATH_ADMIN
		);
	}

	/**
	 * Callback function for supported gateways sub-menu page.
	 *
	 * @since 1.0.0
	 */
	public function mwb_membership_for_woo_gateways() {

		$instance = $this->global_class;

		wc_get_template(
			'templates/membership-templates/mwb-membership-supported-gateway.php',
			array(
				'instance' => $instance,
			),
			'',
			MEMBERSHIP_FOR_WOOCOMMERCE_DIRPATH_ADMIN
		);
	}

	/**
	 * Adding custom column to the custom post type "Membership"
	 *
	 * @param array $columns is an array of deafult columns in custom post type.
	 *
	 * @since 1.0.0
	 */
	public function mwb_membership_for_woo_cpt_columns_membership( $columns ) {

		$columns['membership_view']   = '';
		$columns['membership_status'] = esc_html__( 'Membership Plan Status', 'membership-for-woocommerce' );
		$columns['membership_cost']   = esc_html__( 'Membership Plan Cost', 'membership-for-woocommerce' );

		return $columns;
	}

	/**
	 * Populating custom columns with content.
	 *
	 * @param array   $column is an array of default columns in Custom post type.
	 * @param integer $post_id is the post id.
	 *
	 * @since 1.0.0
	 */
	public function mwb_membership_for_woo_fill_columns_membership( $column, $post_id ) {

		switch ( $column ) {

			case 'membership_view':
				?>

				<a title="<?php echo esc_html__( 'Membership ID #', 'membership-for-woocommerce' ) . esc_html( $post_id ); ?>" href="admin-ajax.php?action=mwb_get_membership_content&post_id=<?php echo esc_html( $post_id ); ?>&nonce=<?php echo esc_html( wp_create_nonce( 'preview-nonce' ) ); ?>" class="thickbox"><img src="<?php echo esc_url( MEMBERSHIP_FOR_WOOCOMMERCE_URL . 'admin/resources/icons/eye-icon.svg' ); ?>" alt="eye"></span></a>

				<?php
				break;

			case 'membership_status':
				$plan_status = get_post_status( $post_id );

				if ( ! empty( $plan_status ) ) {

					// Display Sandbox mode if visibility is private.
					if ( 'private' === $plan_status ) {

						echo esc_html__( 'Sandbox', 'membership-for-woocommerce' );

					} elseif ( 'draft' === $plan_status || 'pending' === $plan_status ) { // Display sandbox mode if status is draft or pending.

						echo esc_html__( 'Sandbox', 'membership-for-woocommerce' );

					} else { // Display live mode.

						echo esc_html__( 'Live', 'membership-for-woocommerce' );
					}
				}

				break;

			case 'membership_cost':
				$plan_cost = get_post_meta( $post_id, 'mwb_membership_plan_price', true );
				$currency  = get_woocommerce_currency_symbol();

				if ( ! empty( $currency ) && ! empty( $plan_cost ) ) {

					echo sprintf( ' %s %s ', esc_html( $currency ), esc_html( $plan_cost ) );

				}

				break;
		}

	}

	/**
	 * Get membership post data ( Ajax handler)
	 *
	 * @return void
	 *
	 * @since 1.0.0
	 */
	public function mwb_get_membership_content() {

		// Nonce verification.
		check_ajax_referer( 'preview-nonce', 'nonce' );

		$plan_id  = ! empty( $_GET['post_id'] ) ? sanitize_text_field( wp_unslash( $_GET['post_id'] ) ) : '';
		$instance = $this->global_class;

		wc_get_template(
			'templates/admin-ajax-templates/membership-plan-preview.php',
			array(
				'post_id'  => $plan_id,
				'instance' => $instance,
			),
			'',
			MEMBERSHIP_FOR_WOOCOMMERCE_DIRPATH_ADMIN
		);

		wp_die();
	}

	/**
	 * Register Custom Meta box for Membership plans creation.
	 *
	 * @since 1.0.0
	 */
	public function mwb_membership_for_woo_meta_box() {

		add_meta_box( 'members_meta_box', esc_html__( 'Create Plan', 'membership-for-woocommerce' ), array( $this, 'mwb_membership_meta_box_callback' ), 'mwb_cpt_membership' );
	}

	/**
	 * Callback funtion for custom meta boxes.
	 *
	 * @param string $post Current post object.
	 *
	 * @since 1.0.0
	 */
	public function mwb_membership_meta_box_callback( $post ) {

		$this->set_plan_creation_fields( get_the_ID() );

		$settings_fields = $this->settings_fields;
		$instance        = $this->global_class;

		wc_get_template(
			'templates/membership-templates/mwb-membership-plans-creation.php',
			array(
				'settings_fields' => $settings_fields,
				'instance'        => $instance,
			),
			'',
			MEMBERSHIP_FOR_WOOCOMMERCE_DIRPATH_ADMIN
		);
	}

	/**
	 * Save meta box fields value.
	 *
	 * @param int $post_id Post ID.
	 *
	 * @since 1.0.0
	 */
	public function mwb_membership_for_woo_save_fields( $post_id ) {

		// Return if doing autosave.
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		// Return if doing ajax :: Quick edits.
		if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
			return;
		}

		// Return on post trash, quick-edit, new post.
		if ( empty( $_POST['action'] ) || 'editpost' != $_POST['action'] ) {
			return;
		}

		// Nonce verification.
		check_admin_referer( 'mwb_membership_plans_creation_nonce', 'mwb_membership_plans_nonce' );

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
	 * Add notices for free membership to plans.
	 */
	public function mwb_membership_shipping_notice() {

		global $post;

		$screen = get_current_screen();

		$post_id = isset( $_GET['post'] ) ? sanitize_text_field( wp_unslash( $_GET['post'] ) ) : '';

		if ( ! empty( $post_id ) ) {

			$free_shipping = get_post_meta( $post_id, 'mwb_memebership_plan_free_shipping', true );

			$page_id = $screen->id;

			if ( 'mwb_cpt_membership' == $page_id ) {

				if ( $post->post_date_gmt == $post->post_modified_gmt ) {

					if ( 'publish' == $post->post_status ) {

						if ( ! empty( $free_shipping ) && 'yes' == $free_shipping ) {
							?>
							<div class="notice notice-success is-dismissible mwb-notice"> 
								<p><strong><?php esc_html_e( 'Membership plan published successfully, Now you can manage membership free shipping ', 'membership-for-woocommerce' ); ?></strong></p>
							</div>
							<?php
						}
					}
				}
			}
		}
	}

	/**
	 * Define Membership default settings fields.
	 *
	 * @return array
	 *
	 * @since 1.0.0
	 */
	public function get_plans_default_value() {

		return array(
			'mwb_membership_plan_price'              => array( 'default' => '0' ),
			'mwb_membership_plan_name_access_type'   => array( 'default' => 'lifetime' ),
			'mwb_membership_plan_duration'           => array( 'default' => '0' ),
			'mwb_membership_plan_duration_type'      => array( 'default' => 'days' ),
			'mwb_membership_plan_recurring'          => array( 'default' => '' ),
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
	 *
	 * @since 1.0.0
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
	 *
	 * @since 1.0.0
	 */
	public function mwb_membership_for_woo_export_membership() {

		$screen = get_current_screen();

		if ( isset( $screen->id ) && ( 'edit-mwb_cpt_membership' === $screen->id ) ) {

			$this->global_class->import_csv_modal_content();
			?>

			<input type="submit" name="export_all_membership" id="export_all_membership" class="button button-primary" value="<?php esc_html_e( 'Export Plans', 'membership-for-woocommerce' ); ?>">
			<input type="submit" name="import_all_membership" id="import_all_membership" class="button button-primary" value="<?php esc_html_e( 'Import Plans', 'membership-for-woocommerce' ); ?>">

			<?php
		}
	}

	/**
	 * Export all Plans data as CSV from Memberships.
	 *
	 * @since 1.0.0
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
						'Plan_status',
						'Plan_price',
						'Plan_access_type',
						'Plan_duration',
						'Plan_duration_type',
						'Plan_recurring',
						'Plan_access_type',
						'Plan_access_duration',
						'Plan_access_duration_type',
						'Plan_discount_type',
						'Plan_discount_price',
						'Plan_allow_free_shipping',
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
							get_post_field( 'post_status', get_post() ),
							get_post_meta( get_the_ID(), 'mwb_membership_plan_price', true ),
							get_post_meta( get_the_ID(), 'mwb_membership_plan_name_access_type', true ),
							get_post_meta( get_the_ID(), 'mwb_membership_plan_duration', true ),
							get_post_meta( get_the_ID(), 'mwb_membership_plan_duration_type', true ),
							get_post_meta( get_the_ID(), 'mwb_membership_plan_recurring', true ),
							get_post_meta( get_the_ID(), 'mwb_membership_plan_access_type', true ),
							get_post_meta( get_the_ID(), 'mwb_membership_plan_time_duration', true ),
							get_post_meta( get_the_ID(), 'mwb_membership_plan_time_duration_type', true ),
							get_post_meta( get_the_ID(), 'mwb_membership_plan_offer_price_type', true ),
							get_post_meta( get_the_ID(), 'mwb_memebership_plan_discount_price', true ),
							get_post_meta( get_the_ID(), 'mwb_memebership_plan_free_shipping', true ),
							$this->global_class->csv_get_prod_title( get_post_meta( get_the_ID(), 'mwb_membership_plan_target_ids', true ) ),
							$this->global_class->csv_get_cat_title( get_post_meta( get_the_ID(), 'mwb_membership_plan_target_categories', true ) ),
							get_post_field( 'post_content', get_post() ),
						)
					);
				}

				fclose( $file );
				exit;

			}
		}
	}

	/**
	 * Callback function for file Upload and import.
	 */
	public function csv_file_upload() {

		$nonce = ! empty( $_POST['nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['nonce'] ) ) : '';

		// Nonce Verification.
		check_ajax_referer( 'plan-import-nonce', 'nonce' );

		// Handling file upload using activity helper class..
		$activity_class = new Membership_Activity_Helper( 'csv-uploads', 'uploads' );

		$csv_file    = ! empty( $_FILES['file'] ) ? $_FILES['file'] : '';
		$upload_file = $activity_class->do_upload( $csv_file, array( 'csv' ) );

		if ( ! current_user_can( 'edit_posts' ) ) {
			exit;
		}

		if ( $upload_file && ( true === $upload_file['result'] ) ) {

			$file_url = $upload_file['url'];
			$csv      = array_map( 'str_getcsv', file( $file_url ) );

			unset( $csv[0] ); // Removing first key after CSV data is converted to array.

			// Getting a formatted CSV data.
			$formatted_csv_data = $this->global_class->csv_data_map( $csv );

			// Getting all Product titles from woocommerce store.
			$all_prod_title = $this->global_class->all_prod_title();

			// Getting all Category titles from woocommerce store.
			$all_cat_title = $this->global_class->all_cat_title();

			$prd_check = '';
			$cat_check = '';

			$csv_prod_title = $this->global_class->csv_prod_title( $csv ); // Getting all product titles from csv.
			$csv_cate_title = $this->global_class->csv_cat_title( $csv ); // Getting all category titles from csv.

			if ( is_array( $csv_prod_title ) && is_array( $csv_cate_title ) ) {

				foreach ( $csv_prod_title as $key => $value ) {

					if ( in_array( $value, $all_prod_title, true ) ) {

						$prd_check = true;
					}
				}

				foreach ( $csv_cate_title as $key => $value ) {

					if ( in_array( $value, $all_cat_title, true ) ) {

						$cat_check = true;
					}
				}
			}

			// If product ids and category ids from csv match from those of woocommerce, then only import the file.
			if ( true === $prd_check && true === $cat_check ) {

				foreach ( $formatted_csv_data as $key => $value ) {
					$plan_id = wp_insert_post(
						array(
							'post_type'    => 'mwb_cpt_membership',
							'post_title'   => $value['post_title'],
							'post_status'  => $value['post_status'],
							'post_content' => $value['post_content'],
						),
						true
					);

					update_post_meta( $plan_id, 'mwb_membership_plan_price', $value['mwb_membership_plan_price'] );
					update_post_meta( $plan_id, 'mwb_membership_plan_name_access_type', $value['mwb_membership_plan_name_access_type'] );
					update_post_meta( $plan_id, 'mwb_membership_plan_duration', $value['mwb_membership_plan_duration'] );
					update_post_meta( $plan_id, 'mwb_membership_plan_duration_type', $value['mwb_membership_plan_duration_type'] );
					update_post_meta( $plan_id, 'mwb_membership_plan_recurring', $value['mwb_membership_plan_recurring'] );
					update_post_meta( $plan_id, 'mwb_membership_plan_access_type', $value['mwb_membership_plan_access_type'] );
					update_post_meta( $plan_id, 'mwb_membership_plan_time_duration', $value['mwb_membership_plan_time_duration'] );
					update_post_meta( $plan_id, 'mwb_membership_plan_time_duration_type', $value['mwb_membership_plan_time_duration_type'] );
					update_post_meta( $plan_id, 'mwb_membership_plan_offer_price_type', $value['mwb_membership_plan_offer_price_type'] );
					update_post_meta( $plan_id, 'mwb_memebership_plan_discount_price', $value['mwb_memebership_plan_discount_price'] );
					update_post_meta( $plan_id, 'mwb_memebership_plan_free_shipping', $value['mwb_memebership_plan_free_shipping'] );
					update_post_meta( $plan_id, 'mwb_membership_plan_target_ids', $value['mwb_membership_plan_target_ids'] );
					update_post_meta( $plan_id, 'mwb_membership_plan_target_categories', $value['mwb_membership_plan_target_categories'] );
				}

				echo wp_json_encode(
					array(
						'status'   => 'success',
						'message'  => 'File Imported Successfully',
						'redirect' => admin_url( 'edit.php?post_type=mwb_cpt_membership' ),
					)
				);

			} else {

				echo wp_json_encode(
					array(
						'status'   => 'failed',
						'message'  => 'Something Went Wrong. Either Products or Categories are not available!',
						'redirect' => admin_url( 'edit.php?post_type=mwb_cpt_membership' ),
					)
				);
			}
		}

		wp_die();
	}

	/**
	 * Custom post type to display the list of all members.
	 *
	 * @since 1.0.0
	 */
	public function mwb_membership_for_woo_cpt_members() {

		$labels = array(
			'name'               => esc_html__( 'Members', 'membership-for-woocommerce' ),
			'singular_name'      => esc_html__( 'Member', 'membership-for-woocommerce' ),
			'add_new'            => esc_html__( 'Add Member', 'membership-for-woocommerce' ),
			'all_items'          => esc_html__( 'All Members', 'membership-for-woocommerce' ),
			'add_new_item'       => esc_html__( 'Add New Member', 'membership-for-woocommerce' ),
			'edit_item'          => esc_html__( 'Edit Member', 'membership-for-woocommerce' ),
			'new_item'           => esc_html__( 'New Member', 'membership-for-woocommerce' ),
			'view_item'          => esc_html__( 'View Member', 'membership-for-woocommerce' ),
			'search_item'        => esc_html__( 'Search Member', 'membership-for-woocommerce' ),
			'not_found'          => esc_html__( 'No Members Found', 'membership-for-woocommerce' ),
			'not_found_in_trash' => esc_html__( 'No Members Found In Trash', 'membership-for-woocommerce' ),
		);

		register_post_type(
			'mwb_cpt_members',
			array(
				'labels'               => $labels,
				'public'               => true,
				'has_archive'          => false,
				'publicly_queryable'   => true,
				'query_var'            => true,
				'capability_type'      => 'post',
				'hierarchical'         => false,
				'show_in_admin_bar'    => true,
				'show_in_menu'         => 'edit.php?post_type=mwb_cpt_membership',
				'menu_icon'            => 'dashicons-businessperson',
				'description'          => esc_html__( 'Displays the list of all members.', 'membership-for-woocommerce' ),
				'register_meta_box_cb' => array( $this, 'membership_for_woo_members_metabox' ),
				'exclude_from_search'  => false,
				'rewrite'              => array(
					'slug' => esc_html__( 'members', 'membership-for-woocommerce' ),
				),
			)
		);
	}

	/**
	 * Remove post type support from Members post type
	 */
	public function membership_for_woo_remove_fields() {

		remove_post_type_support( 'mwb_cpt_members', 'title' );
		remove_post_type_support( 'mwb_cpt_members', 'editor' );

	}

	/**
	 * Remove bulk actions for members post.
	 *
	 * @param array $actions An array of bulk actions options.
	 *
	 * @return array.
	 * @since 1.0.0
	 */
	public function membership_for_woo_members_remove_bulkaction( $actions ) {

		unset( $actions['edit'] );
		unset( $actions['trash'] );

		return $actions;
	}

	/**
	 * Remove quick edit from members.
	 *
	 * @param array $actions An array of quick action on hover.
	 *
	 * @return array.
	 * @since 1.0.0
	 */
	public function membership_for_woo_remove_quick_edit( $actions ) {

		global $post;

		if ( 'mwb_cpt_members' == $post->post_type ) {

			unset( $actions['trash'] );
			unset( $actions['view'] );
			unset( $actions['inline hide-if-no-js'] );
		}
		return $actions;
	}

	/**
	 * Register custom meta box for members custom post type.
	 *
	 * @since 1.0.0
	 */
	public function membership_for_woo_members_metabox() {

		// Add membership details metabox.
		add_meta_box( 'members_meta_box', esc_html__( 'Membership Details', 'membership-for-woocommerce' ), array( $this, 'mwb_members_metabox_callback' ), 'mwb_cpt_members' );

		// Add billing details metabox.
		add_meta_box( 'members_metabox_billing', esc_html__( 'Billing details', 'membership-for-woocommerce' ), array( $this, 'mwb_members_metabox_billing' ), 'mwb_cpt_members', 'normal', 'high' );

		// Add schedule metabox.
		add_meta_box( 'members_metabox_schedule', esc_html__( 'Schedule', 'membership-for-woocommerce' ), array( $this, 'mwb_members_metabox_schedule' ), 'mwb_cpt_members', 'side', 'high' );

		// Remove sumitdiv metabox for mwb_cpt_members.
		remove_meta_box( 'submitdiv', 'mwb_cpt_members', 'side' );

		// Add custom member actions metabox.
		add_meta_box( '_submitdiv', esc_html__( 'Member actions', 'membership-for-woocommerce' ), array( $this, 'member_actions_callback' ), 'mwb_cpt_members', 'side', 'core' );

	}

	/**
	 * Members metabox callback.
	 *
	 * @param object $post is the post object.
	 * @since 1.0.0
	 */
	public function mwb_members_metabox_callback( $post ) {

		// Add a single nonce field to post.
		wp_nonce_field( 'mwb_members_creation_nonce', 'mwb_members_nonce_field' );

		$plan     = get_post_meta( $post->ID, 'plan_obj', true );
		$instance = $this->global_class;

		wc_get_template(
			'templates/members-templates/mwb-members-plans-details.php',
			array(
				'plan'     => $plan,
				'instance' => $instance,
			),
			'',
			MEMBERSHIP_FOR_WOOCOMMERCE_DIRPATH_ADMIN
		);
	}

	/**
	 * Members billing metabox callback.
	 *
	 * @param object $post is the post object.
	 * @since 1.0.0
	 */
	public function mwb_members_metabox_billing( $post ) {

		$member         = $post;
		$member_details = get_post_meta( $post->ID, 'billing_details', true );
		$instance       = $this->global_class;

		wc_get_template(
			'templates/members-templates/mwb-members-plans-billing.php',
			array(
				'member_details' => $member_details,
				'instance'       => $instance,
				'post'           => $member,
			),
			'',
			MEMBERSHIP_FOR_WOOCOMMERCE_DIRPATH_ADMIN
		);

	}

	/**
	 * Members billing metabox save.
	 *
	 * @param int $post_id is the post ID.
	 * @since 1.0.0
	 */
	public function mwb_membership_save_member_fields( $post_id ) {

		// Return if doing autosave.
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		// Return if doing ajax.
		if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
			return;
		}

		// Return on post trash, quick-edit, new post.
		if ( empty( $_POST['save'] ) ) {
			return;
		}

		// Nonce verification.
		check_admin_referer( 'mwb_members_creation_nonce', 'mwb_members_nonce_field' );

		// Saving member actions metabox fields.
		$actions = array(
			'member_status'  => ! empty( $_POST['member_status'] ) ? sanitize_text_field( wp_unslash( $_POST['member_status'] ) ) : '',
			'member_actions' => ! empty( $_POST['member_actions'] ) ? sanitize_text_field( wp_unslash( $_POST['member_actions'] ) ) : '',
		);

		// If manually completing membership then set its expiry date.
		if ( 'complete' == $_POST['member_status'] ) {

			// Getting current activation date.
			$current_date = gmdate( 'Y-m-d' );

			$plan_obj = get_post_meta( $post_id, 'plan_obj', true );

			// Save expiry date in post.
			if ( ! empty( $plan_obj ) ) {

				if ( 'lifetime' == $plan_obj['mwb_membership_plan_name_access_type'] ) {

					update_post_meta( $post_id, 'member_expiry', 'Lifetime' );

				} elseif ( 'limited' == $plan_obj['mwb_membership_plan_name_access_type'] ) {

					$duration = $plan_obj['mwb_membership_plan_duration'] . ' ' . $plan_obj['mwb_membership_plan_duration_type'];

					$expiry_date = strtotime( $current_date . $duration );

					update_post_meta( $post_id, 'member_expiry', $expiry_date );
				}
			}
		}

		foreach ( $actions as $action => $value ) {

			if ( array_key_exists( $action, $_POST ) ) {

				update_post_meta( $post_id, $action, $value );
			}
		}

		// Saving member billing details metabox fields.
		if ( isset( $_POST['payment_gateway_select'] ) ) {

			$payment = ! empty( $_POST['payment_gateway_select'] ) ? sanitize_text_field( wp_unslash( $_POST['payment_gateway_select'] ) ) : '';

		} else {

			$payment = ! empty( $_POST['billing_payment'] ) ? sanitize_text_field( wp_unslash( $_POST['billing_payment'] ) ) : '';
		}

		$fields = array(
			'membership_billing_first_name' => ! empty( $_POST['billing_first_name'] ) ? sanitize_text_field( wp_unslash( $_POST['billing_first_name'] ) ) : '',
			'membership_billing_last_name'  => ! empty( $_POST['billing_last_name'] ) ? sanitize_text_field( wp_unslash( $_POST['billing_last_name'] ) ) : '',
			'membership_billing_company'    => ! empty( $_POST['billing_company'] ) ? sanitize_text_field( wp_unslash( $_POST['billing_company'] ) ) : '',
			'membership_billing_address_1'  => ! empty( $_POST['billing_address_1'] ) ? sanitize_text_field( $_POST['billing_address_1'] ) : '',
			'membership_billing_address_2'  => ! empty( $_POST['billing_address_2'] ) ? sanitize_text_field( $_POST['billing_address_2'] ) : '',
			'membership_billing_city'       => ! empty( $_POST['billing_city'] ) ? sanitize_text_field( wp_unslash( $_POST['billing_city'] ) ) : '',
			'membership_billing_postcode'   => ! empty( $_POST['billing_postcode'] ) ? sanitize_text_field( wp_unslash( $_POST['billing_postcode'] ) ) : '',
			'membership_billing_country'    => ! empty( $_POST['billing_country'] ) ? sanitize_text_field( wp_unslash( $_POST['billing_country'] ) ) : '',
			'membership_billing_state'      => ! empty( $_POST['billing_state'] ) ? sanitize_text_field( wp_unslash( $_POST['billing_state'] ) ) : '',
			'membership_billing_email'      => ! empty( $_POST['billing_email'] ) ? sanitize_text_field( wp_unslash( $_POST['billing_email'] ) ) : '',
			'membership_billing_phone'      => ! empty( $_POST['billing_phone'] ) ? sanitize_text_field( wp_unslash( $_POST['billing_phone'] ) ) : '',
			'payment_method'                => $payment,
		);

		update_post_meta( $post_id, 'billing_details', $fields );

		// When plans are assigned manually.
		if ( isset( $_POST['members_plan_assign'] ) ) {

			$plan_id = ! empty( $_POST['members_plan_assign'] ) ? sanitize_text_field( wp_unslash( $_POST['members_plan_assign'] ) ) : '';

			if ( ! empty( $plan_id ) ) {

				$plan_obj = get_post( $plan_id, ARRAY_A );

				$post_meta = get_post_meta( $plan_id );

				// Formatting array.
				foreach ( $post_meta as $key => $value ) {

					$post_meta[ $key ] = reset( $value );
				}

				$plan_meta = array_merge( $plan_obj, $post_meta );

				update_post_meta( $post_id, 'plan_obj', $plan_meta );
			}
		}

	}

	/**
	 * Members schedule metabox callback.
	 *
	 * @param object $post is the post object.
	 * @since 1.0.0
	 */
	public function mwb_members_metabox_schedule( $post ) {

		$member = $post;

		wc_get_template(
			'templates/members-templates/mwb-members-plans-schedule.php',
			array(
				'post' => $member,
			),
			'',
			MEMBERSHIP_FOR_WOOCOMMERCE_DIRPATH_ADMIN
		);

	}

	/**
	 * Members publish metabox callback.
	 *
	 * @param object $post Post object.
	 * @since 1.0.0
	 */
	public function member_actions_callback( $post ) {

		$member  = $post;
		$actions = get_post_meta( $post->ID, 'member_actions', true );
		$status  = get_post_meta( $post->ID, 'member_status', true );

		wc_get_template(
			'templates/members-templates/mwb-members-actions.php',
			array(
				'post'    => $member,
				'actions' => $actions,
				'status'  => $status,
			),
			'',
			MEMBERSHIP_FOR_WOOCOMMERCE_DIRPATH_ADMIN
		);

	}

	/**
	 * Adding custom columns to the custom post type "Members".
	 *
	 * @param array $columns is an array of deafult columns in custom post type.
	 *
	 * @since 1.0.0
	 */
	public function mwb_membership_for_woo_cpt_columns_members( $columns ) {

		// Adding new columns.
		$columns = array(
			'cb'                   => '<input type="checkbox" />',
			'membership_id'        => esc_html__( 'Membership ID', 'membership-for-woocommerce' ),
			'members_status'       => esc_html__( 'Membership Status', 'membership-for-woocommerce' ),
			'membership_user'      => esc_html__( 'User', 'membership-for-woocommerce' ),
			'membership_user_view' => '',
			'expiration'           => esc_html__( 'Expiry Date', 'membership-for-woocommerce' ),
		);

		return $columns;

	}


	/**
	 * Populating custom columns with content.
	 *
	 * @param array   $column is an array of default columns in Custom post type.
	 * @param integer $post_id is the post id.
	 *
	 * @since 1.0.0
	 */
	public function mwb_membership_for_woo_fill_columns_members( $column, $post_id ) {

		switch ( $column ) {

			case 'membership_id':
				$author_id    = get_post_field( 'post_author', $post_id );
				$display_name = get_the_author_meta( 'display_name', $author_id );
				?>
				<strong><?php echo sprintf( ' #%u %s ', esc_html( $post_id ), esc_html( $display_name ) ); ?></strong>
				<?php
				break;

			case 'members_status':
				$status = get_post_meta( $post_id, 'member_status', true );
				echo esc_html( $status );
				break;

			case 'membership_user':
				$author_id   = get_post_field( 'post_author', $post_id );
				$author_name = get_the_author_meta( 'user_nicename', $author_id );

				echo esc_html( $author_name );
				break;

			case 'membership_user_view':
				add_thickbox();
				?>
				<a title="<?php echo esc_html__( 'Member ID #', 'membership-for-woocommerce' ) . esc_html( $post_id ); ?>" href="admin-ajax.php?action=mwb_get_member_content&post_id=<?php echo esc_html( $post_id ); ?>&nonce=<?php echo esc_html( wp_create_nonce( 'preview-nonce' ) ); ?>" class="thickbox member-preview"><img src="<?php echo esc_url( MEMBERSHIP_FOR_WOOCOMMERCE_URL . 'admin/resources/icons/eye-icon.svg' ); ?>" alt="eye"></a>

				<?php

				break;

			case 'expiration':
				$expiry = get_post_meta( $post_id, 'member_expiry', true );

				if ( 'Lifetime' == $expiry ) {
					echo esc_html( ! empty( $expiry ) ? $expiry : '' );
				} else {
					echo esc_html( ! empty( $expiry ) ? gmdate( 'Y-m-d', $expiry ) : '' );
				}

				break;
		}
	}

	/**
	 * Get members post data (Ajax handler).
	 *
	 * @return void
	 * @since 1.0.0
	 */
	public function mwb_get_member_content() {

		// Nonce Verification.
		check_ajax_referer( 'preview-nonce', 'nonce' );

		$member_id = ! empty( $_GET['post_id'] ) ? sanitize_text_field( wp_unslash( $_GET['post_id'] ) ) : '';
		$instance  = $this->global_class;

		wc_get_template(
			'templates/admin-ajax-templates/members-plans-preview.php',
			array(
				'member_id' => $member_id,
				'instance'  => $instance,
			),
			'',
			MEMBERSHIP_FOR_WOOCOMMERCE_DIRPATH_ADMIN
		);
		wp_die();
	}

	/**
	 * Select2 search for membership target products.
	 *
	 * @since 1.0.0
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

				if ( in_array( $product_type, $unsupported_product_types, true ) || 'outofstock' === $stock ) {

					continue;
				}

				$return[] = array( $search_results->post->ID, $title );

			}
		}
		echo wp_json_encode( $return );

		wp_die();
	}

	/**
	 * Select2 search for membership target product categories.
	 *
	 * @since 1.0.0
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
		echo wp_json_encode( $return );

		wp_die();
	}

	/**
	 * Add export to csv button on Members CPT
	 *
	 * @since 1.0.0
	 */
	public function mwb_membership_for_woo_export_members() {

		$screen = get_current_screen();

		if ( isset( $screen->id ) && ( 'edit-mwb_cpt_members' === $screen->id ) ) {

			?>
			<input type="submit" name="export_all_members" id="export_all_members" class="button button-primary" value="<?php esc_html_e( 'Export Members', 'membership-foe-woocommerce' ); ?>">
			<?php
		}
	}

	/**
	 * Export all Members data as CSV from members.
	 *
	 * @since 1.0.0
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

				fputcsv(
					$file,
					array(
						'Member ID',
						'User Name',
						'User Email',
						'Member Name',
						'Member Email ',
						'Member Phone No.',
						'Payment Method',
						'Plan ID',
						'Plan Name',
						'Membership Status',
						'Expiry Date',
					)
				);

				foreach ( $all_posts as $post ) {
					setup_postdata( $post );
					fputcsv(
						$file,
						array(
							get_the_ID(),
							! empty( $post->post_author ) ? get_the_author_meta( 'display_name', $post->post_author ) : '',
							get_the_author_meta( 'user_email' ),
							$this->global_class->get_member_details( $post, 'name' ),
							$this->global_class->get_member_details( $post, 'email' ),
							$this->global_class->get_member_details( $post, 'phone' ),
							$this->global_class->get_member_details( $post, 'payment_method' ),
							$this->global_class->get_member_details( $post, 'plan_id' ),
							$this->global_class->get_member_details( $post, 'plan_name' ),
							$this->global_class->get_member_details( $post, 'plan_status' ),
							'',
						)
					);

				}
				fclose( $file );
				exit;
			}
		}
	}

	/**
	 * Creating shipping method for membership.
	 *
	 * @param array $methods an array of shipping methods.
	 *
	 * @since 1.0.0
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
	 * Adding membership shipping method.
	 *
	 * @param array $methods an array of shipping methods.
	 * @return array
	 *
	 * @since 1.0.0
	 */
	public function mwb_membership_for_woo_add_shipping_method( $methods ) {

		$methods['mwb_membership_shipping'] = 'Mwb_Membership_Free_Shipping_Method';

		return $methods;
	}

	/**
	 * Add Membership Support Column on payment gateway page.
	 *
	 * @param array $columns An array of default columns.
	 *
	 * @since 1.0.0
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
	 *
	 * @since 1.0.0
	 */
	public function mwb_membership_for_woo_gateway_column_content( $gateways ) {

		$supported_gateways = $this->global_class->supported_gateways();

		echo '<td class="mwb_membership_gateways">';

		if ( in_array( $gateways->id, $supported_gateways, true ) ) {

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
	 *
	 * @since 1.0.0
	 */
	public function mwb_membership_for_supported_gateways( $gateways ) {

		if ( class_exists( 'Mwb_Membership_Adv_Bank_Transfer' ) ) {

			$gateways[] = 'Mwb_Membership_Adv_Bank_Transfer';
		}

		if ( class_exists( 'Membership_Paypal_Express_Checkout' ) ) {

			$gateways[] = 'Membership_Paypal_Express_Checkout';
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
		 * The class responsible for defining all methods of PayPal express checkout payment gateway.
		 */
		require_once MEMBERSHIP_FOR_WOOCOMMERCE_DIRPATH . 'admin/gateways/paypal express checkout/class-membership-paypal-express-checkout.php';

		/**
		 * The class responsible for defining all methods of advance bank transfer gateway.
		 */
		require_once MEMBERSHIP_FOR_WOOCOMMERCE_DIRPATH . 'admin/gateways/advance bank tranfer/class-mwb-membership-adv-bank-transfer.php';
	}

	/**
	 * Add post stats to Membership default offer page.
	 *
	 * @param array  $states An array of post display states.
	 * @param object $post Current post object.
	 *
	 * @since 1.0.0
	 */
	public function mwb_membership_default_page_states( $states, $post ) {

		if ( 'membership-plans' === get_post_field( 'post_name', $post->ID ) ) {

			$states[] = esc_html__( 'Membership Default Page', 'membership-for-woocommerce' );
		}

		return $states;

	}

	/**
	 *  Adding distraction free mode to the offers page.
	 *
	 * @param mixed $page_template Default template for the page.
	 *
	 * @since 1.0.0
	 */
	public function mwb_membership_plan_page_template( $page_template ) {

		$pages_available = get_posts(
			array(
				'post_type'      => 'any',
				'post_status'    => 'publish',
				'posts_per_page' => -1,
				'pagename'       => 'membership-plans',
				'order'          => 'ASC',
				'orderby'        => 'ID',
			)
		);

		$pages_available = array_merge(
			get_posts(
				array(
					'post_type'      => 'any',
					'post_status'    => 'publish',
					'posts_per_page' => -1,
					's'              => '[mwb_membership_default_page_identification]',
					'order'          => 'ASC',
					'orderby'        => 'ID',
				)
			),
			$pages_available
		);

		foreach ( $pages_available as $single_page ) {

			if ( is_page( $single_page->ID ) ) {

				$page_template = plugin_dir_path( __FILE__ ) . '/partials/templates/membership-templates/mwb-membership-template.php';
			}
		}

		return $page_template;
	}

	/**
	 * Remove add-on payment gateways from checkout page.
	 *
	 * @param object $available_gateways Object of all woocommerce availabe gateways.
	 *
	 * @since 1.0.0
	 */
	public function mwb_membership_hide_payment_gateway( $available_gateways ) {

		if ( is_checkout() ) {

			$supported_gateways = $this->global_class->supported_gateways();

			foreach ( $available_gateways as $gateway ) {

				if ( in_array( $gateway->id, $supported_gateways, true ) ) {

					unset( $available_gateways[ $gateway->id ] );
				}
			}
		}

		return $available_gateways;
	}

	/**
	 * Ajax callback for getting states.
	 */
	public function membership_get_states() {

		// Nonce verify.
		check_ajax_referer( 'members-nonce', 'nonce' );

		$country_code = ! empty( $_POST['country'] ) ? sanitize_text_field( wp_unslash( $_POST['country'] ) ) : '';

		$country_class = new WC_Countries();
		$states        = $country_class->__get( 'states' );
		$states        = ! empty( $states[ $country_code ] ) ? $states[ $country_code ] : array();

		$result = '';

		if ( ! empty( $states ) && is_array( $states ) ) {

			foreach ( $states as $state_code => $name ) {

				$result .= '<option value="' . $state_code . '">' . $name . '</option>';
			}

			echo $result;
		}

		wp_die();
	}

	/**
	 * Get all valid screens to add scripts and templates.
	 *
	 * @param array $valid_screens An array of valid screens for onboarding form.
	 * @return array
	 * @since    1.0.0
	 */
	public function add_mwb_frontend_screens( $valid_screens = array() ) {

		if ( is_array( $valid_screens ) ) {

			// Push your screen here.
			array_push( $valid_screens, 'mwb_cpt_membership_page_mwb-membership-for-woo-global-settings' );
			array_push( $valid_screens, 'mwb_cpt_membership_page_mwb_membership-for-woo-gateways' );
			array_push( $valid_screens, 'mwb_cpt_membership_page_mwb-membership-for-woo-shortcodes' );
			array_push( $valid_screens, 'mwb_cpt_membership_page_mwb-membership-for-woo-overview' );
		}

		return $valid_screens;
	}

	/**
	 * Get all valid slugs to add deactivate popup.
	 *
	 * @param array $valid_screens An array of valid screens for onboarding form.
	 * @return array
	 * @since    1.0.0
	 */
	public function add_mwb_deactivation_screens( $valid_screens = array() ) {

		if ( is_array( $valid_screens ) ) {

			// Push your screen here.
			array_push( $valid_screens, 'membership-for-woocommerce' );
		}

		return $valid_screens;
	}


}
// End of class.
