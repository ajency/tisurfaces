<?php
/**
 * uncode functions and definitions
 *
 * @package uncode
 */

$ok_php = true;
if ( function_exists( 'phpversion' ) ) {
    $php_version = phpversion();
    if (version_compare($php_version,'5.3.0') < 0) $ok_php = false;
}
if (!$ok_php && !is_admin()) {
    $title = esc_html__( 'PHP version obsolete','uncode' );
    $html = '<h2>' . esc_html__( 'Ooops, obsolete PHP version' ,'uncode' ) . '</h2>';
    $html .= '<p>' . sprintf( wp_kses( 'We have coded the Uncode theme to run with modern technology and we have decided not to support the PHP version 5.2.x just because we want to challenge our customer to adopt what\'s best for their interests.%sBy running obsolete version of PHP like 5.2 your server will be vulnerable to attacks since it\'s not longer supported and the last update was done the 06 January 2011.%sSo please ask your host to update to a newer PHP version for FREE.%sYou can also check for reference this post of WordPress.org <a href="https://wordpress.org/about/requirements/">https://wordpress.org/about/requirements/</a>' ,'uncode', array('a' => 'href') ), '</p><p>', '</p><p>', '</p><p>') . '</p>';

    wp_die( $html, $title, array('response' => 403) );
}

/**
 * Load the main functions.
 */
require_once get_template_directory() . '/core/inc/main.php';

/**
 * Load the admin functions.
 */
require_once get_template_directory() . '/core/inc/admin.php';

/**
 * Load the uncode export file.
 */
require_once get_template_directory() . '/core/inc/export/uncode_export.php';

/**
 * Font system.
 */
require_once get_template_directory() . '/core/font-system/font-system.php';

/**
 * Load the color system.
 */
require_once get_template_directory() . '/core/inc/colors.php';

/**
 * Required: set 'ot_theme_mode' filter to true.
 */
require_once get_template_directory() . '/core/theme-options/assets/theme-mode/functions.php';

/**
 * Required: include OptionTree.
 */
load_template( get_template_directory() . '/core/theme-options/ot-loader.php' );

/**
 * Load the theme options.
 */
require_once get_template_directory() . '/core/theme-options/assets/theme-mode/theme-options.php';

/**
 * Load the main functions.
 */
require_once get_template_directory() . '/core/inc/performance.php';

/**
 * Load the theme meta boxes.
 */
require_once get_template_directory() . '/core/theme-options/assets/theme-mode/meta-boxes.php';

/**
 * Load TGM plugins activation.
 */
require_once get_template_directory() . '/core/plugins_activation/init.php';

/**
 * Load the media enhanced function.
 */
require_once( ABSPATH . WPINC . '/class-oembed.php' );
require_once get_template_directory() . '/core/inc/media-enhanced.php';

/**
 * Load the bootstrap navwalker.
 */
require_once get_template_directory() . '/core/inc/wp-bootstrap-navwalker.php';

/**
 * Load the bootstrap navwalker.
 */
require_once get_template_directory() . '/core/inc/uncode-comment-walker.php';

/**
 * Load menu builder.
 */
if ($ok_php) require_once get_template_directory() . '/partials/menus.php';

/**
 * Load header builder.
 */
if ($ok_php) require_once get_template_directory() . '/partials/headers.php';

/**
 * Load elements partial.
 */
if ($ok_php) require_once get_template_directory() . '/partials/elements.php';

/**
 * Custom template tags for this theme.
 */
require_once get_template_directory() . '/core/inc/template-tags.php';

/**
 * Helpers functions.
 */
require_once get_template_directory() . '/core/inc/helpers.php';

/**
 * Customizer additions.
 */
require_once get_template_directory() . '/core/inc/customizer.php';

/**
 * Customizer WooCommerce additions.
 */
if (class_exists( 'WooCommerce' )) {
    require_once get_template_directory() . '/core/inc/customizer-woocommerce.php';
}

/**
 * Load one click demo
 */
require_once get_template_directory() . '/core/one-click-demo/init.php';

/**
 * Load Jetpack compatibility file.
 */
require_once get_template_directory() . '/core/inc/jetpack.php';

// adding custom role here
add_role( 'Dealer', 'Dealer', array(
        'read' => true, // True allows that capability
        //'edit_posts' => true, // Allows user to edit their own posts
        //'publish_posts'=>true, //Allows the user to publish, otherwise posts stays in draft mode
        //'edit_published_posts'=>true,
        //'upload_files'=>true,
        //'delete_published_posts'=>true,
    ));
// adding custom role finish here

