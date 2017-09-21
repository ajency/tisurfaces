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

    if (isset ($atts ['subject']) && substr_count($atts ['subject'],'Password reset for')>0 ) {
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


//Mail Template

function generate_email_template($mail_type,$data){

$main_html='<!doctype html>
<html xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office" style="
"><head>
        <!-- NAME: 1 COLUMN -->
        <!--[if gte mso 15]>
        <xml>
            <o:OfficeDocumentSettings>
            <o:AllowPNG/>
            <o:PixelsPerInch>96</o:PixelsPerInch>
            </o:OfficeDocumentSettings>
        </xml>
        <![endif]-->
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>tiSURFACES Template</title>

    <style type="text/css">
        p{
            margin:10px 0;
            padding:0;
        }
        table{
            border-collapse:collapse;
        }
        h1,h2,h3,h4,h5,h6{
            display:block;
            margin:0;
            padding:0;
        }
        img,a img{
            border:0;
            height:auto;
            outline:none;
            text-decoration:none;
        }
        body,#bodyTable,#bodyCell{
            height:100%;
            margin:0;
            padding:0;
            width:100%;
        }
        #outlook a{
            padding:0;
        }
        img{
            -ms-interpolation-mode:bicubic;
        }
        table{
            mso-table-lspace:0pt;
            mso-table-rspace:0pt;
        }
        .ReadMsgBody{
            width:100%;
        }
        .ExternalClass{
            width:100%;
        }
        p,a,li,td,blockquote{
            mso-line-height-rule:exactly;
        }
        a[href^=tel],a[href^=sms]{
            color:inherit;
            cursor:default;
            text-decoration:none;
        }
        p,a,li,td,body,table,blockquote{
            -ms-text-size-adjust:100%;
            -webkit-text-size-adjust:100%;
        }
        .ExternalClass,.ExternalClass p,.ExternalClass td,.ExternalClass div,.ExternalClass span,.ExternalClass font{
            line-height:100%;
        }
        a[x-apple-data-detectors]{
            color:inherit !important;
            text-decoration:none !important;
            font-size:inherit !important;
            font-family:inherit !important;
            font-weight:inherit !important;
            line-height:inherit !important;
        }
        #bodyCell{
            padding:10px;
        }
        .templateContainer{
            max-width:600px !important;
        }
        a.mcnButton{
            display:block;
        }
        .mcnImage{
            vertical-align:bottom;
        }
        .mcnTextContent{
            word-break:break-word;
        }
        .mcnTextContent img{
            height:auto !important;
        }
        .mcnDividerBlock{
            table-layout:fixed !important;
        }
    /*
    @tab Page
    @section Background Style
    @tip Set the background color and top border for your email. You may want to choose colors that match your company\'s branding.
    */
        body,#bodyTable{
            /*@editable*/background-color:#f2f2f2;
        }
    /*
    @tab Page
    @section Background Style
    @tip Set the background color and top border for your email. You may want to choose colors that match your company\'s branding.
    */
        #bodyCell{
            /*@editable*/border-top:0;
        }
    /*
    @tab Page
    @section Email Border
    @tip Set the border for your email.
    */
        .templateContainer{
            /*@editable*/border:0;
        }
    /*
    @tab Page
    @section Heading 1
    @tip Set the styling for all first-level headings in your emails. These should be the largest of your headings.
    @style heading 1
    */
        h1{
            /*@editable*/color:#202020;
            /*@editable*/font-family:Helvetica;
            /*@editable*/font-size:26px;
            /*@editable*/font-style:normal;
            /*@editable*/font-weight:bold;
            /*@editable*/line-height:125%;
            /*@editable*/letter-spacing:normal;
            /*@editable*/text-align:left;
        }
    /*
    @tab Page
    @section Heading 2
    @tip Set the styling for all second-level headings in your emails.
    @style heading 2
    */
        h2{
            /*@editable*/color:#202020;
            /*@editable*/font-family:Helvetica;
            /*@editable*/font-size:22px;
            /*@editable*/font-style:normal;
            /*@editable*/font-weight:bold;
            /*@editable*/line-height:125%;
            /*@editable*/letter-spacing:normal;
            /*@editable*/text-align:left;
        }
    /*
    @tab Page
    @section Heading 3
    @tip Set the styling for all third-level headings in your emails.
    @style heading 3
    */
        h3{
            /*@editable*/color:#cf6161;
            /*@editable*/font-family:Helvetica;
            /*@editable*/font-size:20px;
            /*@editable*/font-style:normal;
            /*@editable*/font-weight:bold;
            /*@editable*/line-height:125%;
            /*@editable*/letter-spacing:normal;
            /*@editable*/text-align:left;
        }
    /*
    @tab Page
    @section Heading 4
    @tip Set the styling for all fourth-level headings in your emails. These should be the smallest of your headings.
    @style heading 4
    */
        h4{
            /*@editable*/color:#202020;
            /*@editable*/font-family:Helvetica;
            /*@editable*/font-size:18px;
            /*@editable*/font-style:normal;
            /*@editable*/font-weight:bold;
            /*@editable*/line-height:125%;
            /*@editable*/letter-spacing:normal;
            /*@editable*/text-align:left;
        }
    /*
    @tab Preheader
    @section Preheader Style
    @tip Set the background color and borders for your email\'s preheader area.
    */
        #templatePreheader{
            /*@editable*/background-color:#f2f2f2;
            /*@editable*/background-image:none;
            /*@editable*/background-repeat:no-repeat;
            /*@editable*/background-position:center;
            /*@editable*/background-size:cover;
            /*@editable*/border-top:0;
            /*@editable*/border-bottom:0;
            /*@editable*/padding-top:9px;
            /*@editable*/padding-bottom:9px;
        }
    /*
    @tab Preheader
    @section Preheader Text
    @tip Set the styling for your email\'s preheader text. Choose a size and color that is easy to read.
    */
        #templatePreheader .mcnTextContent,#templatePreheader .mcnTextContent p{
            /*@editable*/color:#656565;
            /*@editable*/font-family:Helvetica;
            /*@editable*/font-size:12px;
            /*@editable*/line-height:150%;
            /*@editable*/text-align:left;
        }
    /*
    @tab Preheader
    @section Preheader Link
    @tip Set the styling for your email\'s preheader links. Choose a color that helps them stand out from your text.
    */
        #templatePreheader .mcnTextContent a,#templatePreheader .mcnTextContent p a{
            /*@editable*/color:#656565;
            /*@editable*/font-weight:normal;
            /*@editable*/text-decoration:underline;
        }
    /*
    @tab Header
    @section Header Style
    @tip Set the background color and borders for your email\'s header area.
    */
        #templateHeader{
            /*@editable*/background-color:#FFFFFF;
            /*@editable*/background-image:none;
            /*@editable*/background-repeat:no-repeat;
            /*@editable*/background-position:center;
            /*@editable*/background-size:cover;
            /*@editable*/border-top:0;
            /*@editable*/border-bottom:0;
            /*@editable*/padding-top:9px;
            /*@editable*/padding-bottom:0;
        }
    /*
    @tab Header
    @section Header Text
    @tip Set the styling for your email\'s header text. Choose a size and color that is easy to read.
    */
        #templateHeader .mcnTextContent,#templateHeader .mcnTextContent p{
            /*@editable*/color:#202020;
            /*@editable*/font-family:Helvetica;
            /*@editable*/font-size:16px;
            /*@editable*/line-height:150%;
            /*@editable*/text-align:left;
        }
    /*
    @tab Header
    @section Header Link
    @tip Set the styling for your email\'s header links. Choose a color that helps them stand out from your text.
    */
        #templateHeader .mcnTextContent a,#templateHeader .mcnTextContent p a{
            /*@editable*/color:#2BAADF;
            /*@editable*/font-weight:normal;
            /*@editable*/text-decoration:underline;
        }
    /*
    @tab Body
    @section Body Style
    @tip Set the background color and borders for your email\'s body area.
    */
        #templateBody{
            /*@editable*/background-color:#FFFFFF;
            /*@editable*/background-image:none;
            /*@editable*/background-repeat:no-repeat;
            /*@editable*/background-position:center;
            /*@editable*/background-size:cover;
            /*@editable*/border-top:0;
            /*@editable*/border-bottom:2px double #EAEAEA;
            /*@editable*/padding-top:0;
            /*@editable*/padding-bottom:9px;
        }
    /*
    @tab Body
    @section Body Text
    @tip Set the styling for your email\'s body text. Choose a size and color that is easy to read.
    */
        #templateBody .mcnTextContent,#templateBody .mcnTextContent p{
            /*@editable*/color:#202020;
            /*@editable*/font-family:Helvetica;
            /*@editable*/font-size:16px;
            /*@editable*/line-height:150%;
            /*@editable*/text-align:left;
        }
    /*
    @tab Body
    @section Body Link
    @tip Set the styling for your email\'s body links. Choose a color that helps them stand out from your text.
    */
        #templateBody .mcnTextContent a,#templateBody .mcnTextContent p a{
            /*@editable*/color:#2BAADF;
            /*@editable*/font-weight:normal;
            /*@editable*/text-decoration:underline;
        }
    /*
    @tab Footer
    @section Footer Style
    @tip Set the background color and borders for your email\'s footer area.
    */
        #templateFooter{
            /*@editable*/background-color:#f2f2f2;
            /*@editable*/background-image:none;
            /*@editable*/background-repeat:no-repeat;
            /*@editable*/background-position:center;
            /*@editable*/background-size:cover;
            /*@editable*/border-top:0;
            /*@editable*/border-bottom:0;
            /*@editable*/padding-top:9px;
            /*@editable*/padding-bottom:9px;
        }
    /*
    @tab Footer
    @section Footer Text
    @tip Set the styling for your email\'s footer text. Choose a size and color that is easy to read.
    */
        #templateFooter .mcnTextContent,#templateFooter .mcnTextContent p{
            /*@editable*/color:#656565;
            /*@editable*/font-family:Helvetica;
            /*@editable*/font-size:12px;
            /*@editable*/line-height:150%;
            /*@editable*/text-align:center;
        }
    /*
    @tab Footer
    @section Footer Link
    @tip Set the styling for your email\'s footer links. Choose a color that helps them stand out from your text.
    */
        #templateFooter .mcnTextContent a,#templateFooter .mcnTextContent p a{
            /*@editable*/color:#656565;
            /*@editable*/font-weight:normal;
            /*@editable*/text-decoration:underline;
        }
    @media only screen and (min-width:768px){
        .templateContainer{
            width:600px !important;
        }

}   @media only screen and (max-width: 480px){
        body,table,td,p,a,li,blockquote{
            -webkit-text-size-adjust:none !important;
        }

}   @media only screen and (max-width: 480px){
        body{
            width:100% !important;
            min-width:100% !important;
        }

}   @media only screen and (max-width: 480px){
        #bodyCell{
            padding-top:10px !important;
        }

}   @media only screen and (max-width: 480px){
        .mcnImage{
            width:100% !important;
        }

}   @media only screen and (max-width: 480px){
        .mcnCartContainer,.mcnCaptionTopContent,.mcnRecContentContainer,.mcnCaptionBottomContent,.mcnTextContentContainer,.mcnBoxedTextContentContainer,.mcnImageGroupContentContainer,.mcnCaptionLeftTextContentContainer,.mcnCaptionRightTextContentContainer,.mcnCaptionLeftImageContentContainer,.mcnCaptionRightImageContentContainer,.mcnImageCardLeftTextContentContainer,.mcnImageCardRightTextContentContainer{
            max-width:100% !important;
            width:100% !important;
        }

}   @media only screen and (max-width: 480px){
        .mcnBoxedTextContentContainer{
            min-width:100% !important;
        }

}   @media only screen and (max-width: 480px){
        .mcnImageGroupContent{
            padding:9px !important;
        }

}   @media only screen and (max-width: 480px){
        .mcnCaptionLeftContentOuter .mcnTextContent,.mcnCaptionRightContentOuter .mcnTextContent{
            padding-top:9px !important;
        }

}   @media only screen and (max-width: 480px){
        .mcnImageCardTopImageContent,.mcnCaptionBlockInner .mcnCaptionTopContent:last-child .mcnTextContent{
            padding-top:18px !important;
        }

}   @media only screen and (max-width: 480px){
        .mcnImageCardBottomImageContent{
            padding-bottom:9px !important;
        }

}   @media only screen and (max-width: 480px){
        .mcnImageGroupBlockInner{
            padding-top:0 !important;
            padding-bottom:0 !important;
        }

}   @media only screen and (max-width: 480px){
        .mcnImageGroupBlockOuter{
            padding-top:9px !important;
            padding-bottom:9px !important;
        }

}   @media only screen and (max-width: 480px){
        .mcnTextContent,.mcnBoxedTextContentColumn{
            padding-right:18px !important;
            padding-left:18px !important;
        }

}   @media only screen and (max-width: 480px){
        .mcnImageCardLeftImageContent,.mcnImageCardRightImageContent{
            padding-right:18px !important;
            padding-bottom:0 !important;
            padding-left:18px !important;
        }

}   @media only screen and (max-width: 480px){
        .mcpreview-image-uploader{
            display:none !important;
            width:100% !important;
        }

}   @media only screen and (max-width: 480px){
    /*
    @tab Mobile Styles
    @section Heading 1
    @tip Make the first-level headings larger in size for better readability on small screens.
    */
        h1{
            /*@editable*/font-size:22px !important;
            /*@editable*/line-height:125% !important;
        }

}   @media only screen and (max-width: 480px){
    /*
    @tab Mobile Styles
    @section Heading 2
    @tip Make the second-level headings larger in size for better readability on small screens.
    */
        h2{
            /*@editable*/font-size:20px !important;
            /*@editable*/line-height:125% !important;
        }

}   @media only screen and (max-width: 480px){
    /*
    @tab Mobile Styles
    @section Heading 3
    @tip Make the third-level headings larger in size for better readability on small screens.
    */
        h3{
            /*@editable*/font-size:18px !important;
            /*@editable*/line-height:125% !important;
        }

}   @media only screen and (max-width: 480px){
    /*
    @tab Mobile Styles
    @section Heading 4
    @tip Make the fourth-level headings larger in size for better readability on small screens.
    */
        h4{
            /*@editable*/font-size:16px !important;
            /*@editable*/line-height:150% !important;
        }

}   @media only screen and (max-width: 480px){
    /*
    @tab Mobile Styles
    @section Boxed Text
    @tip Make the boxed text larger in size for better readability on small screens. We recommend a font size of at least 16px.
    */
        .mcnBoxedTextContentContainer .mcnTextContent,.mcnBoxedTextContentContainer .mcnTextContent p{
            /*@editable*/font-size:14px !important;
            /*@editable*/line-height:150% !important;
        }

}   @media only screen and (max-width: 480px){
    /*
    @tab Mobile Styles
    @section Preheader Visibility
    @tip Set the visibility of the email\'s preheader on small screens. You can hide it to save space.
    */
        #templatePreheader{
            /*@editable*/display:block !important;
        }

}   @media only screen and (max-width: 480px){
    /*
    @tab Mobile Styles
    @section Preheader Text
    @tip Make the preheader text larger in size for better readability on small screens.
    */
        #templatePreheader .mcnTextContent,#templatePreheader .mcnTextContent p{
            /*@editable*/font-size:14px !important;
            /*@editable*/line-height:150% !important;
        }

}   @media only screen and (max-width: 480px){
    /*
    @tab Mobile Styles
    @section Header Text
    @tip Make the header text larger in size for better readability on small screens.
    */
        #templateHeader .mcnTextContent,#templateHeader .mcnTextContent p{
            /*@editable*/font-size:16px !important;
            /*@editable*/line-height:150% !important;
        }

}   @media only screen and (max-width: 480px){
    /*
    @tab Mobile Styles
    @section Body Text
    @tip Make the body text larger in size for better readability on small screens. We recommend a font size of at least 16px.
    */
        #templateBody .mcnTextContent,#templateBody .mcnTextContent p{
            /*@editable*/font-size:16px !important;
            /*@editable*/line-height:150% !important;
        }

}   @media only screen and (max-width: 480px){
    /*
    @tab Mobile Styles
    @section Footer Text
    @tip Make the footer content text larger in size for better readability on small screens.
    */
        #templateFooter .mcnTextContent,#templateFooter .mcnTextContent p{
            /*@editable*/font-size:14px !important;
            /*@editable*/line-height:150% !important;
        }

}</style>


