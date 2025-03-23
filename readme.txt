=== Fortnite Player Stats ===
Contributors: yourname
Tags: fortnite, stats, gaming, epic games, battle royale
Requires at least: 5.0
Tested up to: 6.4.3
Stable tag: 2.1
License: GPL v2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Display Fortnite player statistics on your WordPress site using Fortnite-API.com

== Description ==

Fortnite Player Stats allows you to display player statistics from the popular game Fortnite on your WordPress website. Simply add a shortcode to any page or post, and visitors can check stats for any Fortnite player.

The plugin uses the Fortnite-API.com service to retrieve up-to-date player information and supports all major platforms (PC, PlayStation, Xbox).

**Key Features:**

* Display stats for Solo, Duo, and Squad game modes
* Show Battle Pass progress and account level
* View win rates, K/D ratios, and more detailed metrics
* Support for different account types (Epic, PlayStation, Xbox)
* Lifetime and current season stats
* Responsive design works on all devices
* Built-in caching to reduce API calls

== Installation ==

1. Upload the `fortnite-player-stats` folder to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Go to Settings > Fortnite Player Stats to configure your API key
4. Add the shortcode `[fortnite_stats_form]` to any page or post

== Frequently Asked Questions ==

= Where do I get an API key? =

Visit [Fortnite-API.com](https://fortnite-api.com/) to register and obtain a free API key.

= How often is the data updated? =

The data is fetched from Fortnite-API.com when requested, but it's cached according to your settings (default is 15 minutes) to reduce API calls.

= Can I customize the appearance? =

Yes, you can modify the CSS in your theme to customize the appearance of the stats display.

= Is this plugin officially affiliated with Epic Games? =

No, this plugin is not affiliated with, endorsed by, or connected to Epic Games or Fortnite. It uses the third-party Fortnite-API.com service.

== Screenshots ==

1. Front-end stats display
2. Admin settings page
3. Debug logs for troubleshooting

== Changelog ==
= 2.1 =
* Added player avatars based on Fortnite default skins
* Improved form layout with all elements in a single row
* Added informative notice about PlayStation/Xbox username differences
* Enhanced error messages for account not found issues
* Fixed issues with some default skins not displaying properly
* Optimized API requests with better caching
* Visual improvements to the player info display
* Added visual separation between player details and statistics
* Mobile responsive improvements

= 2.0 =
* Complete rewrite to use Fortnite-API.com instead of Epic Online Services API
* Simplified plugin configuration (only API key needed instead of Client ID, Secret and Deployment ID)
* Added support for different account types (Epic, PlayStation, Xbox)
* Improved error handling and user notifications
* Enhanced visual design and responsiveness
* Added debug mode for troubleshooting
* Expanded stats display with more detailed metrics
* Better caching mechanism to reduce API calls
* Improved code organization and performance

= 1.1 =
* Added support for current season statistics
* Improved error handling
* Fixed compatibility issues with WordPress 6.0+
* Performance optimizations

= 1.0 =
* Initial release

== Upgrade Notice ==

= 2.0 =
Major update: Now uses Fortnite-API.com for better reliability. You'll need to obtain a new API key from Fortnite-API.com after updating.

== Configuration ==

1. Obtain an API key from [Fortnite-API.com](https://fortnite-api.com/)
2. Go to Settings > Fortnite Player Stats in your WordPress admin
3. Enter your API key
4. Set your preferred cache duration (recommended: 900-3600 seconds)
5. Save changes

== Usage ==

Add the shortcode `[fortnite_stats_form]` to any page or post where you want the Fortnite stats form to appear.

== Troubleshooting ==

If you encounter issues:

1. Enable Debug Mode in the plugin settings
2. Check the Debug Logs page for errors
3. Verify your API key is correct
4. Ensure the username exists on the selected platform
5. Check if your server can make outbound HTTP requests

== Privacy ==

This plugin makes API calls to Fortnite-API.com when users request Fortnite player statistics. The only data sent is the player username and platform. Please consider this when creating your site's privacy policy.