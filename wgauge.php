<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              www.jacobanderson.co.uk
 * @since             1.0.0
 * @package           Wgauge
 *
 * @wordpress-plugin
 * Plugin Name:       wGauge
 * Plugin URI:        http://wgauge.com/
 * Description:       Gather user experience feedback and explore the results
 * Version:           1.0.0
 * Author:            wGauge
 * Author URI:        www.jacobanderson.co.uk
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       wgauge
 * Domain Path:       /languages
 */

// Create a helper function for easy freemius SDK access.
function wgauge_freemius() {
    global $wa_fs;

    if ( ! isset( $wa_fs ) ) {
        // Include Freemius SDK.
        require_once dirname(__FILE__) . '/freemius/start.php';

        $wa_fs = fs_dynamic_init( array(
            'id'                  => '1567',
            'slug'                => 'wGauge',
            'type'                => 'plugin',
            'public_key'          => 'pk_31a0d94bd0ec6846f8e5f1900c7e8',
            'is_premium'          => false,
            'has_addons'          => false,
            'has_paid_plans'      => false,
            'menu'                => array(
                'slug'           => 'wgauge-console',
                'account'        => false,
                'support'        => false,
            ),
        ) );
    }

    return $wa_fs;
}

// Init Freemius.
wgauge_freemius();
// Signal that SDK was initiated.
do_action( 'wa_fs_loaded' );

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

define( 'WGAUGE_VERSION', '1.0.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-wgauge-activator.php
 */
function activate_wgauge() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-wgauge-activator.php';
	Wgauge_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-wgauge-deactivator.php
 */
function deactivate_wgauge() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-wgauge-deactivator.php';
	Wgauge_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_wgauge' );
register_deactivation_hook( __FILE__, 'deactivate_wgauge' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-wgauge.php';



/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_wgauge() {

	$plugin = new Wgauge();
	$plugin->run();

}
run_wgauge();

