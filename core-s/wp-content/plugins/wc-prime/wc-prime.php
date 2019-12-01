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

use WcPrime\ChildSite;
use WcPrime\WP\MetaBox;

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
    use WcPrime\SingletonTrait;

    protected $loader;
    protected $services = [
        'metabox' => MetaBox::class,
    ];
    protected $registeredServices = [];

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
        // register cpt: site
        ChildSite::register();
        // register metabox
        add_action('add_meta_boxes', [MetaBox::class, 'register']);
        // register save metabox
        add_action('save_post', [MetaBox::class, 'registerSaving']);
    }

    /**
     * Load text domain
     */
    public function loadPluginTextdomain()
    {
        load_plugin_textdomain(
			'wc-prime',
			false,
			WC_PRIME_DIR . '/src/languages/'
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

    public function getService($key = '')
    {
        return isset($this->registeredServices[$key]) ? $this->registeredServices[$key] : $this;
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
        // register services
        foreach ($this->services as $key => $class) {
            $this->registeredServices[$key] = $class::getInstance();
        }
        // init
        $this->loader->addAction('init', $this, 'registerAdminHooks');
    }

}

// run the plugin
function wcprime($serviceName = '') {
    global $wcprime;
    if (!isset($wcprime)) {
        $wcprime = WcPrime::getInstance();
        $wcprime->run();
    }
    return $wcprime->getService($serviceName);
}

wcprime();
