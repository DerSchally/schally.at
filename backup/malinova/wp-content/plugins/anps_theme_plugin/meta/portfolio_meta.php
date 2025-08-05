<?php
add_action('add_meta_boxes', 'anps_portfolio_content_add_custom_box');
add_action('save_post', 'anps_portfolio_content_save_postdata');
function anps_portfolio_content_add_custom_box() {
    $screens = array('portfolio');
    foreach ($screens as $screen) {
        add_meta_box('anps_hide_portfolio_meta', esc_html__('Project info table', 'anps_theme_plugin'), 'anps_hide_portolio', $screen, 'side', 'core');
    }
    $pages = array('portfolio', 'post');
    foreach($pages as $screen) {
        add_meta_box('anps_portfolio_side_meta', esc_html__('Breadcrumbs parent page', 'anps_theme_plugin'), 'anps_display_portfolio_breadcrumbs_meta_box', $screen, 'side', 'core');
    }
}
function anps_hide_portolio($post) {
    $socials_value = get_post_meta($post->ID, $key ='anps_portfolio_table_repeater', $single = true );
    $socials = explode('|', $socials_value);

    ?>
        <!-- Social Iconpickers -->
        <div data-anps-repeat>
            <!-- Social Icons field (hidden) -->
            <input data-anps-repeat-field type="hidden" name="anps_portfolio_table_repeater" value="<?php echo esc_attr($socials_value); ?>">

            <!-- Repeater items wrapper -->
            <div class="anps-repeat-items" data-anps-repeat-items>
                <?php foreach($socials as $social) : ?>
                <div class="anps-repeat-item" data-anps-repeat-item>
                    <!-- Fields -->
                    <p>

                        <?php
                            $social = explode(';', $social);
                            $social_icon = '';
                            $social_url = '';

                            if( isset($social[0]) ) {
                                 $social_icon = $social[0];
                            }

                            if( isset($social[1]) ) {
                                 $social_url = $social[1];
                            }
                        ?>
                        <div class="anps-iconpicker">
                            <i class="fa <?php echo esc_attr($social_icon); ?>"></i>
                            <input type="text" value="<?php echo esc_attr($social_icon); ?>">
                            <button type="button"><?php esc_html_e('Select icon', 'anps_theme_plugin'); ?></button>
                        </div>
                    </p>
                    <p>
                        <input type="text" class="widefat" value="<?php echo esc_attr($social_url); ?>" />
                    </p>

                    <!-- Repeater buttons -->
                    <div class="anps-repeat-buttons">
                        <button class="anps-repeat-remove" type="button" data-anps-repeat-remove>-</button>
                        <button class="anps-repeat-add" type="button" data-anps-repeat-add>+</button>
                    </div>
                </div>
                <?php endforeach; ?>
             </div>
        </div>
    <?php
}
function anps_display_portfolio_breadcrumbs_meta_box($post) {
    $custom_breadcrumbs = get_post_meta( $post -> ID, $key = 'custom_breadcrumbs', $single = true );
    ?>
    <select name="custom_breadcrumbs">
            <option value="0"><?php esc_html_e('— Select —', 'anps_theme_plugin'); ?></option>
            <?php
                    $pages = get_pages();
                    foreach ($pages as $item) :
                            if ($custom_breadcrumbs == $item->ID) {
                                    $selected = 'selected';
                            }
                            else {
                                    $selected = '';
                            }
            ?>      <option value="<?php echo esc_attr($item->ID); ?>" <?php echo esc_html($selected); ?>><?php echo esc_html($item->post_title); ?></option>
            <?php endforeach; ?>
    </select>
<?php }
function anps_portfolio_content_save_postdata($post_id) {
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    if (empty($_POST)) {
        return;
    }
    if(!isset($_POST['post_ID'])) {
        if(!$post_id) {
            return;
        } else {
            $_POST['post_ID'] = $post_id;
        }
    }
    if(!isset($_POST['post_type'])) {
        return;
    }
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

    if (!isset($_POST['anps_portfolio_shorttext'])) {
        $_POST['anps_portfolio_shorttext'] = '';
    }
    if (!isset($_POST['custom_breadcrumbs'])) {
        $_POST['custom_breadcrumbs'] = '';
    }
    if (!isset($_POST['anps_portfolio_table_repeater'])) {
        $_POST['anps_portfolio_table_repeater'] = '';
    }
    $table_repeater = $_POST['anps_portfolio_table_repeater'];
    $portfolio_shorttext = $_POST['anps_portfolio_shorttext'];
    $custom_breadcrumbs = $_POST['custom_breadcrumbs'];
    add_post_meta($post_ID, 'anps_portfolio_shorttext', $portfolio_shorttext, true) or update_post_meta($post_ID, 'anps_portfolio_shorttext', $portfolio_shorttext);
    add_post_meta($post_ID, 'custom_breadcrumbs', $custom_breadcrumbs, true) or update_post_meta($post_ID, 'custom_breadcrumbs', $custom_breadcrumbs);
    add_post_meta($post_ID, 'anps_portfolio_table_repeater', $table_repeater, true) or update_post_meta($post_ID, 'anps_portfolio_table_repeater', $table_repeater);
}
