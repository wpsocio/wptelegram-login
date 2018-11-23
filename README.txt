=== WP Telegram Login & Register ===
Contributors: manzoorwanijk
Donate link: https://paypal.me/manzoorwanijk
Tags: telegram, login, register, social, signup
Requires at least: 3.8.0
Tested up to: 4.9.8
Requires PHP: 5.2.4
Stable tag: 1.3.4
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Let the users login to your website with their Telegram and make it simple for them to get connected and let them receive their email notifications on Telegram.

== Description ==
Let the users login to your website with their Telegram and make it simple for them to get connected and let them receive their email notifications on Telegram.

== Excellent Support ==

**Join the Chat**

We have a public group on Telegram to provide help setting up the plugin, discuss issues, features, translations etc. Join [@WPTelegramChat](https://t.me/WPTelegramChat)
For rules, see the pinned message. No spam please.

== Why Telegram Login? ==

* Removes the lengthy registration forms
* Removes the need for captchas
* Removes the need for email verification
* No “forgot password?” stuff
* Provides enough information about the user

== Features ==

*	Safe, secure and easy login method
*	Relies upon SHA-256 hashed data strings 
*	User data is trustworthy – verified by Telegram
*	Users can remotely logout of the websites
*	Can be used to prevent spam registrations
*	Easy to install and set up for the admin
*	Can be used to let new users sign up
*	Existing users can connect their Telegram account
*	Users can be given any desired role on the website
*	Login button can be displayed anywhere
*	Can be extended with custom code

## Widget Info
Goto **Appearance** > **Widgets** and click/drag **WP Telegram Login** and place it where you want it to be.

Alternately, you can use the below shortcode.

Inside page or post content:

`[wptelegram-login button_style="large" show_user_photo="on" corner_radius="15" show_if_user_is="logged_in"]`

Inside the theme templates
~~~
<?php
if ( function_exists( 'wptelegram_login' ) ) {
    $args = array(
        // 'show_user_photo'    => 'off',
        // 'corner_radius'      => 15,
        // 'button_style'       => 'large',
        // 'show_if_user_is'    => 'logged_out',
    );

    wptelegram_login( $args );
}
?>
~~~
or

~~~
<?php
    echo do_shortcode( '[wptelegram-login button_style="small" show_user_photo="off" show_if_user_is="logged_in"]' );
?>
~~~

**Get in touch**

*	Website [wptelegram.com](https://wptelegram.com)
*	Telegram [@WPTelegram](https://t.me/WPTelegram)
*	Facebook [@WPTelegram](https://fb.com/WPTelegram)
*	Twitter [@WPTelegram](https://twitter.com/WPTelegram)

**Contribution**
Development occurs on [Github](https://github.com/manzoorwanijk/wptelegram-login), and all contributions welcome.

**Translations**

Many thanks to the translators for the great job!

* [Artem](https://profiles.wordpress.org/zzart/) (Russian)

* Note: You can also contribute in translating this plugin into your local language. Join the Chat (above)


== Installation ==


1. Upload the `wptelegram-login` folder to the `/wp-content/plugins/` directory
2. Activate the plugin through the Plugins menu in WordPress. After activation, you should see the menu of this plugin the the admin
3. Configure the plugin.

**Enjoy!**

== Frequently Asked Questions ==

= How to create a Telegram Bot =

[How do I create a bot?](https://core.telegram.org/bots/faq#how-do-i-create-a-bot).


== Screenshots ==

1. Settings Page
2. Settings Page (Cont...)
3. Settings Page (Cont...)
4. Widget Settings (back-end)
5. Widget View (front-end)
6. Login and Register page
7. User List Table (for admin)

== Changelog ==

= 1.3.4 =
* Fixed 404 issue for WP in sub directory

= 1.3.3 =
* Bundled CMB2 to handle the dependency
* Fixed UI bugs on login page with errors
* Fixed the shortcode instruction for "show_user_photo"

= 1.3.2 =
* Prepare for WP Telegram 2.0.0

= 1.3.1 =
* Added Russian Translation. Thanks to @zzart
* Fixed the Save Settings errors
* Fixed option class error in old PHP versions

= 1.3.0 =
* Revamped the options to avoid the use of globals
* Added the option to disable the button on default WP login page
* Fixed the PHP warnings for first time install
* Fixed the error in user columns in older WP versions

== Upgrade Notice ==