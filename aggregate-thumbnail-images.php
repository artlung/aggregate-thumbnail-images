<?php

/*
Plugin Name: Aggregate Thumbnail Images
Plugin URI: https://artlung.com/aggregate-thumbnail-images
Description: Provides REST endpoint to get a list of images for a category or tag
             TODO actually generate these aggregate images to serve as a featured image
             TODO could also be used to generate image for og:image
             TODO provide a dashboard to see what images exist and help a user composer create them
Version: 0.5
Author: artlung
Author URI: http://artlung.com
License: GPL2
*/
if (!defined('ABSPATH')) {
    exit;
}
/**
 * Class AggregateThumbnailImages
 */
class AggregateThumbnailImages {
    const route_namespace = 'aggregate-thumbnail-images/v1';
    const images_per_image = 9;
    const image_size_slug = '200';
    const default_featured_size = '600x600';
    const default_featured_type = 'jpg';

    const directory_name_for_aggreate_images = 'aggregate-thumbnail-images';
    const empty_response = [
        'images' => [],
        'filename' => '',
        'exists' => [
            'path' => '',
            'file_exists' => false,
        ],
    ];

    public static function get_category( WP_REST_Request $request ): WP_REST_Response
    {
        $id = $request['id'];
        $images = self::get_images($id, 'category');
        $filename = self::get_target_filename('category', $id);
        if (!$filename) {
            return new WP_REST_Response([
                'images' => [],
                'filename' => $filename,
            ], 404);
        }
        return new WP_REST_Response([
            'images' => $images,
            'filename' => $filename,
        ], 200);
    }

    /**
     * @param WP_REST_Request $request
     * @return WP_REST_Response
     */
    public static function get_tag(WP_REST_Request $request ): WP_REST_Response
    {
        $id = $request['id'];
        $images = self::get_images($id, 'tag');
        $filename = self::get_target_filename('tag', $id);
        $exists = self::get_full_path($filename);
        if (!$filename) {
            return new WP_REST_Response(self::empty_response, 404);
        }
        return new WP_REST_Response([
            'images' => $images,
            'filename' => $filename,
            'exists' => $exists,
        ], 200);


    }

    /**
     * @param $id
     * @param string $string
     * @return array
     */
    private static function get_images($id, string $string): array
    {
        $posts = [];
        if ($string === 'category') {
            $posts = get_posts([
                'category' => $id,
                'posts_per_page' => 18,
            ]);
        }
        else if ($string === 'tag') {
            $posts = get_posts([
                'tag_id' => $id,
                'posts_per_page' => 18,
            ]);
        }
        $images = [];
        // max 9 images
        foreach ($posts as $post) {
            $thumbnail_id = get_post_thumbnail_id($post->ID);
            $thumbnail = wp_get_attachment_image_src($thumbnail_id, self::image_size_slug);
            if ($thumbnail) {
                $images[] = $thumbnail[0];
            }
            if (count($images) >= self::images_per_image) {
                break;
            }
        }
        return $images;
    }

    /**
     * @param $taxonomy
     * @param $id
     * @param string $featured_size
     * @param string $type
     * @return string
     */
    private static function get_target_filename($taxonomy, $id,
                                                string $featured_size = self::default_featured_size,
                                                string $type = self::default_featured_type): string
    {
        $slug = '';
        if ($taxonomy === 'category') {
            $category = get_category($id);
            $slug = $category->slug;
        }
        else if ($taxonomy === 'tag') {
            $tag = get_term($id, 'post_tag');
            $slug = $tag->slug;
        }
        if (!$slug) {
            return '';
        }
        return sprintf('%s_%s_%s.%s', $taxonomy, $slug, self::default_featured_size, $type);
    }

    /**
     * Get the web path for a possible aggregate image and whether it exists
     * @param $filename
     * @return array<string, bool>
     */
    private static function get_full_path($filename): array
    {
        $upload_dir = wp_upload_dir();
        $upload_dir_path = $upload_dir['basedir'];
        $upload_dir_url = $upload_dir['baseurl'];
        $full_path = $upload_dir_path . '/' . self::directory_name_for_aggreate_images . '/' . $filename;
        $full_url = $upload_dir_url . '/' . self::directory_name_for_aggreate_images . '/' . $filename;
        $exists = file_exists($full_path);
        return [
            'path' => $full_url,
            'file_exists' => $exists
        ];
    }

    /**
     * @param WP_REST_Request $request
     * @return WP_REST_Response
     */
    public static function get_category_by_name( WP_REST_Request $request ): WP_REST_Response
    {
        $name = $request['name'];
        $category = get_category_by_slug($name);
        if (!$category) {
            return new WP_REST_Response(self::empty_response, 404);
        }
        $images = self::get_images($category->term_id, 'category');
        $filename = self::get_target_filename('category', $category->term_id);
        $exists = self::get_full_path($filename);
        return new WP_REST_Response([
            'images' => $images,
            'filename' => $filename,
            'exists' => $exists,
        ], 200);
    }

    /**
     * @param WP_REST_Request $request
     * @return WP_REST_Response
     */
    public static function get_tag_by_name( WP_REST_Request $request ): WP_REST_Response
    {
        $name = $request['name'];
        $tag = get_term_by('slug', $name, 'post_tag');
        if (!$tag) {
            return new WP_REST_Response(self::empty_response, 404);
        }
        $images = self::get_images($tag->term_id, 'tag');
        $filename = self::get_target_filename('tag', $tag->term_id);
        $exists = self::get_full_path($filename);
        return new WP_REST_Response([
            'images' => $images,
            'filename' => $filename,
            'exists' => $exists,
        ], 200);
    }

}


add_action( 'rest_api_init', function () {
    register_rest_route( AggregateThumbnailImages::route_namespace, '/category/(?P<id>\d+)', array(
        'methods' => 'GET',
        'callback' => AggregateThumbnailImages::class . '::get_category',
    ) );
    register_rest_route( AggregateThumbnailImages::route_namespace, '/tag/(?P<id>\d+)', array(
        'methods' => 'GET',
        'callback' => AggregateThumbnailImages::class . '::get_tag',
    ) );
    // allow for a categoryByName
    // allow for a tagByName
    register_rest_route( AggregateThumbnailImages::route_namespace, '/categoryByName/(?P<name>[a-zA-Z0-9-]+)', array(
        'methods' => 'GET',
        'callback' => AggregateThumbnailImages::class . '::get_category_by_name',
    ) );
    register_rest_route( AggregateThumbnailImages::route_namespace, '/tagByName/(?P<name>[a-zA-Z0-9-]+)', array(
        'methods' => 'GET',
        'callback' => AggregateThumbnailImages::class . '::get_tag_by_name',
    ) );



} );
