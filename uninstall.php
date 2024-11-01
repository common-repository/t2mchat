<?php

if(! defined ('WP_UNINSTALL_PLUGIN')){

   exit;
}
    
global $wpdb;
$table_namet2m = $wpdb->prefix . "t2mchat";
$wpdb->query("DROP TABLE IF EXISTS $table_namet2m");

// $userTableName = $wpdb->prefix . "users";
// $wpdb->query("ALTER TABLE $userTableName DROP chat_categories");

?>