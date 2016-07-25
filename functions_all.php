<?php /***enqueue child***/
add_action( 'wp_enqueue_scripts', 'theme_enqueue_styles' );
function theme_enqueue_styles() {
	//wp_enqueue_style( 'parent-style', get_template_directory_uri() . '/style.css' );
	//wp_enqueue_style( 'child-style',get_stylesheet_directory_uri() . '/style.css',array('parent-style'));
}
/***********************************i18n*********************************************************/
add_action( 'after_setup_theme', 'my_child_theme_setup' );
function my_child_theme_setup() {
load_child_theme_textdomain( 'profitmag' , get_stylesheet_directory() . '/languages' );
}
/************************************load filter js****************************************************************/
function load_filter_js_byF(){
	wp_enqueue_script('filter_js_byF',get_stylesheet_directory_uri() . '/js/filter-js-byF.js');
}
add_action('wp_enqueue_scripts','load_filter_js_byF');
/******tune*****/
add_action( 'wp_enqueue_scripts', 'my_strength_meter_localize_script' );
function my_strength_meter_localize_script() {
    wp_localize_script( 'password-strength-meter', 'pwsL10n', array(
        'empty'    => __( 'Empty!', 'profitmag' ),
        'short'    => __( 'Very weak', 'profitmag' ),
        'bad'      => __( 'Weak', 'profitmag' ),
        'good'     => __( 'Medium', 'profitmag' ),
        'strong'   => __( 'Strong', 'profitmag' ),
        'mismatch' => __( 'Wrong password!', 'profitmag' )
    ) );
}
/*************************************add order action***********************************************/
function register_awaiting_shipment_order_status() {
    register_post_status( 'wc-shipped', array(
        'label'                     => __('Shipped','profitmag'),
        'public'                    => true,
        'exclude_from_search'       => false,
        'show_in_admin_all_list'    => true,
        'show_in_admin_status_list' => true,
        'label_count'               => _n_noop( 'Shipped <span class="count">(%s)</span>', 'Shipped <span class="count">(%s)</span>' )
    ) );
}
add_action( 'init', 'register_awaiting_shipment_order_status' );
// Add to list of WC Order statuses
function add_awaiting_shipment_to_order_statuses( $order_statuses ) {
    $new_order_statuses = array();
    // add new order status after processing
    foreach ( $order_statuses as $key => $status ) {
        $new_order_statuses[ $key ] = $status;
        if ( 'wc-processing' === $key ) {
            $new_order_statuses['wc-shipped'] = __('Shipped','profitmag');
        }
    }
    return $new_order_statuses;
}
add_filter( 'wc_order_statuses', 'add_awaiting_shipment_to_order_statuses' );
/************************Add checkbox field to the checkout***********************************************/
add_action('woocommerce_after_checkout_billing_form', 'my_custom_checkout_field');
function my_custom_checkout_field( $checkout ) {
	echo '<div id="my-new-field">';
	woocommerce_form_field( 'my_checkbox', array(
	'type'			=> 'checkbox',
	'class'			=> array('input-checkbox'),
	'label'			=> __('Gift wrap?','profitmag'),
	'required'		=> false,
	), $checkout->get_value( 'my_checkbox' ));
	echo '</div>';
}
add_action('woocommerce_checkout_update_order_meta', 'my_custom_checkout_field_update_order_meta');
function my_custom_checkout_field_update_order_meta( $order_id ) {
	if ($_POST['my_checkbox']) update_post_meta( $order_id, 'My Checkbox', esc_attr($_POST['my_checkbox']));
}
add_action( 'woocommerce_admin_order_data_after_billing_address', 'my_custom_checkout_field_display_admin_order_meta', 10, 1 );
function my_custom_checkout_field_display_admin_order_meta($order){
	if (get_post_meta( $order->id, 'My Checkbox', true ))
		echo '<p><strong>Συσκευασία Δώρου: </strong> ΝΑΙ </p>';
}
/*************************Register our sidebars and widgetized areas.*****************************/
function arphabet_widgets_init() {
	
	register_sidebar( array(
		'name'          => 'Translation Widget',
		'id'            => 'custom_widget_area',
		'before_widget' => '<div>',
		'after_widget'  => '</div>',
		'before_title'  => '<h2 class="rounded">',
		'after_title'   => '</h2>',
	) );
	
	register_sidebar( array(
		'name'          => 'Header Bakset Widget',
		'id'            => 'header_basket_widget',
		'before_widget' => '<div>',
		'after_widget'  => '</div>',
		'before_title'  => '<h2 class="rounded">',
		'after_title'   => '</h2>',
	) );
	
}
add_action( 'widgets_init', 'arphabet_widgets_init' );
/*******************************woocommerce hooks for our theme*****************************************/
remove_action( 'woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10);
remove_action( 'woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10);
add_action('woocommerce_before_main_content', 'my_theme_wrapper_start', 10);
add_action('woocommerce_after_main_content', 'my_theme_wrapper_end', 10);

function my_theme_wrapper_start() {
  echo '<section id="main">';
}
function my_theme_wrapper_end() {
  echo '</section>';
}
/********************************admin css change********************************************************/
function fontawesome_dashboard() {
	wp_enqueue_style('fontawesome', 'https://www.silverfamily.gr/wp-content/themes/profitmag-child/css/font-awesome.css', '', '4.6.1', 'all');
}
add_action('admin_init', 'fontawesome_dashboard');
function my_custom_fonts() {
  echo '<style>
	#menu-management .menu-edit, #menu-settings-column .accordion-container, .comment-ays, .feature-filter, .imgedit-group, .manage-menus, .menu-item-handle, .popular-tags, .stuffbox, .widget-inside, .widget-top, .widgets-holder-wrap, .wp-editor-container, p.popular-tags, table.widefat{
		border:0;
	}	#right-sidebar-middle,#left-sidebar-middle,#custom_widget_area,#header_basket_widget,#right-sidebar-top,#fo-top-col-five,#fo-top-col-one,#home-popular,#fo-top-col-six,#fo-bottom-col-one,#fo-bottom-col-two,#fo-bottom-col-three,#fo-bottom-col-four{
		display:none;
		}
	.widefat .column-order_status mark.shipped::after{
	color: #005580;
	content: "\f0d1";
	font-family: FontAwesome;
    font-variant: normal;
    font-weight: 400;
    height: 100%;
    left: 0;
    line-height: 1;
    margin: 0;
    position: absolute;
    text-align: center;
    text-indent: 0;
    text-transform: none;
    top: 0;
    width: 100%;
}

#wp-content-editor-container #ed_toolbar.quicktags-toolbar{
	top: -37px!important;
}
  </style>';
}
add_action('admin_head', 'my_custom_fonts');
/**********************************declare woocommerce support************************************************/
add_action( 'after_setup_theme', 'woocommerce_support' );
function woocommerce_support() {
    add_theme_support( 'woocommerce' );
}
/***********************************logout home redirect*****************************************************/
add_action('wp_logout',create_function('','wp_redirect(home_url());exit();')); 

