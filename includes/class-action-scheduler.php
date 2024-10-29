<?php

class WSNM_Scheduler
{
    /**
     * helper
     *
     * @var mixed
     */
    public $helper;

    /**
	 * Array of seconds for common time periods
	 *
	 * @var array
	 */
	private static $time_periods;

    function __construct($helper){
        $this->helper = $helper;

        self::$time_periods = array(
			array(
				'seconds' => YEAR_IN_SECONDS,
				/* translators: %s: amount of time */
				'names'   => _n_noop( '%s year', '%s years', 'back-in-stock-notifications-for-woocommerce' ),
			),
			array(
				'seconds' => MONTH_IN_SECONDS,
				/* translators: %s: amount of time */
				'names'   => _n_noop( '%s month', '%s months', 'back-in-stock-notifications-for-woocommerce' ),
			),
			array(
				'seconds' => WEEK_IN_SECONDS,
				/* translators: %s: amount of time */
				'names'   => _n_noop( '%s week', '%s weeks', 'back-in-stock-notifications-for-woocommerce' ),
			),
			array(
				'seconds' => DAY_IN_SECONDS,
				/* translators: %s: amount of time */
				'names'   => _n_noop( '%s day', '%s days', 'back-in-stock-notifications-for-woocommerce' ),
			),
			array(
				'seconds' => HOUR_IN_SECONDS,
				/* translators: %s: amount of time */
				'names'   => _n_noop( '%s hour', '%s hours', 'back-in-stock-notifications-for-woocommerce' ),
			),
			array(
				'seconds' => MINUTE_IN_SECONDS,
				/* translators: %s: amount of time */
				'names'   => _n_noop( '%s minute', '%s minutes', 'back-in-stock-notifications-for-woocommerce' ),
			),
			array(
				'seconds' => 1,
				/* translators: %s: amount of time */
				'names'   => _n_noop( '%s second', '%s seconds', 'back-in-stock-notifications-for-woocommerce' ),
			),
		);
    }

    public function schedule_action(){
        $mode = $this->helper->get_mode();
        switch ($mode) {
            case 'automatically':
                if ( false === as_has_scheduled_action( 'wsnm_run_automatically', array(), 'wsnm' ) ) {
                    //Interval in seconds: 1800s -> 30 min  
                    as_schedule_recurring_action( time(), 1800, 'wsnm_run_automatically', array(), 'wsnm', true );
                }
                as_unschedule_all_actions( 'wsnm_run_manually', array(), 'wsnm' );
                break;
            case 'manually':
                if ( false === as_has_scheduled_action( 'wsnm_run_manually', array(), 'wsnm' ) ) {
                    //Interval in seconds: 1800s -> 30 min  
                    as_schedule_recurring_action( time(), 1800, 'wsnm_run_manually', array(), 'wsnm', true );
                }
                as_unschedule_all_actions( 'wsnm_run_automatically', array(), 'wsnm' );
                break;
            default:
                break;
        }
    }

    public function wsnm_run_manually(){
        $incomplete_actions = $this->helper->db_queries->get_incomplete_actions();
        foreach($incomplete_actions as $action){
            $product = wc_get_product($action->product_id);
            if($product){
                if ($product->is_type('simple')) {
                    if ($product->is_in_stock()) {
                        $notifications = $this->helper->getNotificationsBySimpleProduct($product);
                        if ($notifications->have_posts()) {
                            while ($notifications->have_posts()) : $notifications->the_post();
                                $result = $this->helper->the_notification_email(get_the_ID());
                                if($result){
                                    update_post_meta(get_the_ID(), 'wsnm_status', 'sent');
                                    update_post_meta(get_the_ID(), 'wsnm_action_id', $action->id);
                                }
                            endwhile;
                            wp_reset_postdata();
                        }
                    }
                }
                if ($product->is_type('variable')) {
                    $available_variations = $product->get_children();
                    foreach ($available_variations as $key => $value) {
                        $variation_obj = new WC_Product_variation($value);
                        if ($variation_obj->is_in_stock()) {
                            $notifications = $this->helper->getNotificationsByVariation($product, $variation_obj);
                            if ($notifications->have_posts()) {
                                while ($notifications->have_posts()) : $notifications->the_post();
                                    $result = $this->helper->the_notification_email(get_the_ID());
                                    if($result){
                                        update_post_meta(get_the_ID(), 'wsnm_status', 'sent');
                                        update_post_meta(get_the_ID(), 'wsnm_action_id', $action->id);
                                    }
                                endwhile;
                                wp_reset_postdata();
                            }    
                        }
                    }
                }
            }
            $this->helper->db_queries->mark_action_as_complete($action->id);
        }
    }

