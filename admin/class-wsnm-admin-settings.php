<?php

/**
 * WSNM_Woo_Stock_Notify_Me_Admin_Settings
 */
class WSNM_Woo_Stock_Notify_Me_Admin_Settings
{

    /**
     * helper
     *
     * @var WSNM_HELPER
     */
    public $helper;

    /**
     * __construct
     *
     * @param  mixed $helper
     * @return void
     */

    function __construct(WSNM_HELPER $helper)
    {
        $this->helper = $helper;
    }

    /**
     * enqueue_color_picker
     *
     * @param  mixed $hook_suffix
     * @return void
     */
    public function enqueue_color_picker($hook_suffix)
    {
        wp_enqueue_style('wp-color-picker');
    }

    /**
     * settings_menu
     *
     * @return void
     */
    public function settings_menu()
    {
        add_submenu_page(
            'edit.php?post_type=' . $this->helper->getCustomPostType(),
            __('Notify Me - Settings', 'back-in-stock-notifications-for-woocommerce'),
            __('Settings', 'back-in-stock-notifications-for-woocommerce'),
            'manage_options',
            'wsnm-settings',
            array($this, 'settings_page_callback')
        );

        global $submenu;
        unset($submenu['edit.php?post_type=wsnm_notify_me'][10]);
    }

    /**
     * tinyMCE_theme_setup
     *
     * @return void
     */
    public function tinyMCE_theme_setup()
    {
        add_editor_style(array(plugins_url('css/editor-style.css', __FILE__)));
    }

    /**
     * settings_page_callback
     *
     * @return void
     */
    public function settings_page_callback()
    {
        if (!current_user_can('manage_options')) return;
        $active_tab = isset($_GET['tab']) ? sanitize_text_field($_GET['tab']) : 'wsnm-settings-general';
        $tabs = array(
            'wsnm-settings-general' => array(
                'title' => __('General', 'back-in-stock-notifications-for-woocommerce'),
                'url' => admin_url('edit.php?post_type=wsnm_notify_me&page=wsnm-settings'),
                'callback' => 'wsnm_settings_general_content'
            ),
            'wsnm-settings-subscription-form' => array(
                'title' => __('Subscription Form', 'back-in-stock-notifications-for-woocommerce'),
                'url' => admin_url('edit.php?post_type=wsnm_notify_me&page=wsnm-settings&tab=wsnm-settings-subscription-form'),
                'callback' => 'wsnm_settings_subscription_form_content'
            ),
            'wsnm-settings-email-templates' => array(
                'title' => __('Email Templates', 'back-in-stock-notifications-for-woocommerce'),
                'url' => admin_url('edit.php?post_type=wsnm_notify_me&page=wsnm-settings&tab=wsnm-settings-email-templates'),
                'callback' => 'wsnm_settings_email_templates_content'
            ),
            'wsnm-documentation' => array(
                'title' => __('Documentation', 'back-in-stock-notifications-for-woocommerce'),
                'url' => 'https://www.getinnovation.dev/wordpres-plugins/woocommerce-stock-notify-me/documentation/'
            )
        );
        $callback = $tabs[$active_tab]['callback'];
        require_once WSNM_PATH . 'admin/parts/settings-page-header.php';
        $this->$callback();
        require_once WSNM_PATH . 'admin/parts/settings-page-footer.php';
    }

    /**
     * wsnm_settings_general_content
     *
     * @return void
     */
    public function wsnm_settings_general_content(): void
    {
        $colors = $this->helper->get_button_colors();
        $mode = $this->helper->get_mode();
        require_once WSNM_PATH . 'admin/parts/page-general-settings.php';
    }

    /**
     * wsnm_settings_email_templates_content
     *
     * @return void
     */
    public function wsnm_settings_email_templates_content(): void
    {
        $subscribe_confirmation_email = $this->helper->getConfirmationEmailBody();
        $subscribe_confirmation_email_subject = $this->helper->getConfirmationEmailSubject();

        $subscribe_notification_email =  $this->helper->getNotificationEmailBody();
        $subscribe_notification_email_subject = $this->helper->getBackInStockNotificationEmailSubject();

        $subscribe_confirmation_email_status = get_option('wsnm_subscribe_confirmation_status');

        $tinymce_settings =  array(
            'toolbar1' => 'fontselect,fontsizeselect,separator,bold,italic,underline,separator,alignleft,aligncenter,alignright,separator,forecolor,backcolor,separator,textcolor,link,undo,redo,separator,tags',
            'font_formats' => 'Sans Serif=arial,helvetica,sans-serif;Serif=times new roman, serif;Fixed Width=monospace, monospace;Wide=arial black, sans-serif;Narrow=arial narrow, sans-serif;Comic Sans MS=comic sans ms, sans-serif;Garamond=garamond, serif;Georgia=georgia, serif;Tahoma=tahoma, sans-serif;Trebuchet MS=trebuchet ms, sans-serif;Verdana=verdana, sans-serif;',
            'inline_styles' => false,
            'statusbar' => true
        );
        require_once WSNM_PATH . 'admin/parts/page-email-templates.php';
    }

    /**
     * wsnm_settings_subscription_form_content
     *
     * @return void
     */
    public function wsnm_settings_subscription_form_content(): void
    {
        $form_first_last_name = $this->helper->get_first_last_name_status();
        $recaptcha_status = false;
        if (get_option('wsnm_form_recaptcha_status') == 'enabled') $recaptcha_status = true;
        $recaptcha_site_key = get_option('wsnm_recaptcha_site_key');
        $recaptcha_secret_key = get_option('wsnm_recaptcha_secret_key');
        $before_form_text = $this->helper->get_pre_form_text();
        $after_form_text =  $this->helper->get_after_form_text();
        $button_text =  $this->helper->get_button_text();
        $modal_title =  $this->helper->get_modal_title();
        require_once WSNM_PATH . 'admin/parts/page-subscription-form.php';
    }

