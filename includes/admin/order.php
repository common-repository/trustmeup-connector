<?php

namespace TrustMeUp\Admin\Order;

use \TrustMeUp\Helpers;

defined( 'ABSPATH' ) || exit;

/**
 * Register our metabox on WooCommerce single order page.
 *
 * @return void
 */
function register_trustmeup_order_metabox() {
	add_meta_box(
		'trustmeup',
		__( 'TrustMeUp Order', 'trustmeup' ),
		__NAMESPACE__ . '\\render_metabox',
		[ 'shop_order' ],
		'side'
	);
}
add_action( 'add_meta_boxes', __NAMESPACE__ . '\\register_trustmeup_order_metabox', 9999 );

/**
 * Render the TrustMeUp metabox.
 *
 * @param WP_Post $post
 * @return void
 */
function render_metabox( $post ) {
	$order = wc_get_order( $post->ID );

	if ( ! $order ) {
		return;
	}

	$trustmeup_discount = $order->get_meta( 'trustmeup_pac_discount', true );
	$trustmeup_order    = $order->get_meta( 'trustmeup_completed_order', true );
	$discount_html      = '';

	if ( empty( $trustmeup_discount ) && empty( $trustmeup_order ) ) {
		_e( 'No TrustMeUp data for this order...', 'trustmeup' );
		return;
	}

	if ( ! empty( $trustmeup_discount ) ) {
		$discount_html = sprintf(
			'<li class="discount">%1$s <span class="value">%2$s</span></li>',
			__( 'PAC discount:', 'trustmeup' ),
			wc_price( $trustmeup_discount )
		);
	}

	if ( ! empty( $trustmeup_order ) ) {
		$quantity = null;

		if ( isset( $trustmeup_order->cart_items ) ) {
			$quantity = array_sum( wp_list_pluck( $trustmeup_order->cart_items, 'quantity' ) );
		}

		printf(
			'<ul class="info">
				%7$s
				<li>%1$s <span class="value">%4$s</span></li>
				<li>%2$s <span class="value">%5$s</span></li>
				<li>%3$s <span class="value">%6$s</span></li>
			</ul>',
			__( 'ID:', 'trustmeup' ),
			__( 'Products:', 'trustmeup' ),
			__( 'Donor:', 'trustmeup' ),
			isset( $trustmeup_order->id ) ? $trustmeup_order->id : '<span class="no-value">—</span>',
			$quantity ? $quantity : '<span class="no-value">—</span>',
			isset( $trustmeup_order->user, $trustmeup_order->user->display_name ) ? $trustmeup_order->user->display_name : '<span class="no-value">—</span>',
			$discount_html
		);
	}
}

/**
 * Display PAC discount before order total on the Order edit page.
 *
 * @param integer $order_id
 * @return void
 */
function display_pac_discount_in_order_edit_page( $order_id ) {
	$order = wc_get_order( $order_id );

	if ( ! $order ) {
		return;
	}

	$trustmeup_discount = $order->get_meta( 'trustmeup_pac_discount', true );

	if ( empty( $trustmeup_discount ) ) {
		return;
	}

	ob_start();
	?>
	<tr>
		<td class="label"><?php esc_html_e( 'PAC discount', 'trustmeup' ); ?>:</td>
		<td width="1%"></td>
		<td class="total"><?php echo wc_price( $trustmeup_discount * -1 ); ?></td>
	</tr>
	<?php

	echo apply_filters( 'tmu/pac_discount_displayed', ob_get_clean(), 'order-edit', $trustmeup_discount );
}
add_action( 'woocommerce_admin_order_totals_after_tax', __NAMESPACE__ . '\\display_pac_discount_in_order_edit_page' );

/**
 * Display PAC discount before order total in the Order table.
 *
 * @param array $total_rows
 * @param \WC_Order $order
 * @param boolean $tax_display
 * @return void
 */
function display_pac_discount_in_order_details_table( $total_rows, $order, $tax_display ) {
	$trustmeup_discount = $order->get_meta( 'trustmeup_pac_discount', true );

	if ( ! empty( $trustmeup_discount ) ) {
		$discount_row = [
			'label' => __( 'PAC discount:', 'trustmeup' ),
			'value' => wc_price( $trustmeup_discount * -1 ),
		];

		$total_rows = Helpers\array_insert_after( 'cart_subtotal', $total_rows, 'trustmeup_discount', $discount_row );
	}

	return $total_rows;
}
add_action( 'woocommerce_get_order_item_totals', __NAMESPACE__ . '\\display_pac_discount_in_order_details_table', 10, 4 );
