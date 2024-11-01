<?php

namespace TrustMeUp\Woocommerce;

use TrustMeUp\Config;
use TrustMeUp\Helpers;
use TrustMeUp\Cart_Discounter;
use TrustMeUp\API_TrustMeUp;

defined( 'ABSPATH' ) || exit;

add_action( 'init', __NAMESPACE__ . '\\store_init_session' );
function store_init_session() {
    if ( ! session_id() ) {
        session_start();
    }
}

/**
 * Store customer OTP passed via the PAC Store using a ?otp= parameter.
 *
 * @return void
 */
function store_customer_otp() {
	if ( !array_key_exists('otp', $_GET)) {
		return;
	}
        if ( ! session_id() ) {
            session_start();
        }

        $otp =  filter_var($_GET['otp'], FILTER_SANITIZE_STRING);
       
	setcookie( Config::OTP_COOKIE_NAME, $otp, strtotime( '+1 day' ), '/' );
	//setcookie( 'tmu_popup_close', 0, strtotime( '+1 day' ), '/',  filter_var($_SERVER['HTTP_HOST'], FILTER_VALIDATE_DOMAIN));
        $_SESSION['tmu_popup_close'] = 0;
	$url = remove_query_arg( 'otp', Helpers\get_current_url() );

	wp_safe_redirect( $url );
	exit();
}
add_action( 'template_redirect', __NAMESPACE__ . '\\store_customer_otp' );


/**
 * Display customer poup message.
 *
 * @return void
 */
