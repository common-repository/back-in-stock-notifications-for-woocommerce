<form method="post">
    <div class="wsnm-settings-row">
        <h3>
            <?php _e('Form Settings', 'back-in-stock-notifications-for-woocommerce'); ?>
            <span class="dashicons dashicons-info-outline wsnm-tooltip">
                <span class="wsnm-tooltip-text">
                    <?php _e('Manage the subscription form', 'back-in-stock-notifications-for-woocommerce'); ?>
                </span>
            </span>
        </h3>
        <div class="wsnm-field-row">
            <div class="wsnm-field-sub-row">
                <label><input type="checkbox" id="wsnm_form_first_last_name" name="wsnm_form_first_last_name" <?php echo ($form_first_last_name) ? "checked" : ""; ?>><?php _e('Enable First and Last Name', 'back-in-stock-notifications-for-woocommerce'); ?></label>
            </div>
            <div class="wsnm-field-sub-row">
                <label><input type="checkbox" id="wsnm_form_recaptcha_status" name="wsnm_form_recaptcha_status" <?php echo ($recaptcha_status) ? "checked" : ""; ?>><?php _e('Enable reCAPTCHA v2', 'back-in-stock-notifications-for-woocommerce'); ?></label>
                <span class="dashicons dashicons-info-outline wsnm-tooltip">
                    <span class="wsnm-tooltip-text">
                        <?php _e('Avoid any spam by enabling reCAPTCHA v2. Click <a href="https://www.google.com/recaptcha" target="_blank">here</a> to generate the Site & Secret keys', 'back-in-stock-notifications-for-woocommerce'); ?>
                    </span>
                </span>
            </div>
        </div>
        <div class="wsnm-field-row" id="wsnm_form_recaptcha" style="display: <?php echo ($recaptcha_status) ? "block" : "none"; ?> ">
            <div class="wsnm-field-sub-row">
                <label for="wsnm_recaptcha_site_key">
                    <p><?php _e('Recaptcha Site KEY', 'back-in-stock-notifications-for-woocommerce'); ?></p>
                </label>
                <input type="text" value="<?php echo esc_html($recaptcha_site_key); ?>" id="wsnm_recaptcha_site_key" name="wsnm_recaptcha_site_key">
            </div>
            <div class="wsnm-field-sub-row">
                <label for="wsnm_recaptcha_secret_key">
                    <p><?php _e('Recaptcha Secret KEY', 'back-in-stock-notifications-for-woocommerce'); ?></p>
                </label>
                <input type="text" value="<?php echo esc_html($recaptcha_secret_key); ?>" id="wsnm_recaptcha_secret_key" name="wsnm_recaptcha_secret_key">
            </div>
        </div>
    </div>
    <div class="wsnm-settings-row">
        <h3>
            <?php _e('Other Settings', 'back-in-stock-notifications-for-woocommerce'); ?>
        </h3>
        <div class="wsnm-field-row">
            <div class="wsnm-field-sub-row">
                <label for="wsnm_button_text">
                    <p><?php _e('Button Text', 'back-in-stock-notifications-for-woocommerce'); ?></p>
                </label>
                <input type="text" value="<?php echo esc_html($button_text); ?>" id="wsnm_button_text" name="wsnm_button_text" required>
            </div>
        </div>
        <div class="wsnm-field-row">
            <div class="wsnm-field-sub-row">
                <label for="wsnm_modal_title">
                    <p><?php _e('Modal Title', 'back-in-stock-notifications-for-woocommerce'); ?></p>
                </label>
                <input type="text" value="<?php echo esc_html($modal_title); ?>" id="wsnm_modal_title" name="wsnm_modal_title" required>
            </div>
        </div>
        <div class="wsnm-field-row">
            <div class="wsnm-field-sub-row">
                <label for="wsnm_pre_form_content">
                    <p><?php _e('Before Form Text', 'back-in-stock-notifications-for-woocommerce'); ?></p>
                </label>
                <?php
                $tinymce_settings = array(
                    'toolbar1'      => 'fontselect, fontsizeselect, separator, bold, italic, underline, separator, alignleft, aligncenter, alignright, separator, forecolor, backcolor, separator, textcolor, link, undo, redo, separator, removeformat, tags',
                    'font_formats' => 'Sans Serif=arial,helvetica,sans-serif;Serif=times new roman, serif;Fixed Width=monospace, monospace;Wide=arial black, sans-serif;Narrow=arial narrow, sans-serif;Comic Sans MS=comic sans ms, sans-serif;Garamond=garamond, serif;Georgia=georgia, serif;Tahoma=tahoma, sans-serif;Trebuchet MS=trebuchet ms, sans-serif;Verdana=verdana, sans-serif;',
                    'inline_styles' => false,
                    'statusbar' => false,
                );
                $tinymce_settings['setup'] = 'function (editor) {
                            editor.addButton("tags", {
                                type: "listbox",
                                text: "Merge Tags",
                                icon: false,
                                onselect: function (e) {
                                    editor.insertContent(this.value());
                                },
                                values: [
                                    { text: "Product Title", value: "[wsnm-product-title]" },
                                    { text: "Product Price", value: "[wsnm-product-price]" }
                                ]
                            });
                        }';
                $settings = array('tinymce' => $tinymce_settings, 'theme' => 'modern', 'plugins' => 'textcolor, link, autolink, linkchecker', 'media_buttons' => false, 'quicktags' => false,  'textarea_rows' =>  10, 'menubar' => 'insert', 'wpautop' => true, 'textarea_name' => 'wsnm_pre_form_content');
                wp_editor($before_form_text, 'wsnm_pre_form_content', $settings);
                ?>
            </div>
            <div class="wsnm-field-sub-row">
                <label for="wsnm_after_form_content">
                    <p><?php _e('After Form Text', 'back-in-stock-notifications-for-woocommerce'); ?></p>
                </label>
                <?php
                $tinymce_settings['setup'] = 'function (editor) {
                                editor.addButton("tags", {
                                    type: "listbox",
                                    text: "Merge Tags",
                                    icon: false,
                                    onselect: function (e) {
                                        editor.insertContent(this.value());
                                    },
                                    values: [
                                        { text: "Product Title", value: "[wsnm-product-title]" },
                                        { text: "Product Price", value: "[wsnm-product-price]" }
                                    ]
                                });
                            }';
                $settings = array('tinymce' => $tinymce_settings, 'theme' => 'modern', 'plugins' => 'textcolor, link, autolink, linkchecker', 'media_buttons' => false, 'quicktags' => false,  'textarea_rows' =>  10, 'menubar' => 'insert', 'wpautop' => true, 'textarea_name' => 'wsnm_after_form_content');
                wp_editor($after_form_text, 'wsnm_after_form_content', $settings);
                ?>
            </div>
        </div>
    </div>
    <?php wp_nonce_field('wsnm-subscription-form-settings-save', 'nonce-wsnm-subscription-form-settings'); ?>
    <input type="submit" class="button" value="<?php _e('Save', 'back-in-stock-notifications-for-woocommerce'); ?>">
</form>