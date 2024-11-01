<?php

namespace TrustMeUp\Admin;

use \TrustMeUp\Config;
use \TrustMeUp\Options;
use \TrustMeUp\Scheduler;
use \TrustMeUp\Products_Connector;

defined( 'ABSPATH' ) || exit;

//=======================================================================================
//                                                                                       
//    ###    ####    ###    ###  ##  ##     ##        #####     ###     ####    #####  
//   ## ##   ##  ##  ## #  # ##  ##  ####   ##        ##  ##   ## ##   ##       ##     
//  ##   ##  ##  ##  ##  ##  ##  ##  ##  ## ##        #####   ##   ##  ##  ###  #####  
//  #######  ##  ##  ##      ##  ##  ##    ###        ##      #######  ##   ##  ##     
//  ##   ##  ####    ##      ##  ##  ##     ##        ##      ##   ##   ####    #####  
//                                                                                       
//=======================================================================================

/**
 * Register a new WP Mail Pro admin menu page.
 *
 * @return void
 */
function register_admin_menu_item() {
	$icon_base64 = 'data:image/svg+xml;base64,data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMTAwIiBoZWlnaHQ9IjgzIiB2aWV3Qm94PSIwIDAgMTAwIDgzIiB2ZXJzaW9uPSIxLjEiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyIgeG1sbnM6eGxpbms9Imh0dHA6Ly93d3cudzMub3JnLzE5OTkveGxpbmsiPgoJPHBhdGggZmlsbD0id2hpdGUiIGQ9Ik01MS4yNiw1MC45MDUgTDUxLjQyLDUwLjI1MjUgTDUwLjk3NzUsNTAuMDEyNSBDNDguNDUsNDguNTU1IDQ2Ljg3MjUsNDUuODE1IDQ2Ljg1NzUsNDIuODQ1IEw0Ni44Nyw0Mi4zODc1IEM0Ny4xMDI1LDM4LjE3MjUgNTAuNjA3NSwzNC42NyA1NC44NTI1LDM0LjUyMjUgTDU0Ljk0LDM0LjUyMjUgTDU1LjA0LDM0LjUzMjUgTDU1LjE0MjUsMzQuNTQgTDU1LjI0NSwzNC41MzI1IEw1NS4zNDc1LDM0LjUyMjUgTDU1LjQzNSwzNC41MjI1IEw1NS44ODUsMzQuNTQ3NSBDNjAuMDc1LDM0LjkzNSA2My40MzUsMzguNTggNjMuNDI3NSw0Mi44NDI1IEw2My40MSw0My4zOCBDNjMuMjAyNSw0Ni4zMzI1IDYxLjQ2NzUsNDguOTU3NSA1OC44Njc1LDUwLjI1MjUgTDU5LjAyNSw1MC45MDUgTDcxLjQxMjUsNTAuOTA1IEw3MS40Mjc1LDUxLjEwNSBDNzEuNDY1LDUxLjMxIDcxLjU2NSw1MS41MTc1IDcxLjczMjUsNTEuNzUyNSBMNzIuNzE1LDUzLjE0IEM3My4zNiw1NC4wNyA3My45OSw1NS4wMTUgNzQuNjAyNSw1NS45NjUgTDc0Ljk4NzUsNTYuNTc1IEM3Ni44ODc1LDU5LjYyMjUgNzguMzg3NSw2Mi44MjI1IDc4LjI4LDY2LjYyNzUgTDc4LjIzLDY3LjI3NzUgQzc4LjE5LDY3LjkzIDc4LjE1NSw2OC41ODc1IDc4LjAzLDY5LjIyNSBMNzcuODg3NSw2OS45IEM3Ny4yOSw3Mi41OTc1IDc2LjI0NzUsNzUuMTA1IDc0LjM0LDc3LjExNzUgTDczLjc3NSw3Ny42OTI1IEM3MS4wOTUsODAuMjgyNSA2Ny44NDI1LDgxLjc3NSA2NC4xNzUsODIuMzQ1IEw2My40NSw4Mi40Mzc1IEM2MS4zMDI1LDgyLjY1NSA1OS4xMzUsODIuMzAyNSA1Ny4xNiw4MS40MSBMNTYuMTksODAuOTYyNSBDNTMuOTYyNSw3OS44NzI1IDUxLjg3LDc4LjUxNzUgNDkuOTUyNSw3Ni45Mjc1IEw0Ny41Nzc1LDc0Ljk4MjUgQzQ0LjQxNSw3Mi4zOTUgNDEuMjU3NSw2OS43OTc1IDM4LjIyNzUsNjcuMDQ3NSBMMzUuODUyNSw2NC44Mzc1IEMzMi43MSw2MS44NjI1IDI5LjY3LDU4Ljc2NSAyNi41NTI1LDU1Ljc1NzUgTDI1LjExNzUsNTQuNDEgQzI0LjE1LDUzLjUyNSAyMy4xNjc1LDUyLjY0NzUgMjIuMjIsNTEuNzQ1IEwyMi4wMjc1LDUxLjU1IEMyMS44MDI1LDUxLjMwNSAyMS43MDI1LDUxLjEwNzUgMjEuNzE1LDUwLjkwNSBMNTAuOTQyNSw1MC45MDUgTDQ3LjYxLDY0LjkwNzUgTDQ3LjU2MjUsNjUuMTggQzQ3LjU1MjUsNjUuMjYyNSA0Ny41NDc1LDY1LjM0NSA0Ny41NDc1LDY1LjQzIEM0Ny41NDc1LDY2LjYxMjUgNDguNDk3NSw2Ny41ODc1IDQ5LjY1MjUsNjcuNTkyNSBMNTkuOTY1LDY3LjU5MjUgTDYwLjIzNSw2Ny41NzUgQzYxLjI4MjUsNjcuNDMyNSA2Mi4wNzI1LDY2LjUwNzUgNjIuMDcyNSw2NS40MjUgQzYyLjA3MjUsNjUuMjUgNjIuMDUsNjUuMDc1IDYyLjAxLDY0LjkwNSBMNTguNjc3NSw1MC45MDI1IEw1MS4yNiw1MC45MDI1IEw1MS4yNiw1MC45MDUgTDUxLjI2LDUwLjkwNSBaIE01MCwwIEw1MC44NiwwLjAwNzUgQzc4LjA3NSwwLjQ3NSAxMDAsMjMuMDgyNSAxMDAsNTAuOTA1IEw3MS40Mzc1LDUwLjkwNSBMNzEuNDYyNSw1MC43MDUgQzcxLjUyNSw1MC40NzUgNzEuNjMyNSw1MC4yNjI1IDcxLjc4LDUwLjA3NSBMNzIuODEyNSw0OC42Mzc1IEM3My40OTI1LDQ3LjY3MjUgNzQuMTU1LDQ2LjY5MjUgNzQuNzkyNSw0NS43IEw3NS4yMSw0NS4wNCBDNzYuNzIyNSw0Mi42MSA3Ny45Niw0MC4wNTI1IDc4LjIyNSwzNy4xMTc1IEw3OC4yNzc1LDM2LjI5IEM3OC4zOCwzMy44MyA3Ny45MjI1LDMxLjM4IDc2Ljk0LDI5LjEzIEw3Ni42NDc1LDI4LjQ4NzUgQzc0LjUzMjUsMjQuMDcyNSA3MC45NDc1LDIxLjQ1NSA2Ni4zMiwyMC4yMTI1IEw2NS42MDc1LDIwLjAzNzUgQzYxLjM1NSwxOS4xMTI1IDU3LjM3NzUsMjAuMTc3NSA1My43Mzc1LDIyLjYwNSBMNTEuOTc3NSwyMy44MyBDNTAuMjMyNSwyNS4wODUgNDguNTM1LDI2LjQxNSA0Ni43OTc1LDI3LjY4MjUgTDQ1LjE4MjUsMjguOTA1IEM0MC4zOTc1LDMyLjY0NzUgMzYuMTU3NSwzNy4wMiAzMS44LDQxLjI1MjUgTDI5LjkyNSw0My4wMjI1IEMyOC4wMzI1LDQ0Ljc3NSAyNi4wODc1LDQ2LjQ3NSAyNC4xODI1LDQ4LjIxNzUgTDIzLjE4NSw0OS4xNSBMMjIuMjA3NSw1MC4xMDI1IEwyMi4wMjc1LDUwLjI4NzUgQzIxLjgxMjUsNTAuNTI3NSAyMS43MDI1LDUwLjcxNzUgMjEuNjg3NSw1MC45MDI1IEw0LjQ0MDg5MjFlLTE1LDUwLjkwMjUgQzQuNDQwODkyMWUtMTUsMjIuNzkyNSAyMi4zODc1LDAgNDkuOTk3NSwwIEw1MCwwIFoiPjwvcGF0aD4KPC9zdmc+';

	add_menu_page(
		__( 'TrustMeUp', 'trustmeup' ),
		__( 'TrustMeUp', 'trustmeup' ),
		'manage_options',
		Config::ADMIN_PAGE_SLUG,
		__NAMESPACE__ . '\\output_admin_page',
		$icon_base64,
		'58.5'
	);
}
add_action( 'admin_menu', __NAMESPACE__ . '\\register_admin_menu_item' );

