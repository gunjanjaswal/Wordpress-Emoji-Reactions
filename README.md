# WP Emoji Reactions

Add colorful emoji reactions to your WordPress posts and pages, similar to Facebook reactions.

![WP Emoji Reactions](assets/banner-1544x500.png)

## Description

WP Emoji Reactions allows your visitors to react to your content with colorful emoji reactions, similar to Facebook's reaction system. This plugin adds a simple and intuitive reaction system to your posts, pages, or any custom post type.

### Features

* Add emoji reactions to posts, pages, or any custom post type
* Choose from 16 different emoji reactions
* Customize the display name for each reaction
* Set a custom title text for the reactions section
* Display reactions after content or as a floating bar
* View reaction statistics in the admin dashboard
* Mobile-friendly and responsive design
* Works with any WordPress theme

## Installation

1. Upload the `wp-emoji-reactions` folder to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Go to Settings > WP Emoji Reactions to configure the plugin

## Configuration

### Basic Settings

1. **Enabled Emojis**: Select which emoji reactions should be available to your visitors
2. **Custom Names**: Customize the display name for each reaction
3. **Title Text**: Set a custom title text for the reactions section
4. **Position**: Choose where to display reactions (after content, floating, or both)
5. **Post Types**: Select which post types should display reactions

### Advanced Usage

You can also use the following shortcode to display reactions anywhere in your content:

```
[wp_emoji_reactions]
```

Or use the PHP function in your theme files:

```php
<?php if (function_exists('wp_emoji_reactions_display')) { wp_emoji_reactions_display(); } ?>
```

## Frequently Asked Questions

### Can visitors use multiple reactions on the same post?

No, each visitor can only use one reaction per post. They can change their reaction, but cannot use multiple reactions simultaneously.

### How are reactions tracked?

Reactions are tracked by IP address for non-logged-in users and by user ID for logged-in users.

### Can I customize the appearance of the reactions?

Yes, you can use custom CSS to style the reactions. The plugin includes basic styling that works with most themes.

## Screenshots

1. Emoji reactions displayed after post content
2. Admin settings page
3. Reaction statistics in the admin dashboard

## Changelog

### 1.0.0
* Initial release

## Credits

* Developed by [Gunjan Jaswaal](https://gunjanjaswal.me/)
* Emoji font: [Noto Color Emoji](https://fonts.google.com/noto/specimen/Noto+Color+Emoji) by Google

## License

This plugin is licensed under the GPL v2 or later.

```
WP Emoji Reactions is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 2 of the License, or
any later version.

WP Emoji Reactions is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with WP Emoji Reactions. If not, see https://www.gnu.org/licenses/gpl-2.0.html.
```
