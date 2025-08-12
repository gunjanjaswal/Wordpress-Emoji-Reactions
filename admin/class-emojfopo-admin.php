<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://gunjanjaswal.me
 * @since      1.0.0
 *
 * @package    Emojis_For_Posts_And_Pages
 * @subpackage Emojis_For_Posts_And_Pages/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two hooks for
 * enqueuing the admin-specific stylesheet and JavaScript.
 *
 * @package    Emojis_For_Posts_And_Pages
 * @subpackage Emojis_For_Posts_And_Pages/admin
 * @author     Gunjan Jaswaal <hello@gunjanjaswal.me>
 */
class Emojfopo_Admin {

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
        wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/emojfopo-admin.css', array(), $this->version, 'all');
    }

    /**
     * Register the JavaScript for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_scripts() {
        wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/emojfopo-admin.js', array('jquery'), $this->version, false);
        
        wp_localize_script(
            $this->plugin_name,
            'emojfopo_admin',
            array(
                'no_emojis_selected' => esc_html__('No emoji reactions selected. Please select at least one.', 'emojis-for-posts-and-pages'),
                'nonce' => wp_create_nonce('emojfopo_admin_nonce')
            )
        );
    }
    
    /**
     * Add an options page under the Settings submenu
     *
     * @since  1.0.0
     */
    public function add_plugin_admin_menu() {
        add_options_page(
            __('Emojis for Posts and Pages Settings', 'emojis-for-posts-and-pages'),
            __('Emoji Reactions', 'emojis-for-posts-and-pages'),
            'manage_options',
            $this->plugin_name,
            array($this, 'display_plugin_setup_page')
        );
    }
    
    /**
     * Add donate link to plugin action links
     *
     * @since  1.0.0
     * @param  array  $links Array of plugin action links
     * @param  string $file  Plugin file path
     * @return array  Modified array of plugin action links
     */
    public function add_plugin_action_links($links, $file) {
        $plugin_basename = plugin_basename(plugin_dir_path(__DIR__) . 'emojis-for-posts-and-pages.php');
        
        // Check if we're on the correct plugin
        if (plugin_basename($file) === $plugin_basename) {
            // Add donate link at the end
            $donate_link = '<a href="https://buymeacoffee.com/gunjanjaswal" target="_blank" style="color:#3db634;font-weight:bold;">' . __('Donate', 'emojis-for-posts-and-pages') . '</a>';
            $links[] = $donate_link; // Add to the end of the array
        }
        
        return $links;
    }
    
    /**
     * Register plugin settings
     *
     * @since  1.0.0
     */
    public function register_settings() {
        register_setting(
            'emojfopo_settings',
            'emojfopo_enabled_emojis',
            array($this, 'validate_emojis')
        );
        
        register_setting(
            'emojfopo_settings',
            'emojfopo_position',
            array($this, 'validate_position')
        );
        
        register_setting(
            'emojfopo_settings',
            'emojfopo_post_types',
            array($this, 'validate_post_types')
        );
        
        register_setting(
            'emojfopo_settings',
            'emojfopo_custom_names',
            array($this, 'validate_custom_names')
        );
        
        register_setting(
            'emojfopo_settings',
            'emojfopo_title_text',
            array($this, 'validate_title_text')
        );
        
        add_settings_section(
            'emojfopo_general_settings',
            esc_html__('General Settings', 'emojis-for-posts-and-pages'),
            array($this, 'general_settings_section_callback'),
            $this->plugin_name
        );
        
        add_settings_field(
            'emojfopo_position',
            esc_html__('Display Position', 'emojis-for-posts-and-pages'),
            array($this, 'position_field_callback'),
            $this->plugin_name,
            'emojfopo_general_settings'
        );
        
        add_settings_field(
            'emojfopo_post_types',
            esc_html__('Enable on Post Types', 'emojis-for-posts-and-pages'),
            array($this, 'post_types_field_callback'),
            $this->plugin_name,
            'emojfopo_general_settings'
        );
        
        add_settings_field(
            'emojfopo_title_text',
            esc_html__('Reactions Title Text', 'emojis-for-posts-and-pages'),
            array($this, 'title_text_field_callback'),
            $this->plugin_name,
            'emojfopo_general_settings'
        );
        
        add_settings_section(
            'emojfopo_emoji_settings',
            __('Emoji Settings', 'emojis-for-posts-and-pages'),
            array($this, 'emoji_settings_section_callback'),
            $this->plugin_name
        );
        
        add_settings_field(
            'emojfopo_enabled_emojis',
            __('Enabled Emojis', 'emojis-for-posts-and-pages'),
            array($this, 'emojis_field_callback'),
            $this->plugin_name,
            'emojfopo_emoji_settings'
        );
    }
    
    /**
     * Render the settings page for this plugin.
     *
     * @since    1.0.0
     */
    public function display_plugin_setup_page() {
        include_once('partials/emojfopo-admin-display.php');
    }
    
    /**
     * General settings section callback
     *
     * @since    1.0.0
     */
    public function general_settings_section_callback() {
        echo '<p>' . esc_html__('Configure how emoji reactions should be displayed on your site.', 'emojis-for-posts-and-pages') . '</p>';
    }
    
    /**
     * Emoji settings section callback
     *
     * @since    1.0.0
     */
    public function emoji_settings_section_callback() {
        echo '<p>' . esc_html__('Select which emoji reactions should be available to your visitors.', 'emojis-for-posts-and-pages') . '</p>';
    }
    
    /**
     * Position field callback
     *
     * @since    1.0.0
     */
    public function position_field_callback() {
        $position = get_option('emojfopo_position', 'after_content');
        ?>
        <select name="emojfopo_position" id="emojfopo_position">
            <option value="after_content" <?php selected($position, 'after_content'); ?>><?php esc_html_e('After Content', 'emojis-for-posts-and-pages'); ?></option>
            <option value="floating" <?php selected($position, 'floating'); ?>><?php esc_html_e('Floating (Fixed Position)', 'emojis-for-posts-and-pages'); ?></option>
        </select>
        <p class="description"><?php esc_html_e('Choose where to display the emoji reactions.', 'emojis-for-posts-and-pages'); ?></p>
        <?php
    }
    
    /**
     * Post types field callback
     *
     * @since    1.0.0
     */
    public function post_types_field_callback() {
        $post_types = get_post_types(array('public' => true), 'objects');
        $enabled_post_types = get_option('emojfopo_post_types', array('post'));
        
        echo '<p>' . esc_html__('Select which post types should display emoji reactions:', 'emojis-for-posts-and-pages') . '</p>';
        
        foreach ($post_types as $post_type) {
            $checked = in_array($post_type->name, $enabled_post_types) ? 'checked="checked"' : '';
            echo '<label><input type="checkbox" name="emojfopo_post_types[]" value="' . esc_attr($post_type->name) . '" ' . esc_attr($checked) . '> ' . esc_html($post_type->label) . '</label><br>';
        }
        
        echo '<p class="description">' . esc_html__('Select which post types should display emoji reactions.', 'emojis-for-posts-and-pages') . '</p>';
    }
    
    /**
     * Title text field callback
     *
     * @since    1.0.0
     */
    public function title_text_field_callback() {
        $title_text = get_option('emojfopo_title_text', __('Reactions:', 'emojis-for-posts-and-pages'));
        
        echo '<input type="text" name="emojfopo_title_text" value="' . esc_attr($title_text) . '" class="regular-text">';
        echo '<p class="description">' . esc_html__('Customize the title text shown above reactions. Default: "Reactions:"', 'emojis-for-posts-and-pages') . '</p>';
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
        
        $enabled_emojis = get_option('emojfopo_enabled_emojis', $default_emojis);
        $custom_names = get_option('emojfopo_custom_names', array());
        
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
            echo '<input type="checkbox" name="emojfopo_enabled_emojis[' . esc_attr($key) . ']" value="' . esc_attr($emoji) . '" ' . esc_attr($checked) . '>';
            echo '<span class="emoji-preview">' . esc_html($emoji) . '</span>';
            echo '<span class="emoji-name">' . esc_html($custom_name) . '</span>';
            echo '</label>';
            echo '<input type="text" name="emojfopo_custom_names[' . esc_attr($key) . ']" value="' . esc_attr($custom_name) . '" class="emoji-custom-name" placeholder="' . esc_attr__('Custom name', 'emojis-for-posts-and-pages') . '">';
            echo '</div>';
        }
        echo '</div>';
        
        echo '<p class="description">' . esc_html__('Select which emoji reactions should be available to your visitors. You can customize the display name for each reaction.', 'emojis-for-posts-and-pages') . '</p>';
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
            return __('Reactions:', 'emojis-for-posts-and-pages');
        }
        
        return sanitize_text_field($input);
    }
}
