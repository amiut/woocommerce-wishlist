<?php
/**
 * Install class
 *
 * @package WooCommerceWishlist
 * @since   1.0
 */

namespace Dornaweb\WooCommerceWishlist;

defined('ABSPATH') || exit;

class Install
{
    /**
	 * Hook in
	 */
	public static function init() {
        add_filter( 'wpmu_drop_tables', array( __CLASS__, 'wpmu_drop_tables' ) );
    }

    public static function install() {
        if ( ! is_blog_installed() ) {
			return;
        }

		// Check if we are not already running this routine.
		if ( 'yes' === get_transient( 'dweb_wishlist_plugin_installing' ) ) {
			return;
        }

		// If we made it till here nothing is running yet, lets set the transient now.
		set_transient( 'dweb_wishlist_plugin_installing', 'yes', MINUTE_IN_SECONDS * 10 );
		self::create_tables();
		self::setup_stuffs();
		self::create_options();
		self::create_roles();
		self::create_cron_jobs();
		self::create_files();
		self::update_plugin_version();
		self::maybe_update_db_version();
		flush_rewrite_rules();
		delete_transient( 'dweb_wishlist_plugin_installing' );
        do_action('dweb_wishlist_plugin_installed');
    }


	/**
	 * Register stuffs like post types, taxonomies, endpoints, ...
	 *
	 * @since 3.2.0
	 */
	private static function setup_stuffs() {
        // Post types and stuff
	}

    /**
     * Create or update database tables
     */
    private static function create_tables() {
        global $wpdb;
        $wpdb->hide_errors();

        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

        dbDelta( self::get_schema() );
    }

    /**
     * Create options
     */
    public static function create_options() {}

    /**
     * Create user roles with permissions
     */
    public static function create_roles() {}

    /**
     * Create cron jobs
     *
     * @uses WP_Cron
     */
    public static function create_cron_jobs() {}

    /**
     * Create needed files and directories
     */
    public static function create_files() {}

    /**
     * Update plugin version
     */
    public static function update_plugin_version() {
        update_option('dweb_wishlist_version', '1.0');
    }

    /**
     * update database version
     */
    public static function maybe_update_db_version() {
        update_option( 'dweb_wishlist_db_version', '1.0' );
    }

    /**
     * Database chema
     * @return string
     */
    private static function get_schema() {
		global $wpdb;
		$collate = '';
		if ( $wpdb->has_cap( 'collation' ) ) {
			$collate = $wpdb->get_charset_collate();
        }

        /*
		 * Indexes have a maximum size of 767 bytes. Historically, we haven't need to be concerned about that.
		 * As of WP 4.2, however, they moved to utf8mb4, which uses 4 bytes per character. This means that an index which
		 * used to have room for floor(767/3) = 255 characters, now only has room for floor(767/4) = 191 characters.
		 */
		$max_index_length = 191;

        $tables = "
CREATE TABLE {$wpdb->prefix}dweb_bookmarks(
    ID bigint(20) UNSIGNED NOT NULL auto_increment,
    entry_id bigint(20) UNSIGNED NOT NULL DEFAULT 0,
    variation_id bigint(20) UNSIGNED NOT NULL DEFAULT 0,
    list_id bigint(20) UNSIGNED NOT NULL DEFAULT 0,
    user_id bigint(20) UNSIGNED NOT NULL DEFAULT 0,
    note longtext NULL,
    PRIMARY KEY (ID),
    KEY entry_id (entry_id),
    KEY list_id (list_id),
    KEY user_id (user_id)
) $collate;
CREATE TABLE {$wpdb->prefix}dweb_bookmark_lists (
  ID BIGINT UNSIGNED NOT NULL auto_increment,
  title varchar(250),
  slug varchar(200),
  is_public tinyint(1) NOT NULL DEFAULT '0',
  description longtext NULL,
  user_id bigint(20) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (ID),
  KEY user_id (user_id),
  KEY is_public (is_public),
  UNIQUE KEY slug (slug($max_index_length))
) $collate;
        ";

        return $tables;
    }

	/**
	 * Return a list of Tables
	 *
	 * @return array Plugins tables.
	 */
	public static function get_tables() {
        global $wpdb;

		$tables = array(
			"{$wpdb->prefix}dweb_bookmarks",
			"{$wpdb->prefix}dweb_bookmark_lists",
        );

		$tables = apply_filters( 'dweb_wishlist_install_get_tables', $tables );
		return $tables;
    }

	/**
	 * Drop All tables.
	 *
	 * @return void
	 */
	public static function drop_tables() {
		global $wpdb;
		$tables = self::get_tables();
		foreach ( $tables as $table ) {
			$wpdb->query( "DROP TABLE IF EXISTS {$table}" );
		}
    }

	/**
	 * Uninstall tables when MU blog is deleted.
	 *
	 * @param array $tables List of tables that will be deleted by WP.
	 *
	 * @return string[]
	 */
	public static function wpmu_drop_tables( $tables ) {
		return array_merge( $tables, self::get_tables() );
	}
}
