<div class="notification_details_row">
    <div class="notification_details_column">
        <p class="notification_details_title">Notification Status:</p>
        <?php echo wp_kses_post($status_html); ?>
    </div>

    <?php if(!empty($product)): ?>
    <div class="notification_details_column">
        <p class="notification_details_title">Product:</p>
        <a href="<?php echo esc_url($product->get_permalink()); ?>" target="_blank"><?php echo esc_html($product->get_name()); ?></a>
    </div>
    <?php endif; ?>

    <?php if($flname_status): ?>
    <div class="notification_details_column">
        <p class="notification_details_title">Name:</p>
        <?php echo esc_html($first_name); ?> <?php echo esc_html($last_name); ?>
    </div>
    <?php endif; ?>
    <div class="notification_details_column">
        <p class="notification_details_title">Email:</p>
        <a href="<?php echo sprintf('mailto:%s', esc_html($email)); ?>"><?php echo esc_html($email); ?></a>
    </div>
</div>