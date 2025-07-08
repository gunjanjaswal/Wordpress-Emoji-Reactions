<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://gunjanjaswal.me
 * @since      1.0.0
 *
 * @package    WP_Emoji_Reactions
 * @subpackage WP_Emoji_Reactions/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two hooks for
 * enqueuing the admin-specific stylesheet and JavaScript.
 *
 * @package    WP_Emoji_Reactions
 * @subpackage WP_Emoji_Reactions/admin
 * @author     Gunjan Jaswaal <hello@gunjanjaswal.me>
 */
class WP_Emoji_Reactions_Admin {

    /**
     * The ID of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $plugin_name    The ID of this plugin.
     */
    private $plugin_name;

    /**
     * The version of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $version    The current version of this plugin.
     */
    private $version;

    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     * @param    string    $plugin_name       The name of this plugin.
     * @param    string    $version    The version of this plugin.
     */
    public function __construct($plugin_name, $version) {
        $this->plugin_name = $plugin_name;
        $this->version = $version;
    }

    /**
     * Register the stylesheets for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_styles() {
        wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/wp-emoji-reactions-admin.css', array(), $this->version, 'all');
    }

    /**
     * Register the JavaScript for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_scripts() {
        wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/wp-emoji-reactions-admin.js', array('jquery'), $this->version, false);
    }
    
    /**
     * Add an options page under the Settings submenu
     *
     * @since  1.0.0
     */
    public function add_plugin_admin_menu() {
        add_options_page(
            __('WP Emoji Reactions Settings', 'wp-emoji-reactions'),
            __('Emoji Reactions', 'wp-emoji-reactions'),
            'manage_options',
            $this->plugin_name,
            array($this, 'display_plugin_setup_page')
        );
    }
    
    /**
     * Register plugin settings
     *
     * @since  1.0.0
     */
    public function register_settings() {
        register_setting(
            'wp_emoji_reactions_settings',
            'wp_emoji_reactions_enabled_emojis',
            array($this, 'validate_emojis')
        );
        
        register_setting(
            'wp_emoji_reactions_settings',
            'wp_emoji_reactions_position',
            array($this, 'validate_position')
        );
        
        register_setting(
            'wp_emoji_reactions_settings',
            'wp_emoji_reactions_post_types',
            array($this, 'validate_post_types')
        );
        
        register_setting(
            'wp_emoji_reactions_settings',
            'wp_emoji_reactions_custom_names',
            array($this, 'validate_custom_names')
        );
        
        register_setting(
            'wp_emoji_reactions_settings',
            'wp_emoji_reactions_title_text',
            array($this, 'validate_title_text')
        );
        
        add_settings_section(
            'wp_emoji_reactions_general_settings',
            esc_html__('General Settings', 'wp-emoji-reactions'),
            array($this, 'general_settings_section_callback'),
            $this->plugin_name
        );
        
        add_settings_field(
            'wp_emoji_reactions_position',
            esc_html__('Display Position', 'wp-emoji-reactions'),
            array($this, 'position_field_callback'),
            $this->plugin_name,
            'wp_emoji_reactions_general_settings'
        );
        
        add_settings_field(
            'wp_emoji_reactions_post_types',
            esc_html__('Enable on Post Types', 'wp-emoji-reactions'),
            array($this, 'post_types_field_callback'),
            $this->plugin_name,
            'wp_emoji_reactions_general_settings'
        );
        
        add_settings_field(
            'wp_emoji_reactions_title_text',
            esc_html__('Reactions Title Text', 'wp-emoji-reactions'),
            array($this, 'title_text_field_callback'),
            $this->plugin_name,
            'wp_emoji_reactions_general_settings'
        );
        
        add_settings_section(
            'wp_emoji_reactions_emoji_settings',
            __('Emoji Settings', 'wp-emoji-reactions'),
            array($this, 'emoji_settings_section_callback'),
            $this->plugin_name
        );
        
        add_settings_field(
            'wp_emoji_reactions_enabled_emojis',
            __('Enabled Emojis', 'wp-emoji-reactions'),
            array($this, 'emojis_field_callback'),
            $this->plugin_name,
            'wp_emoji_reactions_emoji_settings'
        );
    }
    
