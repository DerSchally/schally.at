<?php
add_action('add_meta_boxes', 'anps_heading_add_custom_box');
add_action('save_post', 'anps_heading_save_postdata');

function anps_heading_add_custom_box() {
    $screens = array('page', 'post', 'team');
    foreach($screens as $screen) {
        if($screen=="product") {
            $anps_priority = "low";
        } else {
            $anps_priority = "high";
        }
        add_meta_box('anps_page_meta', esc_html__('Page options', 'anps_theme_plugin'), 'anps_display_meta_box_page', $screen, 'normal', $anps_priority);
    }
    add_meta_box('anps_page_meta', esc_html__('Page options', 'anps_theme_plugin'), 'anps_display_meta_box_page', 'portfolio', 'normal', 'core');
}

function anps_display_meta_box_page( $post ) {    
    wp_enqueue_style('wp-color-picker');
    wp_enqueue_script("wp-colorpicker");
    wp_enqueue_script("wp-backend");

    $value2 = get_post_meta($post->ID, $key ='anps_disable_heading', $single = true );
    $value_breadcrumbs = get_post_meta($post->ID, $key ='anps_disable_page_breadcrumbs', $single = true );
    $heading_value = get_post_meta($post->ID, $key ='anps_heading_bg', $single = true );
    $page_heading_full = get_post_meta($post->ID, $key ='anps_page_heading_full', $single = true );
    $anps_page_heading_video = get_post_meta($post->ID, $key ='anps_page_heading_video', $single = true );
    $color_title = get_post_meta($post->ID, $key ='anps_color_title', $single = true );
    $color_page = get_post_meta($post->ID, $key ='anps_color_page', $single = true );
    $full_color_top_bar = get_post_meta($post->ID, $key ='anps_full_color_top_bar', $single = true );
    $full_color_title = get_post_meta($post->ID, $key ='anps_full_color_title', $single = true );
    $full_hover_color = get_post_meta($post->ID, $key ='anps_full_hover_color', $single = true );
    $full_screen_logo = get_post_meta($post->ID, $key ='anps_full_screen_logo', $single = true );
    $anps_page_sticky_logo = get_post_meta($post->ID, $key ='anps_page_sticky_logo', $single = true );
    $anps_page_mobile_logo = get_post_meta($post->ID, $key ='anps_page_mobile_logo', $single = true );
    $anps_page_menu_button = get_post_meta($post->ID, $key ='anps_page_menu_button', $single = true );
    $page_overlay_color = get_post_meta($post->ID, $key ='anps_page_overlay_color', $single = true );
    $page_opacity = get_post_meta($post->ID, $key ='anps_page_opacity', $single = true );
    $anps_page_heading_fontsize = get_post_meta($post->ID, $key ='anps_page_heading_fontsize', $single = true );
    $checked = '';
    if($value2=='1') {
        $checked = 'checked';
    }
    $checked_breadcrumbs = '';
    if($value_breadcrumbs=='1') {
        $checked_breadcrumbs = 'checked';
    }
    ?>
   <p></p>
    <table class="page-title min300">
        <tr>
            <td><?php esc_html_e('Disable breadcrumbs', 'anps_theme_plugin'); ?></td>
            <td>
                <input type='checkbox' name='anps_disable_page_breadcrumbs' value='1' <?php echo esc_attr($checked_breadcrumbs); ?>/>
            </td>
        </tr>
        <tr>
            <td><?php esc_html_e('Disable heading', 'anps_theme_plugin'); ?></td>
            <td>
                <input class="hideall-trigger" type='checkbox' name='anps_disable_heading' value='1' <?php echo esc_attr($checked); ?>/>
            </td>
        </tr>
    </table>
    <table class="page-title hideall min300">
        <tr>
            <td>
                <label for="anps_heading_bg"><?php esc_html_e("Page heading background", 'anps_theme_plugin'); ?></label>
            </td>
            <td>
                <input id="anps_heading_bg" type="text" size="36" name="anps_heading_bg" value="<?php echo esc_attr($heading_value); ?>" />
                <input id="_btn" class="upload_image_button button" type="button" value="Upload" />
            </td>
        </tr>
        <tr>
            <td>
                <label for="anps_full_screen_logo"><?php esc_html_e("Logo", 'anps_theme_plugin'); ?></label>
            </td>
            <td>
                <?php $images = get_children('post_type=attachment&post_mime_type=image'); ?>
                <select id="anps_full_screen_logo" name="anps_full_screen_logo">
                    <option value="0"><?php esc_html_e('Select logo', 'anps_theme_plugin'); ?></option>
                    <?php foreach ($images as $item) : ?>
                        <option <?php if ($item->guid == $full_screen_logo) {
                            echo 'selected="selected"';
                        } ?> value="<?php echo esc_attr($item->guid); ?>"><?php echo esc_html($item->post_title); ?></option>
                <?php endforeach; ?>
                </select>
            </td>
        </tr>
        <tr>
            <td>
                <label for="anps_page_sticky_logo"><?php esc_html_e("Sticky logo", 'anps_theme_plugin'); ?></label>
            </td>
            <td>
                <?php $images = get_children('post_type=attachment&post_mime_type=image'); ?>
                <select id="anps_page_sticky_logo" name="anps_page_sticky_logo">
                    <option value="0"><?php esc_html_e('Select sticky logo', 'anps_theme_plugin'); ?></option>
                    <?php foreach ($images as $item) : ?>
                        <option <?php if ($item->guid == $anps_page_sticky_logo) {
                            echo 'selected="selected"';
                        } ?> value="<?php echo esc_attr($item->guid); ?>"><?php echo esc_html($item->post_title); ?></option>
                <?php endforeach; ?>
                </select>
            </td>
        </tr>
        <tr>
            <td>
                <label for="anps_page_mobile_logo"><?php esc_html_e("Mobile logo", 'anps_theme_plugin'); ?></label>
            </td>
            <td>
                <?php $images = get_children('post_type=attachment&post_mime_type=image'); ?>
                <select id="anps_page_mobile_logo" name="anps_page_mobile_logo">
                    <option value="0"><?php esc_html_e('Select mobile logo', 'anps_theme_plugin'); ?></option>
                    <?php foreach ($images as $item) : ?>
                        <option <?php if ($item->guid == $anps_page_mobile_logo) {
                            echo 'selected="selected"';
                        } ?> value="<?php echo esc_attr($item->guid); ?>"><?php echo esc_html($item->post_title); ?></option>
                <?php endforeach; ?>
                </select>
            </td>
        </tr>
        <tr>
            <td>
                <?php esc_html_e("Page header style", 'anps_theme_plugin'); ?>
            </td>
            <td>
                <?php
                    $header_style_options = array(
                        'normal' => esc_html__('Normal', 'anps_theme_plugin'),
                        'large' => esc_html__('Large', 'anps_theme_plugin'),
                    );

                    $page_heading_full = str_replace('on', 'large', $page_heading_full);
                ?>

                <select id="anps_page_heading_full" name="anps_page_heading_full">
                    <option value=''></option>
                    <?php foreach ($header_style_options as $value => $label) : ?>
                        <option <?php if ($value == $page_heading_full) {
                            echo 'selected="selected"';
                        } ?> value="<?php echo esc_attr($value); ?>"><?php echo esc_html($label); ?></option>
                    <?php endforeach; ?>
                </select>
            </td>
        </tr>
        <tr>
            <td>
                <?php esc_html_e("Menu button", 'anps_theme_plugin'); ?>
            </td>
            <td>
                <?php
                    $buttons_data = json_decode(stripslashes(get_option('anps-buttons', '[]')));
                    $buttons = '';
                    if(isset($buttons_data->buttons) && $buttons_data != '') {
                        $buttons = $buttons_data->buttons;
                    }
                ?>

                <select id="anps_page_menu_button" name="anps_page_menu_button">
                    <option value=''></option>
                    <?php if(is_array($buttons)) : ?>
                        <?php foreach ($buttons as $button) : ?>
                            <option <?php if ("{$button->id}" === $anps_page_menu_button) {
                                echo 'selected="selected"';
                            } ?> value="<?php echo esc_attr($button->id); ?>"><?php echo esc_html($button->name); ?></option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </td>
        </tr>
        <tr>
            <td>
                <label for="anps_page_heading_fontsize"><?php esc_html_e('Page heading font size', 'anps_theme_plugin'); ?></label>
            </td>
            <td>
                <input type='number' id='anps_page_heading_fontsize' name='anps_page_heading_fontsize' value='<?php echo esc_attr($anps_page_heading_fontsize); ?>' />
            </td>
        <tr>
        <tr class="hideshow">
            <td>
                <label for="anps_color_title"><?php esc_html_e("Title color", 'anps_theme_plugin'); ?></label>
            </td>
            <td>
                <input class='color-field' type='text' id='anps_color_title' value='<?php echo esc_attr($color_title); ?>' name='anps_color_title' />
            </td>
        </tr>
        <tr>
            <td>
                <label for="anps_color_page"><?php esc_html_e("Page background color", 'anps_theme_plugin'); ?></label>
            </td>
            <td>
                <input class='color-field' type='text' id='anps_color_page' value='<?php echo esc_attr($color_page); ?>' name='anps_color_page' data-default-color='' />
            </td>
        </tr>
        <tr>
            <td>
                <label for="anps_page_overlay_color"><?php esc_html_e("Overlay color", 'anps_theme_plugin'); ?></label>
            </td>
            <td>
                <input class='color-field' type='text' id='anps_page_overlay_color' name='anps_page_overlay_color' value='<?php echo esc_attr($page_overlay_color); ?>' />
            </td>
        </tr>
        <tr>
            <td>
                <label for="anps_page_opacity"><?php esc_html_e("Opacity", 'anps_theme_plugin'); ?></label>
            </td>
            <td>
                <input type='text' id='anps_page_opacity' name='anps_page_opacity' value='<?php echo esc_attr($page_opacity); ?>' placeholder="0.8" />
            </td>
        </tr>
    </table>
<table class="page-title showhide hideall min300">
    <tr>
        <td>
            <label for="anps_page_heading_video"><?php esc_html_e('Page heading video background', 'anps_theme_plugin'); ?></label>
        </td>
        <td>
            <input type="text" name="anps_page_heading_video" id="anps_page_heading_video" value="<?php echo esc_attr($anps_page_heading_video); ?>">
        </td>
    </tr>
    <tr>
        <td>
            <label for="anps_full_color_top_bar"><?php esc_html_e("Top bar color", 'anps_theme_plugin'); ?></label>
        </td>
        <td>
            <input class='color-field' type='text' id='anps_full_color_top_bar' value='<?php echo esc_attr($full_color_top_bar); ?>' name='anps_full_color_top_bar'/>
        </td>
    </tr>
    <tr>
        <td>
            <label for="anps_full_color_title"><?php esc_html_e("Menu and title color", 'anps_theme_plugin'); ?></label>
        </td>
        <td>
            <input class='color-field' type='text' id='anps_full_color_title' value='<?php echo esc_attr($full_color_title); ?>' name='anps_full_color_title' />
        </td>
    </tr>
    <tr>
        <td>
            <label for="anps_full_hover_color"><?php esc_html_e("Hover color", 'anps_theme_plugin'); ?></label>
        </td>
        <td>
            <input class='color-field' type='text' id='anps_full_hover_color' name='anps_full_hover_color' value='<?php echo esc_attr($full_hover_color); ?>' />
        </td>
    </tr>
    </table>


    <script>
        jQuery(document).ready(function() {
    var formfield;
    jQuery('.upload_image_button').click(function() {
        jQuery('html').addClass('Image');
        formfield = jQuery(this).prev().attr('name');
        tb_show('', 'media-upload.php?type=image&TB_iframe=true');
        return false;
    });
    window.original_send_to_editor = window.send_to_editor;
    window.send_to_editor = function(html){
        if (formfield) {
            fileurl = jQuery('img','<div>' + html + '</div>').attr('src');
            jQuery('#'+formfield).val(fileurl);
            tb_remove();
            jQuery('html').removeClass('Image');
            formfield = '';
        } else {
            window.original_send_to_editor(html);
        }
    };

});
    </script>
<?php
}

