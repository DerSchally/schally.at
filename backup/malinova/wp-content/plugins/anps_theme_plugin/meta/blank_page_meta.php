<?php
add_action('add_meta_boxes', 'anps_blank_page_add_custom_box');
add_action('save_post', 'anps_blank_page_save_postdata');

function anps_blank_page_add_custom_box() {
    add_meta_box('anps_blank_page_meta', esc_html__('Blank page', 'anps_theme_plugin'), 'anps_display_meta_box_blank_page', 'page', 'side', 'core');   
}
function anps_display_meta_box_blank_page($post) {
    $header_value = get_post_meta($post->ID, $key ='anps_blank_page_disable_header', $single = true ); 
    $footer_value = get_post_meta($post->ID, $key ='anps_blank_page_disable_footer', $single = true ); 
    $header_checked = checked($header_value, 'on', false);
    $footer_checked = checked($footer_value, 'on', false);
    $data = '';
    $data .= '<ul>';
    $data .= '<li>';
    $data .= '<label class="selectit">';
    $data .= "<input id='anps_blank_page_disable_header' name='anps_blank_page_disable_header' type='checkbox' $header_checked>";
    $data .= esc_html__('Disable Header', 'anps_theme_plugin');
    $data .= '</label>';
    $data .= '</li>';
    $data .= '<li>';
    $data .= '<label class="selectit">';
    $data .= "<input id='anps_blank_page_disable_footer' name='anps_blank_page_disable_footer' type='checkbox' $footer_checked>";
    $data .= esc_html__('Disable Footer', 'anps_theme_plugin');
    $data .= '</label>';
    $data .= '</li>';
    $data .= '</ul>';
    echo wp_kses($data, array(
        'ul' => array(),
        'li' => array(),
        'label' => array(
            'class' => array()
        ),
        'input' => array(
            'id' => array(),
            'name' => array(),
            'type' => array(),
            'checked' => array(),
        )
    ));
}
function anps_blank_page_save_postdata($post_id) {
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
    if (!isset($_POST['anps_blank_page_disable_header'])) {
        $_POST['anps_blank_page_disable_header'] = '';
    }
    //footer
    if (!isset($_POST['anps_blank_page_disable_footer'])) {
        $_POST['anps_blank_page_disable_footer'] = '';
    }
    //save data
    $data_header = $_POST['anps_blank_page_disable_header']; 
    $data_footer = $_POST['anps_blank_page_disable_footer']; 
    add_post_meta($post_ID, 'anps_blank_page_disable_header', $data_header, true) or update_post_meta($post_ID, 'anps_blank_page_disable_header', $data_header);
    add_post_meta($post_ID, 'anps_blank_page_disable_footer', $data_footer, true) or update_post_meta($post_ID, 'anps_blank_page_disable_footer', $data_footer);
}