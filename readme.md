## Aggregate Thumbnail Images

# The Issue
Tags and Images don't have a default visual identity, it would be nice to have a visual representation of the tags and images for usage in CSS or maybe for usage of `og:image`

# History
- Initial version provides an endpoint under the WP REST API to get the available featured images for categories or tags.

# Usage
- Install the plugin
- Access the endpoint at `/wp-json/aggregate-thumbnail-images/v1/category/{id}` or `/wp-json/aggregate-thumbnail-images/v1/tag/{id}` where `{id}` is the ID of the category or tag.
- Access the endpoint at `/wp-json/aggregate-thumbnail-images/v1/categoryByName/{name}` or `/wp-json/aggregate-thumbnail-images/v1/tagByName/{name}` where `{name}` is the name of the category or tag.

# Example response:
```json
{
  "images": [
    "https://example.orgwp-content/uploads/2024/05/C1EFF7E7-4212-43ED-A49C-89B816FB0137.jpeg",
    "https://example.orgwp-content/uploads/2024/05/EFECCA90-C40A-4D83-B197-64D7E1A36642.jpeg",
    "https://example.orgwp-content/uploads/2024/05/9C70C70A-062A-4676-9476-D88AF6C54643.jpg",
    "https://example.orgwp-content/uploads/2024/05/DBE9CDDD-6E9F-4CB9-832D-A79235CB1857-scaled.jpg",
    "https://example.orgwp-content/uploads/2024/05/CDE4CFFA-BE80-4328-BF85-5779D777EC8F.jpeg",
    "https://example.orgwp-content/uploads/2024/05/0B9EF9A0-4BD4-416A-ADD5-803B4BD7A77A.jpeg",
    "https://example.orgwp-content/uploads/2024/04/E9D7FE0B-5EED-4B19-91D1-09BBB57FBC6E-scaled.jpg",
    "https://example.orgwp-content/uploads/2024/04/24C4253D-CA87-44C2-B239-F609A080CAAD.jpeg",
    "https://example.orgwp-content/uploads/2024/04/CF0F9EDA-6D4E-4C24-A468-3076C71FF607-scaled.jpg"
  ],
  "filename": "tag_swim2024_600x600.jpg",
  "exists": {
    "path": "https://example.orgwp-content/uploads/aggregate-thumbnail-images/tag_swim2024_600x600.jpg",
    "file_exists": false
  }
}```