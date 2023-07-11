<?php

/**
 * wpstorm-notify elementor.
 *
 * @since 1.0.0
 */

// Exit if accessed directly.
use Elementor\Widgets_Manager;

if (!defined('ABSPATH')) {
    exit;
}

class Wpstorm_Notify_Elementor
{
    private static $elementorPro;
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
     * @return object initialized object of class.
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
            self::$elementorPro = $integrations_options['elementorPro'] ?? '';
        }
        if (self::$elementorPro) {
            add_action('elementor_pro/forms/actions/register', [$this, 'add_new_wpstorm_notify_newsletter_form_action']);
        }

        add_action('elementor/widgets/register', [$this, 'register_newsletter_widget']);
    }

    /**
     *  Add new action
     */
    public function add_new_wpstorm_notify_newsletter_form_action($form_actions_registrar)
    {

        include_once(__DIR__ . '/form-actions/wpstorm-notify-newsletter-action-after-submit.php');

        $form_actions_registrar->register(new Wpstorm_Notify_Newsletter_Action_After_Submit());
    }

    /**
     * Register List Widget.
     *
     * Include widget file and register widget class.
     *
     * @param Widgets_Manager $widgets_manager Elementor widgets manager.
     *
     * @return void
     * @since 1.0.0
     */
    public function register_newsletter_widget($widgets_manager)
    {

        require_once(__DIR__ . '/widgets/wpstorm-notify-newsletter-widget.php');

        $widgets_manager->register(new Wpstorm_Notify_Newsletter_Widget());
    }
}

Wpstorm_Notify_Elementor::get_instance();
