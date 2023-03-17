<?php
/**
 * The helper class for file uploads.
 *
 * @link       https://wpswings.com
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
 */
class Membership_Activity_Helper {

	/**
	 * Working path prefix( Predefined Server properties ).
	 *
	 * @var string
	 */
	private $prefix = 'mfw-';

	/**
	 * Working dircetory path( Predefined Server properties ).
	 *
	 * @var string
	 */
	private $working_path = '';

	/**
	 * Base dirctory path( Predefined Server properties ).
	 *
	 * @var string
	 */

	private $base_dir;

	/**
	 * Base url( Predefined Server properties ).
	 *
	 * @var string
	 */
	private $base_url;

	/**
	 * Activity ( Running Activity properties ).
	 *
	 * @var string
	 */
	private $activity;

	/**
	 * Active file in the working directory ( Running Activity properties ).
	 *
	 * @var string
	 */
	public $active_file;

	/**
	 * Active forlder in the working directory ( Running Activity properties ).
	 *
	 * @var string
	 */
	public $active_folder;

	/**
	 * Activity folder( Data holding properties ).
	 *
	 * @var string
	 */
	private $folder;

	/**
	 * Activity sub-folder( Data holding properties ).
	 *
	 * @var string.
	 */
	private $sub_folder;

	/**
	 * Constructor function.
	 *
	 * @param bool   $sub_folder Activity sub-folder.
	 * @param string $activity Decides the activity.
	 * @param string $folder Activity folder name.
	 *
	 * @since 1.0.0
	 */
	public function __construct( $sub_folder = false, $activity = 'logger', $folder = 'mfw-activity-logger' ) {

		$random_value = get_option( 'wps_base_url_for_csv_upload' );
		if ( empty( $random_value ) ) {
			$random_value = time();
			$folder = $folder . $random_value;
			update_option( 'wps_base_url_for_csv_upload', $folder );
		} else {
			$folder = $random_value;
		}
		$random_value_subfolder = get_option( 'wps_base_url_for_csv_upload_subfolder' );
		if ( empty( $random_value_subfolder ) ) {
			$random_value_subfolder = time();
			$sub_folder = $sub_folder . $random_value_subfolder;
			update_option( 'wps_base_url_for_csv_upload_subfolder', $sub_folder );
		} else {
			$sub_folder = $random_value_subfolder;
		}
		$this->activity   = $activity;
		$this->folder     = $folder;
		$this->sub_folder = $sub_folder;
		$uploads = wp_upload_dir();

		$base_dir = $uploads['basedir'];
		// Create Base Activity Directory.
		$this->working_path = $base_dir . '/' . $this->folder;
		$this->check_and_create_folder( $this->working_path );

		// Create Activity Sub-Directory.
		if ( ! empty( $this->sub_folder ) ) {

			// Create a subfolder and daily basis log file.
			$log_folder = $this->working_path . '/' . $this->sub_folder;
			$this->check_and_create_folder( $log_folder );
		}

		if ( ! empty( $this->active_folder ) ) {

			switch ( $this->activity ) {

				case 'logger':
					// For Logger only...
					$log_file = $this->prefix . $this->sub_folder;
					$this->check_and_create_file( $log_file );
					break;

				default:
					break;

			}
		}
	}

