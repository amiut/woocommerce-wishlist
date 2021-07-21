<?php
/**
 * WooCommerceWishlist Bookmark Helpers class
 *
 * @package WooCommerceWishlist
 * @since   1.0
 */

namespace Dornaweb\WooCommerceWishlist;

defined('ABSPATH') || exit;

/**
 * WooCommerceWishlist Bookmark class
 */
class Bookmark_Helpers extends Data
{
    public static function user_can_bookmark($user_id = 0, $entry_id = 0) {
        return self::current_user_can_bookmark($entry_id);
    }

    public static function current_user_can_bookmark($entry_id = 0) {
        return true;
        return is_user_logged_in();
    }
}
