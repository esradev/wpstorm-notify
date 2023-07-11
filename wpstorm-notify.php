<?php

/*
* Plugin Name:       Wpstorm Notify
* Plugin URI:        https://wpstorm.ir/wpstorm-notify/
* Description:       Just send sms and email.
* Version:           1.0.0
* Requires at least: 5.8
* Requires PHP:      7.4
* Author:            Wpstorm Genius
* Author URI:        https://wpstorm.ir/about/
* License:           GPL v2 or later
* License URI:       https://www.gnu.org/licenses/gpl-2.0.html
* Update URI:        https://example.com/my-plugin/
* Text Domain:       wpstorm-notify
* Domain Path:       /languages
*/

// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

class Wpstorm_Notify
{

    /**
     * Instance
     *
     * @access private
     * @var object Class object.
     * @since 1.0.0
     */
    private static $instance;

    /**
     * Initiator
     *
     * @return object Initialized object of class.
     * @since 1.0.0
     */
    public static function get_instance()
    {
        if (!isset(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * Constructor
     *
     * @since 1.0.0
     */
    public function __construct()
    {
        $this->define_constants();
        $this->wpstorm_notify_loader();

        register_activation_hook(__FILE__, [$this, 'activate_wpstorm_notify']);
        add_action('activated_plugin', [$this, 'wpstorm_notify_activation_redirect']);
    }

    /**
     * Defines all constants
     *
     * @since 1.0.0
     */
    public function define_constants()
    {

        /**
         * Defines all constants
         *
         * @since 1.0.0
         */
        define('WPSTORM_NOTIFY_VERSION', '1.0.0');
        define('WPSTORM_NOTIFY_FILE', __FILE__);
        define('WPSTORM_NOTIFY_PATH', plugin_dir_path(WPSTORM_NOTIFY_FILE));
        define('WPSTORM_NOTIFY_BASE', plugin_basename(WPSTORM_NOTIFY_FILE));
        define('WPSTORM_NOTIFY_SLUG', 'wpstorm_notify_settings');
        define('WPSTORM_NOTIFY_SETTINGS_LINK', admin_url('admin.php?page=' . WPSTORM_NOTIFY_SLUG));
        define('WPSTORM_NOTIFY_CLASSES_PATH', WPSTORM_NOTIFY_PATH . 'classes/');
        define('WPSTORM_NOTIFY_MODULES_PATH', WPSTORM_NOTIFY_PATH . 'modules/');
        define('WPSTORM_NOTIFY_API_PATH', WPSTORM_NOTIFY_PATH . 'api/');
        define('WPSTORM_NOTIFY_GATEWAYS_PATH', WPSTORM_NOTIFY_PATH . 'gateways/');
        define('WPSTORM_NOTIFY_URL', plugins_url('/', WPSTORM_NOTIFY_FILE));
        define('WPSTORM_NOTIFY_WEB_MAIN', 'https://wpstorm.ir/');
        define('WPSTORM_NOTIFY_WEB_MAIN_DOC', WPSTORM_NOTIFY_WEB_MAIN . 'wpstorm-notify/');
    }

    /**
     * Require loader Wpstorm_Notify class.
     *
     * @return void
     * @since 1.0.0
     */
    public function wpstorm_notify_loader()
    {
        require WPSTORM_NOTIFY_CLASSES_PATH . 'wpstorm-notify-loader.php';
    }

    /**
     * Require Wpstorm_Notify activator class.
     *
     * @return void
     * @since 1.0.0
     */
    public function activate_wpstorm_notify()
    {
        require_once WPSTORM_NOTIFY_CLASSES_PATH . 'wpstorm-notify-activator.php';
        Wpstorm_Notify_Activator::activate();
        Wpstorm_Notify_Activator::modify_option();
    }


    /**
     * Redirect user to plugin settings page after plugin was activated.
     *
     * @return void
     * @since 1.0.0
     */
    public function wpstorm_notify_activation_redirect()
    {
        if (get_option('wpstorm_notify_do_activation_redirect', false)) {
            delete_option('wpstorm_notify_do_activation_redirect');
            exit(wp_redirect(WPSTORM_NOTIFY_SETTINGS_LINK));
        }
    }
}

Wpstorm_Notify::get_instance();