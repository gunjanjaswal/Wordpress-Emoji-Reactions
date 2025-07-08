<?php
/**
 * Plugin Name: WP Emoji Reactions
 * Plugin URI: https://github.com/gunjanjaswal/Wordpress-Emoji-Reactions
 * Description: Add colorful emoji reactions to your WordPress posts and pages, similar to Facebook reactions.
 * Version: 1.0.0
 * Author: Gunjan Jaswaal
 * Author URI: https://gunjanjaswal.me
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: wp-emoji-reactions
 * Domain Path: /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Current plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 */
define( 'WP_EMOJI_REACTIONS_VERSION', '1.0.0' );

/**
 * Define plugin paths and URLs.
 */
define( 'WP_EMOJI_REACTIONS_PATH', plugin_dir_path( __FILE__ ) );
define( 'WP_EMOJI_REACTIONS_URL', plugin_dir_url( __FILE__ ) );
define( 'WP_EMOJI_REACTIONS_BASENAME', plugin_basename( __FILE__ ) );

/**
 * The code that runs during plugin activation.
 *
 * @since 1.0.0
 */
function activate_wp_emoji_reactions() {
	require_once WP_EMOJI_REACTIONS_PATH . 'includes/class-wp-emoji-reactions-activator.php';
	WP_Emoji_Reactions_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 *
 * @since 1.0.0
 */
function deactivate_wp_emoji_reactions() {
	require_once WP_EMOJI_REACTIONS_PATH . 'includes/class-wp-emoji-reactions-deactivator.php';
	WP_Emoji_Reactions_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_wp_emoji_reactions' );
register_deactivation_hook( __FILE__, 'deactivate_wp_emoji_reactions' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require WP_EMOJI_REACTIONS_PATH . 'includes/class-wp-emoji-reactions.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since 1.0.0
 * @return void
 */
function run_wp_emoji_reactions() {
	$plugin = new WP_Emoji_Reactions();
	$plugin->run();
}

// Start the plugin
run_wp_emoji_reactions();
