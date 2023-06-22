<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              developer.com
 * @since             1.0.0
 * @package           DC_Spruced
 *
 * @wordpress-plugin
 * Plugin Name:       DC - Spruced
 * Plugin URI:        https://dilipchauhan013.co.in/
 * Description:       This is the custom plugin for DC - Spruced
 * Version:           1.0.0
 * Author:            DC Digital Agency
 * Author URI:        https://dilipchauhan013.co.in/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       DC-spruced
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
define( 'DC_SPRUCED_VERSION', '1.0.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-DC-spruced-activator.php
 */
function activate_DC_spruced() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-DC-spruced-activator.php';
	DC_Spruced_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-DC-spruced-deactivator.php
 */
function deactivate_DC_spruced() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-DC-spruced-deactivator.php';
	DC_Spruced_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_DC_spruced' );
register_deactivation_hook( __FILE__, 'deactivate_DC_spruced' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-DC-spruced.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_DC_spruced() {

	$plugin = new DC_Spruced();
	$plugin->run();

}
run_DC_spruced();
