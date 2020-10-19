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

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/membership-for-woocommerce-admin.css', array(), $this->version, 'all' );

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

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/membership-for-woocommerce-admin.js', array( 'jquery' ), $this->version, false );

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
				//'show_in_rest'        => true,
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

	

}