function display_customer_noification_popup() {	
	
        if ( ! session_id() ) {
            session_start();
        }
        $wordpress_trustmeup_otp = (array_key_exists(Config::OTP_COOKIE_NAME, $_COOKIE) && filter_var($_COOKIE[Config::OTP_COOKIE_NAME], FILTER_SANITIZE_STRING)) ? filter_var($_COOKIE[Config::OTP_COOKIE_NAME], FILTER_SANITIZE_STRING) :'';
	
        if(array_key_exists('tmu_popup_close', $_SESSION)){
        $tmu_popup_close = filter_var($_SESSION['tmu_popup_close'], FILTER_SANITIZE_STRING);
	if( $wordpress_trustmeup_otp != '' && $tmu_popup_close != 1 ){ ?>
		<div id="tmu-popup">
            <div class="tmu-container">
                <div class="tmu-row">
                    <div class="tmu-popup-logo">
                        <svg width="305" height="61" viewBox="0 0 305 61" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M107.093 42.737C108.609 42.737 109.764 42.5521 110.378 42.4412V38.1158H110.089C109.728 38.1527 109.15 38.1897 108.789 38.1897C107.454 38.1897 106.66 37.783 106.66 36.3042V26.5442H110.414V22.8473H106.66V16.5624H101.101V22.8473H98.2136V26.5442H101.101V37.5612C101.101 41.5539 103.52 42.737 107.093 42.737ZM122.65 42.5152V33.3836C122.65 29.5388 124.671 27.5794 127.739 27.6164C128.136 27.6164 128.533 27.6533 128.93 27.7273H129.075V22.6624C128.822 22.5515 128.425 22.5145 127.883 22.5145C125.501 22.5145 123.769 23.6236 122.541 26.3594H122.433V22.8473H116.983V42.5152H122.65ZM141.996 43.0697C144.631 43.0697 146.364 41.9976 147.772 39.7055H147.88V42.5152H153.258V22.8473H147.663V33.9752C147.663 36.4151 146.256 38.1897 143.982 38.1897C141.96 38.1897 140.914 36.9327 140.914 34.7515V22.8473H135.247V35.8606C135.247 40.1861 137.629 43.0697 141.996 43.0697ZM168.598 43.1436C173.579 43.1436 177.261 40.9255 177.261 36.6739C177.261 31.72 173.363 30.7958 169.934 30.1673C167.299 29.6867 165.205 29.4648 165.205 27.9861C165.205 26.7291 166.324 25.9897 168.057 25.9897C169.862 25.9897 170.944 26.6921 171.305 28.097H176.575C176.07 24.6588 173.399 22.2927 168.057 22.2927C163.545 22.2927 159.972 24.4 159.972 28.4297C159.972 33.0139 163.437 33.9012 166.866 34.5667C169.537 35.0842 171.811 35.3061 171.811 37.0806C171.811 38.4855 170.656 39.3358 168.634 39.3358C166.433 39.3358 165.061 38.3006 164.736 36.4521H159.394C159.683 40.4079 163.04 43.1436 168.598 43.1436ZM190.905 42.737C192.421 42.737 193.576 42.5521 194.189 42.4412V38.1158H193.901C193.54 38.1527 192.962 38.1897 192.601 38.1897C191.266 38.1897 190.472 37.783 190.472 36.3042V26.5442H194.225V22.8473H190.472V16.5624H184.913V22.8473H182.025V26.5442H184.913V37.5612C184.913 41.5539 187.331 42.737 190.905 42.737ZM204.585 42.5152V30.2782C204.585 27.2467 207.075 25.0655 209.385 25.0655C211.37 25.0655 212.706 26.5073 212.706 28.9473V42.5152H216.027V30.2782C216.027 27.2467 218.156 25.0655 220.611 25.0655C222.56 25.0655 224.112 26.5073 224.112 28.9473V42.5152H227.469V28.6515C227.469 24.5109 225.014 22.1818 221.477 22.1818C219.131 22.1818 216.857 23.4018 215.485 25.8788H215.413C214.619 23.4388 212.742 22.1818 210.396 22.1818C207.905 22.1818 205.848 23.4758 204.657 25.62H204.549V22.6624H201.228V42.5152H204.585ZM244 43.0697C248.476 43.0697 251.58 40.5558 252.446 36.8588H249.162C248.548 38.9661 246.707 40.26 244.036 40.26C240.282 40.26 238.225 37.3024 237.972 33.4206H252.843C252.843 30.0564 252.049 27.2097 250.461 25.2503C248.945 23.3279 246.707 22.1818 243.82 22.1818C238.261 22.1818 234.543 26.84 234.543 32.6073C234.543 38.4115 238.044 43.0697 244 43.0697ZM249.27 30.9436H238.044C238.478 27.4315 240.318 24.8436 243.82 24.8436C247.176 24.8436 249.089 27.1358 249.27 30.9436Z" fill="#000008"/>
                            <path d="M266.198 43.0697C268.833 43.0697 270.566 41.9976 271.973 39.7055H272.082V42.5152H277.46V22.8473H271.865V33.9752C271.865 36.4152 270.457 38.1897 268.183 38.1897C266.162 38.1897 265.115 36.9327 265.115 34.7515V22.8473H259.448V35.8606C259.448 40.1861 261.831 43.0697 266.198 43.0697ZM290.49 49.0588V44.3636C290.49 42.2564 290.382 40.8145 290.309 40.0752H290.382C291.501 41.9976 293.522 43.1436 296.049 43.1436C301.174 43.1436 304.495 39.077 304.495 32.6812C304.495 26.6551 301.318 22.2927 296.229 22.2927C293.594 22.2927 291.681 23.4018 290.382 25.657H290.273V22.8473H284.823V49.0588H290.49ZM294.713 38.6333C291.825 38.6333 290.309 36.2303 290.309 32.8661C290.309 29.5388 291.717 26.9139 294.641 26.9139C297.42 26.9139 298.756 29.3539 298.756 32.8661C298.756 36.3782 297.276 38.6333 294.713 38.6333Z" fill="#1D9EBC"/>
                            <path d="M37.005 36.7584L37.1201 36.2871L36.801 36.1132C35.026 35.0746 33.8294 33.1479 33.8259 30.9372L33.8347 30.609C34.0023 27.564 36.5336 25.0351 39.5968 24.9281H39.6604L39.7331 24.9358L39.8073 24.9413L39.8815 24.9358L39.955 24.9281H40.0177L40.3439 24.9488C43.3676 25.2263 45.7932 27.8577 45.7887 30.9372L45.7759 31.3242C45.635 33.4994 44.3363 35.3551 42.4954 36.2871L42.6104 36.7584H51.5527L51.5636 36.9033C51.5898 37.0496 51.6621 37.2014 51.784 37.3704L52.4918 38.3718C52.9576 39.0443 53.4113 39.7259 53.8548 40.414L54.1329 40.8527C55.5057 43.053 56.5891 45.3636 56.5099 48.1117L56.4749 48.5824C56.4442 49.054 56.4186 49.5271 56.3287 49.986L56.2274 50.4755C55.7959 52.4224 55.0425 54.2321 53.6662 55.6886L53.2579 56.1017C51.3228 57.9725 48.9742 59.0503 46.327 59.4622L45.8056 59.5288C44.2478 59.6843 42.7472 59.4395 41.264 58.7869L40.5623 58.4635C38.9437 57.6798 37.4529 56.6942 36.0597 55.5485L34.3467 54.1462C32.0622 52.2769 29.7831 50.401 27.5974 48.4158L25.8813 46.821C23.6138 44.6703 21.4182 42.4341 19.1685 40.2625L18.1315 39.2899C17.4332 38.6492 16.7262 38.0182 16.0405 37.3647L15.9015 37.2246C15.7388 37.0476 15.666 36.9058 15.6764 36.7584H36.775L34.3698 46.8686L34.3355 47.0654C34.2332 47.9794 34.9242 48.8091 35.8441 48.8091H43.2892L43.4828 48.7967C44.3679 48.6819 44.983 47.7907 44.7645 46.8686L42.3593 36.7584H37.005ZM36.0928 0L36.7145 0.00534269C56.3626 0.343262 72.1893 16.668 72.1893 36.7584H51.571L51.5899 36.6142C51.6234 36.4694 51.7005 36.3218 51.8184 36.1599L52.5631 35.1207C53.0535 34.4234 53.5311 33.7169 53.9918 33.0002L54.2939 32.5239C55.385 30.7693 56.2793 28.9214 56.4694 26.8021L56.5083 26.2054C56.5813 24.4219 56.2596 22.699 55.5431 21.0359L55.332 20.5715C53.8039 17.3824 51.2175 15.4923 47.8751 14.5954L47.3615 14.4703C44.2911 13.8 41.4209 14.5706 38.7936 16.3239L37.5219 17.2085C36.2632 18.1144 35.037 19.0739 33.7836 19.9903L32.6166 20.8736C29.1618 23.5761 26.1036 26.7325 22.9564 29.7885L21.6032 31.068C20.2356 32.3326 18.832 33.5597 17.4572 34.8178L16.738 35.4912L16.0306 36.1788L15.9011 36.3142C15.7469 36.4857 15.6662 36.6236 15.6558 36.7584H0C0 16.4565 16.1608 0 36.0928 0Z" fill="#1D9EBC"/>
                        </svg>
                    </div>
                    <div class="tmu-popup-content">
                        <h2><?php esc_html_e('Your PAC discount is applied here!', 'trustmeup'); ?></h2>
                        <p><?php esc_html_e('Shop with confidence and proceed to the checkout to see your PAC discount.', 'trustmeup'); ?></p>
                    </div>
                    <div class="tmu-popup-close">
                        <button class="tmu-popup-close-btn"></button>
                    </div>
                </div>
            </div>
        </div>
	<?php }
        }

	
}
add_action( 'wp_footer', __NAMESPACE__ . '\\display_customer_noification_popup' );

