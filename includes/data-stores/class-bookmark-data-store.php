<?php
/**
 * WooCommerceWishlist Bookmark Data Store class
 *
 * @package WooCommerceWishlist
 * @since   1.0
 */

namespace Dornaweb\WooCommerceWishlist\Data_Stores;

defined('ABSPATH') || exit;

class Bookmark_Data_Store implements \Dornaweb\WooCommerceWishlist\Interfaces\Bookmark_Data_Store_Interface {
    public function create(&$bookmark) {
        global $wpdb;

        $wpdb->insert(
            $wpdb->prefix . 'dweb_bookmarks',
            [
                'entry_id'      => $bookmark->get_entry_id(),
                'variation_id'  => $bookmark->get_variation_id(),
                'list_id'       => $bookmark->get_list_id(),
                'user_id'       => $bookmark->get_user_id(),
                'note'          => $bookmark->get_note(),
            ],
            [
                '%d',
                '%d',
                '%d',
                '%d',
                '%s',
            ]
        );

        $bookmark_id = absint( $wpdb->insert_id );
		return $bookmark_id;
    }

    /**
	 * Read a bookmark item from the database.
	 *
	 * @since 1.0.0
	 *
	 * @param Bookmark $bookmark bookmark object.
	 *
	 * @throws Exception If invalid bookmark.
	 */
	public function read( &$bookmark ) {
		global $wpdb;

		$bookmark->set_defaults();

        $fields = ['entry_id', 'variation_id', 'list_id', 'user_id', 'note'];

		$data = $wpdb->get_row(
            $wpdb->prepare(
                "SELECT ". implode(', ', $fields) ." FROM {$wpdb->prefix}dweb_bookmarks WHERE ID = %d LIMIT 1;",
                $bookmark->get_id()
            )
        );

		if ( ! $data ) {
			throw new Exception( __( 'Invalid Bookmark.', 'woowishlist' ) );
		}

		$bookmark->set_props(
            array_combine(
                $fields,
                array_map(function($field) use($data) {
                    return $data->$field;
                }, $fields)
            )
		);
        $bookmark->set_object_read( true );
	}

    /**
	 * Update an bookmark.
	 *
	 * @since 1.0.0
	 * @param Bookmark $bookmark bookmark instance.
	 */
	public function update( &$bookmark ) {
        global $wpdb;

		$changes = $webhook->get_changes();

        $data = [
            'entry_id'      => $bookmark->get_entry_id(),
            'variation_id'  => $bookmark->get_variation_id(),
            'list_id'       => $bookmark->get_list_id(),
            'user_id'       => $bookmark->get_user_id(),
            'note'          => $bookmark->get_note(),
        ];

        $wpdb->update(
			$wpdb->prefix . 'dweb_bookmarks',
			$data,
			array(
				'ID' => $bookmark->get_id(),
			)
		); // WPCS: DB call ok.

		$bookmark->apply_changes();
    }


	/**
	 * Remove an bookmark from the database.
	 *
	 * @since 1.0.0
	 * @param Bookmark $bookmark      bookmark instance.
	 */
	public function delete( &$bookmark ) {
		global $wpdb;

		$wpdb->delete(
			$wpdb->prefix . 'dweb_bookmarks',
			array(
				'ID' => $bookmark->get_id(),
			),
			array( '%d' )
		); // WPCS: cache ok, DB call ok.
	}

    /**
	 * Get a bookmark object.
	 *
	 * @param  array $data From the DB.
	 * @return \Dornaweb\WooCommerceWishlist\Bookmark
	 */
	private function get_bookmark( $data ) {
        if ($data->ID) {
            $data->id = $data->ID;
        }

		return new \Dornaweb\WooCommerceWishlist\Bookmark( $data );
	}

    /**
     * Check if an entry is bookmarked
     *
     * @param int $entry_id
     * @param int $user_id
     * @param int $variation_id
     *
     * @return bool
     */
    public function is_bookmarked($entry_id, $user_id, $variation_id = 0) {
        global $wpdb;

		$get_results_output = ARRAY_A;

        $query = [];
        $query[] = "SELECT ID FROM {$wpdb->prefix}dweb_bookmarks WHERE 1=1";
        $query[] = $wpdb->prepare( 'AND entry_id = %d', absint( $entry_id ) );
        $query[] = $wpdb->prepare( 'AND user_id = %d', absint( $user_id ) );

        if ($variation_id) {
            $query[] = $wpdb->prepare( 'AND variation_id = %d', absint( $variation_id ) );
        }

        $results = $wpdb->get_results( implode( ' ', $query ), $get_results_output );

        return count($results) > 0;
    }

    /**
     * Query Bookmarks
     *
     * @param array $args
     * @return mixed
     */
    public function get_bookmarks($args = []) {
        global $wpdb;

        $args = wp_parse_args(
			$args,
			[
				'list_id'     => 0,
				'user_id'     => 0,
            ]
		);

        $valid_fields       = ['ID', 'entry_id', 'variation_id', 'list_id', 'user_id', 'note'];
		$get_results_output = ARRAY_A;

        if ( 'ids' === $args['return'] ) {
			$fields = 'ID';
		} elseif ( 'objects' === $args['return'] ) {
			$fields             = '*';
			$get_results_output = OBJECT;
		} else {
			$fields = explode( ',', (string) $args['return'] );
			$fields = implode( ', ', array_intersect( $fields, $valid_fields ) );
		}

        $query = [];
        $query[] = "SELECT {$fields} FROM {$wpdb->prefix}dweb_bookmarks WHERE 1=1";

        if ($args['list_id']) {
            $query[] = $wpdb->prepare( 'AND list_id = %d', absint( $args['list_id'] ) );
        }

        if ($args['user_id']) {
            $query[] = $wpdb->prepare( 'AND user_id = %d', absint( $args['user_id'] ) );
        }

        // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
		$results = $wpdb->get_results( implode( ' ', $query ), $get_results_output );

        switch ( $args['return'] ) {
			case 'ids':
				return wp_list_pluck( $results, 'ID' );
			case 'objects':
				return array_map( [ $this, 'get_bookmark' ], $results );
			default:
				return $results;
		}
    }
}
