<div class="wrap">
    <!-- Wrap the logo in <h2></h2> to display notices under it --> 
    <h2><img src="<?php echo WSNM_URL . 'admin/img/back-in-stock-notif.svg'; ?>" alt="<?php _e('Notify Me', 'back-in-stock-notifications-for-woocommerce'); ?>" class="wsnm-settings-logo"></h2>
    <nav class="wsnm-tab-wrapper">
        <?php foreach($tabs as $key => $tab): ?>
            <a href="<?php echo esc_url($tab['url']); ?>" title="<?php echo esc_html($tab['title']); ?>" class="tab <?php echo ($key == $active_tab) ? 'active' : '';  ?> <?php echo esc_html($key); ?>" target="<?php echo (isset($tab['callback'])) ? '_self' : '_blank'; ?>"><?php echo esc_html($tab['title']); ?></a>
        <?php endforeach; ?>
    </nav>
    <div class="wsnm-content">