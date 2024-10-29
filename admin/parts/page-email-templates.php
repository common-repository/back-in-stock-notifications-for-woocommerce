<form method="post">
    <div class="wsnm-settings-row">
        <h3>
            <?php _e('Confirmation Email', 'back-in-stock-notifications-for-woocommerce'); ?>
            <span class="dashicons dashicons-info-outline wsnm-tooltip">
                <span class="wsnm-tooltip-text">
                    <?php _e('The confirmation email is sent when someone subscribes to get notifications.', 'back-in-stock-notifications-for-woocommerce'); ?>
                </span>
            </span>
        </h3>
        <div class="wsnm-field-row">
            <label><input type="checkbox" name="wsnm_subscribe_confirmation_status" id="wsnm_subscribe_confirmation_status" <?php echo ($subscribe_confirmation_email_status == "disabled") ? "" : "checked"; ?>> <?php _e('Enable the confirmation email', 'back-in-stock-notifications-for-woocommerce'); ?></label>
            <span class="dashicons dashicons-info-outline wsnm-tooltip">
                <span class="wsnm-tooltip-text">
                    <?php _e('Uncheck to disable the default confirmation email.', 'back-in-stock-notifications-for-woocommerce'); ?>
                </span>
            </span>
        </div>
        <div class="wsnm-field-row" id="wsnm_subscribe_confirmation_row" style="display: <?php echo ($subscribe_confirmation_email_status == "disabled") ? "none" : "block"; ?> ">
            <div class="wsnm-field-sub-row">
                <label for="wsnm_subscribe_confirmation_subject">
                    <p><?php _e('Confirmation Email - Subject', 'back-in-stock-notifications-for-woocommerce'); ?></p>
                </label>
                <input type="text" value="<?php echo wp_kses($subscribe_confirmation_email_subject, array()); ?>" id="wsnm_subscribe_confirmation_subject" name="wsnm_subscribe_confirmation_subject">
            </div>
            <div class="wsnm-field-sub-row">
                <label for="wsnm_subscribe_confirmation">
                    <p><?php _e('Confirmation Email - Content', 'back-in-stock-notifications-for-woocommerce'); ?></p>
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
                                    { text: "First Name", value: "[wsnm-first-name]" },
                                    { text: "Last Name", value: "[wsnm-last-name]" },
                                    { text: "Email", value: "[wsnm-email]" },
                                    { text: "Product Title", value: "[wsnm-product-title]" },
                                    { text: "Product Price", value: "[wsnm-product-price]" },
                                    { text: "Product URL", value: "[wsnm-product-url]" },
                                ]
                            });
                        }';
                $settings = array('tinymce' => $tinymce_settings, 'theme' => 'modern', 'plugins' => 'textcolor, link, autolink, linkchecker', 'media_buttons' => false, 'quicktags' => false,  'textarea_rows' =>  12, 'textarea_name' => 'wsnm_subscribe_confirmation', 'wpautop' => true);
                wp_editor($subscribe_confirmation_email, 'wsnm_subscribe_confirmation', $settings);
                ?>
            </div>
        </div>
    </div>
    <div class="wsnm-settings-row">
        <h3>
            <?php _e('Back in Stock Notification Email', 'back-in-stock-notifications-for-woocommerce'); ?>
            <span class="dashicons dashicons-info-outline wsnm-tooltip">
                <span class="wsnm-tooltip-text">
                    <?php _e('The back in stock notification email is sent when the product is back in stock again.', 'back-in-stock-notifications-for-woocommerce'); ?>
                </span>
            </span>
        </h3>
        <div class="wsnm-field-row">
            <div class="wsnm-field-sub-row">
                <label for="wsnm_back_in_stock_notification_subject">
                    <p><?php _e('Back in Stock Notification Email - Subject', 'back-in-stock-notifications-for-woocommerce'); ?></p>
                </label>
                <input type="text" value="<?php echo wp_kses($subscribe_notification_email_subject, array()); ?>" id="wsnm_back_in_stock_notification_subject" name="wsnm_back_in_stock_notification_subject">
            </div>
            <div class="wsnm-field-sub-row">
                <label for="wsnm_back_in_stock_notification">
                    <p><?php _e('Back in Stock Notification Email - Content', 'back-in-stock-notifications-for-woocommerce'); ?></p>
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
                                    { text: "First Name", value: "[wsnm-first-name]" },
                                    { text: "Last Name", value: "[wsnm-last-name]" },
                                    { text: "Email", value: "[wsnm-email]" },
                                    { text: "Product Title", value: "[wsnm-product-title]" },
                                    { text: "Product Price", value: "[wsnm-product-price]" },
                                    { text: "Product Quantity", value: "[wsnm-product-quantity]" },
                                    { text: "Product URL", value: "[wsnm-product-url]" },
                                ]
                            });
                        }';
                $settings = array('tinymce' => $tinymce_settings, 'theme' => 'modern', 'plugins' => 'textcolor, link, autolink, linkchecker', 'media_buttons' => false, 'quicktags' => false,  'textarea_rows' =>  12, 'textarea_name' => 'wsnm_back_in_stock_notification', 'wpautop' => true);
                wp_editor($subscribe_notification_email, 'wsnm_back_in_stock_notification', $settings);
                ?>
            </div>
        </div>
        <div class="wsnm-field-row">
            <label><input type="checkbox" name="wsnm_reset_email" id="wsnm_reset_email"> <?php _e('Reset both emails - The confirmation and Back in stock notification', 'back-in-stock-notifications-for-woocommerce'); ?></label>
        </div>
    </div>
    <?php wp_nonce_field('wsnm-email-settings-save', 'nonce-wsnm-email-settings'); ?>
    <input type="submit" class="button" value="<?php _e('Save', 'back-in-stock-notifications-for-woocommerce'); ?>">
</form>