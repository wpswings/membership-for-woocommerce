<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @link  https://wpswings.com/
 * @since 1.0.0
 *
 * @package    Membership_For_Woocommerce
 * @subpackage Membership_For_Woocommerce/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to.
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Membership_For_Woocommerce
 * @subpackage Membership_For_Woocommerce/admin
 */
class Membership_For_Woocommerce_Admin {


	/**
	 * The ID of this plugin.
	 *
	 * @since 1.0.0
	 * @var   string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since 1.0.0
	 * @var   string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * WPS Membership Plans field.
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
	 * @since 1.0.0
	 * @param string $plugin_name The name of this plugin.
	 * @param string $version     The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version     = $version;
		$this->global_class = Membership_For_Woocommerce_Global_Functions::get();
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since 1.0.0
	 * @param string $hook The plugin page slug.
	 */
	public function mfw_admin_enqueue_styles( $hook ) {
		$screen = get_current_screen();
		if ( isset( $screen->id ) && 'wp-swings_page_home' === $screen->id || 'wp-swings_page_membership_for_woocommerce_menu' === $screen->id ) {

			// multistep form css.
			if ( ! wps_mfw_standard_check_multistep() ) {
				$style_url        = MEMBERSHIP_FOR_WOOCOMMERCE_DIR_URL . 'build/style-index.css';
				wp_enqueue_style(
					'wps-admin-react-styles',
					$style_url,
					array(),
					time(),
					false
				);
				return;
			}
			wp_enqueue_style( $this->plugin_name . 'migrator', plugin_dir_url( __FILE__ ) . 'css/membership-for-woocommerce-migrator-admin.css', array(), $this->version, 'all' );

			wp_enqueue_style( 'wps-mfw-select2-css', MEMBERSHIP_FOR_WOOCOMMERCE_DIR_URL . 'package/lib/select-2/membership-for-woocommerce-select2.css', array(), time(), 'all' );

			wp_enqueue_style( 'wps-mfw-meterial-css', MEMBERSHIP_FOR_WOOCOMMERCE_DIR_URL . 'package/lib/material-design/material-components-web.min.css', array(), time(), 'all' );
			wp_enqueue_style( 'wps-mfw-meterial-css2', MEMBERSHIP_FOR_WOOCOMMERCE_DIR_URL . 'package/lib/material-design/material-components-v5.0-web.min.css', array(), time(), 'all' );
			wp_enqueue_style( 'wps-mfw-meterial-lite', MEMBERSHIP_FOR_WOOCOMMERCE_DIR_URL . 'package/lib/material-design/material-lite.min.css', array(), time(), 'all' );

			wp_enqueue_style( 'wps-mfw-meterial-icons-css', MEMBERSHIP_FOR_WOOCOMMERCE_DIR_URL . 'package/lib/material-design/icon.css', array(), time(), 'all' );

			wp_enqueue_style( $this->plugin_name . '-admin-global', MEMBERSHIP_FOR_WOOCOMMERCE_DIR_URL . 'admin/css/membership-for-woocommerce-admin-global.css', array( 'wps-mfw-meterial-icons-css' ), time(), 'all' );

			wp_enqueue_style( 'wps-admin-min-css', MEMBERSHIP_FOR_WOOCOMMERCE_DIR_URL . 'admin/css/wps-admin.css', array(), $this->version, 'all' );
			wp_enqueue_style( 'wps-datatable-css', MEMBERSHIP_FOR_WOOCOMMERCE_DIR_URL . 'package/lib/datatables/media/css/jquery.dataTables.min.css', array(), $this->version, 'all' );

		}

		if ( isset( $screen->id ) || isset( $screen->post_type ) ) {

			$pagescreen_id   = $screen->id;
			$pagescreen_post = $screen->post_type;

			if ( 'wps_cpt_membership' === $pagescreen_post || 'wps_cpt_membership' === $pagescreen_id || 'wps_cpt_members' == $pagescreen_post ) {

				wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/membership-for-woocommerce-admin.css', array(), time(), 'all' );

				wp_enqueue_style( 'wps_membership_for_woo_select2', plugin_dir_url( __FILE__ ) . 'css/select2.min.css', array(), time(), 'all' );

				wp_enqueue_style( 'wp-jquery-ui-dialog' );

			}

			if ( isset( $_GET['tab'] ) && 'shipping' === $_GET['tab'] ) {

				wp_enqueue_style( 'wps_membership_for_woo_select2', plugin_dir_url( __FILE__ ) . 'css/select2.min.css', array(), $this->version, 'all' );
			}

			if ( 'wps_cpt_members' === $pagescreen_post || 'wps_cpt_members' === $pagescreen_id ) {

				wp_enqueue_style( 'members-admin-css', plugin_dir_url( __FILE__ ) . 'css/membership-for-woo-members-admin.css', array(), $this->version, 'all' );

				wp_enqueue_style( 'wps_membership_for_woo_select2', plugin_dir_url( __FILE__ ) . 'css/select2.min.css', array(), $this->version, 'all' );

			}
		}

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since 1.0.0
	 * @param string $hook The plugin page slug.
	 */
	public function mfw_admin_enqueue_scripts( $hook ) {

		$screen = get_current_screen();
		if ( isset( $screen->id ) && 'wp-swings_page_home' === $screen->id || 'wp-swings_page_membership_for_woocommerce_menu' === $screen->id ) {

			if ( ! wps_mfw_standard_check_multistep() ) {
				// js for the multistep from.
				$script_path      = '../../build/index.js';
				$script_asset_path = MEMBERSHIP_FOR_WOOCOMMERCE_DIR_PATH . 'build/index.asset.php';
				$script_asset      = file_exists( $script_asset_path )
					? require $script_asset_path
					: array(
						'dependencies' => array(
							'wp-hooks',
							'wp-element',
							'wp-i18n',
							'wc-components',
						),
						'version'      => filemtime( $script_path ),
					);
				$script_url        = MEMBERSHIP_FOR_WOOCOMMERCE_DIR_URL . 'build/index.js';
				wp_register_script(
					'react-app-block',
					$script_url,
					$script_asset['dependencies'],
					$script_asset['version'],
					true
				);
				wp_enqueue_script( 'react-app-block' );
				wp_localize_script(
					'react-app-block',
					'frontend_ajax_object',
					array(
						'ajaxurl'            => admin_url( 'admin-ajax.php' ),
						'wps_standard_nonce' => wp_create_nonce( 'ajax-nonce' ),
						'redirect_url' => admin_url( 'admin.php?page=membership_for_woocommerce_menu' ),
						'products_list' => $this->get_products_for_multistep(),
						'is_pro_plugin' => $this->check_licence_of_pro_for_multistep(),
					)
				);
			}

			wp_enqueue_script( 'wps-mfw-select2', MEMBERSHIP_FOR_WOOCOMMERCE_DIR_URL . 'package/lib/select-2/membership-for-woocommerce-select2.js', array( 'jquery' ), time(), false );

			wp_enqueue_script( 'wps-mfw-metarial-js', MEMBERSHIP_FOR_WOOCOMMERCE_DIR_URL . 'package/lib/material-design/material-components-web.min.js', array(), time(), false );
			wp_enqueue_script( 'wps-mfw-metarial-js2', MEMBERSHIP_FOR_WOOCOMMERCE_DIR_URL . 'package/lib/material-design/material-components-v5.0-web.min.js', array(), time(), false );
			wp_enqueue_script( 'wps-mfw-metarial-lite', MEMBERSHIP_FOR_WOOCOMMERCE_DIR_URL . 'package/lib/material-design/material-lite.min.js', array(), time(), false );
			wp_enqueue_script( 'wps-mfw-datatable', MEMBERSHIP_FOR_WOOCOMMERCE_DIR_URL . 'package/lib/datatables.net/js/jquery.dataTables.min.js', array(), time(), false );
			wp_enqueue_script( 'wps-mfw-datatable-btn', MEMBERSHIP_FOR_WOOCOMMERCE_DIR_URL . 'package/lib/datatables.net/buttons/dataTables.buttons.min.js', array(), time(), false );
			wp_enqueue_script( 'wps-mfw-datatable-btn-2', MEMBERSHIP_FOR_WOOCOMMERCE_DIR_URL . 'package/lib/datatables.net/buttons/buttons.html5.min.js', array(), time(), false );
			wp_register_script( $this->plugin_name . 'admin-js', MEMBERSHIP_FOR_WOOCOMMERCE_DIR_URL . 'admin/js/membership-for-woocommerce-admin.js', array( 'jquery', 'wps-mfw-select2', 'wps-mfw-metarial-js', 'wps-mfw-metarial-js2', 'wps-mfw-metarial-lite' ), time(), false );
			wp_localize_script(
				$this->plugin_name . 'admin-js',
				'mfw_admin_param',
				array(
					'ajaxurl' => admin_url( 'admin-ajax.php' ),
					'reloadurl' => admin_url( 'admin.php?page=membership_for_woocommerce_menu' ),
					'mfw_gen_tab_enable' => get_option( 'mfw_radio_switch_demo' ),
					'mfw_admin_param_location' => ( admin_url( 'admin.php' ) . '?page=membership_for_woocommerce_menu&mfw_tab=membership-for-woocommerce-general' ),
				)
			);
			wp_enqueue_script( $this->plugin_name . 'admin-js' );
			wp_enqueue_script( 'wps-admin-min-js', MEMBERSHIP_FOR_WOOCOMMERCE_DIR_URL . 'admin/js/wps-admin.min.js', array(), time(), false );
			wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/membership-for-woocommerce-admin.js', array( 'jquery' ), $this->version, false );

			wp_localize_script(
				$this->plugin_name,
				'admin_ajax_obj',
				array(
					'ajaxurl' => admin_url( 'admin-ajax.php' ),
					'nonce'   => wp_create_nonce( 'plan-import-nonce' ),
					'Plan'  => __( 'Plan ', 'membership-for-woocommerce' ),
					'Plan_warning'  => __( 'Title field can\'t be empty ', 'membership-for-woocommerce' ),

				)
			);

			wp_register_script( 'membership-for-woocommerce-registration-js', plugin_dir_url( __FILE__ ) . 'js/membership-for-woocommerce-registration.js', array( 'jquery' ), time(), false );
			wp_localize_script(
				$this->plugin_name,
				'admin_registration_ajax_obj',
				array(
					'ajaxurl' => admin_url( 'admin-ajax.php' ),
					'nonce'   => wp_create_nonce( 'membership-registration-nonce' ),

				)
			);

			wp_enqueue_script( 'membership-for-woocommerce-registration-js', plugin_dir_url( __FILE__ ) . 'js/membership-for-woocommerce-registration.js', array( 'jquery' ), time(), false );
		}

		if ( isset( $screen->id ) || isset( $screen->post_type ) ) {

			$pagescreen_post = $screen->post_type;
			$pagescreen_id   = $screen->id;

			if ( 'wps_cpt_membership' === $pagescreen_post || 'wps_cpt_membership' === $pagescreen_id || 'wp-swings_page_membership_for_woocommerce_menu' === $screen->id ) {

				wp_register_script( 'woocommerce_admin', WC()->plugin_url() . '/assets/js/admin/woocommerce_admin.js', array( 'jquery', 'jquery-blockui', 'jquery-ui-sortable', 'jquery-ui-widget', 'jquery-ui-core', 'jquery-tiptip', 'wc-enhanced-select' ), MEMBERSHIP_FOR_WOOCOMMERCE_VERSION, false );
				wp_register_script( $this->plugin_name . 'common', MEMBERSHIP_FOR_WOOCOMMERCE_DIR_URL . 'common/js/membership-for-woocommerce-common.js', array( 'jquery' ), $this->version, false );
				wp_localize_script( $this->plugin_name . 'common', 'mfw_common_param', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );

				wp_enqueue_script( $this->plugin_name . 'common' );

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

				wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/membership-for-woocommerce-admin.js', array( 'jquery' ), time(), false );

				wp_localize_script(
					$this->plugin_name,
					'admin_ajax_obj',
					array(
						'ajaxurl' => admin_url( 'admin-ajax.php' ),
						'nonce'   => wp_create_nonce( 'plan-import-nonce' ),
						'Plan'  => __( 'Plan ', 'membership-for-woocommerce' ),
						'Plan_warning'  => __( 'Title field can\'t be empty ', 'membership-for-woocommerce' ),

					)
				);

				wp_enqueue_script( 'wps_membership_for_woo_add_new_plan_script', plugin_dir_url( __FILE__ ) . 'js/wps_membership_for_woo_add_new_plan_script.js', array( 'woocommerce_admin', 'wc-enhanced-select' ), $this->version, false );

				wp_localize_script(
					'wps_membership_for_woo_add_new_plan_script',
					'add_new_obj',
					array(
						'ajax_url' => admin_url( 'admin-ajax.php' ),
					)
				);

				wp_enqueue_script( 'wp-color-picker' );

				add_thickbox();

				wp_enqueue_media();

				wp_enqueue_script( 'jquery-ui-dialog' );

				wp_enqueue_script( 'wps_mmebership_sweet_alert', plugin_dir_url( __FILE__ ) . 'js/sweet-alert2.js', array( 'jquery' ), $this->version, false );

			}

			if ( 'wps_cpt_members' === $pagescreen_post || 'wps_cpt_members' === $pagescreen_id ) {

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
		wp_enqueue_script( $this->plugin_name . 'migrator', plugin_dir_url( __FILE__ ) . 'js/membership-for-woocommerce-migrator-admin.js', array( 'jquery' ), $this->version, false );
		wp_enqueue_script( $this->plugin_name . 'cdn', plugin_dir_url( __FILE__ ) . 'js/membership-for-woocommerce-cdn-admin.js', array( 'jquery' ), $this->version, false );
		wp_enqueue_script( $this->plugin_name . 'swall', plugin_dir_url( __FILE__ ) . 'js/membership-for-woocommerce-swall-admin.js', array( 'jquery' ), $this->version, false );
		wp_localize_script(
			$this->plugin_name . 'migrator',
			'localised',
			array(
				'ajaxurl'          => admin_url( 'admin-ajax.php' ),
				'nonce'            => wp_create_nonce( 'wps_membership_nonce' ),
				'callback'         => 'wps_membership_ajax_callbacks',
				'pending_count'    => $this->wps_membership_get_count( 'pending', 'count' ),
				'pending_products'   => $this->wps_membership_get_count( 'pending', 'products' ),

			)
		);

		wp_enqueue_script( 'membership-for-woocommerce-product-edit-admin', plugin_dir_url( __FILE__ ) . 'js/membership-for-woocommerce-product-edit-admin.js', array( 'jquery' ), $this->version, false );
		wp_localize_script(
			'membership-for-woocommerce-product-edit-admin',
			'wps_product_edit_param',
			array(
				'ajaxurl'          => admin_url( 'admin-ajax.php' ),
				'nonce'            => wp_create_nonce( 'wps_membership_nonce' ),
				'prod_id'          => get_option( 'wps_membership_default_product' ),
			)
		);

	}

	/**
	 * Check licence of pro for multistep.
	 *
	 * @return string
	 */
	public function check_licence_of_pro_for_multistep() {
		$return = 'false';
		if ( function_exists( 'check_membership_pro_plugin_is_active' ) ) {
			$check_licence = check_membership_pro_plugin_is_active();
			if ( $check_licence ) {
				$return = 'true';

			}
		}
		return $return;
	}

	/**
	 * Load products for multistep.
	 *
	 * @return array
	 */
	public function get_products_for_multistep() {
		$products_array = array();
		$args           = array(
			'post_type'      => 'product',
			'posts_per_page' => 10,
			'post_status' => 'publish',
		);

		$loop = new WP_Query( $args );
		while ( $loop->have_posts() ) :
			$loop->the_post();
			global $product;
			$products_array [ $product->get_id() ] = $product->get_name();
		endwhile;
		wp_reset_query();
		return $products_array;
	}

	/**
	 * Adding settings menu for Membership For WooCommerce.
	 *
	 * @since 1.0.0
	 */
	public function mfw_options_page() {
		global $submenu;

		if ( empty( $GLOBALS['admin_page_hooks']['wps-plugins'] ) ) {
			add_menu_page( 'WP Swings', 'WP Swings', 'manage_options', 'wps-plugins', array( $this, 'wps_plugins_listing_page' ), MEMBERSHIP_FOR_WOOCOMMERCE_DIR_URL . 'admin/image/wpswings_logo.png', 15 );

			if ( wps_mfw_standard_check_multistep() ) {
				add_submenu_page( 'wps-plugins', 'Home', 'Home', 'manage_options', 'home', array( $this, 'wpswings_welcome_callback_function' ), 1 );
			}
			$mfw_menus =

			/**
			 * Filter to add plugin menu.
			 *
			 * @since 1.0.0
			 */
			apply_filters( 'wps_add_plugins_menus_array', array() );
			if ( is_array( $mfw_menus ) && ! empty( $mfw_menus ) ) {
				foreach ( $mfw_menus as $mfw_key => $mfw_value ) {
					add_submenu_page( 'wps-plugins', $mfw_value['name'], $mfw_value['name'], 'manage_options', $mfw_value['menu_link'], array( $mfw_value['instance'], $mfw_value['function'] ) );
				}
			}
		} else {
			if ( ! empty( $submenu['wps-plugins'] ) ) {

				if ( ! in_array( 'Home', (array) $submenu['wps-plugins'] ) ) {
					if ( wps_mfw_standard_check_multistep() ) {
						add_submenu_page( 'wps-plugins', 'Home', 'Home', 'manage_options', 'home', array( $this, 'wpswings_welcome_callback_function' ), 1 );
					}
				}
			}
		}

	}

	/**
	 *
	 * Adding the default menu into the WordPress menu.
	 *
	 * @name wpswings_callback_function
	 * @since 1.0.0
	 */
	public function wpswings_welcome_callback_function() {
		include MEMBERSHIP_FOR_WOOCOMMERCE_DIR_PATH . 'admin/partials/membership-for-woocommerce-welcome.php';
	}



	/**
	 * Removing default submenu of parent menu in backend dashboard
	 *
	 * @since 1.0.0
	 */
	public function wps_mfw_remove_default_submenu() {
		global $submenu;
		if ( is_array( $submenu ) && array_key_exists( 'wps-plugins', $submenu ) ) {
			if ( isset( $submenu['wps-plugins'][0] ) ) {
				unset( $submenu['wps-plugins'][0] );
			}
		}
	}


	/**
	 * Membership For WooCommerce mfw_admin_submenu_page.
	 *
	 * @since 1.0.0
	 * @param array $menus Marketplace menus.
	 */
	public function mfw_admin_submenu_page( $menus = array() ) {

		$menus[] = array(
			'name'            => __( 'Membership For WooCommerce', 'membership-for-woocommerce' ),
			'slug'            => 'membership_for_woocommerce_menu',
			'menu_link'       => 'membership_for_woocommerce_menu',
			'instance'        => $this,
			'function'        => 'mfw_options_menu_html',
		);
		return $menus;
	}

	/**
	 * Membership For WooCommerce wps_plugins_listing_page.
	 *
	 * @since 1.0.0
	 */
	public function wps_plugins_listing_page() {
		$active_marketplaces =

		/**
		 * Filter to add menus.
		 *
		 * @since 1.0.0
		 */
		apply_filters( 'wps_add_plugins_menus_array', array() );
		if ( is_array( $active_marketplaces ) && ! empty( $active_marketplaces ) ) {
			include MEMBERSHIP_FOR_WOOCOMMERCE_DIR_PATH . 'admin/partials/welcome.php';
		}
	}

	/**
	 * Membership For WooCommerce admin menu page.
	 *
	 * @since 1.0.0
	 */
	public function mfw_options_menu_html() {

		include_once MEMBERSHIP_FOR_WOOCOMMERCE_DIR_PATH . 'admin/partials/membership-for-woocommerce-admin-dashboard.php';
	}

	/**
	 * Mwb_developer_admin_hooks_listing.
	 *
	 * @return array
	 */
	public function wps_developer_admin_hooks_listing() {
		$admin_hooks = array();
		$val = self::wps_developer_hooks_function( MEMBERSHIP_FOR_WOOCOMMERCE_DIR_PATH . 'admin/' );
		if ( ! empty( $val['hooks'] ) ) {
			$admin_hooks[] = $val['hooks'];
			unset( $val['hooks'] );
		}
		$data = array();
		foreach ( $val['files'] as $v ) {
			if ( 'css' !== $v && 'js' !== $v && 'images' !== $v ) {
				$helo = self::wps_developer_hooks_function( MEMBERSHIP_FOR_WOOCOMMERCE_DIR_PATH . 'admin/' . $v . '/' );
				if ( ! empty( $helo['hooks'] ) ) {
					$admin_hooks[] = $helo['hooks'];
					unset( $helo['hooks'] );
				}
				if ( ! empty( $helo ) ) {
					$data[] = $helo;
				}
			}
		}
		return $admin_hooks;
	}

	/**
	 * Mwb_developer_public_hooks_listing.
	 */
	public function wps_developer_public_hooks_listing() {

		$public_hooks = array();
		$val = self::wps_developer_hooks_function( MEMBERSHIP_FOR_WOOCOMMERCE_DIR_PATH . 'public/' );

		if ( ! empty( $val['hooks'] ) ) {
			$public_hooks[] = $val['hooks'];
			unset( $val['hooks'] );
		}
		$data = array();
		foreach ( $val['files'] as $v ) {
			if ( 'css' !== $v && 'js' !== $v && 'images' !== $v ) {
				$helo = self::wps_developer_hooks_function( MEMBERSHIP_FOR_WOOCOMMERCE_DIR_PATH . 'public/' . $v . '/' );
				if ( ! empty( $helo['hooks'] ) ) {
					$public_hooks[] = $helo['hooks'];
					unset( $helo['hooks'] );
				}
				if ( ! empty( $helo ) ) {
					$data[] = $helo;
				}
			}
		}
		return $public_hooks;
	}
	/**
	 * Mwb_developer_hooks_function.
	 *
	 * @param mixed $path is the path of file.
	 */
	public function wps_developer_hooks_function( $path ) {
		$all_hooks = array();
		$scan = scandir( $path );
		$response = array();
		foreach ( $scan as $file ) {
			if ( strpos( $file, '.php' ) ) {
				$myfile = file( $path . $file );
				foreach ( $myfile as $key => $lines ) {
					if ( preg_match( '/do_action/i', $lines ) && ! strpos( $lines, 'str_replace' ) && ! strpos( $lines, 'preg_match' ) ) {
						$all_hooks[ $key ]['action_hook'] = $lines;
						$all_hooks[ $key ]['desc'] = $myfile[ $key - 1 ];
					}
					if ( preg_match( '/apply_filters/i', $lines ) && ! strpos( $lines, 'str_replace' ) && ! strpos( $lines, 'preg_match' ) ) {
						$all_hooks[ $key ]['filter_hook'] = $lines;
						$all_hooks[ $key ]['desc'] = $myfile[ $key - 1 ];
					}
				}
			} elseif ( strpos( $file, '.' ) == '' && strpos( $file, '.' ) !== 0 ) {
				$response['files'][] = $file;
			}
		}
		if ( ! empty( $all_hooks ) ) {
			$response['hooks'] = $all_hooks;
		}
		return $response;
	}

	/**
	 * Membership For WooCommerce admin menu page.
	 *
	 * @since 1.0.0
	 * @param array $mfw_settings_general Settings fields.
	 */
	public function mfw_admin_general_settings_page( $mfw_settings_general ) {

		$instance = $this->global_class;

		$wps_membership_global_settings = ! empty( get_option( 'wps_membership_global_options' ) ) ? get_option( 'wps_membership_global_options' ) : $instance->default_global_options();
		if ( ! empty( $wps_membership_global_settings ) ) {
			$wps_membership_global_settings = array();
		}
		$wps_membership_global_settings['wps_membership_enable_plugin'] = ! empty( get_option( 'wps_membership_enable_plugin' ) ) ? get_option( 'wps_membership_enable_plugin' ) : '';
		$wps_membership_global_settings['wps_membership_delete_data'] = ! empty( get_option( 'wps_membership_delete_data' ) ) ? get_option( 'wps_membership_delete_data' ) : '';
		$wps_membership_global_settings['wps_membership_plan_user_history'] = get_option( 'wps_membership_plan_user_history' );
		! empty( get_option( 'wps_membership_delete_data' ) ) ? get_option( 'wps_membership_delete_data' ) : '';
		$wps_membership_global_settings['wps_membership_email_subject'] = ! empty( get_option( 'wps_membership_email_subject' ) ) ? get_option( 'wps_membership_email_subject' ) : '';
		$wps_membership_global_settings['wps_membership_email_content'] = ! empty( get_option( 'wps_membership_email_content' ) ) ? get_option( 'wps_membership_email_content' ) : '';
		$wps_membership_global_settings['wps_membership_for_woo_delete_data'] = ! empty( get_option( 'wps_membership_for_woo_delete_data' ) ) ? get_option( 'wps_membership_for_woo_delete_data' ) : '';

		$wps_membership_global_settings['wps_membership_attach_invoice'] = ! empty( get_option( 'wps_membership_attach_invoice' ) ) ? get_option( 'wps_membership_attach_invoice' ) : '';
		$wps_membership_global_settings['wps_membership_invoice_address'] = ! empty( get_option( 'wps_membership_invoice_address' ) ) ? get_option( 'wps_membership_invoice_address' ) : '';
		$wps_membership_global_settings['wps_membership_invoice_phone'] = ! empty( get_option( 'wps_membership_invoice_phone' ) ) ? get_option( 'wps_membership_invoice_phone' ) : '';
		$wps_membership_global_settings['wps_membership_invoice_email'] = ! empty( get_option( 'wps_membership_invoice_email' ) ) ? get_option( 'wps_membership_invoice_email' ) : '';
		$wps_membership_global_settings['wps_membership_invoice_logo'] = ! empty( get_option( 'wps_membership_invoice_logo' ) ) ? get_option( 'wps_membership_invoice_logo' ) : '';

		/**
		 * Filter for global setting.
		 *
		 * @since 1.0.0
		 */
		$wps_membership_global_settings = apply_filters( 'mfw_wps_membership_global_settings', $wps_membership_global_settings );
		update_option( 'wps_membership_global_options', $wps_membership_global_settings );

		$wps_membership_attach_invoice = ! empty( $wps_membership_global_settings['wps_membership_attach_invoice'] ) ? $wps_membership_global_settings['wps_membership_attach_invoice'] : '';
		$wps_membership_attach_invoice_data = '';

		$mfw_settings_general_before = array(
			array(
				'title' => __( 'Enable Membership Plans', 'membership-for-woocommerce' ),
				'type'  => 'radio-switch',
				'description'  => __( 'Enable plugin to start the functionality.', 'membership-for-woocommerce' ),
				'id'    => 'wps_membership_enable_plugin',
				'value' => get_option( 'wps_membership_enable_plugin' ),
				'class' => 'mfw-radio-switch-class',
				'options' => array(
					'yes' => __( 'YES', 'membership-for-woocommerce' ),
					'no' => __( 'NO', 'membership-for-woocommerce' ),
				),
			),

			array(
				'title' => __( 'Delete data at Uninstall', 'membership-for-woocommerce' ),
				'type'  => 'radio-switch',
				'description'  => __( 'If enabled, this will delete all data at plugin uninstall.', 'membership-for-woocommerce' ),
				'id'    => 'wps_membership_for_woo_delete_data',
				'value' => get_option( 'wps_membership_for_woo_delete_data' ),
				'class' => 'mfw-radio-switch-class',
				'options' => array(
					'yes' => __( 'YES', 'membership-for-woocommerce' ),
					'no' => __( 'NO', 'membership-for-woocommerce' ),
				),
			),
			array(
				'title' => __( 'Show History to User', 'membership-for-woocommerce' ),
				'type'  => 'radio-switch',
				'description'  => __( 'This will Enable Users to visit and see Plans History in Membership tab  on My Account page.', 'membership-for-woocommerce' ),
				'id'    => 'wps_membership_plan_user_history',
				'value' => get_option( 'wps_membership_plan_user_history' ),
				'class' => 'mfw-radio-switch-class',
				'options' => array(
					'yes' => __( 'YES', 'membership-for-woocommerce' ),
					'no' => __( 'NO', 'membership-for-woocommerce' ),
				),
			),
			array(
				'title' => __( 'Create User after payment done', 'membership-for-woocommerce' ),
				'type'  => 'radio-switch',
				'description'  => __( 'Enable this to create user when payment is done.', 'membership-for-woocommerce' ),
				'id'    => 'wps_membership_create_user_after_payment',
				'value' => get_option( 'wps_membership_create_user_after_payment' ),
				'class' => 'mfw-radio-switch-class',
				'options' => array(
					'yes' => __( 'YES', 'membership-for-woocommerce' ),
					'no' => __( 'NO', 'membership-for-woocommerce' ),
				),
			),
			array(
				'title' => __( 'Allow your members to Cancel their membership accounts.', 'membership-for-woocommerce' ),
				'type'  => 'radio-switch',
				'description'  => __( 'Enable this Allow your members to Cancel their membership accounts.', 'membership-for-woocommerce' ),
				'id'    => 'wps_membership_allow_cancel_membership',
				'value' => get_option( 'wps_membership_allow_cancel_membership' ),
				'class' => 'mfw-radio-switch-class',
				'options' => array(
					'yes' => __( 'YES', 'membership-for-woocommerce' ),
					'no' => __( 'NO', 'membership-for-woocommerce' ),
				),
			),
			array(
				'title' => __( 'Create member on Processing order status.', 'membership-for-woocommerce' ),
				'type'  => 'radio-switch',
				'description'  => __( 'Enable this Create member on processing order status.', 'membership-for-woocommerce' ),
				'id'    => 'wps_membership_create_member_on_processing',
				'value' => get_option( 'wps_membership_create_member_on_processing' ),
				'class' => 'mfw-radio-switch-class',
				'options' => array(
					'yes' => __( 'YES', 'membership-for-woocommerce' ),
					'no' => __( 'NO', 'membership-for-woocommerce' ),
				),
			),

			array(
				'title' => __( 'Change Buy Now button text.', 'membership-for-woocommerce' ),
				'type'  => 'text',
				'description'  => __( 'Change the text of Buy Now Button.', 'membership-for-woocommerce' ),
				'placeholder' => __( 'Add Text', 'membership-for-woocommerce' ),
				'id'    => 'wps_membership_change_buy_now_text',
				'value' => get_option( 'wps_membership_change_buy_now_text' ),
				'options' => array(
					'yes' => __( 'YES', 'membership-for-woocommerce' ),
					'no' => __( 'NO', 'membership-for-woocommerce' ),
				),
			),

		);
		$after_email = array();

		/**
		 * Filter to attach invoice data.
		 *
		 * @since 1.0.0
		 */
		$after_email = apply_filters( 'wps_membership_set_attach_invoice_data_after_email', $after_email );
		$mfw_settings_general_before = array_merge( $mfw_settings_general_before, $after_email );

		$mfw_settings_general_button = array(
			array(
				'type'  => 'button',
				'id'    => 'mfw_button_demo',
				'button_text' => __( 'Save', 'membership-for-woocommerce' ),
				'class' => 'mfw-button-class',
			),
		);

		$mfw_settings_general = array_merge( $mfw_settings_general_before, $mfw_settings_general_button );
		return $mfw_settings_general;
	}



	/**
	 * Membership For WooCommerce save tab settings.
	 *
	 * @since 1.0.0
	 */
	public function mfw_admin_save_tab_settings() {

		global $mfw_wps_mfw_obj;
		if ( isset( $_POST['mfw_button_demo'] )
			&& ( ! empty( $_POST['wps_tabs_nonce'] )
			&& wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['wps_tabs_nonce'] ) ), 'admin_save_data' ) )
		) {

			$screen = get_current_screen();
			if ( isset( $screen->id ) && 'wp-swings_page_home' === $screen->id ) {
				$enable_tracking = ! empty( $_POST['mfw_enable_tracking'] ) ? sanitize_text_field( wp_unslash( $_POST['mfw_enable_tracking'] ) ) : '';
				update_option( 'mfw_enable_tracking', $enable_tracking );
				return;
			}

			$wps_mfw_gen_flag     = false;
			$mfw_genaral_settings =

			/**
			 * Filter for general setting.
			 *
			 * @since 1.0.0
			 */
			apply_filters( 'mfw_general_settings_array', array() );
			$mfw_button_index     = array_search( 'submit', array_column( $mfw_genaral_settings, 'type' ) );
			if ( isset( $mfw_button_index ) && ( null == $mfw_button_index || '' == $mfw_button_index ) ) {
				$mfw_button_index = array_search( 'button', array_column( $mfw_genaral_settings, 'type' ) );
			}
			if ( isset( $mfw_button_index ) && '' !== $mfw_button_index ) {
				unset( $mfw_genaral_settings[ $mfw_button_index ] );
				if ( is_array( $mfw_genaral_settings ) && ! empty( $mfw_genaral_settings ) ) {
					foreach ( $mfw_genaral_settings as $mfw_genaral_setting ) {
						if ( isset( $mfw_genaral_setting['id'] ) && '' !== $mfw_genaral_setting['id'] ) {
							if ( isset( $_POST[ $mfw_genaral_setting['id'] ] ) ) {
								update_option( $mfw_genaral_setting['id'], is_array( $_POST[ $mfw_genaral_setting['id'] ] ) ? map_deep( wp_unslash( $_POST[ $mfw_genaral_setting['id'] ] ), 'sanitize_text_field' ) : sanitize_text_field( wp_unslash( $_POST[ $mfw_genaral_setting['id'] ] ) ) );
							} else {
								update_option( $mfw_genaral_setting['id'], '' );
							}
						} else {
							$wps_mfw_gen_flag = true;
						}
					}
				}
				if ( $wps_mfw_gen_flag ) {
					$wps_mfw_error_text = esc_html__( 'Id of some field is missing', 'membership-for-woocommerce' );
					$mfw_wps_mfw_obj->wps_mfw_plug_admin_notice( $wps_mfw_error_text, 'error' );
				} else {
					$wps_mfw_error_text = esc_html__( 'Settings saved !', 'membership-for-woocommerce' );
					$mfw_wps_mfw_obj->wps_mfw_plug_admin_notice( $wps_mfw_error_text, 'success' );
				}
			}
		}
	}

