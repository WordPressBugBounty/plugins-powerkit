<?php
/**
 * Plugin Name:       Powerkit
 * Description:       Powerkit – essential components for every WordPress site.
 * Version:           2.9.4
 * Author:            Code Supply Co.
 * Author URI:        https://codesupply.co
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       powerkit
 * Domain Path:       /languages
 *
 * @link              https://codesupply.co
 * @package           Powerkit
 */

defined( 'ABSPATH' ) || die( 'No script kiddies please!' );

/**
 * Variables
 */
define( 'POWERKIT_URL', plugin_dir_url( __FILE__ ) );
define( 'POWERKIT_PATH', plugin_dir_path( __FILE__ ) );

/**
 * Plugin Activation.
 *
 * @param bool $networkwide The networkwide.
 */
function powerkit_plugin_activation( $networkwide ) {
	do_action( 'powerkit_plugin_activation', $networkwide );
}
register_activation_hook( __FILE__, 'powerkit_plugin_activation' );

/**
 * Plugin Deactivation.
 *
 * @param bool $networkwide The networkwide.
 */
function powerkit_plugin_deactivation( $networkwide ) {
	do_action( 'powerkit_plugin_deactivation', $networkwide );
}
register_deactivation_hook( __FILE__, 'powerkit_plugin_deactivation' );

/**
 * Language
 */
load_plugin_textdomain( 'powerkit', false, plugin_basename( POWERKIT_PATH ) . '/languages' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require_once plugin_dir_path( __FILE__ ) . '/core/class-powerkit.php';
