# WP Telegram Login

**Contributors:** [wpsocio](https://github.com/wpsocio), [irshadahmad21](https://github.com/irshadahmad21)
**Tags:** telegram, login, register, social, signup  
**Requires at least:** 6.4  
**Requires PHP:** 7.4  
**Tested up to:** 6.7.1  
**Stable tag:** 1.11.7  
**License:** GPL-3.0-or-later  
**License URI:** [https://www.gnu.org/licenses/gpl-3.0.html](https://www.gnu.org/licenses/gpl-3.0.html)  
**Donate link:** [wpsocio.com/donate](https://wpsocio.com/donate)

[![Wordpress plugin](https://img.shields.io/wordpress/plugin/v/wptelegram-login.svg)](https://wordpress.org/plugins/wptelegram-login/)
[![Wordpress](https://img.shields.io/wordpress/plugin/dt/wptelegram-login.svg)](https://wordpress.org/plugins/wptelegram-login/)
[![Wordpress rating](https://img.shields.io/wordpress/plugin/r/wptelegram-login.svg)](https://wordpress.org/plugins/wptelegram-login/)

Complete contributors list found here: [github.com/wpsocio/wptelegram-login/graphs/contributors](https://github.com/wpsocio/wptelegram-login/graphs/contributors)

**[Download plugin on wordpress.org](https://wordpress.org/plugins/wptelegram-login/)**

## Description

Let the users log in to your WordPress website with their Telegram and make it simple for them to get connected, and let them receive their email notifications on Telegram.

## Why Telegram Login?

- Removes the lengthy registration forms
- Removes the need for captchas
- Removes the need for email verification
- No “forgot password?” stuff
- Provides enough information about the user

## Features:

- Safe, secure, and easy login method
- Relies upon SHA-256 hashed data strings
- User data is trustworthy – verified by Telegram
- Can be used to prevent spam registrations
- Easy to install and set up for the admin
- Can be used to let new users sign up
- Existing users can connect their Telegram account
- Users can be given any desired role on the website
- The login button can be displayed anywhere
- Can be extended with custom code

## Widget Info

Goto **Appearance** > **Widgets** and click/drag **WP Telegram Login** and place it where you want it to be.

Alternatively, you can use the below shortcode.

Inside page or post content:

`[wptelegram-login button_style="large" show_user_photo="1" corner_radius="15" show_if_user_is="logged_in"]`

Inside the theme templates

```php
<?php
if ( function_exists( 'wptelegram_login' ) ) {
    $args = array(
        // 'show_user_photo' => true,
        // 'corner_radius'   => 15,
        // 'button_style'    => 'large',
        // 'show_if_user_is' => 'logged_out',
    );

    wptelegram_login( $args );
}
?>
```

or

```php
<?php echo do_shortcode( '[wptelegram-login button_style="small" show_user_photo="0" show_if_user_is="logged_in"]' ); ?>
```

## Telegram Web App data

The plugin can also handle the data sent by the [Telegram Web App](https://core.telegram.org/bots/webapps). Simply send `window.Telegram.WebApp.initData` query string to this URL: </br >
`http://<your-website.com>/?action=wptelegram_login&source=WebAppData`

The final URL might look like this: </br >
`http://<your-website.com>/?action=wptelegram_login&source=WebAppData&query_id=XXXX&user=XXXX&auth_date=XXXX&hash=XXXX`

The plugin will:

- Validate the data for you
- Create a user account for the user if it doesn't exist
- Login the user to the website in the context of your Web App.

**Note**: You need to use the same bot token for both the plugin and the Web App.

## Contribution

Development takes place in our [Github monorepo](https://github.com/wpsocio/wp-projects), and all contributions welcome.

## Translation

If you want to help with translation of the plugin, you can contribute via [WordPress Plugin Translations](https://translate.wordpress.org/projects/wp-plugins/wptelegram-comments).[WordPress Plugin Translations](https://translate.wordpress.org/projects/wp-plugins/wptelegram-login).

## Installation

#### Automatic installation

Automatic installation is the easiest way -- WordPress will handle the file transfer, and you won’t need to leave your web browser. To do an automatic install of the plugin:

- Log in to your WordPress dashboard
- Navigate to the Plugins menu, and click "Add New"
- In the search field type "wptelegram-login" and hit Enter
- Locate the plugin in the list of search results
- Click on "Install Now" and wait for the installation to complete
- Click on "Activate"

#### Manual installation

Manual installation method requires downloading the plugin and uploading it to your web server via your favorite FTP application. The official WordPress documentation contains [instructions on how to do this here](https://wordpress.org/support/article/managing-plugins/#manual-plugin-installation).

#### Updating

Automatic updates should work smoothly, but we still recommend you back up your site.