function anps_heading_save_postdata($post_id) {
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
        return;
    if (empty($_POST))
        return;

    $post_ID = $_POST['post_ID'];

    if (!isset($_POST['anps_disable_heading'])) {
        $_POST['anps_disable_heading'] = '0';
    }
    $mydata2 = $_POST['anps_disable_heading'];
    if (!isset($_POST['anps_disable_page_breadcrumbs'])) {
        $_POST['anps_disable_page_breadcrumbs'] = '0';
    }

    $bread_data = $_POST['anps_disable_page_breadcrumbs'];

    if (!isset($_POST['anps_heading_bg'])) {
        $_POST['anps_heading_bg'] = '';
    }
    $heading_data = $_POST['anps_heading_bg'];

    if (!isset($_POST['anps_page_heading_full'])) {
        $_POST['anps_page_heading_full'] = '';
    }
    $page_heading_full = $_POST['anps_page_heading_full'];

    if (!isset($_POST['anps_page_heading_video'])) {
        $_POST['anps_page_heading_video'] = '';
    }
    $anps_page_heading_video = $_POST['anps_page_heading_video'];

    if (!isset($_POST['anps_full_color_top_bar'])) {
        $_POST['anps_full_color_top_bar'] = '';
    }
    $full_color_top_bar = $_POST['anps_full_color_top_bar'];

    if (!isset($_POST['anps_full_color_title'])) {
        $_POST['anps_full_color_title'] = '';
    }
    $full_color_title = $_POST['anps_full_color_title'];

    if (!isset($_POST['anps_color_title'])) {
        $_POST['anps_color_title'] = '';
    }
    $color_title = $_POST['anps_color_title'];

    $color_page = isset($_POST['anps_color_page']) ? $_POST['anps_color_page'] : '';

    if (!isset($_POST['anps_full_hover_color'])) {
        $_POST['anps_full_hover_color'] = '';
    }
    $full_hover_color = $_POST['anps_full_hover_color'];

    if (!isset($_POST['anps_full_screen_logo'])) {
        $_POST['anps_full_screen_logo'] = '';
    }
    $full_screen_logo = $_POST['anps_full_screen_logo'];

    if (!isset($_POST['anps_page_sticky_logo'])) {
        $_POST['anps_page_sticky_logo'] = '';
    }
    $anps_page_sticky_logo = $_POST['anps_page_sticky_logo'];

    if (!isset($_POST['anps_page_mobile_logo'])) {
        $_POST['anps_page_mobile_logo'] = '';
    }
    $anps_page_mobile_logo = $_POST['anps_page_mobile_logo'];

    if (!isset($_POST['anps_page_menu_button'])) {
        $_POST['anps_page_menu_button'] = '';
    }
    $anps_page_menu_button = $_POST['anps_page_menu_button'];
    
    if (!isset($_POST['anps_page_overlay_color'])) {
        $_POST['anps_page_overlay_color'] = '';
    }
    $anps_page_overlay_color = $_POST['anps_page_overlay_color'];
    
    if (!isset($_POST['anps_page_opacity'])) {
        $_POST['anps_page_opacity'] = '';
    }
    $anps_page_opacity = $_POST['anps_page_opacity'];
    
    if (!isset($_POST['anps_page_heading_fontsize'])) {
        $_POST['anps_page_heading_fontsize'] = '';
    }
    $anps_page_heading_fontsize = $_POST['anps_page_heading_fontsize'];
    
    add_post_meta($post_ID, 'anps_disable_heading', $mydata2, true) or update_post_meta($post_ID, 'anps_disable_heading', $mydata2);
    add_post_meta($post_ID, 'anps_disable_page_breadcrumbs', $bread_data, true) or update_post_meta($post_ID, 'anps_disable_page_breadcrumbs', $bread_data);
    add_post_meta($post_ID, 'anps_heading_bg', $heading_data, true) or update_post_meta($post_ID, 'anps_heading_bg', $heading_data);
    add_post_meta($post_ID, 'anps_page_heading_full', $page_heading_full, true) or update_post_meta($post_ID, 'anps_page_heading_full', $page_heading_full);
    add_post_meta($post_ID, 'anps_page_heading_video', $anps_page_heading_video, true) or update_post_meta($post_ID, 'anps_page_heading_video', $anps_page_heading_video);
    add_post_meta($post_ID, 'anps_full_color_top_bar', $full_color_top_bar, true) or update_post_meta($post_ID, 'anps_full_color_top_bar', $full_color_top_bar);
    add_post_meta($post_ID, 'anps_color_title', $color_title, true) or update_post_meta($post_ID, 'anps_color_title', $color_title);
    add_post_meta($post_ID, 'anps_color_page', $color_page, true) or update_post_meta($post_ID, 'anps_color_page', $color_page);
    add_post_meta($post_ID, 'anps_full_color_title', $full_color_title, true) or update_post_meta($post_ID, 'anps_full_color_title', $full_color_title);
    add_post_meta($post_ID, 'anps_full_hover_color', $full_hover_color, true) or update_post_meta($post_ID, 'anps_full_hover_color', $full_hover_color);
    add_post_meta($post_ID, 'anps_full_screen_logo', $full_screen_logo, true) or update_post_meta($post_ID, 'anps_full_screen_logo', $full_screen_logo);
    add_post_meta($post_ID, 'anps_page_sticky_logo', $anps_page_sticky_logo, true) or update_post_meta($post_ID, 'anps_page_sticky_logo', $anps_page_sticky_logo);
    add_post_meta($post_ID, 'anps_page_mobile_logo', $anps_page_mobile_logo, true) or update_post_meta($post_ID, 'anps_page_mobile_logo', $anps_page_mobile_logo);
    add_post_meta($post_ID, 'anps_page_menu_button', $anps_page_menu_button, true) or update_post_meta($post_ID, 'anps_page_menu_button', $anps_page_menu_button);
    add_post_meta($post_ID, 'anps_page_overlay_color', $anps_page_overlay_color, true) or update_post_meta($post_ID, 'anps_page_overlay_color', $anps_page_overlay_color);
    add_post_meta($post_ID, 'anps_page_opacity', $anps_page_opacity, true) or update_post_meta($post_ID, 'anps_page_opacity', $anps_page_opacity);
    add_post_meta($post_ID, 'anps_page_heading_fontsize', $anps_page_heading_fontsize, true) or update_post_meta($post_ID, 'anps_page_heading_fontsize', $anps_page_heading_fontsize);
}
