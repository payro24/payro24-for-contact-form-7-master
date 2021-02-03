<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<form>
    <div>
        <input name='payro24_enable' id='payro24_active' value='1' type='checkbox' <?php echo $checked ?>>
        <label for='payro24_active'><?php _e( 'Enable Payment through payro24 gateway', 'payro24-contact-form-7' ) ?></label>
    </div>
    <div>
        <input name='payro24_default_enable' id='payro24_default_active' onclick="active_payro24_amount()" value='1' type='checkbox' <?php echo $amount > 0 ? 'CHECKED' : ''; ?> >
        <label for='payro24_default_active'><?php _e( 'Predefined amount', 'payro24-contact-form-7' ) ?></label>
    </div>
    <table id="payro24_amount_table" style="transition:height .3s ease;overflow:hidden;display:block;height:<?php echo $amount > 0 ? '40px' : '0'; ?>;">
        <tr>
            <td><?php _e( 'Predefined amount', 'payro24-contact-form-7' ) ?></td>
            <td><input id="payro24_amount" type='text' name='payro24_amount' value='<?php echo $amount ?>'></td>
            <td><?php _e( $currency == 'rial' ? 'Rial' : 'Toman', 'payro24-contact-form-7' ) ?></td>
        </tr>
    </table>
    <script>
        function active_payro24_amount(){
            var checkBox = document.getElementById("payro24_default_active");
            var table    = document.getElementById("payro24_amount_table");
            var text     = document.getElementById("payro24_amount");
            if(checkBox.checked != true){
                table.style.height = '0'
                text.value = ''
            }else{
                table.style.height = '40px'
            }
        }
    </script>

    <div>
        <p>
			<?php _e( 'You can choose fields below in your form. If the predefined amount is not empty, field <code>payro24_amount</code> will be ignored. On the other hand, if you want your customer to enter an arbitrary amount, choose <code>payro24_amount</code> in your form and clear the predefined amount.', 'payro24-contact-form-7' ) ?>
        </p>
        <p>
			<?php _e( "Also check your wp-config.php file and look for this line of code: <code>define('WPCF7_LOAD_JS', false)</code>. If there is not such a line, please put it into your wp-config.file.", 'payro24-contact-form-7' ) ?>
        </p>
        <p>
			<?php _e( "Currency are enabled by default, so if you want to turn it off, you can use : <code>currency:off</code> in your tag.", 'payro24-contact-form-7' ) ?>
        </p>
    </div>

    <table class="widefat">
        <thead>
            <tr>
                <th><?php _e( 'Field', 'payro24-contact-form-7' ) ?></th>
                <th><?php _e( 'Description', 'payro24-contact-form-7' ) ?></th>
                <th><?php _e( 'Example', 'payro24-contact-form-7' ) ?></th>
            </tr>
        </thead>

        <tbody>
            <tr>
                <td>payro24_amount</td>
                <td><?php _e( 'An arbitrary amount', 'payro24-contact-form-7' ) ?></td>
                <td>
                    <code>[payment payro24_amount]</code>
                    <code>[payment payro24_amount currency:off]</code>
                </td>
            </tr>
            <tr>
                <td>payro24_description</td>
                <td><?php _e( 'Payment description', 'payro24-contact-form-7' ) ?></td>
                <td><code>[text payro24_description]</code></td>
            </tr>
            <tr>
                <td>payro24_phone</td>
                <td><?php _e( 'Phone number field', 'payro24-contact-form-7' ) ?></td>
                <td><code>[text payro24_phone]</code></td>
            </tr>
            <tr>
                <td>your-email</td>
                <td><?php _e( 'Email field', 'payro24-contact-form-7' ) ?></td>
                <td><code>[email your-email]</code></td>
            </tr>
            <tr>
                <td>your-name</td>
                <td><?php _e( 'User\'s name field', 'payro24-contact-form-7' ) ?></td>
                <td><code>[text your-name]</code></td>
            </tr>
        </tbody>
    </table>
    <input type='hidden' name='post' value='<?php echo $post_id ?>'>
</form>
