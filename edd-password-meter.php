<?php
/**
 * Plugin Name:     Easy Digital Downloads - Password Meter
 * Plugin URI:      https://easydigitaldownloads.com/extension/password-meter/
 * Description:     Adds a password strength meter to the integrated signup form
 * Version:         1.2.1
 * Author:          Sandhills Development, LLC
 * Author URI:      https://sandhillsdev.com
 * Text Domain:     edd-password-meter
 *
 * @package         EDD\PasswordMeter
 * @author          Daniel J Griffiths <dgriffiths@section214.com>
 * @copyright       Copyright (c) 2013-2014, Daniel J Griffiths
 */


// Exit if accessed directly
if( ! defined( 'ABSPATH' ) ) {
	exit;
}


if( ! class_exists( 'EDD_Password_Meter' ) ) {


	/**
	 * Main EDD_Password_Meter class
	 *
	 * @since       1.0.0
	 */
	class EDD_Password_Meter {


		/**
		 * @var         EDD_Password_Meter $instance The one true EDD_Password_Meter
		 * @since       1.0.0
		 */
		private static $instance;


		/**
		 * Get active instance
		 *
		 * @access      public
		 * @since       1.0.2
		 * @return      object self::$instance The one true EDD_Password_Meter
		 */
		public static function instance() {
			if( ! self::$instance ) {
				self::$instance = new EDD_Password_Meter();
				self::$instance->setup_constants();
				self::$instance->includes();
				self::$instance->load_textdomain();
				self::$instance->hooks();
			}

			return self::$instance;
		}


		/**
		 * Setup plugin constants
		 *
		 * @access      public
		 * @since       1.0.2
		 * @return      void
		 */
		public function setup_constants() {
			// Plugin version
			define( 'EDD_PASSWORD_METER_VER', '1.2.1' );

			// Plugin path
			define( 'EDD_PASSWORD_METER_DIR', plugin_dir_path( __FILE__ ) );

			// Plugin URL
			define( 'EDD_PASSWORD_METER_URL', plugin_dir_url( __FILE__ ) );
		}


		/**
		 * Include necessary files
		 *
		 * @access      private
		 * @since       1.1.0
		 * @return      void
		 */
		private function includes() {
			// Include scripts
			require_once EDD_PASSWORD_METER_DIR . 'includes/scripts.php';

			if( is_admin() ) {
				require_once EDD_PASSWORD_METER_DIR . 'includes/admin/settings/register.php';
			}
		}


		/**
		 * Run action and filter hooks
		 *
		 * @access      private
		 * @since       1.0.2
		 * @return      void
		 */
		private function hooks() {
			// Handle licensing
			if( class_exists( 'EDD_License' ) ) {
			    $license = new EDD_License( __FILE__, 'Password Meter', EDD_PASSWORD_METER_VER, 'Daniel J Griffiths' );
			}

			// Add error check
			add_action( 'edd_checkout_error_checks', array( $this, 'error_check' ) );
		}


		/**
		 * Internationalization
		 *
		 * @access      public
		 * @since       1.0.2
		 * @return      void
		 */
		public static function load_textdomain() {
			// Set filter for languages directory
			$lang_dir = dirname( plugin_basename( __FILE__ ) ) . '/languages/';
			$lang_dir = apply_filters( 'EDD_Password_Meter_languages_directory', $lang_dir );

			// Traditional WordPress plugin locale filter
			$locale = apply_filters( 'plugin_locale', get_locale(), '' );
			$mofile = sprintf( '%1$s-%2$s.mo', 'edd-password-meter', $locale );

			// Setup paths to current locale file
			$mofile_local  = $lang_dir . $mofile;
			$mofile_global = WP_LANG_DIR . '/edd-password-meter/' . $mofile;

			if( file_exists( $mofile_global ) ) {
				// Look in global /wp-content/languages/edd-password-meter/ folder
				load_textdomain( 'edd-password-meter', $mofile_global );
			} elseif( file_exists( $mofile_local ) ) {
				// Look in local /wp-content/plugins/edd-password-meter/languages/ folder
				load_textdomain( 'edd-password-meter', $mofile_local );
			} else {
				// Load the default language files
				load_plugin_textdomain( 'edd-password-meter', false, $lang_dir );
			}
        }


		/**
		 * Add required field
		 *
		 * @access      public
		 * @since       1.0.0
		 * @return      array
		 */
		public function error_check() {
			if( ! empty( $_POST['edd_invalid_password_strength'] ) ) {
				edd_set_error( 'edd_invalid_password_strength', __( 'Your password is insecure. Please enter a stronger password.', 'edd-password-meter' ) );
			}
		}
	}
}


/**
 * The main function responsible for returning the one true EDD_Password_Meter
 * instance to functions everywhere
 *
 * @since       1.0.0
 * @return      EDD_Password_Meter The one true EDD_Password_Meter
 */
function EDD_Password_Meter_load() {
	if( ! class_exists( 'Easy_Digital_Downloads' ) ) {
		if( ! class_exists( 'S214_EDD_Activation' ) ) {
			require_once 'includes/libraries/class.s214-edd-activation.php';
		}

		$activation = new S214_EDD_Activation( plugin_dir_path( __FILE__ ), basename( __FILE__ ) );
		$activation = $activation->run();

		return EDD_Password_Meter::instance();
	} else {
		return EDD_Password_Meter::instance();
	}
}
add_action( 'plugins_loaded', 'EDD_Password_Meter_load' );