// custom code
    //1. Add a new form element...
add_action( 'woocommerce_register_form', 'myplugin_register_form' );
function myplugin_register_form() {

    global $wp_roles;
    echo '<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">';
    echo '<label for="role">'._e('Register as').'</lable>';
    echo '<select name="role" class="input">';
    echo '<option value="">Select Role</option>';
    foreach ( $wp_roles->roles as $key=>$value ) {
       // Exclude default roles such as administrator etc. Add your own
            if($key == 'customer' || $key == 'Dealer'){
          echo '<option value="'.$key.'">'.$value['name'].'</option>';
       }
    }
    echo '</select>';
    echo '</p>';
}

//2. Add validation.
if(is_page('my-account')){
    add_action( 'woocommerce_register_post', 'myplugin_registration_errors', 10, 3 );
}
function myplugin_registration_errors(  $username, $email, $validation_errors ) {
    if ( empty( $_POST['role'] ) || ! empty( $_POST['role'] ) && trim( $_POST['role'] ) == '' ) {
         $validation_errors->add( 'role_error', __( '<strong>ERROR</strong>: You must include a role.', 'mydomain' ) );
    }

    return $errors;
}

//3. Finally, save our extra registration user meta.
add_action( 'woocommerce_created_customer', 'myplugin_user_register' );
function myplugin_user_register( $user_id ) {

   $user_id = wp_update_user( array( 'ID' => $user_id, 'role' => $_POST['role'] ) );
}

// Add filter for registration email body
add_filter('wp_mail','handle_wp_mail');

function handle_wp_mail($atts) {

    if (isset ($atts ['subject']) && substr_count($atts ['subject'],'Your username and password info')>0 ) {
        if (isset($atts['message'])) {
           $user = get_user_by( 'email', $atts['to'] );
           $key = get_password_reset_key( $user );
           $url= network_site_url("wp-login.php?action=rp&key=$key&login=" . rawurlencode($user->user_login), 'login');
           $data=array('email'=>$atts['to'],'display_name'=>$user->display_name,'message'=>$atts['message'],'url'=>$url);
           $atts['subject']='Welcome to tiSURFACES';
           $atts['message'] = generate_email_template('registration_mail',$data);
        }
    }
    else if (isset ($atts ['subject']) && substr_count($atts ['subject'],'Password reset for')>0 ) {
        if (isset($atts['message'])) {
            $user = get_user_by( 'email', $atts['to'] );

            $key = get_password_reset_key( $user );

            $url= network_site_url("wp-login.php?action=rp&key=$key&login=" . rawurlencode($user->user_login), 'login');

            $data=array('email'=>$atts['to'],'display_name'=>$user->display_name,'message'=>$atts['message'],'url'=>$url);

           $atts['message'] = generate_email_template('passwordreset_mail',$data);

        }
    }
    return ($atts);
}

add_filter( 'wp_mail_content_type', function( $content_type ) {
    return 'text/html';
});

require get_stylesheet_directory()."/email-template/email-template.php";

function tisurface_woocommerce_cart_item_quantity($product_quantity, $cart_item_key, $cart_item)
{
    $user_id        = get_current_user_id();
    $role_name = tisf_get_user_role($user_id);
$role_arr=array('customer','subscriber','Dealer');

    if (in_array($role_name,$role_arr)){
        if ($cart_item['quantity'] < 20) {
            $html = '<div class="get-discount"><i class="fa fa-unlock"></i> Unlock a special discount by adding more than 20 units of this product to cart!</div>';
        } else if ($cart_item['quantity'] >= 20) {

            $html = '<div class="won-discount"><i class="fa fa-smile-o"></i> You got a special discount for ordering more than 20 units of this products</div>';
        }

        return $product_quantity . $html;
    }
return $product_quantity;
}


add_filter( 'woocommerce_cart_item_quantity', 'tisurface_woocommerce_cart_item_quantity', 10, 3 );


function tisf_get_user_role( $user = null ) {
    $user = $user ? new WP_User( $user ) : wp_get_current_user();
    return $user->roles ? $user->roles[0] : false;
}


/*Add to cart*/
add_filter( 'woocommerce_product_single_add_to_cart_text', 'woo_custom_cart_button_text' );
function woo_custom_cart_button_text() {
    return __( 'Request Quote', 'woocommerce' );
}

