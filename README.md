# Spectra Child Theme

A WordPress child theme for Spectra One, featuring a custom Video Project post type with Carbon Fields integration.

## Features

- **Custom Post Type**: `video-project` for showcasing video work
- **Custom Taxonomies**: Services and Industries for categorization
- **Carbon Fields Integration**: Custom meta fields for video details
- **Block Theme Compatible**: Works with WordPress Full Site Editing (FSE)

## Custom Fields (Carbon Fields)

### Video Tab
- Video Embed URL (Vimeo/YouTube embed format)
- Duration
- Custom Thumbnail

### Project Info Tab
- Featured Project toggle
- Client name
- Agency name

### Production Credits Tab
- Repeatable crew/credits fields (Role + Name)

## Shortcodes

Use these shortcodes in block templates:

- `[video_project_embed]` - Displays the video player
- `[video_project_meta]` - Displays client, agency, and featured badge
- `[video_project_credits]` - Displays production credits
- `[video_project_taxonomies]` - Displays services and industries

## Requirements

- WordPress 6.0+
- Spectra One parent theme
- Carbon Fields plugin

## Installation

1. Install and activate the Spectra One parent theme
2. Install and activate the Carbon Fields plugin
3. Upload this child theme to `/wp-content/themes/`
4. Activate the child theme

## License

GPL v2 or later
