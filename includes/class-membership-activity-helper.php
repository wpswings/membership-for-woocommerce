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
	private $base_dir = WP_CONTENT_DIR . '/uploads/';

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

		$this->activity   = $activity;
		$this->folder     = $folder;
		$this->sub_folder = $sub_folder;

		// Create Base Activity Directory.
		$this->working_path = $this->base_dir . $this->folder;
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
				'message' => esc_html__( 'File upload failed', 'text-domain' ),
			);

		} else {

			// Perform Upload.
			$file_tmp  = ! empty( $file['tmp_name'] ) ? $file['tmp_name'] : false;
			$file_type = ! empty( $file['type'] ) ? $file['type'] : false;

			// Getting file type here ( eg-: 'application/pdf' will return 'pdf' ).
			$file_ext = substr( strrchr( $file_type, '/' ), 1 );

			if ( ! empty( $file_type ) && ! in_array( $file_ext, $allowed_ext, true ) ) {

				return array(
					'result'  => false,
					'message' => esc_html__( 'Invalid File type', 'text-domain' ),
				);
			}

			// Move file to server.
			$location = $this->active_folder . $file['name'];
			move_uploaded_file( $file_tmp, $location );

			return array(
				'result'  => true,
				'message' => esc_html__( 'File upload successful', 'text-domain' ),
				'url'     => esc_url( $this->get_file_url( $location ) ),
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

			$log = 'Website: ' . $_SERVER['REMOTE_ADDR'] . PHP_EOL .
					'Time: ' . current_time( 'F j, Y  g:i a' ) . PHP_EOL .
					'Step: ' . $step . PHP_EOL .
					'Response: ' . wp_json_encode( $response ) . PHP_EOL .
					'----------------------------------------------------------------------------' . PHP_EOL;

			file_put_contents( $file, $log, FILE_APPEND );

			return true;
		}
	}

	/**
	 * Create pdf and upload.
	 *
	 * @param string $content   Content to show on pdf file.
	 * @param string $file_name Name of the file.
	 *
	 * @since 1.0.0
	 */
	public function create_pdf_n_upload( $content = '', $file_name = '' ) {

		$content = iconv( 'UTF-8', 'UTF-8//IGNORE', $content );

		$this->active_file = $file_name . gmdate( 'd-m-y-his' );
		$location          = $this->active_folder . $this->active_file . '.pdf';

		// TCPDF library.
		require_once MEMBERSHIP_FOR_WOOCOMMERCE_DIRPATH . 'resources/tcpdf_min/tcpdf.php';

		if ( ! class_exists( 'TCPDF' ) ) {
			return;
		}

		/**
		 *  Creating pdf using TCPDF library.
		 */
		$pdf = new TCPDF( PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false );
		$pdf->SetMargins( -1, 0, -1 );
		$pdf->setPrintHeader( false );
		$pdf->setPrintFooter( false );
		$pdf->SetFont( 'times', '', 12, '', false );
		$pdf->SetAutoPageBreak( true, PDF_MARGIN_BOTTOM );
		$pdf->AddPage();
		$pdf->writeHTMLCell( 0, 0, '', '', $html, 0, 0, 0, true, '', true );
		$pdf->lastPage();

		try {

			if ( $this->active_file ) {
				$pdf->Output( $location, 'F' );

				return esc_url( $this->get_file_url( $location ) );
			}
		} catch ( Exception $e ) {

			echo esc_html( $e->getMessage() );
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

		$base_url = content_url( 'uploads/mfw-activity-logger' );

		return str_replace( $this->working_path, $base_url, $path );
	}


}
// End of class.
