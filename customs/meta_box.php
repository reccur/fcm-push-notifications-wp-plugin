<?php

defined('ABSPATH') OR die('Sorry, you can\'t do what you want to do !!.');

// for push notifications meta box
function addPushNotificationsMetaBox(){
	// adding metabox in blog post
	$blog_toggled = get_option( 'blog_as_notification' );
	if ($blog_toggled) {
		add_meta_box( 'send_push_notification', 'Push Notifications', 'pushNotificationsMetaBox', 'post', 'side', 'high' );
	}

	// adding metabox in blog push_notification
	add_meta_box( 'send_push_notification', 'Push Notifications', 'pushNotificationsMetaBox', 'push_notification', 'side', 'high' );

	// adding metabox in all custom post types
	addMetaBoxesInCPTs();
}


function addMetaBoxesInCPTs(){

	$args = array(
		'public'   => true,
		'_builtin' => false
	);

	/*names or objects, note names is the default*/
	$output = 'objects';

	/*'and' or 'or'*/
	$operator = 'and';

	$post_types = get_post_types( $args, $output, $operator );

	if ($post_types){
		foreach ( $post_types  as $post_type ){
			$options = get_option( 'cpt_as_notification' ) ? get_option( 'cpt_as_notification' ) : array('null');
			$cpt = $post_type->rewrite['slug'];
			$checked = ( in_array($cpt, $options) ? 'checked' : '' );
			if ($checked == 'checked'){
				// adding metabox in cpt
				add_meta_box( 'send_push_notification', 'Push Notifications', 'pushNotificationsMetaBox', $cpt, 'side', 'high' );
			}
		}
	}
}

function pushNotificationsMetaBox(){
	?>

	<div style="margin-bottom: 10px;">
		<label for="send_push_notification_chk">
			<input id="send_push_notification_chk" type="checkbox" name="send_push_notification_chk" checked> Send Push Notifications on Publish
		</label>
	</div>
	
	<div class="form-field form-required term-name-wrap">
		<div style="padding-bottom:5px"><label for="push_notification_topic">Topic</label></div>
		<input name="push_notification_topic" id="push_notification_topic" type="text" value="" size="100" aria-required="true">
		<p class="howto">Send push notifications to all devices subscribing to this topic.</p>
	</div>
	<?php
}