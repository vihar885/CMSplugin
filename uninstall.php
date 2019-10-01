<?php

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit();
}

function ed_delete_plugin() {
	global $wpdb;

	delete_option( 'email-subscriber-widget' );
	delete_option( 'elp_cron_trigger_option' );
	delete_option( 'elp_cron_mailcount' );
	delete_option( 'elp_cron_adminmail' );
	delete_option( 'elp_c_cronurl' );
	delete_option( 'elp_c_sentreport_subject' );
	delete_option( 'elp_c_sentreport' );
	

	$wpdb->query( sprintf( "DROP TABLE IF EXISTS %s",
		$wpdb->prefix . 'elp_templatetable' ) );
		
	$wpdb->query( sprintf( "DROP TABLE IF EXISTS %s",
		$wpdb->prefix . 'elp_emaillist' ) );
		
	$wpdb->query( sprintf( "DROP TABLE IF EXISTS %s",
		$wpdb->prefix . 'elp_sendsetting' ) );
		
	$wpdb->query( sprintf( "DROP TABLE IF EXISTS %s",
		$wpdb->prefix . 'elp_sentdetails' ) );
		
	$wpdb->query( sprintf( "DROP TABLE IF EXISTS %s",
		$wpdb->prefix . 'elp_deliverreport' ) );
		
	$wpdb->query( sprintf( "DROP TABLE IF EXISTS %s",
		$wpdb->prefix . 'elp_pluginconfig' ) );
	
	$wpdb->query( sprintf( "DROP TABLE IF EXISTS %s",
		$wpdb->prefix . 'elp_postnotification' ) );
}

ed_delete_plugin();