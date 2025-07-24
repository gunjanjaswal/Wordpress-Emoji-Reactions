<?php
/**
 * Fired when the plugin is uninstalled.
 *
 * @link       https://gunjanjaswal.me
 * @since      1.0.0
 *
 * @package    WP_Emoji_Reactions
 */

// If uninstall not called from WordPress, then exit.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    exit;
}

// Clear all caches related to this plugin
$cache_keys = array(
    'wp_emoji_reactions_table_exists',
    'wp_emoji_reactions_admin_stats'
);

foreach ( $cache_keys as $key ) {
    wp_cache_delete( $key );
}

// Also clear any post-specific caches
// This is a best-effort approach as we can't know all post IDs
// that might have reactions without querying the database

// Delete options
delete_option( 'wp_emoji_reactions_enabled_emojis' );
delete_option( 'wp_emoji_reactions_position' );
delete_option( 'wp_emoji_reactions_post_types' );

// Delete database table
global $wpdb;
$table_name = $wpdb->prefix . 'emoji_reactions';

// phpcs:disable
/*
 * The following database operation is intentionally exempt from WordPress coding standards:
 * 1. Direct database query is necessary during uninstall to remove the plugin's table
 * 2. Caching is not needed as this is a one-time operation during uninstall
 * 3. Schema change is required to completely remove the plugin's footprint
 */
$wpdb->query( "DROP TABLE IF EXISTS `{$wpdb->prefix}emoji_reactions`" );
// phpcs:enable
