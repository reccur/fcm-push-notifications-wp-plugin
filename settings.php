<?php

defined('ABSPATH') or die('Sorry, you can\'t do what you want to do !!.');

function pushnotification_settings(){

	/*---------------@ Reg Form Settings @---------------------*/
	register_setting('pushnotification_fields', 'FCM_ENVIRONMENT');
	register_setting('pushnotification_fields', 'FIREBASE_API_KEY');
	register_setting('pushnotification_fields', 'FCM_API_SECRET_KEY');
	register_setting('pushnotification_fields', 'blog_as_notification');
	register_setting('pushnotification_fields', 'cpt_as_notification');

	add_settings_section('pushnotification_fields', '', '', 'pushnotification_settings');

	add_settings_field('set-fcm-environment', 'Environment', 'pushnotification_environment', 'pushnotification_settings', 'pushnotification_fields');

	$environment = esc_attr(get_option('FCM_ENVIRONMENT'));
	if ($environment === 'production') {
		add_settings_field('activate-fcm-settings', 'Firebase API KEY', 'pushnotification_api_key', 'pushnotification_settings', 'pushnotification_fields');
	}

	add_settings_field('activate-fcm-api', 'Devices Subscription API KEY', 'localAPIKey', 'pushnotification_settings', 'pushnotification_fields');
	add_settings_field('activate-pt-settings', 'Choose if you want to send new Blog Post as Push Notification(s)', 'pushnotification_send_notif_settings_blog', 'pushnotification_settings', 'pushnotification_fields');
	add_settings_field('activate-cpt-settings', 'Choose if you want to send new Custom Post as Push Notification(s)', 'pushnotification_send_notif_settings_cpt', 'pushnotification_settings', 'pushnotification_fields');
}

add_action('admin_init', 'pushnotification_settings');

/*-----------------------------*/

function pushnotification_environment()
{
?>
	<select name="FCM_ENVIRONMENT" id="FCM_ENVIRONMENT">
		<option value="production" <?php selected(esc_attr(get_option('FCM_ENVIRONMENT')), 'production') ?>>Production</option>
		<option value="testing" <?php selected(esc_attr(get_option('FCM_ENVIRONMENT')), 'testing') ?>>Testing</option>
	</select>
<?php
}

function pushnotification_api_key(){
	echo '<input type="text" style="width: 100%" id="FIREBASE_API_KEY" name="FIREBASE_API_KEY" value="' . esc_attr(get_option('FIREBASE_API_KEY')) . '" />';
}

function localAPIKey(){
	echo '<input type="text" style="width: 25%; margin-right: 15px" id="FCM_API_SECRET_KEY" name="FCM_API_SECRET_KEY" value="' . esc_attr(get_option('FCM_API_SECRET_KEY')) . '" />';
	echo '<button class="button button-default" id="generatePdFcmApi">Generate API Key</button>';
}

function pushnotification_send_notif_settings_blog(){
	$options = esc_attr(get_option('blog_as_notification'));
	$checked = (@$options == 1 ? 'checked' : '');
	echo '<p><label for="blog_as_notification"><input type="checkbox" id="blog_as_notification" name="blog_as_notification" value="1" ' . $checked . ' /> Blog</label></p>';
}

function pushnotification_send_notif_settings_cpt(){
	$args = array(
		'public'   => true,
		'_builtin' => false
	);
	/*names or objects, note names is the default*/
	$output = 'objects';
	/*'and' or 'or'*/
	$operator = 'and';

	$post_types = get_post_types($args, $output, $operator);

	if ($post_types) {
		foreach ($post_types  as $post_type) {
			$options = get_option('cpt_as_notification') ? get_option('cpt_as_notification') : array('null');
			$pre_registered_cpt = $post_type->rewrite['slug'];
			$checked = (in_array($pre_registered_cpt, $options) ? 'checked' : '');
			echo '<p><label for=""><input type="checkbox" id="cpt_as_notification" name="cpt_as_notification[]" value="' . $post_type->rewrite['slug'] . '" ' . $checked . '  /> ' . $post_type->label . '</label></p>';
		}
	} else {
		echo "<p>It seems there is no Custom Post Type registered in your site !!</p>";
	}
}
/*-----------------------------*/

function pushnotification_page()
{ ?>

	<div class="wrap">
		<h2><a>FCM Settings</a></h2>
		<div class="card" style="max-width: 100%; margin-top:5px !important">
			<?php settings_errors(); ?>
			<form method="post" action="options.php">
				<?php

				settings_fields('pushnotification_fields');
				do_settings_sections('pushnotification_settings');
				submit_button();

				?>
			</form>
		</div>
	</div>

	<div class="wrap">
		<h2><a>List of API' s to use</a> </h2>
		<div class="card" style="max-width: 100%; margin-top:5px !important">
			<table class="form-table">
				<tr>
					<th>Subscribe</th>
					<td>
						<span id="subscribe_url"><span class="dashicons dashicons-admin-page"></span><?php echo site_url('wp-json/nrb/fcm/subscribe'); ?></span>
					</td>
				</tr>
				<tr>
					<th>Un-Subscribe</th>
					<td>
						<span id="unsubscribe_url"><span class="dashicons dashicons-admin-page"></span><?php echo site_url('wp-json/nrb/fcm/unsubscribe'); ?></span>
					</td>
				</tr>
			</table>
		</div>
	</div>

<?php }
