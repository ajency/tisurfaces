<?php
/**
 * Customer processing order email
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/emails/customer-processing-order.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see 	    https://docs.woocommerce.com/document/template-structure/
 * @author 		WooThemes
 * @package 	WooCommerce/Templates/Emails
 * @version     2.5.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @hooked WC_Emails::email_header() Output the email header
 */
do_action( 'woocommerce_email_header', $email_heading, $email );

if($email_heading=='Order has been confirmed'){  
  $data=maybe_unserialize( get_option('woocommerce_wc_order_status_email_58386_settings') );
?>
<span class="im">
<p><?php _e($data['body_text']);  ?></p>
</span>
<?php

}

else if($email_heading=='Here is the Final Quote From Tisurfaces'){  
  $data=maybe_unserialize( get_option('woocommerce_wc_order_status_email_58368_settings') );
?>
<span class="im">
<p><?php _e($data['body_text']);  ?></p>
</span>

<p><?php _e( "As per the discussion we had, here is the final quote for the items you wish to purchase. Please make the payment via direct bank transfer or cheque (details below ) to process the order." ); ?></p>
<p><?php _e( "Direct Bank Transfer : " ); ?></p>
<p><?php _e( "Cheque Payments :" ); ?></p>

<?php
}
/**
 * @hooked WC_Emails::order_details() Shows the order details table.
 * @hooked WC_Structured_Data::generate_order_data() Generates structured data.
 * @hooked WC_Structured_Data::output_structured_data() Outputs structured data.
 * @since 2.5.0
 */
do_action( 'woocommerce_email_order_details', $order, $sent_to_admin, $plain_text, $email );

/**
 * @hooked WC_Emails::order_meta() Shows order meta data.
 */
do_action( 'woocommerce_email_order_meta', $order, $sent_to_admin, $plain_text, $email );

/**
 * @hooked WC_Emails::customer_details() Shows customer details
 * @hooked WC_Emails::email_address() Shows email address
 */
do_action( 'woocommerce_email_customer_details', $order, $sent_to_admin, $plain_text, $email );

/**
 * @hooked WC_Emails::email_footer() Output the email footer
 */
do_action( 'woocommerce_email_footer', $email );
