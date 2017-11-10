<?php
/**
 * Additional Information tab
 *
 * @author        WooThemes
 * @package       WooCommerce/Templates
 * @version       2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $product;

$heading = apply_filters( 'woocommerce_product_additional_information_heading', esc_html__( 'Additional Information', 'woocommerce' ) );

?>

<div class="product-tab">
	<?php if ( $heading ): ?>
		<!-- <h5 class="product-tab-title"><?php echo esc_html($heading); ?></h5> -->
	<?php endif; ?>



	<div class="row add-info-row" style="font-size: 16px;">
		<div class="col-lg-6" style="padding-top: 0; padding-left: 0">
			<?php $product->list_attributes(); ?>

			<?php
			  $additional_info_img =  get_post_meta(get_the_ID(),'additional_info_img', true);
			?>

		</div>
	</div>

	<?php if ($additional_info_img != ''){	?>
		<img src="<?php echo  $additional_info_img; ?>" class="thumbnail">
	<?php }	?>

	<div style="font-size: 16px; margin-top:10px; font-weight:600;">
		<a href="http://www.tisurfaces.com/installation-instruction/" target="_blank" style="text-decoration: underline;">Click here to see installation instructions</a>
	</div>
</div>
