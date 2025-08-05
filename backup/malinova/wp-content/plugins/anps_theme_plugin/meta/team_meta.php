<?php
add_action('add_meta_boxes', 'anps_team_content_add_custom_box');
add_action('save_post', 'anps_team_content_save_postdata');
function anps_team_content_add_custom_box() {
    $screens = array('team');
    foreach ($screens as $screen) {
        add_meta_box('anps_team_social_meta', esc_html__('Social accounts', 'anps_theme_plugin'), 'anps_team_social_meta_box_content', $screen, 'side', 'core');
    }
    foreach ($screens as $screen) {
        add_meta_box(
                'anps_breadcrumbs_sectionid', esc_html__('Team subtitle', 'anps_theme_plugin'), 'anps_display_team_meta_box_content', $screen
        );
    }
}
function anps_team_social_meta_box_content($post) {
    $socials_value = get_post_meta($post->ID, $key ='anps_team_social', $single = true ); 
    $socials = explode('|', $socials_value);
    ?>

    <script>
        jQuery(function($) {
            $(document).ready(anpsColorPickerReady);
        });
    </script>

        <!-- Social Iconpickers -->
        <div data-anps-repeat>
            <!-- Social Icons field (hidden) -->
            <input data-anps-repeat-field type="hidden" name="anps_team_social" value="<?php echo esc_attr($socials_value); ?>">

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
                            $social_bg = '';

                            if( isset($social[0]) ) {
                                $social_icon = $social[0];
                            }

                            if( isset($social[1]) ) {
                                $social_url = $social[1];
                            }

                            if( isset($social[2]) ) {
                                $social_bg = $social[2];
                            }
                        ?>
                        <div class="anps-iconpicker">
                            <i class="fa <?php echo esc_attr($social_icon); ?>"></i>
                            <input type="text" value="<?php echo esc_html($social_icon); ?>">
                            <button type="button"><?php esc_html_e('Select icon', 'anps_theme_plugin'); ?></button>
                        </div>
                    </p>
                    <p>
                        <label><?php esc_html_e("URL", 'anps_theme_plugin'); ?></label><br />
                        <input type="text" class="widefat" value="<?php echo esc_attr($social_url); ?>" />
                    </p>

                    <!-- Background color -->
                    <p>
                        <label><?php esc_html_e("Background color", 'anps_theme_plugin'); ?></label><br />
                        <input class='color-field anps-color-picker' type='text' value='<?php echo esc_attr($social_bg); ?>' />
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
function anps_display_team_meta_box_content( $post ) {
        $value2 = get_post_meta( $post -> ID, $key = 'anps_team_subtitle', $single = true );
	echo "<input class='w-350' type='text' name='anps_team_subtitle' value='".esc_attr($value2)."' />";
}
function anps_team_content_save_postdata($post_id) { 
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
    if ('team' == $_POST['post_type']) {
        if (!current_user_can('edit_page', $post_id))
            return;
    }
    else {
        if (!current_user_can('edit_post', $post_id))
            return;
    }
    if (!isset($_POST['anps_team_social'])) {
        $_POST['anps_team_social'] = '';
    }
    $team_social = $_POST['anps_team_social'];
    $post_ID = $_POST['post_ID'];
    if (!isset($_POST['anps_team_subtitle']))
        $_POST['anps_team_subtitle'] = '';
    $mydata2 = $_POST['anps_team_subtitle'];
    add_post_meta($post_ID, 'anps_team_subtitle', $mydata2, true) or update_post_meta($post_ID, 'anps_team_subtitle', $mydata2);
    add_post_meta($post_ID, 'anps_team_social', $team_social, true) or update_post_meta($post_ID, 'anps_team_social', $team_social);
}
