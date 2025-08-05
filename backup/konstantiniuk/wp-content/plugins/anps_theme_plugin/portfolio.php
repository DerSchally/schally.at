<?php
class Portfolio {
    public function __construct() {
        $this->register_post_type();
        register_taxonomy('portfolio_category', 'portfolio', array(
            'hierarchical' => true,
            'label' => esc_html__('Categories', 'anps_theme_plugin'),
            'query_var' => true,
            'rewrite' => true,
        ));
    }

    private function register_post_type() {
        $args = array(
            'labels' => array(
                'name' => esc_html__('Portfolio', 'anps_theme_plugin'),
                'singular_name' => esc_html__('Portfolio', 'anps_theme_plugin'),
                'view_item' => esc_html__('View portfolio', 'anps_theme_plugin'),
                'search_items' => esc_html__('Search portfolio', 'anps_theme_plugin'),
                'not_found' => esc_html__('No portfolio found', 'anps_theme_plugin'),
            ),
            'query_var' => 'id',
            'rewrite' => array(
                'slug' => get_option('anps_portfolio_slug', 'services')
            ),
            'public' => true,
            'supports' => array(
                'title',
                'thumbnail',
                'editor',
                'categories',
                'excerpt'
            )
        );

       register_post_type('portfolio', $args);
    }
}
