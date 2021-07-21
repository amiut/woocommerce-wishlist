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
use Dornaweb\WooCommerceWishlist\Bookmarks_Factory;

defined('ABSPATH') || exit;

class Bookmarks_REST_Controller extends \Dornaweb\WooCommerceWishlist\Rest_API\REST_Controller
{
    /**
     * REST Route
     */
    public $path = 'bookmarks';

    public $methods = ['post', 'get', 'delete'];
    public $one_methods = ['delete'];

    public function __construct() {
        parent::__construct();
    }

    /**
     * Response for GET Request
     */
    public function get() {
        $data = \Dornaweb\WooCommerceWishlist\Bookmarks_Factory::bookmark(['entry_id' => 45, 'user_id' => 1]);

        try {
            $data = \Dornaweb\WooCommerceWishlist\Bookmarks_Factory::get_bookmarks();

            wp_send_json_success(['bookmarks' => $data]);
        } catch (Data_Exception $e) {
            wp_send_json_error($e->getErrorData(), $e->getCode());
        }
    }

    /**
     * Add a new Bookmark
     */
    public function post($request) {
        $entry_id = absint($request->get_param('entry_id'));

        try {
            if (!$entry_id) {
                wp_send_json_error(['message' => __('Nothing to bookmark', 'woowishlist'), 400]);
            }

            Bookmarks_Factory::bookmark(['entry_id' => $entry_id]);

            wp_send_json_success(['message' => __('Added', 'woowishlist')], 201);

        } catch (Data_Exception $e) {
            wp_send_json_error($e->getErrorData(), $e->getCode());
        }
    }

    /**
     * Remove a bookmark
     */
    public function delete_one($request) {
        $entry_id = absint($request->get_param('id'));

        try {
            if (!is_user_logged_in()) {
                wp_send_json_error(['message' => __('You need to login', 'woowishlist'), 403]);
            }

            Bookmarks_Factory::unbookmark($entry_id, 0, get_current_user_id());

            wp_send_json_success(['message' => __('removed', 'woowishlist')]);

        } catch (Data_Exception $e) {
            wp_send_json_error($e->getErrorData(), $e->getCode());
        }
    }

    /**
     * Permission check
     *
     */
    public function permission_get() {
        return is_user_logged_in();
    }

    public function permission_post() {
        return is_user_logged_in();
    }

    public function permission_delete_one() {
        return is_user_logged_in();
    }
}


