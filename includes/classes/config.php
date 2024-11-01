<?php

namespace TrustMeUp;

defined( 'ABSPATH' ) || exit;

class Config {
	/**
	 * Admin page slug.
	 */
	const ADMIN_PAGE_SLUG = 'trustmeup';

	/**
	 * OTP cookie name.
	 */
	const OTP_COOKIE_NAME = 'wordpress_trustmeup_otp';

	/**
	 * Prefix of the transient storing the TMU order data with PAC discount.
	 */
	const TRUSTMEUP_ORDER_TRANSIENT_PREFIX = 'trustmeup_order_';

	/**
	 * Number of products to fetch per API request.
	 */
	const API_PRODUCTS_LIMIT = 45;

	/**
	 * TrustMeUp API base URL for PROD.
	 */
	const TRUSTMEUP_API_URL = 'https://api.trustmeup.com/api/e-merchant/v1/';

	/**
	 * TrustMeUp API base URL for BETA.
	 */
	const TRUSTMEUP_API_URL_BETA = 'https://platform.beta.trustmeup.com/api/e-merchant/v1/';
        //const TRUSTMEUP_API_URL_BETA = 'https://platform.sandbox.trustmeup.com/api/e-merchant/v1/';

	/**
	 * The name of the product used as a storewide discount.
	 */
	const STOREWIDE_DISCOUNT_PRODUCT_NAME = 'General Store Discount';
}
