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
class Bookmark extends Data
{
    /**
	 * Order Data array. This is the core order data exposed in APIs since 1.0.0.
	 *
	 * @since 1.0.0
	 * @var array
	 */
	protected $data = array(
        'entry_id'      => 0,
        'variation_id'  => 0,
        'list_id'       => 0,
        'user_id'       => 0,
        'note'          => '',
	);


    /**
	 * Stores meta in cache for future reads.
	 * A group must be set to to enable caching.
	 *
	 * @var string
	 */
	protected $cache_group = 'bookmarks';

    /**
	 * Meta type. This should match up with
	 * the types available at https://developer.wordpress.org/reference/functions/add_metadata/.
	 * WP defines 'post', 'user', 'comment', and 'term'.
	 *
	 * @var string
	 */
	protected $meta_type = 'bookmark';

    /**
	 * This is the name of this object type.
	 *
	 * @var string
	 */
	protected $object_type = 'bookmark';

	/**
	 * Constructor.
	 *
	 * @param int|object|array $bookmark ID to load from the DB, or Bookmark object.
	 */
	public function __construct( $bookmark = 0 ) {
		parent::__construct( $bookmark );

		if ( $bookmark instanceof Bookmark ) {
			$this->set_id( $bookmark->get_id() );
		} elseif ( is_numeric( $bookmark ) && $bookmark > 0 ) {
			$this->set_id( $bookmark );
		} elseif ( ! empty( $bookmark->ID ) ) {
			$this->set_id( absint( $bookmark->ID ) );
        }

		$this->data_store = Data_Store::load( 'bookmark' );

		// If we have an ID, load the bookmark from the DB.
		if ( $this->get_id() ) {
			try {
				$this->data_store->read( $this );
			} catch ( Exception $e ) {
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

    /**
     *
     * @param string $context View or Edit context
     * @return
     */
    public function get_entry_id($context = 'view') {
        return $this->get_prop('entry_id', $context);
    }

    /**
     * Alias of get_entry_id
     *
     * @param string $context View or Edit context
     * @return
     */
    public function get_product_id($context = 'view') {
        return $this->get_entry_id($context);
    }

    /**
     * Alias of get_entry_id
     *
     * @param string $context View or Edit context
     * @return
     */
    public function get_variation_id($context = 'view') {
        return $this->get_prop('variation_id', $context);
    }

    /**
     *
     * @param string $context View or Edit context
     * @return
     */
    public function get_list_id($context = 'view') {
        return $this->get_prop('list_id', $context);
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
    public function get_note($context = 'view') {
        return $this->get_prop('note', $context);
    }

    /**
     * Set entry id
     *
     * @param int $entry_id
     */
    public function set_entry_id($entry_id = 0) {
        $this->set_prop( 'entry_id', $entry_id );
    }

    /**
     * Set product id
     * Alias of set_entry_id
     *
     * @param int $product_id
     */
    public function set_product_id($product_id = 0) {
        $this->set_entry_id($product_id);
    }

    /**
     * Set variation id
     *
     * @param string $variation_id
     */
    public function set_variation_id($variation_id = 0) {
        $this->set_prop( 'variation_id', $variation_id );
    }

    /**
     * Set list id
     *
     * @param string $list_id
     */
    public function set_list_id($list_id = 0) {
        $this->set_prop( 'list_id', $list_id );
    }

    /**
     * Set list id
     *
     * @param string $user_id
     */
    public function set_user_id($user_id = 0) {
        $this->set_prop( 'user_id', $user_id );
    }

    /**
     * Set note
     *
     * @param string $note
     */
    public function set_note($note = '') {
        $this->set_prop( 'note', $note );
    }
}
