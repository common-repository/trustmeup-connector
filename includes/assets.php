<?php

namespace TrustMeUp\Assets;

use \TrustMeUp\Config;
use \TrustMeUp\Options;

defined( 'ABSPATH' ) || exit;

/**
 * Should we load assets on this current page?
 *
 * @return boolean
 */
function should_load_assets() {
	$screen = get_current_screen();

	return (
		\TrustMeUp\meets_requirements()
		&& (
			( array_key_exists('page', $_GET) && filter_var($_GET['page'], FILTER_SANITIZE_STRING) === Config::ADMIN_PAGE_SLUG )
			|| ( isset( $screen->post_type ) && $screen->post_type === 'shop_order' )
		)
	);
}

/**
 * Enqueue styles and script.
 *
 * @return void
 */
function enqueue_assets() {
	if ( ! should_load_assets() ) {
		return;
	}

	$enqueuer = new \WPackio\Enqueue( 'TrustMeUp', 'dist', TMU_VERSION, 'plugin', TMU_DIR . TMU_PLUGIN_DIRNAME );

	// Enqueue JS file.
	$js = $enqueuer->enqueue( 'admin_js', 'admin', [
		'js'        => true,
		'css'       => false,
		'js_dep'    => [],
		'css_dep'   => [],
		'in_footer' => true,
	] );

	// Enqueue CSS.
	$css = $enqueuer->enqueue( 'admin_css', 'admin', [
		'js'        => true,
		'css'       => true,
		'js_dep'    => [],
		'css_dep'   => [],
		'in_footer' => true,
	] );

	wp_localize_script(
		$js['js'][2]['handle'],
		'TrustMeUp',
		apply_filters( 'tmu/javascript_data', [] )
	);
}
add_action( 'admin_enqueue_scripts', __NAMESPACE__ . '\\enqueue_assets' );
