<?php

namespace WcPrime;

class ChildSite
{
    public function __construct()
    { }

    public static function register()
    {
        $labels = [
            'name'               => _x('Satellite Sites', 'Plural name'),
            'singular_name'      => _x('Satellite Site', 'post type singular name'),
            'add_new'            => _x('Add New', 'book'),
            'add_new_item'       => __('Add New Site'),
            'edit_item'          => __('Edit Site'),
            'new_item'           => __('New Site'),
            'all_items'          => __('All Sites'),
            'view_item'          => __('View Site'),
            'search_items'       => __('Search Sites'),
            'not_found'          => __('No sites found'),
            'not_found_in_trash' => __('No sites found in the Trash'),
            'parent_item_colon'  => '',
            'menu_name'          => 'Satellite Sites'
        ];
        $args = [
            'labels'        => $labels,
            'description'   => 'Holds your sites data',
            'public'        => true,
            'menu_position' => 5,
            'supports'      => ['title', 'custom-fields'],
            'has_archive'   => true,
        ];
        register_post_type('child_site', $args);
        wcprime('metabox')->add([
            'id' => 'wcp_child_site',
            'title' => __('Children Site Settings', 'wc-prime'),
            'post_type' => 'child_site',
            'priority' => 'high',
            'args' => [
                'fields' => [
                    [
                        'id' => 'wcp_site_url',
                        'title' => __('Site URL', 'wc-prime'),
                        'type' => 'text',
                    ],
                    [
                        'id' => 'wcp_site_key',
                        'title' => __('Consumer Key', 'wc-prime'),
                        'type' => 'text',
                    ],
                    [
                        'id' => 'wcp_site_secret',
                        'title' => __('Comsumer Secret', 'wc-prime'),
                        'type' => 'text',
                    ],
                ],
            ],
        ]);
        // add columns
        add_filter('manage_child_site_posts_columns', function ($columns) {
            return array_merge($columns, [
                'post_name' => __('Identifier', 'wc-prime'),
                'site_url' => __('Site URL', 'wc-prime'),
            ]);
        });
        // add data to columns
        add_action('manage_child_site_posts_custom_column', function ($column, $postId) {
            switch ($column) {
                case 'site_url':
                    $url = get_post_meta($postId, 'wcp_site_url', true);
                    echo '<a href="'.$url.'" target="_blank">' . $url . ' </a>';
                    break;
                case 'post_name':
                    $post = get_post($postId);
                    echo $post->post_name;
                    break;
            }
        }, 10, 2);
    }
}
