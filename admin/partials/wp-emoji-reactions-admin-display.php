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
        <p><?php esc_html_e('Allow visitors to react to your content with colorful emoji reactions, similar to Facebook reactions.', 'wp-emoji-reactions'); ?></p>
    </div>
    
    <form method="post" action="options.php">
        <?php
        settings_fields('wp_emoji_reactions_settings');
        do_settings_sections($this->plugin_name);
        submit_button();
        ?>
    </form>
    
    <div class="wp-emoji-reactions-preview">
        <h2><?php esc_html_e('Preview', 'wp-emoji-reactions'); ?></h2>
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
            echo '<div class="wp-emoji-reactions-title">' . esc_html__('Reactions:', 'wp-emoji-reactions') . '</div>';
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
        <p class="description"><?php esc_html_e('This is how emoji reactions will appear on your site. The actual appearance may vary based on your theme.', 'wp-emoji-reactions'); ?></p>
    </div>
    
    <div class="wp-emoji-reactions-stats">
        <h2><?php esc_html_e('Statistics', 'wp-emoji-reactions'); ?></h2>
        <?php
        global $wpdb;
        $table_name = $wpdb->prefix . 'emoji_reactions';
        
        // Check if table exists
        if ($wpdb->get_var($wpdb->prepare("SHOW TABLES LIKE %s", $table_name)) == $table_name) {
            $total_reactions = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}emoji_reactions");
            $most_reacted_post_id = $wpdb->get_var("SELECT post_id FROM {$wpdb->prefix}emoji_reactions GROUP BY post_id ORDER BY COUNT(*) DESC LIMIT 1");
            $most_used_emoji = $wpdb->get_var("SELECT emoji_id FROM {$wpdb->prefix}emoji_reactions GROUP BY emoji_id ORDER BY COUNT(*) DESC LIMIT 1");
            
            // Translators: %s is the total number of reactions
            echo '<p>' . esc_html(sprintf(__('Total reactions: %s', 'wp-emoji-reactions'), esc_html($total_reactions))) . '</p>';
            
            if ($most_reacted_post_id) {
                // Translators: %1$s is the post permalink, %2$s is the post title
                // Translators: %1$s is the post permalink, %2$s is the post title
                echo '<p>' . wp_kses(sprintf(__('Most reacted post: <a href="%1$s" target="_blank">%2$s</a>', 'wp-emoji-reactions'), esc_url(get_permalink($most_reacted_post_id)), esc_html(get_the_title($most_reacted_post_id))), array('a' => array('href' => array(), 'target' => array()))) . '</p>';
            }
            
            if ($most_used_emoji) {
                $emoji_display = isset($enabled_emojis[$most_used_emoji]) ? $enabled_emojis[$most_used_emoji] : '';
                // Translators: %1$s is the emoji symbol, %2$s is the reaction name
                echo '<p>' . sprintf(esc_html__('Most used reaction: %1$s %2$s', 'wp-emoji-reactions'), esc_html($emoji_display), esc_html(ucfirst(str_replace('_', ' ', $most_used_emoji)))) . '</p>';
            }
        } else {
            echo '<p>' . esc_html__('No reactions yet.', 'wp-emoji-reactions') . '</p>';
        }
        ?>
    </div>
</div>
