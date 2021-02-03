<?php
/**
 * @file Contains Plugin class.
 */

namespace payro24\CF7;

/**
 * Class Plugin
 * Defines some common actions such as activating and deactivating a plugin.
 *
 * @package payro24\CF7
 */
class Plugin {

    /**
     * This is triggered when the plugin is going to be activated.
     *
     * Creates a table in database which stores all transactions.
     *
     * Also defines a variable in the 'wp-config.php' file so that
     * any contact form does not load javascript files in order to disabling
     * ajax capability of those form. This is happened so that we can redirect
     * to the gateway for processing a payment. => define('WPCF7_LOAD_JS',
     * false);
     */
    public static function activate() {
        global $wpdb;
        $table_name = $wpdb->prefix . "cf7_transactions";
        $version = get_option( 'payro24_cf7_version', '1.0' );

        if ( $wpdb->get_var( "show tables like '$table_name'" ) != $table_name ) {
            $sql = "CREATE TABLE $table_name (
                id mediumint(11) NOT NULL AUTO_INCREMENT,
                form_id bigint(11) DEFAULT '0' NOT NULL,
                trans_id VARCHAR(255) NOT NULL,
                track_id VARCHAR(255) NULL,
                gateway VARCHAR(255) NOT NULL,
                amount bigint(11) DEFAULT '0' NOT NULL,
                phone VARCHAR(11) NULL,
                description VARCHAR(255) NOT NULL,
                email VARCHAR(255) NULL,
                created_at bigint(11) DEFAULT '0' NOT NULL,
                status VARCHAR(255) NOT NULL,
                PRIMARY KEY id (id)
            );";
            require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
            dbDelta( $sql );
        }

        if ( file_exists( ABSPATH . "wp-config.php" ) && is_writable( ABSPATH . "wp-config.php" ) ) {
            self::wp_config_put();
        } else if ( file_exists( dirname( ABSPATH ) . "/wp-config.php" ) && is_writable( dirname( ABSPATH ) . "/wp-config.php" ) ) {
            self::wp_config_put( '/' );
        } else {
            ?>
            <div class="error">
                <p><?php _e( 'wp-config.php is not writable, please make wp-config.php writable - set it to 0777 temporarily, then set back to its original setting after this plugin has been activated.', 'payro24-contact-form-7' ); ?></p>
            </div>
            <?php
            exit;
        }

        $payro24_cf7_options = array(
            'api_key' => '',
            'return' => '',
            'sandbox' => '1',
            'currency' => 'rial',
            'success_message' => __( 'Your payment has been successfully completed. Tracking code: {track_id}', 'payro24-contact-form-7' ),
            'failed_message' => __( 'Your payment has failed. Please try again or contact the site administrator in case of a problem.', 'payro24-contact-form-7' ),
        );

