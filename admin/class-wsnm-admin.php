<?php

/**
 * WSNM_Woo_Stock_Notify_Me_Admin
 */
class WSNM_Woo_Stock_Notify_Me_Admin
{

    /**
     * helper
     *
     * @var mixed
     */
    public $helper;

    /**
     * Initialize the class.
     *
     */
    public function __construct($helper)
    {
        $this->helper = $helper;
    }


    /**
     * woocommerce_missing_notice
     *
     * @return void
     */
    public function woocommerce_missing_notice()
    {
        echo sprintf(
            '<div class="notice notice-warning is-dismissible"><p>%s</p></div>',
            __('Woo Stock Notify Me requires Woocommerce to be installed.', 'back-in-stock-notifications-for-woocommerce')
        );
    }

    /**
     * enqueue_styles
     *
     * @return void
     */
    public function enqueue_styles()
    {
        if ($this->isWSNMNotifyMePages()) {
            wp_enqueue_style(WSNM_DOMAIN, WSNM_URL . 'admin/css/selectWoo.min.css', array(), filemtime(WSNM_PATH . 'admin/css/selectWoo.min.css'));
        }
        wp_enqueue_style(WSNM_DOMAIN . '-admin', WSNM_URL . 'admin/css/wsnm-admin.css', array(), filemtime(WSNM_PATH . 'admin/css/wsnm-admin.css'));
    }

    /**
     * enqueue_scripts
     *
     * @return void
     */
    public function enqueue_scripts()
    {
        //if ($this->isWSNMNotifyMePages()) {
            wp_enqueue_script(WSNM_DOMAIN, WSNM_URL . 'admin/js/wsnm-admin.js', array('jquery', 'selectWoo', 'wp-color-picker'), filemtime(WSNM_PATH . 'admin/js/wsnm-admin.js'));
            wp_enqueue_script('wc-enhanced-select');
        //}

    }

    /**
     * custom_post_type
     *
     * @return void
     */
    public function custom_post_type()
    {
        $labels = array(
            'name'               => __('Notify Me', 'back-in-stock-notifications-for-woocommerce'),
            'singular_name'      => __('Notify Me', 'back-in-stock-notifications-for-woocommerce'),
            'add_new'            => _x('Add New', 'Notify Me - Plugin', 'back-in-stock-notifications-for-woocommerce'),
            'add_new_item'       => __('Add New Subscription', 'back-in-stock-notifications-for-woocommerce'),
            'edit_item'          => __('Edit Subscription', 'back-in-stock-notifications-for-woocommerce'),
            'new_item'           => __('New Subscription', 'back-in-stock-notifications-for-woocommerce'),
            'all_items'          => __('All Subscription', 'back-in-stock-notifications-for-woocommerce'),
            'view_item'          => __('View Subscription', 'back-in-stock-notifications-for-woocommerce'),
            'search_items'       => __('Search Subscription', 'back-in-stock-notifications-for-woocommerce'),
            'not_found'          => __('No Subscription found', 'back-in-stock-notifications-for-woocommerce'),
            'not_found_in_trash' => __('No Subscription found in the Trash', 'back-in-stock-notifications-for-woocommerce'),
            'menu_name'          => __('Notify Me', 'back-in-stock-notifications-for-woocommerce')
        );
        $args = array(
            'labels'            => $labels,
            'public'            => true,
            'has_archive'       => false,
            'hierarchical'         => false,
            'query_var'         => false,
            'publicly_queryable' => false,
            'menu_icon'         => 'dashicons-bell',
            'supports'          => array('revisions')
        );
        register_post_type($this->helper->getCustomPostType(), $args);
    }

    /**
     * isWSNMNotifyMePages
     *
     * @return bool
     */
    public function isWSNMNotifyMePages(): bool
    {
        $screen = get_current_screen();
        if ($screen->post_type == $this->helper->getCustomPostType())  return true;
        return false;
    }