/************************************wc i18n******************************************************************/
function weg_localisation() {
    unload_textdomain('woocommerce');
    load_textdomain('woocommerce', get_stylesheet_directory() . '/woocommerce/i18n/languages/woocommerce-el.mo');
}
add_action('init', 'weg_localisation');
/*************************************customize password strength*************************************************/
function custom_fix_forpassjs(){
	wp_deregister_script('wc-password-strength-meter');
	wp_dequeue_script( 'wc-password-strength-meter' );	
	
	wp_register_script('wc-password-strength-meter', get_stylesheet_directory_uri() . '/woocommerce/assets/js/frontend/password-strength-meter.min.js', array( 'jquery', 'password-strength-meter'));
	if (is_page(array('logariasmos-mou','my-account','tameio','checkout')))
	wp_enqueue_script('wc-password-strength-meter');
}
add_action('wp_enqueue_scripts','custom_fix_forpassjs');
/****************************************clean and clear...*******************************************************/
function my_site_WI_dequeue_script() {
	wp_dequeue_script( 'profitmag-bxslider-js' );
	wp_dequeue_script( 'profitmag-slicknav-js' );
}
 
add_action( 'wp_print_scripts', 'my_site_WI_dequeue_script', 100 );
function re_enqueue_the_fixed_script() {
	wp_enqueue_script('profitmag-child-slicknav-js', get_stylesheet_directory_uri() . '/js/jquery.slicknav.min.js',array() );
}
add_action( 'wp_print_scripts', 're_enqueue_the_fixed_script', 150 );

