# WP Emoji Reactions - Documentation

## Overview

WP Emoji Reactions is a WordPress plugin that adds colorful emoji reactions to your posts and pages, similar to Facebook's reaction system. Visitors can react to your content with various emojis, and you can track which reactions are most popular.

## Features

- Add emoji reactions to posts, pages, or any custom post type
- Choose from a variety of colorful emoji reactions
- Track reaction counts and statistics
- Display reactions after content or as a floating element
- One reaction per IP address to prevent spam
- Mobile-friendly and responsive design
- Uses Google's Noto Color Emoji font for consistent cross-platform display

## Installation

1. Upload the `wp-emoji-reactions` folder to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Go to Settings > Emoji Reactions to configure the plugin
4. Choose which emoji reactions to enable and where to display them

## Configuration

### General Settings

1. **Display Position**: Choose where to display the emoji reactions:
   - After Content: Displays reactions at the end of your post content
   - Floating: Displays reactions in a fixed position on the screen

2. **Post Types**: Select which post types should display emoji reactions:
   - Posts
   - Pages
   - Custom post types

### Emoji Settings

Select which emoji reactions should be available to your visitors:
- Like 👍
- Love ❤️
- Laugh 😂
- Wow 😮
- Sad 😢
- Angry 😠
- And more...

## How It Works

1. Visitors can click on an emoji to react to your content
2. Each visitor can only react once per post (based on IP address)
3. Visitors can change their reaction by clicking on a different emoji
4. Reaction counts are displayed in real-time

## Technical Details

### Database

The plugin creates a custom database table `{prefix}_emoji_reactions` with the following structure:
- `id`: Unique identifier for each reaction
- `post_id`: The ID of the post being reacted to
- `emoji_id`: The ID of the emoji reaction (e.g., 'like', 'love')
- `ip_address`: The IP address of the visitor
- `user_id`: The WordPress user ID (if logged in)
- `reaction_time`: The time when the reaction was added

### Options

The plugin stores the following options in the WordPress database:
- `wp_emoji_reactions_enabled_emojis`: Array of enabled emoji reactions
- `wp_emoji_reactions_position`: Display position ('after_content' or 'floating')
- `wp_emoji_reactions_post_types`: Array of post types to display reactions on

### Files and Directory Structure

```
wp-emoji-reactions/
├── admin/                  # Admin-specific functionality
│   ├── css/                # Admin stylesheets
│   ├── js/                 # Admin JavaScript
│   └── partials/           # Admin templates
├── includes/               # Core plugin functionality
├── languages/              # Translation files
├── public/                 # Public-facing functionality
│   ├── css/                # Public stylesheets
│   ├── js/                 # Public JavaScript
│   └── partials/           # Public templates
├── assets/                 # Static assets
│   ├── css/                # Additional CSS
│   ├── js/                 # Additional JavaScript
│   └── images/             # Images
├── index.php               # Silence is golden
├── uninstall.php           # Cleanup on uninstall
├── readme.txt              # WordPress repository readme
└── wp-emoji-reactions.php  # Main plugin file
```

## Frequently Asked Questions

### Can visitors react multiple times to the same post?

No, each visitor can only react once per post, based on their IP address. They can change their reaction by clicking on a different emoji.

### Can I customize which emoji reactions are available?

Yes, you can choose which emoji reactions to enable from the plugin settings page.

### Can I display reactions on custom post types?

Yes, you can choose which post types should display emoji reactions from the plugin settings page.

### Does this plugin work with page builders?

Yes, the plugin should work with most page builders as it hooks into WordPress's content filter.

### Does this plugin slow down my website?

No, the plugin is lightweight and optimized for performance. It only loads resources on pages where reactions are displayed.

## Support

For support, please visit the [plugin support forum](https://wordpress.org/support/plugin/wp-emoji-reactions/) or contact the author at [hello@gunjanjaswal.me](mailto:hello@gunjanjaswal.me).

## Credits

- Plugin developed by [Gunjan Jaswaal](https://gunjanjaswal.me)
- Emoji font: [Noto Color Emoji](https://fonts.google.com/noto/specimen/Noto+Color+Emoji) by Google