    /**
     * add_meta_boxes
     *
     * @return void
     */
    public function add_meta_boxes()
    {
        add_meta_box('wsnm_meta_box', __('Details', 'back-in-stock-notifications-for-woocommerce'), array($this, 'details_box'), $this->helper->getCustomPostType(), 'normal');
    }


    /**
     * details_box
     *
     * @param  object $post
     * @return void
     */
    public function details_box(object $post)
    {
        $product = $this->helper->getNotificationSelectedProduct($post->ID);
        $flname_status = $this->helper->get_first_last_name_status();
        $first_name = $this->helper->get_first_name($post->ID);
        $last_name = $this->helper->get_last_name($post->ID);
        $email = $this->helper->get_email($post->ID);
        $status_html = $this->helper->get_notification_status_html($post->ID);

        require_once WSNM_PATH . 'admin/parts/notification-details.php';
    }



    /**
     * custom_columns
     *
     * @param  mixed $columns
     * @return void
     */
    public function custom_columns($columns)
    {
        unset($columns['title']);
        $columns['custom_title'] = __('Name', 'back-in-stock-notifications-for-woocommerce');
        $n_columns = array();
        foreach ($columns as $key => $value) {
            if ($key == 'date') {
                $n_columns['custom_title'] = 'custom_title';
                $n_columns['sent_email'] = __('Status', 'back-in-stock-notifications-for-woocommerce');
                $n_columns['product'] = __('Product', 'back-in-stock-notifications-for-woocommerce');
            }
            $n_columns[$key] = $value;
        }
        return $n_columns;
    }

    /**
     * custom_wsnm_notify_me_column
     *
     * @param  mixed $column
     * @param  mixed $post_id
     * @return void
     */
    public function custom_wsnm_notify_me_column($column, $post_id)
    {
        switch ($column) {
            case 'custom_title':
                $fname = $this->helper->get_first_name($post_id);
                $lname = $this->helper->get_last_name($post_id);
                $email = $this->helper->get_email($post_id);
                if($fname == '' && $lname == ''){
                    echo esc_html($email);
                }else{
                    echo sprintf('%s %s (%s)', esc_html($fname), esc_html($lname), esc_html($email));
                }
                break;
            case 'sent_email':
                echo wp_kses_post($this->helper->get_notification_status_html($post_id));
                break;
            case 'product':
                $selectedProduct = $this->helper->getNotificationSelectedProduct($post_id);
                if ($selectedProduct) {
                    $url = get_permalink($selectedProduct->get_id());
                    echo sprintf('<a href="%s" title="%s" target="_blank">%s</a>', esc_url($url), esc_attr($selectedProduct->get_name()), esc_html($selectedProduct->get_name()));
                } else {
                    _e('Unavailable', 'back-in-stock-notifications-for-woocommerce');
                }
                break;
            default:
                break;
        }
    }

    /**
     * filter_by_products
     *
     * @return void
     */
    public function filter_by_products()
    {
        global $post_type;
        if ($post_type == $this->helper->getCustomPostType()) {
?>
            <select name="wsnm_filter_product" class="wc-enhanced-select">
                <option value="all"><?php _e('Select Product', 'back-in-stock-notifications-for-woocommerce'); ?></option>
                <?php
                $args = array(
                    'posts_per_page' => -1,
                    'post_type' => 'product',
                    'post_status' => 'publish',
                    'orderby' => 'title,'
                );
                $products = new WP_Query($args);
                $current_product = isset($_GET['wsnm_filter_product']) ? sanitize_text_field($_GET['wsnm_filter_product']) : '';
                if ($products->have_posts()) :
                    while ($products->have_posts()) :
                        $products->the_post();
                        $productID = get_the_ID();
                        $title = get_the_title();
                        printf(
                            '<option value="%s"%s>%s</option>',
                            $productID,
                            $productID == $current_product ? ' selected="selected"' : '',
                            $title
                        );
                    endwhile;
                endif;
                ?>
            </select>
        <?php
        }
    }

