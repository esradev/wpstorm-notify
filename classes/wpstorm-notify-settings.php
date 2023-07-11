<?php

/**
 * wpstorm-notify settings.
 *
 * @since 1.0.0
 */

// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

class Wpstorm_Notify_Settings
{
    public static $actual_link;
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
        add_action('wp_enqueue_scripts', [$this, 'enqueue_styles']);
        add_action('wp_enqueue_scripts', [$this, 'enqueue_scripts']);
        add_action('admin_enqueue_scripts', [$this, 'admin_enqueue_scripts']);
        add_action('admin_enqueue_scripts', [$this, 'admin_enqueue_styles']);
        add_action('admin_menu', [$this, 'init_menu']);
        if (Wpstorm_Notify_Base::$apiKey) {
            add_action('admin_bar_menu', [$this, 'admin_bar_menu'], 60);
        }
        add_filter('plugin_action_links_' . WPSTORM_NOTIFY_BASE, [$this, 'settings_link']);
        add_action('wp_dashboard_setup', [$this, 'rss_meta_box']);
        add_action('init', [$this, 'check_remaining_days']);
        self::$actual_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
    }


    /**
     * Enqueue styles
     *
     * @return void
     */
    public function enqueue_styles()
    {
        wp_register_style('wpstorm-notify-newsletter', WPSTORM_NOTIFY_URL . 'assets/css/wpstorm-notify-newsletter.css', [], WPSTORM_NOTIFY_VERSION, 'all');
    }

    /**
     * Enqueue scripts
     *
     * @return void
     */
    public function enqueue_scripts()
    {
        wp_register_script('wpstorm-notify-newsletter', WPSTORM_NOTIFY_URL . 'assets/js/wpstorm-notify-newsletter.js', ['jquery'], WPSTORM_NOTIFY_VERSION, true);
        wp_localize_script(
            'wpstorm-notify-newsletter',
            'ajax_object',
            ['ajax_url' => admin_url('admin-ajax.php')]
        );
    }

    /**
     * Register the stylesheets for the admin area.
     *
     * @since 1.0.0
     */
    public function admin_enqueue_styles()
    {
        if (self::$actual_link === WPSTORM_NOTIFY_SETTINGS_LINK) {
            wp_enqueue_style('wpstorm-notify-style', WPSTORM_NOTIFY_URL . 'build/index.css', [], WPSTORM_NOTIFY_VERSION);
        }
    }

    /**
     * Register the JavaScript for the admin area.
     *
     * @since 1.0.0
     */
    public function admin_enqueue_scripts($hook)
    {
//        wp_enqueue_script('react-multi-date-picker', WPSTORM_NOTIFY_URL . 'assets/js/react-multi-date-picker.js', [
//            'wp-element',
//            'wp-i18n',
//        ], WPSTORM_NOTIFY_VERSION, true);
        wp_enqueue_script(
            'wpstorm-notify-script',
            WPSTORM_NOTIFY_URL . 'build/index.js',
            [
                'wp-element',
                'wp-i18n',
            ],
            WPSTORM_NOTIFY_VERSION,
            true
        );

        /*
         * Add a localization object ,The base rest api url and a security nonce
         * @see https://since1979.dev/snippet-014-setup-axios-for-the-wordpress-rest-api/
         * */
        wp_localize_script(
            'wpstorm-notify-script',
            'wpstormSmsJsObject',
            [
                'rootapiurl'        => esc_url_raw(rest_url()),
                'nonce'             => wp_create_nonce('wp_rest'),
                'wproules'          => wp_roles(),
                'username'          => Wpstorm_Notify_Base::$username,
                'password'          => Wpstorm_Notify_Base::$password,
                'getPhonebooks'     => Wpstorm_Notify_Ippanel::get_phonebooks(),
                'getCredit'         => Wpstorm_Notify_Ippanel::get_credit(),
                'getActivePlugins'  => get_option('active_plugins'),
                'isDigitsInstalled' => function_exists('digit_ready'),
                'settingsUrl'       => WPSTORM_NOTIFY_SETTINGS_LINK,
            ]
        );

        // Load wpstorm-notify languages for JavaScript files.
        wp_set_script_translations('wpstorm-notify-script', 'wpstorm-notify', WPSTORM_NOTIFY_PATH . '/languages');

        wp_enqueue_script('jquery-validate', WPSTORM_NOTIFY_URL . 'assets/js/jquery.validate.min.js', ['jquery'], WPSTORM_NOTIFY_VERSION, true);
        wp_enqueue_script('select2', WPSTORM_NOTIFY_URL . 'assets/js/select2.min.js', ['jquery-validate'], WPSTORM_NOTIFY_VERSION, true);

        wp_enqueue_style('wpstorm-notify-tracking-code', WPSTORM_NOTIFY_URL . 'assets/css/wpstorm-notify-tracking-code.css', [], WPSTORM_NOTIFY_VERSION, 'all');
        wp_enqueue_script('wpstorm-notify-tracking-code', WPSTORM_NOTIFY_URL . 'assets/js/wpstorm-notify-tracking-code.js', ['jquery-validate'], WPSTORM_NOTIFY_VERSION, true);
    }

    /**
     * Add Admin Menu.
     *
     * @return void
     */
    public function init_menu()
    {
        add_menu_page(
            __('wpstorm-notify', 'wpstorm-notify'),
            __('wpstorm-notify', 'wpstorm-notify'),
            'manage_options',
            WPSTORM_NOTIFY_SLUG,
            [
                $this,
                'admin_page',
            ],
            'dashicons-testimonial',
            100
        );
        add_submenu_page(
            WPSTORM_NOTIFY_SLUG,
            __('wpstorm-notify', 'wpstorm-notify'),
            __('Settings', 'wpstorm-notify'),
            'manage_options',
            WPSTORM_NOTIFY_SLUG,
            [
                $this,
                'admin_page',
            ]
        );
    }

    /**
     * Init Admin Page.
     */
    public function admin_page()
    {
        include_once WPSTORM_NOTIFY_MODULES_PATH . 'core/wpstorm-notify-admin-page.php';
    }

    /**
     * Add bar menu. Show some links for wpstorm-notify plugin on the admin bar.
     *
     * @since 1.0.0
     */
    public function admin_bar_menu()
    {
        global $wp_admin_bar;
        if (!is_super_admin() || !is_admin_bar_showing()) {
            return;
        }

        $wp_admin_bar->add_menu(
            [
                'id'     => 'wpstorm-notify',
                'parent' => null,
                'group'  => null,
                'title'  => __('wpstorm-notify', 'wpstorm-notify'),
                'meta'   => [
                    'title' => __('wpstorm-notify', 'textdomain'),
                    // This title will show on hover
                ],
            ]
        );
        $credit = Wpstorm_Notify_Ippanel::get_credit();
        if (is_numeric($credit)) {
            $wp_admin_bar->add_menu(
                [
                    'parent' => 'wpstorm-notify',
                    'id'     => 'wpstorm-notify-admin-bar-credit',
                    'title'  => __('Account credit: ', 'wpstorm-notify') . number_format($credit) . __(' $IR_T', 'wpstorm-notify'),
                    'href'   => get_bloginfo('url') . '/wp-admin/admin.php?page=wpstorm-notify_settings',
                ]
            );
        }

        $wp_admin_bar->add_menu(
            [
                'parent' => 'wpstorm-notify',
                'title'  => __('Send Sms', 'wpstorm-notify'),
                'id'     => 'wpstorm-notify-admin-bar-send-sms',
                'href'   => get_bloginfo('url') . '/wp-admin/admin.php?page=wpstorm-notify_settings#/send_sms',
            ]
        );
        $wp_admin_bar->add_menu(
            [
                'parent' => 'wpstorm-notify',
                'title'  => __('wpstorm-notify', 'wpstorm-notify'),
                'id'     => 'wpstorm-notify-admin-bar-go-to-site',
                'href'   => 'https://wpstorm-notify.ir/',
            ]
        );
    }

    /**
     * Plugin settings link on all plugins page.
     *
     * @since 1.0.0
     */
    public function settings_link($links)
    {
        // Add settings link
        $settings_link = '<a href="' . WPSTORM_NOTIFY_SETTINGS_LINK . '">' . esc_html__('Settings', 'wpstorm-notify') . '</a>';

        // Add document link
        $doc_link = '<a href="' . WPSTORM_NOTIFY_WEB_MAIN_DOC . '" target="_blank" rel="noopener noreferrer">' . esc_html__('Docs', 'wpstorm-notify') . '</a>';
        array_push($links, $settings_link, $doc_link);

        return $links;
    }

    /**
     * Show the latest posts from https://wpstorm-notify.ir/ on dashboard widget
     *
     * @since 1.0.0
     */
    public function rss_meta_box()
    {
        if (get_option('rss_meta_box', '1') == '1') {
            add_meta_box(
                'wpstorm-notify_Rss',
                __('wpstorm-notify latest news', 'wpstorm-notify'),
                [
                    $this,
                    'rss_postbox_container',
                ],
                'dashboard',
                'side',
                'low'
            );
        }
    }

    public function rss_postbox_container()
    {
        ?>
        <div class="rss-widget">
            <?php
            wp_widget_rss_output(
                'https://wpstorm-notify.ir/feed/',
                [
                    'items'        => 3,
                    'show_summary' => 1,
                    'show_author'  => 1,
                    'show_date'    => 1,
                ]
            );
            ?>
        </div>
        <?php

    }

    /**
     * Check remaining days.
     */
    public function check_remaining_days()
    {
        $panel_expire_date = get_option('panel_expire_date', '');
        if (empty($panel_expire_date)) {
            return;
        }
        $future     = strtotime($panel_expire_date);
        $timefromdb = time();
        $timeleft   = $future - $timefromdb;
        $daysleft   = round((($timeleft / 24) / 60) / 60);
        if (!is_numeric($daysleft)) {
            return;
        }
        if ($daysleft > 30) {
            delete_option('sent_low_remaining_days_30');
            delete_option('sent_low_remaining_days_7');

            return;
        }
        if ($daysleft > 20 && $daysleft < 30) {
            $already_sent = get_option('sent_low_remaining_days_30', '');
            if ($already_sent === '1') {
                return;
            }
            $message = __('Dear user, your panel will expire less than a month from now. To renew your SMS panel, contact wpstorm-notify', 'wpstorm-notify');
            Wpstorm_Notify_Ippanel::send_message([Wpstorm_Notify_Base::$admin_number], $message, '+98club');
            update_option('sent_low_remaining_days_30', '1');
        } elseif ($daysleft > 1 && $daysleft < 7) {
            $already_sent = get_option('sent_low_remaining_days_7', '');
            if ($already_sent == '1') {
                return;
            }

            $message = __('Dear user, your panel will expire less than a week from now. To renew your SMS panel, contact wpstorm-notify.', 'wpstorm-notify');
            Wpstorm_Notify_Ippanel::send_message([Wpstorm_Notify_Base::$admin_number], $message, '+98club');
            update_option('sent_low_remaining_days_7', '1');
        }
    }
}

Wpstorm_Notify_Settings::get_instance();
