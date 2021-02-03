<?php
/**
 * @file Contains Payment class.
 */

namespace payro24\CF7\Payment;

use payro24\CF7\ServiceInterface;

/**
 * Class Payment
 *
 * This class defines a method which will be hooked into an event when
 * a contact form is going to be submitted.
 * In that method we want to redirect to payro24 payment gateway if everything is
 * ok.
 *
 * @package payro24\CF7\Payment
 */
class Payment implements ServiceInterface {

	/**
	 * {@inheritdoc}
	 */
	public function register() {
		add_action( 'wpcf7_mail_sent', array( $this, 'after_send_mail' ) );
	}

	/** Hooks into 'wpcf7_mail_sent'.
	 *
	 * @param $cf7
	 *   the contact form's data which is submitted.
	 */
	public function after_send_mail( $cf7 ) {
		global $wpdb;
		global $postid;
		$postid = $cf7->id();

		$enable = get_post_meta( $postid, "_payro24_cf7_enable", TRUE );
		if ( $enable != "1" ){
			return;
		}

		$wpcf7       = \WPCF7_ContactForm::get_current();
		$submission  = \WPCF7_Submission::get_instance();

		$phone       = '';
		$description = '';
		$amount      = '';
		$email       = '';
		$name        = '';

		if ( $submission ) {
			$data        = $submission->get_posted_data();
			$phone       = isset( $data['payro24_phone'] ) ? $data['payro24_phone'] : "";
			$description = isset( $data['payro24_description'] ) ? $data['payro24_description'] : "";
			$amount      = isset( $data['payro24_amount'] ) ? $data['payro24_amount'] : "";
			$email       = isset( $data['your-email'] ) ? $data['your-email'] : "";
			$name        = isset( $data['your-name'] ) ? $data['your-name'] : "";
		}

		$predefined_amount = get_post_meta( $postid, "_payro24_cf7_amount", TRUE );
		if ( $predefined_amount !== "" ) {
			$amount = $predefined_amount;
		}

		$options = get_option( 'payro24_cf7_options' );
		foreach ( $options as $k => $v ) {
			$value[ $k ] = $v;
		}
		$active_gateway = 'payro24';
		$url_return     = get_home_url()."?cf7_payro24=callback";

		$row                = array();
		$row['form_id']     = $postid;
		$row['trans_id']    = '';
		$row['gateway']     = $active_gateway;
		$row['amount']      = $value['currency'] == 'rial' ? $amount : $amount * 10;
		$row['amount']      = $value['currency'] == 'rial' ? $amount : $amount * 10;
		$row['phone']       = $phone;
		$row['description'] = $description;
		$row['email']       = $email;
		$row['created_at']  = time();
		$row['status']      = 'pending';
		$row['log']         = '';
		$row_format         = array(
			'%d',
			'%s',
			'%s',
			'%d',
			'%d',
			'%s',
			'%s',
			'%s',
			'%s',
			"%s",
		);

		$api_key = $value['api_key'];
		$sandbox = $value['sandbox'] == 1 ? 'true' : 'false';
		$amount  = intval( $amount );
		$desc    = $description;

        if ( empty( $api_key ) ) {
            wp_redirect( add_query_arg( 'payro24_error', __( 'payro24 should be configured properly', 'payro24-contact-form-7' )) );
            exit;
        }

		if ( empty( $amount ) ) {
            wp_redirect( add_query_arg( 'payro24_error', __( 'Amount can not be empty', 'payro24-contact-form-7' )) );
            exit;
		}

		$data    = array(
			'order_id' => time(),
			'amount'   => $value['currency'] == 'rial' ? $amount : $amount * 10,
			'name'     => $name,
			'phone'    => $phone,
			'mail'     => $email,
			'desc'     => $desc,
			'callback' => $url_return,
		);
		$headers = array(
			'Content-Type' => 'application/json',
			'P-TOKEN'    => $api_key,
			'P-SANDBOX'    => $sandbox,
		);
		$args    = array(
			'body'    => json_encode( $data ),
			'headers' => $headers,
			'timeout' => 15,
		);

		$response = $this->call_gateway_endpoint( 'https://api.payro24.ir/v1.1/payment', $args );
		if ( is_wp_error( $response ) ) {
			$error = $response->get_error_message();
			$row['status'] = 'failed';
			$row['log'] = $error;
			$wpdb->insert( $wpdb->prefix . "cf7_transactions", $row, $row_format );
            wp_redirect( add_query_arg( 'payro24_error', $error ) );
			exit();
		}

		$http_status = wp_remote_retrieve_response_code( $response );
		$result      = wp_remote_retrieve_body( $response );
		$result      = json_decode( $result );

		if ( $http_status != 201 || empty( $result ) || empty( $result->id ) || empty( $result->link ) ) {
			$error = sprintf( 'Error : %s (error code: %s)', $result->error_message, $result->error_code );
			$row['status'] = 'failed';
			$row['log'] = $error;
			$wpdb->insert( $wpdb->prefix . "cf7_transactions", $row, $row_format );
            wp_redirect( add_query_arg( 'payro24_error', $error ) );
		}
		else {
			$row['trans_id'] = $result->id;
			$wpdb->insert( $wpdb->prefix . "cf7_transactions", $row, $row_format );
            wp_redirect( $result->link );
		}
		exit();
	}

	/**
	 * Calls the gateway endpoints.
	 *
	 * Tries to get response from the gateway for 4 times.
	 *
	 * @param $url
	 * @param $args
	 *
	 * @return array|\WP_Error
	 */
	private function call_gateway_endpoint( $url, $args ) {
		$number_of_connection_tries = 4;
		while ( $number_of_connection_tries ) {
			$response = wp_safe_remote_post( $url, $args );
			if ( is_wp_error( $response ) ) {
				$number_of_connection_tries --;
				continue;
			} else {
				break;
			}
		}
		return $response;
	}
}
