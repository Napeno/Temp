<?php
/*
Plugin Name: WooCommerce Quote or Enquiry Contact Form 7
Description: A plugin to add product enquiry button with contact form 7 
Author: Geek Code Lab
Version: 2.9
WC tested up to: 8.3.0
Author URI: https://geekcodelab.com/
*/

if(!defined('ABSPATH')) exit;

define("WQOECF_BUILD",2.9);

if(!defined("WQOECF_PLUGIN_DIR_PATH"))
	define("WQOECF_PLUGIN_DIR_PATH",plugin_dir_path(__FILE__));	
	
if(!defined("WQOECF_PLUGIN_URL"))
	define("WQOECF_PLUGIN_URL",plugins_url().'/'.basename(dirname(__FILE__)));	


require_once( WQOECF_PLUGIN_DIR_PATH .'functions.php' );
include( WQOECF_PLUGIN_DIR_PATH . 'enquiry.php' );


add_action('admin_menu', 'wqoecf_admin_menu_quote_or_enquiry_contact_form' );

add_action( 'wp_enqueue_scripts', 'wqoecf_include_front_script' );

add_action('admin_print_styles', 'wqoecf_admin_styles');
 
register_activation_hook( __FILE__, 'wqoecf_plugin_active_quote_or_enquiry_contact_form' );

function wqoecf_plugin_active_quote_or_enquiry_contact_form(){
	$options_db =  wqoecf_quote_enquiry_options();
	if(empty($options_db))
	{
		do_action( 'wp_default_color_text_options' );
	}
}
/** Trigger an admin notice if WooCommerce or Contact Form 7 is not installed.*/
if ( ! function_exists( 'wqoecf_install_require_plugins_admin_notice' ) ) {
	function wqoecf_install_require_plugins_admin_notice() {
		if ( ! function_exists( 'WC' ) || ! is_plugin_active( 'contact-form-7/wp-contact-form-7.php' ) ) { ?>
			<div class="error">
				<p>
					<?php
					// translators: %s is the plugin name.
					echo esc_html( sprintf( __( '%s is enabled but not effective. It requires both two plugins WooCommerce and Contact Form 7 in order to work.' ), 'WooCommerce Quote or Enquiry Contact Form 7' ) );
					?>
				</p>
			</div>
		<?php
		}
	}
}
add_action( 'admin_notices', 'wqoecf_install_require_plugins_admin_notice' );

//Set default values of color && text
function wqoecf_default_color_text_options(){
	$btntext="Enquiry";
	$btncolor="#289dcc";
    $options['button_text'] = $btntext;
	$options['button_color'] = $btncolor;
	$options['product_single_page'] = 'on';
	$options['product_list_page'] = 'on';
	update_option('wqoecf_quote_or_enquiry_settings', $options);
}
add_action( 'wp_default_color_text_options', 'wqoecf_default_color_text_options' );


function wqoecf_plugin_add_settings_link( $links ) { 
	$support_link = '<a href="https://geekcodelab.com/contact/"  target="_blank" >' . __( 'Support' ) . '</a>'; 
	array_unshift( $links, $support_link );	

	$settings_link = '<a href="admin.php?page=wqoecf-quote-or-enquiry-contact-form">' . __( 'Settings' ) . '</a>'; 	
	array_unshift( $links, $settings_link );	

	return $links;	
}

$plugin = plugin_basename( __FILE__ );
add_filter( "plugin_action_links_$plugin", 'wqoecf_plugin_add_settings_link');

function wqoecf_include_front_script() {
	wp_register_style("wqoecf-front-woo-quote", WQOECF_PLUGIN_URL."/assets/css/wqoecf-front-style.css", array(), WQOECF_BUILD);  
	wp_register_script("wqoecf-front-woo-quote", WQOECF_PLUGIN_URL."/assets/js/wqoecf-front-script.js", array(), WQOECF_BUILD, true);

	if( is_woocommerce() ) {
		wp_enqueue_style( 'wqoecf-front-woo-quote' );
		wp_enqueue_script( 'wqoecf-front-woo-quote' );
	}	
}
function wqoecf_admin_styles() {
	if( is_admin() ) {
		$css=WQOECF_PLUGIN_URL."/assets/css/wqoecf_admin_style.css";	
		wp_enqueue_style('wqoecf-admin-style.css', $css, array(), WQOECF_BUILD);
		wp_enqueue_script( 'wp-color-picker' );
		 // Add the color picker css file       
        wp_enqueue_style( 'wp-color-picker' ); 
	}
}
 
