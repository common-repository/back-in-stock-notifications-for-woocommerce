<div id="wsnm-modal" class="wsnm-modal">
    <div class="wsnm-modal-content">
        <div class="wsnm-modal-header">
            <span class="wsnm-modal-header-text"><?php echo apply_filters('wsnm-modal-title', __('Subscribe', 'back-in-stock-notifications-for-woocommerce')); ?></span>
            <span class="wsnm-modal-close">&times;</span>
        </div>
        <div class="wsnm-modal-body">
            <div class="wsnm-modal-row">
                <?php echo wp_kses_post($before_form_text); ?>
            </div>
            <div class="wsnm-modal-row" id="wsnm-ajax-response"></div>
            <form id="wsnm-out-of-stock-form" method="post">
                <?php if ($name_status) : ?>
                    <div class="wsnm-modal-row">
                        <div class="wsnm-modal-column">
                            <input type="text" name="wsnm_form_first_name" placeholder="<?php _e('First Name ...', 'back-in-stock-notifications-for-woocommerce'); ?>" value="" required>
                        </div>
                        <div class="wsnm-modal-column">
                            <input type="text" name="wsnm_form_last_name" placeholder="<?php _e('Last Name ...', 'back-in-stock-notifications-for-woocommerce'); ?>" value="" required>
                        </div>
                    </div>
                <?php endif; ?>
                <div class="wsnm-modal-row">
                    <input type="email" name="wsnm_form_email" placeholder="<?php _e('Enter Your Email ...', 'back-in-stock-notifications-for-woocommerce'); ?>" value="" required>
                </div>
                <?php if($recatpcha_status): ?>
                    <div class="wsnm-modal-row">
                        <div id="ligh-recaptcha-id"></div>
                    </div>
                <?php endif; ?>
                <div class="wsnm-modal-row">
                    <div id="wsnm-submit-form" class="wsnm-submit-form" style="color:<?php echo esc_html($colors['text']); ?>; background-color:<?php echo esc_html($colors['background']); ?>">
                        <?php echo apply_filters('wsnm-modal-form-button', __('Subscribe', 'back-in-stock-notifications-for-woocommerce')); ?>
                    </div>
                </div>
                <?php wp_nonce_field('wsnm_add_request', 'wsnm_add_request_field', false); ?>
            </form>
            <div class="wsnm-modal-row">
                <?php echo wp_kses_post($after_form_text); ?>
            </div>
        </div>
    </div>
</div>