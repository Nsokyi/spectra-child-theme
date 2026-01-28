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

### Usage in Block Templates

In the Full Site Editor, add shortcodes using the **Shortcode block**:

1. Edit your template (e.g., `single-video-project.html`)
2. Add a Shortcode block from the block inserter
3. Paste the shortcode into the block

### Available Shortcodes

All shortcodes automatically detect the current post context when used on a single `video-project` template. No parameters required.

- `[video_project_embed]` - Displays the video player
- `[video_project_meta]` - Displays client, agency, and featured badge
- `[video_project_credits]` - Displays production credits
- `[video_project_taxonomies]` - Displays services and industries

### Optional Parameters

To display content from a specific post (e.g., in archives or other templates):

```
[video_project_embed id="123"]
[video_project_meta id="123"]
[video_project_credits id="123"]
[video_project_taxonomies id="123"]
```

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
