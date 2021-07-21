<?php
/**
 * Bookmark Data Store Interface
 *
 * @version 1.0.0
 * @package WooCommerceWishlist\Interface
 */

namespace Dornaweb\WooCommerceWishlist\Interfaces;

/**
 * WC Bookmark Data Store Interface
 *
 * Functions that must be defined by the Bookmark data store (for functions).
 *
 * @version  1.0.0
 */
interface Bookmark_Data_Store_Interface {

	/**
	 * Add a Bookmark
	 *
	 * @param  array $bookmark Order Data.
	 * @return int   Bookmark ID
	 */
	public function create( &$bookmark );

	/**
	 * Read a Bookmark
	 *
	 * @param  array $bookmark Order Data.
	 * @return int   Bookmark ID
	 */
	public function read( &$bookmark );

	/**
	 * Delete a Bookmark
	 *
	 * @param  array $bookmark Order Data.
	 * @return int   Bookmark ID
	 */
	public function delete( &$bookmark );

	/**
     * Check if an entry is bookmarked
     *
     * @param int $entry_id
     *
     * @return bool
     */
	public function is_bookmarked($entry_id, $user_id);

    /**
     * Query Bookmarks
     *
     * @param array $args
     * @return mixed
     */
    public function get_bookmarks($args = []);
}
