<?php
/**
 * @file Contains Menu class.
 */

namespace payro24\CF7\Admin;

use payro24\CF7\ServiceInterface;

/**
 * Class Menu
 *
 * Defines some admin menus as a sub menu for Contact Form menu
 * in the Wordpress dashboard.
 *
 * @package payro24\CF7\Admin
 */
class Menu implements ServiceInterface {

    /**
     * {@inheritdoc}
     */
    public function register() {
        add_action( 'admin_menu', array( $this, 'admin_menu' ) );
    }

    /**
     * Defines some sub menus for the Contact Form 7 menu
     * in the Wordpress dashboard.
     */
    public function admin_menu() {
        add_submenu_page( 'wpcf7',
            __( 'payro24 Configuration', 'payro24-contact-form-7' ),
            __( 'payro24 Configuration', 'payro24-contact-form-7' ),
            'wpcf7_edit_contact_forms', 'payro24_cf7_admin_table',
            array( $this, 'admin_table' ) );

        add_submenu_page( 'wpcf7',
            __( 'Transactions List', 'payro24-contact-form-7' ),
            __( 'payro24 Transactions', 'payro24-contact-form-7' ),
            'wpcf7_edit_contact_forms', 'payro24_cf7_admin_list_trans',
            array( $this, 'list_trans' ) );

    }

    /**
     * A sub menu which is responsible for payro24 gateway configuration.
     */
    public function admin_table() {
        require_once( CF7_payro24_PLUGIN_PATH . 'templates/gateway-config.php' );
    }

    /**
     * A sub menu which is responsible for showing all transactions
     * which are done by payro24 gateway.
     */
    public function list_trans() {
        require_once( CF7_payro24_PLUGIN_PATH . 'templates/list-transactions.php' );
    }
}
