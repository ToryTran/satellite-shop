<?php

/**
 *
 * @link              http://github.com
 * @since             1.0.0
 * @package           wc-prime
 *
 * @wordpress-plugin
 * Plugin Name:       WooCommerce Prime
 * Plugin URI:        https://github.com
 * Description:       Manage orders and products from one place
 * Version:           1.0.0
 * Author:            qnts
 * Author URI:        https://github.com/qnts
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       wc-prime
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
define('WC_PRIME_VERSION', '1.0.0');
define('WC_PRIME_FILE', __FILE__);
define('WC_PRIME_DIR', plugin_dir_path(__FILE__));
define('WC_PRIME_URL', plugin_dir_url( __FILE__ ));
// lazy load all dependencies
require_once(WC_PRIME_DIR . '/vendor/autoload.php');
require_once(WC_PRIME_DIR . '/src/helpers.php');

class WcPrime
{
    protected $loader;
    private static $instance;

    public static function getInstance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * The code that runs during plugin activation.
     */
    public function activate()
    {
        WcPrime\WP\Activator::activate();
    }

    /**
     * The code that runs during plugin deactivation.
     */
    public function deactivate()
    {
        WcPrime\WP\Deactivator::deactivate();
    }

    /**
     * Register admin hooks, styles, js
     */
    public function registerAdminHooks()
    {
        add_action('admin_enqueue_scripts', function () {
            wp_enqueue_style('wc-prime-css', wcp_asset('css/style.min.css'), false, WC_PRIME_VERSION);
            // register scripts
            wp_register_script('wc-prime-vendor', wcp_asset('js/vendor.min.js'), ['jquery'], WC_PRIME_VERSION, true);
            wp_register_script('wc-prime-script', wcp_asset('js/main.min.js'), ['jquery', 'wc-prime-vendor'], WC_PRIME_VERSION, true);
            // only need to enqueue this because vendor is a dependency
            wp_enqueue_script('wc-prime-script');
        });
    }

    /**
     * Load text domain
     */
    public function loadPluginTextdomain()
    {
        load_plugin_textdomain(
			'wc-prime',
			false,
			WC_PRIME_DIR . '/languages/'
		);
    }

    public function run()
    {
        $this->loader->run();
    }

    public function getLoader()
    {
        return $this->loader;
    }

    private function __construct()
    {
        // register loader
        $this->loader = new WcPrime\WP\Loader();
        // activation hooks
        register_activation_hook(WC_PRIME_FILE, [$this, 'activate']);
        register_deactivation_hook(WC_PRIME_FILE, [$this, 'deactivate']);
        // set locale
        $this->loader->addAction('plugins_loaded', $this, 'loadPluginTextdomain');
        $this->loader->addAction('init', $this, 'registerAdminHooks');
    }

}

// run the plugin
function wcsalite() {
    global $wcsalite;
    if (!isset($wcsalite)) {
        $wcsalite = WcPrime::getInstance();
        $wcsalite->run();
    }

    return $wcsalite;
}

wcsalite();