</head>
    <body>
        <center>
            <table align="center" border="0" cellpadding="0" cellspacing="0" height="100%" width="100%" id="bodyTable" style="background-color: #000000;">
                <tbody><tr>
                    <td align="center" valign="top" id="bodyCell">
                        <!-- BEGIN TEMPLATE // -->
                        <!--[if gte mso 9]>
                        <table align="center" border="0" cellspacing="0" cellpadding="0" width="600" style="width:600px;">
                        <tr>
                        <td align="center" valign="top" width="600" style="width:600px;">
                        <![endif]-->
                        <table border="0" cellpadding="0" cellspacing="0" width="100%" class="templateContainer" style="width: 600px;max-width:600px;">
                            <tbody><tr>
                                <td valign="top" id="templatePreheader"><table border="0" cellpadding="0" cellspacing="0" width="100%" class="mcnTextBlock" style="min-width:100%;">
    <tbody class="mcnTextBlockOuter">
        <tr>
            <td valign="top" class="mcnTextBlockInner" style="padding-top:9px;">
                <!--[if mso]>
                <table align="left" border="0" cellspacing="0" cellpadding="0" width="100%" style="width:100%;">
                <tr>
                <![endif]-->

                <!--[if mso]>
                <td valign="top" width="390" style="width:390px;">
                <![endif]-->
                <table align="center" border="0" cellpadding="0" cellspacing="0" style="max-width:390px;" width="100%" class="mcnTextContentContainer">
                    <tbody><tr>

                        <td valign="top" class="mcnTextContent" style="padding-top:0; padding-left:18px; padding-bottom:9px; padding-right:18px;text-align: center;">

                            <h2 class="text-logo h3 logo-skinnable" data-maxheight="24" style="font-size:24px;text-align: center;color:#fff;">tiSURFACES</h2>
                        </td>
                    </tr>
                </tbody></table>
                <!--[if mso]>
                </td>
                <![endif]-->

                <!--[if mso]>
                <td valign="top" width="210" style="width:210px;">
                <![endif]-->
                <!--[if mso]>
                </td>
                <![endif]-->

                <!--[if mso]>
                </tr>
                </table>
                <![endif]-->
            </td>
        </tr>
    </tbody>