function give_dequeue_plugin_css() {
	wp_dequeue_style('profitmag-bxslider-style');
	wp_deregister_style('profitmag-bxslider-style');
	wp_dequeue_style('font-awesome');
	wp_deregister_style('font-awesome');
	wp_dequeue_style('profitmag-font-awesome');
	wp_deregister_style('profitmag-font-awesome');
	wp_dequeue_style('profitmag-color-scheme');
	wp_deregister_style('profitmag-color-scheme');
	wp_dequeue_style('wpsm_ac-sh-font-awesome-front');
	wp_deregister_style('wpsm_ac-sh-font-awesome-front');
	wp_dequeue_style('wpsm_faq-font-awesome-front');
	wp_deregister_style('wpsm_faq-font-awesome-front');
}
add_action('wp_enqueue_scripts','give_dequeue_plugin_css', 100);
function enqueue_our_required_stylesheets(){
	wp_enqueue_style('font-awesome', get_stylesheet_directory_uri() . '/css/font-awesome.min.css'); 
}
add_action('wp_enqueue_scripts','enqueue_our_required_stylesheets',150);
/*************************Hide shipping rates when free shipping is available*****************************/
add_filter( 'woocommerce_package_rates', 'hide_shipping_when_free_is_available', 10, 2 );
function hide_shipping_when_free_is_available( $rates, $package ) {
  	if ( isset( $rates['free_shipping'] ) ) {
  		unset( $rates['flat_rate'] );
	}
	return $rates;
}
add_filter( 'woocommerce_available_payment_gateways', 'my_custom_available_payment_gateways' );
function my_custom_available_payment_gateways( $gateways ) {
	$chosen_shipping_rates = WC()->session->get( 'chosen_shipping_methods' );
	if ( ! in_array( 'local_pickup', $chosen_shipping_rates ) ) :
		unset( $gateways['pis'] );
	endif;
	return $gateways;
}
/*******************************************redirect on pass change**************************************************/
function woocommerce_new_pass_redirect( $user ) {
	wp_redirect( get_permalink(woocommerce_get_page_id('myaccount')));
	exit;
}
add_action( 'woocommerce_customer_reset_password', 'woocommerce_new_pass_redirect' );
/******************************************add empty cart button************************************************************/
add_action('init', 'woocommerce_clear_cart_url');
function woocommerce_clear_cart_url() {
	global $woocommerce;
	if( isset($_REQUEST['clear-cart']) ) {
		$woocommerce->cart->empty_cart();
	}
}
/*******************Fix Shipping method not translated by Woo_Poly*********************************************************/
// In Cart and Checkout pages
function tlc_translate_shipping_label( $label ) {
	if (($label=='Δωρεάν Αποστολή')&&(pll_current_language()=='en'))
		return 'Free shipping';
	if (($label=='Παραλαβή από το κατάστημα')&&(pll_current_language()=='en'))
		return 'Pick-up from store';
	return $label;
}
add_filter( 'woocommerce_shipping_rate_label', 'tlc_translate_shipping_label', 10, 1 );