/**
 * Enqueue popup css and js.
 *
 * @return void
 */

function tmu_user_scripts() {
    
    wp_enqueue_style( 'tmu-popup',  TMU_URL . "assets/popup.css");
    wp_enqueue_script( 'tmu-popup', TMU_URL . "assets/popup.js", array(), null, true );
    // Localize the script with new data
	$info_array = array(
		'url' =>admin_url('admin-ajax.php'),		
	);
	wp_localize_script( 'tmu-popup', 'ajax',  $info_array);
}

add_action( 'wp_enqueue_scripts', __NAMESPACE__ . '\\tmu_user_scripts' );

/**
 * Popup close ajax.
 *
 * @return void
 */

function tmu_popupclose_ajax_function() {    
    
    if(array_key_exists('popup_close', $_POST) && filter_var($_POST['popup_close'], FILTER_SANITIZE_STRING) == 1 ){
        $popClose = filter_var($_POST['popup_close'], FILTER_SANITIZE_STRING);
        
    	//setcookie( 'tmu_popup_close', $popClose, strtotime( '+1 day' ), '/',  filter_var($_SERVER['HTTP_HOST'], FILTER_VALIDATE_DOMAIN) );   	
        $_SESSION['tmu_popup_close'] = $popClose; 
        echo filter_var($_SESSION['tmu_popup_close'], FILTER_SANITIZE_STRING);
    }
    
    wp_die();
    
}

add_action( 'wp_ajax_nopriv_tmu_set_popup_close_cookie', __NAMESPACE__ . '\\tmu_popupclose_ajax_function' );
add_action( 'wp_ajax_tmu_set_popup_close_cookie', __NAMESPACE__ . '\\tmu_popupclose_ajax_function' );