    public function wsnm_run_automatically(){
        $subscriptions = new WP_Query(
            array(
                'post_type' => 'wsnm_notify_me',
                'posts_per_page' => -1,
                'meta_query' => array(
                    array( 
                        array('key' => 'wsnm_status', 'value' => 'pending')
                    )
                )
            )
        );
        if ($subscriptions->have_posts()) {
            while ($subscriptions->have_posts()):
                $subscriptions->the_post();
                $product = $this->helper->getNotificationSelectedProduct(get_the_ID());
                $parent_product_id = $this->helper->get_notification_product_value(get_the_ID());
                if(is_a( $product, 'WC_Product_Variation' ) || is_a( $product, 'WC_Product_Simple' )){
                    if($product->is_in_stock() && !$this->helper->is_product_paused($parent_product_id)){
                        $result = $this->helper->the_notification_email(get_the_ID());
                        if($result){
                            update_post_meta(get_the_ID(), 'wsnm_status', 'sent');
                            update_post_meta(get_the_ID(), 'wsnm_action_id', 'automatically');
                            update_post_meta(get_the_ID(), 'wsnm_action_time', current_time('mysql'));
                        }
                    }
                }
            endwhile;
            wp_reset_postdata();
        }
    }

    public function next_run($hook){
        $next_timestamp = as_next_scheduled_action($hook, array(), 'wsnm');
        if(is_int($next_timestamp)){
            $schedule_display_string = date( 'Y-m-d H:i:s O', $next_timestamp );
    
            if ( gmdate( 'U' ) > $next_timestamp ) {
                $schedule_display_string .= sprintf( __( ' (%s ago)', 'back-in-stock-notifications-for-woocommerce' ), self::human_interval( gmdate( 'U' ) - $next_timestamp ) );
            } else {
                $schedule_display_string .= sprintf( __( ' (%s)', 'back-in-stock-notifications-for-woocommerce' ), self::human_interval( $next_timestamp - gmdate( 'U' ) ) );
            }
            return $schedule_display_string;
        }
        return false;
    }

	/**
	 * Inspired by the Crontrol::interval() function by Edward Dale: https://wordpress.org/plugins/wp-crontrol/ and 
     * Woocommerce
	 *
	 * @param int $interval A interval in seconds.
	 * @param int $periods_to_include Depth of time periods to include, e.g. for an interval of 70, and $periods_to_include of 2, both minutes and seconds would be included. With a value of 1, only minutes would be included.
	 * @return string A human friendly string representation of the interval.
	 */
	private static function human_interval( $interval, $periods_to_include = 2 ) {

		if ( $interval <= 0 ) {
			return __( 'Now!', 'back-in-stock-notifications-for-woocommerce' );
		}

		$output = '';

		for ( $time_period_index = 0, $periods_included = 0, $seconds_remaining = $interval; $time_period_index < count( self::$time_periods ) && $seconds_remaining > 0 && $periods_included < $periods_to_include; $time_period_index++ ) {

			$periods_in_interval = floor( $seconds_remaining / self::$time_periods[ $time_period_index ]['seconds'] );

			if ( $periods_in_interval > 0 ) {
				if ( ! empty( $output ) ) {
					$output .= ' ';
				}
				$output .= sprintf( _n( self::$time_periods[ $time_period_index ]['names'][0], self::$time_periods[ $time_period_index ]['names'][1], $periods_in_interval, 'back-in-stock-notifications-for-woocommerce' ), $periods_in_interval );
				$seconds_remaining -= $periods_in_interval * self::$time_periods[ $time_period_index ]['seconds'];
				$periods_included++;
			}
		}
		return $output;
	}
}