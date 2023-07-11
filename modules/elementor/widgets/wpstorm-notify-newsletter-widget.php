<?php

use Elementor\Controls_Manager;
use Elementor\Widget_Base;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

class Wpstorm_Notify_Newsletter_Widget extends Widget_Base
{

    /**
     * Get widget name.
     *
     * Retrieve list widget name.
     *
     * @return string Widget name.
     * @since 1.0.0
     * @access public
     */
    public function get_name()
    {
        return 'wpstorm-notify-news';
    }

    /**
     * Get widget title.
     *
     * Retrieve list widget title.
     *
     * @return string Widget title.
     * @since 1.0.0
     * @access public
     */
    public function get_title()
    {
        return esc_html__('wpstorm-notify Newsletter', 'wpstorm-notify');
    }

    /**
     * Get widget icon.
     *
     * Retrieve list widget icon.
     *
     * @return string Widget icon.
     * @since 1.0.0
     * @access public
     */
    public function get_icon()
    {
        return 'eicon-bullet-list';
    }

    /**
     * Get custom help URL.
     *
     * Retrieve a URL where the user can get more information about the widget.
     *
     * @return string Widget help URL.
     * @since 1.0.0
     * @access public
     */
    public function get_custom_help_url()
    {
        return 'https://developers.wpstorm-notify.ir/docs/widgets/';
    }

    /**
     * Get widget categories.
     *
     * Retrieve the list of categories the list widget belongs to.
     *
     * @return array Widget categories.
     * @since 1.0.0
     * @access public
     */
    public function get_categories()
    {
        return ['general'];
    }

    /**
     * Get widget keywords.
     *
     * Retrieve the list of keywords the list widget belongs to.
     *
     * @return array Widget keywords.
     * @since 1.0.0
     * @access public
     */
    public function get_keywords()
    {
        return ['wpstorm-notify', 'form', 'newsletter'];
    }

