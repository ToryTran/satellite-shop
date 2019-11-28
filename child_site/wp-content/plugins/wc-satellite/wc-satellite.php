<?php

/**
 *
 * @link              http://github.com
 * @since             1.0.0
 * @package           wc-satellite
 *
 * @wordpress-plugin
 * Plugin Name:       WooCommerce Satellite
 * Plugin URI:        https://github.com
 * Description:       Sync orders and products from core site to this site and vice-versa
 * Version:           1.0.0
 * Author:            qnts
 * Author URI:        https://github.com/qnts
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       wc-satellite
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
define('WC_SATELLITE_VERSION', '1.0.0');
define('WC_SATELLITE_FILE', __FILE__);
define('WC_SATELLITE_DIR', plugin_dir_path(__FILE__));
// lazy load all dependencies
require_once(WC_SATELLITE_DIR . '/vendor/autoload.php');

class WcSatellite
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
        WcSatellite\WP\Activator::activate();
    }

    /**
     * The code that runs during plugin deactivation.
     */
    public function deactivate()
    {
        WcSatellite\WP\Deactivator::deactivate();
    }

    /**
     * Register admin hooks, styles, js
     */
    public function registerAdminHooks()
    {

    }

    /**
     * Load text domain
     */
    public function loadPluginTextdomain()
    {
        load_plugin_textdomain(
			'wc-satellite',
			false,
			WC_SATELLITE_DIR . '/languages/'
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
        $this->loader = new WcSatellite\WP\Loader();
        // activation hooks
        register_activation_hook(WC_SATELLITE_FILE, [$this, 'activate']);
        register_deactivation_hook(WC_SATELLITE_FILE, [$this, 'deactivate']);
        // set locale
        $this->loader->addAction('plugins_loaded', $this, 'loadPluginTextdomain');
        $this->loader->addAction('init', $this, 'registerAdminHooks');
    }

}

// run the plugin
function wcsalite() {
    global $wcsalite;
    if (!isset($wcsalite)) {
        $wcsalite = WcSatellite::getInstance();
        $wcsalite->run();
    }

    return $wcsalite;
}

wcsalite();
