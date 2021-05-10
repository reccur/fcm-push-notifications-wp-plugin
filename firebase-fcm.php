<?php
/*
* @package firebase-push-notifications
* Plugin Name: Firebase Push Notifications
* Plugin URI: https://www.nishanhitang.com.np/
* Description: Firebase Push Notifications is a plugin through which you can send push notifications directly from your WordPress site to android devices via Firebase Cloud Messagingservice. When a new blog is posted or existing blog is updated, a push notification sent to android device.
* Version: 1.1.8
* WC requires at least: 3.4.2
* WC tested up to: 4.6.1
* Author: Nishan Hitang
* Author URI: https://nishanhitang.com.np/
* Licence: GPLv2 or Later
* Text Domain: firebase-push-notifications
*/

/** 
 * Version checks
 */
global $wp_version;

if (version_compare($wp_version, '4.0', '<')) {
	wp_die(
		'Sorry, <b>Firebase Push Notifications</b> plugin requires WordPress 4.0 or newer. <p></p>',
		'Warning !!',
		['back_link' => true]
	);
}

if (version_compare(phpversion(), '5.6', '<')) {
	wp_die(
		'Sorry, <b>Firebase Push Notifications</b> plugin requires php version 5.6 or above',
		'Warning !!',
		['back_link' => true]
	);
}
/**
 * * ABSPATH check
 */
defined('ABSPATH') || wp_die('Sorry, you can\'t do what you want to do !!.', 'Warning !!', ['back_link' => true]);

/**
 * pd Android FCM Plugin Class
 */
