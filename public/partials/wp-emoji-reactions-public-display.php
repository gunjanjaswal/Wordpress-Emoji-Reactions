<?php
/**
 * Provide a public-facing view for the plugin
 *
 * This file is used to markup the public-facing aspects of the plugin.
 *
 * @link       https://gunjanjaswal.me
 * @since      1.0.0
 *
 * @package    WP_Emoji_Reactions
 * @subpackage WP_Emoji_Reactions/public/partials
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
?>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->
<div class="<?php echo esc_attr($container_class); ?>" data-post-id="<?php echo esc_attr($post_id); ?>">
    <?php if (!is_archive() && !is_home() && !is_front_page()): ?>
    <div class="emojfopo-title"><?php echo esc_html(get_option('emojfopo_title_text', esc_html__('Reactions:', 'emojis-for-posts-and-pages'))); ?></div>
    <?php endif; ?>
    <div class="emojfopo-buttons">
        <?php foreach ($enabled_emojis as $key => $emoji) : ?>
            <?php $count = isset($reaction_counts[$key]) ? $reaction_counts[$key] : 0; ?>
            <?php $active_class = ($user_reaction === $key) ? 'active' : ''; ?>
            <?php $custom_name = isset($custom_names[$key]) ? $custom_names[$key] : ucfirst(str_replace('_', ' ', $key)); ?>
            <button class="emojfopo-reaction-button<?php echo esc_attr($user_reaction === $key ? ' active' : ''); ?>" data-reaction="<?php echo esc_attr($key); ?>" title="<?php echo esc_attr(isset($custom_names[$key]) ? $custom_names[$key] : ucfirst($key)); ?>">
                <span class="emoji"><?php echo esc_html($emoji); ?></span>
                <span class="count"><?php echo esc_html($count); ?></span>
            </button>
        <?php endforeach; ?>
    </div>
    
    <?php if ($user_reaction) : ?>
        <div class="emojfopo-message">
            <?php 
            esc_html_e('You reacted with', 'emojis-for-posts-and-pages'); 
            echo ' ' . esc_html($enabled_emojis[$user_reaction]); 
            ?>
        </div>
    <?php endif; ?>
</div>
