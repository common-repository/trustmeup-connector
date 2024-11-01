<?php

namespace TrustMeUp\Strings;

defined( 'ABSPATH' ) || exit;

/**
 * Data used by JS/React: strings.
 *
 * @param array $data
 * @return array
 */
function inject_js_main_data( $data ) {
	$data['strings'] = [
		// Top-level, global strings.
		'Global.PageTitle'             => esc_html__( 'TrustMeUp', 'trustmeup' ),
		'Button.Save'                  => esc_html__( 'Save', 'trustmeup' ),
		'Button.Refresh'               => esc_html__( 'Refresh', 'trustmeup' ),
		'Button.Edit'                  => esc_html__( 'Edit', 'trustmeup' ),
		'Button.EditConnectedProducts' => esc_html__( 'Edit connected offers', 'trustmeup' ),
		'Button.Reset'                 => esc_html__( 'Reset', 'trustmeup' ),
		'Button.Disconnect'            => esc_html__( 'Disconnect', 'trustmeup' ),
		'Button.DisconnectAll'         => esc_html__( 'Disconnect all', 'trustmeup' ),
		'Button.Connect'               => esc_html__( 'Connect', 'trustmeup' ),
		'Button.ConnectProducts'       => esc_html__( 'Connect offers', 'trustmeup' ),
		'Button.Cancel'                => esc_html__( 'Cancel', 'trustmeup' ),
		'Button.SyncWithTMU'           => esc_html__( 'Synchronize with TrustMeUp', 'trustmeup' ),
		'Button.RefreshProducts'       => esc_html__( 'Refresh offers list', 'trustmeup' ),
		'Button.DisconnectAll?'        => esc_html__( 'Disconnect all offers?', 'trustmeup' ),
		'Button.SelectAllAvailable'    => esc_html__( 'Select all available', 'trustmeup' ),

		// Texts.
		'Text.CredentialsMissing'         => esc_html__( 'Please enter your TrustMeUp API credentials in the Settings tab.', 'trustmeup' ),
		'Text.Product'                    => esc_html__( 'Offer', 'trustmeup' ),
		'Text.SKU'                        => esc_html__( 'SKU', 'trustmeup' ),
		'Text.Discount'                   => esc_html__( 'Discount', 'trustmeup' ),
		'Text.SyncScheduled'              => esc_html__( 'Synchronization scheduled at', 'trustmeup' ),
		'Text.NoConnectedProducts'        => esc_html__( 'No connected offers yet…', 'trustmeup' ),
		'Text.NoWooProducts'              => esc_html__( 'No offers in your shop yet…', 'trustmeup' ),
		'Text.NoWooProductsAfterFilter'   => esc_html__( 'No offers found…', 'trustmeup' ),
		'Text.ConnectedAt'                => esc_html__( 'Connected at', 'trustmeup' ),
		'Text.ProductsListSoonSync'       => esc_html__( 'The offers listed below will soon be synchronized with your WooCommerce shop.', 'trustmeup' ),
		'Text.DisconnectAllProducts'      => esc_html__( 'Disconnect all offers', 'trustmeup' ),
		'Text.TextBeforeAPICreds'         => sprintf( __( 'Visit the %1$sDevelopers section%2$s of your TrustMeUp Merchant account to find your client ID and password.', 'trustmeup' ), '<a href="https://www.trustmeup.com/en/dashboard/merchants/developers" target="_blank" class="link">', '</a>' ),
		'Text.TextBeforeAPICredsBeta'     => sprintf( __( 'Visit the %1$sDevelopers section%2$s of your TrustMeUp Beta Merchant account to find your client ID and password.', 'trustmeup' ), '<a href="https://beta.trustmeup.com/en/dashboard/merchants/developers" target="_blank" class="link">', '</a>' ),
		'Text.DefaultDiscountProduct'     => esc_html__( 'Storewide discount', 'trustmeup' ),
		'Text.GroupProduct'               => esc_html__( 'Group offer', 'trustmeup' ),
		'Text.SingleProduct'              => esc_html__( 'Single offer', 'trustmeup' ),
		'Text.TrustMeUpProduct'           => esc_html__( 'TrustMeUp offer', 'trustmeup' ),
		'Text.WooProduct'                 => esc_html__( 'WooCommerce offers', 'trustmeup' ),
		'Text.ConnectorPopupTitle'        => esc_html__( 'Connect offer', 'trustmeup' ),
		'Text.ConnectorPopupDesc'         => esc_html__( 'Select a offer from your shop on the right.', 'trustmeup' ),
		'Text.ConnectorPopupMultipleDesc' => esc_html__( 'Select one or many offers from your shop on the right.', 'trustmeup' ),

		// New ones.
		'Text.ConnectedProducts' => esc_html__( 'Connected offers', 'trustmeup' ),
		'Text.SyncQueue'         => esc_html__( 'Synchronization queue', 'trustmeup' ),
		'Text.ApiCredentials'    => esc_html__( 'TrustMeUp API credentials', 'trustmeup' ),

		// Tab names.
		'Tabs.Overview' => esc_html__( 'Overview', 'trustmeup' ),
		'Tabs.Settings' => esc_html__( 'Settings', 'trustmeup' ),

		// Settings.
		'Settings.ValidMerchantTokenIs' => esc_html__( 'Your TrustMeUp token is', 'trustmeup' ),
		'Settings.InvalidMerchantToken' => esc_html__( 'Your TrustMeUp authentication is absent or invalid.', 'trustmeup' ),

		// Fields.
		'Field.ClientID'            => esc_html__( 'Client ID', 'trustmeup' ),
		'Field.Password'            => esc_html__( 'Password', 'trustmeup' ),
		'Field.EnvironmentCheckbox' => esc_html__( 'Use my TrustMeUp beta credentials', 'trustmeup' ),
		'Field.SearchProducts'      => esc_html__( 'Search products', 'trustmeup' ),
		'Field.CategoriesProducts'  => esc_html__( 'Search categories', 'trustmeup' ),

		// Legend.
		'Text.Legend'                   => esc_html__( 'Legend:', 'trustmeup' ),
		'Text.LegendAlreadyConnected'   => esc_html__( 'Already connected', 'trustmeup' ),
		'Text.LegendConnectedWithOther' => esc_html__( 'Connected with other offer', 'trustmeup' ),
		'Text.LegendAvailable'          => esc_html__( 'Available for connection', 'trustmeup' ),
	];

	return $data;
}
add_filter( 'tmu/javascript_data', __NAMESPACE__ . '\\inject_js_main_data', 20, 1 );
