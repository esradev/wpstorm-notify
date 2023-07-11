<?php

/**
 * wpstorm-notify woocommerce.
 *
 * @since 1.0.0
 */

// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

class Wpstorm_Notify_Woocommerce
{
    /**
     * Instance
     *
     * @access private
     * @var object Class object.
     * @since 1.0.0
     */
    private static $instance;

    private static $woo_checkout_otp_pattern;
    private static $woo_tracking_pattern;
    private static $woo_checkout_otp;
    private static $woo_retention_order_no;
    private static $woo_retention_order_month;
    private static $woo_retention_msg;

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
        $woocommerce_options = json_decode(get_option('wpstorm-notify_woocommerce_options'), true);
        if ($woocommerce_options) {
            self::$woo_checkout_otp          = $woocommerce_options['woo_checkout_otp'];
            self::$woo_checkout_otp_pattern  = $woocommerce_options['woo_checkout_otp_pattern'];
            self::$woo_tracking_pattern      = $woocommerce_options['woo_tracking_pattern'];
            self::$woo_retention_order_no    = $woocommerce_options['woo_retention_order_no'];
            self::$woo_retention_order_month = $woocommerce_options['woo_retention_order_month'];
            self::$woo_retention_msg         = $woocommerce_options['woo_retention_msg'];
        }