    /**
     * Render the settings page for this plugin.
     *
     * @since    1.0.0
     */
    public function display_plugin_setup_page() {
        include_once('partials/wp-emoji-reactions-admin-display.php');
    }
    
    /**
     * General settings section callback
     *
     * @since    1.0.0
     */
    public function general_settings_section_callback() {
        echo '<p>' . esc_html__('Configure how emoji reactions should be displayed on your site.', 'wp-emoji-reactions') . '</p>';
    }
    
    /**
     * Emoji settings section callback
     *
     * @since    1.0.0
     */
    public function emoji_settings_section_callback() {
        echo '<p>' . esc_html__('Select which emoji reactions should be available to your visitors.', 'wp-emoji-reactions') . '</p>';
    }
    
    /**
     * Position field callback
     *
     * @since    1.0.0
     */
    public function position_field_callback() {
        $position = get_option('wp_emoji_reactions_position', 'after_content');
        ?>
        <select name="wp_emoji_reactions_position" id="wp_emoji_reactions_position">
            <option value="after_content" <?php selected($position, 'after_content'); ?>><?php esc_html_e('After Content', 'wp-emoji-reactions'); ?></option>
            <option value="floating" <?php selected($position, 'floating'); ?>><?php esc_html_e('Floating (Fixed Position)', 'wp-emoji-reactions'); ?></option>
        </select>
        <p class="description"><?php esc_html_e('Choose where to display the emoji reactions.', 'wp-emoji-reactions'); ?></p>
        <?php
    }
    
    /**
     * Post types field callback
     *
     * @since    1.0.0
     */
    public function post_types_field_callback() {
        $post_types = get_post_types(array('public' => true), 'objects');
        $enabled_post_types = get_option('wp_emoji_reactions_post_types', array('post'));
        
        echo '<p>' . esc_html__('Select which post types should display emoji reactions:', 'wp-emoji-reactions') . '</p>';
        
        foreach ($post_types as $post_type) {
            $checked = in_array($post_type->name, $enabled_post_types) ? 'checked="checked"' : '';
            echo '<label><input type="checkbox" name="wp_emoji_reactions_post_types[]" value="' . esc_attr($post_type->name) . '" ' . esc_attr($checked) . '> ' . esc_html($post_type->label) . '</label><br>';
        }
        
        echo '<p class="description">' . esc_html__('Select which post types should display emoji reactions.', 'wp-emoji-reactions') . '</p>';
    }
    
    /**
     * Title text field callback
     *
     * @since    1.0.0
     */
    public function title_text_field_callback() {
        $title_text = get_option('wp_emoji_reactions_title_text', __('Reactions:', 'wp-emoji-reactions'));
        
        echo '<input type="text" name="wp_emoji_reactions_title_text" value="' . esc_attr($title_text) . '" class="regular-text">';
        echo '<p class="description">' . esc_html__('Customize the title text shown above reactions. Default: "Reactions:"', 'wp-emoji-reactions') . '</p>';
    }
    
