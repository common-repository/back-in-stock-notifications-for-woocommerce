<?php

/**
 * WSNM_HELPER
 */
class WSNM_HELPER
{

    /**
     * custom_post_type
     *
     * @var string
     */
    public $custom_post_type = 'wsnm_notify_me';

    public $db_queries;
    public $scheduler;

    /**
     * __construct
     *
     * @return void
     */
    function __construct()
    {
        $this->db_queries = new WSNM_DB_Queries($this);
        $this->scheduler = new WSNM_Scheduler($this);
        $this->custom_post_type = 'wsnm_notify_me';
    }



    /**
     * getNotificationProduct
     *
     * @param  int $notification_id
     * @return mixed
     */
    public function getNotificationProduct(int $notification_id)
    {
        $product = false;
        $product_id = get_post_meta($notification_id, 'wsnm_product_id', true);
        if($product_id) $product = wc_get_product($product_id);
        return $product;
    }

    /**
     * is_notification_a_variation
     *
     * @param  int $notification_id
     * @return mixed
     */
    public function is_notification_a_variation(int $notification_id)
    {
        $variant_id = get_post_meta($notification_id, 'wsnm_variation_id', true);
        if($variant_id) return true;
        return false;
    }

    /**
     * get_notification_variation_value
     *
     * @param  int $notification_id
     * @return mixed
     */
    public function get_notification_variation_value(int $notification_id)
    {
        return get_post_meta($notification_id, 'wsnm_variation_id', true);
    }

    /**
     * get_notification_product_value
     *
     * @param  int $notification_id
     * @return mixed
     */
    public function get_notification_product_value(int $notification_id)
    {
        return get_post_meta($notification_id, 'wsnm_product_id', true);
    }

    /**
     * getNotificationVariation
     *
     * @param  int $notification_id
     * @return mixed
     */
    public function getNotificationVariation(int $notification_id)
    {
        $product = false;
        $variant_id = get_post_meta($notification_id, 'wsnm_variation_id', true);
        if($variant_id) $product = wc_get_product($variant_id);
        return $product;
    }

    /**
     * getNotificationSelectedProduct
     *
     * @param  int $notification_id
     * @return mixed
     */
    public function getNotificationSelectedProduct(int $notification_id)
    {
        $product = false;
        $product_id = get_post_meta($notification_id, 'wsnm_product_id', true);
        $variant_id = get_post_meta($notification_id, 'wsnm_variation_id', true);
        if($product_id) $product = wc_get_product($product_id);
        if($variant_id) $product = wc_get_product($variant_id);
        return $product;
    }

    public function getNotificationsBySimpleProduct($product)
    {
        $args = array(
            array('key' => 'wsnm_product_id', 'value' => $product->get_id()),
            array('key' => 'wsnm_status', 'value' => 'pending')
        );
        return new WP_Query(array('post_type' => 'wsnm_notify_me', 'meta_query' => array($args)));
    }

    public function getNotificationsByVariation($product, $variation){
        $product_id = $product->get_id();
        $variation_id = $variation->get_id();
        $args = array(
            array('key' => 'wsnm_product_id', 'value' => $product_id),
            array('key' => 'wsnm_variation_id', 'value' => $variation_id),
            array('key' => 'wsnm_status', 'value' => 'pending')
        );
        return new WP_Query(array('post_type' => 'wsnm_notify_me', 'meta_query' => array($args)));
    }

    /**
     * getCustomPostType
     *
     * @return string
     */
    public function getCustomPostType(): string
    {
        return $this->custom_post_type;
    }


    /**
     * convert_merge_tags
     *
     * @param  mixed $content
     * @param  object $post
     * @return string
     */
    public function convert_merge_tags(string $content, object $post): string
    {
        $first_name = get_post_meta($post->ID, 'wsnm_first_name', true);
        $last_name = get_post_meta($post->ID, 'wsnm_last_name', true);
        $email = get_post_meta($post->ID, 'wsnm_email', true);
        $merge_tags = array(
            array('old' => '[wsnm-first-name]',        'new' => $first_name),
            array('old' => '[wsnm-last-name]',         'new' => $last_name),
            array('old' => '[wsnm-email]',             'new' => $email)
        );

        foreach ($merge_tags as $tag) {
            if (strpos($content, $tag['old']) !== false) {
                $content = str_replace($tag['old'], $tag['new'], $content);
            }
        }
        return $content;
    }

    /**
     * convert_product_merge_tags
     *
     * @param  mixed $content
     * @param  int $product_id
     * @return string
     */
    public function convert_product_merge_tags(string $content, int $product_id): string
    {
        $product = wc_get_product($product_id);
        $quantity = $product->get_stock_quantity();
        if(is_null($quantity)) $quantity = 'unlimited';
        $merge_tags = array(
            array('old' => '[wsnm-product-title]',     'new' => $product->get_name()),
            array('old' => '[wsnm-product-price]',     'new' => $product->get_price_html()),
            array('old' => '[wsnm-product-url]',       'new' => get_permalink($product->get_id())),
            array('old' => '[wsnm-product-quantity]',  'new' => $quantity)
        );

        foreach ($merge_tags as $tag) {
            if (strpos($content, $tag['old']) !== false) {
                $content = str_replace($tag['old'], $tag['new'], $content);
            }
        }
        return $content;
    }