	/**
	 * Function to perform upload.
	 *
	 * @param string $file        File to upload.
	 * @param array  $allowed_ext Allowed file extension.
	 *
	 * @since 1.0.0
	 */
	public function do_upload( $file = '', $allowed_ext = array() ) {

		// Not on logging activity.
		if ( 'uploads' !== $this->activity || empty( $file ) ) {

			return false;
		}

		if ( ! empty( $file['error'] ) ) {

			return array(
				'result'  => false,
				'message' => esc_html__( 'File upload failed', 'membership-for-woocommerce' ),
			);

		} else {

			// Perform Upload.
			$file_tmp  = ! empty( $file['tmp_name'] ) ? $file['tmp_name'] : false;
			$file_type = ! empty( $file['type'] ) ? $file['type'] : false;
			$file_name = isset( $file['name'] ) ? sanitize_text_field( wp_unslash( $file['name'] ) ) : '';
			$file_security = pathinfo( $file_name, PATHINFO_EXTENSION );

			// Getting file type here ( eg-: 'application/pdf' will return 'pdf' ).
			$file_ext = substr( strrchr( $file_type, '/' ), 1 );

			if ( empty( $file_security ) || 'csv' != $file_security ) {

				return array(
					'result'  => false,
					'message' => esc_html__( 'Invalid File type', 'membership-for-woocommerce' ),
				);
			}

			// Move file to server.
			$location = $this->active_folder . time() . '-' . $file['name'];
			move_uploaded_file( $file_tmp, $location );

			return array(
				'result'  => true,
				'message' => esc_html__( 'File upload successful', 'membership-for-woocommerce' ),
				'url'     => esc_url( $this->get_file_url( $location ) ),
				'path'    => $location,
			);
		}
	}

	/**
	 * Function to create log.
	 *
	 * @param string $step     Current activity.
	 * @param array  $response Response to output as log.
	 *
	 * @since 1.0.0
	 */
	public function create_log( $step = '', $response = array() ) {

		// Not on logging activity.
		if ( 'logger' !== $this->activity ) {

			return false;
		}

		$file = $this->active_file;
		if ( file_exists( $file ) && is_writable( $file ) ) {
			$server_add = isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '';

			//phpcs:disable
			$log = 'Website: ' . $server_add . PHP_EOL . // phpcs:ignore
					'Time: ' . current_time( 'F j, Y  g:i a' ) . PHP_EOL .
					'Step: ' . $step . PHP_EOL .
					'Response: ' . wp_json_encode( $response ) . PHP_EOL .
					'----------------------------------------------------------------------------' . PHP_EOL;
			//phpcs:enable
			global $wp_filesystem;  // global object of WordPress filesystem.
			require_once ABSPATH . '/wp-admin/includes/file.php';
			WP_Filesystem(); // intialise new file system object.
			$wp_filesystem->put_contents( $file, $log, false );

			return true;
		}
	}

	/**
	 * Library functions :: Check/create folder.
	 *
	 * @param string $path Path to create folder.
	 *
	 * @since 1.0.0
	 */
	public function check_and_create_folder( $path = '' ) {

		if ( ! empty( $path ) && ! is_dir( $path ) ) {

			mkdir( $path, 0755, true );
		}
		$_temp = get_option( 'index_file_created', 'not done' );
		if ( 'not done' == $_temp ) {

			fopen( $path . '/index.php', 'wb' );
		}
		// Mark the current active file.
		$this->active_folder = $path . '/';
	}

	/**
	 * Library functions :: Check/create file.
	 *
	 * @param string $_file File to create.
	 *
	 * @since 1.0.0
	 */
	public function check_and_create_file( $_file = '' ) {

		if ( empty( $_file ) ) {

			return;
		}

		$file_path = $this->active_folder . $_file . '-' . gmdate( 'd-m-Y' );

		if ( ! file_exists( $file_path ) || ! is_writable( $file_path ) ) {

			@fopen( $file_path, 'a' );
		}

		// Mark the current active file.
		$this->active_file = $file_path;
	}

	/**
	 * Library functions :: Convert path into url.
	 *
	 * @param string $path Path to covert into url.
	 *
	 * @since 1.0.0
	 */
	public function get_file_url( $path = '' ) {
		$folder = get_option( 'wps_base_url_for_csv_upload' );
		$uploads = wp_upload_dir();
		$base_url = $uploads['baseurl'] . '/' . $folder;

		return str_replace( $this->working_path, $base_url, $path );
	}


}
// End of class.
