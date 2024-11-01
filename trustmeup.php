<?php
/**
 * Plugin Name: TrustMeUp WooCommerce Connector
 * Description: Connect your WooCommerce shop with your TrustMeUp shop.
 * Author: TrustMeUp
 * Author URI: https://www.trustmeup.com/
 * Text Domain: trustmeup
 * Domain Path: /languages/
 * Version: 1.4.6
 * WC requires at least: 4.0
 * WC tested up to: 7.3.0
 */

namespace TrustMeUp;

defined( 'ABSPATH' ) || exit;

/**
 * Define plugin constants
 */
define( 'TMU_VERSION', '1.4.6' );
define( 'TMU_URL', plugin_dir_url( __FILE__ ) );
define( 'TMU_ROOT_FILE', __FILE__ );
define( 'TMU_DIR', plugin_dir_path( __FILE__ ) );
define( 'TMU_PLUGIN_DIRNAME', basename( rtrim( dirname( __FILE__ ), '/' ) ) );
define( 'TMU_BASENAME', plugin_basename( __FILE__ ) );

/**
 * Check for requirements and (maybe) load the plugin vital files.
 *
 * @return void
 */
function init() {
	if ( file_exists( TMU_DIR . '/vendor/autoload.php' ) ) {
		require_once TMU_DIR . '/vendor/autoload.php';
	}

	/**
	 * Register required files.
	 */
	require_once TMU_DIR . '/includes/classes/_abstract/ajax-route.php';
	require_once TMU_DIR . '/includes/classes/ajax-routes/fields-save.php';
	require_once TMU_DIR . '/includes/classes/ajax-routes/products-refresh.php';
	require_once TMU_DIR . '/includes/classes/ajax-routes/products-resync.php';
	require_once TMU_DIR . '/includes/classes/ajax-routes/products-connect.php';
	require_once TMU_DIR . '/includes/classes/ajax-routes/products-disconnect.php';
	require_once TMU_DIR . '/includes/classes/ajax.php';
	require_once TMU_DIR . '/includes/classes/api-trustmeup.php';
	require_once TMU_DIR . '/includes/classes/cart-discounter.php';
	require_once TMU_DIR . '/includes/classes/config.php';
	require_once TMU_DIR . '/includes/classes/products-connector.php';
	require_once TMU_DIR . '/includes/admin/order.php';
	require_once TMU_DIR . '/includes/admin.php';
	require_once TMU_DIR . '/includes/assets.php';
	require_once TMU_DIR . '/includes/auto-connector.php';
	require_once TMU_DIR . '/includes/crons.php';
	require_once TMU_DIR . '/includes/functions.php';
	require_once TMU_DIR . '/includes/helpers.php';
	require_once TMU_DIR . '/includes/hooks.php';
	require_once TMU_DIR . '/includes/strings.php';
	require_once TMU_DIR . '/includes/notices.php';
	require_once TMU_DIR . '/includes/options.php';
	require_once TMU_DIR . '/includes/scheduler.php';
	require_once TMU_DIR . '/includes/woocommerce.php';

	if ( ! meets_requirements() && ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
		add_action( 'admin_notices', 'TrustMeUp\Notices\notice_for_missing_requirements' );
		return;
	}

	/**
	 * Initialize our hookable classes.
	 */
	$api = new Ajax();
}
add_action( 'plugins_loaded', __NAMESPACE__ . '\\init' );

/**
 * Does this WP install meet minimum requirements?
 *
 * @return boolean
 */
function meets_requirements() {
	global $wp_version;

	return (
		class_exists( 'WooCommerce' ) &&
		function_exists( 'as_schedule_single_action' ) &&
		version_compare( PHP_VERSION, '5.6', '>=' ) &&
		version_compare( $wp_version, '5.5', '>=' )
	);
}

/**
 * Trigger a custom action when activating the plugin.
 *
 * @param string $plugin
 * @param boolean $network
 * @return void
 */
function trustmeup_activation( $plugin, $network ) {
	if ( $plugin !== TMU_BASENAME ) {
		return;
	}

	init();
	do_action( 'tmu/plugin_activation', (bool) $network );
}
add_action( 'activate_plugin', __NAMESPACE__ . '\\trustmeup_activation', 10, 2 );

/**
 * Trigger a custom action when de-activating the plugin.
 *
 * @return void
 */
function trustmeup_deactivation( $plugin, $network ) {
	if ( $plugin !== TMU_BASENAME ) {
		return;
	}

	init();
	do_action( 'tmu/plugin_deactivation', (bool) $network );
}
add_action( 'deactivate_plugin', __NAMESPACE__ . '\\trustmeup_deactivation', 10, 2 );

/**
 * Translations.
 *
 * @return void
 */
function load_translations() {
	load_plugin_textdomain( 'trustmeup', false, TMU_PLUGIN_DIRNAME . '/languages/' );
}
add_action( 'plugins_loaded', __NAMESPACE__ . '\\load_translations' );

// Code to be placed in functions.php of your theme or a custom plugin file.
add_filter( 'load_textdomain_mofile', __NAMESPACE__ . '\\load_custom_plugin_translation_file', 10, 2 );

/*
 * Replace 'textdomain' with your plugin's textdomain. e.g. 'woocommerce'. 
 * File to be named, for example, yourtranslationfile-en_GB.mo
 * File to be placed, for example, wp-content/lanaguages/textdomain/yourtranslationfile-en_GB.mo
 */
function load_custom_plugin_translation_file( $mofile, $domain ) {

  if ( 'trustmeup' === $domain ) {
  	
    $mofile = TMU_DIR . '/languages/trustmeup-' . get_locale() . '.mo';
    
  }
  return $mofile;
}
