<div class="isotope-system isotope-general-light">
    <div class="isotope-wrapper half-gutter">
        <div class="isotope-container isotope-layout style-masonry isotope-pagination" data-type="masonry" data-layout="fitRows" data-lg="1000" data-md="600" data-sm="480">
    <?php 
        $page_title = get_the_title();
        $args = array(
            'post_type' => 'product',
            'posts_per_page' => -1,
            'product_cat' => $page_title,
            'orderby' => 'name',
            'order' => 'ASC'
        );
        $loop = new WP_Query( $args );
    ?>
        <?php while ( $loop->have_posts() ) : $loop->the_post();
            global $product;
        ?>
            <div class="tmb tmb-woocommerce tmb-iso-w3 tmb-iso-h4 tmb-light tmb-overlay-text-anim tmb-overlay-anim tmb-content-center  grid-cat-99 tmb-content-under tmb-media-first tmb-no-bg">    
                <div class="t-inside animate_when_almost_visible bottom-t-top start_animation" data-delay="200">
                  <div class="t-entry-visual" tabindex="0">
                    <div class="t-entry-visual-tc">
                      <div class="t-entry-visual-cont">
                        <div class="dummy" style="padding-top: 74.81%;"></div>
                        <a tabindex="-1" href="<?php echo get_permalink( $loop->post->ID ) ?>" class="pushed" target="_self">
                        <div class="t-entry-visual-overlay">
                          <div class="t-entry-visual-overlay-in style-dark-bg" style="opacity: 0.1;"></div>
                        </div>
                        <div class="t-overlay-wrap">
                          <div class="t-overlay-inner">
                            <div class="t-overlay-content">
                              <div class="t-overlay-text half-block-padding">
                                <div class="t-entry t-single-line"></div>
                              </div>
                            </div>
                          </div>
                        </div>
                        <?php if (has_post_thumbnail( $loop->post->ID )) echo get_the_post_thumbnail($loop->post->ID, 'shop_catalog'); else echo '<img src="'.woocommerce_placeholder_img_src().'" alt="<?php the_title(); ?>" width="300px" height="300px" />'; ?></a>
                        <div class="add-to-cart-overlay"><?php woocommerce_template_loop_add_to_cart( $loop->post, $product ); ?></div>
                      </div>
                    </div>
                  </div>
                  <div class="t-entry-text">
                    <div class="t-entry-text-tc half-block-padding">
                      <div class="t-entry">
                        <h3 class="t-entry-title h6"><a href="<?php echo get_permalink( $loop->post->ID ) ?>"><?php the_title(); ?></a></h3>
                        <span class="price h6"><span class="wc-measurement-price-calculator-price"><ins class="h2"><span class="woocommerce-Price-amount amount"><span class="woocommerce-Price-currencySymbol"><?php echo $product->get_price_html(); ?></span></span></ins></span></span></div>
                    </div>
                  </div>
                </div>
            </div>
        <?php endwhile; ?>
        <?php wp_reset_query(); ?>
    </div>
</div>