        add_option( "payro24_cf7_options", $payro24_cf7_options );
    }

    /**
     * This is triggered when the plugin is going to be deactivated.
     */
    public static function deactivate() {

        function wp_config_delete( $slash = '' ) {
            $config = file_get_contents( ABSPATH . "wp-config.php" );
            $config = preg_replace( "/( ?)(define)( ?)(\()( ?)(['\"])WPCF7_LOAD_JS(['\"])( ?)(,)( ?)(0|1|true|false)( ?)(\))( ?);/i", "", $config );
            file_put_contents( ABSPATH . $slash . "wp-config.php", $config );
        }

        function return_error() {
            ob_start();
            ?>
            <div class="error">
                <p><?php _e( 'wp-config.php is not writable, please make wp-config.php writable - set it to 0777 temporarily, then set back to its original setting after this plugin has been deactivated.', 'payro24-contact-form-7' ); ?></p>
            </div>
            <button onclick="goBack()">Go Back and try again</button>
            <script>
                function goBack() {
                    window.history.back();
                }
            </script>
            <?php
            return ob_get_clean();
        }

        if ( file_exists( ABSPATH . "wp-config.php" ) && is_writable( ABSPATH . "wp-config.php" ) ) {
            wp_config_delete();
        } else if ( file_exists( dirname( ABSPATH ) . "/wp-config.php" ) && is_writable( dirname( ABSPATH ) . "/wp-config.php" ) ) {
            wp_config_delete( '/' );
        } else {
            print return_error();
            exit;
        }

        delete_option( "payro24_cf7_options" );
        delete_option( "payro24_cf7_version" );
    }

    public static function update() {
        global $wpdb;
        $table_name = $wpdb->prefix . "cf7_transactions";
        $version = get_option( 'payro24_cf7_version', '1.0' );

        if ( version_compare( $version, '2.1.1' ) < 0 ) {
            $collate = '';

            if ( $wpdb->has_cap( 'collation' ) ) {
                if ( ! empty($wpdb->charset ) ) {
                    $collate .= "DEFAULT CHARACTER SET utf8";
                }
                if ( ! empty($wpdb->collate ) ) {
                    $collate .= " COLLATE $wpdb->collate";
                }
            }
            $sql = "CREATE TABLE $table_name (
                id mediumint(11) NOT NULL AUTO_INCREMENT,
                form_id bigint(11) DEFAULT '0' NOT NULL,
                trans_id VARCHAR(255) NOT NULL,
                track_id VARCHAR(255) NULL,
                gateway VARCHAR(255) NOT NULL,
                amount bigint(11) DEFAULT '0' NOT NULL,
                phone VARCHAR(11) NULL,
                description VARCHAR(255) NOT NULL,
                email VARCHAR(255) NULL,
                created_at bigint(11) DEFAULT '0' NOT NULL,
                status VARCHAR(255) NOT NULL,
                log LONGTEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL,
                PRIMARY KEY id (id)
            ) $collate;";
            require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
            dbDelta( $sql );

            //update options
            $options = get_option( 'payro24_cf7_options' );
            if(empty($options['currency'])){
                $options['currency'] = 'rial';
                update_option( "payro24_cf7_options", $options );
            }
            update_option( 'payro24_cf7_version', '2.1.3' );

            //handle the mistake from version 2.1.0
            if ( file_exists( ABSPATH . "wp-config.php" ) && is_writable( ABSPATH . "wp-config.php" ) ) {
                self::wp_config_put();
            } else if ( file_exists( dirname( ABSPATH ) . "/wp-config.php" ) && is_writable( dirname( ABSPATH ) . "/wp-config.php" ) ) {
                self::wp_config_put( '/' );
            } else {
                ?>
                <div class="error">
                    <p><?php _e( 'wp-config.php is not writable, please make wp-config.php writable - set it to 0777 temporarily, then set back to its original setting after this plugin has been activated.', 'payro24-contact-form-7' ); ?></p>
                </div>
                <?php
                exit;
            }

            //update all the previous tags to new one we defined
            $rows = $wpdb->get_results( "SELECT * FROM " . $wpdb->prefix . "postmeta WHERE meta_key='_form'" );
            if ( ! empty( $rows ) ) {
                foreach ($rows as $row) {
                    $meta_value = preg_replace('/(\[(text))(  *)(payro24_amount){1}(?!\-)(?!\_)(?![A-Za-z_0-9])/', '[payment payro24_amount', $row->meta_value);
                    $meta_value = preg_replace('/(\[(text\*))(  *)(payro24_amount){1}(?!\-)(?!\_)(?![A-Za-z_0-9])/', '[payment* payro24_amount', $meta_value);
                    $wpdb->update( $wpdb->prefix . 'postmeta',
                        array( 'meta_value' => $meta_value ),
                        array( 'meta_id' => $row->meta_id ),
                        array( '%s' ),
                        array( '%d' )
                    );
                }
            }
        }
    }

    public static function wp_config_put( $slash = '' ){
        $config = file_get_contents( ABSPATH . "wp-config.php" );
        $config = preg_replace( "/( ?)(define)( ?)(\()( ?)(['\"])WPCF7_LOAD_JS(['\"])( ?)(,)( ?)(0|1|true|false)( ?)(\))( ?);/i", "", $config );
        $config = preg_replace( "/^([\r\n\t ]*)(\<\?)(php)?/i", "<?php define('WPCF7_LOAD_JS', false);", $config );
        file_put_contents( ABSPATH . $slash . "wp-config.php", $config );
    }
}
