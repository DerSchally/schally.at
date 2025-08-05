<?php

    function anps_content_add_custom_box() {
        $screens = array('portfolio');
        foreach ($screens as $screen) {
            add_meta_box(
                    'anps_breadcrumbs_sectionid', esc_html__('Side Content', 'anps_theme_plugin'), 'anps_display_meta_box_content', $screen
            );
        }
    }
    function anps_display_meta_box_content( $post ) {
            $value2 = get_post_meta( $post -> ID, $key = 'portfolio_side_content', $single = true );
            wp_editor( $value2, 'content-id', array( 'textarea_name' => 'portfolio_side_content', 'media_buttons' => false, 'teeny'=>true ) );
    }
    function anps_content_save_postdata($post_id) { 
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
            return;
        if (empty($_POST))
            return;
        // Check permissions
        if ('portfolio' == $_POST['post_type']) {
            if (!current_user_can('edit_page', $post_id))
                return;
        }
        else {
            if (!current_user_can('edit_post', $post_id))
                return;
        }
        $post_ID = $_POST['post_ID'];
        if (!isset($_POST['portfolio_side_content'])) {
            $_POST['portfolio_side_content'] = '';
        }
        $mydata2 = $_POST['portfolio_side_content'];
        add_post_meta($post_ID, 'portfolio_side_content', $mydata2, true) or update_post_meta($post_ID, 'portfolio_side_content', $mydata2);
    }