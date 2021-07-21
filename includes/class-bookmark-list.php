<?php
/**
 * WooCommerceWishlist Bookmark List class
 *
 * @package WooCommerceWishlist
 * @since   1.0
 */

namespace Dornaweb\WooCommerceWishlist;

defined('ABSPATH') || exit;

/**
 * WooCommerceWishlist Bookmark list class
 */
class Bookmark_List extends Data
{
    /**
	 * Order Data array. This is the core order data exposed in APIs since 1.0.0.
	 *
	 * @since 1.0.0
	 * @var array
	 */
	protected $data = array(
        'title'         => '',
        'slug'          => '',
        'user_id'       => 0,
        'description'   => '',
        'is_public'     => 0,
	);


    /**
	 * Stores meta in cache for future reads.
	 * A group must be set to to enable caching.
	 *
	 * @var string
	 */
	protected $cache_group = 'bookmark_lists';

    /**
	 * Meta type. This should match up with
	 * the types available at https://developer.wordpress.org/reference/functions/add_metadata/.
	 * WP defines 'post', 'user', 'comment', and 'term'.
	 *
	 * @var string
	 */
	protected $meta_type = 'bookmark_list';

    /**
	 * This is the name of this object type.
	 *
	 * @var string
	 */
	protected $object_type = 'bookmark_list';

	/**
	 * Constructor.
	 *
	 * @param int|object|array $bookmark_list ID to load from the DB, or Bookmark_list object.
	 */
	public function __construct( $bookmark_list = 0 ) {
		parent::__construct( $bookmark_list );

		if ( $bookmark_list instanceof Bookmark_list ) {
			$this->set_id( $bookmark_list->get_id() );
		} elseif ( is_numeric( $bookmark_list ) && $bookmark_list > 0 ) {
			$this->set_id( $bookmark_list );
		} elseif ( ! empty( $bookmark_list->ID ) ) {
			$this->set_id( absint( $bookmark_list->ID ) );
        }

		$this->data_store = Data_Store::load( 'bookmark_list' );

		// If we have an ID, load the bookmark_list from the DB.
		if ( $this->get_id() ) {
			try {
				$this->data_store->read( $this );
			} catch ( \Exception $e ) {
				$this->set_id( 0 );
				$this->set_object_read( true );
			}
		} else {
			$this->set_object_read( true );
		}
	}

	/**
	 * Merge changes with data and clear.
	 * Overrides DATA::apply_changes.
	 * array_replace_recursive does not work well for order items because it merges taxes instead
	 * of replacing them.
	 *
	 * @since 3.2.0
	 */
	public function apply_changes() {
		if ( function_exists( 'array_replace' ) ) {
			$this->data = array_replace( $this->data, $this->changes ); // phpcs:ignore PHPCompatibility.FunctionUse.NewFunctions.array_replaceFound
		} else { // PHP 5.2 compatibility.
			foreach ( $this->changes as $key => $change ) {
				$this->data[ $key ] = $change;
			}
		}
		$this->changes = [];
	}

    public function exists() {
        return $this->get_id() > 0;
    }

    /**
     *
     * @param string $context View or Edit context
     * @return
     */
    public function get_title($context = 'view') {
        return $this->get_prop('title', $context);
    }

    /**
     *
     * @param string $context View or Edit context
     * @return
     */
    public function get_slug($context = 'view') {
        return $this->get_prop('slug', $context);
    }

    /**
     *
     * @param string $context View or Edit context
     * @return
     */
    public function get_description($context = 'view') {
        return $this->get_prop('description', $context);
    }

    /**
     *
     * @param string $context View or Edit context
     * @return
     */
    public function get_user_id($context = 'view') {
        return $this->get_prop('user_id', $context);
    }

    /**
     *
     * @param string $context View or Edit context
     * @return
     */
    public function is_public($context = 'view') {
        return (bool) $this->get_prop('is_public', $context);
    }

    /**
     * Set Title
     *
     * @param int $title
     */
    public function set_title($title = '') {
        $this->set_prop( 'title', $title );
    }

    /**
     * Set slug
     *
     * @param int $slug
     */
    public function set_slug($slug = '') {
        $this->set_prop( 'slug', $slug );
    }

    /**
     * Set description
     *
     * @param int $description
     */
    public function set_description($description = '') {
        $this->set_prop( 'description', $description );
    }

    /**
     * Set user_id
     *
     * @param int $user_id
     */
    public function set_user_id($user_id = 0) {
        $this->set_prop( 'user_id', $user_id );
    }

    /**
     * Set is_public
     *
     * @param int $is_public
     */
    public function set_is_public($is_public = 0) {
        $this->set_prop( 'is_public', $is_public );
    }
}