    /**
     * custom_filters_query
     *
     * @param  mixed $query
     * @return void
     */
    public function custom_filters_query($query)
    {
        if (is_admin() && $query->is_main_query()) {
            $meta_query = ['relation' => 'OR'];
            $scr = get_current_screen();
            if ($scr->base !== 'edit' && $scr->post_type !== $this->helper->getCustomPostType()) return;

            if (isset($_GET['wsnm_filter_product']) && $_GET['wsnm_filter_product'] != 'all') {
                $meta_query[] = array(
                    'key' => 'wsnm_product_id',
                    'value' => sanitize_text_field($_GET['wsnm_filter_product'])
                );
                $meta_query[] = array(
                    'key' => 'wsnm_variation_id',
                    'value' => sanitize_text_field($_GET['wsnm_filter_product'])
                );
            }

            $search_term = $query->query_vars['s'];

            $query->query_vars['s'] = '';

            if ($search_term != '') {
                $meta_query[] = array(
                    'key' => 'wsnm_email',
                    'value' => $search_term,
                    'compare' => 'LIKE'
                );
            }
            $query->set('meta_query', $meta_query);
        }
    }

    /**
     * download_csv
     *
     * @param  mixed $bulk_actions
     * @return array
     */
    public function download_csv($bulk_actions): array
    {
        $bulk_actions['generate_csv_wsnm_records'] = __('Download CSV', 'back-in-stock-notifications-for-woocommerce');
        return $bulk_actions;
    }

    /**
     * handle_download_csv
     *
     * @param  mixed $redirect_url
     * @param  mixed $action
     * @param  mixed $post_ids
     * @return void
     */
    public function handle_download_csv($redirect_url, $action, $post_ids)
    {
        if ($action == 'generate_csv_wsnm_records') {
            $columns = [
                __('Email', 'back-in-stock-notifications-for-woocommerce')
            ];
            if($this->helper->get_first_last_name_status()){
                $columns[] = __('Name', 'back-in-stock-notifications-for-woocommerce');
            }
            $columns[] = __('Product', 'back-in-stock-notifications-for-woocommerce');
            $columns[] = __('Status', 'back-in-stock-notifications-for-woocommerce');
            header('Content-Type: text/csv');
            header('Content-Disposition: attachment; filename="records.csv"');
            header('Pragma: no-cache');
            header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
            $out = fopen('php://output', 'w');
            fputcsv($out, $columns, ';', '"', '\\');
            foreach ($post_ids as $post_id) {
                $values = [];
                $values[] = $this->helper->get_email($post_id);
                if($this->helper->get_first_last_name_status()){
                    $values[] =  $this->helper->get_first_name($post_id) .' '. $this->helper->get_last_name($post_id);
                }
                $product = $this->helper->getNotificationProduct($post_id);
                if ($product) {
                    $values[] = $product->get_title();
                    $status = $this->helper->get_notification_status($post_id, $product);
                    $values[] = $status['message'];
                } else {
                    $values[] = __('Not Available', 'back-in-stock-notifications-for-woocommerce');
                    $values[] = __('Not Available', 'back-in-stock-notifications-for-woocommerce');
                }
                fputcsv($out, $values, ';', '"', '\\');
            }
            exit();
        }
    }

    /**
     * custom_product_data_tab
     *
     * @param  array $product_data_tabs
     * @return array
     */
    public function custom_product_data_tab(array $product_data_tabs): array
    {
        $product_data_tabs['wsnm-options'] = array(
            'label' => __('Out of stock - Notifications', 'back-in-stock-notifications-for-woocommerce'),
            'target' => 'wsnm_options_data',
        );
        return $product_data_tabs;
    }

