<?php

/**
 * WSNM_i18n
 * 
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files
 */
class WSNM_i18n
{


    /**
     * load_plugin_textdomain
     *
     * @return void
     */
    public function load_plugin_textdomain()
    {

        load_plugin_textdomain(
            'back-in-stock-notifications-for-woocommerce',
            false,
            dirname(WSNM_BASE) . '/languages/'
        );
    }
}