if (!class_exists('FirebaseMessaging')) {
	class FirebaseMessaging
	{
		public function __construct()
		{
			add_action('init', [$this, 'fcmDeviceCPT']);
			add_action('init', [$this, 'pushnotification_send_push_notification_cpt']);
			// add_action('init', [$this, 'create_subscriptions_hierarchical_taxonomy'], 0);
			/*Registering Custom Columns*/
			add_filter('manage_pushnotification_posts_columns', 'registeredDevicesColumns');
			add_action('manage_pushnotification_posts_custom_column', 'FCMCustomCoulums', 10, 2);
			add_action('manage_edit-pushnotification_sortable_columns', 'makeFCMCustomCoulumsSortable', 10, 1);

			/*Registering Custom Metaboxes*/
			add_action('add_meta_boxes', 'addPushNotificationsMetaBox', 10, 1);
			/*add_action( 'save_post', 'pushnotification_device_data' );*/
			add_filter('plugin_action_links', [$this, 'pushnotification_plugin_add_settings_link'], 10, 5);
			add_filter('bulk_actions-edit-post', [$this, 'bulk_action']);
			//check for this
			add_filter('post_row_actions', [$this, 'remove_row_actions'], 10, 2);
			// wp footer
			add_action('admin_footer', [$this, 'fcm_js_scripts']);
		}

		public function activate(){
			/*register custom database*/
			$this->create_pushnotification_table();
			/*flush rewrite rules to avoid rare conflicts*/
			flush_rewrite_rules();
		}

		public function remove_row_actions($actions, $post){
			if ($post->post_type == 'fcm_device') {
				unset($actions['edit']);
				unset($actions['view']);
				unset($actions['trash']);
				unset($actions['inline hide-if-no-js']);
				return $actions;
			} else {
				return $actions;
			}
		}

		public function bulk_action($actions){
			$actions['send_fcm_notification'] = 'Send FCM Notification';
			return $actions;
		}

		public function fcmDeviceCPT(){

			$post_type = 'fcm_device';

			$labels = [
				'name' 					=> __('All Registered Devices'),
				'singular_name' 		=> __('Device'),
				'add_new'				=> __('Add New Device'),
				'add_new item'			=> __('Add New Device'),
				'search_items' 			=> __('Search for Email'),
				'edit_item'				=> __('Edit Device'),
				'new_item' 				=> __('Device'),
				'menu_name'				=> __('Firebase FCM'),
				'all_items'          	=> __('Registered Devices'),
				'name_custom_bar'		=> __('Device'),
				'not_found'           	=> __('No device(s) found'),
				'not_found_in_trash'  	=> __('No device(s) found in Trash')
			];

			$args = [
				'labels'				=> $labels,
				'show_ui'				=> true,
				'show_in_menu'			=> true,
				'capability_type'		=> 'post',
				'hierarchical'			=> false,
				'menu_position'			=> 27,
				'public'				=> false,
				'has_archive'			=> false,
				'publicaly_querable'	=> false,
				'query_var'				=> false,
				'supports'				=> false,
				// 'taxonomies'			=> array('subscriptions'),
				'capabilities' => array(
					'create_posts' 		=> false
				),
				'map_meta_cap'        	=> true
			];

			if (current_user_can('manage_woocommerce') || current_user_can('activate_plugins')) {
				register_post_type($post_type, $args);
			}
		}

		public function pushnotification_send_push_notification_cpt()
		{

			$post_type = 'push_notification';

			$labels = [
				'name' 					=> __('Notifications'),
				'singular_name' 		=> __('Notification'),
				'add_new'				=> __('Create New Notification Message'),
				'add_new item'			=> __('Create New Notification Message'),
				'search_items' 			=> __('Search for Sent Notification'),
				'edit_item'				=> __('Edit Notification'),
				'new_item' 				=> __('Notification'),
				'menu_name'				=> __('Push Notifications'),
				'name_custom_bar'		=> __('Notifications '),
				'not_found'           	=> __('No Notification(s) found'),
				'not_found_in_trash'  	=> __('No Notification(s) found in Trash')
			];

			$args = [
				'labels'				=> $labels,
				'show_ui'				=> true,
				'show_in_menu'			=> true,
				'capability_type'		=> 'post',
				'hierarchical'			=> false,
				'public'				=> false,
				'has_archive'			=> false,
				'publicaly_querable'	=> false,
				'query_var'				=> false,
				'menu_position'			=> 28,
				'supports'				=> array('title', 'excerpt', 'featured_image', 'thumbnail'),
				'show_in_menu' 			=> 'edit.php?post_type=fcm_device',
				'taxonomies'			=> array('subscriptions')
			];

			if (current_user_can('manage_woocommerce') || current_user_can('activate_plugins')) {
				register_post_type($post_type, $args);
			}
		}

		// public function create_subscriptions_hierarchical_taxonomy()
		// {

		// 	$labels = [
		// 		'name' 					=> _x('Subscriptions', 'taxonomy general name'),
		// 		'singular_name' 		=> _x('Subscription', 'taxonomy singular name'),
		// 		'search_items' 			=>  __('Search Subscriptions'),
		// 		'all_items' 			=> __('All Subscriptions'),
		// 		'parent_item' 			=> __('Parent Subscription'),
		// 		'parent_item_colon' 	=> __('Parent Subscription:'),
		// 		'edit_item' 			=> __('Edit Subscription'),
		// 		'update_item' 			=> __('Update Subscription'),
		// 		'add_new_item' 			=> __('Add New Subscription'),
		// 		'new_item_name' 		=> __('New Subscription Name'),
		// 		'menu_name' 			=> __('Subscriptions'),
		// 	];

		// 	register_taxonomy(
		// 		'subscriptions',
		// 		[],
		// 		[
		// 			'hierarchical' 		=> true,
		// 			'parent_item'  		=> false,
		// 			'parent_item_colon' => false,
		// 			'labels' 			=> $labels,
		// 			'show_ui' 			=> true,
		// 			'show_admin_column' => true,
		// 			'show_in_rest' 		=> true,
		// 			'query_var' 		=> true,
		// 			'rewrite' 			=> array('slug' => 'subscriptions'),
		// 		]
		// 	);
		// }


		public function create_pushnotification_table(){
			require_once plugin_dir_path(__FILE__) . 'db.php';
		}

		public function fcm_js_scripts()
		{
?>
			<script>
				let generatePdFcmApi = document.getElementById('generatePdFcmApi');
				if (generatePdFcmApi) {
					generatePdFcmApi.addEventListener('click', function(e) {
						e.preventDefault();
						let ajaxUrl = '<?php echo admin_url("admin-ajax.php") ?>';
						let xhttp = new XMLHttpRequest();
						xhttp.onreadystatechange = function() {
							if (this.readyState == 4 && this.status == 200) {
								let StringResponse = xhttp.responseText;
								let jsonResponse = JSON.parse(StringResponse);
								if (jsonResponse.success === true) {
									document.getElementById("FCM_API_SECRET_KEY").value = jsonResponse.data;
								}
							}
						}
						let params = '?action=generate_fcm_api';
						xhttp.open("GET", ajaxUrl + params, true);
						xhttp.send();
					});
				}
				const copy_url = (element) => {
					var text = document.querySelector(element);
					var selection = window.getSelection();
					var range = document.createRange();
					range.selectNodeContents(text);
					selection.removeAllRanges();
					selection.addRange(range);
					document.execCommand('copy');
				};
				document.getElementById('FCM_ENVIRONMENT').addEventListener('change', function(e) {
					document.getElementById('FIREBASE_API_KEY').closest('tr').style.display = 'none';
				});
			</script>
<?php
		}

		public function pushnotification_plugin_add_settings_link($actions, $plugin_file)
		{
			static $plugin;

			if (!isset($plugin))

				$plugin = plugin_basename(__FILE__);

			if ($plugin == $plugin_file) {

				$settings 	= ['settings' => '<a href="edit.php?post_type=fcm_device&page=settings">' . __('Settings') . '</a>'];

				$actions = array_merge($settings, $actions);
			}

			return $actions;
		}
	}
	$firebaseMessagingInstance = new FirebaseMessaging();
}

// activation
register_activation_hook(__FILE__, [$firebaseMessagingInstance, 'activate']);


foreach (glob(plugin_dir_path(__FILE__) . "customs/*.php") as $file) {
	include $file;
}

foreach (glob(plugin_dir_path(__FILE__) . "lib/*.php") as $file) {
	include $file;
}

foreach (glob(plugin_dir_path(__FILE__) . "apis/*.php") as $file) {
	include $file;
}

foreach (glob(plugin_dir_path(__FILE__) . "functions/*.php") as $file) {
	include $file;
}

require plugin_dir_path(__FILE__) . 'pages.php';
