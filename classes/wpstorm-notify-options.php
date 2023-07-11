<?php

// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

class Wpstorm_Notify_Options
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
     * @since 1.0.0
     * @return object Initialized object of class.
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
        add_action('init', [$this, 'register_settings_options']);
        add_action('init', [$this, 'register_login_notify_options']);
        add_action('init', [$this, 'register_phonebook_options']);
        add_action('init', [$this, 'register_comments_options']);
        add_action('init', [$this, 'register_newsletter_options']);
        add_action('init', [$this, 'register_woocommerce_options']);
        add_action('init', [$this, 'register_elementor_options']);
        add_action('init', [$this, 'register_edd_options']);
        add_action('init', [$this, 'register_aff_options']);
        add_action('init', [$this, 'register_membership_options']);
        add_action('init', [$this, 'register_integrations_options']);
    }

    /**
     * Register settings options.
     *
     * @return void
     * @since 1.0.0
     */
    public function register_settings_options()
    {
        $wpstorm_notify_settings_options = '';
        add_option('wpstorm_notify_settings_options', $wpstorm_notify_settings_options);
    }

    /**
     * Register login notify options
     *
     * @return void
     * @since 1.0.0
     */
    public function register_login_notify_options()
    {
        $wpstorm_notify_login_notify_options = '';
        add_option('wpstorm_notify_login_notify_options', $wpstorm_notify_login_notify_options);
    }

    /**
     * Register Phonebook options
     *
     * @return void
     * @since 1.0.0
     */
    public function register_phonebook_options()
    {
        $wpstorm_notify_phonebook_options = '';
        add_option('wpstorm_notify_phonebook_options', $wpstorm_notify_phonebook_options);
    }

    /**
     * Register comments options.
     *
     * @return void
     * @since 1.0.0
     */
    public function register_comments_options()
    {
        $wpstorm_notify_comments_options = '';
        add_option('wpstorm_notify_comments_options', $wpstorm_notify_comments_options);
    }

    /**
     * Register newsletter options.
     *
     * @return void
     * @since 1.0.0
     */
    public function register_newsletter_options()
    {
        $wpstorm_notify_newsletter_options = '';
        add_option('wpstorm_notify_newsletter_options', $wpstorm_notify_newsletter_options);
    }

    /**
     * Register WooCommerce options.
     *
     * @return void
     * @since 1.0.0
     */
    public function register_woocommerce_options()
    {
        $wpstorm_notify_woocommerce_options = '';
        add_option('wpstorm_notify_woocommerce_options', $wpstorm_notify_woocommerce_options);
    }

    /**
     * Register Elementor options.
     *
     * @return void
     * @since 1.0.0
     */
    public function register_elementor_options()
    {
        $wpstorm_notify_elementor_options = '';
        add_option('wpstorm_notify_elementor_options', $wpstorm_notify_elementor_options);
    }

    /**
     * Register EDD options.
     *
     * @return void
     * @since 1.0.0
     */
    public function register_edd_options()
    {
        $wpstorm_notify_edd_options = '';
        add_option('wpstorm_notify_edd_options', $wpstorm_notify_edd_options);
    }

    /**
     * Register Aff options.
     *
     * @return void
     * @since 1.0.0
     */
    public function register_aff_options()
    {
        $wpstorm_notify_aff_options = '';
        add_option('wpstorm_notify_aff_options', $wpstorm_notify_aff_options);
    }

    /**
     * Register Membership options.
     *
     * @return void
     * @since 1.0.0
     */
    public function register_membership_options()
    {
        $wpstorm_notify_membership_options = '';
        add_option('wpstorm_notify_membership_options', $wpstorm_notify_membership_options);
    }

    /**
     * Register Integrations options.
     *
     * @return void
     * @since 1.0.0
     */
    public function register_integrations_options()
    {
        $wpstorm_notify_integrations_options = '';
        add_option('wpstorm_notify_integrations_options', $wpstorm_notify_integrations_options);
    }
}
Wpstorm_Notify_Options::get_instance();
