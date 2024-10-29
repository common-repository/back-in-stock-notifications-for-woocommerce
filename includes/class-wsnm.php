<?php

class WSNM_Woo_Stock_Notify_Me
{
    /**
     * The loader that's responsible for maintaining and registering all hooks that power
     * the plugin.
     *
     * @var WSNM_Woo_Stock_Notify_Me_Loader    $loader    Maintains and registers all hooks for the plugin.
     */
    protected $loader;

    /**
     * helper
     *
     * @var mixed
     */
    public $helper;

    /**
     * Define the core functionality of the plugin.
     *
     */
    public function __construct()
    {
        $this->load_dependencies();
        $this->set_locale();
        $this->helper = new WSNM_HELPER;
        $this->define_admin_hooks();
        $this->define_public_hooks();
    }

    /**
     * Load the required dependencies for this plugin.
     *
     */
    private function load_dependencies()
    {
        /**
         * The class responsible for SQL queries
         */
        require_once WSNM_PATH . 'includes/class-wsnm-db-queries.php';

        /**
         * The package and class responsible for scheduling the actions
         */
        require_once WSNM_PATH . 'packages/action-scheduler/action-scheduler.php';
        require_once WSNM_PATH . 'includes/class-action-scheduler.php';

        /**
         * The class containing various useful methods
         */
        require_once WSNM_PATH . 'includes/class-wsnm-helper.php';

        /**
         * The class responsible for orchestrating the actions and filters of the
         * core plugin.
         */
        require_once WSNM_PATH . 'includes/class-wsnm-loader.php';

        /**
         * The class responsible for defining internationalization functionality
         * of the plugin.
         */
        require_once WSNM_PATH . 'includes/class-wsnm-i18n.php';

        /**
         * The class responsible for defining all actions that occur in the admin area.
         */
        require_once WSNM_PATH . 'admin/class-wsnm-admin.php';

        /**
         * The class responsible for all custom settings that occur in the admin area.
         */
        require_once WSNM_PATH . 'admin/class-wsnm-admin-settings.php';

        /**
         * The class responsible for defining all actions that occur in the public area.
         */
        require_once WSNM_PATH . 'public/class-wsnm-public.php';

        $this->loader = new WSNM_Woo_Stock_Notify_Me_Loader();
    }


    /**
     * set_locale
     *
     * Define the locale for this plugin for internationalization.
     *
     * @return void
     */
    public function set_locale()
    {
        $plugin_i18n = new WSNM_i18n();

        $this->loader->add_action('plugins_loaded', $plugin_i18n, 'load_plugin_textdomain');
    }



    /**
     * Register all of the hooks related to the admin area functionality
     * of the plugin.
     *
     */
    private function define_admin_hooks()
    {
        $plugin_admin = new WSNM_Woo_Stock_Notify_Me_Admin($this->helper);
        $plugin_admin_settings = new WSNM_Woo_Stock_Notify_Me_Admin_Settings($this->helper);

        if (in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')))) {
            //WSNM_Woo_Stock_Notify_Me_Admin Actions
            $this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_styles');
            $this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts');
            $this->loader->add_action('init', $plugin_admin, 'custom_post_type');
            $this->loader->add_action('add_meta_boxes', $plugin_admin, 'add_meta_boxes');
            $this->loader->add_action('manage_wsnm_notify_me_posts_custom_column', $plugin_admin, 'custom_wsnm_notify_me_column', 10, 2);
            $this->loader->add_action('restrict_manage_posts', $plugin_admin, 'filter_by_products');
            $this->loader->add_action('pre_get_posts', $plugin_admin, 'custom_filters_query');
            $this->loader->add_action('woocommerce_product_data_panels', $plugin_admin, 'custom_product_data_tab_fields');
            $this->loader->add_action('woocommerce_process_product_meta', $plugin_admin, 'process_custom_product_data_tab_fields');
            $this->loader->add_action('admin_footer', $plugin_admin, 'open_data_tab_by_default');

            //WSNM_Woo_Stock_Notify_Me_Admin Filters
            $this->loader->add_filter('bulk_actions-edit-wsnm_notify_me', $plugin_admin, 'download_csv');
            $this->loader->add_filter('handle_bulk_actions-edit-wsnm_notify_me', $plugin_admin, 'handle_download_csv', 10, 3);
            $this->loader->add_filter('manage_wsnm_notify_me_posts_columns', $plugin_admin, 'custom_columns');
            $this->loader->add_filter('woocommerce_product_data_tabs', $plugin_admin, 'custom_product_data_tab', 99, 1);
            $this->loader->add_filter('plugin_action_links_' . WSNM_BASE, $plugin_admin, 'add_action_links');

            //WSNM_Woo_Stock_Notify_Me_Admin_Settings Actions
            $this->loader->add_action('admin_menu', $plugin_admin_settings, 'settings_menu');
            $this->loader->add_action('init', $plugin_admin_settings, 'save_settings');
            $this->loader->add_action('admin_notices', $plugin_admin_settings, 'save_settings_notice');
            $this->loader->add_action('after_setup_theme', $plugin_admin_settings, 'tinyMCE_theme_setup');
            $this->loader->add_action('admin_enqueue_scripts', $plugin_admin_settings, 'enqueue_color_picker');

            //WSNM_SCHEDULER Actions
            $this->loader->add_action('init', $this->helper->scheduler, 'schedule_action');
            $this->loader->add_action('wsnm_run_manually', $this->helper->scheduler, 'wsnm_run_manually');
            $this->loader->add_action('wsnm_run_automatically', $this->helper->scheduler, 'wsnm_run_automatically');
        
            //WSNM_DB_Queries Actions
            $this->loader->add_action('wsnm_new_manually_notification', $this->helper->db_queries, 'add_manually_action');
        } else {
            $this->loader->add_action('admin_notices', $plugin_admin, 'woocommerce_missing_notice');
        }
    }


    /**
     * Register all of the hooks related to the public-facing functionality
     * of the plugin.
     *
     */
    private function define_public_hooks()
    {

        $plugin_public = new WSNM_Woo_Stock_Notify_Me_Public($this->helper);

        if (in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')))) {
            $this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_resources');
            $this->loader->add_action('wp_ajax_wsnm_open_popup', $plugin_public, 'generate_popup');
            $this->loader->add_action('wp_ajax_nopriv_wsnm_open_popup', $plugin_public, 'generate_popup');
            $this->loader->add_action('wp_ajax_wsnm_save_request', $plugin_public, 'save_request');
            $this->loader->add_action('wp_ajax_nopriv_wsnm_save_request', $plugin_public, 'save_request');
            $this->loader->add_action('wp_head', $plugin_public, 'recaptcha_resources');
            $this->loader->add_filter('woocommerce_get_availability', $plugin_public, 'prepare_cta', 100, 2);
            $this->loader->add_filter('wsnm-text-cta', $plugin_public, 'modify_cta_text', 10, 1);
            $this->loader->add_filter('wsnm-modal-title', $plugin_public, 'modify_modal_title', 10, 1);
        }
    }

    /**
     * Run the loader to execute all of the hooks with WordPress.
     *
     */
    public function run()
    {
        $this->loader->run();
    }

    /**
     * The reference to the class that orchestrates the hooks with the plugin.
     *
     * @return    WSNM_Woo_Stock_Notify_Me_Loader    Orchestrates the hooks of the plugin.
     */
    public function get_loader()
    {
        return $this->loader;
    }
}
