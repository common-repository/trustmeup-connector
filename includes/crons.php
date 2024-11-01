<?php

namespace TrustMeUp\Crons;

defined( 'ABSPATH' ) || exit;

/**
 * Register new CRON jobs.
 *
 * @return void
 */
function register_cron_jobs() {
	if ( ! wp_next_scheduled( 'tmu/cron/fetch_trustmeup_products' ) ) {
		wp_schedule_event( strtotime( 'tomorrow 03:00' ), 'daily', 'tmu/cron/fetch_trustmeup_products' );
	}

	if ( ! wp_next_scheduled( 'tmu/cron/autoconnect_non_connected_products' ) ) {
		wp_schedule_event( strtotime( 'tomorrow 04:00' ), 'daily', 'tmu/cron/autoconnect_non_connected_products' );
	}
}
add_action( 'init', __NAMESPACE__ . '\\register_cron_jobs' );

/**
 * De-register our CRON hooks when de-activating this plugin.
 *
 * @return void
 */
function deregister_cron_jobs() {
	wp_clear_scheduled_hook( 'tmu/cron/fetch_trustmeup_products' );
	wp_clear_scheduled_hook( 'tmu/cron/autoconnect_non_connected_products' );
}
add_action( 'tmu/plugin_deactivation', __NAMESPACE__ . '\\deregister_cron_jobs' );
