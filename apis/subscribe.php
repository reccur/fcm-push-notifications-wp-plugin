<?php

defined('ABSPATH') or die('Sorry, you can\'t do what you want to do !!.');

add_action('rest_api_init', 'subsbcribeAPIHook');

function subsbcribeAPIHook(){

	register_rest_route(
		'nrb',
		'/fcm/subscribe/',
		array(
			'methods'  => 'GET',
			'callback' => 'subscribeAPICallback',
			'permission_callback' => '__return_true'
		)
	);

	function subscribeAPICallback($request){

		$fields = ['api_secret_key', 'email', 'device_token', 'subscribed'];

		foreach ($fields as $field) {
			if (!isset($request[$field])) {
				echo json_encode(['status' => 'error', 'msg' => 'required parameters missing, please read the documentation!']);
				return;
			}
		}

		$pushnotification_api_secret_key  = sanitize_text_field($request['api_secret_key']);
		$pushnotification_email      = sanitize_text_field($request['email']);
		$pushnotification_device_token    = sanitize_text_field($request['device_token']);
		$pushnotification_subscribed      = sanitize_text_field($request['subscribed']);
		$pushnotification_device          = sanitize_text_field($request['device_name']);
		$pushnotification_os_version      = sanitize_text_field($request['os_version']);

		$postarr = array(
			'post_title'    => $pushnotification_email,
			'post_author'   => 1,
			'post_status'   => 'publish',
			'post_type'     => 'fcm_device',
		);

		if (get_option('FCM_API_SECRET_KEY') !== $pushnotification_api_secret_key) {

			$res = array('status' => 'warning', 'message' => 'incorrect api secret key passed');
			return $res;
		} else if (pushnotification_token_exists($pushnotification_device_token)) {

			$res = array('status' => 'warning', 'message' => 'device token already exists');
			return $res;
		} else {

			$post_id = wp_insert_post($postarr, false);

			wp_set_object_terms($post_id, $pushnotification_subscribed, 'subscriptions');

			pushnotification_insert_data(
				array(
					'post_id'						=> $post_id,
					'pushnotification_device_token'		=> $pushnotification_device_token,
					'pushnotification_device'			=> $pushnotification_device,
					'pushnotification_os_version'		=> $pushnotification_os_version
				)
			);

			$res = array('status' => 'ok', 'message' => 'device token registered', 'registered_id' => $post_id);
			return $res;
		}
	}
}
