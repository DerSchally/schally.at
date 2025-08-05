<?php
add_action('add_meta_boxes', 'anps_featured_video_add_custom_box');
add_action('save_post', 'anps_featured_video_save_postdata');

function anps_featured_video_add_custom_box() { 
    $screens = array('post', 'portfolio');
    foreach($screens as $screen) {
        add_meta_box('anps_featured_video_meta', esc_html__('Featured video', 'anps_theme_plugin'), 'anps_display_meta_box_featured_video', $screen, 'side');
    }
}

function anps_display_meta_box_featured_video( $post ) { 
    $value2 = get_post_meta($post->ID, $key ='anps_featured_video', $single = true ); 
    echo "<input class='w-255' type='text' name='anps_featured_video' value='".esc_attr($value2)."' />";
}

function anps_featured_video_save_postdata($post_id) {
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    if (empty($_POST)) {
        return;
    }
    if(!$_POST['post_ID']) {
        if(!$post_id) {
            return;
        } else {
            $_POST['post_ID'] = $post_id;
        }
    }
    $post_ID = $_POST['post_ID'];

    if (!isset($_POST['anps_featured_video'])) {
        $_POST['anps_featured_video'] = '';
    }

    $mydata2 = $_POST['anps_featured_video']; 

    add_post_meta($post_ID, 'anps_featured_video', $mydata2, true) or update_post_meta($post_ID, 'anps_featured_video', $mydata2);
}
