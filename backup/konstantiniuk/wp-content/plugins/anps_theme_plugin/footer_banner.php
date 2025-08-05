<?php
class Footer_banner {
    public function __construct() {
        $this->register_post_type();
    }
    
    private function register_post_type() {
        $args = array(
          'labels' => array(
              'name' =>'Footer banner',
              'singular_name' => 'Footer banner',
              'add_new' => 'Add new',
              'add_new_item' => 'Add new item',
              'edit_item' => 'Edit item',
              'new_item' => 'New banner',
              'view_item' => 'View banner',
              'search_items' => 'Search banner',
              'not_found' => 'No banner found'
          ),
            'query_var' => 'footer_banner',
            'rewrite' => array(
                'slug' => 'footer_banner'
            ),
            'public' => true,
            'supports' => array(
                            'title',
                            'editor',
            )
        );
        
       register_post_type('footer_banner', $args);
    }
}