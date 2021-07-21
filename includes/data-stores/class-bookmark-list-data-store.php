<?php
/**
 * WooCommerceWishlist Bookmark List Data Store class
 *
 * @package WooCommerceWishlist
 * @since   1.0
 */

namespace Dornaweb\WooCommerceWishlist\Data_Stores;

defined('ABSPATH') || exit;

class Bookmark_List_Data_Store implements \Dornaweb\WooCommerceWishlist\Interfaces\Bookmark_List_Data_Store_Interface {
    /**
	 * Add a Bookmark List
	 *
	 * @param  array $bookmark_list Order Data.
	 * @return int   Bookmark List ID
	 */
    public function create(&$bookmark_list) {
        global $wpdb;

        $wpdb->insert(
            $wpdb->prefix . 'dweb_bookmark_lists',
            [
                'title'         => $bookmark_list->get_title(),
                'slug'          => $bookmark_list->get_slug(),
                'description'   => $bookmark_list->get_description(),
                'user_id'       => $bookmark_list->get_user_id(),
                'is_public'     => $bookmark_list->is_public() ? 1 : 0,
            ],
            [
                '%s',
                '%s',
                '%s',
                '%d',
                '%d',
            ]
        );

        $bookmark_list_id = absint( $wpdb->insert_id );
		return $bookmark_list_id;
    }

    /**
	 * Read a bookmark list from the database.
	 *
	 * @since 1.0.0
	 *
	 * @param Bookmark_List $bookmark_list Bookmark_List object.
	 *
	 * @throws Exception If invalid bookmark list.
	 */
	public function read( &$bookmark_list ) {
		global $wpdb;

		$bookmark_list->set_defaults();

        $fields = ['title', 'slug', 'description', 'user_id', 'is_public'];

		$data = $wpdb->get_row(
            $wpdb->prepare(
                "SELECT ". implode(', ', $fields) ." FROM {$wpdb->prefix}dweb_bookmark_lists WHERE ID = %d LIMIT 1;",
                $bookmark_list->get_id()
            )
        );

		if ( ! $data ) {
			throw new Exception( __( 'Invalid Bookmark.', 'woowishlist' ) );
		}

		$bookmark_list->set_props(
            array_combine(
                $fields,
                array_map(function($field) use($data) {
                    return $data->$field;
                }, $fields)
            )
		);
        $bookmark_list->set_object_read( true );
	}

    /**
	 * Update an bookmark list.
	 *
	 * @since 1.0.0
	 * @param Bookmark_List $bookmark_list Bookmark_List instance.
	 */
	public function update( &$bookmark_list ) {
        global $wpdb;

		$changes = $webhook->get_changes();

        $data = [
            'entry_id'      => $bookmark_list->get_entry_id(),
            'variation_id'  => $bookmark_list->get_variation_id(),
            'list_id'       => $bookmark_list->get_list_id(),
            'note'          => $bookmark_list->get_note(),
        ];

        $wpdb->update(
			$wpdb->prefix . 'dweb_bookmark_lists',
			$data,
			array(
				'ID' => $bookmark_list->get_id(),
			)
		); // WPCS: DB call ok.

		$bookmark_list->apply_changes();
    }


	/**
	 * Remove a bookmark list from the database.
	 *
	 * @since 1.0.0
	 * @param Bookmark $bookmark_list      Bookmark_List instance.
	 */
	public function delete( &$bookmark_list ) {
		global $wpdb;

		$wpdb->delete(
			$wpdb->prefix . 'dweb_bookmark_lists',
			array(
				'ID' => $bookmark_list->get_id(),
			),
			array( '%d' )
		); // WPCS: cache ok, DB call ok.
	}

    /**
	 * Get a bookmark list object.
	 *
	 * @param  array $data From the DB.
	 * @return \Dornaweb\WooCommerceWishlist\Bookmark_List
	 */
	private function get_bookmark_list( $data ) {
        if ($data->ID) {
            $data->id = $data->ID;
        }

		return new \Dornaweb\WooCommerceWishlist\Bookmark_List( $data );
	}

    /**
     * Query Bookmark lists
     *
     * @param array $args
     * @return mixed
     */
    public function get_bookmark_lists($args = []) {
        global $wpdb;

        $args = wp_parse_args(
			$args,
			[
				'user_id'     => 0,
				'is_public'   => 0,
            ]
		);

        $valid_fields       = ['title', 'slug', 'description', 'user_id', 'is_public'];
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
        $query[] = "SELECT {$fields} FROM {$wpdb->prefix}dweb_bookmark_lists WHERE 1=1";

        if ($args['user_id']) {
            $query[] = $wpdb->prepare( 'AND user_id = %d', absint( $args['user_id'] ) );
        }

        if ($args['is_public']) {
            $query[] = $wpdb->prepare( 'AND is_public = %d', absint( $args['is_public'] ) );
        }

        // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
		$results = $wpdb->get_results( implode( ' ', $query ), $get_results_output );

        switch ( $args['return'] ) {
			case 'ids':
				return wp_list_pluck( $results, 'ID' );
			case 'objects':
				return array_map( [ $this, 'get_bookmark_list' ], $results );
			default:
				return $results;
		}
    }
}
