<?php
/**
 * The helper class for file uploads.
 *
 * @link       https://makewebbetter.com
 * @since      1.0.0
 *
 * @package    Membership_For_Woocommerce
 * @subpackage Membership_For_Woocommerce/admin
 */

/**
 * The helper class for file uploads..
 *
 * @package    Membership_For_Woocommerce
 * @subpackage Membership_For_Woocommerce/admin
 * @author     Make Web Better <plugins@makewebbetter.com>
 */
class Membership_Activity_Helper {

	/**
	 * The Activity of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $activity;
	private $base_dir =  WP_CONTENT_DIR . '/uploads/';
	private $working_dir;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $folder;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $sub_folder;


	/**
	 * Constructor function.
	 */
	public function __construct( $activity='logger', $folder='mfw-activity-logger', $sub_folder=array() ) {
		
		$this->activity = $activity;
		$this->folder = $folder;
		$this->sub_folder = $sub_folder;

		switch ( $this->activity  ) {
			case 'uploads':
				# For Uploads only...
				$this->working_path = $this->$base_dir . $folder;
				break;
			
			default:
				# For Logger only...
				$this->working_path = $this->$base_dir . $folder;
				break;
		}
	}

	public function create_log() {

	}
}