    /**
     * getConfirmationEmailSubject
     *
     * @return string
     */
    public function getConfirmationEmailSubject(): string
    {
        $subject = get_option('wsnm_subscribe_confirmation_email_subject');
        if ($subject) return $subject;
        return __('You subscribed to [wsnm-product-title]', 'back-in-stock-notifications-for-woocommerce');
    }

    /**
     * getBackInStockNotificationEmailSubject
     *
     * @return string
     */
    public function getBackInStockNotificationEmailSubject(): string
    {
        $subject = get_option('wsnm_subscribe_notification_email_subject');
        if ($subject) return $subject;
        return __('The product [wsnm-product-title] is back in stock', 'back-in-stock-notifications-for-woocommerce');
    }

    /**
     * getConfirmationEmailBody
     *
     * @return string
     */
    public function getConfirmationEmailBody(): string
    {
        $email_body = get_option('wsnm_subscribe_confirmation_email');
        if ($email_body) return $email_body;
        $site_name = get_bloginfo('name');
        ob_start();
        require_once WSNM_PATH . 'admin/parts/emails/default-confirmation-email.php';
        return ob_get_clean();
    }

    /**
     * getNotificationEmailBody
     *
     * @return string
     */
    public function getNotificationEmailBody(): string
    {
        $email_body = get_option('wsnm_subscribe_notification_email');
        if ($email_body) return $email_body;
        $site_name = get_bloginfo('name');
        ob_start();
        require_once WSNM_PATH . 'admin/parts/emails/default-notification-email.php';
        return ob_get_clean();
    }

    /**
     * the_confirmation_email
     *
     * @param  int $post_id
     * @return void
     */
    public function the_confirmation_email(int $post_id): void
    {
        $post = get_post($post_id);
        $product = $this->getNotificationSelectedProduct($post->ID);
        $email = get_post_meta($post->ID, 'wsnm_email', true);

        $body = nl2br($this->getConfirmationEmailBody());
        $body = $this->convert_merge_tags($body, $post);
        $body = $this->convert_product_merge_tags($body, $product->get_id());

        $subject = $this->getConfirmationEmailSubject();
        $subject = $this->convert_merge_tags($subject, $post);
        $subject = $this->convert_product_merge_tags($subject, $product->get_id());
        $headers = array('Content-Type: text/html; charset=UTF-8');
        wp_mail($email, $subject, $body, $headers);
    }

    /**
     * the_notification_email
     *
     * @param  int $post_id
     */
    public function the_notification_email(int $post_id)
    {
        $post = get_post($post_id);
        $product = $this->getNotificationSelectedProduct($post->ID);
        $email = get_post_meta($post->ID, 'wsnm_email', true);

        $body = nl2br($this->getNotificationEmailBody());
        $body = $this->convert_merge_tags($body, $post);
        $body = $this->convert_product_merge_tags($body, $product->get_id());

        $subject = $this->getBackInStockNotificationEmailSubject();
        $subject = $this->convert_merge_tags($subject, $post);
        $subject = $this->convert_product_merge_tags($subject, $product->get_id());
        $headers = array('Content-Type: text/html; charset=UTF-8');
        return wp_mail($email, $subject, $body, $headers);
    }

    /**
     * get_notification_status
     *
     * @param  int $notification_id
     * @return array
     */
    public function get_notification_status(int $notification_id, $product): array
    {
        //When the product has been deleted
        $productPost = $this->getNotificationSelectedProduct($notification_id);
        if (!$productPost) return array('key' => 'unavailable', 'message' => __('Unavailable', 'back-in-stock-notifications-for-woocommerce'));

        $status = get_post_meta($notification_id, 'wsnm_status', true);
        $pause_status = get_post_meta($product->get_id(), 'wsnm_product_pause', true);
        $mode = $this->get_mode();
        $action_in_progress = $this->db_queries->is_action_in_progress($product->get_id());

        //Sent
        if ($status == 'sent'){
            return array('key' => 'sent', 'message' => __('Sent', 'back-in-stock-notifications-for-woocommerce'));
        }   

        //Paused
        if ($mode == 'automatically' && $pause_status == 'yes'){
            return array('key' => 'paused', 'message' => __('Paused', 'back-in-stock-notifications-for-woocommerce'));
        }

        if ($mode == 'manually' && !$action_in_progress){
            return array('key' => 'paused', 'message' => __('Paused', 'back-in-stock-notifications-for-woocommerce'));
        }
        
        //Pending
        if ($status == 'pending'){
            return array('key' => 'waiting', 'message' => __('Waiting', 'back-in-stock-notifications-for-woocommerce'));
        }
    }

