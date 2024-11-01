<?php

namespace TrustMeUp;

use \TrustMeUp\Options;
use \TrustMeUp\Helpers;

defined( 'ABSPATH' ) || exit;

class Products_Connector {
	protected $item = null;

	/**
	 * Construct the object with an item from the API response OR from an object constructed on-demand.
	 *
	 * @param object $item A TrustMeUp API /products/ response single item.
	 */
	public function __construct( $item = null ) {
		$this->item = (object) $item;
	}

	//=======================================
	//                                       
	//   ####   #####   ##     ##  ####    
	//  ##     ##   ##  ####   ##  ##  ##  
	//  ##     ##   ##  ##  ## ##  ##  ##  
	//  ##     ##   ##  ##    ###  ##  ##  
	//   ####   #####   ##     ##  ####    
	//                                       
	//=======================================

	/**
	 * Can we process this API item to connect it with a WC product from our shop?
	 *
	 * @return boolean
	 */
	public function is_valid() {
		return (
			isset( $this->item->id ) && ! empty( $this->item->id )
			&& (
				// Normal product
				( isset( $this->item->external_id ) && ! empty( $this->item->external_id ) )
			)
		);
	}

	/**
	 * Is this item already scheduled for import?
	 *
	 * @return boolean
	 */
	public function is_already_scheduled() {
		$actions = as_get_scheduled_actions(
			[
				'hook'     => 'tmu/connect_product',
				'status'   => \ActionScheduler_Store::STATUS_PENDING,
				'args'     => [ 'id' => sanitize_text_field( $this->item->id ) ],
			]
		);

		return ! empty( $actions );
	}

	//===========================================================
	//                                                           
	//   ####    #####  ######  ######  #####  #####     ####  
	//  ##       ##       ##      ##    ##     ##  ##   ##     
	//  ##  ###  #####    ##      ##    #####  #####     ###   
	//  ##   ##  ##       ##      ##    ##     ##  ##      ##  
	//   ####    #####    ##      ##    #####  ##   ##  ####   
	//                                                           
	//===========================================================

	/**
	 * Get WooCommerce products connected to a TrustMeUp product.
	 *
	 * @return array
	 */
	public static function get_trustmeup_products_with_sync_data() {
		$products_list = Options\get_plugin_option( 'products_list', [] );

		if ( empty( $products_list ) ) {
			return [];
		}

		$woo_products = wc_get_products( [ 'has_trustmeup_id' => true, 'limit' => -1, 'orderby' => 'name', 'order' => 'ASC' ] );
		$date_format  = sprintf( '%1$s %2$s', get_option( 'date_format' ), get_option( 'time_format' ) );

		$woo_connected_products = array_map( function( $product ) use ( $date_format ) {
			$image = wp_get_attachment_image_src( $product->get_image_id( 'woocommerce_thumbnail' ) );

			return (object) [
				'id'           => (int) $product->get_id(),
				'thumbnail'    => $image ? $image[0] : wc_placeholder_img_src( 'woocommerce_thumbnail' ),
				'trustmeup_id' => $product->get_meta( 'trustmeup_product_id', true ),
				'sku'          => $product->get_sku(),
				'name'         => html_entity_decode( $product->get_name() ),
				'permalink'    => $product->get_permalink(),
				'synced_at'    => wp_date( $date_format, $product->get_meta( 'trustmeup_synced_at', true ) ),
			];
		}, $woo_products );

		// Send back the list of TrustMeUp products with a "synced_with" array containing the WC products synced with each product.
		return array_map( function( $tmu_product ) use ( $woo_connected_products ) {
			$tmu_product->synced_with = array_values( array_filter( $woo_connected_products, function( $woo_product ) use ( $tmu_product ) {
				return $woo_product->trustmeup_id === $tmu_product->id;
			} ) );

			return $tmu_product;
		}, $products_list );
	}

	//===========================================================
	//                                                           
	//    ###     ####  ######  ##   #####   ##     ##   ####  
	//   ## ##   ##       ##    ##  ##   ##  ####   ##  ##     
	//  ##   ##  ##       ##    ##  ##   ##  ##  ## ##   ###   
	//  #######  ##       ##    ##  ##   ##  ##    ###     ##  
	//  ##   ##   ####    ##    ##   #####   ##     ##  ####   
	//                                                           
	//===========================================================

	/**
	 * Connect a WooCommerce product with a specific TrustMeUp product.
	 *
	 * @param integer $woo_product_id
	 * @param string $tmu_product_id
	 * @return void
	 */
	public static function connect( $woo_product_id, $tmu_product_id ) {
		$product = wc_get_product( (int) $woo_product_id );

		if ( $product && $product->exists() ) {
			$product->update_meta_data( 'trustmeup_product_id', $tmu_product_id );
			$product->update_meta_data( 'trustmeup_synced_at', time() );
			$product->save();

			return true;
		}

		return false;
	}

	/**
	 * Delete all existing connections.
	 *
	 * @return integer
	 */
	public static function disconnect_all() {
		// Single connected products.
		global $wpdb;
		$posts_ids = $wpdb->get_col(
			"SELECT DISTINCT( posts.ID ) FROM $wpdb->postmeta meta
			LEFT JOIN $wpdb->posts posts ON meta.post_id = posts.ID
			WHERE meta.meta_key LIKE 'trustmeup_%'
			AND posts.post_type = 'product'"
		);

		if ( count( $posts_ids ) > 0 ) {
			$deleted = $wpdb->query(
				"DELETE meta FROM $wpdb->postmeta meta
				LEFT JOIN $wpdb->posts posts ON meta.post_id = posts.ID
				WHERE meta.meta_key LIKE 'trustmeup_%'
				AND posts.post_type = 'product'"
			);

			Helpers\log( sprintf( __( '%1$d products\' connections have been deleted (%2$d metadata rows deleted).', 'trustmeup' ), count( $posts_ids ), (int) $deleted ), 'info', [ 'product_ids' => (array) $posts_ids ] );
		}

		return $posts_ids;
	}

	/**
	 * Delete all connections for a specific TMU product.
	 *
	 * @param string $tmu_product_id
	 * @return boolean
	 */
	public static function disconnect( $tmu_product_id = '' ) {
		global $wpdb;

		$woo_products_ids = wc_get_products( [ 'trustmeup_id' => $tmu_product_id, 'limit' => -1, 'orderby' => 'name', 'order' => 'ASC', 'return' => 'ids' ] );
		$deleted          = 0;

		foreach ( $woo_products_ids as $woo_product_id ) {
			$deleted += $wpdb->query(
				$wpdb->prepare(
					"DELETE meta FROM $wpdb->postmeta meta
					LEFT JOIN $wpdb->posts posts ON meta.post_id = posts.ID
					WHERE meta.meta_key LIKE 'trustmeup_%'
					AND posts.post_type = 'product'
					AND posts.ID = %d",
					(int) $woo_product_id
				)
			);
		}

		if ( $deleted > 0 ) {
			Helpers\log( sprintf( __( '%1$d WooCommerce products have been disconnected from %2$s.', 'trustmeup' ), count( $woo_products_ids ), $tmu_product_id ), 'info' );
		}

		return $deleted > 0;
	}
}
