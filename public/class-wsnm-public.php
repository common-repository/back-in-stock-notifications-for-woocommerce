<?php

/**
 * WSNM_Woo_Stock_Notify_Me_Public
 */
class WSNM_Woo_Stock_Notify_Me_Public
{

    /**
     * helper
     *
     * @var mixed
     */
    public $helper;

    /**
     * __construct
     *
     * @param  mixed $helper
     * @return void
     */
    public function __construct($helper)
    {
        $this->helper = $helper;
    }

    /**
     * enqueue_resources
     *
     * @return void
     */
    public function enqueue_resources()
    {
        wp_enqueue_style(WSNM_DOMAIN, WSNM_URL . 'public/css/wsnm.css', array(), filemtime(WSNM_PATH . 'public/css/wsnm.css'));
        wp_enqueue_script(WSNM_DOMAIN, WSNM_URL . 'public/js/wsnm.js', array('jquery'), filemtime(WSNM_PATH . 'public/js/wsnm.js'));
        wp_localize_script(WSNM_DOMAIN, 'ajax_object', array('ajax_url' => admin_url('admin-ajax.php')));
    }
    
    /**
     * recaptcha_resources
     *
     * @return void
     */
    public function recaptcha_resources(){
        if($this->helper->is_recaptcha_enabled()){
            echo '<script src="https://www.google.com/recaptcha/api.js" async defer></script>';
        }
    }

    /**
     * ajaxReturn
     *
     * @param  mixed $status
     * @param  mixed $message
     * @param  mixed $code
     * @return void
     */
    public function ajaxReturn($status, $message, $code)
    {
        $data['status'] = $status;
        $data['message'] = $message;
        $data['code'] = $code;
        wp_send_json($data);
    }

