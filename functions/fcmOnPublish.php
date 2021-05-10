<?php

defined('ABSPATH') or die('Sorry, you can\'t do what you want to do !!.');

if (!function_exists('wp_get_current_user')) {
	include(ABSPATH . "wp-includes/pluggable.php");
}

// Sending push notification if a blog is posted
$blog_as_notification = esc_attr(get_option('blog_as_notification'));
if ($blog_as_notification == 1) {
	if (current_user_can('administrator')) {
		add_action('publish_post', 'sendPublishedNotification', 10, 2);
	}
}

// Sending push notification from chosen CPT
if (current_user_can('administrator')) {
	add_action('admin_init', 'sendPushNotificationForCPT', 10, 1);

	function sendPushNotificationForCPT()
	{
		$args = array(
			'public'   => true,
			'_builtin' => false
		);
		/*names or objects, note names is the default*/
		$output = 'objects';
		/*'and' or 'or'*/
		$operator = 'and';

		// die("here");

		$post_types = get_post_types($args, $output, $operator);

		if ($post_types) {
			foreach ($post_types  as $post_type) {
				$options = get_option('cpt_as_notification') ? get_option('cpt_as_notification') : array('null');
				$cpt = $post_type->rewrite['slug'];

				if (in_array($cpt, $options)) {
					add_action('publish_' . $cpt, 'sendPublishedNotification', 10, 2);
				}
			}
		}
	}
}

// Sending push notification from Custom Push Notification Menu
if (current_user_can('administrator')) {
	add_action('publish_push_notification', 'sendPublishedNotification', 10, 2);
}

function sendPublishedNotification($ID = null, $post = null){

	// selected category slugs as topics
	$topics = getTopics($post);

	if (isset($_POST['push_notification_topic']) && $_POST['push_notification_topic'] != '') {
		array_push($topics, $_POST['push_notification_topic']);
	}

	// $title = $post->post_title;
	// $message = get_the_excerpt($post);
	// $image = get_the_post_thumbnail_url($post, 'push_notification_image');
	// $post_id = $post->ID;

	// $push = new PushMessage($title, $message, $image, $post_id);

	$message = array(
		'post_id'       => $post->ID, 
		'title'         => $post->post_title, 
		'message'       => get_the_excerpt($post), 
		'image'         => get_the_post_thumbnail_url($post, 'push_notification_image'), 
		'post_date'     => $post->post_date, 
		'post_modified' => $post->post_modified, 
	);
	$push = new PushMessage($message);

	// getting the push from push object
	$pushNotification = $push->getNotification();

	// creating firebase class object
	$firebase = new Firebase();

	// send notification if the pd android fcm metabox option is checked for each blog post or custom post type
	if (isset($_POST['send_push_notification_chk']) && $_POST['send_push_notification_chk'] == 'on') {
		sendPushNotificationToSubscribersOnly($firebase, $pushNotification, $topics);
	}

	// send notification if the push_notification cpt page send this notification 
	require_once(ABSPATH . 'wp-admin/includes/screen.php');
	$currentScreen = get_current_screen();

	// echo "<pre>";
	// print_r($currentScreen);
	// die();

	if ($currentScreen->id === "push_notification") {
		sendPushNotificationToSubscribersOnly($firebase, $pushNotification, $topics);
	}
}

function sendPushNotificationToSubscribersOnly($firebase = null, $message = null, $topics=array()){

	// echo "<pre>";
	// print_r($topics);
	// die();

	$firebase->sendToTopicSubscribers($message, $topics);
	// $firebase->send($device->device_token, $message);
	// need to check if the device is subscribed
	// foreach (allRegisteredDevices() as $device) {
	// 	$firebase->send($device->device_token, $message);
	// }
}


// set topics as selected categories
function getTopics($post=null){
	$categories = get_the_terms($post->ID, 'category');
	$topics = array();
	foreach ($categories as $category) {
		array_push($topics, $category->slug);
	}

	// also add custom post type slug to topic
	$cpt = get_post_type( $post->ID );
	array_push($topics, $cpt);

	return $topics;
}