/**
 * Register a new WP Mail Pro admin menu page.
 *
 * @return void
 */
function output_admin_style() {
	$icon        = TMU_DIR . '/assets/images/trustmeup-icon.svg';
	$icon_base64 = 'data:image/svg+xml;base64,' . base64_encode( $icon );

	echo '<style>
		#adminmenu li.toplevel_page_trustmeup div.wp-menu-image { background-image: url("' . $icon_base64 . '") !important; }
		#adminmenu li.toplevel_page_trustmeup div.wp-menu-image:before { display:none !important; }
		</style>';
}
//add_action( 'admin_footer', __NAMESPACE__ . '\\output_admin_style' );

/**
 * Output the admin page content.
 *
 * @return void
 */
function output_admin_page() {
	if ( ! \TrustMeUp\meets_requirements() ) {
		return;
	}

	echo '<div id="tmu-admin-page-container"><span class="spinner is-active"></span></div>';
}

//===========================================================
//                                                           
//      ##   ####        ####      ###    ######    ###    
//      ##  ##           ##  ##   ## ##     ##     ## ##   
//      ##   ###         ##  ##  ##   ##    ##    ##   ##  
//  ##  ##     ##        ##  ##  #######    ##    #######  
//   ####   ####         ####    ##   ##    ##    ##   ##  
//                                                           
//===========================================================