//Fix Payment gateway and respective description not translated by Woo_Poly
function tlc_translate_payment_gateway_title( $title, $id ) {
	if (($id=='piraeusbank_gateway')&&pll_current_language()=='en')
		return 'Credit/Debit Card <font style="font-style:italic;font-size:0.7em">*powered by</font>';
	if ($title=='Απευθείας Τραπεζική Μεταφορά'&&pll_current_language()=='en')
		return 'Bank transfer';
	if ($title=='Pay in Store'&&pll_current_language()=='el')
		return 'Πληρωμή στο κατάστημα';
	if ($id=='cod'&&pll_current_language()=='en')
		return 'Cash on delivery (+2€)';
	return $title;
}
add_filter( 'woocommerce_gateway_title', 'tlc_translate_payment_gateway_title', 10, 2 );
//geteway description
function tlc_translate_payment_gateway_description( $description, $id ) {
	if (($id=='piraeusbank_gateway')&&(pll_current_language()=='en'))
		return 'Pay with your credit or debit card.  It is fast safe and easy! <font style="font-style:italic;">You will be redirected to the banks safe enviroment to complete your payment. </font>';
	if (($id=='bacs')&&(pll_current_language()=='en'))
		return 'After placing your order our bank account details will be displayed. The order will be sent after we receive your payment in our bank account. Please write your order id in your bank transfer description. Thank you!';
	if (($id=='paypal')&&(pll_current_language()=='en'))
		return 'The favourite way to pay on the internet. Need more details? Click on the link on the right.';
	if (($id=='pis')&&(pll_current_language()=='en'))
		return 'Pay traditionally with cash in our store.';
	if (($id=='cod')&&(pll_current_language()=='en'))
		return 'Pay to the courier on delivery.';
    return $description;
}
add_filter( 'woocommerce_gateway_description', 'tlc_translate_payment_gateway_description', 10, 2 );

//Hide payment gateways based on shipping method
function payment_gateway_disable( $available_gateways ) {
	global $woocommerce;
	$mylocalvar=$woocommerce->cart->subtotal;
	if ($mylocalvar<24){
		unset($available_gateways['other_payment']);
		unset($available_gateways['cheque']);
	}
	if(pll_current_language()=='el'){
		unset($available_gateways['cheque']);
	}else if (isset($available_gateways['other_payment'])){
		unset($available_gateways['other_payment']);
	}
	if (isset($available_gateways['pis'])){
		unset($available_gateways['cheque']);
		unset($available_gateways['other_payment']);
	}
    return $available_gateways;
}
add_filter( 'woocommerce_available_payment_gateways', 'payment_gateway_disable' );

