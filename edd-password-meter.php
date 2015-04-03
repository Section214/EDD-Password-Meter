<?php
/**
 * Plugin Name:     Easy Digital Downloads - Password Meter
 * Plugin URI:      https://easydigitaldownloads.com/extension/password-meter/
 * Description:     Adds a password strength meter to the integrated signup form
 * Version:         1.1.2
 * Author:          Daniel J Griffiths
 * Author URI:      http://section214.com
 * Text Domain:     edd-password-meter
 *
 * @package         EDD\PasswordMeter
 * @author          Daniel J Griffiths <dgriffiths@section214.com>
 * @copyright       Copyright (c) 2013-2014, Daniel J Griffiths
 */

// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;


if( !class_exists( 'EDD_Password_Meter' ) ) {

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
            if( !self::$instance ) {
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
            // Plugin path
            define( 'EDD_PASSWORD_METER_DIR', plugin_dir_path( __FILE__ ) );

            // Plugin URL
            define( 'EDD_PASSWORD_METER_URL', plugin_dir_url( __FILE__ ) );

            // Plugin version
            define( 'EDD_PASSWORD_METER_VER', '1.1.2' );
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
            require_once( EDD_PASSWORD_METER_DIR . 'includes/scripts.php' );
        }


        /**
         * Run action and filter hooks
         *
         * @access      private
         * @since       1.0.2
         * @return      void
         */
        private function hooks() {
            // Register settings
            add_filter( 'edd_settings_extensions', array( $this, 'settings' ), 1 );

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
            $locale     = apply_filters( 'plugin_locale', get_locale(), '' );
            $mofile     = sprintf( '%1$s-%2$s.mo', 'edd-password-meter', $locale );

            // Setup paths to current locale file
            $mofile_local   = $lang_dir . $mofile;
            $mofile_global  = WP_LANG_DIR . '/edd-password-meter/' . $mofile;

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
         * Add settings
         *
         * @since       1.0.0
         * @access      public
         * @param       array $settings the existing EDD settings array
         * @return      array $settings the filtered EDD settings array
         */
        public function settings( $settings ) {
            $new_settings = array(
                array(
                    'id'    => 'edd_password_meter_settings',
                    'name'  => '<strong>' . __( 'Password Meter Settings', 'edd-password-meter' ) . '</strong>',
                    'desc'  => __( 'Configure Password Meter Settings', 'edd-password-meter' ),
                    'type'  => 'header'
                ),
                array(
                    'id'    => 'edd_password_meter_checkmode',
                    'name'  => __( 'Check Mode', 'edd-password-meter' ),
                    'desc'  => __( 'Specify strict or moderate password checking', 'edd-password-meter' ),
                    'type'  => 'select',
                    'options'   => array(
                        'STRICT'    => __( 'Strict', 'edd-password-meter' ),
                        'MODERATE'  => __( 'Moderate','edd-password-meter' )
                    ),
                    'std'   => 'STRICT'
                ),
                array(
                    'id'    => 'edd_password_meter_strength',
                    'name'  => __( 'Required Strength', 'edd-password-meter' ),
                    'desc'  => __( 'Set the minimum required password strength Min: 0, Max: 1, Default: 0.8', 'edd-password-meter' ),
                    'type'  => 'number',
                    'min'   => '0',
                    'max'   => '1',
                    'step'  => '0.1',
                    'size'  => 'small',
                    'std'   => '0.8'
                ),
                array(
                    'id'    => 'edd_password_meter_min_length',
                    'name'  => __( 'Minimum Length', 'edd-password-meter' ),
                    'desc'  => __( 'Set the minimum allowed password length Default: 8', 'edd-password-meter' ),
                    'type'  => 'number',
                    'min'   => '0',
                    'step'  => '1',
                    'size'  => 'small',
                    'std'   => '8'
                ),
                array(
                    'id'    => 'edd_password_meter_max_length',
                    'name'  => __( 'Maximum Length', 'edd-password-meter' ),
                    'desc'  => __( 'Set the maximum allowed password length Default: 16', 'edd-password-meter' ),
                    'type'  => 'number',
                    'min'   => '0',
                    'step'  => '1',
                    'size'  => 'small',
                    'std'   => '16'
                ),
                array(
                    'id'    => 'edd_password_meter_hide_toggle',
                    'name'  => __( 'Disallow Show Password', 'edd-password-meter' ),
                    'desc'  => __( 'Select to disable the \'show password\' toggle', 'edd-password-meter' ),
                    'type'  => 'checkbox'
                ),
                array(
                    'id'    => 'edd_password_meter_hide_gen',
                    'name'  => __( 'Disallow Password Generation', 'edd-password-meter' ),
                    'desc'  => __( 'Select to disabled the \'generate password\' button', 'edd-password-meter' ),
                    'type'  => 'checkbox'
                ),
                array(
                    'id'    => 'edd_password_meter_hide_tooltip',
                    'name'  => __( 'Hide Tooltips', 'edd-password-meter' ),
                    'desc'  => __( 'Select to hide generation guideline tooltips', 'edd-password-meter' ),
                    'type'  => 'checkbox'
                )
            );

            return array_merge( $settings, $new_settings );
        }


        /**
         * Add required field
         *
         * @access      public
         * @since       1.0.0
         * @return      array
         */
        public function error_check() {
            if( !empty( $_POST['edd_invalid_password_strength'] ) ) {
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
    if( !class_exists( 'Easy_Digital_Downloads' ) ) {
        if( !class_exists( 'S214_EDD_Activation' ) ) {
            require_once( 'includes/class.s214-edd-activation.php' );
        }

        $activation = new S214_EDD_Activation( plugin_dir_path( __FILE__ ), basename( __FILE__ ) );
        $activation = $activation->run();

        return EDD_Password_Meter::instance();
    } else {
        return EDD_Password_Meter::instance();
    }
}
add_action( 'plugins_loaded', 'EDD_Password_Meter_load' );
