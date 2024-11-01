<?php

if( !function_exists('add_action') ) {
    die('Hi there, I am just a plugin not much I can do when called directly');
}

//this function runs upon plugin activation and not on installation
function t2m_activate_plugin(){   
    global $wpdb;
    // create a table named t2mchat
    $table_name_t2m = $wpdb->prefix . "t2mchat";
    //create table for user language
    $table_name_t2mlang = $wpdb->prefix . "t2mchatlanguage";
    //create table for keys
    $table_name_t2mkeys = $wpdb->prefix . "t2mkeys";
    //create table for token
    $table_name_t2mtokens = $wpdb->prefix . "t2mtokens";
    //create table for JWT
    $table_name_t2mJwts = $wpdb->prefix . "t2mjwts";
    $charset_collate_is = $wpdb->get_charset_collate();

    //table definitions
    $createTableT2mchat =
    "CREATE TABLE $table_name_t2m (
        ID enum('1') NOT NULL,
        ClientID VARCHAR(255) NOT NULL,
        ClientSecret TEXT NOT NULL,
        ClientService TEXT NOT NULL,
        PRIMARY KEY (ID)
    )$charset_collate_is;";

    $createTableT2mlang =
    "CREATE TABLE $table_name_t2mlang (
        ID INT NOT NULL AUTO_INCREMENT,
        UserID TEXT NOT NULL,
        UserLanguageID TEXT NOT NULL,
        PRIMARY KEY (ID)
    )$charset_collate_is;";

    $createTableKeys =
    "CREATE TABLE $table_name_t2mkeys (
        ID INT NOT NULL AUTO_INCREMENT,
        key_data VARCHAR(255) NOT NULL,
        PRIMARY KEY (ID),
        INDEX (key_data)
    )$charset_collate_is;";

    $createTableTokens =
    "CREATE TABLE $table_name_t2mtokens (
        ID INT NOT NULL AUTO_INCREMENT,
        token VARCHAR(255) NOT NULL,
        data VARCHAR(255) NOT NULL,
        PRIMARY KEY (ID),
        INDEX (token)
    )$charset_collate_is;";

     $createTableJwts =
    "CREATE TABLE $table_name_t2mJwts (
        ID INT NOT NULL AUTO_INCREMENT,
        jwt VARCHAR(255) NOT NULL,
        PRIMARY KEY (ID),
        INDEX (jwt)
    )$charset_collate_is;";
    
    //require this for altering WP Database
    require_once( ABSPATH . '/wp-admin/includes/upgrade.php' );
    //table creation here, aborts if exists
    maybe_create_table($table_name_t2m, $createTableT2mchat);
    maybe_create_table($table_name_t2mlang, $createTableT2mlang);
    maybe_create_table($table_name_t2mkeys, $createTableKeys);
    maybe_create_table($table_name_t2mtokens, $createTableTokens);
    maybe_create_table($table_name_t2mJwts, $createTableJwts);

    //adding columns account_id and chat_categories to users table; executes only if they dont exist
    $userTableName = $wpdb->prefix . "users";
    $checkColCategories= $wpdb->query(  "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE table_name = $userTableName AND column_name = 'chat_categories'" );
    if(empty($checkColCategories)){
        $wpdb->query("ALTER TABLE $userTableName ADD account_id TEXT NOT NULL, ");  
    }
    $checkColAccountId= $wpdb->query(  "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE table_name = $userTableName AND column_name = 'account_id'" );
    if(empty($checkColAccountId)){
        $wpdb->query("ALTER TABLE $userTableName ADD chat_categories TEXT NOT NULL");  $createChat_Categories = $wpdb->query("ALTER TABLE $userTableName ADD account_id TEXT NOT NULL");
    }
}
?>