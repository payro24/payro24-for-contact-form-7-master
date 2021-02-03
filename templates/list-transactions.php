<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! current_user_can( "manage_options" ) ) {
	wp_die( __( 'You do not have sufficient permissions to access this page.', 'payro24-contact-form-7' ) );
}

global $wpdb;
$pagenum    = isset( $_GET['pagenum'] ) ? absint( $_GET['pagenum'] ) : 1;
$limit      = 10;
$offset     = ( $pagenum - 1 ) * $limit;
$table_name = $wpdb->prefix . "cf7_transactions";

$transactions = $wpdb->get_results( "SELECT * FROM $table_name  ORDER BY $table_name.id DESC LIMIT $offset, $limit", ARRAY_A );
$total        = $wpdb->get_var( "SELECT COUNT($table_name.id) FROM $table_name" );
$num_of_pages = ceil( $total / $limit );
$cntx         = 0;
$options = get_option( 'payro24_cf7_options' );
$currency = $options['currency'];
?>
<div class="wrap">
    <h2><?php _e( 'Forms Transactions', 'payro24-contact-form-7' ) ?></h2>
    <table class="widefat post fixed" cellspacing="0">
        <thead>
        <tr>
            <th><?php _e( 'Form Name', 'payro24-contact-form-7' ) ?></th>
            <th> <?php _e( 'Date', 'payro24-contact-form-7' ) ?></th>
            <th><?php _e( 'Email', 'payro24-contact-form-7' ) ?></th>
            <th><?php _e( 'Amount', 'payro24-contact-form-7' ) ?></th>
            <th><?php _e( 'Transaction ID', 'payro24-contact-form-7' ) ?></th>
            <th><?php _e( 'Tracking Code', 'payro24-contact-form-7' ) ?></th>
            <th><?php _e( 'Payment Status', 'payro24-contact-form-7' ) ?></th>
            <th><?php _e( 'Payment Log', 'payro24-contact-form-7' ) ?></th>
        </tr>
        </thead>
        <tfoot>
        <tr>
            <th><?php _e( 'Form Name', 'payro24-contact-form-7' ) ?></th>
            <th> <?php _e( 'Date', 'payro24-contact-form-7' ) ?></th>
            <th><?php _e( 'Email', 'payro24-contact-form-7' ) ?></th>
            <th><?php _e( 'Amount', 'payro24-contact-form-7' ) ?></th>
            <th><?php _e( 'Transaction ID', 'payro24-contact-form-7' ) ?></th>
            <th><?php _e( 'Tracking Code', 'payro24-contact-form-7' ) ?></th>
            <th><?php _e( 'Payment Status', 'payro24-contact-form-7' ) ?></th>
            <th><?php _e( 'Payment Log', 'payro24-contact-form-7' ) ?></th>
        </tr>
        </tfoot>
        <tbody>
		<?php
		if ( count( $transactions ) == 0 ) :
			?>
            <tr class="alternate author-self status-publish iedit" valign="top">
                <td colspan="6"><?php _e( 'There are not any transactions.', 'payro24-contact-form-7' ) ?></td>
            </tr>
		<?php
		else:
			foreach ( $transactions as $transaction ):
				?>
                <tr class="alternate author-self status-publish iedit"
                    valign="top">
                    <td> <?php echo get_the_title( $transaction['form_id'] ) ?> </td>
                    <td style="direction: ltr; text-align: right;">
						<?php echo date( "Y-m-d H:i:s", $transaction['created_at'] ); ?>
                    </td>

                    <td> <?php echo $transaction['email'] ?></td>
                    <td>
                        <?php
                        echo ($currency == 'rial' ? $transaction['amount'] : $transaction['amount'] / 10) . " ";
                        _e( $currency == 'rial' ? 'Rial' : 'Toman', 'payro24-contact-form-7' );
                        ?>
                    </td>
                    <td> <?php echo $transaction['trans_id'] ?></td>
                    <td> <?php echo $transaction['track_id'] ?></td>
                    <td>
						<?php if ( $transaction['status'] == "completed" ): ?>
                            <b style="color: #388e3c"><?php _e( 'completed', 'payro24-contact-form-7' ) ?></b>
						<?php elseif ( $transaction['status'] == "failed" ): ?>
                            <b style="color: #f00"><?php _e( 'failed', 'payro24-contact-form-7' ) ?></b>
						<?php elseif ( $transaction['status'] == "pending" ): ?>
                            <b style="color: #ff8f00"><?php _e( 'pending payment', 'payro24-contact-form-7' ) ?></b>
						<?php endif; ?>
                    </td>
                    <td> <?php echo $transaction['log'] ?></td>
                </tr>
			<?php
			endforeach;
		endif;
		?>
        </tbody>
    </table>
    <br>
	<?php
	$page_links = paginate_links( array(
		'base'      => add_query_arg( 'pagenum', '%#%' ),
		'format'    => '',
		'prev_text' => __( '&laquo;', 'payro24-contact-form-7' ),
		'next_text' => __( '&raquo;', 'payro24-contact-form-7' ),
		'total'     => $num_of_pages,
		'current'   => $pagenum,
	) );

	if ( $page_links ):
		?>
        <center>
            <div class="tablenav">
                <div class="tablenav-pages" style="float:none; margin: 1em 0">
					<?php echo $page_links ?>
                </div>
            </div>
        </center>
	<?php endif; ?>
    <br>
    <hr>
</div>