</table></td>
                            </tr>
                            <tr>
                  <td valign="top" id="templateHeader" style="padding: 20px;font-family: helvetica;background-size: 84%;background-position: 0 0;background-repeat: repeat-x;background-color: #fff;">

                    <table border="0" cellpadding="0" cellspacing="0" width="100%" class="mcnCaptionBlock">
                <tbody class="mcnCaptionBlockOuter">
                    <tr>
                        <td class="mcnCaptionBlockInner" valign="top" style="padding:9px;">




                    <div style="text-align: center;color: #303133;font-family: helvetica;">
                    ';




     switch ($mail_type) {


                            case 'passwordreset_mail':
                             $table_html.='

                                <h1 style="text-align: center;font-weight: 100;margin-bottom: 15px;color: #303133;"><span style="border-bottom: 2px solid #dbc698;">H</span>ello '.$data['display_name'].'</h1>

                                    <p style="font-size: 17px;font-weight: 100;margin: 15px 0;">We heard you need a password reset.</p>

                                    <p style="font-size: 17px;font-weight: 100;margin: 15px 0;line-height: 1.5;">Click the link below and you\'ll be redirected to tiSURFACES site from which you can set a new password.</p>

                                    <a href="'.$data['url'].'" style="background-color: #d40000;color: #fff;text-decoration: none;font-weight: 100;padding: 10px 20px;margin: 10px 0;display: inline-block;cursor:pointer;font-size: 16px;">Reset Password</a>

                              ';

                            break;




                          default:
                            $table_html.='No email template';
                            break;
     }

                            $table_html.=' </div>




                    </td>
                </tr>
            </tbody>
        </table>
        </td>
        </tr><tr>
                                <td valign="top" id="templateFooter">
