<?php
class AnpsTeam {
    public function __construct() {
        $this->register_post_type();
    }

    private function register_post_type() {
        //team single page option
        $team_single_option = get_option('anps_team_single_page', '1');

        $query_var = 'team';
        if($team_single_option == '') {
            $query_var = false;
        }

        $args = array(
          'labels' => array(
              'name' => esc_html__('Team', 'anps_theme_plugin'),
              'singular_name' => esc_html__('Team', 'anps_theme_plugin'),
              'view_item' => esc_html__('View team', 'anps_theme_plugin'),
              'search_items' => esc_html__('Search team', 'anps_theme_plugin'),
              'not_found' => esc_html__('No team found', 'anps_theme_plugin'),
            ),
            'query_var' => $query_var,
            'rewrite' => array(
                'slug' => get_option('anps_team_slug', 'team')
            ),
            'public' => true,
            'supports' => array(
                            'title',
                            'thumbnail',
                            'editor',
                            'excerpt'
            )
        );

       register_post_type('team', $args);
    }
}
