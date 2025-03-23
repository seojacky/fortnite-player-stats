# Fortnite Player Stats

WordPress plugin to display Fortnite player statistics using the Fortnite-API.com service.

## Description

Fortnite Player Stats is a lightweight WordPress plugin that allows website visitors to check statistics for any Fortnite player. The plugin uses the unofficial Fortnite-API.com service to retrieve accurate and up-to-date player information.

## Features

- Display comprehensive player statistics for Fortnite
- Support for Solo, Duo, and Squad game modes
- View Battle Pass level and progress
- Track wins, K/D ratio, win rate, and more
- Support for different account types (Epic, PlayStation, Xbox)
- Lifetime and season statistics
- Responsive design for all devices
- Automatic data caching to reduce API calls

## Installation

1. Upload the `fortnite-player-stats` folder to your `/wp-content/plugins/` directory
2. Activate the plugin through the WordPress admin interface
3. Go to Settings > Fortnite Player Stats to enter your API key
4. Add the shortcode `[fortnite_stats_form]` to any page or post

## Configuration

### API Key Setup

1. Visit [Fortnite-API.com](https://fortnite-api.com/) and register for an account
2. Obtain your API key
3. Enter the API key in the plugin settings page

### Cache Settings

Adjust the cache duration to control how frequently the plugin requests new data from the API. The recommended setting is between 15-60 minutes (900-3600 seconds).

## Usage

Simply add the `[fortnite_stats_form]` shortcode to any page or post where you want to display the Fortnite stats form. Visitors can then:

1. Enter a Fortnite username
2. Select the account type (Epic, PlayStation, Xbox)
3. Choose the time window (Lifetime or Current Season)
4. View detailed statistics

## Troubleshooting

If you encounter issues with the plugin:

1. Enable debug mode in the plugin settings
2. Check the debug logs for error messages
3. Verify that your API key is entered correctly
4. Ensure that the WordPress server can make outbound HTTP requests

## Requirements

- WordPress 5.0 or higher
- PHP 7.2 or higher

## License

GPL v2 or later

---

This plugin is not affiliated with, endorsed by, or connected to Epic Games or Fortnite.