    /**
     * save_settings
     *
     * @return void
     */
    public function save_settings()
    {
        //General Settings
        if (isset($_POST['nonce-wsnm-settings'])  && wp_verify_nonce($_POST['nonce-wsnm-settings'], 'wsnm-settings-save')) {
            //Mode 
            $mode = sanitize_text_field($_POST['wsnm_mode']);
            update_option('wsnm_mode', $mode);

            //Button Style
            $background_color = sanitize_hex_color($_POST['wsnm_btn_background_color']);
            $text_color = sanitize_hex_color($_POST['wsnm_btn_text_color']);
            update_option('wsnm_btn_background_color', $background_color);
            update_option('wsnm_btn_text_color', $text_color);
        }

        //Email Template Settings
        if (isset($_POST['nonce-wsnm-email-settings'])  && wp_verify_nonce($_POST['nonce-wsnm-email-settings'], 'wsnm-email-settings-save')) {
            if(!isset($_POST['wsnm_reset_email'])){
                $confirmation_email = wp_kses(stripslashes_deep($_POST['wsnm_subscribe_confirmation']), wp_kses_allowed_html('post'));
                $confirmation_email_subject = sanitize_text_field($_POST['wsnm_subscribe_confirmation_subject']);
                update_option('wsnm_subscribe_confirmation_email', $confirmation_email);
                update_option('wsnm_subscribe_confirmation_email_subject', $confirmation_email_subject);
                if (isset($_POST['wsnm_subscribe_confirmation_status'])) {
                    update_option('wsnm_subscribe_confirmation_status', 'enabled');
                } else {
                    update_option('wsnm_subscribe_confirmation_status', 'disabled');
                }
                $notification_email = wp_kses(stripslashes_deep($_POST['wsnm_back_in_stock_notification']), wp_kses_allowed_html('post'));
                $notification_email_subject = sanitize_text_field($_POST['wsnm_back_in_stock_notification_subject']);
                update_option('wsnm_subscribe_notification_email', $notification_email);
                update_option('wsnm_subscribe_notification_email_subject', $notification_email_subject);
            }else{
                delete_option('wsnm_subscribe_confirmation_email');
                delete_option('wsnm_subscribe_confirmation_email_subject');
                delete_option('wsnm_subscribe_notification_email');
                delete_option('wsnm_subscribe_notification_email_subject');
                delete_option('wsnm_subscribe_confirmation_status');
            }
        }

        //Form Subscription Settings
        if (isset($_POST['nonce-wsnm-subscription-form-settings'])  && wp_verify_nonce($_POST['nonce-wsnm-subscription-form-settings'], 'wsnm-subscription-form-settings-save')) {
            //First & Last Name
            if (isset($_POST['wsnm_form_first_last_name'])) {
                update_option('wsnm_form_first_last_name', 'enabled');
            } else {
                update_option('wsnm_form_first_last_name', 'disabled');
            }
            //reCAPTCHA
            if (isset($_POST['wsnm_form_recaptcha_status'])) {
                update_option('wsnm_form_recaptcha_status', 'enabled');
            } else {
                update_option('wsnm_form_recaptcha_status', 'disabled');
            }
            $recaptcha_site_key = sanitize_text_field($_POST['wsnm_recaptcha_site_key']);
            $recaptcha_secret_key = sanitize_text_field($_POST['wsnm_recaptcha_secret_key']);
            update_option('wsnm_recaptcha_site_key', $recaptcha_site_key);
            update_option('wsnm_recaptcha_secret_key', $recaptcha_secret_key);

            //Button Text
            $button_text = sanitize_text_field($_POST['wsnm_button_text']);
            update_option('wsnm_btn_text', $button_text);

            //Modal Title
            $modal_title = sanitize_text_field($_POST['wsnm_modal_title']);
            update_option('wsnm_modal_title', $modal_title);

            //Before & Aftet text 
            $before_text = wp_kses(stripslashes_deep($_POST['wsnm_pre_form_content']), wp_kses_allowed_html('post'));
            $after_text = wp_kses(stripslashes_deep($_POST['wsnm_after_form_content']), wp_kses_allowed_html('post'));
            update_option('wsnm_pre_form_content', stripslashes_deep($before_text));
            update_option('wsnm_after_form_content', stripslashes_deep($after_text));
        }
    }

    /**
     * save_settings_notice
     *
     * @return void
     */
    public function save_settings_notice()
    {
        if (
            (isset($_POST['nonce-wsnm-settings'])  && wp_verify_nonce($_POST['nonce-wsnm-settings'], 'wsnm-settings-save'))
            ||
            (isset($_POST['nonce-wsnm-email-settings'])  && wp_verify_nonce($_POST['nonce-wsnm-email-settings'], 'wsnm-email-settings-save'))
            ||
            (isset($_POST['nonce-wsnm-subscription-form-settings'])  && wp_verify_nonce($_POST['nonce-wsnm-subscription-form-settings'], 'wsnm-subscription-form-settings-save'))
        ) {
            echo sprintf(
                '<div class="notice notice-success is-dismissible"><p><strong>%s:</strong> %s</p></div>',
                WSNM_NAME,
                __('Settings successfully updated', 'back-in-stock-notifications-for-woocommerce')
            );
        }
    }
}