<table border="0" cellpadding="0" cellspacing="0" width="100%" class="mcnTextBlock" style="min-width:100%;">
    <tbody class="mcnTextBlockOuter">
        <tr>
            <td valign="top" class="mcnTextBlockInner" style="padding-top:9px;">
                <!--[if mso]>
                <table align="left" border="0" cellspacing="0" cellpadding="0" width="100%" style="width:100%;">
                <tr>
                <![endif]-->

                <!--[if mso]>
                <td valign="top" width="600" style="width:600px;">
                <![endif]-->
                <table align="left" border="0" cellpadding="0" cellspacing="0" style="max-width:100%; min-width:100%;" width="100%" class="mcnTextContentContainer">
                    <tbody><tr>

                        <td valign="top" class="mcnTextContent" style="padding-top:10px; padding-right:18px; padding-bottom:20px; padding-left:18px;color: #656565;font-family: Helvetica;font-size: 13px;line-height: 150%;text-align: center;">

                            <p style="margin: 0;">You received this email because you\'re a registered tiSURFACES user. We occasionally send system alerts with account information, planned outages, system improvements, and important customer-service updates. We\'ll keep them brief and useful. Promise.</p>
                            <p style="margin: 0;margin-bottom: 10px;">©2017 tisurfaces.com. All Rights Reserved.</p>
                    <a href="" style="padding: 0 5px;color: #656565;font-weight: normal;text-decoration: underline;">Terms of use</a><span style="vertical-align: text-bottom;font-size: 15px;">.</span><a href="*|UPDATE_PROFILE|*" style="padding: 0 5px;color: #656565;font-weight: normal;text-decoration: underline;">View in browser</a><span style="vertical-align: text-bottom;font-size: 15px;">.</span><a href="*|UNSUB|*" style="padding: 0 5px;color: #656565;font-weight: normal;text-decoration: underline;">Unsubscribe</a><!-- <span style="vertical-align: text-bottom;font-size: 15px;">.</span><a href="" style="padding: 0 5px;">Unsubscribe</a> --><br>
                        </td>
                    </tr>
                </tbody></table>
                <!--[if mso]>
                </td>
                <![endif]-->

                <!--[if mso]>
                </tr>
                </table>
                <![endif]-->
            </td>
        </tr>
    </tbody>
</table></td>
                            </tr>
                        </tbody></table>
                        <!--[if gte mso 9]>
                        </td>
                        </tr>
                        </table>
                        <![endif]-->
                        <!-- // END TEMPLATE -->
                    </td>
                </tr>
            </tbody></table>
        </center>

</body></html>';


return $main_html.$table_html;
}

// custom code finish