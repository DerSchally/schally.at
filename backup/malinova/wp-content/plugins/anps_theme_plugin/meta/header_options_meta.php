<?php
add_action('add_meta_boxes', 'anps_header_options_add_custom_box');
add_action('save_post', 'anps_header_options_save_postdata');

function anps_header_options_add_custom_box() {
    add_meta_box('anps_header_options_meta', esc_html__('Header options', 'anps_theme_plugin'), 'anps_display_meta_box_header_options', 'page', 'side', 'core'); 
    add_meta_box('anps_spacing_options_meta', esc_html__('Spacing options', 'anps_theme_plugin'), 'anps_display_meta_box_spacing_options', 'page', 'side', 'core');       
}
/* Topa bar, above nav menu */
function anps_display_meta_box_header_options($post) {
    $top_bar = get_post_meta($post->ID, $key ='anps_header_options_top_bar', $single = true ); 
    $above_menu = get_post_meta($post->ID, $key ='anps_header_options_above_menu', $single = true ); 
    $select_array = array(esc_html__('Default', 'anps_theme_plugin'), esc_html__('Off', 'anps_theme_plugin'), esc_html__('On', 'anps_theme_plugin'));
    $data = '';
    $data .= '<div class="inside">';
    $data .= '<p>';
    $data .= '<label for="anps_header_options_top_bar">';
    $data .= '<strong>'.esc_html__('Top bar', 'anps_theme_plugin').'</strong>';
    $data .= '</label>';
    $data .= '</p>';
    $data .= '<p>';
    $data .= '<select name="anps_header_options_top_bar" id="anps_header_options_top_bar">';
    foreach($select_array as $key=>$item) {
        $selected = '';
        if($key==$top_bar) {
           $selected = ' selected'; 
        }
        $data .= "<option value='$key'$selected>$item</option>";
    }
    $data .= '</select>';
    $data .= '</p>';
    $data .= '<p>';
    $data .= '<label class="selectit">';
    $data .= '<strong>'.esc_html__('Above menu', 'anps_theme_plugin').'</strong>';
    $data .= '</label>';
    $data .= '</p>';
    $data .= '<p>';
    $data .= '<select name="anps_header_options_above_menu">';
    foreach($select_array as $key=>$item) {
        $selected2 = '';
        if($key==$above_menu) {
           $selected2 = ' selected'; 
        }
        $data .= "<option value='$key'$selected2>$item</option>";
    }
    $data .= '</select>';
    $data .= '</p>';
    $data .= '</div>';
    echo wp_kses($data, array(
        'div' => array(
            'class' => array()
        ),
        'p' => array(),
        'label' => array(
            'for' => array()
        ),
        'strong' => array(),
        'select' => array(
            'name' => array(),
            'id' => array()
        ),
        'option' => array(
            'value' => array(),
            'selected' => array()
        )
    ));
}
/* Header/footer margin */
function anps_display_meta_box_spacing_options($post) {
    $header_value = get_post_meta($post->ID, $key ='anps_header_options_header_margin', $single = true ); 
    $footer_value = get_post_meta($post->ID, $key ='anps_header_options_footer_margin', $single = true ); 
    $header_margin_checked = checked($header_value, 'on', false);
    $footer_margin_checked = checked($footer_value, 'on', false);
    $data = '';
    $data .= '<ul>';
    $data .= '<li>';
    $data .= '<label class="selectit">';
    $data .= "<input id='anps_header_options_header_margin' name='anps_header_options_header_margin' type='checkbox' $header_margin_checked>";
    $data .= esc_html__('Remove Header Margin', 'anps_theme_plugin');
    $data .= '</label>';
    $data .= '</li>';
    $data .= '<li>';
    $data .= '<label class="selectit">';
    $data .= "<input id='anps_header_options_footer_margin' name='anps_header_options_footer_margin' type='checkbox' $footer_margin_checked>";
    $data .= esc_html__('Remove Footer Margin', 'anps_theme_plugin');
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
function anps_header_options_save_postdata($post_id) {
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
    if (!isset($_POST['anps_header_options_header_margin'])) {
        $_POST['anps_header_options_header_margin'] = '';
    }
    //footer
    if (!isset($_POST['anps_header_options_footer_margin'])) {
        $_POST['anps_header_options_footer_margin'] = '';
    }
    //top bar
    if (!isset($_POST['anps_header_options_top_bar'])) {
        $_POST['anps_header_options_top_bar'] = '';
    }
    //above menu
    if (!isset($_POST['anps_header_options_above_menu'])) {
        $_POST['anps_header_options_above_menu'] = '';
    }
    //save data
    $data_header = $_POST['anps_header_options_header_margin']; 
    $data_footer = $_POST['anps_header_options_footer_margin']; 
    $data_top_bar = $_POST['anps_header_options_top_bar']; 
    $data_above_menu = $_POST['anps_header_options_above_menu']; 
    add_post_meta($post_ID, 'anps_header_options_header_margin', $data_header, true) or update_post_meta($post_ID, 'anps_header_options_header_margin', $data_header);
    add_post_meta($post_ID, 'anps_header_options_footer_margin', $data_footer, true) or update_post_meta($post_ID, 'anps_header_options_footer_margin', $data_footer);
    add_post_meta($post_ID, 'anps_header_options_top_bar', $data_top_bar, true) or update_post_meta($post_ID, 'anps_header_options_top_bar', $data_top_bar);
    add_post_meta($post_ID, 'anps_header_options_above_menu', $data_above_menu, true) or update_post_meta($post_ID, 'anps_header_options_above_menu', $data_above_menu);
}