	/**
	 * Setting for add plan sub tab.
	 *
	 * @param array $mfw_add_plans_settings_array contains array.
	 * @return array
	 */
	public function wps_mfw_add_plans_settings_array( $mfw_add_plans_settings_array ) {
		$mfw_add_plans_settings_array = array(
			array(
				'title' => __( 'Enter Plan name', 'membership-for-woocommerce' ),
				'type'  => 'text',
				'description'  => __( 'Enter the name for the membership plans you are creating.', 'membership-for-woocommerce' ),
				'id'    => 'wps_mfw_reg_plan_name',
				'placeholder' => 'Enter Plan Name',
			),

			array(
				'title' => __( 'Enter plan price', 'membership-for-woocommerce' ),
				'type'  => 'number',
				'description'  => __( 'Enter the price for the membership plans you are creating.', 'membership-for-woocommerce' ),
				'id'    => 'wps_mfw_reg_plan_price',
				'placeholder' => 'Enter Plan Price',
			),
			array(
				'title' => __( 'Set Access Type( Expiry of plan )', 'membership-for-woocommerce' ),
				'type'  => 'access_type',
				'description'  => __( 'Set the expiry of the membership plan.', 'membership-for-woocommerce' ),
				'id'    => 'wps_mfw_access_type',
				'placeholder' => 'Enter Plan Price',
			),

			array(
				'type'  => 'button',
				'id'    => 'wps_create_membership_plan_button',
				'button_text' => __( 'Create plan', 'membership-for-woocommerce' ),
				'class' => 'mfw-button-class',
			),
		);

		return $mfw_add_plans_settings_array;
	}

