<?php
/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://gunjanjaswal.me
 * @since      1.0.0
 *
 * @package    WP_Emoji_Reactions
 * @subpackage WP_Emoji_Reactions/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and hooks for
 * the public-facing side of the site.
 *
 * @package    WP_Emoji_Reactions
 * @subpackage WP_Emoji_Reactions/public
 * @author     Gunjan Jaswaal <hello@gunjanjaswal.me>
 */
class WP_Emoji_Reactions_Public {

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
     * @param    string    $plugin_name       The name of the plugin.
     * @param    string    $version    The version of this plugin.
     */
    public function __construct($plugin_name, $version) {
        $this->plugin_name = $plugin_name;
        $this->version = $version;
    }

    /**
     * Register the stylesheets for the public-facing side of the site.
     *
     * @since    1.0.0
     * @return   void
     */
    public function enqueue_styles() {
        wp_enqueue_style(
            $this->plugin_name,
            plugin_dir_url( __FILE__ ) . 'css/wp-emoji-reactions-public.css',
            array(),
            $this->version,
            'all'
        );
        
        // Load Noto Color Emoji font
        wp_enqueue_style(
            $this->plugin_name . '-noto-emoji',
            'https://fonts.googleapis.com/css2?family=Noto+Color+Emoji&display=swap',
            array(),
            $this->version
        );
    }

    /**
     * Register the JavaScript for the public-facing side of the site.
     *
     * @since    1.0.0
     * @return   void
     */
    public function enqueue_scripts() {
        wp_enqueue_script(
            $this->plugin_name,
            plugin_dir_url( __FILE__ ) . 'js/wp-emoji-reactions-public.js',
            array( 'jquery' ),
            $this->version,
            true // Load in footer for better performance
        );
        
        // Get custom reaction names
        $custom_names = get_option( 'wp_emoji_reactions_custom_names', array() );
        
        wp_localize_script(
            $this->plugin_name,
            'wp_emoji_reactions',
            array(
                'ajax_url'        => admin_url( 'admin-ajax.php' ),
                'nonce'           => wp_create_nonce( 'wp_emoji_reactions_nonce' ),
                'already_reacted' => esc_html__( 'You have already reacted to this post.', 'wp-emoji-reactions' ),
                'reaction_added'  => esc_html__( 'Your reaction has been added!', 'wp-emoji-reactions' ),
                'error'           => esc_html__( 'An error occurred. Please try again.', 'wp-emoji-reactions' ),
                'you_reacted_with' => esc_html__( 'You reacted with', 'wp-emoji-reactions' ),
                'reaction_names'  => $custom_names
            )
        );
    }
    
    /**
     * Display emoji reactions after post content
     *
     * @since    1.0.0
     * @param    string    $content    The post content.
     * @return   string    Modified post content with emoji reactions.
     */
    public function display_emoji_reactions($content) {
        // Only show on singular posts of enabled post types
        if (!is_singular() || !$this->should_show_reactions()) {
            return $content;
        }
        
        $post_id = get_the_ID();
        $reactions_html = $this->get_reactions_html($post_id);
        
        return $content . $reactions_html;
    }
    
    /**
     * Display floating emoji reactions
     *
     * @since    1.0.0
     */
    public function display_floating_emoji_reactions() {
        // Only show on singular posts of enabled post types
        if (!is_singular() || !$this->should_show_reactions()) {
            return;
        }
        
        $post_id = get_the_ID();
        $reactions_html = $this->get_reactions_html($post_id, true);
        
        echo wp_kses_post($reactions_html);
    }
    
    /**
     * Check if reactions should be shown for current post
     *
     * @since    1.0.0
     * @return   boolean    Whether reactions should be shown.
     */
    private function should_show_reactions() {
        $post_types = get_option('wp_emoji_reactions_post_types', array('post'));
        $current_post_type = get_post_type();
        
        return in_array($current_post_type, $post_types);
    }
    
    /**
     * Get HTML for emoji reactions
     *
     * @since    1.0.0
     * @param    int       $post_id    The post ID.
     * @param    boolean   $floating   Whether the reactions are floating.
     * @return   string    HTML for emoji reactions.
     */
    private function get_reactions_html($post_id, $floating = false) {
        $enabled_emojis = get_option('wp_emoji_reactions_enabled_emojis', array(
            'like' => '👍',
            'love' => '❤️',
            'laugh' => '😂',
            'wow' => '😮',
            'sad' => '😢',
            'angry' => '😠'
        ));
        
        if (empty($enabled_emojis)) {
            return '';
        }
        
        $custom_names = get_option('wp_emoji_reactions_custom_names', array());
        $reaction_counts = $this->get_reaction_counts($post_id);
        $user_reaction = $this->get_user_reaction($post_id);
        
        $container_class = $floating ? 'wp-emoji-reactions-container floating' : 'wp-emoji-reactions-container';
        
        // Use template instead of building HTML manually
        ob_start();
        include plugin_dir_path(__FILE__) . 'partials/wp-emoji-reactions-public-display.php';
        $html = ob_get_clean();
        
        // HTML is now generated from the template
        
        return $html;
    }
    
    /**
     * Get reaction counts for a post
     *
     * @since    1.0.0
     * @param    int       $post_id    The post ID.
     * @return   array     Reaction counts.
     */
    private function get_reaction_counts($post_id) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'emoji_reactions';
        
        $counts = array();
        
