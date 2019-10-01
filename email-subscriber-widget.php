<?php
/*
Plugin Name: email-subscriber-widget
Plugin URI: http://wordpress.viharp.sgedu.site
Description: The aim of this plugin is to generate a subscriber widget on a website and user can add their email id and name so they will receive offers and discount emails from client. in a backend, client can see their subscribers with their email id name and subscription date and time. they also can delete subscription.also, client can place widget on their website footer or sidebar. we have also provided option for that in backend.
Version: 1.0
Author: Vihar Patel
Author URI: http://wordpress.viharp.sgedu.site
License: open
*/



if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { die('You are not allowed to call this page directly.'); }

if (!session_id()) { session_start(); }
require_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'base'.DIRECTORY_SEPARATOR.'defined.php');
require_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'classes'.DIRECTORY_SEPARATOR.'stater.php');
add_action('admin_menu', array( 'elp_cls_registerhook', 'elp_adminmenu' ));
register_activation_hook(ELP_FILE, array( 'elp_cls_registerhook', 'elp_activation' ));
register_deactivation_hook(ELP_FILE, array( 'elp_cls_registerhook', 'elp_deactivation' ));
add_action( 'widgets_init', array( 'elp_cls_registerhook', 'elp_widget_loading' ));
add_shortcode( 'email-posts-subscribers', 'elp_shortcode' );
require_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'classes'.DIRECTORY_SEPARATOR.'directly.php');

add_action( 'admin_enqueue_scripts', array( 'elp_cls_registerhook', 'elp_load_scripts' ) );

function elp_textdomain() 
{
	  load_plugin_textdomain( 'email-subscriber-widget' , false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
}

add_action( 'user_register', 'elp_sync_registereduser');

add_action( 'transition_post_status', array( 'elp_cls_dbquerynote', 'elp_prepare_notification' ), 10, 3 );
add_action('plugins_loaded', 'elp_textdomain');

register_activation_hook(__FILE__, 'elp_cron_activation');
register_deactivation_hook(__FILE__, 'elp_cron_deactivation');
?>