/*Proceed to Checkout*/
remove_action( 'woocommerce_proceed_to_checkout', 'woocommerce_button_proceed_to_checkout', 20 );
add_action('woocommerce_proceed_to_checkout', 'sm_woo_custom_checkout_button_text',20);
function sm_woo_custom_checkout_button_text() {
    $checkout_url = WC()->cart->get_checkout_url();
  ?>
       <a href="<?php echo $checkout_url; ?>" class="checkout-button btn btn-default alt wc-forward"><?php  _e( 'Submit Request', 'woocommerce' ); ?></a>
  <?php
}


/* WooCommerce: The Code Below Removes Checkout Fields */
add_filter( 'woocommerce_checkout_fields' , 'custom_override_checkout_fields' );

function custom_override_checkout_fields( $fields ) {
unset($fields['billing']['billing_address_1']);
unset($fields['billing']['billing_address_2']);
unset($fields['billing']['billing_city']);
unset($fields['billing']['billing_postcode']);
unset($fields['billing']['billing_country']);
unset($fields['billing']['billing_state']);
unset($fields['account']['account_username']);
unset($fields['account']['account_password']);
unset($fields['account']['account_password-2']);
return $fields;
}

/**
 * Custom text on the receipt page.
 */
function isa_order_received_text( $text, $order ) {
    return '<span class="thank-you">Thank You. Your request for quote has been received. We will get back to you with the best quote.</span>';
}
add_filter('woocommerce_thankyou_order_received_text', 'isa_order_received_text', 10, 2 );


//login-logout menu link
add_filter('wp_nav_menu_items', 'add_login_logout_link', 10, 2);
function add_login_logout_link($items, $args) {
        ob_start();
        wp_loginout('index.php');
        $loginoutlink = ob_get_contents();
        ob_end_clean();
        $items .= '<li class="login-out">'. $loginoutlink .'</li>';
    return $items;
}

/* checkout page: place order to submit request */
add_filter( 'woocommerce_order_button_text', 'woo_custom_order_button_text' );
function woo_custom_order_button_text() {
    return __( 'submit request', 'woocommerce' );
}

function custom_shop_page_redirect(){

         if (is_product_category()) {

            global $wp_query;

            $cat = $wp_query->get_queried_object();
            if($cat->slug=='all' ){
    			wp_redirect( home_url('/shop') );
                exit();
    		}
    }
}
add_action( 'template_redirect', 'custom_shop_page_redirect' );

// logout redirect
add_action( 'wp_logout', 'auto_redirect_external_after_logout');
function auto_redirect_external_after_logout(){
  wp_redirect( home_url('/my-account/') );
  exit();
}


// define the woocommerce_product_options_inventory_product_data callback 
function tisurfaces_action_woocommerce_product_options_stock_fields() { 
  
     woocommerce_wp_text_input( 
        array( 
            'id'          => '_in_transit', 
            'label'       => __( 'In Transit quantity', 'woocommerce' ), 
            'placeholder' => '',
            'desc_tip'    => 'true',
            'class'    => '_in_transit',
            'description' => __( 'In Transit qty', 'woocommerce' ),
            'type'              => 'number', 
            'custom_attributes' => array(
                    'step'  => 'any',
                    'min'   => '0'                   
                )  
        )
    );

}; 
add_action( 'woocommerce_product_options_stock_fields', 'tisurfaces_action_woocommerce_product_options_stock_fields', 10, 0 ); 



// Save extra stock Fields
function tisurfaces_woo_add_custom_general_fields_save( $post_id ){
    
  
    $woocommerce_text_field = isset($_POST['_in_transit']) ? $_POST['_in_transit'] : 0 ;
    update_post_meta( $post_id, '_in_transit', esc_attr( $woocommerce_text_field ) );
    
}

add_action( 'woocommerce_process_product_meta', 'tisurfaces_woo_add_custom_general_fields_save' );


/* add text below addto cart btn */
function tisurfaces_content_after_addtocart_button_func() {
    if ( 'yes' === get_option( 'woocommerce_manage_stock' ) ) {
        
       $product_id=get_the_id();
       $_in_transit= get_post_meta( $product_id, '_in_transit', true );
       $_stock= get_post_meta( $product_id, '_stock', true );
    
       if($_in_transit=='')
        $_in_transit=0;

       if($_stock==0 && $_in_transit==0){
            echo '<div  style="font-size:10px;">Currently this product is out of stock.You can still place the order,we will deliver it in 6-8 weeks</div>';
       }
       else{
            echo '<div  style="font-size:10px;">Products in stock : '.$_stock.'
            Products in transit to India: '.$_in_transit.'
            You can pre-order the products and we will deliver it in 3-4 weeks. </div>';
       }
        
    }
}
add_action( 'woocommerce_after_add_to_cart_button', 'tisurfaces_content_after_addtocart_button_func' );

// custom code finish