//====================================================================
//                                                                    
//   ####  ##   ##  #####   ####  ##  ##   #####   ##   ##  ######  
//  ##     ##   ##  ##     ##     ## ##   ##   ##  ##   ##    ##    
//  ##     #######  #####  ##     ####    ##   ##  ##   ##    ##    
//  ##     ##   ##  ##     ##     ## ##   ##   ##  ##   ##    ##    
//   ####  ##   ##  #####   ####  ##  ##   #####    #####     ##    
//                                                                    
//====================================================================

/**
 * When WooCommerce calculates the grand total,
 * 1. construct TrustMeUp API cart when accessing the checkout page,
 * 2. Apply PAC discount to total.
 *
 * @param float $cart
 * @param WC_Cart $cart_object
 * @return double $total
 */
function construct_trustmeup_cart( $total, $cart ) {
	if (
		is_admin() && ! defined( 'DOING_AJAX' )
		|| ( ! is_checkout() && ! is_cart() )
		|| defined( 'TMU_CART_CONSTRUCTED' )
		|| ( class_exists( 'WC_Subscriptions_Cart' ) && \WC_Subscriptions_Cart::cart_contains_subscription() )
	) {
		return $total;
	}

	$discounter = new Cart_Discounter( $cart );
	$discounter->create_trustmeup_order();

	$trustmeup_order = $discounter->get_trustmeup_order();

	if ( $trustmeup_order ) {
		$total = $total - $trustmeup_order->pac_amount;
	}

	define( 'TMU_CART_CONSTRUCTED', true );

	return $total;
}
add_filter( 'woocommerce_calculated_total', __NAMESPACE__ . '\\construct_trustmeup_cart', 100, 2 );

/**
 * Void the TrustMeUp cart when the Woo Cart is emptied, so that the user PACs can be used immediately somewhere else.
 *
 * @return void
 */
function void_trustmeup_cart_when_wc_cart_is_emptied() {
	if ( WC()->cart->get_cart_contents_count() === 0 ) {
		Cart_Discounter::clear_user_cart();
	}
}
add_action( 'woocommerce_cart_item_removed', __NAMESPACE__ . '\\void_trustmeup_cart_when_wc_cart_is_emptied' );

/**
 * Display the PAC discount before the total.
 *
 * @return void
 */
function display_pac_discount() {
	$discounter      = new Cart_Discounter( WC()->cart );
	$trustmeup_order = $discounter->get_trustmeup_order();

	if ( ! $trustmeup_order ) {
		return;
	}

	ob_start();

	if ( isset( $trustmeup_order->max_pac_amount ) ) {
	?>
	<tr class="trustmeup-discount max-amount">
		<th><?php esc_html_e( 'PAC discount available', 'trustmeup' ); ?></th>
		<td data-title="<?php esc_attr_e( 'PAC discount available', 'trustmeup' ); ?>"><?php echo wc_price( $trustmeup_order->max_pac_amount * 1 ); ?></td>
	</tr>
	<?php
	}

	if ( isset( $trustmeup_order->user->pac_balance ) ) {
	?>
	<tr class="trustmeup-discount max-amount">
		<th><?php esc_html_e( 'Your PACs available', 'trustmeup' ); ?></th>
		<td data-title="<?php esc_attr_e( 'Your PACs available', 'trustmeup' ); ?>"><?php echo wc_price( $trustmeup_order->user->pac_balance * 1 ); ?></td>
	</tr>
	<?php
	}

	if ( isset( $trustmeup_order->pac_amount ) ) {
	?>
	<tr class="trustmeup-discount discount">
		<th><img class="trustmeup-icon" src="<?php echo TMU_URL . '/assets/images/trustmeup-icon.png'; ?>" style="display:inline-block; width: 1.2rem; height:.9rem; margin-right: .5rem; position: relative; top: .1rem;" /><?php esc_html_e( 'PAC discount', 'trustmeup' ); ?></th>
		<td data-title="💙 <?php esc_attr_e( 'PAC discount', 'trustmeup' ); ?>"><?php echo wc_price( $trustmeup_order->pac_amount * -1 ); ?></td>
	</tr>
	<?php
	}

	echo apply_filters( 'tmu/pac_discount_displayed', ob_get_clean(), 'checkout', $trustmeup_order );
}
add_action( 'woocommerce_review_order_before_order_total', __NAMESPACE__ . '\\display_pac_discount', 10 );
add_action( 'woocommerce_cart_totals_before_order_total', __NAMESPACE__ . '\\display_pac_discount', 10 );