function wqoecf_admin_menu_quote_or_enquiry_contact_form(){
	
	add_submenu_page( 'woocommerce','Quote Or Enquiry Contact Form 7', 'Quote Or Enquiry Contact Form 7', 'manage_options', 'wqoecf-quote-or-enquiry-contact-form', 'wqoecf_quote_or_enquiry_contact_form_page_setting');

}

function wqoecf_quote_or_enquiry_contact_form_page_setting() {
	if(!current_user_can('manage_options') ){
		wp_die( __('You do not have sufficient permissions to access this page.') );
	}
	include( WQOECF_PLUGIN_DIR_PATH . 'options.php' );	
}
function wqoecf_main()
{

	$options =  wqoecf_quote_enquiry_options();
	$status="";
	$contactform="";
	$allow_user = "";
	$product_single_page = "";
	$product_list_page = "";
	$single_page="";
	$list_page="";
	if(isset($options['status'])){
		$status = $options['status'];
	}
	if(isset($options['contact_form7'])){
		$contactform = $options['contact_form7'];
	}
	if(isset($options['allow_user'])){
		$allow_user = $options['allow_user'];
	}
	if(isset($options['product_single_page'])){
		$product_single_page = $options['product_single_page'];
	}
	if(isset($options['product_list_page'])){
		$product_list_page = $options['product_list_page'];
	}
	if($status=='on' && !empty($contactform)){
		$options_status =  wqoecf_quote_enquiry_options();
		if(isset($options['product_single_page'])){
		$single_page = $options_status['product_single_page'];
		}
		if(isset($options['product_list_page'])){
		$list_page = $options_status['product_list_page'];
		}
		if($list_page == 'on'){  
			if($allow_user != 'on'){
				if(is_user_logged_in()){
			 		add_filter( 'woocommerce_loop_add_to_cart_link', 'wqoecf_shop_page_enquiry_button', 10, 2 );
				}
			}else{
					add_filter( 'woocommerce_loop_add_to_cart_link', 'wqoecf_shop_page_enquiry_button', 10, 2 );
			}
		}

		if($single_page == 'on'){  
			if($allow_user != 'on'){
				if(is_user_logged_in()){
			 		add_action( 'woocommerce_single_product_summary', 'wqoecf_single_page_enquiry_button', 30 );
					add_action('wp','wqoecf_single_page_remove_add_cart');
				}
			}else{
					add_action( 'woocommerce_single_product_summary', 'wqoecf_single_page_enquiry_button', 30 );
					add_action('wp','wqoecf_single_page_remove_add_cart');
			}
		}
		
	}	
}

add_action("init","wqoecf_main");
function wqoecf_shop_page_enquiry_button( $button, $product  ) {
	global $post;

	if ( ! wp_script_is( 'enqueued', 'wqoecf-front-woo-quote' ) ) {
		wp_enqueue_style( 'wqoecf-front-woo-quote' );
		wp_enqueue_script( 'wqoecf-front-woo-quote' );
	}	
	

	$btntext="";
	$options= wqoecf_quote_enquiry_options();
	if(isset($options['button_text'])){
		$btntext = $options['button_text'];
	}
	
	$disable_form=get_option_quote_wqoecf_disable_form($product->get_id());
	if($disable_form!='yes')
	{
		global $post, $product;
		$pro_title = get_the_title($post->ID);
		$product_id = $product->get_id();
		$price = get_post_meta($product_id, '_price', true);
		$button = '<a class="wqoecf_enquiry_button" href="javascript:void(0)"  data-product-title="'.$pro_title.'" data-product-price="'.$price.'" data-product-id="'.$product_id.'"  >' . $btntext . '</a>';
		

	}
    return $button;

	
}

