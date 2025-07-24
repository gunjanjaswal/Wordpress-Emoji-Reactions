<?php
/**
 * The core plugin class.
 *
 * @link       https://gunjanjaswal.me
 * @since      1.0.0
 *
 * @package    WP_Emoji_Reactions
 * @subpackage WP_Emoji_Reactions/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * @since      1.0.0
 * @package    WP_Emoji_Reactions
 * @subpackage WP_Emoji_Reactions/includes
 * @author     Gunjan Jaswaal <hello@gunjanjaswal.me>
 */
class WP_Emoji_Reactions {

    /**
     * The loader that's responsible for maintaining and registering all hooks that power
     * the plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      WP_Emoji_Reactions_Loader    $loader    Maintains and registers all hooks for the plugin.
     */
    protected $loader;

    /**
     * The unique identifier of this plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      string    $plugin_name    The string used to uniquely identify this plugin.
     */
    protected $plugin_name;

    /**
     * The current version of the plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      string    $version    The current version of the plugin.
     */
    protected $version;

    /**
     * Define the core functionality of the plugin.
     *
     * @since    1.0.0
     */
    public function __construct() {
        if (defined('EMOJIS_FOR_POSTS_VERSION')) {
            $this->version = EMOJIS_FOR_POSTS_VERSION;
        } else {
            $this->version = '1.0.0';
        }
        $this->plugin_name = 'emojis-for-posts-and-pages';

        $this->load_dependencies();
        $this->set_locale();
        $this->define_admin_hooks();
        $this->define_public_hooks();
    }

    /**
     * Load the required dependencies for this plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function load_dependencies() {

        /**
         * The class responsible for orchestrating the actions and filters of the
         * core plugin.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-wp-emoji-reactions-loader.php';

        /**
         * The class responsible for defining internationalization functionality
         * of the plugin.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-wp-emoji-reactions-i18n.php';

        /**
         * The class responsible for defining all actions that occur in the admin area.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'admin/class-wp-emoji-reactions-admin.php';

        /**
         * The class responsible for defining all actions that occur in the public-facing
         * side of the site.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'public/class-wp-emoji-reactions-public.php';

        $this->loader = new WP_Emoji_Reactions_Loader();
    }

    /**
     * Define the locale for this plugin for internationalization.
     *
     * @since    1.0.0
     * @access   private
     */
    private function set_locale() {

        $plugin_i18n = new WP_Emoji_Reactions_i18n();
        
        // Since WordPress 4.6+, translations are automatically loaded for plugins on WordPress.org
        // This hook is kept for backward compatibility with older WordPress versions
        $this->loader->add_action('plugins_loaded', $plugin_i18n, 'load_plugin_textdomain');
    }

    /**
     * Register all of the hooks related to the admin area functionality
     * of the plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function define_admin_hooks() {

        $plugin_admin = new WP_Emoji_Reactions_Admin($this->get_plugin_name(), $this->get_version());

        $this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_styles');
        $this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts');
        
        // Add admin menu
        $this->loader->add_action('admin_menu', $plugin_admin, 'add_plugin_admin_menu');
        
        // Register settings
        $this->loader->add_action('admin_init', $plugin_admin, 'register_settings');
    }

    /**
     * Register all of the hooks related to the public-facing functionality
     * of the plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function define_public_hooks() {

        $plugin_public = new WP_Emoji_Reactions_Public($this->get_plugin_name(), $this->get_version());

        $this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_styles');
        $this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_scripts');
        
        // Add emoji reactions to content
        $position = get_option('wp_emoji_reactions_position', 'after_content');
        if ($position === 'after_content') {
            $this->loader->add_filter('the_content', $plugin_public, 'display_emoji_reactions');
        } else {
            $this->loader->add_action('wp_footer', $plugin_public, 'display_floating_emoji_reactions');
        }
        
        // Handle AJAX requests for emoji reactions
        $this->loader->add_action('wp_ajax_emoji_reaction', $plugin_public, 'handle_emoji_reaction');
        $this->loader->add_action('wp_ajax_nopriv_emoji_reaction', $plugin_public, 'handle_emoji_reaction');
        
        // Get emoji reaction counts
        $this->loader->add_action('wp_ajax_get_emoji_reactions', $plugin_public, 'get_emoji_reactions');
        $this->loader->add_action('wp_ajax_nopriv_get_emoji_reactions', $plugin_public, 'get_emoji_reactions');
    }

    /**
     * Run the loader to execute all of the hooks with WordPress.
     *
     * @since    1.0.0
     */
    public function run() {
        $this->loader->run();
    }

    /**
     * The name of the plugin used to uniquely identify it within the context of
     * WordPress and to define internationalization functionality.
     *
     * @since     1.0.0
     * @return    string    The name of the plugin.
     */
    public function get_plugin_name() {
        return $this->plugin_name;
    }

    /**
     * The reference to the class that orchestrates the hooks with the plugin.
     *
     * @since     1.0.0
     * @return    WP_Emoji_Reactions_Loader    Orchestrates the hooks of the plugin.
     */
    public function get_loader() {
        return $this->loader;
    }

    /**
     * Retrieve the version number of the plugin.
     *
     * @since     1.0.0
     * @return    string    The version number of the plugin.
     */
    public function get_version() {
        return $this->version;
    }
}
