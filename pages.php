<?php

defined('ABSPATH') or die('Sorry, you can\'t do what you want to do !!.');


function adminPageSettings(){
	$capability = current_user_can('manage_woocommerce') ? 'manage_woocommerce' : 'manage_options';
	add_submenu_page('edit.php?post_type=fcm_device', 'Settings', 'Settings', $capability, 'settings', 'pushnotification_page');
}

add_action('admin_menu', 'adminPageSettings');

require_once 'settings.php';