function wqoecf_single_page_enquiry_button(){
	if ( ! wp_script_is( 'enqueued', 'wqoecf-front-woo-quote' ) ) {
		wp_enqueue_style( 'wqoecf-front-woo-quote' );
		wp_enqueue_script( 'wqoecf-front-woo-quote' );
	}	
	
	$disable_form=get_option_quote_wqoecf_disable_form(get_the_ID());
	$btntext="";
	$options= wqoecf_quote_enquiry_options();

	if(isset($options['button_text'])){
		$btntext = $options['button_text'];
	}
	
	if($disable_form!='yes')
	{
		global $post, $product;
		$pro_title = get_the_title($post->ID);
		$product_id = $product->get_id();
		$price = get_post_meta($product_id, '_price', true);
		echo '<a class="wqoecf_enquiry_button" href="javascript:void(0)"  data-product-title="'.$pro_title.'" data-product-price="'.$price.'" data-product-id="'.$product_id.'"  >' . $btntext . '</a>';
		

	} 
	
} 
function wqoecf_single_page_remove_add_cart(){

	$product_id = get_the_ID(); // the ID of the product to check
	$_product = wc_get_product( $product_id );
	$disable_form=get_option_quote_wqoecf_disable_form( get_the_ID() ); 

	if($disable_form!='yes' && !empty($_product)){		
		if( $_product->is_type( 'simple' ) ) {
			remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30 );			
		}
		remove_action( 'woocommerce_single_variation', 'woocommerce_single_variation_add_to_cart_button', 20 );
	}
}


add_action("wp_footer","wqoecf_quote_enquiry_script");


function wqoecf_quote_enquiry_script()
{
	$options	= wqoecf_quote_enquiry_options();
	$form_title		= "Product Enquiry";

	if(isset($options['contact_form7']))			$contactform 	= $options['contact_form7'];	
	if (isset($options['wqoecf_form_title'])) 		$form_title 	= $options['wqoecf_form_title'];
	
	?>
	<div class="wqoecf-pop-up-box" style="display: none;" data-loader-path="<?php echo WQOECF_PLUGIN_URL; ?>/assets/images/ajax-loader.gif">
		<!-- <img class="wqoecf_close" src="<?php echo WQOECF_PLUGIN_URL; ?>/assets/images/close.png" onclick="wqoecf_hide()"> -->

		<button class="wqoecf_close" onclick="wqoecf_hide()"><span></span><span></span></button>
		<div>
			<p class="wqoecf_form_title"><?php echo $form_title; ?></p>
			<?php echo do_shortcode('[contact-form-7 id="'.$contactform.'"]'); ?>
		</div>
	</div>
	<?php
}
  
add_action('wp_head','wqoecf_set_button_color');
function wqoecf_set_button_color(){?>
	<style>
		<?php 
		$options =  wqoecf_quote_enquiry_options();
		$btncolor="";
		if(isset($options['button_color'])){
			$btncolor = $options['button_color'];
			?>
			.woocommerce a.wqoecf_enquiry_button {
			background-color: <?php echo $btncolor; ?>;
			}
		<?php
		}  ?>
	</style> 
	<?php 
}

/** Shortcode for Enquiry button */
add_shortcode( 'wqoecf_button_quote', 'wqoecf_req_button_quote' );
function wqoecf_req_button_quote($atts, $content = null) {
	if ( ! wp_script_is( 'enqueued', 'wqoecf-front-woo-quote' ) ) {
		wp_enqueue_style( 'wqoecf-front-woo-quote' );
		wp_enqueue_script( 'wqoecf-front-woo-quote' );
	}

    $args = shortcode_atts( array( 'product' => false ), $atts );

    ob_start();

    wqoecf_render_button( $args['product'], $args );

    return ob_get_clean();
}

function wqoecf_render_button( $product_id = false, $args = array() ) {
	$btntext = '';
	$options= wqoecf_quote_enquiry_options();
    if ( ! $product_id ) {
        global $product, $post;
        if( !$product instanceof WC_Product && $post instanceof WP_Post){
            $product = wc_get_product( $post->ID);
        }
    } else {
        $product = wc_get_product( $product_id );
    }

    global $woocommerce_loop;

	if($product) {
		$product_id = $product->get_id();
		$price = get_post_meta($product_id, '_price', true);
		$template_button = 'add-to-quote.php';
		$wcloop_name = ! is_null( $woocommerce_loop ) && ! is_null( $woocommerce_loop['name'] ) ? $woocommerce_loop['name'] : '';

		$pro_title = get_the_title($product_id);

		if(isset($options['button_text'])){
			$btntext = $options['button_text'];
		}

		$button = '<a class="wqoecf_enquiry_button" href="javascript:void(0)" data-product-title="'.$pro_title.'" data-product-price="'.$price.'" data-product-id="'.$product_id.'"  >' . $btntext . '</a>';

		echo $button;
	}
}

/**
 * Added HPOS support for woocommerce
 */
add_action( 'before_woocommerce_init', 'wqoecf_before_woocommerce_init' );
function wqoecf_before_woocommerce_init() {
    if ( class_exists( \Automattic\WooCommerce\Utilities\FeaturesUtil::class ) ) {
		\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', __FILE__, true );
	}
}