    /**
     * save_request
     *
     * @return void
     */
    public function save_request()
    {
        //Check Nonce
        if (!isset($_POST['nonce'])){
            $this->ajaxReturn(false, __('Something went wrong.', 'back-in-stock-notifications-for-woocommerce'), 'nonce-missing');
        }
        if (!wp_verify_nonce($_POST['nonce'], 'wsnm_add_request')) $this->ajaxReturn(false, __('Something went wrong.', 'back-in-stock-notifications-for-woocommerce'), 'nonce-wrong');

        //Check for the first and last name
        if($this->helper->get_first_last_name_status()){
            if(isset($_POST['first_name']) && isset($_POST['last_name'])){
                $f_name = sanitize_text_field($_POST['first_name']);
                $l_name = sanitize_text_field($_POST['last_name']);
                if(strlen($f_name) > 0 && strlen($l_name) > 0){
                    $meta['wsnm_first_name'] = $f_name;
                    $meta['wsnm_last_name'] = $l_name;
                }else{
                    $this->ajaxReturn(false, __('Please specify the first and last name', 'back-in-stock-notifications-for-woocommerce'), 'name-invalid');
                }
            }else{
                $this->ajaxReturn(false, __('Please specify the first and last name', 'back-in-stock-notifications-for-woocommerce'), 'name-invalid'); 
            }
        }

        //Check Email
        if(!isset($_POST['email'])){
            $this->ajaxReturn(false, __('Please specify an email.', 'back-in-stock-notifications-for-woocommerce'), 'email-invalid');
        }
        $email = sanitize_email($_POST['email']);
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)){
            $this->ajaxReturn(false, __('Please specify a valid email.', 'back-in-stock-notifications-for-woocommerce'), 'email-invalid');
        }
        $meta['wsnm_email'] = $email;

        //Check reCaptcha, if is set
        if($this->helper->is_recaptcha_enabled()){
            if(get_option( 'wsnm_recaptcha_site_key' ) && get_option( 'wsnm_recaptcha_secret_key' )){
                if(!isset($_POST['recaptcha'])){
                    $this->ajaxReturn(false, 'reCaptcha - Please check \'I am not a robot\'', 'recaptcha-issue');
                }
                $url = sprintf( 
                    "https://www.google.com/recaptcha/api/siteverify?secret=%s&response=%s&remoteip=%s", 
                    urlencode(get_option( 'wsnm_recaptcha_secret_key' )), 
                    urlencode(sanitize_text_field($_POST['recaptcha'])), 
                    urlencode($_SERVER['REMOTE_ADDR'])
                );
                $result = json_decode(file_get_contents($url), TRUE);
                if( $result['success'] != 1 ){
                    $this->ajaxReturn(false, 'reCaptcha - Please check \'I am not a robot\'', 'recaptcha-issue');
                }
            }else{
                $this->ajaxReturn(false, __('Please configure reCaptcha', 'back-in-stock-notifications-for-woocommerce'), 'recaptcha-configure');
            }
        }

        //Check for product (type - simple)
        if (!isset($_POST['product'])) $this->ajaxReturn(false, __('Please select a valid product.', 'back-in-stock-notifications-for-woocommerce'), 'product-missing');
        $product = wc_get_product(sanitize_text_field($_POST['product']));
        if (!$product) $this->ajaxReturn(false, __('The product doesn\'t exist.', 'back-in-stock-notifications-for-woocommerce'), 'product-missing');
        if (!$product->is_type('variable') && !$product->is_type('simple')) {
            $this->ajaxReturn(false, __('Only Simple and Variable products', 'back-in-stock-notifications-for-woocommerce'), 'product-type-not-rupported');
        }
        $meta['wsnm_product_id'] = $product->get_id();

        //Check for product (type - variable)
        if (isset($_POST['variation']) && $_POST['variation'] != "") {
            $variation_id = sanitize_text_field($_POST['variation']);
            $variation_product = wc_get_product($variation_id);
            if (!$variation_product) {
                $this->ajaxReturn(false, __('Please select a valid variation.', 'back-in-stock-notifications-for-woocommerce'), 'invalid-variation');
            }
            $meta['wsnm_variation_id'] = $variation_id;
        }

        $events_query_meta = array( 
            array('key' => 'wsnm_status', 'value' => 'pending'),
            array('key' => 'wsnm_email', 'value' => $meta['wsnm_email']),
            array('key' => 'wsnm_product_id', 'value' => $meta['wsnm_product_id'])
        );

        if (array_key_exists("wsnm_variation_id", $meta)){
            $events_query_meta[] = array('key' => 'wsnm_variation_id', 'value' => $meta['wsnm_variation_id']);
        };

        $events_query = new WP_Query(
            array(
                'post_type' => 'wsnm_notify_me',
                'posts_per_page' => -1,
                'meta_query' => array($events_query_meta)
            )
        );

        if (!$events_query->have_posts()) {
            $meta['wsnm_status'] = 'pending';
            $post_id = wp_insert_post(
                array(
                    'post_type' => 'wsnm_notify_me',
                    'post_status' => 'publish',
                    'ping_status' => 'closed',
                    'meta_input' => $meta
                )
            );
            if ($post_id) {
                $this->helper->the_confirmation_email($post_id);
                $this->ajaxReturn(true, __('Notification set! Thank you!', 'back-in-stock-notifications-for-woocommerce'), 'success');
            }
            $this->ajaxReturn(false, __('Something went wrong', 'back-in-stock-notifications-for-woocommerce'), 'something-wrong');
        } else {
            $this->ajaxReturn(false, __('You are already subscribed for this product', 'back-in-stock-notifications-for-woocommerce'), 'exists');
        }
    }

    /**
     * prepare_cta
     *
     * @param  mixed $availability
     * @param  mixed $obj
     * @return void
     */
    public function prepare_cta($availability, $obj)
    {
        global $product;
        if (empty($product)) return $availability;
        
        if(is_a( $obj, 'WC_Product_Variation' ) || is_a( $obj, 'WC_Product_Simple' )){
            $isPaused = $this->helper->is_product_paused($product->get_id());
            if (!$obj->is_in_stock() && !$isPaused) {
                $variationtext = '';
                if(is_a( $obj, 'WC_Product_Variation' )){
                    $variationtext = sprintf('data-variation="%s"', $obj->get_id());
                }
                $nonce = wp_create_nonce('wsnm-popup-form');
                $colors = $this->helper->get_button_colors();
                $availability['availability'] .= sprintf('<div id="wsnm-cta" class="wsnm-cta" %s data-product="%s" data-nonce="%s" style="background-color:%s; color:%s;">%s</div>', $variationtext, $product->get_id(), $nonce, $colors['background'], $colors['text'], apply_filters('wsnm-text-cta', __('Subscribe', 'back-in-stock-notifications-for-woocommerce')));
            }
        }

        return $availability;
    }

    /**
     * generate_popup
     *
     * @return void
     */
    public function generate_popup()
    {
        $data = ['error' => false];
        if (!isset($_POST['nonce']) || !isset($_POST['product'])) $data['error'] = true;
        if (!wp_verify_nonce($_POST['nonce'], 'wsnm-popup-form')) $data['error'] = true;

        if ($data['error']) {
            wp_send_json($data);
        }
        if(!empty($_POST['variation'])){
            $product = wc_get_product(sanitize_text_field($_POST['variation']));
        }else{
            $product = wc_get_product(sanitize_text_field($_POST['product']));
        }
        
        $before_form_text = $this->helper->get_pre_form_text();
        $before_form_text = $this->helper->convert_product_merge_tags($before_form_text, $product->get_id());
        $after_form_text =  $this->helper->get_after_form_text();
        $after_form_text =  $this->helper->convert_product_merge_tags($after_form_text, $product->get_id());
        $name_status = $this->helper->get_first_last_name_status();
        $colors = $this->helper->get_button_colors();
        $recaptcha_key = $data['recaptcha_key'] = get_option('wsnm_recaptcha_site_key');
        $recatpcha_status = $data['recaptcha_status'] = $this->helper->is_recaptcha_enabled();
        ob_start();
        require_once WSNM_PATH . 'public/parts/form-modal.php';
        $data['content'] = ob_get_clean();
        wp_send_json($data);
    }

    /**
     * modify_cta_text
     * 
     * @param  mixed $text
     * @return string
     */
    public function modify_cta_text($text): string
    {
        $text = $this->helper->get_button_text();
        return $text;
    }

    /**
     * modify_modal_title
     * 
     * @param  mixed $text
     * @return string
     */
    public function modify_modal_title($text): string
    {
        $text = $this->helper->get_modal_title();
        return $text;
    }
}