//==================================================
//                                                  
//  ##     ##   #####   ######  ##   ####  #####  
//  ####   ##  ##   ##    ##    ##  ##     ##     
//  ##  ## ##  ##   ##    ##    ##  ##     #####  
//  ##    ###  ##   ##    ##    ##  ##     ##     
//  ##     ##   #####     ##    ##   ####  #####  
//                                                  
//==================================================

/**
 * Display the notice before the cart totals.
 *
 * @return void
 */
function display_notice_before_cart_totals() {
	$discounter      = new Cart_Discounter( WC()->cart );
	$trustmeup_order = $discounter->get_trustmeup_order();

	if ( ! $trustmeup_order ) {
		return;
	}

	if ( should_display_notice( $trustmeup_order ) ) {
		printf(
			'<div class="trustmeup-incentive-notice">
				<img class="trustmeup-icon" src="%2$s" />
				<p>%1$s</p>
			</div>',
			get_incentive_notice_message( $trustmeup_order ),
			TMU_URL . '/assets/images/trustmeup-icon.png'
		);

		if ( apply_filters( 'tmu/incentive_notice_css', true ) ) {
			echo '<style>
				div.trustmeup-incentive-notice { background: #fff; border: 1px solid #CACACA; color: #111; padding: 1rem; margin-bottom: 1rem; font-size: .8rem; }
				div.trustmeup-incentive-notice strong { display:block; color: #149DBD; font-size: 1.2rem; }
				div.trustmeup-incentive-notice a { font-weight:bold; color: #149DBD; }
				div.trustmeup-incentive-notice p { margin: 0; font-size: 1rem; }
				div.trustmeup-incentive-notice img.trustmeup-icon { width: 2.4rem; height:1.8rem; margin-bottom: 0; }
			</style>';
		}
	}
}
add_action( 'woocommerce_proceed_to_checkout', __NAMESPACE__ . '\\display_notice_before_cart_totals', 5 );

/**
 * Should we display the incentive notice on Cart/Checkout?
 *
 * @param object $trustmeup_order
 * @return boolean
 */
function should_display_notice( $trustmeup_order ) {
	if ( defined( 'TRUSTMEUP_ALWAYS_DISPLAY_NOTICE' ) && TRUSTMEUP_ALWAYS_DISPLAY_NOTICE ) {
		return true;
	}

	return isset( $trustmeup_order->donate_by_shopping, $trustmeup_order->donate_by_shopping->is_eligible ) && $trustmeup_order->donate_by_shopping->is_eligible;
}

/**
 * Get the notice message.
 *
 * @return void
 */
function get_incentive_notice_message( $trustmeup_order ) {
	if ( ! empty( $trustmeup_order->donate_by_shopping->campaign ) ) {
		$campaign_name = $trustmeup_order->donate_by_shopping->campaign->name_it;
		$language_code = substr( get_locale(), 0, 2 );
		$language_prop = "name_{$language_code}";

		if ( isset( $trustmeup_order->donate_by_shopping->campaign->{$language_prop} ) ) {
			$campaign_name = $trustmeup_order->donate_by_shopping->campaign->{$language_prop};
		}

		$html = sprintf(
			__( '<strong>Message from TrustMeUp: Donate By Shopping!</strong>
			You can still get a %1$s discount on this purchase by donating to your favourite Non-Profit Association <em>%3$s by %4$s</em>! <a href="%2$s">Donate now</a>.', 'trustmeup' ),
			wc_price( $trustmeup_order->donate_by_shopping->amount ),
			$trustmeup_order->donate_by_shopping->url,
			$campaign_name,
			$trustmeup_order->donate_by_shopping->partner->name
		);
	} else {
		$html = sprintf(
			__( '<strong>Message from TrustMeUp: Donate By Shopping!</strong>
			You can still get a %1$s discount on this purchase by donating to your favourite Non-Profit Association! <a href="%2$s">Donate now</a>.', 'trustmeup' ),
			wc_price( $trustmeup_order->donate_by_shopping->amount ),
			$trustmeup_order->donate_by_shopping->url
		);
	}

	return $html;
}

//==============================================
//                                              
//   #####   #####    ####    #####  #####    
//  ##   ##  ##  ##   ##  ##  ##     ##  ##   
//  ##   ##  #####    ##  ##  #####  #####    
//  ##   ##  ##  ##   ##  ##  ##     ##  ##   
//   #####   ##   ##  ####    #####  ##   ##  
//                                              
//==============================================

/**
 * Save TrustMeUp API data on the WooCommerce order (before PAC burning).
 *
 * @param \WC_Order $order
 * @return void
 */
function save_trustmeup_api_order_data_on_woocommerce_order( $order ) {
	// If the WC_Order already has the TMU order metadata, bail early.
	if ( ! empty( $order->get_meta( 'trustmeup_pending_order', true ) ) ) {
		return;
	}

	// Save data on the order.
	$trustmeup_order_key = WC()->session->get( 'trustmeup_order_key' );

	if ( ! empty( $trustmeup_order_key ) ) {
		$transient_name  = sprintf( '%1$s%2$s', Config::TRUSTMEUP_ORDER_TRANSIENT_PREFIX, $trustmeup_order_key );
		$trustmeup_order = get_transient( $transient_name );

		if ( ! empty( $trustmeup_order ) ) {
			// Save data on the order.
			$order->update_meta_data( 'trustmeup_pending_order', $trustmeup_order );
			$order->update_meta_data( 'trustmeup_pac_discount', $trustmeup_order->pac_amount );
			$order->add_order_note( sprintf( __( 'Saving TrustMeUp order metadata (PAC discount: %1$s).', 'trustmeup' ), wc_price( $trustmeup_order->pac_amount ) ) );
		}

		// Delete transient and session data.
		delete_transient( $transient_name );
		WC()->session->set( 'trustmeup_order_key', null );
	}

	$order->save();
}
add_action( 'woocommerce_checkout_order_created', __NAMESPACE__ . '\\save_trustmeup_api_order_data_on_woocommerce_order', 1, 1 );

/**
 * Burn PAC after an order has been placed and save TMU API data on the Order.
 *
 * @param integer $order_id
 * @return void
 */
function burn_pac_after_order_is_placed( $order_id ) {
	$order = wc_get_order( $order_id );

	// If this is not a TMU-powered order, ignore.
	if ( empty( $order->get_meta( 'trustmeup_pending_order', true ) ) ) {
		return;
	}

	// If the WC_Order already has the TMU order metadata, bail early.
	if ( ! empty( $order->get_meta( 'trustmeup_completed_order', true ) ) ) {
		return;
	}

	$api                   = new API_TrustMeUp();
	$trustmeup_final_order = $api->complete_order();

	if ( ! is_wp_error( $trustmeup_final_order ) ) {
           
            Helpers\debug('Hook for second api: woocommerce_order_status_completed,woocommerce_order_status_processing,woocommerce_payment_complete');
            Helpers\debug($trustmeup_final_order);
            
            $order->update_meta_data( 'trustmeup_completed_order', $trustmeup_final_order );
            $order->add_order_note( __( 'TrustMeUp order has been completed; PACs have been burned.', 'trustmeup' ) );
	
            
        }

	$order->save();
}

add_action( 'woocommerce_order_status_completed', __NAMESPACE__ . '\\burn_pac_after_order_is_placed', 1 );
add_action( 'woocommerce_order_status_processing', __NAMESPACE__ . '\\burn_pac_after_order_is_placed', 1 );
add_action( 'woocommerce_payment_complete', __NAMESPACE__ . '\\burn_pac_after_order_is_placed', 1 );

/**
* Create provision  for(freeze PACs)
*
* @param int $order_id
* @return void
*/
function create_initiate_provision($order_id){


        $api = new API_TrustMeUp();
        // Create provision (freeze PACs).
        $api->initiate_checkout(); 
        
}

add_action( 'woocommerce_checkout_create_order', __NAMESPACE__ . '\\create_initiate_provision',  20, 1  );

/**
* Release initiate provision for(freeze PACs)
*
* @param int $order_id
* @return void
*/
function release_initiate_provision($order_id){

    $order = wc_get_order( $order_id );
    // If this is not a TMU-powered order, ignore.
    if ( empty( $order->get_meta( 'trustmeup_pending_order', true ) ) ) {
            return;
    }

    $api = new API_TrustMeUp();

    $api->delete_past_orders();
    
}

add_action( 'woocommerce_order_status_pending', __NAMESPACE__ . '\\release_initiate_provision' , 1, 1 );
add_action( 'woocommerce_order_status_failed', __NAMESPACE__ . '\\release_initiate_provision' , 1, 1 );
//add_action( 'woocommerce_order_status_on-hold', __NAMESPACE__ . '\\release_initiate_provision' , 1, 1 );
