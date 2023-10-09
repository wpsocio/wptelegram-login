=== WP Telegram Login & Register ===
Contributors: wpsocio, irshadahmad21
Donate link: https://wpsocio.com/donate
Tags: telegram, login, register, social, signup
Requires at least: 6.0
Requires PHP: 7.0
Tested up to: 6.3.1
Stable tag: 1.10.7
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Let the users login to your website with their Telegram and make it simple for them to get connected and let them receive their email notifications on Telegram.

== Description ==
Let the users login to your website with their Telegram and make it simple for them to get connected and let them receive their email notifications on Telegram.

== Excellent Support ==

**Join the Chat**

We have a public group on Telegram to help set up the plugin and discuss issues, features, translations, etc. Join [@WPTelegramChat](https://t.me/WPTelegramChat)
For rules, see the pinned message. No spam, please.

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
*	Can be used to prevent spam registrations
*	Easy to install and set up for the admin
*	Can be used to let new users sign up
*	Existing users can connect their Telegram account
*	Users can be given any desired role on the website
*	Login button can be displayed anywhere
*	Can be extended with custom code

## Widget Info
Goto **Appearance** > **Widgets** and click/drag **WP Telegram Login** and place it where you want it to be.

Alternatively, you can use the below shortcode.

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

## Telegram Web App data

The plugin can also handle the data sent by the [Telegram Web App](https://core.telegram.org/bots/webapps). Simply send `window.Telegram.WebApp.initData` query string to this URL: 

`http://<your-website.com>/?action=wptelegram_login&source=WebAppData`

The final URL might look like this:

`http://<your-website.com>/?action=wptelegram_login&source=WebAppData&query_id=XXXX&user=XXXX&auth_date=XXXX&hash=XXXX`

The plugin will:

- Validate the data for you
- Create a user account for the user if it doesn't exist
- Login the user to the website in the context of your Web App.

**Note**: You need to use the same bot token for both the plugin and the Web App.

**Get in touch**

*	Website [wptelegram.com](https://wptelegram.com)
*	Telegram [@WPTelegram](https://t.me/WPTelegram)
*	Facebook [@WPTelegram](https://fb.com/WPTelegram)
*	Twitter [@WPTelegram](https://twitter.com/WPTelegram)

**Contribution**

Development occurs on [Github](https://github.com/wpsocio/wptelegram-login), and all contributions are welcome.

**Translations**

Many thanks to the translators for doing a great job!

* [Artem](https://profiles.wordpress.org/zzart/) (Russian)

* Note: You can also contribute to translating this plugin into your local language. Join the Chat (above)


== Installation ==


1. Upload the `wptelegram-login` folder to the `/wp-content/plugins/` directory
2. Activate the plugin through the Plugins menu in WordPress. After activation, you should see the menu of this plugin the admin
3. Configure the plugin.

**Enjoy!**

== Frequently Asked Questions ==

= How to create a Telegram Bot =

[How do I create a bot?](https://core.telegram.org/bots/faq#how-do-i-create-a-bot).

= Can I display button more than once on a page =

Sorry, that's not possible because Telegram allows only a single button per page.

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
8. User Profile (wp-admin)
9. WooCommerce Account Page

== Changelog ==

= 1.10.7 =
- Fixed the bug for Telegram Mini Apps which pass HTML encoded query string

= 1.10.6 =
- Fixed validation for Direct Link Mini Apps with start command

= 1.10.5 =
- Fixed validation for Direct Link Mini Apps

= 1.10.4 =
- Added Telegram Mini App login support

= 1.10.3 =
- Fixed translations not loaded for some strings

= 1.10.2 =
- Added Telegram user fields to WooCommerce Account page

= 1.10.1 =
- Added language option for the login widget

= 1.10.0 =
- Added support for Telegram Web App data authorization

== Upgrade Notice ==