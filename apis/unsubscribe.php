<?php

defined('ABSPATH') or die('Sorry, you can\'t do what you want to do !!.');

add_action('rest_api_init', 'unsubscribeAPIHook');

function unsubscribeAPIHook(){

    register_rest_route(
        'nrb',
        '/fcm/unsubscribe/',
        array(
            'methods'  => 'GET',
            'callback' => 'unSubscribeAPICallback',
            'permission_callback' => '__return_true'
        )
    );

    function unSubscribeAPICallback($request)
    {
        $fields = [ 'api_secret_key', 'unsubscribe_with' ];

		foreach ($fields as $field) {
			if (!isset($request[$field])) {
				echo json_encode(['status' => 'error', 'msg' => 'required parameters missing, please read the documentation for correct parameters']);
				return;
			}
		}
        
        $pushnotification_unsubcribe_with = sanitize_text_field($request['unsubscribe_with']);
        $pushnotification_api_secret_key  = sanitize_text_field($request['api_secret_key']);
        $pushnotification_device_token    = sanitize_text_field($request['device_token']);

        if( get_option( 'FCM_API_SECRET_KEY' ) !== $pushnotification_api_secret_key ) {

			$res = array('status' => 'warning', 'message' => 'incorrect api secret key passed' );
			return $res;
		}
        
        // unregister the device
        if ($pushnotification_unsubcribe_with == 'token') {
            // checking for the parameter device_token
            if(!$pushnotification_device_token) {
                $res = array('status' => 'error', 'message' => 'device token not passed, please read the documentation for correct parameters');
                return $res;
            }
            // checking for token exist
            else if(! pushnotification_token_exists($pushnotification_device_token)) {
                $res = array('status' => 'error', 'message' => 'device token does not exist');
                return $res;
            }
            // deleting the device 
            else {
                $postid = pushnotification_get_device_post_id($pushnotification_device_token);
                // deleting the post
                wp_delete_post($postid, false);
                // delting the fcm data
                pushnotification_delete_data($postid);
                $res = array('status' => 'ok', 'message' => 'device token unregistered');
                return $res;
            }
        }        
        // unregister the user
        else if ($pushnotification_unsubcribe_with == 'email') {

            $pushnotification_email      = sanitize_text_field($request['email']);
            $posts = find_posts_by_email($pushnotification_email, 'pushnotification');
            if($posts){
                $deleted_ids = [];
                foreach ($posts as $post) {
                    // deleting the post
                    $deleted_ids[] = wp_delete_post($post->ID, false);
                    // delting the fcm data
                    pushnotification_delete_data($post->ID);
                }
                $res = array('status' => 'ok', 'message' => 'user with multiple devices unregistered');
                return $res;
            }
            else {
                $res = array('status' => 'error', 'message' => 'user email or token does not exist');
                return $res;
            }
        }
        else {
            $res = array('status' => 'error', 'message' => 'user email or token does not exist');
            return $res;
        }
    }
}

if (!function_exists('find_posts_by_email')) {
    function find_posts_by_email($page_title, $post_type = false, $output = OBJECT)
    {
        global $wpdb;
        //Handle specific post type?
        $post_type_where = $post_type ? 'AND post_type = %s' : '';
        //Query all columns so as not to use get_post()
        $results = $wpdb->get_results($wpdb->prepare("SELECT * FROM $wpdb->posts WHERE post_title = %s $post_type_where AND post_status = 'publish'", $page_title, $post_type ? $post_type : ''));

        if ($results) {
            $output = array();
            foreach ($results as $post) {
                $output[] = $post;
            }
            return $output;
        }
        return null;
    }
}
