# Aggregate Thumbnail Images

## Description

### WordPress Plugin

WordPress Plugin to expose images based on tag or category. This plugin is intended to be used by themes or other plugins to provide a visual representation of tags and categories.

### Shell Script

A shell script `aggregate-thumbnail-images.sh` is provided to create a thumbnail images based on the API output
- `chmod +x aggregate-thumbnail-images.sh`
- `./aggregate-thumbnail-images.sh --taxonomy=tag --name=swimming` will generate the image wherever you invoke the script.
- Requirements
- - `bash` for running the script
- - `jq` for parsing JSON
- - `curl` for making HTTP requests
- - `convert` from ImageMagick for image manipulation
- - add a file called `.aggregate-thumbnail-images` in your home directory with the website url 

## The Issue
Tags and Images don't have a default visual identity, it would be nice to have a visual representation of the tags and images for usage in CSS or maybe for usage of `og:image`  based on the [Featured Images](https://codex.wordpress.org/Post_Thumbnails) for the posts in that category.

## Usage of WordPress Plugin
- Install the plugin to `wp-content/plugins/aggregate-thumbnail-images`
- Access the endpoint at `/wp-json/aggregate-thumbnail-images/v1/category/{id}` or `/wp-json/aggregate-thumbnail-images/v1/tag/{id}` where `{id}` is the ID of the category or tag.
- Access the endpoint at `/wp-json/aggregate-thumbnail-images/v1/categoryByName/{name}` or `/wp-json/aggregate-thumbnail-images/v1/tagByName/{name}` where `{name}` is the name of the category or tag.

### Example GET request

```bash
curl -X GET "https://example.org/wp-json/aggregate-thumbnail-images/v1/tag/1" -H  "accept: application/json"
```


### Example response:
```json
{
  "images": [
    "https://example.org/wp-content/uploads/2024/05/C1EFF7E7-4212-43ED-A49C-89B816FB0137.jpeg",
    "https://example.org/wp-content/uploads/2024/05/EFECCA90-C40A-4D83-B197-64D7E1A36642.jpeg",
    "https://example.org/wp-content/uploads/2024/05/9C70C70A-062A-4676-9476-D88AF6C54643.jpg",
    "https://example.org/wp-content/uploads/2024/05/DBE9CDDD-6E9F-4CB9-832D-A79235CB1857-scaled.jpg",
    "https://example.org/wp-content/uploads/2024/05/CDE4CFFA-BE80-4328-BF85-5779D777EC8F.jpeg",
    "https://example.org/wp-content/uploads/2024/05/0B9EF9A0-4BD4-416A-ADD5-803B4BD7A77A.jpeg",
    "https://example.org/wp-content/uploads/2024/04/E9D7FE0B-5EED-4B19-91D1-09BBB57FBC6E-scaled.jpg",
    "https://example.org/wp-content/uploads/2024/04/24C4253D-CA87-44C2-B239-F609A080CAAD.jpeg",
    "https://example.org/wp-content/uploads/2024/04/CF0F9EDA-6D4E-4C24-A468-3076C71FF607-scaled.jpg"
  ],
  "filename": "tag_swim2024_600x600.jpg",
  "exists": {
    "path": "https://example.org/wp-content/uploads/aggregate-thumbnail-images/tag_swim2024_600x600.jpg",
    "file_exists": false
  }
}
```

## History

### 1.0.0: 
- Initial version provides an endpoint under the WP REST API to get the available featured images for categories or tags.
- Added a shell script to consume the api and generate the image based on the output.

### 1.0.1:
- `convert` no longer works the way it did, ImageMagick prefers `magick` so switched the shell script to use `magick` for `convert` then `montage` to combine the images.

## Goals
- Make those images or allow WordPress users to compose images for tags and categories or both
- In other words, make that `file_exists` be able to return `true` and mean it.
- Assess security implications
- Assess performance implications
- Assess usability
- Add some unit testing

## Potential directions
- Add a way to do this for a page based on child pages featured images
- Add a way to add a logo to the images
- Add a way to add a background image to the images
- Add a way to add a gradient to the images
- Add a way to add a pattern to the images

## Want to help?
- Feel free to fork and submit a PR
- Have a suggestion? Open an issue

## Contributors
- [Joe Crawford](https://artlung.com/)