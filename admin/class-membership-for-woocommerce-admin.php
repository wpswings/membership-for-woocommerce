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

		if ( isset( $screen->id ) ) {

			$pagescreen = $screen->id;

			if ( 'toplevel_page_membership-for-woocommerce-setting' == $pagescreen ) {

				wp_enqueue_style( 'mwb_membershi_for_woo_admin_style', plugin_dir_url( __FILE__ ) . 'css/membership-for-woocommerce-admin.css', array(), $this->version, 'all' );

				wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/select2.min.css', array(), $this->version, 'all' );
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

		if ( isset( $screen->id ) ) {

			$pagescreen = $screen->id;

			if ( 'toplevel_page_membership-for-woocommerce-setting' == $pagescreen ) {

				wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/select2.min.js', array( 'jquery' ), $this->version, false );

				wp_enqueue_script( 'membership-for-woocommerce-admin', plugin_dir_url( __FILE__ ) . 'js/membership-for-woocommerce-admin.js', array( 'jquery' ), $this->version, false );

				wp_enqueue_script( 'mwb_membership_for_woo_add_new_plan_script', plugin_dir_url( __FILE__ ) . 'js/mwb_membership_for_woo_add_new_plan_script.js', array( 'woocommerce_admin', 'wc-enhanced-select' ), $this->version, false );

				wp_localize_script( 'mwb_membership_for_woo_add_new_plan_script', 'ajax_url', admin_url( 'admin-ajax.php' ) );

				wp_enqueue_script( 'wp-color-picker' );
			}
		}	

	}

	/**
	 * Adding Membership menu page.
	 *
	 * @since 1.0.0
	 */
	public function mwb_memberships_for_woo_admin_menu() {

		add_menu_page(
			esc_html__( 'Membership', 'membership-for-woocommerce' ),
			esc_html__( 'Membership', 'membership-for-woocommerce' ),
			'manage_woocommerce',
			'membership-for-woocommerce-setting',
			array( $this, 'mwb_membership_for_woo_add_backend' ),
			'dashicons-businessperson',
			57,
		);

		// Add submenu for membership settings.
		add_submenu_page( 'membership-for-woocommerce-setting', esc_html__( 'Membership Settings', 'membership-for-woocommerce' ), esc_html__( 'Membership Settings', 'membership-for-woocommerce' ), 'manage_options', 'membership-for-woocommerce-setting' );

		// Add submenu for members list.
		add_submenu_page( 'membership-for-woocommerce-setting', esc_html__( 'Members', 'membership-for-woocommerce' ), esc_html__( 'Members', 'membership-for-woocommerce' ), 'manage_options', 'edit.php?post_type=cpt_members' );
	}

	/**
	 * Callback function for Membership menu page.
	 */
	public function mwb_membership_for_woo_add_backend() {

		require_once plugin_dir_path( __FILE__ ) . '/partials/membership-for-woocommerce-admin-display.php';

	}

	// /**
	//  * Callback funtion for submenu members page.
	//  */
	// public function add_submenu_page_members_callback() {

	// 	require_once MEMBERSHIP_FOR_WOOCOMMERCE_DIRPATH . 'admin/members/membership-for-woocommerce-members.php';

	// }

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
			'cpt_members',
			array(
				'labels'              => $labels,
				'public'              => true,
				'has_archive'         => true,
				'publicly_queryable'  => true,
				'query_var'           => true,
				'capability_type'     => 'post',
				'hierarchical'        => false,
				'show_in_admin_bar'   => true,
				'show_in_menu'        => false,
				'menu_icon'           => 'dashicons-book-alt',
				'description'         => __( 'Displays the list of all members.', 'membership-for-woocommerce' ),
				'supports'            => array(
					'title',
					'comments',
					'editor',
					'excerpt',
					'thumbnail',
					'revisions',
					'author',
				),
				'exclude_from_search' => false,
				'rewrite'             => array(
					'slug' => __( 'members', 'members-for-woocommerce' ),
				),
			)
		);
	}

	/**
	 * Display Submenu "Members" as active when working with CPT.
	 */
	public function mwb_membership_for_woo_submenu_active() {

		global $parent_file, $post_type;

		if ( ! empty( $post_type ) && 'cpt_members' == $post_type ) {

			$parent_file = 'membership-for-woocommerce-setting';

		}

	}

	/**
	 * Adding custom columns to the custom post types.
	 *
	 * @param array $columns is an array of deafult columns in custom post type.
	 */
	public function mwb_membership_for_woo_cpt_columns( $columns ) {

		// Removing author and comments column.
		unset(
			$columns['wpseo-score'],
			$columns['wpseo-title'],
			$columns['wpseo-metadesc'],
			$columns['wpseo-focuskw'],
		);

		// Adding new columns.
		$columns = array(
			'cb'                => '<input type="checkbox" />',
			'membership_id'     => __( 'Membership ID', 'membership-for-woocommerce' ),
			'membership_status' => __( 'Membership Status', 'membership-for-woocommerce' ),
			'membership_user'   => __( 'User', 'membership-for-woocommerce' ),
			'expiration'        => __( 'Expiry Date', 'membership-for-woocommerce' ),
		);

		return $columns;

	}

	/**
	 * Populating custom columns with content.
	 *
	 * @param array   $column is an array of default columns in Custom post type.
	 * @param integer $post_id is the post id.
	 */
	public function mwb_membership_for_woo_fill_columns( $column, $post_id ) {

		switch ( $column ) {

			case 'membership_id':
				echo get_the_title( $post_id );
				break;

			case 'membership_status':
				echo 'status';
				break;

			case 'membership_user':
				$author_id = get_post_field( 'post_author', $post_id );
				$author_name = get_the_author_meta( 'user_nicename', $author_id );
				echo $author_name;
				break;

			case 'expiration':
				echo 'expiry date';
				break;
		}
	}


}
