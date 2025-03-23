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

## Examples
    `https://fortnite-api.com/v2/cosmetics/br/search?matchMethod=contains&name=Default`

    ```json
    {
  "status": 200,
  "data": {
    "id": "CID_DefaultOutfit",
    "name": "Default",
    "description": "null",
    "type": {
      "value": "outfit",
      "displayValue": "Outfit",
      "backendValue": "AthenaCharacter"
    },
    "rarity": {
      "value": "common",
      "displayValue": "Common",
      "backendValue": "EFortRarity::Common"
    },
    "images": {
      "smallIcon": "https://fortnite-api.com/images/cosmetics/br/cid_defaultoutfit/smallicon.png",
      "icon": "https://fortnite-api.com/images/cosmetics/br/cid_defaultoutfit/icon.png",
      "lego": {
        "small": "https://fortnite-api.com/images/cosmetics/lego/cid_defaultoutfit/small.png",
        "large": "https://fortnite-api.com/images/cosmetics/lego/cid_defaultoutfit/large.png"
      },
      "bean": {
        "small": "https://fortnite-api.com/images/cosmetics/beans/bean_defaultoutfit/small.png",
        "large": "https://fortnite-api.com/images/cosmetics/beans/bean_defaultoutfit/large.png"
      }
    },
    "variants": [
      {
        "channel": "Military",
        "type": "Combat",
        "options": [
          {
            "tag": "Random",
            "name": "Random",
            "image": "https://fortnite-api.com/images/cosmetics/br/cid_defaultoutfit/variants/military/random.png"
          },
          {
            "tag": "Outfit0",
            "name": "Emmy",
            "image": "https://fortnite-api.com/images/cosmetics/br/cid_defaultoutfit/variants/military/outfit0.png"
          },
          {
            "tag": "Outfit1",
            "name": "Amy",
            "image": "https://fortnite-api.com/images/cosmetics/br/cid_defaultoutfit/variants/military/outfit1.png"
          },
          {
            "tag": "Outfit2",
            "name": "Melody",
            "image": "https://fortnite-api.com/images/cosmetics/br/cid_defaultoutfit/variants/military/outfit2.png"
          },
          {
            "tag": "Outfit3",
            "name": "Ramirez",
            "image": "https://fortnite-api.com/images/cosmetics/br/cid_defaultoutfit/variants/military/outfit3.png"
          },
          {
            "tag": "Outfit4",
            "name": "Simone",
            "image": "https://fortnite-api.com/images/cosmetics/br/cid_defaultoutfit/variants/military/outfit4.png"
          },
          {
            "tag": "Outfit5",
            "name": "Lena",
            "image": "https://fortnite-api.com/images/cosmetics/br/cid_defaultoutfit/variants/military/outfit5.png"
          },
          {
            "tag": "Outfit6",
            "name": "Jo",
            "image": "https://fortnite-api.com/images/cosmetics/br/cid_defaultoutfit/variants/military/outfit6.png"
          },
          {
            "tag": "Outfit7",
            "name": "Jonesy",
            "image": "https://fortnite-api.com/images/cosmetics/br/cid_defaultoutfit/variants/military/outfit7.png"
          },
          {
            "tag": "Outfit8",
            "name": "Aiden",
            "image": "https://fortnite-api.com/images/cosmetics/br/cid_defaultoutfit/variants/military/outfit8.png"
          },
          {
            "tag": "Outfit9",
            "name": "Cyrus",
            "image": "https://fortnite-api.com/images/cosmetics/br/cid_defaultoutfit/variants/military/outfit9.png"
          },
          {
            "tag": "Outfit10",
            "name": "Elias",
            "image": "https://fortnite-api.com/images/cosmetics/br/cid_defaultoutfit/variants/military/outfit10.png"
          },
          {
            "tag": "Outfit11",
            "name": "Jonah",
            "image": "https://fortnite-api.com/images/cosmetics/br/cid_defaultoutfit/variants/military/outfit11.png"
          },
          {
            "tag": "Outfit12",
            "name": "Robbie",
            "image": "https://fortnite-api.com/images/cosmetics/br/cid_defaultoutfit/variants/military/outfit12.png"
          },
          {
            "tag": "Outfit13",
            "name": "Liam",
            "image": "https://fortnite-api.com/images/cosmetics/br/cid_defaultoutfit/variants/military/outfit13.png"
          }
        ]
      },
      {
        "channel": "Streetwear",
        "type": "Casual",
        "options": [
          {
            "tag": "Random",
            "name": "Random",
            "image": "https://fortnite-api.com/images/cosmetics/br/cid_defaultoutfit/variants/streetwear/random.png"
          },
          {
            "tag": "Outfit0",
            "name": "Emmy",
            "image": "https://fortnite-api.com/images/cosmetics/br/cid_defaultoutfit/variants/streetwear/outfit0.png"
          },
          {
            "tag": "Outfit1",
            "name": "Amy",
            "image": "https://fortnite-api.com/images/cosmetics/br/cid_defaultoutfit/variants/streetwear/outfit1.png"
          },
          {
            "tag": "Outfit2",
            "name": "Melody",
            "image": "https://fortnite-api.com/images/cosmetics/br/cid_defaultoutfit/variants/streetwear/outfit2.png"
          },
          {
            "tag": "Outfit3",
            "name": "Ramirez",
            "image": "https://fortnite-api.com/images/cosmetics/br/cid_defaultoutfit/variants/streetwear/outfit3.png"
          },
          {
            "tag": "Outfit4",
            "name": "Simone",
            "image": "https://fortnite-api.com/images/cosmetics/br/cid_defaultoutfit/variants/streetwear/outfit4.png"
          },
          {
            "tag": "Outfit5",
            "name": "Lena",
            "image": "https://fortnite-api.com/images/cosmetics/br/cid_defaultoutfit/variants/streetwear/outfit5.png"
          },
          {
            "tag": "Outfit6",
            "name": "Jo",
            "image": "https://fortnite-api.com/images/cosmetics/br/cid_defaultoutfit/variants/streetwear/outfit6.png"
          },
          {
            "tag": "Outfit7",
            "name": "Jonesy",
            "image": "https://fortnite-api.com/images/cosmetics/br/cid_defaultoutfit/variants/streetwear/outfit7.png"
          },
          {
            "tag": "Outfit8",
            "name": "Aiden",
            "image": "https://fortnite-api.com/images/cosmetics/br/cid_defaultoutfit/variants/streetwear/outfit8.png"
          },
          {
            "tag": "Outfit9",
            "name": "Cyrus",
            "image": "https://fortnite-api.com/images/cosmetics/br/cid_defaultoutfit/variants/streetwear/outfit9.png"
          },
          {
            "tag": "Outfit10",
            "name": "Elias",
            "image": "https://fortnite-api.com/images/cosmetics/br/cid_defaultoutfit/variants/streetwear/outfit10.png"
          },
          {
            "tag": "Outfit11",
            "name": "Jonah",
            "image": "https://fortnite-api.com/images/cosmetics/br/cid_defaultoutfit/variants/streetwear/outfit11.png"
          },
          {
            "tag": "Outfit12",
            "name": "Robbie",
            "image": "https://fortnite-api.com/images/cosmetics/br/cid_defaultoutfit/variants/streetwear/outfit12.png"
          },
          {
            "tag": "Outfit13",
            "name": "Liam",
            "image": "https://fortnite-api.com/images/cosmetics/br/cid_defaultoutfit/variants/streetwear/outfit13.png"
          }
        ]
      }
    ],
    "added": "2024-12-10T09:33:41Z"
  }
}
```

## Requirements

- WordPress 5.0 or higher
- PHP 7.2 or higher

## License

GPL v2 or later

---

This plugin is not affiliated with, endorsed by, or connected to Epic Games or Fortnite.
