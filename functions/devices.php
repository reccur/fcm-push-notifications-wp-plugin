<?php

defined('ABSPATH') OR die('Sorry, you can\'t do what you want to do !!.');

function allRegisteredDevices(){
	global $wpdb;
	$table = $wpdb->prefix.'fcm_registered_devices';
	$result = $wpdb->get_results( "SELECT * FROM {$table}" );
	return $result;
}
