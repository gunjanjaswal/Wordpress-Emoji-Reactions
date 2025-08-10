<?php
/**
 * Fired during plugin deactivation.
 *
 * @link       https://gunjanjaswal.me
 * @since      1.0.0
 *
 * @package    WP_Emoji_Reactions
 * @subpackage WP_Emoji_Reactions/includes
 */

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      1.0.0
 * @package    WP_Emoji_Reactions
 * @subpackage WP_Emoji_Reactions/includes
 * @author     Gunjan Jaswaal <hello@gunjanjaswal.me>
 */
class WP_Emoji_Reactions_Deactivator {

    /**
     * Plugin deactivation functionality.
     *
     * @since    1.0.0
     */
    public static function deactivate() {
        // We don't delete the database tables on deactivation
        // This ensures user data is preserved if the plugin is reactivated
        
        // If you want to clean up completely, uncomment the following code
        /*
        global $wpdb;
        $table_name = $wpdb->prefix . 'emoji_reactions';
        $wpdb->query("DROP TABLE IF EXISTS $table_name");
        
        delete_option('wp_emoji_reactions_enabled_emojis');
        delete_option('wp_emoji_reactions_position');
        delete_option('wp_emoji_reactions_post_types');
        */
    }
}
