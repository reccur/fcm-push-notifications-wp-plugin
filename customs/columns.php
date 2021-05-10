<?php
defined('ABSPATH') OR die('Sorry, you can\'t do what you want to do !!.');

function registeredDevicesColumns( $columns ) {
	$cols = array();
	$cols['cb'] = "Multiselect";
	$cols['email'] = 'Email';
	$cols['device_token'] = 'Token';
	$cols['device'] = 'Device';
	$cols['os_version'] = 'OS Version';
	$cols['taxonomy-subscriptions'] = 'Subscribed To';
	$cols['added_on'] = 'Added On';
	return $cols;
}

function FCMCustomCoulums( $column, $post_id ){

	switch( $column ){

		case 'email' :
		echo get_the_title( $post_id );
		break;

		case 'device_token' :
		$device_token = pushnotification_get_column_data( $post_id, 'device_token');
		echo "<span title='{$device_token}'>" . mb_strimwidth( $device_token, 0, 35, '...' ) . "</span>";
		break;

		case 'device' :
		$device = pushnotification_get_column_data( $post_id, 'device');
		echo $device;
		break;

		case 'os_version' :
		$os_version = pushnotification_get_column_data( $post_id, 'os_version');
		echo $os_version;
		break;

		case 'subscribed' :
		$subscribed = get_subscribed_term_names( $post_id, 'subscriptions' ) ;
		echo $subscribed;
		break;

		case 'added_on' :
		$added_on = get_the_date( get_option( 'date_format' ), $post_id );
		echo $added_on;
		break;
	}
}

function makeFCMCustomCoulumsSortable( $columns ) {
	return wp_parse_args(array(
		'added_on'   => 'orderby',
		'taxonomy-subscriptions' => 'orderby',
		'os_version' => 'orderby',
		'device'     => 'orderby',
		'email'      => 'orderby'
	), $columns );
}