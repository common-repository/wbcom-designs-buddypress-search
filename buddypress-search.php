<?php
/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://wbcomdesigns.com/
 * @since             1.0.0
 * @package           Buddypress_Search
 *
 * @wordpress-plugin
 * Plugin Name:       Wbcom Designs - BuddyPress Search
 * Plugin URI:        https://wbcomdesigns.com/downloads/buddypress-search/
 * Description:       BuddyPress search allows you to search your community elements along with WordPress Post, Pages and Custom Post Type.
 * Version:           1.4.0
 * Author:            wbcomdesigns
 * Author URI:        https://wbcomdesigns.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       buddypress-search
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'BUDDYPRESS_SEARCH_VERSION', '1.4.0' );

define( 'BP_SEARCH_DIR', dirname( __FILE__ ) );
define( 'BP_SEARCH_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
define( 'BP_SEARCH_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'BP_SEARCH_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );
if ( ! defined( 'BP_SEARCH_PLUGIN_FILE' ) ) {
	define( 'BP_SEARCH_PLUGIN_FILE', __FILE__ );
}

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-buddypress-search-activator.php
 */
function activate_buddypress_search() {
	if ( class_exists( 'BuddyPress' ) ) {
		require_once plugin_dir_path( __FILE__ ) . 'includes/class-buddypress-search-activator.php';
		Buddypress_Search_Activator::activate();
	}
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-buddypress-search-deactivator.php
 */
function deactivate_buddypress_search() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-buddypress-search-deactivator.php';
	Buddypress_Search_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_buddypress_search' );
register_deactivation_hook( __FILE__, 'deactivate_buddypress_search' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-buddypress-search.php';


/**
 * The core plugin functions that is used to define internationalization,
 * add plugin related global functions
 */
require plugin_dir_path( __FILE__ ) . 'includes/buddypress-search-functions.php';


/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_buddypress_search() {
	$plugin = new Buddypress_Search();
	$plugin->run();

}


add_action( 'bp_include', 'buddypress_search_plugin_init' );
/**
 * Check plugin requirement on plugins loaded
 * this plugin requires BuddyPress to be installed and active
 */
function buddypress_search_plugin_init() {

	global $wpdb;

	if ( is_multisite() && BP_ROOT_BLOG != $wpdb->blogid ) {
		return;
	}

	run_buddypress_search();
	add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'buddypress_search_plugin_links' );
}

/**
 * Function to add plugin links.
 *
 * @param array $links Plugin action links array.
 */
function buddypress_search_plugin_links( $links ) {
	$bmpro_links = array(
		'<a href="' . admin_url( 'admin.php?page=buddypress-search' ) . '">' . __( 'Settings', 'buddypress-search' ) . '</a>',
		'<a href="https://wbcomdesigns.com/contact/" target="_blank">' . __( 'Support', 'buddypress-search' ) . '</a>',
	);
	return array_merge( $links, $bmpro_links );
}

add_action( 'activated_plugin', 'buddypress_search_activation_redirect_settings' );

/**
 * Redirect to plugin settings page after activated.
 *
 * @since  1.0.0
 *
 * @param string $plugin Path to the plugin file relative to the plugins directory.
 */
function buddypress_search_activation_redirect_settings( $plugin ) {

	if ( plugin_basename( __FILE__ ) === $plugin && class_exists( 'BuddyPress' ) ) {
		if ( isset( $_REQUEST['action'] ) && $_REQUEST['action'] == 'activate' && isset( $_REQUEST['plugin'] ) && $_REQUEST['plugin'] == $plugin ) {
			wp_safe_redirect( admin_url( 'admin.php?page=buddypress-search' ) );
			exit;
		}
	}
}


/**
 *  Check if buddypress activate.
 */
function buddypress_search_requires_buddypress() {
	if ( ! class_exists( 'BuddyPress' ) ) {
		deactivate_plugins( plugin_basename( __FILE__ ) );
		add_action( 'admin_notices', 'buddypress_search_required_plugin_admin_notice' );
	}
}

add_action( 'admin_init', 'buddypress_search_requires_buddypress' );
/**
 * Throw an Alert to tell the Admin why it didn't activate.
 *
 * @author wbcomdesigns
 * @since  2.3.0
 */
function buddypress_search_required_plugin_admin_notice() {
	$bpquotes_plugin = esc_html__( ' BuddyPress Search', 'buddypress-search' );
	$bp_plugin       = esc_html__( 'BuddyPress', 'buddypress-search' );
	echo '<div class="error"><p>';
	/* translators: %1$s: BuddyPress Search ;  %2$s: BuddyPress*/
	echo sprintf( esc_html__( '%1$s is ineffective now as it requires %2$s to be installed and active.', 'buddypress-search' ), '<strong>' . esc_html( $bpquotes_plugin ) . '</strong>', '<strong>' . esc_html( $bp_plugin ) . '</strong>' );
	echo '</p></div>';
	if ( null !== filter_input( INPUT_GET, 'activate' ) ) {
		$activate = filter_input( INPUT_GET, 'activate' );
		unset( $activate );
	}
}
