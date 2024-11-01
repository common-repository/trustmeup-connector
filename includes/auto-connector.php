<?php

namespace TrustMeUp\Auto_Connector;

use TrustMeUp\API_TrustMeUp;
use TrustMeUp\Config;
use TrustMeUp\Products_Connector;
use TrustMeUp\Helpers;
use TrustMeUp\Options;

defined( 'ABSPATH' ) || exit;

//=========================
//                         
//    ###    #####   ##  
//   ## ##   ##  ##  ##  
//  ##   ##  #####   ##  
//  #######  ##      ##  
//  ##   ##  ##      ##  
//                         
//=========================

/**
 * Does this TMU store has a "General Store Discount" product?
 *
 * @return false|string false if has no GSD, TMU product ID if it has one.
 */
function has_storewide_discount_product() {
	$products_list = Options\get_plugin_option( 'products_list', [] );

	if ( empty( $products_list ) ) {
		return false;
	}

	$tmu_product = array_filter( $products_list, function( $product ) {
		return strtolower( $product->name ) === strtolower( Config::STOREWIDE_DISCOUNT_PRODUCT_NAME );
	} );

	if ( ! empty( $tmu_product ) ) {
		return array_values( $tmu_product )[0]->id;
	}

	return false;
}

//========================================
//                                        
//   ####  #####     #####   ##     ##  
//  ##     ##  ##   ##   ##  ####   ##  
//  ##     #####    ##   ##  ##  ## ##  
//  ##     ##  ##   ##   ##  ##    ###  
//   ####  ##   ##   #####   ##     ##  
//                                        
//========================================

/**
 * Auto-connect non-connected products daily, via CRON.
 *
 * @return void
 */
function autoconnect_non_connected_products() {
	if ( ! has_storewide_discount_product() ) {
		return;
	}

	$connected              = 0;
	$tmu_product_id         = has_storewide_discount_product();
	$non_connected_products = wc_get_products( [ 'has_trustmeup_id' => false, 'return' => 'ids', 'limit' => -1, ] );

	if ( empty( $non_connected_products ) ) {
		return;
	}

	foreach ( $non_connected_products as $product_id ) {
		$success = Products_Connector::connect( $product_id, $tmu_product_id );

		if ( $success ) {
			$connected++;
		}
	}

	Helpers\log( sprintf( __( '%1$d non-connected WooCommerce products have been automatically connected to %2$s.', 'trustmeup' ), $connected, $tmu_product_id ), 'info' );
}
add_action( 'tmu/cron/autoconnect_non_connected_products', __NAMESPACE__ . '\\autoconnect_non_connected_products', 10 );
