<?php

// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

class Wpstorm_Notify_Loader
{

    public static $woocommerce;
    public static $elementorPro;
    public static $digits;
    public static $edd;
    public static $bookly;
    public static $gravityForms;
    public static $indeedMembershipPro;
    public static $paidMembershipsPro;
    public static $affiliateWp;
    public static $indeedAffiliatePro;
    public static $yithWoocommerceAffiliates;

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
     */
    public function __construct()
    {
        $integrations_options = json_decode(get_option('wpstorm_notify_integrations_options'), true);
        if ($integrations_options) {
            self::$woocommerce               = $integrations_options['woocommerce'] ?? '';
            self::$elementorPro              = $integrations_options['elementorPro'] ?? '';
            self::$digits                    = $integrations_options['digits'] ?? '';
            self::$edd                       = $integrations_options['edd'] ?? '';
            self::$bookly                    = $integrations_options['bookly'] ?? '';
            self::$gravityForms              = $integrations_options['gravityForms'] ?? '';
            self::$indeedMembershipPro       = $integrations_options['indeedMembershipPro '] ?? '';
            self::$paidMembershipsPro        = $integrations_options['paidMembershipsPro  '] ?? '';
            self::$affiliateWp               = $integrations_options['affiliateWp'] ?? '';
            self::$indeedAffiliatePro        = $integrations_options['indeedAffiliatePro'] ?? '';
            self::$yithWoocommerceAffiliates = $integrations_options['yithWoocommerceAffiliates'] ?? '';
        }
        $this->load_dependencies();
    }

    /**
     * Load the required dependencies for this plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function load_dependencies()
    {

        require_once WPSTORM_NOTIFY_CLASSES_PATH . 'wpstorm-notify-base.php';
        require_once WPSTORM_NOTIFY_GATEWAYS_PATH . 'sms/ippanel/wpstorm-notify-ippanel.php';

        // The class responsible for the settings page
        require_once WPSTORM_NOTIFY_CLASSES_PATH . 'wpstorm-notify-settings.php';

        // The class responsible for defining internationalization functionality of the plugin.
        require_once WPSTORM_NOTIFY_CLASSES_PATH . 'wpstorm-notify-i18n.php';

        // The class responsible for defining main options of the plugin.
        require_once WPSTORM_NOTIFY_CLASSES_PATH . 'wpstorm-notify-options.php';

        // The class responsible for defining REST Routs API of the plugin.
        require_once WPSTORM_NOTIFY_API_PATH . 'wpstorm-notify-routes.php';

        // The class responsible for defining all actions for elementor.
        require_once WPSTORM_NOTIFY_MODULES_PATH . 'elementor/wpstorm-notify-elementor.php';

        // The class responsible for defining all actions for woocommerce.
        if (self::$woocommerce) {
            require_once WPSTORM_NOTIFY_MODULES_PATH . 'woocommerce/wpstorm-notify-woocommerce.php';
            require_once WPSTORM_NOTIFY_MODULES_PATH . 'woocommerce/order-review/wpstorm-notify-order-review.php';
            require_once WPSTORM_NOTIFY_MODULES_PATH . 'woocommerce/order-actions/wpstorm-notify-order-actions.php';
        }

        // The class responsible for defining all actions for edd.
        if (self::$edd) {
            require_once WPSTORM_NOTIFY_MODULES_PATH . 'edd/wpstorm-notify-edd.php';
        }

        // The class responsible for defining all actions for aff.
        if (self::$indeedAffiliatePro || self::$affiliateWp || self::$yithWoocommerceAffiliates) {
            require_once WPSTORM_NOTIFY_MODULES_PATH . 'aff/wpstorm-notify-aff.php';
        }

        // The class responsible for defining all actions for newsletter.
        require_once WPSTORM_NOTIFY_MODULES_PATH . 'newsletter/wpstorm-notify-newsletter.php';

        // The class responsible for defining all actions for login-notify.
        require_once WPSTORM_NOTIFY_MODULES_PATH . 'core/wpstorm-notify-login-notify.php';

        // The class responsible for defining all actions for comments.
        require_once WPSTORM_NOTIFY_MODULES_PATH . 'core/wpstorm-notify-comments.php';

        // The class responsible for defining all actions for membership.
        if (self::$paidMembershipsPro || self::$indeedMembershipPro) {
            require_once WPSTORM_NOTIFY_MODULES_PATH . 'membership/wpstorm-notify-membership.php';
        }

        // The class responsible for defining all actions for gravity-forms.
        if (self::$gravityForms) {
            require_once WPSTORM_NOTIFY_MODULES_PATH . 'gravity-forms/wpstorm-notify-gravity-forms.php';
        }
        // The class responsible for defining all actions for digits.
        if (self::$digits) {
            require_once WPSTORM_NOTIFY_MODULES_PATH . 'digits/wpstorm-notify-digits.php';
        }
    }
}

Wpstorm_Notify_Loader::get_instance();
