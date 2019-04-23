<?php
defined( 'ABSPATH' ) || exit;

/**
 * Functions used by plugins
 */
if ( ! class_exists( 'XLWCTY_WC_Dependencies' ) ) {
	require_once 'class-xlwcty-wc-dependencies.php';
}

/**
 * WC Detection
 */
if ( ! function_exists( 'xlwcty_is_woocommerce_active' ) ) {
	function xlwcty_is_woocommerce_active() {
		return XLWCTY_WC_Dependencies::woocommerce_active_check();
	}

}
