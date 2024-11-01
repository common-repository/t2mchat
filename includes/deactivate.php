<?php

if( !function_exists('add_action') ) {
    die('Hi there, I am just a plugin not much i can do when called directly');
}

function t2m_deactivate_plugin(){
    global $wpdb;  
    $table_chat = $wpdb->prefix . "t2mchat";
    $wpdb->query("DROP TABLE IF EXISTS $table_chat"); 
    //table for language id's
    $table_name_t2mlang = $wpdb->prefix . "t2mchatlanguage";
    $wpdb->query("DROP TABLE IF EXISTS $table_name_t2mlang"); 
    //table for keys
    $table_name_t2mkeys = $wpdb->prefix . "t2mkeys";
    $wpdb->query("DROP TABLE IF EXISTS $table_name_t2mkeys");
    //table for tokens
    $table_name_t2mtokens = $wpdb->prefix . "t2mtokens";
    $wpdb->query("DROP TABLE IF EXISTS $table_name_t2mtokens");
    //table for jwt
    $table_name_t2mjwts = $wpdb->prefix . "t2mjwts";
    $wpdb->query("DROP TABLE IF EXISTS $table_name_t2mjwts");
     
    $userTableName = $wpdb->prefix . "users";
    $wpdb->query("ALTER TABLE $userTableName DROP chat_categories, DROP account_id");

}
?>