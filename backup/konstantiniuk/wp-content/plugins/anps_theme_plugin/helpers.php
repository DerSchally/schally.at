<?php
/* include all shortcodes from config file */
function anps_include_all_shortcodes($file) {
    $config_url = get_template_directory() . '/config/config_shortcodes.json';
    if(file_exists($config_url)) {
        $config_data = file_get_contents($config_url); 
        $shortcodes = json_decode($config_data);    
        foreach($shortcodes as $item) {
            $dir = plugin_dir_path( __FILE__ ).'shortcodes/'.$item;
            if(file_exists($dir.'/'.$file.'.php')) {
                include_once $dir.'/'.$file.'.php';
            }
        } 
    } else {
        $dir = plugin_dir_path( __FILE__ ).'shortcodes';  
        $results = scandir($dir);
        foreach($results as $item) {
            if ($item === '.' or $item === '..') { continue; }
            if (is_dir($dir . '/' . $item) && file_exists($dir.'/'.$item.'/'.$file.'.php')) {
                include_once $dir.'/'.$item.'/'.$file.'.php';
            }
        }
    }
}
/* get all cf7 forms */
function anps_get_all_cf7_forms() {
    $cf7 = get_posts('post_type="wpcf7_contact_form"&numberposts=-1');
    $contact_form_array = array();
    if($cf7) {
        foreach($cf7 as $cform) {
            $contact_form_array[$cform->post_title] = $cform->ID;
        }
    } else {
        $contact_form_array[esc_html__('No contact forms found', 'js_composer') ] = 0;
    }
    return $contact_form_array;
}
/* Blog categories new parameter */
function anps_blog_categories_settings_field($settings, $value) {
    $blog_data = '<select name="'.$settings['param_name'].'" class="wpb_vc_param_value wpb-input wpb-select '.$settings['param_name'].' '.$settings['type'].'">';
    $blog_data .= '<option class="0" value="0">'.esc_html__("All", 'anps_theme_plugin').'</option>';
    foreach(get_categories() as $val) {
        $selected = '';
        if ($value!=='' && $val->slug == $value) {
             $selected = ' selected';
        }
        $blog_data .= '<option class="'.$val->slug.'" value="'.$val->slug.'"'.$selected.'>'.$val->name.'</option>';
    }
    $blog_data .= '</select>';
    return $blog_data;
}

/* Button types */
function anps_buttons_field($settings, $value) {
    $return = '';
    $buttons_data = json_decode(stripslashes(get_option('anps-buttons', '[]')));
    $buttons = $buttons_data->buttons;

    $return .= '<select name="'.$settings['param_name'].'" class="wpb_vc_param_value wpb-input wpb-select '.$settings['param_name'].' '.$settings['type'].'">';

    foreach($buttons as $button) {
        $selected = $value == $button->id ? ' selected' : '';
        $return .= '<option' . $selected . ' value="' . $button->id . '">' . $button->name . '</option>';
    }

    $return .= '</select>';
    return $return;
}
/* Autocomplete input field */
function anps_autocomplete_field($settings, $value) {
    $preload = [];
    $selected_values = '';
    $value_array = $value;

    $max_items = 'null';

    if (isset($settings['max_items'])) {
        $max_items = $settings['max_items'];
    }

    if (!is_array($value)) {
        $value_array = explode(',', $value);
    }

    if (isset($settings['post_type'])) {
        $args = array(
            'post_type' => $settings['post_type'],
            'showposts' => '50',
        );
        $posts = new WP_Query($args);
        $posts = $posts->get_posts();

        foreach ($posts as $post) {
            $preload[] = array(
                'id'    => $post->ID,
                'title' => $post->post_title,
            );
        }

        foreach ($value_array as $val) {
            $selected_values .= '<option value="' . $val . '" selected="selected">' . get_the_title($id) . '</option>';
        }
    } else if (isset($settings['term'])) {
        $terms = get_terms($settings['term']);
        
        foreach ($terms as $term) {
            $preload[] = array(
                'id'    => $term->term_id,
                'title' => str_replace("'", 'anps_single_quote', $term->name),
            );
        }

        foreach ($value_array as $val) {
            $selected_values .= '<option value="' . $val . '" selected="selected">' . get_the_title($id) . '</option>';
        }
    } else if (isset($settings['data'])) {
        $data = $settings['data'];

        foreach ($data as $val => $name) {
            $preload[] = array(
                'id' => $val,
                'title' => $name,
            );
        }

        foreach ($value_array as $val) {
            $selected_values .= '<option value="' . $val . '" selected="selected">' . $val . '</option>';
        }
    }

    return '<select
        multiple="multiple"
        class="wpb_vc_param_value wpb-input wpb-select columns dropdown"
        name="' . $settings['param_name'] . '"
        data-max-items="' . $max_items . '"
        data-autocomplete=\'' . json_encode($preload) . '\'>'
            . $selected_values .
    '</select>';
}
/* Tags input field */
function anps_tags_field($settings, $value) {
    $max_items = 'null';

    if (isset($settings['max_items'])) {
        $max_items = $settings['max_items'];
    }

    return '<input
        data-max-items="' . $max_items . '"
        class="wpb_vc_param_value wpb-textinput textfield"
        type="text"
        name="' . $settings['param_name'] . '"
        data-tags
        value="' . $value . '"
    />';
}
/* Responsive input field */
function anps_input_field($settings, $value) {
    if ($value === '') {
        $value = '|||';
    }
    $values = explode('|', $value);
    $data = '';
    $data .= '<div class="anps-input">';
        $data .= '<input name="' . $settings['param_name'] . '" type="text" class="anps-input__hidden wpb-textinput textfield ' . $settings['param_name'] . ' wpb_vc_param_value" value="' . $value . '">';
        $data .= '<input type="text" class="anps-input__field wpb-textinput text textfield" value="' . $values[3] . '">';
        $data .= '<div class="anps-input__select">';
            $data .= '<button class="anps-input__active"><i class="fa fa-desktop"></i></button>';
            $data .= '<ul class="anps-input__list">';
                $data .= '<li class="anps-input__item"><button class="anps-input__button" data-val="xs" title="Mobile"><i class="fa fa-mobile"></i></button></li>';
                $data .= '<li class="anps-input__item"><button class="anps-input__button" data-val="sm" title="Tablet"><i class="fa fa-tablet"></i></button></li>';
                $data .= '<li class="anps-input__item"><button class="anps-input__button" data-val="md" title="Laptop"><i class="fa fa-laptop"></i></button></li>';
                $data .= '<li class="anps-input__item anps-input__item--active"><button class="anps-input__button" data-val="lg" title="Desktop"><i class="fa fa-desktop"></i></button></li>';
            $data .= '</ul>';
            $data .= '</div>';
    $data .= '</div>';
    return $data;
}
/* Portfolio categories new parameter */
function anps_portfolio_categories_settings_field($settings, $value) {
    $categories = get_terms('portfolio_category');
    $data = '<select name="'.$settings['param_name'].'" class="wpb_vc_param_value wpb-input wpb-select '.$settings['param_name'].' '.$settings['type'].'">';
    $data .= '<option class="0" value="0">'.esc_html__("All", 'anps_theme_plugin').'</option>';
    foreach($categories as $val) {
        $selected = '';
        if ($value!=='' && $val->term_id == $value) {
             $selected = ' selected';
        }
        $data .= '<option class="'.$val->term_id.'" value="'.$val->term_id.'"'.$selected.'>'.$val->name.'</option>';
    }
    $data .= '</select>';
    return $data;
}
/* All pages new parameter */
function anps_all_pages_settings_field($settings, $value) {
    $data = '<select name="'.$settings['param_name'].'" class="wpb_vc_param_value wpb-input wpb-select '.$settings['param_name'].' '.$settings['type'].'">';
    foreach(get_pages() as $val) {
        $selected = '';
        if ($value!=='' && $val->ID == $value) {
             $selected = ' selected';
        }
        $data .= '<option class="'.$val->ID.'" value="'.$val->ID.'"'.$selected.'>'.$val->post_title.'</option>';
    }
    $data .= '</select>';
    return $data;
}