    /**
     * custom_product_data_tab_fields
     *
     * @return void
     */
    public function custom_product_data_tab_fields()
    {
        global $post;
        $mode = $this->helper->get_mode();
        $in_progress = $this->helper->db_queries->is_action_in_progress($post->ID);
        $is_paused = $this->helper->is_product_paused($post->ID);
        ?>
        <div id="wsnm_options_data" class="panel woocommerce_options_panel">
            <?php if($mode == 'automatically'): ?>
                <div class="options_group">
                    <?php
                        woocommerce_wp_checkbox(array(
                            'id'            => 'wsnm_product_pause',
                            'wrapper_class' => 'show_if_simple show_if_variable',
                            'label'         => __('Pause Notifications.', 'back-in-stock-notifications-for-woocommerce'),
                            'description'   => __('By default the notifications are sent for all products, check this to pause the notifications for this particular product.', 'back-in-stock-notifications-for-woocommerce'),
                            'default'       => '0',
                            'desc_tip'      => false,
                        ));
                    ?>
                </div>
            <?php endif; ?>
            <?php if($mode == 'manually' && get_post_status() == 'publish' && !$in_progress): ?>
                <div class="options_group">
                    <?php
                        woocommerce_wp_checkbox(array(
                            'id'            => 'wsnm_send_notifications',
                            'wrapper_class' => 'show_if_simple show_if_variable',
                            'label'         => __('Manually Mode', 'back-in-stock-notifications-for-woocommerce'),
                            'description'   => __('Check the box and save the product to send notifications.', 'back-in-stock-notifications-for-woocommerce'),
                            'default'       => '0',
                            'desc_tip'      => false,
                        ));
                    ?>
                </div>
            <?php endif; ?>
            <?php if($mode == 'manually' && get_post_status() == 'publish' && $in_progress): ?>
                <div class="options_group">
                    <p class="request-in-process-notification">Your request is in queue. Scheduled date is <em><?php echo esc_html($this->helper->scheduler->next_run('wsnm_run_manually')); ?></em></p>
                </div>
            <?php endif; ?>
            <?php if($mode == 'automatically' && get_post_status() == 'publish' && !$is_paused): ?>
                <div class="options_group wsnm-automatically-mode-enabled">
                    <p class="request-in-process-notification">Automatically mode enabled. The next checking date is <em><?php echo esc_html($this->helper->scheduler->next_run('wsnm_run_automatically')); ?></em></p>
                </div>
            <?php endif; ?>
        </div>
        <?php
    }

    /**
     * process_custom_product_data_tab_fields
     *
     * @param  int $post_id
     * @return void
     */
    public function process_custom_product_data_tab_fields(int $post_id)
    {
        //Update status
        $status = isset($_POST['wsnm_product_pause']) ? 'yes' : 'no';
        update_post_meta($post_id, 'wsnm_product_pause', $status);

        //Check for manually mode 
        $mode = $this->helper->get_mode();
        if($mode == 'manually')
        {
            if(isset($_POST['wsnm_send_notifications']))
            {
                do_action('wsnm_new_manually_notification', $post_id);
            }
        }
    }

    /**
     * open_data_tab_by_default
     *
     * @return void
     */
    public function open_data_tab_by_default()
    {
        $screen = get_current_screen();

        if ($screen->post_type == 'product' && $screen->parent_base == 'edit' && isset($_GET['wsnm_product_tab']) && isset($_GET['wsnm_product_tab_content'])) {
            echo sprintf('<script>jQuery(document).ready(function($){$(".%s a").trigger("click");$("html, body").animate({scrollTop: $("#%s").offset().top - 600}, "slow");})</script>', esc_js($_GET['wsnm_product_tab']), esc_js($_GET['wsnm_product_tab_content']));
        }
    }

    /**
     * add_action_links
     *
     * @return array
     */
    public function add_action_links($links)
    {
        $settings_link = sprintf('<a href="%s" title="%s">%s</a>', menu_page_url('wsnm-settings', false), __('Plugin Settings', 'back-in-stock-notifications-for-woocommerce'),  __('Settings', 'back-in-stock-notifications-for-woocommerce'));
        array_push( $links, $settings_link);
        return $links;
    }
}
