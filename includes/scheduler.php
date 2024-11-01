<?php

namespace TrustMeUp\Scheduler;

use \TrustMeUp\API_TrustMeUp;
use \TrustMeUp\Products_Connector;
use \TrustMeUp\Helpers;
use \TrustMeUp\Options;

defined( 'ABSPATH' ) || exit;

//===========================================
//                                           
//  ######    ###     ####  ##  ##   ####  
//    ##     ## ##   ##     ## ##   ##     
//    ##    ##   ##   ###   ####     ###   
//    ##    #######     ##  ## ##      ##  
//    ##    ##   ##  ####   ##  ##  ####   
//                                           
//===========================================

/**
 * Fetch new merchant token.
 *
 * @param string $token
 * @param boolean $disconnect
 * @return mixed
 */
function fetch_new_merchant_token( $token, $disconnect = false ) {
	$api   = new API_TrustMeUp();
	$token = $api->get_merchant_token( trustmeup_get_api_client( 'client_id' ), trustmeup_get_api_client( 'password' ) );

	if ( is_wp_error( $token ) ) {
		Options\delete_plugin_option( 'merchant_token' );

		return $token;
	}

	$token = sanitize_text_field( $token );
	Options\update_plugin_option( 'merchant_token', $token );

	do_action( 'tmu/new_merchant_token_saved', $token, $disconnect );

	return $token;
}
add_filter( 'tmu/fetch_merchant_token', __NAMESPACE__ . '\\fetch_new_merchant_token', 20, 2 );

/**
 * When a new token is saved, disconnect all products.
 *
 * @return void
 */
function disconnect_all_products_after_token_is_saved( $token, $disconnect ) {
	if ( ! trustmeup_can_run() || ! $disconnect ) {
		return;
	}

	$disconnected = Products_Connector::disconnect_all();
}
add_action( 'tmu/new_merchant_token_saved', __NAMESPACE__ . '\\disconnect_all_products_after_token_is_saved', 10, 2 );

/**
 * Fetch the TrustMeUp products list and save it in database.
 *
 * @return void
 */
function save_trustmeup_products_list() {
	if ( ! trustmeup_can_run() ) {
		return;
	}

	$api      = new API_TrustMeUp();
	$products = $api->get_merchant_products();

	if ( ! is_array( $products ) || empty( $products ) ) {
		return;
	}

	$data = [];

	foreach ( $products as $product ) {
		$data[] = (object) [
			'id'       => sanitize_text_field( $product->id ),
			'name'     => sanitize_text_field( $product->name ),
			'discount' => sanitize_text_field( $product->max_pac_discount ),
		];
	}

	usort( $data, function( $a, $b ) {
		return strcmp( $a->name, $b->name );
	} );

	Options\update_plugin_option( 'products_list', $data );

	Helpers\log( sprintf( __( 'A fresh list of %1$d TrustMeUp products has been saved locally.', 'trustmeup' ), count( $data ) ), 'success' );

	do_action( 'tmu/trustmeup_products_saved', $data, $products );
}
add_action( 'tmu/new_merchant_token_saved', __NAMESPACE__ . '\\save_trustmeup_products_list', 20 );

/**
 * Disconnect Woo Products that were NOT mentioned in the latest API response (TMU product was deleted for example).
 *
 * @param array $data
 * @param array $products
 * @return void
 */
function delete_connections_not_present_in_api_anymore( $data, $products ) {
	global $wpdb;
	$trustmeup_ids = wp_list_pluck( $data, 'id' );

	if ( empty( $trustmeup_ids ) ) {
		return;
	}

	$currently_connected_products = $wpdb->get_results(
		"SELECT tmu_id_table.meta_value AS trustmeup_id, posts_table.ID as product_id
		FROM $wpdb->postmeta AS tmu_id_table
		INNER JOIN $wpdb->posts AS posts_table ON tmu_id_table.post_id = posts_table.ID
		WHERE 1
		AND posts_table.post_type = 'product'
		AND tmu_id_table.meta_key = 'trustmeup_product_id'"
	);

	foreach ( $currently_connected_products as $connected_product ) {
		if ( in_array( $connected_product->trustmeup_id, $trustmeup_ids, true ) ) {
			continue;
		}

		Helpers\log( sprintf( __( 'Disconnecting #%1$d because it is no longer present in TrustMeUp products list.', 'trustmeup' ), (int) $connected_product->product_id ), 'info' );
		Products_Connector::disconnect( $connected_product->trustmeup_id );
	}
}
add_action( 'tmu/trustmeup_products_saved', __NAMESPACE__ . '\\delete_connections_not_present_in_api_anymore', 10, 2 );
