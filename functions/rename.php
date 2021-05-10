<?php

defined('ABSPATH') OR die('Sorry, you can\'t do what you want to do !!.');

add_action( 'current_screen', 'pushnotification_this_screen' );

function pushnotification_this_screen() {
	$currentScreen = get_current_screen();
	if( $currentScreen->id === "push_notification" ) {
		add_filter( 'gettext', 'pushnotification_rename_push_btn', 10, 2 );
	}
}

function pushnotification_rename_push_btn( $translation, $original ){
	if ('Update' == $original ) {
		return 'Update & Send';
	} elseif ('Publish' == $original ) {
		return'Send Message';
	} elseif ( 'Excerpt' == $original ) {
		return 'Message';
	} else {
		$pos = strpos($original, 'Excerpts are optional hand-crafted summaries of your');
		if ($pos !== false) {
			return '<b>Note:</b> <i>(Maximum 100 characters)</i>';
		}
	} return $translation;
}