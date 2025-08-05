<?php
add_action('add_meta_boxes', 'anps_footer_banner_add_custom_box');
add_action('save_post', 'anps_footer_banner_save_postdata');

function anps_footer_banner_add_custom_box() {
    $screens = array('post', 'page', 'portfolio', 'product', 'team');
    foreach ($screens as $screen) {
        add_meta_box('anps_footer_banner_meta', esc_html__('Footer banner', 'anps_theme_plugin'), 'anps_display_meta_box_footer_banner', $screen, 'side', 'core');     
    }
}
/* Top bar, above nav menu */
function anps_display_meta_box_footer_banner($post) {
    include_once(get_template_directory() . '/anps-framework/classes/AnpsFramework.php');
    $anps_framework = new AnpsFramework();
    $footer_banner_value = get_post_meta($post->ID, $key ='anps_footer_banner_page', $single = true );
    $data = $anps_framework->anps_create_select( 'anps_footer_banner_page', $anps_framework->anps_get_custom_posts_array('footer_banner'), '', $footer_banner_value, '1');
    $allowed_html = array(
        'select' => array(
            'name' => array(),
            'id' => array()
        ),
        'option' => array(
            'value' => array(),
            'selected' => array(),
        ),
        'div' => array(
            'anps-select' => array(),
        ),
    );
    if(empty($footer_banner_value)) {
        echo wp_kses($data, $allowed_html);
        echo '<a href="'.get_admin_url().'edit.php?post_type=footer_banner">'.esc_html__('Create footer banner', 'anps_theme_plugin').'</a>';
    }
}
function anps_footer_banner_save_postdata($post_id) {
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
    //header
    if (!isset($_POST['anps_footer_banner_page'])) {
        $_POST['anps_footer_banner_page'] = '';
    }
    //save data
    $footer_banner = $_POST['anps_footer_banner_page'];
    add_post_meta($post_ID, 'anps_footer_banner_page', $footer_banner, true) or update_post_meta($post_ID, 'anps_footer_banner_page', $footer_banner);
}