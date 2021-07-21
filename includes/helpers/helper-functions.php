<?php
/**
 * Helper functions
 *
 */
use Dornaweb\WooCommerceWishlist\Data_Store;
use Dornaweb\WooCommerceWishlist\Bookmarks_Factory;

/**
 * Check if an entry is bookmarked
 *
 */
if (!function_exists('dweb_wishlist_is_bookmarked')) {
    function dweb_wishlist_is_bookmarked($entry_id = 0, $list_id = 0, $user_id = 0) {
        if (!$entry_id) {
            global $post;
            $entry_id = absint($post->ID);
        }

        if (!$user_id) {
            $user_id = absint(get_current_user_id());
        }

        $data_store = Data_Store::load('bookmark');
        return $data_store->is_bookmarked($entry_id, $user_id, $list_id);
    }
}

/**
 * Get user Bookmarks
 *
 */
if (!function_exists('dweb_wishlist_get_bookmarks')) {
    function dweb_wishlist_get_bookmarks($args = []) {
        return Bookmarks_Factory::get_bookmarks($args);
    }
}
