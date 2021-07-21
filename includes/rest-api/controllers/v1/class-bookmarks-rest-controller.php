<?php
/**
 * Bookmarks REST Controller
 *
 * @package WooCommerceWishlist
 * @since   1.0.0
 * @version 1.0.0
 */

namespace Dornaweb\WooCommerceWishlist\Rest_API\Controllers\V1;
use Dornaweb\WooCommerceWishlist\Data_Exception;

defined('ABSPATH') || exit;

class Bookmarks_REST_Controller extends \Dornaweb\WooCommerceWishlist\Rest_API\REST_Controller
{
    /**
     * REST Route
     */
    public $path = 'bookmarks';

    public $methods = ['post', 'get'];

    public function __construct() {
        parent::__construct();
    }

    /**
     * Response for GET Request
     */
    public function get() {
        header('Content-type: text/html');
        $data = \Dornaweb\WooCommerceWishlist\Bookmarks_Factory::bookmark(['entry_id' => 1, 'user_id' => 1]);

        try {
            $data = \Dornaweb\WooCommerceWishlist\Bookmarks_Factory::get_bookmarks();

            wp_send_json_success(['bookmarks' => $data]);
        } catch (Data_Exception $e) {
            wp_send_json_error($e->getErrorData());
        }

    }

    /**
     * Add a new Bookmark
     */
    public function post() {
        Dornaweb\WooCommerceWishlist\Bookmarks_Factory::bookmark([
            // 'entry_id'  => 1,
        ]);

        wp_json_success([
            'message' => 'Added'
        ]);
    }

    /**
     * Permission check
     *
     */
    public function permission_get() {
        return true;
    }

    public function permission_post() {
        return true;
    }
}


