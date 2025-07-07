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
if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

// Delete options
delete_option('wp_emoji_reactions_enabled_emojis');
delete_option('wp_emoji_reactions_position');
delete_option('wp_emoji_reactions_post_types');

// Delete database table
global $wpdb;
$table_name = $wpdb->prefix . 'emoji_reactions';
$wpdb->query($wpdb->prepare("DROP TABLE IF EXISTS %s", $table_name));
