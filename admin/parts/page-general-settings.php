<form method="post">
    <div class="wsnm-settings-row">
        <h3>
            <?php _e('Mode', 'back-in-stock-notifications-for-woocommerce'); ?>
        </h3>
        <div class="wsnm-field-row wsnm-field-row-flex">
            <div class="wsnm-field-sub-row">
                <label><input type="radio" id="wsnm_mode" name="wsnm_mode" value="manually" <?php echo ($mode) == 'manually' ? "checked" : ""; ?>><?php _e('Manually', 'back-in-stock-notifications-for-woocommerce'); ?></label>
                <span class="dashicons dashicons-info-outline wsnm-tooltip">
                    <span class="wsnm-tooltip-text">
                        <?php _e('Is the default mode. The notifications are triggered manually by administrator directly from the product page.', 'back-in-stock-notifications-for-woocommerce'); ?>
                    </span>
                </span>
            </div>
            <div class="wsnm-field-sub-row">
                <label><input type="radio" id="wsnm_mode" name="wsnm_mode" value="automatically" <?php echo ($mode) == 'automatically' ? "checked" : ""; ?>><?php _e('Automatically', 'back-in-stock-notifications-for-woocommerce'); ?></label>
                <span class="dashicons dashicons-info-outline wsnm-tooltip">
                    <span class="wsnm-tooltip-text">
                        <?php _e('When this mode is enabled, the notifications are triggered automatically by the stock status. The notifications are sent when the product is back in stock.', 'back-in-stock-notifications-for-woocommerce'); ?>
                    </span>
                </span>
            </div>
        </div>
    </div>
    <div class="wsnm-settings-row">
        <h3>
            <?php _e('Button Style', 'back-in-stock-notifications-for-woocommerce'); ?>
            <span class="dashicons dashicons-info-outline wsnm-tooltip">
                <span class="wsnm-tooltip-text">
                    <?php _e('Change the default button style', 'back-in-stock-notifications-for-woocommerce'); ?>
                </span>
            </span>
        </h3>
        <div class="wsnm-field-row wsnm-field-row-flex">
            <div class="wsnm-field-sub-row">
                <label for="wsnm_button_background_color"><p><?php _e('Background color', 'back-in-stock-notifications-for-woocommerce'); ?></p></label>
                <input type="text" value="<?php echo wp_kses($colors['background'], array()); ?>" id="wsnm_btn_background_color"  name="wsnm_btn_background_color">
            </div>
            <div class="wsnm-field-sub-row">
                <label for="wsnm_button_background_color"><p><?php _e('Text color', 'back-in-stock-notifications-for-woocommerce'); ?></p></label>
                <input type="text" value="<?php echo wp_kses($colors['text'], array()); ?>" id="wsnm_btn_text_color"  name="wsnm_btn_text_color">
            </div>
        </div>
    </div>
    <?php wp_nonce_field( 'wsnm-settings-save', 'nonce-wsnm-settings' ); ?>
    <input type="submit" class="button" value="<?php _e('Save', 'back-in-stock-notifications-for-woocommerce'); ?>">
</form>