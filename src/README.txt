=== WP Telegram Login & Register ===
Contributors: wpsocio, irshadahmad21
Donate link: https://wpsocio.com
Tags: telegram, login, register, social, signup
Requires at least: 5.8
Tested up to: 6.0
Requires PHP: 7.0
Stable tag: 1.9.13
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

`[wptelegram-login button_style="large" show_user_photo="1" corner_radius="15" show_if_user_is="logged_in"]`

Inside the theme templates
~~~
<?php
if ( function_exists( 'wptelegram_login' ) ) {
    $args = array(
        // 'show_user_photo' => false,
        // 'corner_radius'   => 15,
        // 'button_style'    => 'large',
        // 'show_if_user_is' => 'logged_out',
    );

    wptelegram_login( $args );
}
?>
~~~
or

~~~
<?php
    echo do_shortcode( '[wptelegram-login button_style="small" show_user_photo="0" show_if_user_is="logged_in"]' );
?>
~~~

**Get in touch**

*	Website [wptelegram.com](https://wptelegram.com)
*	Telegram [@WPTelegram](https://t.me/WPTelegram)
*	Facebook [@WPTelegram](https://fb.com/WPTelegram)
*	Twitter [@WPTelegram](https://twitter.com/WPTelegram)

**Contribution**
Development occurs on [Github](https://github.com/wpsocio/wptelegram-login), and all contributions welcome.

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

= Can I display button more than once on a page =

Sorry, that's not possible because Telegram allows only single button per page.

= I see "Bot domain invalid" message =

Follow the instructions given on the settings page. You need to send `/setdomain` to @BotFather.


== Screenshots ==

1. Settings Page
2. Settings Page (Cont...)
3. Settings Page (Cont...)
4. Widget Settings (back-end)
5. Widget View (front-end)
6. Login and Register page
7. User List Table (for admin)

== Changelog ==

= 1.9.13 =
- Maintenance release

= 1.9.12 =
- Fixed PHP Fatal Error for Block Widgets

= 1.9.11 =
- Fixed PHP warning for `block_categories` deprecation

= 1.9.10 =
- Fixed the failed login when Telegram name has special characters

= 1.9.9 =
- Cleaned up the admin menu for single entry for WP Telegram

= 1.9.8 =
- Fixed the issue of settings not saved due to trailing slash redirects

= 1.9.7 =
- Improved user REST meta query for Telegram users
- Fixed default settings for Gutenberg blocks

= 1.9.6 =
- Fixed Telegram login when login and register are on same page
- Fixed the issue of custom redirect URL not saved

= 1.9.5 =
- Fixed the messed up last update

= 1.9.4 =
- Fixed the hidden login on WP registration page

= 1.9.3 =
- Fixed admin links on settings page
- Fixed translation for plugin title

= 1.9.2 =
-   Fixed the missing Telegram ID in user table
-   Fixed creation of new user on login

= 1.9.1 =
-   Fixed i18n

= 1.9.0 =
-   Fixed the syntax error for older PHP versions.
-   Refreshed the UI
-   Switched to PHP namespaces

== Upgrade Notice ==