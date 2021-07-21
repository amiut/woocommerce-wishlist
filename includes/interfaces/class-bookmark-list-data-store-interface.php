<?php
/**
 * Bookmark List Data Store Interface
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
interface Bookmark_List_Data_Store_Interface {

	/**
	 * Add a Bookmark list List
	 *
	 * @param  array $bookmark_list Data.
	 * @return int   Bookmark ID
	 */
	public function create( &$bookmark_list );

	/**
	 * Read a Bookmark list
	 *
	 * @param  array $bookmark_list Data.
	 * @return int   Bookmark ID
	 */
	public function read( &$bookmark_list );

	/**
	 * Update a Bookmark list
	 *
	 * @param  array $bookmark_list Data.
	 * @return int   Bookmark ID
	 */
	public function update( &$bookmark_list );

	/**
	 * Delete a Bookmark list
	 *
	 * @param  array $bookmark_list Data.
	 * @return int   Bookmark ID
	 */
	public function delete( &$bookmark_list );

    /**
     * Query Bookmarks
     *
     * @param array $args
     * @return mixed
     */
    public function get_bookmark_lists($args = []);
}
