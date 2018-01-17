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
    $_volume_min_value=get_min_volume_product_variation($cart_item['product_id'],$cart_item['variation_id']);

    if (in_array($role_name,$role_arr)){
        if($_volume_min_value>0){
          if ($cart_item['quantity'] < $_volume_min_value) {
              $html = '<div class="get-discount"><i class="fa fa-percent"></i> Unlock a special discount by adding more than 20 units of this product to cart!</div>';
          } else if ($cart_item['quantity'] >= $_volume_min_value) {

              $html = '<div class="won-discount"><i class="fa fa-percent" style="color: orange;"></i> You got a special discount for ordering more than 20 units of this products</div>';
          }
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
    return __( 'Add to Cart', 'woocommerce' );
}

/*Proceed to Checkout*/
remove_action( 'woocommerce_proceed_to_checkout', 'woocommerce_button_proceed_to_checkout', 20 );
add_action('woocommerce_proceed_to_checkout', 'sm_woo_custom_checkout_button_text',20);
function sm_woo_custom_checkout_button_text() {
    $checkout_url = WC()->cart->get_checkout_url();
  ?>
       <a href="<?php echo $checkout_url; ?>" class="checkout-button btn btn-default alt wc-forward"><?php  _e( 'Proceed to Order', 'woocommerce' ); ?></a>
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

// Login redirect to my-account page
/*add_action('init','possibly_redirect');
function possibly_redirect(){
 global $pagenow;
 if ($pagenow == 'wp-login.php' && !is_user_logged_in()) {
  wp_redirect(home_url('/my-account/'));
  exit();
 }
}*/

/* checkout page: place order to submit request */
add_filter( 'woocommerce_order_button_text', 'woo_custom_order_button_text' );
function woo_custom_order_button_text() {
    return __( 'Proceed to Order', 'woocommerce' );
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
    $product_id=get_the_id();
    if ( 'yes' === get_post_meta( $product_id, '_manage_stock',true ) ) {

       $_in_transit= get_post_meta( $product_id, '_in_transit', true );
       $_stock= get_post_meta( $product_id, '_stock', true );

       if($_in_transit=='')
        $_in_transit=0;

       if($_stock<=0 && $_in_transit<=0){
            echo '<p  class="delivery-note">Currently this product is out of stock.You can still place the order,we will deliver it in <strong>6-8 weeks</strong></p>';
       }
       else{
            echo '<p  class="delivery-note">Products in stock : <strong>'.$_stock.'</strong>.,
            Products in transit to India: <strong>'.$_in_transit.'</strong><br>
            You can pre-order the products and we will deliver it in <strong>3-4 weeks</strong>. </p>';
       }

    }
}
add_action( 'woocommerce_after_add_to_cart_button', 'tisurfaces_content_after_addtocart_button_func' );

add_filter( 'woocommerce_product_tabs', 'woo_reorder_tabs', 98 );
function woo_reorder_tabs( $tabs ) {
  $tabs['description']['priority'] = 15;
  $tabs['additional_information']['priority'] = 10;
  return $tabs;
}

// Edit WooCommerce dropdown menu item of shop page//
// Options: menu_order, popularity, rating, date, price, price-desc

function my_woocommerce_catalog_orderby( $orderby ) {
  unset($orderby["menu_order"]);
  unset($orderby["popularity"]);
  unset($orderby["rating"]);
  unset($orderby["date"]);
  return $orderby;
}
add_filter( "woocommerce_catalog_orderby", "my_woocommerce_catalog_orderby", 20 );

remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_meta', 40 );


function wps_remove_role() {
    remove_role( 'editor' );
    remove_role( 'author' );
    remove_role( 'contributor' );
    remove_role( 'subscriber' );
}
add_action( 'init', 'wps_remove_role' );


// custom admin login logo
function custom_login_logo()
{
   echo '<style type="text/css">
   h1 a { background-image: url(/wp-content/uploads/2017/06/Slice-2.png) !important;   width: 100% !important; background-size: auto !important;}
   </style>';
}
add_action('login_head', 'custom_login_logo');



/**
 * { item_description -display volume discount price backend field}
 */
// add_action( 'woocommerce_product_options_general_product_data', 'woo_add_custom_general_fields' );

function woo_add_custom_general_fields() {

  global $woocommerce, $post;

  echo '<div class="options_group">';

 woocommerce_wp_text_input(
    array(
        'id'          => '_volume_discount_price',
        'label'       => __( 'Volume Discount Price (&#x20b9;)', 'woocommerce' ),
        'placeholder' => '',
        'desc_tip'    => 'true',
        'class'    => 'discountvalue',
        'description' => __( 'Volume Discount in Price will be applicable on a exceeds minimum qty for a single product.', 'woocommerce' ),
        'type'              => 'number',
        'custom_attributes' => array(
                'step'  => 'any',
                'min'   => '0'
            )
    )
);





  echo '</div>';

}

/**
 * { item_description -volume discount price save }
 */
// add_action( 'woocommerce_process_product_meta', 'woo_add_custom_general_fields_save' );

function woo_add_custom_general_fields_save( $post_id ){


    $woocommerce_text_field = isset($_POST['_volume_discount_price']) ? $_POST['_volume_discount_price'] : 0 ;
    update_post_meta( $post_id, '_volume_discount_price', esc_attr( $woocommerce_text_field ) );

}

/**
 * { function_description -product line item sub total }
 *
 * @param      <type>  $product_subtotal  The product subtotal
 * @param      <type>  $product           The product
 * @param      <type>  $quantity          The quantity
 * @param      <type>  $instance          The instance
 *
 * @return     string  ( description_of_the_return_value )
 */
function ti_woocommerce_cart_product_subtotal( $product_subtotal, $product, $quantity, $instance ) {

    $user_id        = get_current_user_id();
    $role_name = tisf_get_user_role($user_id);

    if($role_name=='Dealer'){
      global $woocommerce;

      $product_id=$product->parent_id;
      $variation_id=$product->get_id();
      foreach ($instance->cart_contents as  $cart_item_key => $cart_value) {
        if( $variation_id == $cart_value['variation_id']){
          $line_total=$cart_value['line_total'];
        }
      }

      $discount= ti_discountCalculation($product_id, $quantity,$variation_id);
      $new_product_subtotal=$line_total-$discount;

      if($line_total==$new_product_subtotal)
        return wc_price($line_total);
      else
        return '<strike>'.wc_price($line_total).'</strike> <u>'.wc_price($new_product_subtotal).'</u>';
    }

    return $product_subtotal;
};

add_filter( 'woocommerce_cart_product_subtotal', 'ti_woocommerce_cart_product_subtotal', 10, 4 );




/**
 * { function_description -additional volume discount display}
 *
 * @param      <type>  $cart_object  The cartesian object
 */
function sale_custom_price($cart_object) {
    $user_id        = get_current_user_id();
    $role_name = tisf_get_user_role($user_id);

    if($role_name=='Dealer'){
      global $woocommerce;
      $final_total=0;
      foreach ($cart_object->cart_contents as  $cart_item_key => $cart_value) {
        $product_id=$cart_value['product_id'];
        $quantity=$cart_value['quantity'];
        $line_total=$cart_value['line_total'];
        $variation_id=$cart_value['variation_id'];
        $final_total=$final_total+ti_discountCalculation($product_id,$quantity,$variation_id);
      }

      $discount=$final_total;

      if($discount!=0)
          $cart_object->add_fee('Special Discount', -$discount, true, '');
    }

}
add_action( 'woocommerce_cart_calculate_fees', 'sale_custom_price');

/**
 * { function_description -function to get only the volume discount }
 *
 * @param      <type>   $product_id  The product identifier
 * @param      integer  $quantity    The quantity
 * @param      <type>   $line_total  The line total
 *
 * @return     integer  ( description_of_the_return_value )
 */
function ti_discountCalculation($product_id, $quantity,$variation_id){

    $discount_price=get_volume_discount_product_variation($product_id, $quantity,$variation_id);
    return $total_discount=$discount_price*$quantity;
}

/**
 * Gets the volume discount product variation.
 *
 * @param      <type>   $product_id    The product identifier
 * @param      integer  $quantity      The quantity
 * @param      <type>   $variation_id  The variation identifier
 *
 * @return     integer  The volume discount product variation.
 */
function get_volume_discount_product_variation($product_id, $quantity,$variation_id){
   $_pricing_rules=get_post_meta($product_id,  '_pricing_rules', true );
   // echo "<pre>";
  if(is_array($_pricing_rules)){
    foreach ($_pricing_rules as  $rules) {
       foreach ($rules['conditions'] as $roles_value) {
         if($roles_value['args']['applies_to']=='everyone'){

           if(in_array($variation_id,$rules['variation_rules']['args']['variations'])){

              foreach ($rules['rules'] as $r_value) {
                if($quantity >= $r_value['from']){
                  return $r_value['amount'];
                }
              }
            }
         }
       }
     }
   }
   return 0;
}



function get_min_volume_product_variation($product_id,$variation_id){
   $_pricing_rules=get_post_meta($product_id,  '_pricing_rules', true );
   // echo "<pre>";
    if(is_array($_pricing_rules)){
     foreach ($_pricing_rules as  $rules) {
       foreach ($rules['conditions'] as $roles_value) {
         if($roles_value['args']['applies_to']=='everyone'){
          if(isset($rules['variation_rules']['args']['variations'])){
           if(in_array($variation_id,$rules['variation_rules']['args']['variations'])){

              foreach ($rules['rules'] as $r_value) {
                return $r_value['from'];
              }
            }
          }
         }
       }
     }
   }
   return 0;
}

/**
 * { function_description -calculate the discount with the product subtotal}
 *
 * @param      <type>   $product_id  The product identifier
 * @param      integer  $quantity    The quantity
 * @param      integer  $line_total  The line total
 *
 * @return     integer  ( description_of_the_return_value )
 */
function ti_discountCalculation_subtotal($product_id, $quantity,$line_total,$variation_id){

    $discount_price=get_volume_discount_product_variation($product_id, $quantity,$variation_id);

    return $line_total-($discount_price*$quantity);
}


/**
 * filter to add a discount on the subtotal of the cart n checkout
 *
 * @param      <type>  $cart_subtotal  The cartesian subtotal
 * @param      <type>  $compound       The compound
 * @param      <type>  $instance       The instance
 *
 * @return     <type>  ( description_of_the_return_value )
 */

function ti_filter_woocommerce_cart_subtotal( $cart_subtotal, $compound, $instance ) {
   $user_id        = get_current_user_id();
    $role_name = tisf_get_user_role($user_id);

    if($role_name=='Dealer'){

    global $woocommerce;
    $final_total=0;
    foreach ($instance->cart_contents as  $cart_item_key => $cart_value) {
       $product_id=$cart_value['product_id'];
       $quantity=$cart_value['quantity'];
       $line_total=$cart_value['line_total'];
       $variation_id=$cart_value['variation_id'];
       $final_total=$final_total+ti_discountCalculation_subtotal($product_id,$quantity,$line_total,$variation_id);
    }


    return wc_price($final_total);
  }
  return $cart_subtotal;
};

add_filter( 'woocommerce_cart_subtotal', 'ti_filter_woocommerce_cart_subtotal', 10, 3 );


/**
 * { item_description - add to show all dealers submenu in users menu in dashboard}
 */
add_action('admin_menu', 'my_users_menu');

function my_users_menu() {
  add_users_page('My Plugin Users', 'All Dealers', 'read', 'dealer-user', 'dealer_user_function');
}
function dealer_user_function(){
  wp_redirect( 'users.php?role=Dealer' );
}

/**
 * Redirect users to custom URL based on their role after login
 *
 * @param string $redirect
 * @param object $user
 * @return string
 */
function wc_custom_user_redirect( $redirect, $user ) {
  // Get the first of all the roles assigned to the user
  $role = $user->roles[0];
  $dashboard = admin_url();
  $myaccount = get_permalink( wc_get_page_id( 'myaccount' ) );
  $shop = home_url('/shop') ;
  if( $role == 'administrator' ) {
    //Redirect administrators to the dashboard
    $redirect = $dashboard;
  } elseif ( $role == 'Dealer' ) {
    //Redirect shop managers to the dashboard
    $redirect = $shop;
  } elseif ( $role == 'customer' || $role == 'subscriber' ) {
    //Redirect customers and subscribers to the "My Account" page
    $redirect = $myaccount;
  } else {
    //Redirect any other role to the previous visited page or, if not available, to the home
    $redirect = wp_get_referer() ? wp_get_referer() : home_url();
  }
  return $redirect;
}
add_filter( 'woocommerce_login_redirect', 'wc_custom_user_redirect', 10, 2 );


/**
 * Gets the volume discount for dealer.
 *
 * @param      <type>   $product_id  The product identifier
 *
 * @return     integer  The volume discount for dealer.
 */
function get_volume_discount_for_dealer($product_id){
   $_pricing_rules=get_post_meta($product_id,  '_pricing_rules', true );
   // echo "<pre>";
   if(is_array($_pricing_rules)){
     foreach ($_pricing_rules as  $rules) {
       foreach ($rules['conditions'] as $roles_value) {
          if(isset($roles_value['args']['roles'])){
            if($roles_value['args']['applies_to']=='roles' && in_array('Dealer' ,$roles_value['args']['roles'])){
              foreach ($rules['rules'] as $r_value) {
                  return array('value' => $r_value['amount'],'type'=>$r_value['type']);
              }
            }
          }
        }
      }
   }
    return array('value' => 0,'type'=>'');
}

add_action( 'woocommerce_before_single_product_summary', 'ti_single_product_dealer_discount_calc', 10 );
function ti_single_product_dealer_discount_calc(){
  global $post;

  $user_id        = get_current_user_id();
  $role_name = tisf_get_user_role($user_id);

  if($role_name=='Dealer'){

    wp_register_script( 'accounting', plugin_dir_path('/woocommerce/assets/js/accounting/accounting.min.js') );
    wp_enqueue_script( 'accounting' );
    wp_register_script( 'measure_calc_custom', get_template_directory_uri() . '/library/js/measure-calc-custom.js' );
    wp_enqueue_script( 'measure_calc_custom' );

    $dealer_volume_discount=get_volume_discount_for_dealer($post->ID);
    //print_r($dealer_volume_discount);
    wp_localize_script( 'measure_calc_custom', 'dealer_volume_discount',
          array(
              'value' => $dealer_volume_discount['value'],
              'type' => $dealer_volume_discount['type'],
          )
      );
  }

}
// define the woocommerce_get_price_html callback
function ti_woocommerce_get_price_html( $price, $instance ) {
    global $post,$product,$woocommerce;

  $user_id        = get_current_user_id();
  $role_name = tisf_get_user_role($user_id);

  if($role_name=='Dealer'){
    $price='';
    $price_data =get_post_meta($post->ID,'_price',false);

    $discount_data=get_volume_discount_for_dealer($post->ID);

    if($discount_data['type']=='price_discount'){
      $min=current($price_data)-$discount_data['value'];
      $max=end($price_data)-$discount_data['value'];
    }
    else if($discount_data['type']=='percentage_discount'){
       $min=(current($price_data)-(($discount_data['value']/100)*current($price_data)));
       $max=(end($price_data)-(($discount_data['value']/100)*end($price_data)));
    }
    else{
      $min=current($price_data);
      $max=end($price_data);
    }

    $price.='<span class="woocommerce-Price-currencySymbol">'.woocommerce_price($min).' – </span> <span class="woocommerce-Price-currencySymbol">'.woocommerce_price($max).'</span>';

  }
  return $price;
};
add_filter( 'woocommerce_get_price_html', 'ti_woocommerce_get_price_html', 10, 2 );


/**
 * Remove password strength check.
 */
function ti_remove_password_strength() {
  if ( wp_script_is( 'wc-password-strength-meter', 'enqueued' ) ) {
    wp_dequeue_script( 'wc-password-strength-meter' );
  }
}
add_action( 'wp_print_scripts', 'ti_remove_password_strength', 10 );
// custom code finish