	/**
	 * Sanitation for an array
	 *
	 * @param mixed $wps_input_array for array value.
	 * @return array
	 */
	public function wps_sanitize_array( $wps_input_array ) {
		foreach ( $wps_input_array as $wps_input_array_key => $wps_input_array_value ) {
			$wps_input_array_key   = sanitize_text_field( $wps_input_array_key );
			$wps_input_array_value = sanitize_text_field( $wps_input_array_value );
		}
		return $wps_input_array;
	}

	/**
	 * Custom post type to display the list of all members.
	 *
	 * @since 1.0.0
	 */
	public function wps_membership_for_woo_cpt_members() {

		$labels = array(
			'name'               => esc_html__( 'Members', 'membership-for-woocommerce' ),
			'singular_name'      => esc_html__( 'Member', 'membership-for-woocommerce' ),
			'all_items'          => esc_html__( 'All Members', 'membership-for-woocommerce' ),
			'edit_item'          => esc_html__( 'Edit Member', 'membership-for-woocommerce' ),
			'new_item'           => esc_html__( 'New Member', 'membership-for-woocommerce' ),
			'view_item'          => esc_html__( 'View Member', 'membership-for-woocommerce' ),
			'search_item'        => esc_html__( 'Search Member', 'membership-for-woocommerce' ),
			'not_found'          => esc_html__( 'No Members Found', 'membership-for-woocommerce' ),
			'not_found_in_trash' => esc_html__( 'No Members Found In Trash', 'membership-for-woocommerce' ),
		);

		register_post_type(
			'wps_cpt_members',
			array(
				'labels'               => $labels,
				'public'               => true,
				'has_archive'          => false,
				'publicly_queryable'   => true,
				'query_var'            => true,
				'capability_type'      => 'post',
				'hierarchical'         => false,
				'show_in_admin_bar'    => false,
				'show_in_menu'         => 'edit.php?post_type=wps_cpt_membership',
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

		remove_post_type_support( 'wps_cpt_members', 'title' );
		remove_post_type_support( 'wps_cpt_members', 'editor' );
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

		if ( 'wps_cpt_members' == $post->post_type ) {
			unset( $actions['trash'] );
			unset( $actions['view'] );
			unset( $actions['inline hide-if-no-js'] );
		}
		if ( 'wps_cpt_membership' == $post->post_type ) {

			unset( $actions['view'] );

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
		add_meta_box( 'members_meta_box', esc_html__( 'Membership Details', 'membership-for-woocommerce' ), array( $this, 'wps_members_metabox_callback' ), 'wps_cpt_members' );

		// Add billing details metabox.
		add_meta_box( 'members_metabox_billing', esc_html__( 'Billing details', 'membership-for-woocommerce' ), array( $this, 'wps_members_metabox_billing' ), 'wps_cpt_members', 'normal', 'high' );

		// Remove sumitdiv metabox for wps_cpt_members.
		remove_meta_box( 'submitdiv', 'wps_cpt_members', 'side' );

		// Add custom member actions metabox.
		add_meta_box( '_submitdiv', esc_html__( 'Member actions', 'membership-for-woocommerce' ), array( $this, 'member_actions_callback' ), 'wps_cpt_members', 'side', 'core' );

	}

	/**
	 * Members billing metabox callback.
	 *
	 * @param object $post is the post object.
	 * @since 1.0.0
	 */
	public function wps_members_metabox_billing( $post ) {

		$member         = $post;
		$member_details = get_post_meta( $post->ID, 'billing_details', true );
		$instance       = $this->global_class;

		wc_get_template(
			'admin/partials/templates/members-templates/wps-members-plans-billing.php',
			array(
				'member_details' => $member_details,
				'instance'       => $instance,
				'post'           => $member,
			),
			'',
			MEMBERSHIP_FOR_WOOCOMMERCE_DIR_PATH
		);

	}

	/**
	 * Ajax callback for getting states.
	 */
	public function wps_membership_get_states() {

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
			//phpcs:disable
			echo esc_attr( $result ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			//phpcs:enable
		}

		wp_die();
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
			'admin/partials/templates/members-templates/wps-members-actions.php',
			array(
				'post'    => $member,
				'actions' => $actions,
				'status'  => $status,
			),
			'',
			MEMBERSHIP_FOR_WOOCOMMERCE_DIR_PATH
		);
	}




	/**
	 * Members metabox callback.
	 *
	 * @param object $post is the post object.
	 * @since 1.0.0
	 */
	public function wps_members_metabox_callback( $post ) {

		// Add a single nonce field to post.
		wp_nonce_field( 'wps_members_creation_nonce', 'wps_members_nonce_field' );

		$plan     = get_post_meta( $post->ID, 'plan_obj', true );
		$instance = $this->global_class;

		wc_get_template(
			'admin/partials/templates/members-templates/wps-members-plans-details.php',
			array(
				'plan'     => $plan,
				'instance' => $instance,
			),
			'',
			MEMBERSHIP_FOR_WOOCOMMERCE_DIR_PATH
		);
	}

	/**
	 * Custom post type for membership plans creation and settings.
	 *
	 * @since 1.0.0
	 */
	public function wps_membership_for_woo_cpt_membership() {

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
			'wps_cpt_membership',
			array(
				'labels'               => $labels,
				'public'               => true,
				'has_archive'          => false,
				'show_ui'              => true,
				'publicly_queryable'   => true,
				'query_var'            => true,
				'capability_type'      => 'post',
				'hierarchical'         => false,
				'show_in_admin_bar'    => false,
				'show_in_menu'         => true,
				'menu_position'        => null,
				'menu_icon'            => 'dashicons-buddicons-buddypress-logo',
				'description'          => esc_html__( 'Membership Plans will be created here.', 'membership-for-woocommerce' ),
				'register_meta_box_cb' => array( $this, 'wps_membership_for_woo_meta_box' ),
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
	 * Register Custom Meta box for Membership plans creation.
	 *
	 * @since 1.0.0
	 */
	public function wps_membership_for_woo_meta_box() {

		add_meta_box( 'members_meta_box', esc_html__( 'Create Plan     ', 'membership-for-woocommerce' ), array( $this, 'wps_membership_meta_box_callback' ), 'wps_cpt_membership' );
	}


	/**
	 * Callback funtion for custom meta boxes.
	 *
	 * @param string $post Current post object.
	 *
	 * @since 1.0.0
	 */
	public function wps_membership_meta_box_callback( $post ) {

		$this->set_plan_creation_fields( get_the_ID() );

		$settings_fields = $this->settings_fields;
		$instance        = $this->global_class;

		wc_get_template(
			'admin/partials/templates/membership-templates/wps-membership-plans-creation.php',
			array(
				'settings_fields' => $settings_fields,
				'instance'        => $instance,
			),
			'',
			MEMBERSHIP_FOR_WOOCOMMERCE_DIR_PATH
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

			foreach ( $this->get_plans_default_value() as $plan_key => $plan_value ) {

				$default = ! empty( $plan_value['default'] ) ? $plan_value['default'] : '';

				$data                          = get_post_meta( $post_id, $plan_key, true );
				$this->settings_fields[ $plan_key ] = ! empty( $data ) ? $data : $default;
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

		$fields = array(
			'wps_membership_plan_price'                  => array( 'default' => '0' ),
			'wps_membership_plan_info'                   => array( 'default' => '' ),
			'wps_membership_plan_name_access_type'       => array( 'default' => 'lifetime' ),
			'wps_membership_plan_duration'               => array( 'default' => '0' ),
			'wps_membership_plan_duration_type'          => array( 'default' => 'days' ),
			'wps_membership_subscription'                => array( 'default' => 'no' ),
			'wps_membership_subscription_expiry'         => array( 'default' => '0' ),
			'wps_membership_subscription_expiry_type'    => array( 'default' => 'days' ),
			'wps_membership_plan_recurring'              => array( 'default' => '' ),
			'wps_membership_plan_access_type'            => array( 'default' => 'immediate_type' ),
			'wps_membership_plan_time_duration'          => array( 'default' => '0' ),
			'wps_membership_plan_time_duration_type'     => array( 'default' => 'days' ),
			'wps_membership_plan_offer_price_type'       => array( 'default' => '%' ),
			'wps_memebership_plan_discount_price'        => array( 'default' => '0' ),
			'wps_memebership_plan_free_shipping'         => array( 'default' => 'no' ),
			'wps_membership_plan_hide_products'          => array( 'default' => 'no' ),
			'wps_membership_show_notice'                 => array( 'default' => 'no' ),
			'wps_membership_notice_message'              => array( 'default' => '' ),
			'wps_membership_plan_target_categories'      => array( 'default' => array() ),
			'wps_membership_plan_target_ids'             => array( 'default' => array() ),
			'wps_membership_plan_post_target_ids'        => array( 'default' => array() ),
			'wps_membership_plan_target_tags'            => array( 'default' => array() ),
			'wps_membership_plan_target_post_tags'       => array( 'default' => array() ),
			'wps_membership_plan_target_post_categories' => array( 'default' => array() ),
			'wps_membership_club'                        => array( 'default' => array() ),
			'wps_membership_plan_page_target_ids'        => array( 'default' => array() ),
			'wps_membership_plan_target_disc_categories' => array( 'default' => array() ),
			'wps_membership_plan_target_disc_tags'       => array( 'default' => array() ),
			'wps_membership_plan_target_disc_ids'        => array( 'default' => array() ),
			'wps_membership_plan_target_disc_ids'        => array( 'default' => array() ),
			'wps_membership_product_offer_price_type'    => array( 'default' => '%' ),
			'wps_memebership_product_discount_price'     => array( 'default' => '0' ),
		);

		/**
		 * Filter to get plan value.
		 *
		 * @since 1.0.0
		 */
		$fields = apply_filters( 'get_plans_default_value', $fields );

		return $fields;
	}

	/**
	 * Remove "Add Plans" submenu from Membership CPT.
	 */
	public function wps_membership_remove_submenu() {

		if ( post_type_exists( 'wps_cpt_membership' ) ) {

			remove_submenu_page( 'edit.php?post_type=wps_cpt_membership', 'post-new.php?post_type=wps_cpt_membership' );

		}

	}

		/**
		 * Add post stats to Membership default offer page.
		 *
		 * @param array  $states An array of post display states.
		 * @param object $post Current post object.
		 *
		 * @since 1.0.0
		 */
	public function wps_membership_default_page_states( $states, $post ) {

		if ( 'membership-plans' === get_post_field( 'post_name', $post->ID ) ) {

			$states[] = esc_html__( 'Membership Default Page', 'membership-for-woocommerce' );
		}

		/**
		 * Filter for default page.
		 *
		 * @since 1.0.0
		 */
		$states = apply_filters( 'wps_membership_default_page_states', $states );

		return $states;

	}

	/**
	 * Adding membership shipping method.
	 *
	 * @param array $methods an array of shipping methods.
	 * @return array
	 *
	 * @since 1.0.0
	 */
	public function wps_membership_for_woo_add_shipping_method( $methods ) {

		$methods['wps_membership_shipping'] = 'WPS_Membership_Free_Shipping_Method';

		/**
		 * Filter to add shipping method.
		 *
		 * @since 1.0.0
		 */
		$methods = apply_filters( 'wps_membership_for_woo_add_shipping_method', $methods );

		return $methods;
	}

	/**
	 * Adding custom column to the custom post type "Membership"
	 *
	 * @param array $columns is an array of deafult columns in custom post type.
	 *
	 * @since 1.0.0
	 */
	public function wps_membership_for_woo_cpt_columns_membership( $columns ) {

		$columns['membership_view']   = '';
		$columns['membership_status'] = esc_html__( 'Membership Plan Status', 'membership-for-woocommerce' );
		$columns['membership_cost']   = esc_html__( 'Membership Plan Cost', 'membership-for-woocommerce' );

		/**
		 * Filter to add membership.
		 *
		 * @since 1.0.0
		 */
		$columns = apply_filters( 'wps_membership_for_woo_cpt_columns_membership', $columns );
		return $columns;
	}

	/**
	 * Adding custom columns to the custom post type "Members".
	 *
	 * @param array $columns is an array of deafult columns in custom post type.
	 *
	 * @since 1.0.0
	 */
	public function wps_membership_for_woo_cpt_columns_members( $columns ) {

		// Adding new columns.
		$columns = array(
			'cb'                         => '<input type="checkbox" />',
			'membership_id'              => esc_html__( 'Membership ID', 'membership-for-woocommerce' ),
			'members_status'             => esc_html__( 'Membership Status', 'membership-for-woocommerce' ),
			'membership_user'            => esc_html__( 'User', 'membership-for-woocommerce' ),
			'membership_plan_associated' => esc_html__( 'Plan Associated', 'membership-for-woocommerce' ),
			'membership_user_view'       => esc_html__( 'Plan Preview', 'membership-for-woocommerce' ),
			'expiration'                 => esc_html__( 'Expiry Date', 'membership-for-woocommerce' ),
		);

		/**
		 * Filter for column members.
		 *
		 * @since 1.0.0
		 */
		$columns = apply_filters( 'wps_membership_for_woo_cpt_columns_members', $columns );

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
	public function wps_membership_for_woo_fill_columns_membership( $column, $post_id ) {

		switch ( $column ) {

			case 'membership_view':
				?>

				<a title="<?php echo esc_html__( 'Membership ID #', 'membership-for-woocommerce' ) . esc_html( $post_id ); ?>" href="admin-ajax.php?action=wps_membership_get_membership_content&post_id=<?php echo esc_html( $post_id ); ?>&nonce=<?php echo esc_html( wp_create_nonce( 'preview-nonce' ) ); ?>" class="thickbox"><img src="<?php echo esc_url( MEMBERSHIP_FOR_WOOCOMMERCE_DIR_URL . 'admin/resources/icons/eye-icon.svg' ); ?>" alt="eye"></span></a>

				<?php
				break;

			case 'membership_status':
				$plan_status = get_post_status( $post_id );

				if ( ! wps_membership_check_plugin_enable() ) {

					echo esc_html__( 'Disabled from settings', 'membership-for-woocommerce' );
					break;
				}
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
				$plan_cost = get_post_meta( $post_id, 'wps_membership_plan_price', true );
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
	public function wps_membership_get_membership_content() {

		// Nonce verification.
		check_ajax_referer( 'preview-nonce', 'nonce' );

		$plan_id  = ! empty( $_GET['post_id'] ) ? sanitize_text_field( wp_unslash( $_GET['post_id'] ) ) : '';
		$instance = $this->global_class;

		wc_get_template(
			'admin/partials/templates/admin-ajax-templates/membership-plan-preview.php',
			array(
				'post_id'  => $plan_id,
				'instance' => $instance,
			),
			'',
			MEMBERSHIP_FOR_WOOCOMMERCE_DIR_PATH
		);

		wp_die();
	}

	/**
	 * Populating custom columns with content.
	 *
	 * @param array   $column is an array of default columns in Custom post type.
	 * @param integer $post_id is the post id.
	 *
	 * @since 1.0.0
	 */
	public function wps_membership_for_woo_fill_columns_members( $column, $post_id ) {
		switch ( $column ) {

			case 'membership_id':
				$author_id    = get_post_field( 'post_author', $post_id );
				$display_name = get_the_author_meta( 'display_name', get_post_meta( $post_id, 'wps_member_user', true ) );
				$withdrawal_status = get_post_meta( $post_id, 'member_status', true );
				?>
				<strong class="wps_hide_<?php echo esc_attr( $withdrawal_status ); ?>"><?php echo sprintf( ' #%u %s ', esc_html( $post_id ), esc_html( $display_name ) ); ?></strong>
				<?php
				break;

			case 'members_status':
				$author_id    = get_post_field( 'post_author', $post_id );
				$plan_id = get_post_meta( $post_id, 'plan_obj', true );

				if ( is_array( $plan_id ) && ! empty( $plan_id ) ) {
					$plan_id = $plan_id['ID'];
				} else {
					$plan_id = 0;
				}
				$withdrawal_status = get_post_meta( $post_id, 'member_status', true );

				if ( 'complete' === $withdrawal_status ) {
					?>
										<span class="wps-member-status-complete" ><?php esc_html_e( 'complete', 'membership-for-woocommerce' ); ?></span>
										<?php
				} elseif ( 'cancelled' === $withdrawal_status ) {
					?>
										<span class="wps-member-status-cancelled" ><?php esc_html_e( 'cancelled', 'membership-for-woocommerce' ); ?></span>
									<?php
				} elseif ( 'expired' === $withdrawal_status ) {
					?>
										<span class="wps-member-status-cancelled" ><?php esc_html_e( 'expired', 'membership-for-woocommerce' ); ?></span>
									<?php
				} else {
					?>
				<form action="" method="POST">
				<select onchange="this.className=this.options[this.selectedIndex].className" plan_id="<?php echo esc_attr( $plan_id ); ?>" user_id="<?php echo esc_attr( $author_id ); ?>" post_id_value="<?php echo esc_attr( $post_id ); ?>" name="wps-wpg-gen-table_status" id="wps-wpg-gen-table_status" aria-controls="wps-wpg-gen-section-table" class="<?php echo esc_attr( get_post_status( $post_id ) ); ?>">
					<option class="complete" value="complete" >&nbsp;&nbsp;<?php esc_html_e( 'Complete', 'membership-for-woocommerce' ); ?></option>
					<option class="pending" value="pending" selected="selected">&nbsp;&nbsp;<?php esc_html_e( 'Pending', 'membership-for-woocommerce' ); ?></option>
					<option class="cancelled" value="cancelled" >&nbsp;&nbsp;<?php esc_html_e( 'cancelled', 'membership-for-woocommerce' ); ?></option>
				</select>
				<input type="hidden" name="withdrawal_id" value="<?php echo esc_attr( $post_id ); ?>" />
				<input type="hidden" name="user_id" value="<?php echo esc_attr( $author_id ); ?>" />
				<div id="overlay" style="display:none">
					<img src='<?php echo esc_url( MEMBERSHIP_FOR_WOOCOMMERCE_DIR_URL ) . 'admin/image/loader.gif'; ?>' width="64" height="64" /><br>Loading..
				</div>
			</form>
					<?php

				}
									$status = get_post_meta( $post_id, 'member_status', true );

				break;

			case 'membership_user':
				$author_id   = get_post_field( 'post_author', $post_id );
				$author_name = get_the_author_meta( 'user_nicename', get_post_meta( $post_id, 'wps_member_user', true ) );

				echo esc_html( $author_name );
				break;

			case 'membership_plan_associated':
				$plan     = get_post_meta( $post_id, 'plan_obj', true );
				$author_name = get_the_author_meta( 'user_nicename', get_post_meta( $post_id, 'wps_member_user', true ) );
				$plan_name = '';
				if ( is_array( $plan ) && ! empty( $plan ) ) {
					foreach ( $plan as $key => $value ) {
						if ( 'post_title' == $key ) {
							$plan_name .= $value;
							$plan_name .= ',';

						}
					}
				}
				$plan_name = $plan_name . trim( '' );
				$plan_name = rtrim( $plan_name, ',' );
				echo esc_html( ! empty( $plan_name ) ? $plan_name : __( 'No Plan Found', 'membership-for-woocommerce' ) );

				break;

			case 'membership_user_view':
				add_thickbox();
				?>
				<a title="<?php echo esc_html__( 'Member ID #', 'membership-for-woocommerce' ) . esc_html( $post_id ); ?>" href="admin-ajax.php?action=wps_membership_get_member_content&post_id=<?php echo esc_html( $post_id ); ?>&nonce=<?php echo esc_html( wp_create_nonce( 'preview-nonce' ) ); ?>" class="thickbox member-preview"><img src="<?php echo esc_url( MEMBERSHIP_FOR_WOOCOMMERCE_DIR_URL . 'admin/resources/icons/eye-icon.svg' ); ?>" alt="eye"></a>

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
	public function wps_membership_get_member_content() {

		// Nonce Verification.
		check_ajax_referer( 'preview-nonce', 'nonce' );

		$member_id = ! empty( $_GET['post_id'] ) ? sanitize_text_field( wp_unslash( $_GET['post_id'] ) ) : '';
		$instance  = $this->global_class;

		wc_get_template(
			'admin/partials/templates/admin-ajax-templates/members-plans-preview.php',
			array(
				'member_id' => $member_id,
				'instance'  => $instance,
			),
			'',
			MEMBERSHIP_FOR_WOOCOMMERCE_DIR_PATH
		);
		wp_die();
	}

	/**
	 * Select2 search for membership target products.
	 *
	 * @since 1.0.0
	 */
	public function wps_membership_search_products_for_membership() {
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

		/**
		 * Filter for search products.
		 *
		 * @since 1.0.0
		 */
		$search_results = apply_filters( 'wps_membership_search_products_for_membership', $search_results );

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

				$exclude = get_post_meta( $search_results->post->ID, '_wps_membership_exclude', true );

				if ( 'yes' === $exclude ) {
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
	public function wps_membership_search_product_categories_for_membership() {

		$return = array();
		$args   = array(
			'search'   => ! empty( $_GET['q'] ) ? sanitize_text_field( wp_unslash( $_GET['q'] ) ) : '',
			'taxonomy' => 'product_cat',
			'orderby'  => 'name',
		);

		/**
		 * Filter for search category.
		 *
		 * @since 1.0.0
		 */
		$args = apply_filters( 'wps_membership_search_product_categories_for_membership', $args );

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
	public function wps_membership_for_woo_export_members() {

		$screen = get_current_screen();
		if ( isset( $screen->id ) && ( 'edit-wps_cpt_members' === $screen->id ) ) {
			$obj_public = new Membership_For_Woocommerce_Public( '', '' );
			$data = $obj_public->custom_query_data;

			?>
								
				<select id="filter_member_status" >
					<option value="All"><?php esc_html_e( 'Filter By Status', 'membership-for-woocommerce' ); ?></option>
					<option value="All"><?php esc_html_e( 'Show All', 'membership-for-woocommerce' ); ?></option>
					<option value="complete"><?php esc_html_e( 'Complete', 'membership-for-woocommerce' ); ?></option>
					<option value="expired"><?php esc_html_e( 'Expired', 'membership-for-woocommerce' ); ?></option>
					<option value="pending"><?php esc_html_e( 'Pending', 'membership-for-woocommerce' ); ?></option>
					<option value="cancelled"><?php esc_html_e( 'Cancelled', 'membership-for-woocommerce' ); ?></option>
				</select>
				<select id="filter_membership_name" >
					<option value="All"><?php esc_html_e( 'Filter By Membership Plan', 'membership-for-woocommerce' ); ?></option>
					<option value="All"><?php esc_html_e( 'Show All', 'membership-for-woocommerce' ); ?></option>
					<?php
					if ( ! empty( $data ) && is_array( $data ) ) {

						if ( is_array( $data ) && ! empty( $data ) ) {
							foreach ( $data as $plan_membership ) {
								if ( ! empty( $plan_membership['post_title'] ) ) {
									?>
									<option value="<?php echo esc_attr( $plan_membership['post_title'] ); ?>"><?php echo esc_html( $plan_membership['post_title'] ); ?></option>
										
									<?php
								}
							}
						}
					}
					?>
				</select>
					
			<input type="submit" name="export_all_members" id="export_all_members" class="button button-primary" value="<?php esc_html_e( 'Export Members', 'membership-for-woocommerce' ); ?>">
			
			<?php

		}
	}

	/**
	 * Export all Members data as CSV from members.
	 *
	 * @since 1.0.0
	 */
	public function wps_membership_for_woo_export_csv_members() {

		if ( isset( $_GET['export_all_members'] ) ) {

			global $post;

			$args = array(
				'post_type'   => 'wps_cpt_members',
				'numberposts' => -1,
			);

			$all_posts = get_posts( $args );

			if ( ! empty( $all_posts ) ) {

				header( 'Content-type: text/csv' );
				header( 'Content-Disposition: attachment; filename="wps_members.csv"' );
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
						'Plan ID',
						'Plan Name',
						'Membership Status',
						'Expiry Date',
					)
				);

				//phpcs:disable
				foreach ( $all_posts as $post_datas ) {
					setup_postdata( $post_datas );
					fputcsv(
						$file,
						array(
							$post_datas->ID,
							! empty( $post_datas->post_author ) ? get_the_author_meta( 'display_name', $post_datas->post_author ) : '',
							get_the_author_meta( 'user_email' ),
							$this->global_class->get_member_details( $post_datas, 'name' ),
							$this->global_class->get_member_details( $post_datas, 'email' ),
							$this->global_class->get_member_details( $post_datas, 'phone' ),
							$this->global_class->get_member_details( $post_datas, 'plan_id' ),
							$this->global_class->get_member_details( $post_datas, 'plan_name' ),
							$this->global_class->get_member_details( $post_datas, 'plan_status' ),
							'',
						)
					);

				}
				//phpcs:enable

				fclose( $file );
				exit;
			}
		}
	}

	/**
	 * Export all Plans data as CSV from Memberships.
	 *
	 * @since 1.0.0
	 */
	public function wps_membership_for_woo_export_csv_membership() {

		if ( isset( $_GET['export_all_membership'] ) ) {

			global $post;

			$args = array(
				'post_type'   => 'wps_cpt_membership',
				'post_status' => array( 'private', 'draft', 'pending', 'publish' ),
				'numberposts' => -1,
			);

			$all_posts = get_posts( $args );

			if ( ! empty( $all_posts ) ) {

				header( 'Content-type: text/csv' );
				header( 'Content-Disposition: attachment; filename="wps_membership.csv"' );
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
				$args = array(
					'post_type'   => 'wps_cpt_membership',
					'post_status' => array( 'publish' ),
					'numberposts' => -1,
				);

				// phpcs:disable
				foreach ( $all_posts as $single_post ) { /* phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited */

					setup_postdata( $single_post );

					fputcsv(
						$file,
						array(
							$single_post->ID,
							get_post_field( 'post_title', $single_post->ID ),
							get_post_field( 'post_status', $single_post->ID ),
							get_post_meta( $single_post->ID, 'wps_membership_plan_price', true ),
							get_post_meta( $single_post->ID, 'wps_membership_plan_name_access_type', true ),
							get_post_meta( $single_post->ID, 'wps_membership_plan_duration', true ),
							get_post_meta( $single_post->ID, 'wps_membership_plan_duration_type', true ),
							get_post_meta( $single_post->ID, 'wps_membership_plan_recurring', true ),
							get_post_meta( $single_post->ID, 'wps_membership_plan_access_type', true ),
							get_post_meta( $single_post->ID, 'wps_membership_plan_time_duration', true ),
							get_post_meta( $single_post->ID, 'wps_membership_plan_time_duration_type', true ),
							get_post_meta( $single_post->ID, 'wps_membership_plan_offer_price_type', true ),
							get_post_meta( $single_post->ID, 'wps_memebership_plan_discount_price', true ),
							get_post_meta( $single_post->ID, 'wps_memebership_plan_free_shipping', true ),
							$this->global_class->csv_get_prod_title( get_post_meta( $single_post->ID, 'wps_membership_plan_target_ids', true ) ),
							$this->global_class->csv_get_cat_title( get_post_meta( $single_post->ID, 'wps_membership_plan_target_categories', true ) ),
							get_post_field( 'post_content', $single_post->ID ),
						)
					);
				}
				// phpcs:enable

				fclose( $file );
				exit;
			}
		}
	}

	/**
	 * Add export to csv button on Membership CPT
	 *
	 * @since 1.0.0
	 */
	public function wps_membership_for_woo_export_membership() {

		$screen = get_current_screen();

		if ( isset( $screen->id ) && ( 'edit-wps_cpt_membership' === $screen->id ) ) {

			$this->global_class->import_csv_modal_content();
			?>

			<input type="submit" name="export_all_membership" id="export_all_membership" class="button button-primary" value="<?php esc_html_e( 'Export Plans', 'membership-for-woocommerce' ); ?>">
			<input type="submit" name="import_all_membership" id="import_all_membership" class="button button-primary" value="<?php esc_html_e( 'Import Plans', 'membership-for-woocommerce' ); ?>">

			<?php
		}
	}


	/**
	 * Members billing metabox save.
	 *
	 * @param int $post_id is the post ID.
	 * @since 1.0.0
	 */
	public function wps_membership_save_member_fields( $post_id ) {

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
		check_admin_referer( 'wps_members_creation_nonce', 'wps_members_nonce_field' );

		// Saving member actions metabox fields.
		$actions = array(
			'member_status'  => ! empty( $_POST['member_status'] ) ? sanitize_text_field( wp_unslash( $_POST['member_status'] ) ) : '',
			'member_actions' => ! empty( $_POST['member_actions'] ) ? sanitize_text_field( wp_unslash( $_POST['member_actions'] ) ) : '',
		);
		$plan_id = '';

			// When plans are assigned manually.
		if ( isset( $_POST['members_plan_assign'] ) ) {

			$plan_id = ! empty( $_POST['members_plan_assign'] ) ? sanitize_text_field( wp_unslash( $_POST['members_plan_assign'] ) ) : '';

			if ( ! empty( $plan_id ) ) {

				$plan_obj = get_post( $plan_id, ARRAY_A );

				$post_meta = get_post_meta( $plan_id );

				// Formatting array.
				foreach ( $post_meta as $post_meta_key => $post_meta_value ) {

					$post_meta[ $post_meta_key ] = reset( $post_meta_value );
				}

				$plan_meta = array_merge( $plan_obj, $post_meta );

				update_post_meta( $post_id, 'plan_obj', $plan_meta );
			}
		}

		$post   = get_post( $post_id );
		$current_assigned_user = '';
		$member_details = get_post_meta( $post_id, 'billing_details', true );
		$email      = ! empty( $member_details['membership_billing_email'] ) ? $member_details['membership_billing_email'] : '';
		if ( ! empty( $email ) ) {
			$user = get_user_by( 'email', $email );
			$current_assigned_user = $user->ID;
		} else {
			$current_assigned_user = ! empty( $_POST['wps_member_user'] ) ? sanitize_text_field( wp_unslash( $_POST['wps_member_user'] ) ) : '';

		}
		if ( $current_assigned_user ) {

			update_post_meta( $post_id, 'wps_member_user', $current_assigned_user );
		}

		$current_memberships = get_user_meta( $current_assigned_user, 'mfw_membership_id', true );

		$current_memberships = ! empty( $current_memberships ) ? $current_memberships : array();
		if ( ! in_array( $post_id, (array) $current_memberships ) ) {
			array_push( $current_memberships, $post_id );
		}

		if ( 'yes' == get_post_meta( $plan_id, 'wps_membership_subscription', true ) ) {
			Update_post_meta( $post_id, 'is_subscription_plan_member', 'yes' );
		} else {
			Update_post_meta( $post_id, 'is_subscription_plan_member', '' );
		}

		// Assign membership plan to user and assign 'member' role to it.
		update_user_meta( $current_assigned_user, 'mfw_membership_id', $current_memberships );

		// If manually completing membership then set its expiry date.
		if ( 'complete' == $_POST['member_status'] ) {

			// Getting current activation date.
			$current_date = gmdate( 'Y-m-d' );

			$plan_obj = get_post_meta( $post_id, 'plan_obj', true );

			// Save expiry date in post.
			if ( ! empty( $plan_obj ) ) {

				$membership_plubic = new Membership_For_Woocommerce_Public( $this->plugin_name, $this->version );
				$membership_plubic->assign_club_membership_to_member( $plan_obj['ID'], $plan_obj, $post_id );
				$access_type = get_post_meta( $plan_obj['ID'], 'wps_membership_plan_access_type', true );

				if ( 'delay_type' == $access_type ) {
					$time_duration      = get_post_meta( $plan_obj['ID'], 'wps_membership_plan_time_duration', true );
					$time_duration_type = get_post_meta( $plan_obj['ID'], 'wps_membership_plan_time_duration_type', true );
					$current_date = gmdate( 'Y-m-d', strtotime( $current_date . ' + ' . $time_duration . ' ' . $time_duration_type ) );
				}

				if ( 'lifetime' == $plan_obj['wps_membership_plan_name_access_type'] ) {

					update_post_meta( $post_id, 'member_expiry', 'Lifetime' );

				} elseif ( 'limited' == $plan_obj['wps_membership_plan_name_access_type'] ) {

					$duration = $plan_obj['wps_membership_plan_duration'] . ' ' . $plan_obj['wps_membership_plan_duration_type'];
					$today_date = gmdate( 'Y-m-d' );
					$expiry_date = strtotime( $today_date . $duration );

					update_post_meta( $post_id, 'member_expiry', $expiry_date );

					$order_id = get_post_meta( $post_id, 'member_order_id', true );
					if ( 'yes' == $plan_obj['wps_membership_subscription'] ) {
						$subscription_id = get_post_meta( $order_id, 'wps_subscription_id', true );
						if ( ! empty( $subscription_id ) ) {

							update_post_meta( $subscription_id, 'wps_subscription_status', 'active' );
							update_post_meta( $subscription_id, 'wps_next_payment_date', $expiry_date );

							if ( ! empty( $plan_obj['wps_membership_subscription_expiry'] ) ) {
								if ( function_exists( 'wps_sfw_susbcription_expiry_date' ) ) {
									$current_time = current_time( 'timestamp' );
									$wps_susbcription_end = wps_sfw_susbcription_expiry_date( $subscription_id, $current_time );
									update_post_meta( $subscription_id, 'wps_susbcription_end', $wps_susbcription_end );
								}
							} else {
								update_post_meta( $subscription_id, 'wps_susbcription_end', '' );
							}
						}
					}
				}

				$post   = get_post( $post_id );
				$user    = get_userdata( $current_assigned_user );

				$user = new WP_User( $current_assigned_user ); // create a new user object for this user.
				$expiry_date = get_post_meta( $post_id, 'member_expiry', true );
				if ( 'Lifetime' == $expiry_date ) {
					$expiry_date = 'Lifetime';
				} else {
					$expiry_date = esc_html( ! empty( $expiry_date ) ? gmdate( 'Y-m-d', $expiry_date ) : '' );
				}

				$order_id = get_post_meta( $post_id, 'member_order_id', true );

				$user_name = $user->data->display_name;
				$customer_email = WC()->mailer()->emails['membership_creation_email'];
				if ( ! empty( $customer_email ) ) {
					$email_status = $customer_email->trigger( $current_assigned_user, $plan_obj, $user_name, $expiry_date, $order_id );
				}
			}

			update_user_meta( $current_assigned_user, 'is_member', 'member' );

		} elseif ( 'cancelled' == $_POST['member_status'] ) {  // If manually cancelling membership then remove its expiry date.

			$post   = get_post( $post_id );
			$user = get_userdata( $current_assigned_user );
			$expiry_date = '';
			$plan_obj = get_post_meta( $post_id, 'plan_obj', true );
			$today_date = gmdate( 'Y-m-d' );
			// Save expiry date in post.
			if ( ! empty( $plan_obj ) ) {

				$access_type = get_post_meta( $plan_obj['ID'], 'wps_membership_plan_access_type', true );

				if ( 'delay_type' == $access_type ) {
					$time_duration      = get_post_meta( $plan_obj['ID'], 'wps_membership_plan_time_duration', true );
					$time_duration_type = get_post_meta( $plan_obj['ID'], 'wps_membership_plan_time_duration_type', true );

					$current_date = gmdate( 'Y-m-d', strtotime( $today_date . ' + ' . $time_duration . ' ' . $time_duration_type ) );

				}

				if ( 'lifetime' == $plan_obj['wps_membership_plan_name_access_type'] ) {

					update_post_meta( $post_id, 'member_expiry', 'Lifetime' );

				} elseif ( 'limited' == $plan_obj['wps_membership_plan_name_access_type'] ) {

					$duration = $plan_obj['wps_membership_plan_duration'] . ' ' . $plan_obj['wps_membership_plan_duration_type'];

					$expiry_date = strtotime( $current_date . $duration );

					if ( 'delay_type' == $access_type ) {
						$delay_duration = $time_duration . ' ' . $time_duration_type;

						$expiry_date = gmdate( strtotime( $today_date . $duration ) );
						$date_exipary = gmdate( 'Y-m-d', $expiry_date );
						$expiry_date = strtotime( $date_exipary . $delay_duration );

					}
					update_post_meta( $post_id, 'member_expiry', $expiry_date );
				}
			}

			$user_name = $user->data->display_name;
			$order_id = get_post_meta( $post_id, 'member_order_id', true );
			$customer_email = WC()->mailer()->emails['membership_cancell_email'];
			$expiry_date = get_post_meta( $post_id, 'member_expiry', true );

			if ( is_array( $plan_obj ) && array_key_exists( 'wps_membership_subscription', $plan_obj ) ) {

				if ( 'yes' == $plan_obj['wps_membership_subscription'] ) {
					$subscription_id = get_post_meta( $order_id, 'wps_subscription_id', true );
					if ( ! empty( $subscription_id ) ) {
						update_post_meta( $subscription_id, 'wps_subscription_status', 'cancelled' );

					}
				}
			}

			if ( 'Lifetime' == $expiry_date ) {
				$expiry_date = 'Lifetime';
			} else {
				$expiry_date = esc_html( ! empty( $expiry_date ) ? gmdate( 'Y-m-d', $expiry_date ) : '' );

			}

			if ( ! empty( $customer_email ) ) {

				$email_status = $customer_email->trigger( $current_assigned_user, $plan_obj, $user_name, $expiry_date, $order_id );
			}
			update_post_meta( $post_id, 'member_expiry', '' );
			update_post_meta( $post_id, 'plan_obj', '' );

			$other_member_exists = false;
			$wps_membership_posts = get_post_field( 'post_author', $post_id );

			$memberships = get_user_meta( $wps_membership_posts, 'mfw_membership_id', true );
			foreach ( $memberships as $key => $m_id ) {

				$status = get_post_meta( $m_id, 'member_status', true );
				if ( 'complete' == $status ) {
					if ( $m_id == $post_id ) {
						$other_member_exists = false;
					} else {
						$other_member_exists = true;
					}
				}
			}

			if ( false == $other_member_exists ) {
				update_user_meta( $current_assigned_user, 'is_member', '' );
			}
		} else {

			$order_id = get_post_meta( $post_id, 'member_order_id', true );
			$expiry_date = get_post_meta( $post_id, 'member_expiry', true );
			$subscription_id = get_post_meta( $order_id, 'wps_subscription_id', true );
			if ( 'yes' == $plan_obj['wps_membership_subscription'] || ! empty( $order_id ) ) {
				$subscription_id = get_post_meta( $order_id, 'wps_subscription_id', true );
				if ( ! empty( $subscription_id ) ) {
					update_post_meta( $subscription_id, 'wps_subscription_status', sanitize_text_field( wp_unslash( $_POST['member_status'] ) ) );
					update_post_meta( $subscription_id, 'wps_next_payment_date', $expiry_date );
					if ( ! empty( $plan_obj['wps_membership_subscription_expiry'] ) ) {
						if ( function_exists( 'wps_sfw_susbcription_expiry_date' ) ) {
							$current_time = current_time( 'timestamp' );
							$wps_susbcription_end = wps_sfw_susbcription_expiry_date( $subscription_id, $current_time );
							update_post_meta( $subscription_id, 'wps_susbcription_end', $wps_susbcription_end );
						}
					} else {
						update_post_meta( $subscription_id, 'wps_susbcription_end', '' );
					}
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

		} elseif ( isset( $_POST['billing_payment'] ) ) {

			$payment = ! empty( $_POST['billing_payment'] ) ? sanitize_text_field( wp_unslash( $_POST['billing_payment'] ) ) : '';
		} else {
			$payment = ! empty( get_post_meta( $post_id, 'billing_details_payment', true ) ) ? get_post_meta( $post_id, 'billing_details_payment', true ) : '';
		}

			// phpcs:disable
		$fields = array(
			'membership_billing_first_name' => ! empty( $_POST['billing_first_name'] ) ? sanitize_text_field( wp_unslash( $_POST['billing_first_name'] ) ) : '',
			'membership_billing_last_name'  => ! empty( $_POST['billing_last_name'] ) ? sanitize_text_field( wp_unslash( $_POST['billing_last_name'] ) ) : '',
			'membership_billing_company'    => ! empty( $_POST['billing_company'] ) ? sanitize_text_field( wp_unslash( $_POST['billing_company'] ) ) : '',
			'membership_billing_address_1'  => ! empty( $_POST['billing_address_1'] ) ? sanitize_text_field( wp_unslash( $_POST['billing_address_1'] ) ) : '',
			'membership_billing_address_2'  => ! empty( $_POST['billing_address_2'] ) ? sanitize_text_field( wp_unslash( $_POST['billing_address_2'] ) ) : '',
			'membership_billing_city'       => ! empty( $_POST['billing_city'] ) ? sanitize_text_field( wp_unslash( $_POST['billing_city'] ) ) : '',
			'membership_billing_postcode'   => ! empty( $_POST['billing_postcode'] ) ? sanitize_text_field( wp_unslash( $_POST['billing_postcode'] ) ) : '',
			'membership_billing_country'    => ! empty( $_POST['billing_country'] ) ? sanitize_text_field( wp_unslash( $_POST['billing_country'] ) ) : '',
			'membership_billing_state'      => ! empty( $_POST['billing_state'] ) ? sanitize_text_field( wp_unslash( $_POST['billing_state'] ) ) : '',
			'membership_billing_email'      => ! empty( $_POST['billing_email'] ) ? sanitize_text_field( wp_unslash( $_POST['billing_email'] ) ) : '',
			'membership_billing_phone'      => ! empty( $_POST['billing_phone'] ) ? sanitize_text_field( wp_unslash( $_POST['billing_phone'] ) ) : '',
			'payment_method'                => $payment,
		);          // phpcs:enable

		update_post_meta( $post_id, 'billing_details', $fields );

	}

	/**
	 * Save meta box fields value.
	 *
	 * @param int $post_id Post ID.
	 *
	 * @since 1.0.0
	 */
	public function wps_membership_for_woo_save_fields( $post_id ) {

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
		check_admin_referer( 'wps_membership_plans_creation_nonce', 'wps_membership_plans_nonce' );
		$offered_product = array();
		$product_discount = '';
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
				if ( 'wps_membership_plan_hide_products' == $field ) {
					update_post_meta( $post_id, $field . $post_id, $post_data );
				}

				if ( 'wps_membership_plan_target_disc_ids' == $field ) {
					$offered_product = $post_data;
				}
				if ( 'wps_memebership_product_discount_price' == $field ) {
					if ( ! empty( $post_data ) ) {

						$product_discount = $post_data;
					} else {
						$product_discount = 0;
					}
				}
				if ( isset( $_POST['wps_membership_plan_info'] ) ) {
					update_post_meta( $post_id, 'wps_membership_plan_info', ! empty( map_deep( wp_unslash( $_POST['wps_membership_plan_info'] ), 'sanitize_text_field' ) ) ? map_deep( wp_unslash( $_POST['wps_membership_plan_info'] ), 'sanitize_text_field' ) : '' );
				}
				update_post_meta( $post_id, 'wps_membership_plan_target_ids_search', '' );
			}
			$discount_type  = isset( $_POST['wps_membership_product_offer_price_type'] ) ? sanitize_text_field( wp_unslash( $_POST['wps_membership_product_offer_price_type'] ) ) : ''; // phpcs:ignore

			foreach ( $offered_product as $key => $product_id ) {

				update_post_meta( $product_id, '_wps_membership_discount_' . $post_id, $product_discount );
				if ( '%' == $discount_type ) {

					update_post_meta( $product_id, '_wps_membership_percentage', 'yes' );
				} else {
					update_post_meta( $product_id, '_wps_membership_percentage', 'no' );
				}
			}
		}
		if ( ! empty( $_POST['wps_membership_plan_duration_type'] ) ) {

			if ( is_array( $_POST['wps_membership_plan_duration_type'] ) ) {

				$post_data = ! empty( $_POST['wps_membership_plan_duration_type'] ) ? array_map( 'sanitize_text_field', wp_unslash( $_POST['wps_membership_plan_duration_type'] ) ) : $default;

			} else {

				$post_data = ! empty( $_POST['wps_membership_plan_duration_type'] ) ? sanitize_text_field( wp_unslash( $_POST['wps_membership_plan_duration_type'] ) ) : $default;

			}
		}
		update_post_meta( $post_id, 'wps_membership_subscription_expiry_type', substr( $post_data, 0, -1 ) );
	}

	/**
	 * Add notices for free membership to plans.
	 */
	public function wps_membership_shipping_notice() {

		global $post;

		$screen = get_current_screen();

		$post_id = isset( $_GET['post'] ) ? sanitize_text_field( wp_unslash( $_GET['post'] ) ) : '';

		if ( ! empty( $post_id ) ) {

			$free_shipping = get_post_meta( $post_id, 'wps_memebership_plan_free_shipping', true );

			$page_id = $screen->id;

			if ( 'wps_cpt_membership' == $page_id ) {

				if ( $post->post_date_gmt == $post->post_modified_gmt ) {

					if ( 'publish' == $post->post_status ) {

						if ( ! empty( $free_shipping ) && 'yes' == $free_shipping ) {
							?>
							<div class="notice notice-success is-dismissible wps-notice"> 
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
	 * Creating shipping method for membership.
	 *
	 * @param array $methods an array of shipping methods.
	 *
	 * @since 1.0.0
	 */
	public function wps_membership_for_woo_create_shipping_method( $methods ) {

		if ( ! class_exists( 'WPS_Membership_Free_Shipping_Method' ) ) {
			/**
			 * Custom shipping class for membership.
			 */
			require_once plugin_dir_path( __FILE__ ) . '/classes/class-wps-membership-free-shipping-method.php'; // Including class file.
			new WPS_Membership_Free_Shipping_Method();
		}
	}

	/**
	 * Creating shipping method for membership.
	 *
	 * @param array  $user_id id of current user.
	 * @param object $old_user_data old data of currentt user.
	 *
	 * @since 1.0.0
	 */
	public function wps_membership_for_woo_update_profile_for_member( $user_id, $old_user_data ) {

		if ( in_array( 'member', (array) $old_user_data->roles ) ) {
			$user = new WP_User( $user_id ); // create a new user object for this user.
			$user->add_role( 'member' ); // set them to whatever role you want using the full word.
		}
	}

	/**
	 * Creating shipping method for membership.
	 *
	 * @param array $post_id id of current post.
	 *
	 * @since 1.0.0
	 */
	public function wps_membership_for_woo_add_to_trash_member( $post_id ) {
		update_post_meta( $post_id, 'member_status', 'cancelled' );
	}

	/**
	 * Creating activation hook new blog.
	 *
	 * @param object $new_site id of current blog.
	 *
	 * @since 1.0.0
	 */
	public function wps_membership_for_woo_on_create_new_blog( $new_site ) {
		if ( ! function_exists( 'is_plugin_active_for_network' ) ) {
			require_once ABSPATH . '/wp-admin/includes/plugin.php';
		}
		// check if the plugin has been activated on the network.
		if ( is_plugin_active_for_network( 'membership-for-woocommerce/membership-for-woocommerce.php' ) ) {
			$blog_id = $new_site->blog_id;

			// switch to newly created site.
			switch_to_blog( $blog_id );
			global $wpdb;
			$blog_ids = $wpdb->get_col( "SELECT blog_id FROM $wpdb->blogs" );
			foreach ( $blog_ids as $blog_id ) {
				switch_to_blog( $blog_id );

				// Creating Instance of the global functions class.
				$global_class = Membership_For_Woocommerce_Global_Functions::get();

				add_role(
					'member',
					__( 'Member', 'membership-for-woocommerce' ),
					array(
						'read' => true,
					)
				);

				/**
				 * Generating default membership plans page at the time of plugin activation.
				 */
				$wps_membership_default_plans_page_id = get_option( 'wps_membership_default_plans_page' );
				if ( empty( $wps_membership_default_plans_page_id ) ) {

					$page_content = '5' <= get_bloginfo( 'version' ) ? $global_class->gutenberg_content() : '[wps_membership_default_plans_page]';

					if ( empty( $wps_membership_default_plans_page_id ) || 'publish' !== get_post_status( $wps_membership_default_plans_page_id ) ) {

						$wps_membership_plans_page = array(
							'comment_status' => 'closed',
							'ping_status'    => 'closed',
							'post_content'   => $page_content,
							'post_name'      => 'membership-plans',
							'post_status'    => 'publish',
							'post_title'     => 'Membership Plans',
							'post_type'      => 'page',
						);

						$wps_membership_plans_post = wp_insert_post( $wps_membership_plans_page );

						update_option( 'wps_membership_default_plans_page', $wps_membership_plans_post );
					}
				} else {
						  $current_post = get_post( $wps_membership_default_plans_page_id, 'ARRAY_A' );
						  $current_post['post_status'] = 'publish';
						  wp_update_post( $current_post );
				}

				/**
				 * Generating default membership plans page at the time of plugin activation.
				 */
				$wps_membership_default_product = get_option( 'wps_membership_default_product' );

				if ( empty( $wps_membership_default_product ) || 'private' !== get_post_status( $wps_membership_default_product ) ) {

						 $wps_membership_product = array(
							 'post_name'    => 'membership-product',
							 'post_status'  => 'private',
							 'post_title'   => 'Membership Product',
							 'post_type'    => 'product',
							 'post_author'  => 1,
							 'post_content' => stripslashes( html_entity_decode( 'Auto generated product for membership please do not delete or update.', ENT_QUOTES, 'UTF-8' ) ),
						 );

						 $wps_membership_product_id = wp_insert_post( $wps_membership_product );

						 if ( ! is_wp_error( $wps_membership_product_id ) ) {

							 $product = wc_get_product( $wps_membership_product_id );

							 wp_set_object_terms( $wps_membership_product_id, 'simple', 'product_type' );
							 update_post_meta( $wps_membership_product_id, '_regular_price', 0 );
							 update_post_meta( $wps_membership_product_id, '_price', 0 );
							 update_post_meta( $wps_membership_product_id, '_visibility', 'hidden' );
							 update_post_meta( $wps_membership_product_id, '_virtual', 'yes' );
							 if ( version_compare( WC_VERSION, '3.0', '>=' ) ) {

								 $product->set_reviews_allowed( false );
								 $product->set_catalog_visibility( 'hidden' );
								 $product->save();
							 }

							 update_option( 'wps_membership_default_product', $wps_membership_product_id );
						 }
				}
				restore_current_blog();

			}
		}
	}

	/**
	 * Cancle membership acc to subscriptions.
	 *
	 * @param mixed $wps_subscription_id id of current subscription id.
	 * @param mixed $wps_status is the subscription status.
	 *
	 * @since 1.0.0
	 */
	public function wps_membership_cancel_membership_acc_susbcription( $wps_subscription_id, $wps_status ) {
		$order_id = get_post_meta( $wps_subscription_id, 'wps_parent_order', true );
		$order = wc_get_order( $order_id );
		$member_id = '';

		foreach ( $order->get_items() as $item_id => $item ) {

			if ( ! empty( $item->get_meta( '_member_id' ) ) ) {
				$member_id = $item->get_meta( '_member_id' );
			}
		}

		if ( ! empty( $member_id ) ) {
			update_post_meta( $member_id, 'member_status', 'cancelled' );

		}

	}

	/**
	 * Assign column in user table.
	 *
	 * @param [type] $contactmethods is the method to add new column.
	 * @return [type]
	 */
	public function wps_membership_new_column_value_assign( $contactmethods ) {
		$contactmethods['member_type'] = 'Member Type';
		return $contactmethods;
	}

	/**
	 * Assign Column Name in user table.
	 *
	 * @param [type] $column is the column to create.
	 * @return [type]
	 */
	public function wps_membership_new_modify_user_table_value( $column ) {
		$column['member_type'] = 'Member Type';
		return $column;
	}

	/**
	 * Undocumented function
	 *
	 * @param [type] $val return the value.
	 * @param [type] $column_name assign value to column name.
	 * @param [type] $user_id is the user id of the current user.
	 * @return [type]
	 */
	public function wps_membership_new_modify_user_table_add_user( $val, $column_name, $user_id ) {
		switch ( $column_name ) {
			case 'member_type':
				$is_member = get_user_meta( $user_id, 'is_member', true );
				$member_label = '';
				if ( 'member' == $is_member ) {
					$member_label = 'Member';
				} else {
					$member_label = 'Non-Member';
				}
				return $member_label;
			default:
		}
		return $val;
	}



	/**
	 * Displays notice to upgrade for membership plugin.
	 *
	 * @param [mixed] $plugin_file Path to the plugin file relative to the plugins directory.
	 * @return void
	 */
	public function mfw_migrate_db_keys_notice( $plugin_file ) {

		?>
		
	<tr class="plugin-update-tr active notice-warning notice-alt">
	<td colspan="4" class="plugin-update colspanchange">
		<div class="notice notice-warning inline update-message notice-alt">
			<div class='wps-notice-title wps-notice-section'>
				<p><strong>Membership Database Update Required!</strong></p>
			</div>
			<div class='wps-notice-content wps-notice-section'>
				<p>The latest update includes some substantial changes across different areas of plugin.<strong> Please Migrate Your Data </strong>by clicking on <strong>Start Import </strong>Button.</p>
			</div>
			<div class="treat-wrapper">
				<button class="treat-button"><?php esc_html_e( 'Start Import!', 'membership-for-woocommerce' ); ?></button>
			</div>
		</div>
	</td>
</tr>
<style>
	.wps-notice-section > p:before {
		content: none;
	}
</style>
	
		<?php

	}


	/**
	 * Ajax callback.
	 *
	 * @return void
	 */
	public function wps_membership_ajax_callbacks() {
		check_ajax_referer( 'wps_membership_nonce', 'nonce' );
		$event = ! empty( $_POST['event'] ) ? sanitize_text_field( wp_unslash( $_POST['event'] ) ) : '';
		if ( method_exists( $this, $event ) ) {
			$data = $this->$event( $_POST );
		} else {
			$data = esc_html__( 'method not found', 'membership-for-woocommerce' );
		}
		echo wp_json_encode( $data );
		wp_die();
	}


	/**
	 * Order count.
	 *
	 * @param string $type type.
	 * @param string $action action.
	 * @since    2.1.0
	 */
	public function wps_membership_get_count( $type = 'all', $action = 'count' ) {

		switch ( $type ) {
			case 'pending':
				$post_meta_keys = array(
					'mwb_membership_plan_price',
					'_mwb_sfw_product',
					'mwb_sfw_subscription_number',
					'mwb_sfw_subscription_interval',
					'mwb_sfw_subscription_expiry_number',
					'mwb_sfw_subscription_expiry_interval',
					'mwb_membership_plan_info',
					'mwb_membership_plan_name_access_type',
					'mwb_membership_plan_duration',
					'mwb_membership_plan_duration_type',
					'mwb_membership_subscription',
					'mwb_membership_subscription_expiry',
					'mwb_membership_subscription_expiry_type',
					'mwb_membership_plan_recurring',
					'mwb_membership_plan_access_type',
					'mwb_membership_plan_time_duration',
					'mwb_membership_plan_time_duration_type',
					'mwb_membership_plan_offer_price_type',
					'mwb_memebership_plan_discount_price',
					'mwb_memebership_plan_free_shipping',
					'mwb_membership_show_notice',
					'mwb_membership_notice_message',
					'mwb_membership_plan_target_categories',
					'mwb_membership_plan_target_ids',
					'mwb_membership_plan_post_target_ids',
					'mwb_membership_plan_target_tags',
					'mwb_membership_plan_target_post_categories',
					'mwb_membership_club',
					'mwb_membership_plan_page_target_ids',
					'mwb_membership_plan_target_disc_categories',
					'mwb_membership_plan_target_disc_tags',
					'mwb_membership_plan_target_disc_ids',
					'mwb_membership_product_offer_price_type',
					'mwb_memebership_product_discount_price',
					'mwb_member_user',
					'_mwb_membership_exclude',
					'_mwb_membership_discount_product_',
					'_mwb_membership_discount_product_price',
					'mwb_membership_plan_target_ids_search',
					'mwb_membership_plan_hide_products',
					'_mwb_membership_percentage',
					'mwb_membership_plan_target_post_tags',
					'mwb_membership_plan_price',
					'_mwb_membership_percentage_',
					'mwb_membership_plan_target_disc_ids',
					'mwb_membership_plan_target_disc_categories',
					'mwb_membership_plan_target_disc_tags',
					'_mwb_membership_discount_',
					'mwb_membership_exclude',
					'mwb_membership_plan_info',
					'mwb_memebership_product_discount_price',
					'mwb_membership_product_offer_price_type',
					'mwb_membership_club',
					'mwb_membership_plan_target_ids',
					'mwb_membership_plan_target_categories',
					'mwb_membership_plan_target_tags',
					'mwb_membership_plan_hide_products',
					'_mwb_membership_discount',
					'mwb_membership_product_offer_price_type',
					'_mwb_membership_discount_product_',
					'_mwb_membership_discount_product_price',
				);

				$result = get_posts(
					array(
						'post_type' => 'product',
						'post_status'    => array( 'publish', 'draft', 'trash' ),
						'numberposts'       => -1,
						'meta_key' => $post_meta_keys,
						'fields'         => 'ids',
					)
				);
					$temp2 = get_posts(
						array(
							'post_type' => 'product_variation',
							'post_status'    => array( 'publish', 'draft', 'trash' ),
							'numberposts'       => -1,
							'meta_key' => $post_meta_keys,
							'fields'         => 'ids',
						)
					);
					$temp3 = get_posts(
						array(
							'post_type' => 'mwb_cpt_members',
							'post_status'    => array( 'publish', 'draft', 'trash' ),
							'numberposts'       => -1,
							'meta_key' => $post_meta_keys,
							'fields'         => 'ids',
						)
					);
					$temp4 = get_posts(
						array(
							'post_type' => 'mwb_cpt_membership',
							'post_status'    => array( 'publish', 'draft', 'trash' ),
							'numberposts'       => -1,
							'meta_key' => $post_meta_keys,
							'fields'         => 'ids',
						)
					);
					$temp5 = get_posts(
						array(
							'post_type' => 'wps_cpt_membership',
							'post_status'    => array( 'publish', 'draft', 'trash' ),
							'numberposts'       => -1,
							'meta_key' => $post_meta_keys,
							'fields'         => 'ids',
						)
					);
					$temp6 = get_posts(
						array(
							'post_type' => 'wps_cpt_members',
							'post_status'    => array( 'publish', 'draft', 'trash' ),
							'numberposts'       => -1,
							'meta_key' => $post_meta_keys,
							'fields'         => 'ids',
						)
					);
				if ( empty( $result ) ) {
					$result = array();
				}
				if ( empty( $temp2 ) ) {
					$temp2 = array();
				}
				if ( empty( $temp3 ) ) {
					$temp3 = array();
				}
				if ( empty( $temp4 ) ) {
					$temp4 = array();
				}
				if ( empty( $temp5 ) ) {
					$temp5 = array();
				}
				if ( empty( $temp6 ) ) {
					$temp6 = array();
				}
				if ( is_array( $result ) && is_array( $temp2 ) ) {

					$result = array_merge( $result, $temp2 );

				}
				if ( is_array( $result ) && is_array( $temp3 ) ) {

					$result = array_merge( $result, $temp3 );

				}
				if ( is_array( $result ) && is_array( $temp4 ) ) {

					$result = array_merge( $result, $temp4 );

				}
				if ( is_array( $result ) && is_array( $temp5 ) ) {

					$result = array_merge( $result, $temp5 );

				}
				if ( is_array( $result ) && is_array( $temp6 ) ) {

					$result = array_merge( $result, $temp6 );

				}
				$final_result = array();
				foreach ( $result as $key => $value ) {
					$final_result[]['post_id'] = $value;
				}
				break;

		}

		if ( 'count' === $action ) {
			$final_result = ! empty( $final_result ) ? count( $final_result ) : 0;
		}

		return $final_result;
	}

	/**
	 * Import product callback.
	 *
	 * @param array $product_data The $_POST data.
	 */
	public function wps_mfw_import_single_product( $product_data = array() ) {

		$products = ! empty( $product_data['products'] ) ? $product_data['products'] : array();

		// return $products.
		if ( empty( $products ) ) {
			return compact( 'products' );
		}

		// Remove this product from request.
		foreach ( $products as $key => $product ) {
			$product_id = ! empty( $product['post_id'] ) ? $product['post_id'] : false;
			unset( $products[ $key ] );
			break;
		}

		// Attempt for one product.
		if ( ! empty( $product_id ) ) {

			try {

				$post_meta_keys = array(
					'mwb_membership_plan_price',
					'_mwb_sfw_product',
					'mwb_sfw_subscription_number',
					'mwb_sfw_subscription_interval',
					'mwb_sfw_subscription_expiry_number',
					'mwb_sfw_subscription_expiry_interval',
					'mwb_membership_plan_info',
					'mwb_membership_plan_name_access_type',
					'mwb_membership_plan_duration',
					'mwb_membership_plan_duration_type',
					'mwb_membership_subscription',
					'mwb_membership_subscription_expiry',
					'mwb_membership_subscription_expiry_type',
					'mwb_membership_plan_recurring',
					'mwb_membership_plan_access_type',
					'mwb_membership_plan_time_duration',
					'mwb_membership_plan_time_duration_type',
					'mwb_membership_plan_offer_price_type',
					'mwb_memebership_plan_discount_price',
					'mwb_memebership_plan_free_shipping',
					'mwb_membership_show_notice',
					'mwb_membership_notice_message',
					'mwb_membership_plan_target_categories',
					'mwb_membership_plan_target_ids',
					'mwb_membership_plan_post_target_ids',
					'mwb_membership_plan_target_tags',
					'mwb_membership_plan_target_post_categories',
					'mwb_membership_club',
					'mwb_membership_plan_page_target_ids',
					'mwb_membership_plan_target_disc_categories',
					'mwb_membership_plan_target_disc_tags',
					'mwb_membership_plan_target_disc_ids',
					'mwb_membership_product_offer_price_type',
					'mwb_memebership_product_discount_price',
					'mwb_member_user',
					'_mwb_membership_exclude',
					'_mwb_membership_discount_product_',
					'_mwb_membership_discount_product_price',
					'mwb_membership_plan_target_ids_search',
					'mwb_membership_plan_hide_products',
					'_mwb_membership_percentage',
					'mwb_membership_plan_target_post_tags',
					'mwb_membership_plan_price',
					'_mwb_membership_percentage_',
					'mwb_membership_plan_target_disc_ids',
					'mwb_membership_plan_target_disc_categories',
					'mwb_membership_plan_target_disc_tags',
					'_mwb_membership_discount_',
					'plan_obj',
					'mwb_membership_exclude',
					'mwb_membership_plan_info',
					'mwb_memebership_product_discount_price',
					'mwb_membership_product_offer_price_type',
					'mwb_membership_club',
					'mwb_membership_plan_target_ids',
					'mwb_membership_plan_target_categories',
					'mwb_membership_plan_target_tags',
					'mwb_membership_plan_hide_products',
					'_mwb_membership_discount',
					'mwb_membership_product_offer_price_type',
					'_mwb_membership_discount_product_',
					'_mwb_membership_discount_product_price',
				);

				foreach ( $post_meta_keys as $key => $meta_keys ) {
					if ( ! empty( $product_id ) ) {

						if ( 'mwb_member_user' == $meta_keys ) {
							$post_type_data = get_post( $product_id );
							$post_type = $post_type_data->post_type;
							if ( 'mwb_cpt_members' == $post_type ) {
								$args = array(
									'ID'        => $product_id,
									'post_type' => 'wps_cpt_members',
								);
								wp_update_post( $args );
							}
						}

						if ( 'plan_obj' == $meta_keys ) {
							$plan_obj_array = get_post_meta( $product_id, 'plan_obj', true );
							$plan_obj_array2 = array();
							if ( ! empty( $plan_obj_array ) ) {

								foreach ( $plan_obj_array as $key => $values ) {
									if ( ! is_array( $values ) && $key && $values ) {
										$new_key = str_replace( 'mwb_', 'wps_', $key );
										$new_value = str_replace( 'mwb_', 'wps_', $values );
										$plan_obj_array2[ $new_key ] = $new_value;
									}
								}

								update_post_meta( $product_id, 'plan_obj', $plan_obj_array2 );
								continue;
							}
						} else {

							$values  = get_post_meta( $product_id, $meta_keys, true );
							$new_key = str_replace( 'mwb_', 'wps_', $meta_keys );

							if ( ! empty( get_post_meta( $product_id, $new_key, true ) ) ) {
								continue;
							}

							$arr_val_post = array();
							if ( is_array( $values ) ) {
								foreach ( $values  as $key => $value ) {
									$keys = str_replace( 'mwb_', 'wps_', $key );

									$new_key1             = str_replace( 'mwb_', 'wps_', $value );
									$arr_val_post[ $key ] = $new_key1;
								}
								update_post_meta( $product_id, $new_key, $arr_val_post );
							} else {
								update_post_meta( $product_id, $new_key, $values );
							}
							delete_post_meta( $product_id, $meta_keys );
						}

						if ( 'mwb_membership_plan_hide_products' == $meta_keys ) {

							$value   = get_post_meta( $product_id, 'mwb_membership_plan_hide_products' . $product_id, true );
							$new_key = str_replace( 'mwb_', 'wps_', $meta_keys );

							if ( ! empty( $value ) ) {

								update_post_meta( $product_id, 'wps_membership_plan_hide_products' . $product_id, $value );

							}
							delete_post_meta( $product_id, 'mwb_membership_plan_hide_products' . $product_id );
						} else if ( '_mwb_membership_discount_' == $meta_keys ) {
							$temp_array = get_post_meta( $product_id );
							if ( is_array( $temp_array ) && ! empty( $temp_array ) ) {

								foreach ( $temp_array as $temp_key => $temp_value ) {
									if ( strpos( $temp_key, $meta_keys ) !== false ) {

										$value   = get_post_meta( $product_id, $temp_key, true );
										$new_key = str_replace( 'mwb_', 'wps_', $temp_key );
										if ( ! empty( $value ) ) {

											update_post_meta( $product_id, $new_key, $value );
										}
										delete_post_meta( $product_id, $temp_key );
									}
								}
							}
						} else {
									$values  = get_post_meta( $product_id, $meta_keys, true );
									$new_key = str_replace( 'mwb_', 'wps_', $meta_keys );

							if ( ! empty( get_post_meta( $product_id, $new_key, true ) ) ) {
								continue;
							}

									$arr_val_post = array();
							if ( is_array( $values ) ) {
								foreach ( $values  as $key => $value ) {

									$new_key1             = str_replace( 'mwb_', 'wps_', $value );
									$arr_val_post[ $key ] = $new_key1;
								}
								update_post_meta( $product_id, $new_key, $arr_val_post );
							} else {
								update_post_meta( $product_id, $new_key, $values );
							}
									delete_post_meta( $product_id, $meta_keys );
						}
					}
				}
				/**
				 * Action to data migrate.
				 *
				 * @since 1.0.0
				 */
				do_action( 'wps_membership_postmeta_data_migrate_for_pro', $product_id );

			} catch ( \Throwable $th ) {
				wp_die( esc_html( $th->getMessage() ) );
			}
		}
		return compact( 'products' );
	}

	/**
	 * Function to add new tab in product tab.
	 *
	 * @param array $product_data_tabs contains tabs.
	 * @return array
	 */
	public function mfw_attach_plan_product_data_tab( $product_data_tabs ) {
		$temp = true;
		global $post;
		$product_id = $post->ID;
		$terms_post = get_the_terms( $post->cat_ID, 'product_cat' );
		$product_cat_ids = array();

		if ( ! empty( $terms_post ) ) {

			foreach ( $terms_post as $term_cat ) {
				$product_cat_ids[] = $term_cat->term_id;

			}
		}

		$product_tag_ids = array();
		$terms = get_terms( 'product_tag' );

		if ( ! empty( $terms ) ) {

			foreach ( $terms as $term_tag ) {
				$product_tag_ids[] = $term_tag->term_id;

			}
		}

		$results = get_posts(
			array(
				'post_type' => 'wps_cpt_membership',
				'post_status' => 'publish',
				'meta_key' => 'wps_membership_plan_target_ids',
				'numberposts' => -1,
				'fields'  => 'ids',

			)
		);

		$product_ids = array();
		$category_ids = array();
		$tag_ids = array();
		foreach ( $results as $key => $value ) {
			$include_products = get_post_meta( $value, 'wps_membership_plan_target_ids', true );
			if ( ! is_array( $include_products ) && empty( $include_products ) ) {
				$include_products = array();
			}
			$offered_products = get_post_meta( $value, 'wps_membership_plan_target_disc_ids', true );
			if ( ! is_array( $offered_products ) && empty( $offered_products ) ) {
				$offered_products = array();
			}
			$product_ids = array_merge( $product_ids, $include_products, $offered_products );

			$include_cats = get_post_meta( $value, 'wps_membership_plan_target_categories', true );
			if ( ! is_array( $include_cats ) && empty( $include_cats ) ) {
				$include_cats = array();
			}
			$offered_cats = get_post_meta( $value, 'wps_membership_plan_target_disc_categories', true );
			if ( ! is_array( $offered_cats ) && empty( $offered_cats ) ) {
				$offered_cats = array();
			}
			$category_ids = array_merge( $category_ids, $include_cats, $offered_cats );

			$include_tags = get_post_meta( $value, 'wps_membership_plan_target_tags', true );
			if ( ! is_array( $include_tags ) && empty( $include_tags ) ) {
				$include_tags = array();
			}
			$offered_tags = get_post_meta( $value, 'wps_membership_plan_target_disc_tags', true );
			if ( ! is_array( $offered_tags ) && empty( $offered_tags ) ) {
				$offered_tags = array();
			}
			$tag_ids      = array_merge( $tag_ids, $include_tags, $offered_tags );
		}

		foreach ( $product_cat_ids as $key => $id ) {
			if ( in_array( $id, $category_ids ) ) {
				$temp = false;
			}
		}

		foreach ( $product_tag_ids as $key => $id ) {
			if ( in_array( $id, $tag_ids ) ) {
				$temp = false;
			}
		}

		if ( in_array( $product_id, $product_ids ) ) {
			$temp = false;
		}

		if ( true == $temp ) {

			$product_data_tabs['attach-membership'] = array(
				'label'  => __( 'Attach Membership', 'membership-for-woocommerce' ),
				'target' => 'wps_attach_membership',
			);
		} else {
			if ( ! empty( get_post_meta( $product_id, 'wps_membership_plan_with_product', true ) ) ) {

				update_post_meta( $product_id, 'wps_membership_plan_with_product', '' );
			}
		}

		return $product_data_tabs;
	}

	/**
	 * Function for adding data fields.
	 *
	 * @return void
	 */
	public function mfw_attach_plan_product_data_fields() {
		global $post;
		$product_id = $post->ID;
		$results = get_posts(
			array(
				'post_type' => 'wps_cpt_membership',
				'post_status' => 'publish',
				'meta_key' => 'wps_membership_plan_target_ids',
				'numberposts' => -1,

			)
		);

		$final_results = array(
			''        => __( 'Select Membership plan', 'woocommerce' ),
		);
		foreach ( $results as $key => $value ) {

			$final_results[ $results[ $key ]->ID ] = $results[ $key ]->post_title;

		}
		echo '<div class="wps_membership_dropdown hidden ">';
		woocommerce_wp_select(
			array(
				'id'          => 'wps_attach_plans',
				'label'       => __( 'Select Plan', 'membership-for-woocommerce' ),
				'selected'   => true,
				'description' => __( 'Choose plan to attach with product.', 'membership-for-woocommerce' ),
				'desc_tip'    => true,
				'options'     => $final_results,
				'value'      => get_post_meta( $product_id, 'wps_membership_plan_with_product', true ),
			)
		);

		echo '</div>';
		wp_nonce_field( 'wps_mfw_attach_membership_nonce', 'wps_mfw_attach_membership_nonce_field' );
	}

	/**
	 * Function to save data.
	 *
	 * @return void
	 */
	public function wps_mfw_save_product_data() {
		global $post;
		if ( isset( $post->ID ) ) {
			if ( ! current_user_can( 'edit_post', $post->ID ) ) {
				return;
			}
			if ( ! isset( $_POST['wps_mfw_attach_membership_nonce_field'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['wps_mfw_attach_membership_nonce_field'] ) ), 'wps_mfw_attach_membership_nonce' ) ) {
				return;
			}
			$product_id = $post->ID;
			$product = wc_get_product( $product_id );
			if ( isset( $product ) && is_object( $product ) ) {
				$selected_plan = isset( $_POST['wps_attach_plans'] ) ? sanitize_text_field( wp_unslash( $_POST['wps_attach_plans'] ) ) : '';
				if ( ! empty( $selected_plan ) ) {
					update_post_meta( $product_id, 'wps_membership_plan_with_product', $selected_plan );
				} else {
					if ( ! empty( get_post_meta( $product_id, 'wps_membership_plan_with_product', true ) ) ) {

						update_post_meta( $product_id, 'wps_membership_plan_with_product', '' );
					}
				}
			}
		}
	}



	/**
	 * Members billing metabox save.
	 *
	 * @return void
	 */
	public function wps_membership_save_member_status() {

		// Nonce verification.
		check_ajax_referer( 'members-nonce', 'nonce' );

		// Saving member actions metabox fields.
		$actions = array(
			'member_status'  => ! empty( $_POST['member_status'] ) ? sanitize_text_field( wp_unslash( $_POST['member_status'] ) ) : '',
			'member_actions' => ! empty( $_POST['member_actions'] ) ? sanitize_text_field( wp_unslash( $_POST['member_actions'] ) ) : '',
		);
		$plan_id = '';

		$post_id = ! empty( $_POST['post_id'] ) ? sanitize_text_field( wp_unslash( $_POST['post_id'] ) ) : '';

			// When plans are assigned manually.
		if ( isset( $_POST['members_plan_assign'] ) ) {

			$plan_id = ! empty( $_POST['members_plan_assign'] ) ? sanitize_text_field( wp_unslash( $_POST['members_plan_assign'] ) ) : '';

			if ( ! empty( $plan_id ) ) {

				$plan_obj = get_post( $plan_id, ARRAY_A );

				$post_meta = get_post_meta( $plan_id );

				// Formatting array.
				foreach ( $post_meta as $post_meta_key => $post_meta_value ) {

					$post_meta[ $post_meta_key ] = reset( $post_meta_value );
				}

				$plan_meta = array_merge( $plan_obj, $post_meta );

				update_post_meta( $post_id, 'plan_obj', $plan_meta );
			}
		}

		$post   = get_post( $post_id );
		$current_assigned_user = '';
		$member_details = get_post_meta( $post_id, 'billing_details', true );
		$email      = ! empty( $member_details['membership_billing_email'] ) ? $member_details['membership_billing_email'] : '';
		if ( ! empty( $email ) ) {
			$user = get_user_by( 'email', $email );
			$current_assigned_user = $user->ID;
		} else {
			$current_assigned_user = ! empty( $_POST['wps_member_user'] ) ? sanitize_text_field( wp_unslash( $_POST['wps_member_user'] ) ) : '';

		}
		if ( $current_assigned_user ) {

			update_post_meta( $post_id, 'wps_member_user', $current_assigned_user );
		}

		$current_memberships = get_user_meta( $current_assigned_user, 'mfw_membership_id', true );

		$current_memberships = ! empty( $current_memberships ) ? $current_memberships : array();
		if ( ! in_array( $post_id, (array) $current_memberships ) ) {
			array_push( $current_memberships, $post_id );
		}

		if ( 'yes' == get_post_meta( $plan_id, 'wps_membership_subscription', true ) ) {
			Update_post_meta( $post_id, 'is_subscription_plan_member', 'yes' );
		} else {
			Update_post_meta( $post_id, 'is_subscription_plan_member', '' );
		}

		// Assign membership plan to user and assign 'member' role to it.
		update_user_meta( $current_assigned_user, 'mfw_membership_id', $current_memberships );

		// If manually completing membership then set its expiry date.
		if ( 'complete' == $_POST['member_status'] ) {

			// Getting current activation date.
			$current_date = gmdate( 'Y-m-d' );

			$plan_obj = get_post_meta( $post_id, 'plan_obj', true );

			// Save expiry date in post.
			if ( ! empty( $plan_obj ) ) {

				$membership_plubic = new Membership_For_Woocommerce_Public( $this->plugin_name, $this->version );
				$membership_plubic->assign_club_membership_to_member( $plan_obj['ID'], $plan_obj, $post_id );
				$access_type = get_post_meta( $plan_obj['ID'], 'wps_membership_plan_access_type', true );

				if ( 'delay_type' == $access_type ) {
					$time_duration      = get_post_meta( $plan_obj['ID'], 'wps_membership_plan_time_duration', true );
					$time_duration_type = get_post_meta( $plan_obj['ID'], 'wps_membership_plan_time_duration_type', true );
					$current_date = gmdate( 'Y-m-d', strtotime( $current_date . ' + ' . $time_duration . ' ' . $time_duration_type ) );
				}

				if ( 'lifetime' == $plan_obj['wps_membership_plan_name_access_type'] ) {

					update_post_meta( $post_id, 'member_expiry', 'Lifetime' );

				} elseif ( 'limited' == $plan_obj['wps_membership_plan_name_access_type'] ) {

					$duration = $plan_obj['wps_membership_plan_duration'] . ' ' . $plan_obj['wps_membership_plan_duration_type'];
					$today_date = gmdate( 'Y-m-d' );
					$expiry_date = strtotime( $today_date . $duration );

					update_post_meta( $post_id, 'member_expiry', $expiry_date );

					$order_id = get_post_meta( $post_id, 'member_order_id', true );
					if ( 'yes' == $plan_obj['wps_membership_subscription'] ) {
						$subscription_id = get_post_meta( $order_id, 'wps_subscription_id', true );
						if ( ! empty( $subscription_id ) ) {

							update_post_meta( $subscription_id, 'wps_subscription_status', 'active' );
							update_post_meta( $subscription_id, 'wps_next_payment_date', $expiry_date );

							if ( ! empty( $plan_obj['wps_membership_subscription_expiry'] ) ) {
								if ( function_exists( 'wps_sfw_susbcription_expiry_date' ) ) {
									$current_time = current_time( 'timestamp' );
									$wps_susbcription_end = wps_sfw_susbcription_expiry_date( $subscription_id, $current_time );
									update_post_meta( $subscription_id, 'wps_susbcription_end', $wps_susbcription_end );
								}
							} else {
								update_post_meta( $subscription_id, 'wps_susbcription_end', '' );
							}
						}
					}
				}

				$post   = get_post( $post_id );
				$user    = get_userdata( $current_assigned_user );

				$user = new WP_User( $current_assigned_user ); // create a new user object for this user.
				$expiry_date = get_post_meta( $post_id, 'member_expiry', true );
				if ( 'Lifetime' == $expiry_date ) {
					$expiry_date = 'Lifetime';
				} else {
					$expiry_date = esc_html( ! empty( $expiry_date ) ? gmdate( 'Y-m-d', $expiry_date ) : '' );
				}

				$order_id = get_post_meta( $post_id, 'member_order_id', true );

				$user_name = $user->data->display_name;
				$customer_email = WC()->mailer()->emails['membership_creation_email'];
				if ( ! empty( $customer_email ) ) {
					$email_status = $customer_email->trigger( $current_assigned_user, $plan_obj, $user_name, $expiry_date, $order_id );
				}
			}

			update_user_meta( $current_assigned_user, 'is_member', 'member' );

		} elseif ( 'cancelled' == $_POST['member_status'] ) {  // If manually cancelling membership then remove its expiry date.

			$post   = get_post( $post_id );
			$user = get_userdata( $current_assigned_user );
			$expiry_date = '';
			$plan_obj = get_post_meta( $post_id, 'plan_obj', true );
			$today_date = gmdate( 'Y-m-d' );
			// Save expiry date in post.
			if ( ! empty( $plan_obj ) ) {

				$access_type = get_post_meta( $plan_obj['ID'], 'wps_membership_plan_access_type', true );

				if ( 'delay_type' == $access_type ) {
					$time_duration      = get_post_meta( $plan_obj['ID'], 'wps_membership_plan_time_duration', true );
					$time_duration_type = get_post_meta( $plan_obj['ID'], 'wps_membership_plan_time_duration_type', true );

					$current_date = gmdate( 'Y-m-d', strtotime( $today_date . ' + ' . $time_duration . ' ' . $time_duration_type ) );

				}

				if ( 'lifetime' == $plan_obj['wps_membership_plan_name_access_type'] ) {

					update_post_meta( $post_id, 'member_expiry', 'Lifetime' );

				} elseif ( 'limited' == $plan_obj['wps_membership_plan_name_access_type'] ) {

					$duration = $plan_obj['wps_membership_plan_duration'] . ' ' . $plan_obj['wps_membership_plan_duration_type'];

					$expiry_date = strtotime( $current_date . $duration );

					if ( 'delay_type' == $access_type ) {
						$delay_duration = $time_duration . ' ' . $time_duration_type;

						$expiry_date = gmdate( strtotime( $today_date . $duration ) );
						$date_exipary = gmdate( 'Y-m-d', $expiry_date );
						$expiry_date = strtotime( $date_exipary . $delay_duration );

					}
					update_post_meta( $post_id, 'member_expiry', $expiry_date );
				}
			}

			$user_name = $user->data->display_name;
			$order_id = get_post_meta( $post_id, 'member_order_id', true );
			$customer_email = WC()->mailer()->emails['membership_cancell_email'];
			$expiry_date = get_post_meta( $post_id, 'member_expiry', true );

			if ( 'yes' == $plan_obj['wps_membership_subscription'] ) {
				$subscription_id = get_post_meta( $order_id, 'wps_subscription_id', true );
				if ( ! empty( $subscription_id ) ) {
					update_post_meta( $subscription_id, 'wps_subscription_status', 'cancelled' );

				}
			}

			if ( 'Lifetime' == $expiry_date ) {
				$expiry_date = 'Lifetime';
			} else {
				$expiry_date = esc_html( ! empty( $expiry_date ) ? gmdate( 'Y-m-d', $expiry_date ) : '' );

			}

			if ( ! empty( $customer_email ) ) {

				$email_status = $customer_email->trigger( $current_assigned_user, $plan_obj, $user_name, $expiry_date, $order_id );
			}
			update_post_meta( $post_id, 'member_expiry', '' );
			update_post_meta( $post_id, 'plan_obj', '' );

			$other_member_exists = false;
			$wps_membership_posts = get_post_field( 'post_author', $post_id );

			$memberships = get_user_meta( $wps_membership_posts, 'mfw_membership_id', true );
			foreach ( $memberships as $key => $m_id ) {

				$status = get_post_meta( $m_id, 'member_status', true );
				if ( 'complete' == $status ) {
					if ( $m_id == $post_id ) {
						$other_member_exists = false;
					} else {
						$other_member_exists = true;
					}
				}
			}

			if ( false == $other_member_exists ) {
				update_user_meta( $current_assigned_user, 'is_member', '' );
			}
		} else {

			$order_id = get_post_meta( $post_id, 'member_order_id', true );
			$expiry_date = get_post_meta( $post_id, 'member_expiry', true );
			$subscription_id = get_post_meta( $order_id, 'wps_subscription_id', true );
			if ( 'yes' == $plan_obj['wps_membership_subscription'] || ! empty( $order_id ) ) {
				$subscription_id = get_post_meta( $order_id, 'wps_subscription_id', true );
				if ( ! empty( $subscription_id ) ) {
					update_post_meta( $subscription_id, 'wps_subscription_status', sanitize_text_field( wp_unslash( $_POST['member_status'] ) ) );
					update_post_meta( $subscription_id, 'wps_next_payment_date', $expiry_date );
					if ( ! empty( $plan_obj['wps_membership_subscription_expiry'] ) ) {
						if ( function_exists( 'wps_sfw_susbcription_expiry_date' ) ) {
							$current_time = current_time( 'timestamp' );
							$wps_susbcription_end = wps_sfw_susbcription_expiry_date( $subscription_id, $current_time );
							update_post_meta( $subscription_id, 'wps_susbcription_end', $wps_susbcription_end );
						}
					} else {
						update_post_meta( $subscription_id, 'wps_susbcription_end', '' );
					}
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

		} elseif ( isset( $_POST['billing_payment'] ) ) {

			$payment = ! empty( $_POST['billing_payment'] ) ? sanitize_text_field( wp_unslash( $_POST['billing_payment'] ) ) : '';
		} else {
			$payment = ! empty( get_post_meta( $post_id, 'billing_details_payment', true ) ) ? get_post_meta( $post_id, 'billing_details_payment', true ) : '';
		}

			// phpcs:disable
		$fields = array(
			'membership_billing_first_name' => ! empty( $_POST['billing_first_name'] ) ? sanitize_text_field( wp_unslash( $_POST['billing_first_name'] ) ) : '',
			'membership_billing_last_name'  => ! empty( $_POST['billing_last_name'] ) ? sanitize_text_field( wp_unslash( $_POST['billing_last_name'] ) ) : '',
			'membership_billing_company'    => ! empty( $_POST['billing_company'] ) ? sanitize_text_field( wp_unslash( $_POST['billing_company'] ) ) : '',
			'membership_billing_address_1'  => ! empty( $_POST['billing_address_1'] ) ? sanitize_text_field( wp_unslash( $_POST['billing_address_1'] ) ) : '',
			'membership_billing_address_2'  => ! empty( $_POST['billing_address_2'] ) ? sanitize_text_field( wp_unslash( $_POST['billing_address_2'] ) ) : '',
			'membership_billing_city'       => ! empty( $_POST['billing_city'] ) ? sanitize_text_field( wp_unslash( $_POST['billing_city'] ) ) : '',
			'membership_billing_postcode'   => ! empty( $_POST['billing_postcode'] ) ? sanitize_text_field( wp_unslash( $_POST['billing_postcode'] ) ) : '',
			'membership_billing_country'    => ! empty( $_POST['billing_country'] ) ? sanitize_text_field( wp_unslash( $_POST['billing_country'] ) ) : '',
			'membership_billing_state'      => ! empty( $_POST['billing_state'] ) ? sanitize_text_field( wp_unslash( $_POST['billing_state'] ) ) : '',
			'membership_billing_email'      => ! empty( $_POST['billing_email'] ) ? sanitize_text_field( wp_unslash( $_POST['billing_email'] ) ) : '',
			'membership_billing_phone'      => ! empty( $_POST['billing_phone'] ) ? sanitize_text_field( wp_unslash( $_POST['billing_phone'] ) ) : '',
			'payment_method'                => $payment,
		);          // phpcs:enable

		update_post_meta( $post_id, 'billing_details', $fields );

			$wps_wsfw_error_text = esc_html__( 'Membership Status changed', 'membership-for-woocommerce' );
			$message             = array(
				'msg'     => $wps_wsfw_error_text,
				'msgType' => 'success',
			);
			wp_send_json( $message );

	}

	/**
	 * Create plan ajax callback.
	 *
	 * @return void
	 */
	public function wps_membership_create_plan_reg_callback() {
		check_ajax_referer( 'membership-registration-nonce', 'nonce' );
		$plan_price = isset( $_POST['plan_price'] ) ? sanitize_text_field( wp_unslash( $_POST['plan_price'] ) ) : '';
		$plan_access_type = isset( $_POST['plan_access_type'] ) ? sanitize_text_field( wp_unslash( $_POST['plan_access_type'] ) ) : '';
		$plan_title = isset( $_POST['plan_title'] ) ? sanitize_text_field( wp_unslash( $_POST['plan_title'] ) ) : '';
		$plan_duration_type = isset( $_POST['plan_duration_type'] ) ? sanitize_text_field( wp_unslash( $_POST['plan_duration_type'] ) ) : '';
		$plan_duration = isset( $_POST['plan_duration'] ) ? sanitize_text_field( wp_unslash( $_POST['plan_duration'] ) ) : '';

		if ( ! empty( $plan_price ) && ! empty( $plan_access_type ) && ! empty( $plan_title ) ) {
			$plan_id = wp_insert_post(
				array(
					'post_type'    => 'wps_cpt_membership',
					'post_title'   => $plan_title,
					'post_status'  => 'publish',

				),
				true
			);
			update_post_meta( $plan_id, 'wps_membership_plan_price', $plan_price );
			update_post_meta( $plan_id, 'wps_membership_plan_name_access_type', $plan_access_type );
			if ( 'limited' == $plan_access_type ) {
				update_post_meta( $plan_id, 'wps_membership_plan_duration', $plan_duration );
				update_post_meta( $plan_id, 'wps_membership_plan_duration_type', $plan_duration_type );
			}
		}
	}

	/**
	 * Function for registration.
	 *
	 * @return void
	 */
	public function wps_membership_search_products_for_membership_registration() {

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

		/**
		 * Filter for search products.
		 *
		 * @since 1.0.0
		 */
		$search_results = apply_filters( 'wps_membership_search_products_for_membership', $search_results );

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

				$exclude = get_post_meta( $search_results->post->ID, '_wps_membership_exclude', true );

				if ( 'yes' === $exclude ) {
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
	 * Functional for save registration form.
	 *
	 * @return void
	 */
	public function mfw_admin_save_tab_settings_reg_form() {
		$results = get_posts(
			array(
				'post_type' => 'wps_cpt_membership',
				'post_status' => 'publish',
				'numberposts' => -1,
				'fields' => 'ids',

			)
		);

		if ( isset( $_POST['wps_membership_restriction_button'] ) ) {
			$value_check = isset( $_POST['wps_nonce_name'] ) ? sanitize_text_field( wp_unslash( $_POST['wps_nonce_name'] ) ) : '';
			wp_verify_nonce( $value_check, 'wps-form-nonce' );
			foreach ( $results as $key => $value ) {
				$wps_membership_plan_target_ids = isset( $_POST[ 'wps_membership_plan_target_ids_' . $value ] ) ? map_deep( wp_unslash( $_POST[ 'wps_membership_plan_target_ids_' . $value ] ), 'sanitize_text_field' ) : array();
				$wps_membership_plan_target_categories = isset( $_POST[ 'wps_membership_plan_target_cats_' . $value ] ) ? map_deep( wp_unslash( $_POST[ 'wps_membership_plan_target_cats_' . $value ] ), 'sanitize_text_field' ) : array();
				$wps_membership_plan_target_tags = isset( $_POST[ 'wps_membership_plan_target_tags_' . $value ] ) ? map_deep( wp_unslash( $_POST[ 'wps_membership_plan_target_tags_' . $value ] ), 'sanitize_text_field' ) : array();
				if ( is_array( $wps_membership_plan_target_ids ) && ! empty( $wps_membership_plan_target_ids ) ) {

					update_post_meta( $value, 'wps_membership_plan_target_ids', $wps_membership_plan_target_ids );
				}
				if ( is_array( $wps_membership_plan_target_categories ) && ! empty( $wps_membership_plan_target_categories ) ) {

					update_post_meta( $value, 'wps_membership_plan_target_categories', $wps_membership_plan_target_categories );
				}
				if ( is_array( $wps_membership_plan_target_tags ) && ! empty( $wps_membership_plan_target_tags ) ) {

					update_post_meta( $value, 'wps_membership_plan_target_tags', $wps_membership_plan_target_tags );
				}
			}
		}

		if ( isset( $_POST['wps_add_member_button'] ) ) {
			$value_check = isset( $_POST['wps_nonce_name'] ) ? sanitize_text_field( wp_unslash( $_POST['wps_nonce_name'] ) ) : '';
			wp_verify_nonce( $value_check, 'wps-form-nonce' );
			$post_id = wp_insert_post(
				array(
					'post_type'    => 'wps_cpt_members',
					'post_status'  => 'publish',

				),
				true
			);
			update_post_meta( $post_id, 'member_status', 'complete' );
			$actions = array(
				'member_status'  => 'complete',
				'member_actions' => '',
			);
			$plan_id = '';

				// When plans are assigned manually.
			if ( isset( $_POST['members_plan_assign'] ) ) {

				$plan_id = ! empty( $_POST['members_plan_assign'] ) ? sanitize_text_field( wp_unslash( $_POST['members_plan_assign'] ) ) : '';

				if ( ! empty( $plan_id ) ) {

					$plan_obj = get_post( $plan_id, ARRAY_A );

					$post_meta = get_post_meta( $plan_id );

					// Formatting array.
					foreach ( $post_meta as $post_meta_key => $post_meta_value ) {

						$post_meta[ $post_meta_key ] = reset( $post_meta_value );
					}

					$plan_meta = array_merge( $plan_obj, $post_meta );

					update_post_meta( $post_id, 'plan_obj', $plan_meta );
				}
			}

			$post   = get_post( $post_id );
			$current_assigned_user = '';
			$member_details = get_post_meta( $post_id, 'billing_details', true );
			$email      = ! empty( $member_details['membership_billing_email'] ) ? $member_details['membership_billing_email'] : '';
			if ( ! empty( $email ) ) {
				$user = get_user_by( 'email', $email );
				$current_assigned_user = $user->ID;
			} else {
				$current_assigned_user = ! empty( $_POST['wps_member_user'] ) ? sanitize_text_field( wp_unslash( $_POST['wps_member_user'] ) ) : '';

			}
			if ( $current_assigned_user ) {

				update_post_meta( $post_id, 'wps_member_user', $current_assigned_user );
			}

			$current_memberships = get_user_meta( $current_assigned_user, 'mfw_membership_id', true );

			$current_memberships = ! empty( $current_memberships ) ? $current_memberships : array();
			if ( ! in_array( $post_id, (array) $current_memberships ) ) {
				array_push( $current_memberships, $post_id );
			}

			if ( 'yes' == get_post_meta( $plan_id, 'wps_membership_subscription', true ) ) {
				Update_post_meta( $post_id, 'is_subscription_plan_member', 'yes' );
			} else {
				Update_post_meta( $post_id, 'is_subscription_plan_member', '' );
			}

			// Assign membership plan to user and assign 'member' role to it.
			update_user_meta( $current_assigned_user, 'mfw_membership_id', $current_memberships );

			// If manually completing membership then set its expiry date.

				// Getting current activation date.
				$current_date = gmdate( 'Y-m-d' );

				$plan_obj = get_post_meta( $post_id, 'plan_obj', true );

				// Save expiry date in post.
			if ( ! empty( $plan_obj ) ) {

				$membership_plubic = new Membership_For_Woocommerce_Public( $this->plugin_name, $this->version );
				$membership_plubic->assign_club_membership_to_member( $plan_obj['ID'], $plan_obj, $post_id );
				$access_type = get_post_meta( $plan_obj['ID'], 'wps_membership_plan_access_type', true );

				if ( 'delay_type' == $access_type ) {
					$time_duration      = get_post_meta( $plan_obj['ID'], 'wps_membership_plan_time_duration', true );
					$time_duration_type = get_post_meta( $plan_obj['ID'], 'wps_membership_plan_time_duration_type', true );
					$current_date = gmdate( 'Y-m-d', strtotime( $current_date . ' + ' . $time_duration . ' ' . $time_duration_type ) );
				}

				if ( 'lifetime' == $plan_obj['wps_membership_plan_name_access_type'] ) {

					update_post_meta( $post_id, 'member_expiry', 'Lifetime' );

				} elseif ( 'limited' == $plan_obj['wps_membership_plan_name_access_type'] ) {

					$duration = $plan_obj['wps_membership_plan_duration'] . ' ' . $plan_obj['wps_membership_plan_duration_type'];
					$today_date = gmdate( 'Y-m-d' );
					$expiry_date = strtotime( $today_date . $duration );

					update_post_meta( $post_id, 'member_expiry', $expiry_date );

					$order_id = get_post_meta( $post_id, 'member_order_id', true );
					if ( array_key_exists( 'wps_membership_subscription', $plan_obj ) ) {

						if ( 'yes' == $plan_obj['wps_membership_subscription'] ) {
							$subscription_id = get_post_meta( $order_id, 'wps_subscription_id', true );
							if ( ! empty( $subscription_id ) ) {

								update_post_meta( $subscription_id, 'wps_subscription_status', 'active' );
								update_post_meta( $subscription_id, 'wps_next_payment_date', $expiry_date );

								if ( ! empty( $plan_obj['wps_membership_subscription_expiry'] ) ) {
									if ( function_exists( 'wps_sfw_susbcription_expiry_date' ) ) {
										$current_time = current_time( 'timestamp' );
										$wps_susbcription_end = wps_sfw_susbcription_expiry_date( $subscription_id, $current_time );
										update_post_meta( $subscription_id, 'wps_susbcription_end', $wps_susbcription_end );
									}
								} else {
									update_post_meta( $subscription_id, 'wps_susbcription_end', '' );
								}
							}
						}
					}
				}

				$post   = get_post( $post_id );
				$user    = get_userdata( $current_assigned_user );

				$user = new WP_User( $current_assigned_user ); // create a new user object for this user.
				$expiry_date = get_post_meta( $post_id, 'member_expiry', true );
				if ( 'Lifetime' == $expiry_date ) {
					$expiry_date = 'Lifetime';
				} else {
					$expiry_date = esc_html( ! empty( $expiry_date ) ? gmdate( 'Y-m-d', $expiry_date ) : '' );
				}

				$order_id = get_post_meta( $post_id, 'member_order_id', true );
				$user_name = '';
				if ( isset( $user->data->display_name ) ) {

					$user_name = $user->data->display_name;
				}
				$customer_email = '';
				if ( key_exists( 'membership_creation_email', WC()->mailer()->emails ) ) {

					$customer_email = WC()->mailer()->emails['membership_creation_email'];
				}
				if ( ! empty( $customer_email ) ) {
					$email_status = $customer_email->trigger( $current_assigned_user, $plan_obj, $user_name, $expiry_date, $order_id );
				}
			}

				update_user_meta( $current_assigned_user, 'is_member', 'member' );

			// }

			foreach ( $actions as $action => $value ) {

				if ( array_key_exists( $action, $_POST ) ) {

					update_post_meta( $post_id, $action, $value );
				}
			}

			// Saving member billing details metabox fields.
			if ( isset( $_POST['payment_gateway_select'] ) ) {

				$payment = ! empty( $_POST['payment_gateway_select'] ) ? sanitize_text_field( wp_unslash( $_POST['payment_gateway_select'] ) ) : '';

			} elseif ( isset( $_POST['billing_payment'] ) ) {

				$payment = ! empty( $_POST['billing_payment'] ) ? sanitize_text_field( wp_unslash( $_POST['billing_payment'] ) ) : '';
			} else {
				$payment = ! empty( get_post_meta( $post_id, 'billing_details_payment', true ) ) ? get_post_meta( $post_id, 'billing_details_payment', true ) : '';
			}

				// phpcs:disable
			$fields = array(
				'membership_billing_first_name' => ! empty( $_POST['billing_first_name'] ) ? sanitize_text_field( wp_unslash( $_POST['billing_first_name'] ) ) : '',
				'membership_billing_last_name'  => ! empty( $_POST['billing_last_name'] ) ? sanitize_text_field( wp_unslash( $_POST['billing_last_name'] ) ) : '',
				'membership_billing_company'    => ! empty( $_POST['billing_company'] ) ? sanitize_text_field( wp_unslash( $_POST['billing_company'] ) ) : '',
				'membership_billing_address_1'  => ! empty( $_POST['billing_address_1'] ) ? sanitize_text_field( wp_unslash( $_POST['billing_address_1'] ) ) : '',
				'membership_billing_address_2'  => ! empty( $_POST['billing_address_2'] ) ? sanitize_text_field( wp_unslash( $_POST['billing_address_2'] ) ) : '',
				'membership_billing_city'       => ! empty( $_POST['billing_city'] ) ? sanitize_text_field( wp_unslash( $_POST['billing_city'] ) ) : '',
				'membership_billing_postcode'   => ! empty( $_POST['billing_postcode'] ) ? sanitize_text_field( wp_unslash( $_POST['billing_postcode'] ) ) : '',
				'membership_billing_country'    => ! empty( $_POST['billing_country'] ) ? sanitize_text_field( wp_unslash( $_POST['billing_country'] ) ) : '',
				'membership_billing_state'      => ! empty( $_POST['billing_state'] ) ? sanitize_text_field( wp_unslash( $_POST['billing_state'] ) ) : '',
				'membership_billing_email'      => ! empty( $_POST['billing_email'] ) ? sanitize_text_field( wp_unslash( $_POST['billing_email'] ) ) : '',
				'membership_billing_phone'      => ! empty( $_POST['billing_phone'] ) ? sanitize_text_field( wp_unslash( $_POST['billing_phone'] ) ) : '',
				'payment_method'                => $payment,
			);          // phpcs:enable

			update_post_meta( $post_id, 'billing_details', $fields );

		}

	}

	/**
	 * Function to send msg to all members.
	 *
	 * @return void
	 */
	public function wps_mfwp_send_msg_to_all_members() {
		if ( isset( $_POST['wps-mfwp-send-to-all-members'] ) ) {
			$value_check = isset( $_POST['wps_send_msg_hidden'] ) ? sanitize_text_field( wp_unslash( $_POST['wps_send_msg_hidden'] ) ) : '';
			wp_verify_nonce( $value_check, 'wps_mfw_send_msg_nonce' );
			$members = get_posts(
				array(
					'post_type' => 'wps_cpt_members',
					'post_status' => array( 'publish', 'draft' ),
					'numberposts' => -1,

				)
			);

			if ( ! empty( $members ) ) {
				foreach ( $members as $index => $values ) {

					$status  = get_post_meta( $values->ID, 'member_status', true );

					if ( 'complete' === $status ) {

						$user_id = get_post_meta( $values->ID, 'wps_member_user', true );
						$user = get_userdata( $user_id );

						$user_email = $user->user_email;

						// For simplicity, lets assume that user has typed their first and last name when they sign up.
						$user_full_name = $user->user_firstname . ' ' . $user->user_lastname;
						$to = $user_email;
						$subject = 'Important message for you!';
						 $body = isset( $_POST['wps-mfwp-msg-body'] ) ? sanitize_text_field( wp_unslash( $_POST['wps-mfwp-msg-body'] ) ) : '';

						$headers = array( 'Content-Type: text/html; charset=UTF-8' );
						$headers = 'From: ' . get_option( 'admin_email' ) . "\r\n";

						if ( wp_mail( $to, $subject, $body, $headers ) ) {
							error_log( 'email has been successfully sent to user whose email is ' . $user_email );

						} else {
							error_log( 'email failed to sent to user whose email is ' . $user_email );
						}
					}
				}
			}
		}
	}



}
