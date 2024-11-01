<?php
/*
* Plugin Name: T2MChat
* Description: Multilingual chat system with analytics and chatbot
* Version: 1.2
* Author: TechstackSolutions
* Author URI: techstacksolutions.com
* Text Domain: t2mchat
* License: GPLv2 or later
*/

if( !function_exists('add_action') ) {
    die('Hi there, I am just a plugin, not much I can do when called directly.');
}

//includes
include('includes/library/vendor/autoload.php');
include('includes/activate.php');
include('includes/deactivate.php');
include('includes/t2m-react-route.php');
include('includes/t2m-config-menu.php');
include('includes/t2m-handle-form.php');
include('includes/t2m-chat-category.php');
include('includes/t2m-user-category.php');
include('includes/t2m-frontend-chatapp.php');
include('includes/t2m-chat-app.php');

// activation/deactivation hooks
register_activation_hook(__FILE__, 't2m_activate_plugin');
register_deactivation_hook(__FILE__, 't2m_deactivate_plugin');

//shortcodes
//---------

//actions
//admin menu for t2m
add_action('admin_menu','t2m_admin_menu_option');
//script and style enqueue for admin pages
add_action( 'admin_enqueue_scripts', 't2m_be_style' );
//script and style enqueue for WP frontend pages
add_action( 'wp_enqueue_scripts', 't2m_fe_style' );

add_action('rest_api_init',  function () {
    $routes = new T2mchat\Handleform\HandleForm();
    $routes->register_routes();
 });

//custom endpoint class objects
//getting and posting chat categories
$chat_category = new chatCategory();  
$chat_app = new chatApp();
//handling token
//$t2m_token = new T2mchat\TokenHandler\TokenHandler();

// update/delete user hooks
$uduser = new T2mchat\UserCategory\UserCategory();
add_action( 'delete_user', array($uduser, 'UserDelete'));
add_action( 'deleted_user', array($uduser, 'UserDeleted'));
add_action( 'profile_update', array($uduser, 'UserUpdate'));

//adding fe_chat_app function to the theme header file to render react frontend
add_action( 'wp_head', 'fe_chat_app');

//functions
//admin menu pages
function t2m_admin_menu_option(){
    add_menu_page('T2M Configuration', 'T2M', 'manage_options', 't2m-config-menu', 't2m_menu_page','dashicons-admin-generic',200);
    add_submenu_page( 't2m-config-menu', 'T2M Configuration', 'Config', 'manage_options', 't2m-config-menu', 't2m_menu_page' );
    add_submenu_page( 't2m-config-menu', 'T2M Administration', 'Administration', 'manage_options', 't2m-admin-menu', 't2m_react_route' );
}

//styles and scripts
function t2m_be_style(){
    
   if($_GET["page"] == "t2m-config-menu"){
        wp_enqueue_script('t2m_stylesheet', plugins_url('/assets/bootstrap.min.js', __FILE__));
        wp_enqueue_style('t2m_stylesheet', plugins_url('/assets/bootstrap.min.css', __FILE__));
    }
    elseif($_GET["page"] == "t2m-admin-menu"){
        wp_enqueue_script('t2m_stylesheet', plugins_url('/administrator/js/build/backend.js', __FILE__), array(), false, true);
        wp_enqueue_style('t2m_stylesheet', plugins_url('/administrator/js/build/backend.css', __FILE__), array(), null);
        wp_localize_script( 't2m_stylesheet', 't2mlocalobject', array(
            'root' => esc_url_raw( rest_url() ),
            'homeurl' => esc_url_raw( home_url() ),
            't2mchatJwt' => t2m_get_jwt()
        ));
    }
}

function t2m_fe_style(){ 
    $chat_category = new chatCategory();  
    $chat_app = new chatApp();
    wp_enqueue_script('t2m_fe_scripts', plugins_url('/administrator/js/build/frontend.js', __FILE__), array(), false, true);
    wp_enqueue_style('t2m_fe_stylesheet', plugins_url('/administrator/js/build/frontend.css', __FILE__), array(), null);
    wp_localize_script( 't2m_fe_scripts', 't2mlocalobject', array(
        'root' => esc_url_raw( rest_url() ),
        'homeurl' => esc_url_raw( home_url()),
        't2mchatJwt' => t2m_get_jwt(),
        'nonce' => wp_create_nonce('wp_rest'),
        'tm2chatUser' => $chat_app->get_curr_user(),
        'tm2chatLangId' => $chat_app->get_user_lang()
	));
}

function t2m_get_jwt(){
    global $wpdb;
    $table_name = $wpdb->prefix . "t2mkeys";
    $key = $wpdb->get_row("SELECT key_data FROM $table_name limit 1");
    if ($key==null) {
        $key = md5(uniqid(mt_rand(), true));
        $wpdb->insert("$table_name", 
        array(
            'key_data' => $key
        ),
        array(
            '%s'
            ) 
        ); 
    } else {
        $key = $key->key_data;
    }
    $jwt = \Firebase\JWT\JWT::encode(
        array(
            "iss" => "t2mchat",
            "iat" => time(),
            "exp" => time() + (60 * 60)
        ), $key);
    $table_name_jwt = $wpdb->prefix . "t2mjwts";
    $wpdb->insert("$table_name_jwt", 
        array(
            'jwt' => $jwt
        ),
        array(
            '%s'
        ) 
    ); 
    return $jwt;
}

?>