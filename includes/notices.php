<?php

namespace TrustMeUp\Notices;

use TrustMeUp\Config;
use \TrustMeUp\Options;

defined( 'ABSPATH' ) || exit;

/**
 * Display a notice if requirements are not met.
 *
 * @return void
 */
function notice_for_missing_requirements() {
	printf(
		'<div class="notice notice-error"><p>%1$s</p></div>',
		__( 'The TrustMeUp plugin requirements are not met (the WooCommerce plugin and at least PHP 5.6 and WordPress 5.5 are required).', 'trustmeup' )
	);
}

/**
 * Display a notice if TrustMeUp credentials are not set.
 *
 * @return void
 */
function notice_if_no_credentials() {
	if ( trustmeup_can_run() ) {
		return;
	}

	printf(
		'<div class="notice notice-error tmu-credentials"><p>%1$s</p></div>',
		sprintf(
			__( 'Please visit <a href="%1$s">the Settings tab</a> to enter your TrustMeUp credentials.', 'trustmeup' ),
			admin_url( 'admin.php?tab=settings&page=' . Config::ADMIN_PAGE_SLUG )
		)
	);
}
add_action( 'admin_notices', __NAMESPACE__ . '\\notice_if_no_credentials' );
