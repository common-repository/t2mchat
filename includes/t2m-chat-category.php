<?php

class chatCategory{

    function __construct(){
    
    //get categories associated with a user
    add_action( 'rest_api_init', function () {
        register_rest_route('t2mchat/v2', '/get_users', array(
        'methods' => 'GET',
        'callback' => array($this, 'get_user_data')
        ));
    });

    //delete categories associated with a user
    add_action( 'rest_api_init', function () {
        register_rest_route('t2mchat/v2', '/delete_categories', array(
        'methods' => 'POST',
        'callback' => array($this, 'delete_user_data')
        ));
    });

    //save categories for a user
    add_action( 'rest_api_init', function () {
        register_rest_route( 't2mchat/v2', 'post_categories', array(
            'methods' => 'POST',
            'callback' => array($this, 'post_user_data')
        ));
    });
    }

    function get_user_data(WP_REST_Request $request){

        global $wpdb;
        $offset = $request->get_param("offset");
        $size = $request->get_param("size");
        $userTableName = $wpdb->prefix . "users";
        $records = $wpdb->get_results("SELECT display_name as 'label', user_email as 'key', user_pass as 'password' from $userTableName");
        $results = array("count" => $count, "result" => $records);
        return $results;
    }

    function delete_user_data(WP_REST_Request $request){

        global $wpdb;
        $userTableName = $wpdb->prefix . "users";
        $parameters = $request->get_body();
        $arr = json_decode($parameters, true);
        $email = $arr[0];
        $success = $wpdb->query("UPDATE $userTableName SET chat_categories = null, account_id = null WHERE user_email = '$email'");
        return $success;
    }

    function post_user_data(WP_REST_Request $request){

        global $wpdb;
        $userTableName = $wpdb->prefix . "users";
        $parameters = $request->get_body();
        $arr = json_decode($parameters, true);
        $results = [];
        for($i=0; $i<count($arr); $i++){
            $data = $arr[$i];
            $user_email = $data[email];
            $id = $data[id];
            $chat_categories_arr = $data[categories];
            $categories = json_encode($chat_categories_arr);
            $success = $wpdb->query("UPDATE $userTableName SET chat_categories = '$categories', account_id = '$id' WHERE user_email = '$user_email'");
            if($success){
                array_push($results, $arr[$i]);
            }   
        }
        return $results;
    }
}

?>