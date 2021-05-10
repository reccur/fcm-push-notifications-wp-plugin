<?php

defined('ABSPATH') OR die('Sorry, you can\'t do what you want to do !!.');

function pushnotification_insert_data($val){
	global $wpdb;
	$table = $wpdb->prefix.'fcm_registered_devices';

	$data = array(
		'post_ID'			=> $val['post_id'],
		'device_token' 		=> $val['pushnotification_device_token'],
		'device' 			=> $val['pushnotification_device'],
		'os_version' 		=> $val['pushnotification_os_version']
	);

	$format = array(
		'%d',
		'%s', 
		'%s',
		'%s'
	);

	$wpdb->insert( $table, $data, $format );
	return $wpdb->insert_id;
}

/**
 * @param passing data to delete values in the custom table of pushnotification
 * @since 1.0.0
 * @version 1.0.0
 */
function pushnotification_delete_data($id)
{
	global $wpdb;
	$table = $wpdb->prefix.'fcm_registered_devices';

	$wpdb->delete( $table, array( 'post_ID' => $id ) );
}

/**
 * @param passing data to get post_ID from custom table of pushnotification
 * @since 1.0.0
 * @version 1.0.0
 */
function pushnotification_get_device_post_id($device_token)
{
	global $wpdb;
	$table = $wpdb->prefix.'fcm_registered_devices';
	
	$thepost = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $table WHERE device_token = %s", $device_token ) );
	return $thepost->post_ID; 
}

/**
 * @param passing data to get values each column in the custom table of pushnotification
 * @since 1.0.0
 * @version 1.0.0
 */
function pushnotification_get_column_data($id, $key)
{
	global $wpdb;
	$table = $wpdb->prefix.'fcm_registered_devices';
	$result = $wpdb->get_row( "SELECT {$key} FROM {$table} WHERE post_ID = {$id}" );
	return $result->$key;
}

/**
 * @param passing token to see if token exists in pushnotification
 * @since 1.0.0
 * @version 1.0.0
 */
function pushnotification_token_exists($token)
{
	global $wpdb;
	$table = $wpdb->prefix.'fcm_registered_devices';
	$result = $wpdb->get_row( "SELECT * FROM {$table} WHERE device_token = '{$token}'" );
	if ($result)
		return true;
}

/**
 * @param passing token to see if token exists in pushnotification
 * @since 1.0.0
 * @version 1.0.0
 */
function pushnotification_email_exists($token)
{
	global $wpdb;
	$table = $wpdb->prefix.'fcm_registered_devices';
	$result = $wpdb->get_row( "SELECT * FROM {$table} WHERE device_token = '{$token}'" );
	if ($result)
		return true;
}

/**
 * @param deleting the entire device details for custom table when deleted from wp backend
 * @since 1.0.0
 * @version 1.0.0
 */
add_action( 'init', 'pushnotification_init_custom_table_row_del' );
function pushnotification_init_custom_table_row_del() {
    add_action( 'delete_post', 'pushnotification_delete_data_from_backend', 10 );
}

function pushnotification_delete_data_from_backend( $pid )
{
    global $wpdb;
    $table = $wpdb->prefix.'fcm_registered_devices';
    
    if ( $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $table WHERE post_ID = %d", $pid ) ) ) {
        $wpdb->delete( $table, array( 'post_ID' => $pid ) );
    }
}