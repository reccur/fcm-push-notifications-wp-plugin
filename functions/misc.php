<?php

defined('ABSPATH') OR die('Sorry, you can\'t do what you want to do !!.');

add_theme_support( 'post-thumbnails' );
add_image_size( 'push_notification_image', '980', '444', true );

/*-------------- Removing add new link from left bar menu ---------------------*/

add_action( 'admin_menu', 'pushnotification_adjust_the_wp_menu', 999 );
function pushnotification_adjust_the_wp_menu() {
  //$page = remove_submenu_page( 'edit.php', 'post-new.php' );
  //or for custom post type 'myposttype'.
	remove_submenu_page( 'edit.php?post_type=pushnotification', 'post-new.php?post_type=pushnotification' );
}

/*------------ Removing bulk actions from CPT----------------------*/

add_filter( 'bulk_actions-edit-pushnotification', 'pushnotification_p_remove_from_bulk_actions' );
function pushnotification_p_remove_from_bulk_actions( $actions ){
	unset( $actions[ 'edit' ] );
	return $actions;
}

/*-------------------------------------------*/
if ( isset($_GET['post_type']) && $_GET['post_type'] == 'pushnotification')
{
	add_action('admin_head', 'pushnotification_custom_admin_css');
	function pushnotification_custom_admin_css() {
		echo "<style>.tablenav .actions.bulkactions { padding-right: 0 } </style>";
	}
}
/*-------------------------------------*/
// Admin footer modification

$current_url = isset($_GET['post_type']) && !empty($_GET['post_type']) ? $_GET['post_type'] : '';
$pf_fcm_plugin_urls = array( 'pushnotification', 'push_notification');