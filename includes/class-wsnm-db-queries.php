<?php

class WSNM_DB_Queries{
    
    /**
     * helper
     *
     * @var mixed
     */
    public $helper;

    public $table_name = 'wsnm_actions';

    function __construct($helper){
        $this->helper = $helper;
    }

    public function add_manually_action($product_id){
        global $wpdb;
		$dbName = $wpdb->prefix.$this->table_name;
        $result = $wpdb->get_results(
			$wpdb->prepare("SELECT * from {$dbName} WHERE product_id = %s AND status = 0", $product_id)
		);
		if(!count($result)){
            $wpdb->insert( 
                $dbName, 
                array(
                    'product_id' => $product_id,
                    'status' => false,
                    'created_time' => current_time('mysql')
                )
            );
		}
    }

    public function is_action_in_progress($product_id){
        global $wpdb;
		$dbName = $wpdb->prefix.$this->table_name;
        $result = $wpdb->get_results(
			$wpdb->prepare("SELECT * from {$dbName} WHERE product_id = %s AND status = 0", $product_id)
		);
        if(count($result)){
            return true;
        }
        return false;
    }

    public function get_incomplete_actions(){
        global $wpdb;
		$dbName = $wpdb->prefix.$this->table_name;
        $actions = $wpdb->get_results("SELECT * from {$dbName} WHERE status = 0");
        return $actions;
    }

    public function mark_action_as_complete($id){
        global $wpdb;
		$dbName = $wpdb->prefix.$this->table_name;
        $wpdb->update(
            $dbName,
            array(
                'status' => true,
                'complete_time' => current_time('mysql')
            ),
            [ 'id' => $id ]
        );
    }
}