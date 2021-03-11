<?php
/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://wptravelengine.com/
 * @since             1.0.0
 * @package           WP_Travel_Engine
 *
 * @wordpress-plugin
 * Plugin Name:       WordPress Travel Booking Plugin - WP Travel Engine
 * Plugin URI:        https://wordpress.org/plugins/wp-travel-engine/
 * Description:       WP Travel Engine is a free travel booking WordPress plugin to create travel and tour packages for tour operators and travel agencies. It is a complete travel management system and includes plenty of useful features. You can create your travel booking website using WP Travel Engine in less than 5 minutes.
 * Version:           4.3.5
 * Author:            WP Travel Engine
 * Author URI:        https://wptravelengine.com/
 * License:           GPLv3
 * License URI:       http://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain:       wp-travel-engine
 * Domain Path:       /languages
 */

 // Freemius
if ( ! function_exists( 'wte_fs' ) ) {
    // Create a helper function for easy SDK access.
    function wte_fs() {
        global $wte_fs;

        if ( ! isset( $wte_fs ) ) {
            // Include Freemius SDK.
            require_once dirname(__FILE__) . '/includes/lib/freemius/start.php';

            $wp_travel_engine_first_time_activation_flag = get_option('wp_travel_engine_first_time_activation_flag',false);

            if( $wp_travel_engine_first_time_activation_flag == false ){
                $slug = "wp-travel-engine-onboard";
            }else{
                $slug = "class-wp-travel-engine-admin.php";
            }
            $arg_array =  array(
                'id'                 => '5392',
                'slug'               => 'wp-travel-engine',
                'type'               => 'plugin',
                'public_key'         => 'pk_d9913f744dc4867caeec5b60fc76d',
                'is_premium'         => false,
                'has_addons'         => false,
                'has_paid_plans'     => false,
                'menu'               => array(
                    'slug'           => $slug, // Default: class-wp-travel-engine-admin.php
                    'account'        => false,
                    'contact'        => false,
                    'support'        => false,
                    'parent'         => array(
                        'slug'    => 'edit.php?post_type=booking',
                    ),
                ),
            );
            $wte_fs = fs_dynamic_init($arg_array);
        }
        return $wte_fs;
    }

    // Init Freemius.
    wte_fs();
    // Signal that SDK was initiated.
    do_action( 'wte_fs_loaded' );
}

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die;
}

// Not like register_uninstall_hook(), you do NOT have to use a static function.
wte_fs()->add_action('after_uninstall', 'wte_fs_uninstall_cleanup');
function wte_fs_uninstall_cleanup(){}

$wp_travel_engine_settings = get_option( 'wp_travel_engine_settings',true );
$payment_debug_mode        = isset( $wp_travel_engine_settings['payment_debug'] ) && $wp_travel_engine_settings['payment_debug'] === 'yes' ? true : false;

define( 'WP_TRAVEL_ENGINE_PAYMENT_DEBUG', $payment_debug_mode );
define( 'WP_TRAVEL_ENGINE_FILE_PATH', __FILE__ );
define( 'WP_TRAVEL_ENGINE_BASE_PATH', dirname( __FILE__ ) );
define( 'WP_TRAVEL_ENGINE_ABSPATH', dirname( __FILE__ ) . '/' );
define( 'WP_TRAVEL_ENGINE_IMG_PATH', WP_TRAVEL_ENGINE_BASE_PATH.'/admin/css/icons' );
define( 'WP_TRAVEL_ENGINE_TEMPLATE_PATH', WP_TRAVEL_ENGINE_BASE_PATH.'/includes/templates' );
define( 'WP_TRAVEL_ENGINE_FILE_URL', plugins_url( '', __FILE__ ) );
define( 'WP_TRAVEL_ENGINE_VERSION', '4.3.5' );
define( 'WP_TRAVEL_ENGINE_POST_TYPE', 'trip' );
define( 'WP_TRAVEL_ENGINE_URL', rtrim( plugin_dir_url( __FILE__ ), '/' ) );
define( 'WP_TRAVEL_ENGINE_IMG_URL', rtrim( plugin_dir_url( __FILE__ ), '/' ) );
// this is the URL our updater / license checker pings. This should be the URL of the site with EDD installed
define( 'WP_TRAVEL_ENGINE_STORE_URL', 'https://wptravelengine.com/' ); // IMPORTANT: change the name of this constant to something unique to prevent conflicts with other plugins using this system
define( 'WP_TRAVEL_ENGINE_PLUGIN_LICENSE_PAGE', 'wp_travel_engine_license_page' );

/**
 * Load plugin updater file
 */
require plugin_dir_path( __FILE__ ) . 'plugin-updater.php';

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-wp-travel-engine-activator.php
 */
function activate_wp_travel_engine() {
    require_once plugin_dir_path( __FILE__ ) . 'includes/class-wp-travel-engine-activator.php';
    Wp_Travel_Engine_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-wp-travel-engine-deactivator.php
 */
function deactivate_wp_travel_engine() {
    require_once plugin_dir_path( __FILE__ ) . 'includes/class-wp-travel-engine-deactivator.php';
    Wp_Travel_Engine_Deactivator::deactivate();
}

// register_activation_hook( __FILE__, 'wte_activate' );
register_activation_hook( __FILE__, 'activate_wp_travel_engine' );
register_deactivation_hook( __FILE__, 'deactivate_wp_travel_engine' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-wp-travel-engine.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_Wp_Travel_Engine() {

    $plugin = new Wp_Travel_Engine();
    $plugin->run();

}
run_Wp_Travel_Engine();
