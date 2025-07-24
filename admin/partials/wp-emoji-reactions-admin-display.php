<?php
/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://gunjanjaswal.me
 * @since      1.0.0
 *
 * @package    WP_Emoji_Reactions
 * @subpackage WP_Emoji_Reactions/admin/partials
 */
?>

<div class="wrap">
    <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
    
    <div class="notice notice-info">
        <p><?php esc_html_e('Allow visitors to react to your content with colorful emoji reactions, similar to Facebook reactions.', 'emojis-for-posts-and-pages'); ?></p>
    </div>
    
    <form method="post" action="options.php">
        <?php
        settings_fields('wp_emoji_reactions_settings');
        do_settings_sections($this->plugin_name);
        submit_button();
        ?>
    </form>
    
    <div class="wp-emoji-reactions-preview">
        <h2><?php esc_html_e('Preview', 'emojis-for-posts-and-pages'); ?></h2>
        <div class="preview-container">
            <?php
            $enabled_emojis = get_option('wp_emoji_reactions_enabled_emojis', array(
                'like' => '👍',
                'love' => '❤️',
                'laugh' => '😂',
                'wow' => '😮',
                'sad' => '😢',
                'angry' => '😠'
            ));
            
            echo '<div class="wp-emoji-reactions-container">';
            echo '<div class="wp-emoji-reactions-title">' . esc_html__('Reactions:', 'emojis-for-posts-and-pages') . '</div>';
            echo '<div class="wp-emoji-reactions-buttons">';
            
            foreach ($enabled_emojis as $key => $emoji) {
                echo '<div class="wp-emoji-reaction-button" data-reaction="' . esc_attr($key) . '">';
                echo '<span class="emoji">' . esc_html($emoji) . '</span>';
                echo '<span class="count">0</span>';
                echo '</div>';
            }
            
            echo '</div>'; // .wp-emoji-reactions-buttons
            echo '</div>'; // .wp-emoji-reactions-container
            ?>
        </div>
        <p class="description"><?php esc_html_e('This is how emoji reactions will appear on your site. The actual appearance may vary based on your theme.', 'emojis-for-posts-and-pages'); ?></p>
    </div>
    
    <div class="wp-emoji-reactions-stats">
        <h2><?php esc_html_e('Statistics', 'emojis-for-posts-and-pages'); ?></h2>
        <?php
        // Get cached statistics or fetch from database
        $stats = wp_cache_get( 'wp_emoji_reactions_admin_stats' );
        
        if ( false === $stats ) {
            global $wpdb;
            $table_name = $wpdb->prefix . 'emoji_reactions';
            $stats = array(
                'total_reactions' => 0,
                'most_reacted_post_id' => 0,
                'most_used_emoji' => ''
            );
            
            // Check if table exists - using cached value
            $table_exists = wp_cache_get( 'wp_emoji_reactions_table_exists' );
            if ( false === $table_exists ) {
                // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- Caching is implemented right after this call
                $table_exists = $wpdb->get_var( $wpdb->prepare( "SHOW TABLES LIKE %s", $table_name ) ) === $table_name;
                wp_cache_set( 'wp_emoji_reactions_table_exists', $table_exists, '', HOUR_IN_SECONDS );
            }
            
            if ( $table_exists ) {
                // Get total reactions count - check cache first
                $total_reactions_key = 'wp_emoji_reactions_total_count';
                $total_reactions = wp_cache_get( $total_reactions_key );
                if ( false === $total_reactions ) {
                    // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- Caching is implemented right after this call
                    $total_reactions = $wpdb->get_var(
                        // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Table name is hardcoded with prefix
                        "SELECT COUNT(*) FROM {$wpdb->prefix}emoji_reactions"
                    );
                    wp_cache_set( $total_reactions_key, $total_reactions, '', HOUR_IN_SECONDS );
                }
                $stats['total_reactions'] = $total_reactions;
                
                // Get most reacted post - check cache first
                $most_reacted_post_key = 'wp_emoji_reactions_most_reacted_post';
                $most_reacted_post_id = wp_cache_get( $most_reacted_post_key );
                if ( false === $most_reacted_post_id ) {
                    // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- Caching is implemented right after this call
                    $most_reacted_post_id = $wpdb->get_var(
                        // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Table name is hardcoded with prefix
                        "SELECT post_id FROM {$wpdb->prefix}emoji_reactions GROUP BY post_id ORDER BY COUNT(*) DESC LIMIT 1"
                    );
                    wp_cache_set( $most_reacted_post_key, $most_reacted_post_id, '', HOUR_IN_SECONDS );
                }
                $stats['most_reacted_post_id'] = $most_reacted_post_id;
                
                // Get most used emoji - check cache first
                $most_used_emoji_key = 'wp_emoji_reactions_most_used_emoji';
                $most_used_emoji = wp_cache_get( $most_used_emoji_key );
                if ( false === $most_used_emoji ) {
                    // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- Caching is implemented right after this call
                    $most_used_emoji = $wpdb->get_var(
                        // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Table name is hardcoded with prefix
                        "SELECT emoji_id FROM {$wpdb->prefix}emoji_reactions GROUP BY emoji_id ORDER BY COUNT(*) DESC LIMIT 1"
                    );
                    wp_cache_set( $most_used_emoji_key, $most_used_emoji, '', HOUR_IN_SECONDS );
                }
                $stats['most_used_emoji'] = $most_used_emoji;
                
                // Cache the results for 1 hour
                wp_cache_set( 'wp_emoji_reactions_admin_stats', $stats, '', HOUR_IN_SECONDS );
            }
        }
        
        $total_reactions = $stats['total_reactions'];
        $most_reacted_post_id = $stats['most_reacted_post_id'];
        $most_used_emoji = $stats['most_used_emoji'];
            
        // Translators: %s is the total number of reactions
        echo '<p>' . esc_html(sprintf(__('Total reactions: %s', 'emojis-for-posts-and-pages'), esc_html($total_reactions))) . '</p>';
        
        if ($most_reacted_post_id) {
            // Translators: %1$s is the post permalink, %2$s is the post title
            echo '<p>' . wp_kses(sprintf(__('Most reacted post: <a href="%1$s" target="_blank">%2$s</a>', 'emojis-for-posts-and-pages'), esc_url(get_permalink($most_reacted_post_id)), esc_html(get_the_title($most_reacted_post_id))), array('a' => array('href' => array(), 'target' => array()))) . '</p>';
        }
        
        if ($most_used_emoji) {
            $emoji_display = isset($enabled_emojis[$most_used_emoji]) ? $enabled_emojis[$most_used_emoji] : '';
            // Translators: %1$s is the emoji symbol, %2$s is the reaction name
            echo '<p>' . sprintf(esc_html__('Most used reaction: %1$s %2$s', 'emojis-for-posts-and-pages'), esc_html($emoji_display), esc_html(ucfirst(str_replace('_', ' ', $most_used_emoji)))) . '</p>';
        }
        ?>
    </div>
</div>