/**
 * Data used by JS/React: main data.
 *
 * @param array $data
 * @return array
 */
function inject_js_main_data( $data ) {
	$data['data'] = [
		'api' => [
			'rest_url' => esc_url_raw( rest_url() ),
			'nonce'    => wp_create_nonce( 'wp_rest' ),
		],
		'current_tab' => isset( $_GET['tab'] ) ? sanitize_text_field( $_GET['tab'] ) : 'overview',
		'fields'      => [
			'api_environment'    => Options\get_plugin_option( 'api_environment', 'prod' ),
			'api_client_id'      => Options\get_plugin_option( 'api_client_id', '' ),
			'api_password'       => Options\get_plugin_option( 'api_password', '' ),
			'api_client_id_beta' => Options\get_plugin_option( 'api_client_id_beta', '' ),
			'api_password_beta'  => Options\get_plugin_option( 'api_password_beta', '' ),
			'merchant_token'     => Options\get_plugin_option( 'merchant_token', null ),
			'connected_products' => Products_Connector::get_trustmeup_products_with_sync_data(),
			'woo_products'       => get_woocommerce_products(),
		],
	];

	return $data;
}
add_filter( 'tmu/javascript_data', __NAMESPACE__ . '\\inject_js_main_data', 10, 1 );

/**
 * Get a list of WooCommerce products to be displayed in the Product Connector popup.
 *
 * @return array
 */
function get_woocommerce_products() {
	$products = wc_get_products( [
		'status'     => [ 'publish' ],
		'visibility' => 'visible',
		'limit'      => 9999,
	] );

	$products = array_map( function( $product ) {
		/**
		 * @var \WC_Product $product
		 */
		$image = wp_get_attachment_image_src( $product->get_image_id( 'woocommerce_thumbnail' ) );
		
		$cat_ids = $product->get_category_ids();
		$cat_names = [];
		foreach ( (array) $cat_ids as $cat_id) {
			$cat_term = get_term_by('id', (int)$cat_id, 'product_cat');
			if($cat_term){
				$cat_names[] = $cat_term->name;
			}
		}
		
		return (object) [
			'id'        => $product->get_id(),
			'name'      => $product->get_title(),
			'thumbnail' => $image ? $image[0] : wc_placeholder_img_src( 'woocommerce_thumbnail' ),
			'categories' => $cat_names,
		];
	}, $products );

	usort( $products, function( $a, $b ) {
		return strcmp( $a->name, $b->name );
	} );

	return $products;
}
