<?php
/**
 * Email Addresses
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/emails/email-addresses.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see       https://docs.woocommerce.com/document/template-structure/
 * @author    WooThemes
 * @package   WooCommerce/Templates/Emails
 * @version     3.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

$text_align = is_rtl() ? 'right' : 'left';

?><table id="addresses" cellspacing="0" cellpadding="0" style="width: 100%; vertical-align: top;" border="0">
  <tr>
    <td class="td billing-td" style="background: #f7f7f7;text-align:<?php echo $text_align; ?>; font-family: 'Helvetica Neue', Helvetica, Roboto, Arial, sans-serif;" valign="top" width="50%">
      <h3><?php _e( 'Customer Details', 'woocommerce' ); ?></h3>

      <table style="width:100%;">
        <tr>
          <td>
            <p class="text"><?php echo $order->get_formatted_billing_address(); ?></p>
          </td>
          <td>
            <label style="font-weight: 600; font-size: 12px;">Email: </label>
            <p class="text">
              <?php echo $order->get_billing_email(); ?>
            </p>
          </td>
          <td>
            <label style="font-weight: 600; font-size: 12px;">Phone: </label>
            <p class="text">
              <?php echo $order->get_billing_phone(); ?>
            </p>
          </td>
        </tr>
      </table>




    </td>
    <?php if ( ! wc_ship_to_billing_address_only() && $order->needs_shipping_address() && ( $shipping = $order->get_formatted_shipping_address() ) ) : ?>
      <td class="td" style="text-align:<?php echo $text_align; ?>; font-family: 'Helvetica Neue', Helvetica, Roboto, Arial, sans-serif;" valign="top" width="50%">
        <h3><?php _e( 'Shipping address', 'woocommerce' ); ?></h3>

        <p class="text"><?php echo $shipping; ?></p>
      </td>
    <?php endif; ?>
  </tr>
</table>