        add_action('wp_enqueue_scripts', [$this, 'enqueue_styles']);
        add_action('wp_enqueue_scripts', [$this, 'enqueue_scripts']);
        add_action('add_meta_boxes', [$this, 'add_tracking_code_meta_box']);
        add_action('add_meta_boxes', [$this, 'tracking_code_order_postbox']);
        add_action('wp_ajax_send_tracking_code_sms', [$this, 'send_tracking_code_sms']);
        add_action('wp_ajax_nopriv_send_tracking_code_sms', [$this, 'send_tracking_code_sms']);
        add_action('woocommerce_thankyou', [$this, 'woo_payment_finished']);
        add_action('init', [$this, 'woo_retention_action']);
        add_action('woocommerce_checkout_get_value', [$this, 'pre_populate_checkout_fields'], 10, 2);
        add_filter('woocommerce_billing_fields', [$this, 'woocommerce_checkout_fields']);
        add_action('woocommerce_checkout_process', [$this, 'woocommerce_checkout_process']);
        add_action('wp_ajax_send_otp_code', [$this, 'send_otp_code']);
        add_action('wp_ajax_nopriv_send_otp_code', [$this, 'send_otp_code']);
    }

    /**
     * Register the stylesheets for the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function enqueue_styles()
    {
        $woo_checkout_otp = self::$woo_checkout_otp;
        if ($woo_checkout_otp && is_checkout()) {
            wp_enqueue_style('wpstorm-notify-woo-otp', WPSTORM_NOTIFY_URL . 'assets/css/wpstorm-notify-woo-otp.css', [], wpstorm-notify_VERSION, 'all');
        }
    }

    /**
     * Register the JavaScript for the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function enqueue_scripts()
    {
        $woo_checkout_otp = self::$woo_checkout_otp;
        if ($woo_checkout_otp && is_checkout()) {
            wp_enqueue_script('wpstorm-notify-woo-otp', WPSTORM_NOTIFY_URL . 'assets/js/wpstorm-notify-woo-otp.js', ['jquery'], WPSTORM_NOTIFY_VERSION, true);
            wp_localize_script(
                'wpstorm-notify-woo-otp',
                'ajax_url',
                ['ajax_url' => admin_url('admin-ajax.php')]
            );
        }
    }

    /**
     * Show Already sent tracking codes for current order
     *
     * @return void
     */
    public function already_sent_tracking_codes()
    {
        // Get the current order ID
        $order_id = get_the_ID();

        // Retrieve the tracking code data from the custom table for this order
        global $wpdb;
        $table_name         = $wpdb->prefix . 'wpstorm-notify_tracking_codes';
        $tracking_code_data = $wpdb->get_results($wpdb->prepare("SELECT * FROM $table_name WHERE order_id = %d", $order_id));

        // Display a message if no tracking code data is found
        if (empty($tracking_code_data)) {
            echo '<p> ' . esc_html__('No tracking code data found for this order.', 'wpstorm-notify') . '</p>';

            return;
        }

        // Display all tracking code data if there is more than one record
        if (count($tracking_code_data) > 1) {
            echo '<p>' . esc_html__('Multiple tracking codes found for this order:', 'wpstorm-notify') . '</p>';

            foreach ($tracking_code_data as $data) {
                // Convert the date format to your desired format
                $formatted_date = date('Y/m/d', strtotime($data->post_date));
                echo '<ul>';
                echo '<li><strong>' . esc_html__('Tracking Code: ', 'wpstorm-notify') . '</strong>' . esc_html($data->tracking_code) . '</li>';
                echo '<li><strong>' . esc_html__('Post Service Provider: ', 'wpstorm-notify') . '</strong> ' . esc_html($data->post_service_provider) . '</li>';
                echo '<li><strong>' . esc_html__('Post Date: ', 'wpstorm-notify') . '</strong>' . esc_html($formatted_date) . '</li>';
                echo '</ul>';
            }
        } else {
            echo '<p>' . esc_html__('One tracking code found for this order:', 'wpstorm-notify') . '</p>';
            // Convert the date format to your desired format
            $formatted_date = date('Y/m/d', strtotime($tracking_code_data[0]->post_date));
            ?>
            <div class="already-sent-tracking-code">
                <p>
                    <strong><?php echo esc_html__('Tracking Code: ', 'wpstorm-notify') ?></strong> <?php echo esc_html($tracking_code_data[0]->tracking_code); ?>
                </p>
                <p>
                    <strong><?php echo esc_html__('Post Service Provider: ', 'wpstorm-notify') ?></strong> <?php echo esc_html($tracking_code_data[0]->post_service_provider); ?>
                </p>
                <p>
                    <strong><?php echo esc_html__('Post Date: ', 'wpstorm-notify') ?></strong> <?php echo esc_html($formatted_date); ?>
                </p>
            </div>
            <?php
        }
    }

    /**
     * Add the meta box to the order page for show already sent tracking code
     *
     * @return void
     */
    public function add_tracking_code_meta_box()
    {
        add_meta_box(
            'already-sent-tracking-codes',
            __('Already sent tracking code', 'wpstorm-notify'),
            [$this, 'already_sent_tracking_codes'],
            'shop_order',
            'side',
            'default'
        );
    }


    /**
     * Send Tracking code for orders.
     *
     * @since 1.0.0
     */
    public function tracking_code_order_postbox()
    {
        add_meta_box(
            'tracking_send_sms',
            __('Send tracking code', 'wpstorm-notify'),
            [
                $this,
                'add_order_tracking_box',
            ],
            'shop_order',
            'side',
            'core'
        );
    }


    /**
     * Show send tracking code sms form in order page as meta box.
     *
     * @param $post
     *
     * @return void
     */
    public function add_order_tracking_box($post)
    {
        echo '<div id="tracking-code-container">';
        echo '<label for="tracking-code-input">' . esc_html__('Tracking Code', 'wpstorm-notify') . '</label>';
        echo '<div id="tracking-code-input"><input type="text" name="tracking_code" id="tracking_code" placeholder="' . esc_attr__('Enter tracking code', 'wpstorm-notify') . '"/></div>';

        // Select input for selecting the service provider
        echo '<div id="tracking-code-provider">';
        echo '<label for="post_service_provider">' . esc_html__('Service Provider', 'wpstorm-notify') . '</label>';
        echo '<select name="post_service_provider" id="post_service_provider">';

        // Default options
        echo '<option value="post_office">' . esc_html__('Post Office', 'wpstorm-notify') . '</option>';
        echo '<option value="tipaxco">' . esc_html__('Tipaxco', 'wpstorm-notify') . '</option>';

        // Option for custom provider name
        echo '<option value="custom_provider">' . esc_html__('Custom Provider', 'wpstorm-notify') . '</option>';

        echo '</select>';
        echo '</div>';

        // Date picker for selecting the date of posting
        echo '<div id="tracking-code-date">';
        echo '<label for="post_date">' . esc_html__('Date of Posting', 'wpstorm-notify') . '</label>';
        echo '<div id="wpstorm-notify-post-persian-date"></div>';
        echo '<input type="hidden" name="wpstorm-notify-tracking-date-field-value" id="wpstorm-notify-tracking-date-field-value" value=""/>';
        echo '</div>';

        echo '<div id="tracking-code-button"><div class="button" id="send_tracking_code_button"><span class="button__text">' . esc_html__('Send Sms', 'wpstorm-notify') . '</span></div></div>';
        echo '<input type="hidden" id="tracking-code-order_id" value="' . esc_attr($post->ID) . '">';
        echo '<div id="send_tracking_code_response" style="display: none;"></div>';
        echo '</div>';

        ?>
        <script>
            // Handle custom provider input
            const select = document.querySelector('#post_service_provider')
            const input = document.createElement('input')
            input.type = 'text'
            input.style.display = 'none'
            input.name = 'post_service_provider'
            input.id = 'custom_provider'
            input.placeholder = 'نام شرکت یا نحوه ارسال سفارش'
            select.after(input)
            select.addEventListener('change', function() {
                if (this.value === 'custom_provider') {
                    input.style.display = ''
                    input.focus()
                } else {
                    input.style.display = 'none'
                    input.value = ''
                }
            })
        </script>
        <?php
    }

    public function send_tracking_code_sms()
    {
        $tracking_code         = sanitize_text_field($_POST['tracking_code'] ?? '');
        $post_service_provider = sanitize_text_field($_POST['post_service_provider'] ?? '');
        $post_date             = sanitize_text_field($_POST['post_date'] ?? '');
        $order_id              = absint($_POST['order_id'] ?? '');
        try {
            if (empty($tracking_code)) {
                throw new Exception(__('Please enter the tracking code.', 'wpstorm-notify'));
            }

            if (empty($post_service_provider) || $post_service_provider == 'none') {
                throw new Exception(__('Please select a service provider.', 'wpstorm-notify'));
            }

            if (empty($post_date)) {
                throw new Exception(__('Please select the date of posting.', 'wpstorm-notify'));
            }

            $order = wc_get_order($order_id);
            $phone = $order->get_billing_phone();
            if (empty($phone)) {
                throw new Exception(__('Customer phone number not entered.', 'wpstorm-notify'));
            }

            $order_data['order_id']              = $order->get_id();
            $order_data['order_status']          = wc_get_order_status_name($order->get_status());
            $order_data['billing_full_name']     = $order->get_formatted_billing_full_name();
            $order_data['shipping_full_name']    = $order->get_formatted_shipping_full_name();
            $order_data['post_service_provider'] = $post_service_provider;
            $order_data['post_date']             = $post_date;

            $this->send_tracking_code($phone, $tracking_code, $order_data);

            // Convert post_date to date format to save on the DB
            $date_str       = str_replace(['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'], [
                '0',
                '1',
                '2',
                '3',
                '4',
                '5',
                '6',
                '7',
                '8',
                '9'
            ], $post_date);
            $date_parts     = explode('/', $date_str); // explode the string by "/"
            $year           = $date_parts[0];
            $month          = $date_parts[1];
            $day            = $date_parts[2];
            $jalali_date    = new DateTime("$year-$month-$day", new DateTimeZone('Asia/Tehran')); // create a DateTime object with the Jalali date
            $gregorian_date = $jalali_date->format('Y-m-d'); // format the date in the Gregorian calendar as 'YYYY-MM-DD'

            // Insert tracking code data into database
            global $wpdb;
            $table_name = $wpdb->prefix . 'wpstorm-notify_tracking_codes';
            $wpdb->insert(
                $table_name,
                [
                    'tracking_code'         => $tracking_code,
                    'post_service_provider' => $post_service_provider,
                    'post_date'             => $gregorian_date,
                    'order_id'              => $order_id
                ]
            );

            wp_send_json_success();
        } catch (Exception $e) {
            wp_send_json_error($e->getMessage());
        }
    }


    /**
     * Send tracking code.
     *
     * @param $phone
     * @param $tracking_code
     * @param $order_data
     *
     * @return bool
     * @throws Exception
     */
    public function send_tracking_code($phone, $tracking_code, $order_data)
    {

        if (empty(self::$woo_tracking_pattern)) {
            throw new Exception(__('Pattern not entered to send tracking code', 'wpstorm-notify'));
        }

        $phone          = Wpstorm_Notify_Base::tr_num($phone);
        $input_data     = [];
        $patternMessage = Wpstorm_Notify_Ippanel::get_registered_pattern_variables(self::$woo_tracking_pattern);
        if ($patternMessage === null) {
            throw new Exception(__('Probably your pattern has not been approved', 'wpstorm-notify'));
        }
        if (str_contains($patternMessage, '%tracking_code%')) {
            $input_data['tracking_code'] = strval($tracking_code);
        }
        if (str_contains($patternMessage, '%order_id%')) {
            $input_data['order_id'] = strval($order_data['order_id']);
        }
        if (str_contains($patternMessage, '%order_status%')) {
            $input_data['order_status'] = strval($order_data['order_status']);
        }
        if (str_contains($patternMessage, '%billing_full_name%')) {
            $input_data['billing_full_name'] = strval($order_data['billing_full_name']);
        }
        if (str_contains($patternMessage, '%shipping_full_name%')) {
            $input_data['shipping_full_name'] = strval($order_data['shipping_full_name']);
        }
        if (str_contains($patternMessage, '%post_service_provider%')) {
            $input_data['post_service_provider'] = strval($order_data['post_service_provider']);
        }
        if (str_contains($patternMessage, '%post_date%')) {
            $input_data['post_date'] = strval($order_data['post_date']);
        }

        return Wpstorm_Notify_Ippanel::send_pattern(self::$woo_tracking_pattern, $phone, $input_data);
    }

    /**
     * Send woocommerce verification code.
     */
    public function send_woocommerce_verification_code($phone, $data)
    {
        $phone = Wpstorm_Notify_Base::tr_num($phone);
        if (empty($phone) || empty(self::$woo_checkout_otp_pattern) || empty($data)) {
            return false;
        }

        $input_data         = [];
        $input_data['code'] = strval($data['code']);

        return Wpstorm_Notify_Ippanel::send_pattern(self::$woo_checkout_otp_pattern, $phone, $input_data);
    }

    /**
     * Check if code is valid for woocommerce
     */
    public function check_if_code_is_valid_for_woo($phone, $code)
    {
        global $wpdb;
        $table          = $wpdb->prefix . 'wpstorm_notify_vcode';
        $generated_code = $wpdb->get_col("SELECT code FROM {$table} WHERE phone = '" . $phone . "'");
        if ($generated_code[0] == $code) {
            // $wpdb->delete( $table, array( 'phone' => $phone ) );
            return true;
        }

        return false;
    }

    /**
     * Delete code for woocommerce
     */
    public function delete_code_for_woo($phone)
    {
        global $wpdb;
        $table = $wpdb->prefix . 'wpstorm_notify_vcode';
        $wpdb->delete($table, ['phone' => $phone]);
    }

    /**
     * Woocommerce payment finished.
     */
    public function woo_payment_finished($id)
    {
        $order              = wc_get_order($id);
        $phone              = $order->get_billing_phone();
        $billing_first_name = $order->get_billing_first_name();
        $billing_last_name  = $order->get_billing_last_name();
        $user_full_name     = $billing_first_name . ' ' . $billing_last_name;

        if (empty($phone)) {
            return;
        }

        $list[0] = (object) [
            'number'       => $phone,
            'name'         => $user_full_name ?? '',
            'phonebook_id' => (int) Wpstorm_Notify_Base::$woo_phonebook_id
        ];
        Wpstorm_Notify_Ippanel::save_list_of_phones_to_phonebook($list);

        $this->delete_otp_code($order);

        return true;
    }

    /**
     * Woocommerce retention action.
     */
    public function woo_retention_action()
    {
        $retention_order_no    = self::$woo_retention_order_no;
        $retention_order_month = self::$woo_retention_order_month;
        $retention_message     = self::$woo_retention_msg;
        if (empty($retention_order_no) || empty($retention_order_month) || empty($retention_message)) {
            return;
        }

        global $wpdb;
        $customer_ids = $wpdb->get_col("SELECT DISTINCT meta_value  FROM $wpdb->postmeta WHERE meta_key = '_customer_user' AND meta_value > 0");
        if (sizeof($customer_ids) > 0) {
            foreach ($customer_ids as $customer_id) {
                $customer   = new WC_Customer($customer_id);
                $last_order = $customer->get_last_order();
                if (!$last_order) {
                    continue;
                }
                $sent_retention_message = get_post_meta($last_order->get_id(), 'sent_retention_message', true);
                if ($sent_retention_message == '1') {
                    continue;
                }
                $date_completed = $last_order->get_date_completed();
                if (!empty($date_completed) && $date_completed->getTimestamp() <= strtotime('-' . $retention_order_month . ' Months')) {
                    $args   = [
                        'type'           => 'shop_order',
                        'customer_id'    => $customer_id,
                        'date_completed' => '<=' . strtotime('-' . $retention_order_month . ' Months'),
                    ];
                    $orders = wc_get_orders($args);
                    if (count($orders) >= $retention_order_no) {
                        $message = str_replace([
                            '%billing_full_name%',
                            '%shipping_full_name%',
                        ], [
                            $last_order->get_formatted_billing_full_name(),
                            $last_order->get_formatted_shipping_full_name(),
                        ], $retention_message);
                        Wpstorm_Notify_Ippanel::send_message([$last_order->get_billing_phone()], $message, Wpstorm_Notify_Base::$fromNum);
                        update_post_meta($last_order->get_id(), 'sent_retention_message', '1');
                    }
                }
            }
        }
    }

    /**
     * Woocommerce checkout fields.
     */
    public function woocommerce_checkout_fields($fields)
    {
        if (self::$woo_checkout_otp !== 'true') {
            return $fields;
        }

        $fields['billing_phone_send_otp']   = [
            'label'    => __('Verification code', 'wpstorm-notify'),
            'required' => '0',
            'type'     => 'text',
            'class'    => [
                'form-row-wide',
                'otp_field'
            ],
            'priority' => 101
        ];
        $fields['billing_phone_otp']        = [
            'label'    => __('Verification code', 'wpstorm-notify'),
            'required' => '1',
            'type'     => 'number',
            'class'    => [
                'form-row-first',
                'otp_field'
            ],
            'priority' => 102
        ];
        $fields['billing_phone_otp_button'] = [
            'label'    => __('Send', 'wpstorm-notify'),
            'required' => '0',
            'class'    => [
                'form-row-last',
                'otp_field_should_remove'
            ],
            'priority' => 103
        ];

        return $fields;
    }

    /**
     * Woocommerce checkout process.
     */
    public function woocommerce_checkout_process()
    {
        if (self::$woo_checkout_otp !== 'true') {
            return;
        }

        $billing_phone_otp = isset($_POST['billing_phone_otp']) ? sanitize_text_field($_POST['billing_phone_otp']) : '';
        if (empty($billing_phone_otp)) {
            wc_add_notice(__('Please confirm your phone number first', 'wpstorm-notify'), 'error');
        }
        $billing_phone = isset($_POST['billing_phone']) ? sanitize_text_field($_POST['billing_phone']) : '';
        $is_valid      = $this->check_if_code_is_valid_for_woo($billing_phone, $billing_phone_otp);
        if (!$is_valid) {
            wc_add_notice(__('The verification code entered is not valid', 'wpstorm-notify'), 'error');
        }
    }


    /**
     * Send OTP Code.
     */
    public function send_otp_code()
    {
        $mobile = sanitize_text_field($_POST['mobile']);
        if (!isset($mobile)) {
            wp_send_json_error(__('Please enter phone number.', 'wpstorm-notify'));
        }
        $generated_code = rand(1000, 9999);
        Wpstorm_Notify_Base::save_generated_code_to_db($mobile, $generated_code);
        $data   = [
            'code' => $generated_code,
        ];
        $result = $this->send_woocommerce_verification_code($mobile, $data);
        if (!$result) {
            wp_send_json_error(__('An error occurred', 'wpstorm-notify'));
        } else {
            wp_send_json_success(__('Verification code sent successfully', 'wpstorm-notify'));
        }
    }

    /**
     * Delete OTP Code.
     */
    public function delete_otp_code($order_id)
    {
        $order = wc_get_order($order_id);
        $this::delete_code_for_woo($order->get_billing_phone());
    }

    /**
     *
     * Pre-populate checkout fields.
     *
     */

    public function pre_populate_checkout_fields($input, $key)
    {
        global $current_user;
        $digits_mobile = get_user_meta($current_user->ID, 'digits_phone_no', true);
        switch ($key):
            case 'billing_first_name':
            case 'shipping_first_name':
                return $current_user->first_name;
                break;

            case 'billing_last_name':
            case 'shipping_last_name':
                return $current_user->last_name;
                break;
            case 'billing_phone':
                return !empty($digits_mobile) ? '0' . $digits_mobile : '';
                break;

        endswitch;
    }
}

Wpstorm_Notify_Woocommerce::get_instance();