/***************************fix error strings translations**********************************************************/
function fix_error_strings_not_translating($translated, $original, $domain){
	global $errorstringfix;
	if ($original=='<strong>ERROR</strong>: The password you entered for the username %s is incorrect.')
		$errorstringfix=1;
	if ($original=='<strong>ERROR</strong>: Invalid username.')
		$errorstringfix=2;
	if ($original=='Username is required.')
		$errorstringfix=3;
	if ($original=='Password is required.')
		$errorstringfix=4;
	if ($original=='Check your e-mail for the confirmation link.')
		$errorstringfix=6;
	if ($original=='Passwords do not match.')
		$errorstringfix=7;
	if ($original=='Invalid username or e-mail.')
		$errorstringfix=8;
	if ($original=='Enter a username or e-mail address.')
		$errorstringfix=9;
	if ($original=='%s removed. %sUndo?%s')
		return 'Το %s Αφαιρέθηκε. %sΑναίρεση;%s';
	if ($original=='%s removed.')
		return 'Το καλάθι ενημερώθηκε.';
	if ($original=='Cart updated.'){
		$errorstringfix=10;
		return 'Το καλάθι ενημερώθηκε.';
	}
	return $translated;
	
}
add_filter('gettext','fix_error_strings_not_translating',10,3);
function set_errfix_cart(){
	global $errorstringfix;
	$errorstringfix=5;
}
add_action('wc_add_to_cart_message','set_errfix_cart');
/***********************************order status placeholder***************************************************/
function custom_admin_js() {
    echo '<script>document.getElementById("add_order_note").placeholder = "ACS κωδικός αποστολής ΣΚΕΤΟΣ!"</script>';
}
add_action('admin_footer', 'custom_admin_js');
/*********************************state/country not mandatory in checkout***************************************/
add_filter( 'woocommerce_billing_fields', 'wc_npr_filter_state_billing', 10, 1 );
add_filter( 'woocommerce_shipping_fields', 'wc_npr_filter_state_shipping', 10, 1 );
function wc_npr_filter_state_billing($address_fields){
	$address_fields['billing_state']['required'] = false;
	return $address_fields;
}
function wc_npr_filter_state_shipping($address_fields){
	$address_fields['shipping_state']['required'] = false;
	return $address_fields;
}
/*************************************redirection on account change***********************************************/
add_action('woocommerce_customer_save_address','my_redirect_to_accountf');
add_action('woocommerce_save_account_details','my_redirect_to_accountf');
function my_redirect_to_accountf(){
	if (pll_current_language()=='el'){
		wp_safe_redirect( 'https://www.silverfamily.gr/arxiki/logariasmos-mou/' );
		exit;
	}
}
/****************************************async javascript**********************************************************/
// add (async and) defer to javascripts
function wcs_defer_javascripts ( $url ) {
	if( !is_admin() ){
		if ( FALSE === strpos( $url, '.js' ) ) return $url;
		if ( strpos( $url, 'jquery.js' ) ) return $url;
		return "$url' defer='defer";
	}else{
		return $url;
	}
}
add_filter( 'clean_url', 'wcs_defer_javascripts', 11, 1 );
/****************************************single product order**********************************************************/
remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_title', 5 );
remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_rating', 10 );
remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_price', 10 );
remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_excerpt', 20 );
remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_meta', 40 );
/*********************************************emails subject fix hard***************************************************/
add_filter('woocommerce_email_subject_customer_completed_order', 'change_completed_email_subject', 1, 2);
function change_completed_email_subject( $subject, $order ) {
	global $headermailf;
	if (pll_get_post_language($order->id)=='en'){
		$headermailf = 'Your order has been completed';
	}else{
		$headermailf = 'Η παραγγελία σας ολοκληρώθηκε';
	}
	$subject = sprintf( 'Silverfamily - %s', $headermailf);
	return $subject;
}
add_filter('woocommerce_email_subject_customer_processing_order', 'change_processing_email_subject', 1, 2);
function change_processing_email_subject( $subject, $order ) {
	global $headermailf;
	if (pll_get_post_language($order->id)=='en'){
		$headermailf = 'Your order has been received';
	}else{
		$headermailf = 'Η παραγγελία σας έχει καταχωρηθεί';
	}
	$subject = sprintf( 'Silverfamily - %s', $headermailf);
	return $subject;
}
add_filter('woocommerce_email_subject_customer_refunded_order', 'change_refund_email_subject', 1, 2);
function change_refund_email_subject( $subject, $order ) {
	global $headermailf;
	if (pll_get_post_language($order->id)=='en'){
		$headermailf = 'Your order has been refunded';
	}else{
		$headermailf = 'Έχει γίνει επιστροφή του ποσού της παραγγελίας σας';
	}
	$subject = sprintf( 'Silverfamily - %s', $headermailf);
	return $subject;
}
add_filter('woocommerce_email_subject_customer_reset_password', 'change_password_email_subject', 1, 2);
function change_password_email_subject( $subject, $order ) {
	global $headermailf;
	$headermailf = 'Επαναφορά κωδικού/Reset Password';
	$subject = sprintf( 'Silverfamily - %s', $headermailf);
	return $subject;
}
add_filter('woocommerce_email_subject_customer_new_account', 'change_new_email_subject', 1, 2);
function change_new_email_subject( $subject, $order ) {
	global $headermailf;
	$headermailf = 'Νεος λογαριασμος/New account';
	$subject = sprintf( 'Silverfamily - %s', $headermailf);
	return $subject;
}
remove_action('woocommerce_checkout_after_customer_details', array($newsletters_woocommerce, 'woo_checkout_after_customer_details'), 10, 1);
add_action('woocommerce_checkout_after_order_review', array($newsletters_woocommerce, 'woo_checkout_after_customer_details'), 10, 2);


?>