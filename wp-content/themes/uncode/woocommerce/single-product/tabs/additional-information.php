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



<div class="row add-info-row">
	<div class="col-lg-6">
		<?php $product->list_attributes(); ?>
	</div>
	<div class="col-lg-6">
	<h5 class="product-tab-title" style="margin: 0 0 10px 0;">Installation Instructions</h5>
		
		  <!-- thumbnail image wrapped in a link -->

		  		<?php 
		  $instructions_img_url =  get_post_meta(get_the_ID(),'install-instructions-img', true);
		  if ($instructions_img_url == '') 
		  		{
		  			$instructions_img_url = 'http://via.placeholder.com/1024x768';
		  		}
?>
		  <a href="#img1">
			  <div class="img-cover">
			    	<img src="<?php echo  $instructions_img_url; ?>" class="thumbnail">
			   </div>
		  </a>

		  <!-- lightbox container hidden with CSS -->

		  <a href="#_" class="lightbox" id="img1">
		    <img src="<?php echo  $instructions_img_url; ?>">		    
		  </a>
		  <!-- /test -->
		

		
		<?php 
$instructions_img_url =  get_post_meta(get_the_ID(),'install-instructions-img', true);
		?>
	</div>
</div>
</div>
