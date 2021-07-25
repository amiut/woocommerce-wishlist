<?php
/**
 * WooCommerceWishlist Bookmark class
 *
 * @package WooCommerceWishlist
 * @since   1.0
 */

namespace Dornaweb\WooCommerceWishlist;

defined('ABSPATH') || exit;

/**
 * WooCommerceWishlist Bookmark class
 */
class Bookmarks_Factory extends {
    /**
     * Bookmark an item (any wordpress post)
     *
     * @param array $args
     */
    public static function bookmark($args = []) {
        $args = wp_parse_args(
			$args,
			[
				'list_id'           => 0,
				'user_id'           => (int) get_current_user_id(),
                'entry_id'          => 0,
                'note'              => '',
            ]
		);

        $data_store = Data_Store::load('bookmark');

        if ($data_store->is_bookmarked($args['entry_id'], $args['user_id'], $args['list_id'])) {
            throw new Data_Exception('dweb_wishlist_already_bookmarked', __('Already bookmarked', 'woowishlist'), 409);
        }

        if (!Bookmark_Helpers::user_can_bookmark($args['user_id'])) {
            throw new Data_Exception('dweb_wishlist_user_not_allowed_to_bookmark', __('User not allowed to bookmark', 'woowishlist'), 403);
        }

        if (!$args['entry_id'] || !get_post_status($args['entry_id'])) {
            throw new Data_Exception('dweb_wishlist_nothing_to_bookmark', __('Nothing to bookmark', 'woowishlist'));
        }

        if ($args['list_id']) {
            $list = new Bookmark_List($args['list_id']);

            if (!$list->exists()) {
                throw new Data_Exception('dweb_wishlist_list_not_exist', __('Bookmark list does not exist', 'woowishlist'));
            }
        }

        $bookmark = new Bookmark();
        $bookmark->set_props($args);
        $bookmark->save();
    }

    /**
     * Un-Bookmark an item
     *
     * @param array $args
     */
    public static function unbookmark($entry_id = 0, $list_id = 0, $user_id = 0) {
        $data_store = Data_Store::load('bookmark');


        var_dump($entry_id);
        var_dump($list_id);

        if (!$data_store->is_bookmarked($entry_id, $user_id, $list_id)) {
            throw new Data_Exception('dweb_wishlist_nothing_to_remove', __('Entry is not bookmarked', 'woowishlist'));
        }

        $found_bookmarks = $data_store->get_bookmarks([
            'entry_id' => $entry_id,
            'user_id' => $user_id,
            'list_id' => $list_id,
        ]);

        foreach ($found_bookmarks as $bookmark) {
            $bookmark->delete();
        }
    }

    /**
     * List users bookmarked entries
     *
     */
    public static function get_bookmarks($args = []) {
        $args = wp_parse_args(
			$args,
			[
				'list_id'           => 0,
				'user_id'           => (int) get_current_user_id(),
                'post_type'         => 'product',
            ]
		);

        $data_store = Data_Store::load('bookmark');
        return $data_store->get_bookmarks($args);
    }
}
