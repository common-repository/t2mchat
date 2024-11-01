<?php

class chatApp {
    
    function __construct(){

        // add_action('init', array($this, 'get_loggedin_user'));

        //get the user if logged in
        add_action( 'rest_api_init', function () {
            register_rest_route( 't2mchat/v2', '/get_curr_user', array(
                'methods' => 'GET',
                'callback' => array($this, 'get_curr_user')
            ));
        });

        //get languages ID associated with the user
        add_action( 'rest_api_init', function () {
            register_rest_route( 't2mchat/v2', '/get_curr_user_lang', array(
                'methods' => 'GET',
                'callback' => array($this, 'get_user_lang')
            ));
        });

        //save language ID associated with the user
        add_action( 'rest_api_init', function () {
            register_rest_route( 't2mchat/v2', 'save_lang', array(
                'methods' => 'POST',
                'callback' => array($this, 't2m_save_u_lang')
            ));
        });

    }

    function get_curr_user(){    
        if(is_user_logged_in()){
            $user = wp_get_current_user();
            $role = $user->roles[0];
            $email = $user->data->user_email;
            $dname = $user->data->display_name;
            $id = $user->id;
            $data = array(
                "role" => $role,
                "email" => $email,
                "dname" => $dname
            );
            return $data;
        }
        else{
           return NULL;
        }
    }

    function get_user_lang(){
        if(get_current_user_id() === 0){
            return NULL;
        }
        else{
            $user_id = get_current_user_id();
            global $wpdb;
            $langTableName = $wpdb->prefix . "t2mchatlanguage";
            $languageID = $wpdb->get_col("SELECT UserLanguageID FROM $langTableName WHERE UserID=$user_id", 0);
            if (count($languageID) == 0) {
                return;
            }
            $languageIDs = json_decode($languageID[0]);
            return array_combine($languageIDs, $languageIDs);
        }
    }

    function t2m_save_u_lang(WP_REST_Request $request){
        global $wpdb;
        $TableName = $wpdb->prefix . "t2mchatlanguage";
        $parameters = $request->get_body();
        $arr = json_decode($parameters, true);
        $email = $arr["email"];
        $langIDs = json_encode($arr["keys"]);
        $user = get_user_by_email($email);
        if ($user == null) {
            return null;
        }
        try {
            $userId = $user->data->ID;
            $wpdb->delete($TableName, array('UserID' => $userId));
            $wpdb->insert($TableName, array("UserLanguageID" => $langIDs, "UserID" => $userId));
        } catch (Exception $e) {
            print_r($e);
            exit;
        }
    }
    
}
?>