    public function get_notification_status_html(int $notification_id)
    {

        $product = $this->getNotificationProduct($notification_id);
        $status = $this->get_notification_status($notification_id, $product);
        $mode = $this->get_mode();

        $action = '';
        if ($status['key'] == 'paused') {
            if($mode == 'automatically'){
                $action = sprintf('<p>Unpause the product <a href="%s&wsnm_product_tab=wsnm-options_tab&wsnm_product_tab_content=wsnm_options_data" target="_blank">here</a></p>', get_edit_post_link($this->get_notification_product_value($notification_id)));
            }
            if($mode == 'manually'){
                $action = sprintf('<p>Enable notification here <a href="%s&wsnm_product_tab=wsnm-options_tab&wsnm_product_tab_content=wsnm_options_data" target="_blank">here</a></p>', get_edit_post_link($this->get_notification_product_value($notification_id)));
            }
        }
        if ($status['key'] == 'waiting') {
            if($this->is_notification_a_variation($notification_id)){
                $action = sprintf('<p>A notification will be sent when the product will be back in stock, manage stock <a href="%s&wsnm_product_tab=variations_tab&wsnm_product_tab_content=variable_product_options" target="_blank">here</a></p>', get_edit_post_link($this->get_notification_product_value($notification_id)));
            }else{
                $action = sprintf('<p>A notification will be sent when the product will be back in stock, manage stock <a href="%s&wsnm_product_tab=inventory_tab&wsnm_product_tab_content=inventory_product_data" target="_blank">here</a></p>', get_edit_post_link($this->get_notification_product_value($notification_id)));
            }
        }

        if($status['key'] == 'unavailable'){
            if($this->is_notification_a_variation($notification_id)){
                $id = $this->get_notification_variation_value($notification_id);
            }else{
                $id = $this->get_notification_product_value($notification_id);
            }
            $action = sprintf('<p>The product #%s can\'t be found! Most likely it has been removed.</p>', $id);
        }

        return sprintf('%s <span class="wsnm-product-list-status wsnm-tooltip wsnm_product_%s"><span class="wsnm-tooltip-text"><p><strong>Status:</strong> %1$s %s</p></span></span>', $status['message'], $status['key'], $action);
    }

    /**
     * is_product_paused
     *
     * @param  int $product_id
     * @return bool
     */
    public function is_product_paused(int $product_id): bool
    {
        $paused = get_post_meta($product_id, 'wsnm_product_pause', true);
        if ($paused == 'yes') return true;
        return false;
    }

    /**
     * get_button_colors
     *
     * @return array
     */
    public function get_button_colors(): array
    {
        $colors = array(
            'background' => '#46B450',
            'text' => '#ffffff'
        );
        $background_color = get_option('wsnm_btn_background_color');
        if ($background_color) $colors['background'] = $background_color;
        $text_color = get_option('wsnm_btn_text_color');
        if ($text_color) $colors['text'] = $text_color;
        return $colors;
    }

    /**
     * get_button_text
     *
     * @return string
     */
    public function get_button_text(): string
    {
        $text = __('Subscribe', 'back-in-stock-notifications-for-woocommerce');
        $text_modified = get_option('wsnm_btn_text');
        if ($text_modified) $text = $text_modified;
        return $text;
    }

    /**
     * get_modal_title
     *
     * @return string
     */
    public function get_modal_title(): string
    {
        $title = __('Subscribe', 'back-in-stock-notifications-for-woocommerce');
        $title_modified = get_option('wsnm_modal_title');
        if ($title_modified) $title = $title_modified;
        return $title;
    }

    /**
     * get_pre_form_text
     *
     * @return string
     */
    public function get_pre_form_text(): string
    {
        $email_body = get_option('wsnm_pre_form_content');
        if ($email_body) return $email_body;
        ob_start();
        require_once WSNM_PATH . 'public/parts/pre-form-content.php';
        return ob_get_clean();
    }

    /**
     * get_after_form_text
     *
     * @return string
     */
    public function get_after_form_text(): string
    {
        $email_body = get_option('wsnm_after_form_content');
        if ($email_body) return $email_body;
        ob_start();
        require_once WSNM_PATH . 'public/parts/after-form-content.php';
        return ob_get_clean();
    }

    /**
     * get_first_last_name_status
     *
     * @return bool
     */
    public function get_first_last_name_status(): bool
    {
        if (get_option('wsnm_form_first_last_name') == 'disabled') return false;
        return true;
    }

    public function get_first_name(int $notification_id)
    {
        $fname = get_post_meta($notification_id, 'wsnm_first_name', true);
        if($fname) return $fname;
        return '';
    }

    public function get_last_name(int $notification_id)
    {
        $lname = get_post_meta($notification_id, 'wsnm_last_name', true);
        if($lname) return $lname;
        return '';
    }

    public function get_email(int $notification_id)
    {
        $email = get_post_meta($notification_id, 'wsnm_email', true);
        if($email) return $email;
        return '';
    }
    
    /**
     * is_recaptcha_enabled
     *
     * @return bool
     */
    public function is_recaptcha_enabled(): bool {
        if (get_option('wsnm_form_recaptcha_status') == 'enabled') return true;
        return false;
    }

    public function get_mode()
    {
        if (get_option('wsnm_mode') == 'automatically') return 'automatically';
        if (get_option('wsnm_mode') == 'manually') return 'manually';
        //Return 'manually' by default
        return 'manually';
    }
}