        // Check if table exists
        if ($wpdb->get_var($wpdb->prepare("SHOW TABLES LIKE %s", $table_name)) != $table_name) {
            return array();
        }
        
        $results = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT emoji_id, COUNT(*) as count FROM {$wpdb->prefix}emoji_reactions WHERE post_id = %d GROUP BY emoji_id",
                $post_id
            )
        );
        
        // Convert results to a simple array with emoji_id as key and count as value
        $counts = array();
        foreach ($results as $row) {
            $counts[$row->emoji_id] = (int) $row->count;
        }
        
        return $counts;
    }
    
    /**
     * Get user's reaction for a post
     *
     * @since    1.0.0
     * @param    int       $post_id    The post ID.
     * @return   string|boolean    User's reaction or false if none.
     */
    private function get_user_reaction($post_id) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'emoji_reactions';
        
        // Check if table exists
        if ($wpdb->get_var($wpdb->prepare("SHOW TABLES LIKE %s", $table_name)) != $table_name) {
            return false;
        }
        
        $ip_address = $this->get_user_ip();
        $user_id = get_current_user_id();
        
        $reaction = $wpdb->get_var(
            $wpdb->prepare(
                "SELECT emoji_id FROM {$wpdb->prefix}emoji_reactions WHERE post_id = %d AND (ip_address = %s OR (user_id > 0 AND user_id = %d)) LIMIT 1",
                $post_id,
                $ip_address,
                $user_id
            )
        );
        
        return $reaction ? $reaction : false;
    }
    
    /**
     * Handle emoji reaction AJAX request
     *
     * @since    1.0.0
     */
    public function handle_emoji_reaction() {
        // Verify nonce
        if (!isset($_POST['nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['nonce'])), 'wp_emoji_reactions_nonce')) {
            wp_send_json_error(array('message' => esc_html__('Security check failed.', 'wp-emoji-reactions')));
        }
        
        // Get post data
        $post_id = isset($_POST['post_id']) ? intval($_POST['post_id']) : 0;
        $reaction = isset($_POST['reaction']) ? sanitize_text_field(wp_unslash($_POST['reaction'])) : '';
        
        if (!$post_id || !$reaction) {
            wp_send_json_error(array('message' => esc_html__('Invalid data.', 'wp-emoji-reactions')));
        }
        
        // Verify post exists
        if (!get_post($post_id)) {
            wp_send_json_error(array('message' => esc_html__('Post not found.', 'wp-emoji-reactions')));
        }
        
        // Verify reaction is valid
        $enabled_emojis = get_option('wp_emoji_reactions_enabled_emojis', array());
        if (!isset($enabled_emojis[$reaction])) {
            wp_send_json_error(array('message' => esc_html__('Invalid reaction.', 'wp-emoji-reactions')));
        }
        
        // Get user info
        $ip_address = $this->get_user_ip();
        $user_id = get_current_user_id();
        
        global $wpdb;
        $table_name = $wpdb->prefix . 'emoji_reactions';
        
        // Check if user already reacted
        $existing_reaction = $this->get_user_reaction($post_id);
        
        if ($existing_reaction) {
            // Update existing reaction
            $result = $wpdb->update(
                $table_name,
                array(
                    'emoji_id' => $reaction,
                    'reaction_time' => current_time('mysql')
                ),
                array(
                    'post_id' => $post_id,
                    'ip_address' => $ip_address
                )
            );
        } else {
            // Insert new reaction
            $result = $wpdb->insert(
                $table_name,
                array(
                    'post_id' => $post_id,
                    'emoji_id' => $reaction,
                    'ip_address' => $ip_address,
                    'user_id' => $user_id,
                    'reaction_time' => current_time('mysql')
                )
            );
        }
        
        if ($result === false) {
            wp_send_json_error(array('message' => esc_html__('Database error.', 'wp-emoji-reactions')));
        }
        
        // Get updated counts
        $counts = $this->get_reaction_counts($post_id);
        
        wp_send_json_success(array(
            'message' => esc_html__('Reaction saved successfully.', 'wp-emoji-reactions'),
            'counts' => $counts,
            'user_reaction' => $reaction
        ));
    }
    
    /**
     * Get emoji reaction counts via AJAX
     *
     * @since    1.0.0
     */
    public function get_emoji_reactions() {
        // Verify nonce
        if (!isset($_POST['nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['nonce'])), 'wp_emoji_reactions_nonce')) {
            wp_send_json_error(array('message' => esc_html__('Security check failed.', 'wp-emoji-reactions')));
        }
        
        // Get post ID
        $post_id = isset($_POST['post_id']) ? intval($_POST['post_id']) : 0;
        
        if (!$post_id) {
            wp_send_json_error(array('message' => esc_html__('Invalid post ID.', 'wp-emoji-reactions')));
        }
        
        // Get reaction counts
        $counts = $this->get_reaction_counts($post_id);
        
        // Get user's reaction
        $user_reaction = $this->get_user_reaction($post_id);
        
        wp_send_json_success(array(
            'counts' => $counts,
            'user_reaction' => $user_reaction
        ));
    }
    
    /**
     * Get user's IP address
     *
     * Uses WordPress's built-in function to get the user's IP address safely.
     *
     * @since    1.0.0
     * @return   string    User's IP address.
     */
    private function get_user_ip() {
        // Use WordPress's built-in function to get the IP address safely
        return sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) );
    }
}