    /**
     * Emojis field callback
     *
     * @since    1.0.0
     */
    public function emojis_field_callback() {
        $default_emojis = array(
            'like' => '👍',
            'love' => '❤️',
            'laugh' => '😂',
            'wow' => '😮',
            'sad' => '😢',
            'angry' => '😠'
        );
        
        $enabled_emojis = get_option('wp_emoji_reactions_enabled_emojis', $default_emojis);
        $custom_names = get_option('wp_emoji_reactions_custom_names', array());
        
        $available_emojis = array(
            'like' => '👍',
            'love' => '❤️',
            'laugh' => '😂',
            'wow' => '😮',
            'sad' => '😢',
            'angry' => '😠',
            'clap' => '👏',
            'thinking' => '🤔',
            'fire' => '🔥',
            'party' => '🎉',
            'thumbs_down' => '👎',
            'eyes' => '👀',
            'rocket' => '🚀',
            'heart_eyes' => '😍',
            'hundred' => '💯',
            'tada' => '🎊'
        );
        
        echo '<div class="emoji-grid">';
        foreach ($available_emojis as $key => $emoji) {
            $checked = isset($enabled_emojis[$key]) ? 'checked="checked"' : '';
            $custom_name = isset($custom_names[$key]) ? $custom_names[$key] : ucfirst(str_replace('_', ' ', $key));
            
            echo '<div class="emoji-item">';
            echo '<label>';
            echo '<input type="checkbox" name="wp_emoji_reactions_enabled_emojis[' . esc_attr($key) . ']" value="' . esc_attr($emoji) . '" ' . esc_attr($checked) . '>';
            echo '<span class="emoji-preview">' . esc_html($emoji) . '</span>';
            echo '<span class="emoji-name">' . esc_html($custom_name) . '</span>';
            echo '</label>';
            echo '<input type="text" name="wp_emoji_reactions_custom_names[' . esc_attr($key) . ']" value="' . esc_attr($custom_name) . '" class="emoji-custom-name" placeholder="' . esc_attr__('Custom name', 'wp-emoji-reactions') . '">';
            echo '</div>';
        }
        echo '</div>';
        
        echo '<p class="description">' . esc_html__('Select which emoji reactions should be available to your visitors. You can customize the display name for each reaction.', 'wp-emoji-reactions') . '</p>';
    }
    
    /**
     * Validate emojis
     *
     * @since    1.0.0
     * @param    array    $input    Selected emojis input.
     * @return   array    Validated emojis.
     */
    public function validate_emojis($input) {
        // If no emojis are selected, use the default set
        if (empty($input)) {
            $default_emojis = array(
                'like' => '👍',
                'love' => '❤️',
                'laugh' => '😂',
                'wow' => '😮',
                'sad' => '😢',
                'angry' => '😠'
            );
            return $default_emojis;
        }
        
        // Make sure we're returning the correct format
        $validated = array();
        foreach ($input as $key => $emoji) {
            if (!empty($emoji)) {
                $validated[$key] = $emoji;
            }
        }
        
        return $validated;
    }
    
    /**
     * Validate position
     *
     * @since    1.0.0
     */
    public function validate_position($input) {
        $valid_positions = array('after_content', 'floating');
        
        if (!in_array($input, $valid_positions)) {
            return 'after_content';
        }
        
        return $input;
    }
    
    /**
     * Validate post types
     *
     * @since    1.0.0
     */
    public function validate_post_types($input) {
        if (empty($input)) {
            return array('post');
        }
        
        $valid_post_types = get_post_types(array('public' => true));
        
        foreach ($input as $key => $post_type) {
            if (!in_array($post_type, $valid_post_types)) {
                unset($input[$key]);
            }
        }
        
        return $input;
    }
    
    /**
     * Validate custom names
     *
     * @since    1.0.0
     * @param    array    $input    Custom names input.
     * @return   array    Validated custom names.
     */
    public function validate_custom_names($input) {
        $validated = array();
        
        if (is_array($input)) {
            foreach ($input as $key => $name) {
                $validated[$key] = sanitize_text_field($name);
                
                // If empty, use default name based on key
                if (empty($validated[$key])) {
                    $validated[$key] = ucfirst(str_replace('_', ' ', $key));
                }
            }
        }
        
        return $validated;
    }
    
    /**
     * Validate title text
     *
     * @since    1.0.0
     * @param    string    $input    Title text input.
     * @return   string    Validated title text.
     */
    public function validate_title_text($input) {
        if (empty($input)) {
            return __('Reactions:', 'wp-emoji-reactions');
        }
        
        return sanitize_text_field($input);
    }
}
