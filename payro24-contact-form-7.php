<?php
/*
Plugin Name: payro24 for Contact Form 7
Description: Integrates payro24 Payment Gateway with Contact Form 7
Author: payro24
Author URI: https://payro24.ir/
Version: 2.1.2
Text Domain: payro24-contact-form-7
Domain Path: languages
*/

require_once 'vendor/autoload.php';

use payro24\CF7\Init;
use payro24\CF7\Plugin;

define( 'CF7_payro24_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );


/**
 * Load plugin textdomain.
 */
function payro24_contact_form_7_load_textdomain() {
    load_plugin_textdomain( 'payro24-contact-form-7', false, basename( dirname( __FILE__ ) ) . '/languages' );
}

add_action( 'init', 'payro24_contact_form_7_load_textdomain' );

include_once(ABSPATH . 'wp-admin/includes/plugin.php');
if ( is_plugin_active( 'contact-form-7/wp-contact-form-7.php' ) ) {
    Init::call_services();
}

function cf7_payro24_activate() {
    Plugin::activate();
}

function cf7_payro24_deactivate() {
    Plugin::deactivate();
}

add_action( 'plugins_loaded', 'cf7_payro24_update_db', 10, 0 );
function cf7_payro24_update_db() {
    $version = get_option( 'payro24_cf7_version', '1.0' );
    if ( version_compare( $version, '2.1.1' ) < 0 ) {
        Plugin::update();
    }

    if(isset($_GET['cf7_payro24'])){
        if($_GET['cf7_payro24'] == 'callback') {
            require_once( dirname(__FILE__) . '/includes/Callback.php' );
        }
    }
}

$plugin = new payro24\CF7\Plugin();
register_activation_hook( __FILE__, 'cf7_payro24_activate' );
register_deactivation_hook( __FILE__, 'cf7_payro24_deactivate' );
