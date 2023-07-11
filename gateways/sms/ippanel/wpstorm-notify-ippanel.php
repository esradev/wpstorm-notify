<?php

/**
 * Define the IPPanel API class.
 *
 * @since      1.0.0
 */

// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

class Wpstorm_Notify_Ippanel
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
     */
    public function __construct()
    {
    }

    /**
     * Get phonebooks.
     *
     * @since 1.0.0
     */
    public static function get_phonebooks()
    {
        // Try the first method: wp_remote_post
        $url = 'https://ippanel.com/services.jspd';
        $data = [
            'uname' => Wpstorm_Notify_Base::$username,
            'pass' => Wpstorm_Notify_Base::$password,
            'op' => 'booklist'
        ];
        $args = [
            'body' => $data,
            'timeout' => '5',
            'redirection' => '5',
            'httpversion' => '1.0',
            'sslverify' => false, // Set to true if your server has a valid SSL certificate
            'headers' => [
                'Content-Type' => 'application/x-www-form-urlencoded'
            ]
        ];
        $response = wp_remote_post($url, $args);
        if (!is_wp_error($response)) {
            $response_body = wp_remote_retrieve_body($response);
            $response_data = json_decode($response_body);

            if (isset($response_data[1])) {
                return json_decode($response_data[1], true);
            }
        }

        if (is_wp_error($response)) {
            // Try the second method: wp_remote_request
            $args = [
                'method'      => 'GET',
                'timeout'     => 5,
                'redirection' => 5,
                'httpversion' => '1.0',
                'headers'     => [
                    'Authorization' => Wpstorm_Notify_Base::$apiKey,
                    'Content-Type'  => 'application/json'
                ],
            ];
            $response = wp_remote_request('http://api.ippanel.com/api/v1/phonebook/phonebooks', $args);
            if (!is_wp_error($response)) {
                $response_code = wp_remote_retrieve_response_code($response);
                if ($response_code == 200) {
                    $response_body = wp_remote_retrieve_body($response);
                    $response_data = json_decode($response_body, true);
                    if ($response_data) {
                        return $response_data['data'];
                    }
                }
            }
        }
        // Both methods failed, return false
        return false;
    }

    /**
     * Save list of phones to phonebook
     *
     * @param array $list
     *
     * @return false|mixed|null
     * @since 1.0.0
     */
    public static function save_list_of_phones_to_phonebook(array $list)
    {
        $body = [
            'list' => $list,
        ];
        $headers = [
            'Authorization' => Wpstorm_Notify_Base::$apiKey,
            'Content-Type' => 'application/json',
        ];
        $args = [
            'method' => 'POST',
            'headers' => $headers,
            'body' => json_encode($body),
        ];
        $response = wp_remote_post('http://api.ippanel.com/api/v1/phonebook/numbers-add-list', $args);
        if (is_wp_error($response)) {
            return false;
        }

        return json_decode(wp_remote_retrieve_body($response), true);
    }

    /**
     * Get registered pattern variables.
     *
     * @since 1.0.0
     */
    public static function get_registered_pattern_variables($patternCode)
    {
        $body  = [
            'uname'       => Wpstorm_Notify_Base::$username,
            'pass'        => Wpstorm_Notify_Base::$password,
            'op'          => 'patternInfo',
            'patternCode' => $patternCode,
        ];

        $res   = wp_remote_post(
            'http://ippanel.com/api/select',
            [
                'method'      => 'POST',
                'headers'     => ['Content-Type' => 'application/json'],
                'data_format' => 'body',
                'body'        => json_encode($body),
            ]
        );
        if (is_wp_error($res)) {
            return $res;
        }
        $res = json_decode($res['body'], true);

        return $res['data']['patternMessage'] ?? null;
    }


    /**
     * Send Message function.
     *
     * @param $phones
     * @param $message
     * @param $sender
     *
     * @return array|mixed|WP_Error|null
     */
    public static function send_message($phones, $message, $sender)
    {

        $body     = [
            'op'      => 'send',
            'uname'   => Wpstorm_Notify_Base::$username,
            'pass'    => Wpstorm_Notify_Base::$password,
            'message' => $message,
            'from'    => $sender,
            'to'      => $phones,
            'time'    => '',
        ];
        $response = wp_remote_post(
            'http://ippanel.com/api/select',
            [
                'method'      => 'POST',
                'headers'     => ['Content-Type' => 'application/json'],
                'data_format' => 'body',
                'body'        => json_encode($body),
            ]
        );
        if (is_wp_error($response)) {
            return $response;
        }

        return json_decode($response['body']);
    }

    public static function send_sms_to_phonebooks($phonebooks_ids, $message, $sender)
    {

        $url = 'https://ippanel.com/services.jspd';

        $param = [
            'uname' => Wpstorm_Notify_Base::$username,
            'pass' => Wpstorm_Notify_Base::$password,
            'from' => $sender,
            'message' => $message,
            'bookid' => json_encode($phonebooks_ids),
            'op' => 'booksend'
        ];

        $response = wp_remote_post($url, [
            'method' => 'POST',
            'body' => $param
        ]);

        if (is_wp_error($response)) {
            $error_message = $response->get_error_message();
            return "Something went wrong: $error_message";
        } else {
            $response_body = wp_remote_retrieve_body($response);
            $response_data = json_decode($response_body);

            return $response_data[1];
        }
    }

    /**
     * Get credit.
     *
     * @return false|string
     * @since 1.0.0
     *
     */
    public static function get_credit()
    {
        $body     = [
            'uname' => Wpstorm_Notify_Base::$username,
            'pass'  => Wpstorm_Notify_Base::$password,
            'op'    => 'credit',
        ];
        $response = wp_remote_post(
            'http://ippanel.com/api/select',
            [
                'method'      => 'POST',
                'headers'     => ['Content-Type' => 'application/json'],
                'data_format' => 'body',
                'body'        => json_encode($body),
            ]
        );
        if (is_wp_error($response)) {
            return false;
        }

        $response = json_decode($response['body'], true);
        if (!is_array($response) || !isset($response[1])) {
            return false;
        }
        $separator = '.';
        if (strpos($response[1], '/')) {
            $separator = '/';
        }
        if (strpos($response[1], '.')) {
            $separator = '.';
        }

        $credit_rial = explode($separator, $response[1])[0];

        return substr($credit_rial, 0, -1);
    }


    /**
     * Send low credit notify to admin.
     *
     * @return void
     * @since 1.0.0
     *
     */
    public static function send_admin_low_credit_to_admin()
    {
        $fromnum = '3000505';
        if (empty(Wpstorm_Notify_Base::$admin_number)) {
            return;
        }
        $message  = __('Dear user, The charge for your SMS panel is less than 10 thousand tomans, and your sites SMS may not be sent soon and your site may be blocked. I will charge the SMS system as soon as possible. www.wpstorm.ir, +989300410381', 'Wpstorm_Notify');
        $body     = [
            'uname'   => Wpstorm_Notify_Base::$username,
            'pass'    => Wpstorm_Notify_Base::$password,
            'from'    => $fromnum,
            'op'      => 'send',
            'to'      => [Wpstorm_Notify_Base::$admin_number],
            'time'    => '',
            'message' => $message,
        ];
        $response = wp_remote_post(
            'http://ippanel.com/api/select',
            [
                'method'      => 'POST',
                'headers'     => ['Content-Type' => 'application/json'],
                'data_format' => 'body',
                'body'        => json_encode($body),
            ]
        );
        json_decode($response['body']);
    }


    /**
     * Send pattern.
     *
     * @param $pattern
     * @param $phone
     * @param $input_data
     *
     * @return bool
     * @since 1.0.0
     *
     */
    public static function send_pattern($pattern, $phone, $input_data)
    {
        $body     = [
            'user'        => Wpstorm_Notify_Base::$username,
            'pass'        => Wpstorm_Notify_Base::$password,
            'fromNum'     => Wpstorm_Notify_Base::$fromNum,
            'op'          => 'pattern',
            'patternCode' => $pattern,
            'toNum'       => $phone,
            'inputData'   => [$input_data],
        ];

        $response = wp_remote_post(
            'http://ippanel.com/api/select',
            [
                'method'      => 'POST',
                'headers'     => ['Content-Type' => 'application/json'],
                'data_format' => 'body',
                'body'        => json_encode($body),
            ]
        );
        if (is_wp_error($response)) {
            return false;
        }

        $response = json_decode($response['body'], true);

        return true;
    }

    /**
     * Check if credentials is valid.
     */
    public static function check_if_credentials_is_valid()
    {
        $body = [
            'username' => Wpstorm_Notify_Base::$username,
            'password' => Wpstorm_Notify_Base::$password,
        ];

        $response = wp_remote_post(
            'http://reg.ippanel.com/parent/wpstorm',
            [
                'method'      => 'POST',
                'headers'     => ['Content-Type' => 'application/json'],
                'data_format' => 'body',
                'body'        => json_encode($body),
            ]
        );
        $response = json_decode($response['body']);
        if ($response->message == 1) {
            return true;
        }

        return false;
    }

    /**
     * Send timed sms
     *
     * @param $phone_number
     * @param $date
     * @param $message
     *
     * @return array|WP_Error
     */
    public static function send_timed_sms($phone_number, $date, $message)
    {
        // Define the endpoint URL and request parameters
        $url = 'https://api2.ippanel.com/api/v1/sms/send/webservice/single';
        $params = [
            'recipient' => [$phone_number],
            'sender' => '+983000505',
            'time' => $date,
            'message' => $message
        ];
        $headers = [
            'Accept' => 'application/json',
            'Apikey' => Wpstorm_Notify_Base::$apiKey,
            'Content-Type' => 'application/json'
        ];

        // Make the wp_remote_post() request
        $response = wp_remote_post($url, [
            'headers' => $headers,
            'body' => json_encode($params),
        ]);

        return $response;
    }
}

Wpstorm_Notify_Ippanel::get_instance();