function get_anps_icons() {
    $dir = plugin_dir_path( __FILE__ ).'icons';  
    $results = scandir($dir);
    $icons = array();
    foreach($results as $item) {
        if ($item === '.' or $item === '..') { continue; }
        
        $icon_name = str_replace('.svg', '', $item);

        $icons[] = $icon_name;
    }

    return $icons;
}

function anps_icons_style() {
    echo '<style>';
    $anps_icons = get_anps_icons();
    foreach($anps_icons as $icon_name) {
        $icon = sanitize_title($icon_name);
        $url = plugins_url('anps_theme_plugin');
        echo ".anps-icon-{$icon} { background-image: url('{$url}/icons/{$icon_name}.svg'); }";
    }
    echo '</style>';
}
add_action('admin_head', 'anps_icons_style');

// Add new custom font to Font Family selection in icon box module
function anps_icons() {
    $param = WPBMap::getParam( 'vc_icon', 'type' );
    $param['value'][__( 'Anpsthemes icons', 'anps_theme_plugin' )] = 'anps_icons';
    vc_update_shortcode_param( 'vc_icon', $param );
}
if (class_exists('WPBMap')) {
    add_filter( 'init', 'anps_icons', 40 );
}

function anps_icons_add( $icons ) {
    $icons_anps = get_anps_icons();

    $icons_vc = array();

    foreach($icons_anps as $icon) {
        $icons_vc[] = array('anps-icon-' . sanitize_title($icon) => $icon);
    }

    return $icons_vc;
}
add_filter( 'vc_iconpicker-type-anps_icons', 'anps_icons_add' );
/* Add new post status */
function anps_new_post_status(){
    register_post_status('anps_new', array(
            'label'                     => _x( 'New', 'anps_theme_plugin' ),
            'public'                    => true,
            'show_in_admin_all_list'    => false,
            'show_in_admin_status_list' => true,
            'label_count'               => _n_noop( 'New <span class="count">(%s)</span>', 'New <span class="count">(%s)</span>' ),
    ) );
}
add_action( 'init', 'anps_new_post_status' );

/* Display post state in admin */
function anps_display_new_state( $states ) {
     global $post;
     $arg = get_query_var( 'post_status' );
     if($arg != 'anps_new'){
          if($post->post_status == 'anps_new'){
               return array('New');
          }
     }
    return $states;
}
add_filter( 'display_post_states', 'anps_display_new_state' );