<?php
/**
 * Provide a public-facing view for the plugin
 *
 * This file is used to markup the public-facing aspects of the plugin.
 *
 * @link       https://gunjanjaswal.me
 * @since      1.0.0
 *
 * @package    Emojis_For_Posts_And_Pages
 * @subpackage Emojis_For_Posts_And_Pages/public/partials
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
        <?php foreach ($enabled_emojis as $emojfopo_key => $emojfopo_emoji) : ?>
            <?php $emojfopo_count = isset($reaction_counts[$emojfopo_key]) ? $reaction_counts[$emojfopo_key] : 0; ?>
            <?php $emojfopo_active_class = ($user_reaction === $emojfopo_key) ? 'active' : ''; ?>
            <?php $emojfopo_custom_name = isset($custom_names[$emojfopo_key]) ? $custom_names[$emojfopo_key] : ucfirst(str_replace('_', ' ', $emojfopo_key)); ?>
            <button class="emojfopo-reaction-button<?php echo esc_attr($user_reaction === $emojfopo_key ? ' active' : ''); ?>" data-reaction="<?php echo esc_attr($emojfopo_key); ?>" title="<?php echo esc_attr(isset($custom_names[$emojfopo_key]) ? $custom_names[$emojfopo_key] : ucfirst($emojfopo_key)); ?>">
                <span class="emoji"><?php echo esc_html($emojfopo_emoji); ?></span>
                <span class="count"><?php echo esc_html($emojfopo_count); ?></span>
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