    /**
     * Register list widget controls.
     *
     * Add input fields to allow the user to customize the widget settings.
     *
     * @since 1.0.0
     * @access protected
     */
    protected function register_controls()
    {

        $this->start_controls_section(
            'content_section',
            [
                'label' => esc_html__('wpstorm-notify Newsletter', 'wpstorm-notify'),
                'tab'   => Controls_Manager::TAB_CONTENT,
            ]
        );

        $phonebook_options = [];

        $phonebooks = Wpstorm_Notify_Ippanel::get_phonebooks();

        if (is_array($phonebooks)) {
            foreach ($phonebooks as $phonebook) {
                $phonebook_options[$phonebook['id']] = $phonebook['title'];
            }
        }

        $this->add_control(
            'phonebook',
            [
                'label'   => esc_html__('Phonebook', 'wpstorm-notify'),
                'type'    => Controls_Manager::SELECT,
                'default' => '0',
                'options' => $phonebook_options,
            ]
        );

        $this->add_control(
            'send_verify_code',
            [
                'label'        => esc_html__('Send Verify Code?', 'wpstorm-notify'),
                'type'         => Controls_Manager::SWITCHER,
                'label_on'     => esc_html__('Yes', 'wpstorm-notify'),
                'label_off'    => esc_html__('No', 'wpstorm-notify'),
                'return_value' => 'yes',
                'default'      => 'no',
            ]
        );

        $this->add_control(
            'verify_code_pattern',
            [
                'label' => esc_html__('Verify Code Pattern', 'wpstorm-notify'),
                'type'  => Controls_Manager::TEXT,
            ]
        );

        $this->add_control(
            'send_welcome_msg',
            [
                'label'        => esc_html__('Send Welcome Message?', 'wpstorm-notify'),
                'type'         => Controls_Manager::SWITCHER,
                'label_on'     => esc_html__('Yes', 'wpstorm-notify'),
                'label_off'    => esc_html__('No', 'wpstorm-notify'),
                'return_value' => 'yes',
                'default'      => 'no',
            ]
        );

        $this->add_control(
            'welcome_msg_pattern',
            [
                'label' => esc_html__('Welcome Message Pattern', 'wpstorm-notify'),
                'type'  => Controls_Manager::TEXT,
            ]
        );
        $this->end_controls_section();

        // Add Style Controls For #newsletter
        $this->start_controls_section(
            'newsletter_section',
            [
                'label' => esc_html__('wpstorm-notify Newsletter Form', 'wpstorm-notify'),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'form_title',
            [
                'label' => esc_html__('Form Title', 'wpstorm-notify'),
                'type'  => Controls_Manager::TEXT,
            ]
        );

        $this->add_control(
            'name_placeholder',
            [
                'label'   => esc_html__('Name Field Placeholder', 'wpstorm-notify'),
                'type'    => Controls_Manager::TEXT,
                'default' => esc_html__('Like: Ghafary', 'wpstorm-notify'),
            ]
        );

        $this->add_control(
            'phone_placeholder',
            [
                'label'   => esc_html__('Phone Field Placeholder', 'wpstorm-notify'),
                'type'    => Controls_Manager::TEXT,
                'default' => esc_html__('Like: 09300410381', 'wpstorm-notify'),
            ]
        );

        $this->add_control(
            'newsletter_background_color',
            [
                'label'     => esc_html__('Form Background Color', 'wpstorm-notify'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} #newsletter' => 'background-color: {{VALUE}};',
                ],
                'default'   => '#f7f7f7',
            ]
        );

        $this->add_control(
            'newsletter_border_radius',
            [
                'label'      => esc_html__('Form Border Radius', 'wpstorm-notify'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em'],
                'selectors'  => [
                    '{{WRAPPER}} #newsletter' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'default'    => [
                    'top'    => '5',
                    'right'  => '5',
                    'bottom' => '5',
                    'left'   => '5',
                    'unit'   => 'px',
                ],
            ]
        );

        $this->add_control(
            'newsletter_form_alignment',
            [
                'label'     => esc_html__('Form Alignment', 'wpstorm-notify'),
                'type'      => Controls_Manager::CHOOSE,
                'options'   => [
                    'left'   => [
                        'title' => esc_html__('Left', 'wpstorm-notify'),
                        'icon'  => 'eicon-text-align-left',
                    ],
                    'center' => [
                        'title' => esc_html__('Center', 'wpstorm-notify'),
                        'icon'  => 'eicon-text-align-center',
                    ],
                    'right'  => [
                        'title' => esc_html__('Right', 'wpstorm-notify'),
                        'icon'  => 'eicon-text-align-right',
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} #newsletter' => 'text-align: {{VALUE}};',
                ],
                'default'   => 'center',
            ]
        );

        $this->add_control(
            'newsletter_form_margin',
            [
                'label'      => esc_html__('Form Margin', 'wpstorm-notify'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors'  => [
                    '{{WRAPPER}} #newsletter' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'default'    => [
                    'top'    => '5',
                    'right'  => '30',
                    'bottom' => '5',
                    'left'   => '30',
                    'unit'   => '%',
                ],
            ]
        );
        $this->add_control(
            'newsletter_form_padding',
            [
                'label'      => esc_html__('Form Padding', 'wpstorm-notify'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors'  => [
                    '{{WRAPPER}} #newsletter' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'default'    => [
                    'top'    => 10,
                    'right'  => 10,
                    'bottom' => 10,
                    'left'   => 10,
                    'unit'   => 'px',
                ],
            ]
        );

        $this->add_responsive_control(
            'newsletter_input_width',
            [
                'label'      => esc_html__('Input Width', 'wpstorm-notify'),
                'type'       => Controls_Manager::SLIDER,
                'size_units' => ['px', '%', 'em'],
                'range'      => [
                    'px' => [
                        'min' => 0,
                        'max' => 500,
                    ],
                    '%'  => [
                        'min' => 0,
                        'max' => 100,
                    ],
                    'em' => [
                        'min' => 0,
                        'max' => 50,
                    ],
                ],
                'default'    => [
                    'unit' => 'px',
                    'size' => 200,
                ],
                'selectors'  => [
                    '{{WRAPPER}} .newsletter_text' => 'width: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'newsletter_input_margin',
            [
                'label'      => esc_html__('Input Margin', 'wpstorm-notify'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors'  => [
                    '{{WRAPPER}} .newsletter_text' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'default'    => [
                    'top'    => '5',
                    'right'  => '5',
                    'bottom' => '0',
                    'left'   => '5',
                    'unit'   => 'px',
                ],
            ]
        );
        $this->add_control(
            'newsletter_input_padding',
            [
                'label'      => esc_html__('Input Padding', 'wpstorm-notify'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors'  => [
                    '{{WRAPPER}} .newsletter_text' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'default'    => [
                    'top'    => 10,
                    'right'  => 10,
                    'bottom' => 10,
                    'left'   => 10,
                    'unit'   => 'px',
                ],
            ]
        );
        $this->add_control(
            'newsletter_input_direction',
            [
                'label'     => esc_html__('Input Text Direction', 'wpstorm-notify'),
                'type'      => Controls_Manager::SELECT,
                'options'   => [
                    'rtl' => esc_html__('Right to Left', 'wpstorm-notify'),
                    'ltr' => esc_html__('Left to Right', 'wpstorm-notify'),
                ],
                'default'   => 'rtl',
                'selectors' => [
                    '{{WRAPPER}} .newsletter_input' => 'direction: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'newsletter_text_border_radius',
            [
                'label'      => esc_html__('Text Border Radius', 'wpstorm-notify'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors'  => [
                    '{{WRAPPER}} .newsletter_text' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'default'    => [
                    'top'    => 5,
                    'right'  => 5,
                    'bottom' => 5,
                    'left'   => 5,
                    'unit'   => 'px',
                ],
            ]
        );

        $this->add_control(
            'newsletter_text_background_color',
            [
                'label'     => esc_html__('Text Background Color', 'wpstorm-notify'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .newsletter_text' => 'background-color: {{VALUE}};',
                ],
                'default'   => '#ffffff',
            ]
        );

        $this->add_control(
            'newsletter_text_font_size',
            [
                'label'      => esc_html__('Text Font Size', 'wpstorm-notify'),
                'type'       => Controls_Manager::SLIDER,
                'size_units' => ['px', 'em'],
                'range'      => [
                    'px' => [
                        'min' => 10,
                        'max' => 30,
                    ],
                    'em' => [
                        'min' => 1,
                        'max' => 3,
                    ],
                ],
                'selectors'  => [
                    '{{WRAPPER}} .newsletter_text' => 'font-size: {{SIZE}}{{UNIT}};',
                ],
                'default'    => [
                    'size' => 16,
                    'unit' => 'px',
                ],
            ]
        );

        $this->add_control(
            'newsletter_text_color',
            [
                'label'     => esc_html__('Text Color', 'wpstorm-notify'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .newsletter_text' => 'color: {{VALUE}};',
                ],
                'default'   => '#000000',
            ],
		);


        $this->end_controls_section();

        // Submit Button Section
        $this->start_controls_section(
            'submit_button_section',
            [
                'label' => esc_html__('Submit Button', 'wpstorm-notify'),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

        // Submit Button Controls
        $this->add_control(
            'submit_button_text',
            [
                'label'   => esc_html__('Button Text', 'wpstorm-notify'),
                'type'    => Controls_Manager::TEXT,
                'default' => esc_html__('Submit', 'wpstorm-notify'),
            ]
        );

        $this->add_control(
            'submit_button_color',
            [
                'label'     => esc_html__('Button Background Color', 'wpstorm-notify'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .submit-button' => 'background-color: {{VALUE}};',
                ],
                'default'   => '#0002cb',
            ]
        );

        $this->add_control(
            'submit_button_text_color',
            [
                'label'     => esc_html__('Button Text Color', 'wpstorm-notify'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .submit-button' => 'color: {{VALUE}};',
                ],
                'default'   => '#000000',
            ]
        );

        $this->add_control(
            'submit_button_margin',
            [
                'label'      => esc_html__('Margin', 'wpstorm-notify'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'selectors'  => [
                    '{{WRAPPER}} .submit-button' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'submit_button_padding',
            [
                'label'      => esc_html__('Padding', 'wpstorm-notify'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'selectors'  => [
                    '{{WRAPPER}} .submit-button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
        $this->end_controls_section();

        // Submit Button Section
        $this->start_controls_section(
            'submit_code_button_section',
            [
                'label' => esc_html__('Submit Code Button', 'wpstorm-notify'),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );
        // Submit Code Button Controls
        $this->add_control(
            'submit_code_button_text',
            [
                'label'   => esc_html__('Button Text', 'wpstorm-notify'),
                'type'    => Controls_Manager::TEXT,
                'default' => esc_html__('Submit Code', 'wpstorm-notify'),
            ]
        );

        $this->add_control(
            'submit_code_button_color',
            [
                'label'     => esc_html__('Button Background Color', 'wpstorm-notify'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .submit-code-button' => 'background-color: {{VALUE}};',
                ],
                'default'   => '#0002cb',
            ]
        );

        $this->add_control(
            'submit_code_button_text_color',
            [
                'label'     => esc_html__('Button Text Color', 'wpstorm-notify'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .submit-code-button' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'submit_code_button_margin',
            [
                'label'      => esc_html__('Margin', 'wpstorm-notify'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'selectors'  => [
                    '{{WRAPPER}} .submit-code-button' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'submit_code_button_padding',
            [
                'label'      => esc_html__('Padding', 'wpstorm-notify'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'selectors'  => [
                    '{{WRAPPER}} .submit-code-button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();

        // Resent Button Section
        $this->start_controls_section(
            'resent_code_button_section',
            [
                'label' => esc_html__('Resent Code Button', 'wpstorm-notify'),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );
        // Resent Code Button Controls
        $this->add_control(
            'resent_code_button_text',
            [
                'label'   => esc_html__('Button Text', 'wpstorm-notify'),
                'type'    => Controls_Manager::TEXT,
                'default' => esc_html__('Resent Code', 'wpstorm-notify'),
            ]
        );

        $this->add_control(
            'resent_code_button_color',
            [
                'label'     => esc_html__('Button Background Color', 'wpstorm-notify'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .resent-code-button' => 'background-color: {{VALUE}};',
                ],
                'default'   => '#0002cb',
            ]
        );

        $this->add_control(
            'resent_code_button_text_color',
            [
                'label'     => esc_html__('Button Text Color', 'wpstorm-notify'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .resent-code-button' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'resent_code_button_margin',
            [
                'label'      => esc_html__('Margin', 'wpstorm-notify'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'selectors'  => [
                    '{{WRAPPER}} .resent-code-button' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'resent_code_button_padding',
            [
                'label'      => esc_html__('Padding', 'wpstorm-notify'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'selectors'  => [
                    '{{WRAPPER}} .resent-code-button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();
    }

    protected function render()
    {
        wp_enqueue_script('elementor-frontend');
        $settings = $this->get_settings_for_display();
        ?>
        <div id="newsletter">
            <h3><?php echo esc_html($settings['form_title']); ?></h3>
            <form id="newsletter_form">
                <input id="newsletter_phonebook_id" type="hidden" name="phonebook_id" value="<?php echo esc_attr($settings['phonebook']); ?>">
                <input id="newsletter_send_verify_code" type="hidden" name="send_verify_code" value="<?php echo esc_attr($settings['send_verify_code']); ?>">
                <input id="newsletter_verify_code_pattern" type="hidden" name="verify_code_pattern" value="<?php echo esc_attr($settings['verify_code_pattern']); ?>">
                <input id="newsletter_send_welcome_msg" type="hidden" name="send_welcome_msg" value="<?php echo esc_attr($settings['send_welcome_msg']); ?>">
                <input id="newsletter_welcome_msg_pattern" type="hidden" name="welcome_msg_pattern" value="<?php echo esc_attr($settings['welcome_msg_pattern']); ?>">

                <div class="newsletter_input a">
                    <input id="newsletter_name" type="text" class="newsletter_text" placeholder="<?php echo esc_attr($settings['name_placeholder']); ?>">
                </div>
                <div class="newsletter_input a">
                    <input id="newsletter_mobile" type="text" class="newsletter_text" placeholder="<?php echo esc_attr($settings['phone_placeholder']); ?>">
                </div>
                <div class="newsletter_input b" style="display: none;">
                    <input id="newsletter_verify_code" type="text" class="newsletter_text" placeholder="<?php echo esc_attr__('Verification code', 'wpstorm-notify'); ?>">
                </div>

            </form>
            <div id="newsletter_message" style="display: none;">
            </div>
            <div class="newsletter_submit">
                <button id="newsletter_submit_button" class="submit-button" style="color:<?php echo esc_attr($settings['submit_button_text_color']); ?>;background-color:<?php echo esc_attr($settings['submit_button_color']); ?>;margin:<?php echo esc_attr($settings['submit_button_margin']); ?>;padding:<?php echo esc_attr($settings['submit_button_padding']); ?>;">
                    <span><?php echo esc_html($settings['submit_button_text']); ?></span>
                </button>
            </div>
            <div id="newsletter_completion" class="newsletter_submit" style="display: none;">
                <button id="newsletter_submit_code" class="submit-code-button" style="color:<?php echo esc_attr($settings['submit_code_button_text_color']); ?>;background-color:<?php echo esc_attr($settings['submit_code_button_color']); ?>;margin:<?php echo esc_attr($settings['submit_code_button_margin']); ?>;padding:<?php echo esc_attr($settings['submit_code_button_padding']); ?>;">
                    <span><?php echo esc_html($settings['submit_code_button_text']); ?></span>
                </button>
                <button id="newsletter_resend_code" class="resent-code-button" style="color:<?php echo esc_attr($settings['resent_code_button_text_color']); ?>;background-color:<?php echo esc_attr($settings['resent_code_button_color']); ?>;margin:<?php echo esc_attr($settings['resent_code_button_margin']); ?>;padding:<?php echo esc_attr($settings['resent_code_button_padding']); ?>;">
                    <span><?php echo esc_html($settings['resent_code_button_text']); ?></span>
                </button>
            </div>
        </div>

        <script>
            jQuery(document).ready(function($) {
                'use strict'
                let submit_div = $('.newsletter_submit')
                let submit_button = $('#newsletter_submit_button')
                let submit_code = $('#newsletter_submit_code')
                let resend_code = $('#newsletter_resend_code')
                let newsletter_completion_div = $('#newsletter_completion')
                let name = $('#newsletter_name')
                let mobile = $('#newsletter_mobile')
                let phonebook_id = $('#newsletter_phonebook_id')
                let send_verify_code = $('#newsletter_send_verify_code')
                let verify_code_pattern = $('#newsletter_verify_code_pattern')
                let send_welcome_msg = $('#newsletter_send_welcome_msg')
                let welcome_msg_pattern = $('#newsletter_welcome_msg_pattern')
                let verify_code = $('#newsletter_verify_code')
                let newsletter_message = $('#newsletter_message')

                let has_error = false
                submit_button.click(function() {
                    has_error = false
                    name.removeClass('error')
                    mobile.removeClass('error')
                    if (name.val() === '') {
                        has_error = true
                        name.addClass('error')
                    }
                    if (mobile.val().length < 10) {
                        has_error = true
                        mobile.addClass('error')
                    }
                    if (has_error) {
                        return
                    }
                    let data = {
                        action: 'newsletter_send_verification_code',
                        mobile: mobile.val(),
                        name: name.val(),
                        phonebook_id: phonebook_id.val(),
                        send_verify_code: send_verify_code.val(),
                        verify_code_pattern: verify_code_pattern.val(),
                        send_welcome_msg: send_welcome_msg.val(),
                        welcome_msg_pattern: welcome_msg_pattern.val(),
                    }
                    submit_button.addClass('button--loading')
                    submit_button.prop('disabled', true)
                    $.post('<?php echo admin_url('admin-ajax.php'); ?>', data, function(response) {
                        submit_button.removeClass('button--loading')
                        if (response.success) {
                            if (send_verify_code.val() !== 'yes') {
                                newsletter_message.removeClass('success error')
                                newsletter_message.hide()
                                newsletter_message.empty()
                                newsletter_message.addClass('success')
                                newsletter_message.append('ثبت نام با موفقیت انجام شد')
                                newsletter_message.show()
                            } else {
                                submit_div.hide()
                                $('.newsletter_input.a').hide()
                                newsletter_completion_div.show()
                                $('.newsletter_input.b').show()
                                let seconds = 90
                                let interval
                                resend_code.prop('disabled', true)
                                interval = setInterval(function() {
                                    resend_code
                                        .find('span')
                                        .html('ارسال مجدد کد' + ' (' + seconds + ')')
                                    if (seconds === 0) {
                                        resend_code.find('span').html('ارسال مجدد کد')
                                        resend_code.prop('disabled', false)
                                        clearInterval(interval)
                                    }
                                    seconds--
                                }, 1000)
                            }
                        } else {
                            newsletter_message.addClass('error')
                            newsletter_message.append('شما عضو خبرنامه هستید')
                            newsletter_message.show()
                        }
                    })
                })

                resend_code.click(function() {
                    submit_button.click()
                })

                submit_code.click(function() {
                    has_error = false
                    verify_code.removeClass('error')
                    if (verify_code.val() === '' || verify_code.val().length !== 4) {
                        has_error = true
                        verify_code.addClass('error')
                    }
                    if (has_error) {
                        return
                    }
                    let data = {
                        action: 'add_phone_to_newsletter',
                        code: verify_code.val(),
                        name: name.val(),
                        mobile: mobile.val(),
                        phonebook_id: phonebook_id.val(),
                        welcome_msg_pattern: welcome_msg_pattern.val(),
                    }
                    submit_code.addClass('button--loading')
                    submit_code.prop('disabled', true)
                    $.post('<?php echo admin_url('admin-ajax.php'); ?>', data, function(response) {
                        submit_code.removeClass('button--loading')
                        submit_code.prop('disabled', false)
                        newsletter_message.removeClass('success error')
                        newsletter_message.hide()
                        newsletter_message.empty()
                        if (response.success) {
                            newsletter_message.addClass('success')
                            newsletter_message.append('ثبت نام با موفقیت انجام شد')
                            newsletter_message.show()
                            newsletter_completion_div.hide()
                        } else {
                            newsletter_message.addClass('error')
                            newsletter_message.append('کد وارد شده صحیح نیست')
                            newsletter_message.show()
                        }
                    })
                })
            })
        </script>
        <?php

    }
}
