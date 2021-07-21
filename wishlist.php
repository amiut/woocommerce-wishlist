<?php
/**
 * Plugin Name: Woocommerce Wishlist
 * Description: Wishlist plugin for woocommerce by Dornaweb.com
 * Plugin URI:  https://wwww.dornaweb.com
 * Version:     1.0
 * Author:      Dornaweb
 * Author URI:  https://wwww.dornaweb.com
 * License:     GPL
 * Text Domain: woowishlist
 * Domain Path: /languages
 *
 * @package Woocommerce Wishlist Plugin
 */

namespace Dornaweb;

defined("ABSPATH") || exit;

@ini_set('error_reporing', E_ALL);
@ini_set('display_errors', 1);

if (!defined("DWEB_WISHLIST_PLUGIN_FILE")) {
    define("DWEB_WISHLIST_PLUGIN_FILE", __FILE__);
}

/**
 * Load core packages and the autoloader.
 * The SPL Autoloader needs PHP 5.6.0+ and this plugin won't work on older versions
 */
if (version_compare(PHP_VERSION, "5.6.0", ">=")) {
    require __DIR__ . "/includes/class-autoloader.php";
}

/**
 * Returns the main instance of WooCommerceWishlist.
 *
 * @since  1.0
 * @return WooCommerceWishlist\App
 */
function woowishlist()
{
    // phpcs:ignore WordPress.NamingConventions.ValidFunctionName.FunctionNameInvalid
    return WooCommerceWishlist\App::instance();
}

// Global for backwards compatibility.
$GLOBALS["woowishlist"] = woowishlist();
