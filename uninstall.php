<?php
/**
 * Fired when the plugin is uninstalled.
 *
 * @link       https://gunjanjaswal.me
 * @since      1.0.0
 *
 * @package    Emojis_For_Posts_And_Pages
 */

// If uninstall not called from WordPress, then exit.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    exit;
}

// Clear all caches related to this plugin
$emojfopo_cache_keys = array(
    'emojfopo_table_exists',
    'emojfopo_admin_stats'
);

foreach ( $emojfopo_cache_keys as $emojfopo_key ) {
    wp_cache_delete( $emojfopo_key );
}

// Also clear any post-specific caches
// This is a best-effort approach as we can't know all post IDs
// that might have reactions without querying the database

// Delete options
delete_option( 'emojfopo_enabled_emojis' );
delete_option( 'emojfopo_position' );
delete_option( 'emojfopo_post_types' );
delete_option( 'emojfopo_title_text' );
delete_option( 'emojfopo_custom_names' );

// Delete database table
global $wpdb;
$emojfopo_table_name = $wpdb->prefix . 'emojfopo_reactions';

// phpcs:disable
/*
 * The following database operation is intentionally exempt from WordPress coding standards:
 * 1. Direct database query is necessary during uninstall to remove the plugin's table
 * 2. Caching is not needed as this is a one-time operation during uninstall
 * 3. Schema change is required to completely remove the plugin's footprint
 */
$wpdb->query( "DROP TABLE IF EXISTS `{$wpdb->prefix}emojfopo_reactions`" );
// phpcs:enable
