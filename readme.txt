=== WP Telegram Login & Register ===
Contributors: wpsocio, irshadahmad21
Donate link: https://wpsocio.com/donate
Tags: telegram, login, register, social, signup
Requires at least: 6.4
Requires PHP: 7.4
Tested up to: 6.6.2
Stable tag: 1.11.4
License: GPL-3.0-or-later
License URI: https://www.gnu.org/licenses/gpl-3.0.html

Let your users login and register via Telegram, making it easier form them to get started on your website.

== Description ==
Let the users login to your website with their Telegram and make it simple for them to get connected and let them receive their email notifications on Telegram.

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

== Excellent Support ==

= Join the Chat =

We have a public group on Telegram to help set up the plugin and discuss issues, features, translations, etc. Join [@WPTelegramChat](https://t.me/WPTelegramChat)
For rules, see the pinned message. No spam, please.

= Get in touch =

*	Website [wpsocio.com](https://wpsocio.com)
*	Telegram [@WPTelegram](https://t.me/WPTelegram)
*	Facebook [@WPTelegram](https://fb.com/WPTelegram)
*	Twitter [@WPTelegram](https://twitter.com/WPTelegram)

== Contribution ==

Development takes place in our [Github monorepo](https://github.com/wpsocio/wp-projects), and all contributions welcome.

== Frequently Asked Questions ==

= How to create a Telegram Bot =

[How do I create a bot?](https://core.telegram.org/bots/faq#how-do-i-create-a-bot).

= Can I display button more than once on a page =

Sorry, that's not possible because Telegram allows only a single button per page.

= I see "Bot domain invalid" message =

Please follow the instructions given on the WP Telegram Login settings page.

It looks like you missed one that says to send the <code>/setdomain</code> command to @BotFather.

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

== Installation ==

= Automatic installation =

Automatic installation is the easiest way -- WordPress will handle the file transfer, and you won’t need to leave your web browser. To do an automatic install of the plugin:
 
* Log in to your WordPress dashboard
* Navigate to the Plugins menu, and click "Add New"
* In the search field type "wptelegram-login" and hit Enter
* Locate the plugin in the list of search results
* Click on "Install Now" and wait for the installation to complete
* Click on "Activate"

= Manual installation =

Manual installation method requires downloading the plugin and uploading it to your web server via your favorite FTP application. The official WordPress documentation contains [instructions on how to do this here](https://wordpress.org/support/article/managing-plugins/#manual-plugin-installation).

= Updating =

Automatic updates should work smoothly, but we still recommend you back up your site.

== Changelog ==

= 1.11.4 =
- Performance improvements
- Fixed Telegram login redirect for Mini Apps

[See full changelog](https://github.com/wpsocio/wptelegram-login/blob/main/CHANGELOG.md)
