# Android FCM Push Notifications

Android FCM Push Notifications is a plugin through which you can send push notifications directly from your WordPress site to android devices via Firebase Cloud Messaging (FCM) service. When a new blog is posted or existing blog is updated, a push notification sent to android device. Just set your topic!


# Directory Structure

- /admin/ 						- Plugin backend stuff.
- /functions/					- Functions and plugin operations.
- /includes/					- External third party classes and libraries.
- /languages/					- Translation files go here. 
- /public/						- Front end files and functions that matter on the front end go here.
- index.php						- Dummy file.
- license.txt					- GPL v2
- prefix_starter-plugin.php		- Main plugin file containing plugin name and other version info for WordPress.
- readme.txt					- Readme for WordPress plugin repository.
- uninstall.php					- Fired when the plugin is uninstalled. 


# TODO

- Note: (S&R) = Search and Replace by matching case.
- Plugin name: Starter Plugin (S&R)
- Plugin folder slug: starter-plugin (S&R)
- Decide on a prefix for the plugin (S&R)
- Plugin description					in prefix_starter-plugin.php
- Text domain. Text domain for plugins has to be the folder name of the plugin. For eg. if your plugin is in /wp-content/plugins/abc-def/ folder text domain should be abc-def (S&R)
- Update prefix_settings_link() 		in \admin\basic-setup.php
- Update prefix_footer_text()			in \admin\basic-setup.php
- Update prefix_add_menu_links() 		in \admin\admin-ui-setup.php
- Update prefix_register_settings() 	in \admin\admin-ui-setup.php
- Update UI format and settings			in \admin\admin-ui-render.php
- Update uninstall.php
- Update readme.txt
- Update PREFIX_VERSION_NUM 			in prefix_starter-plugin.php (keep this line for future updates)