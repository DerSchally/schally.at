<?php
include_once get_template_directory() . '/anps-framework/classes/AnpsFramework.php';
class AnpsAdminPlugin extends AnpsFramework {
    public function save_options($redirect) {
        foreach($_POST as $name=>$value) {
            update_option($name, stripslashes($value));
        }
        if($redirect == 'whitelist_settings') {
            header("Location: edit.php?post_type=whitelist&page=anps_whitelist_settings");
        } elseif($redirect == 'whitelist_token_sale') {
            header("Location: edit.php?post_type=whitelist&page=anps_whitelist_settings&sub_page=token_sale");
        } else {
            header("Location: themes.php?page=anps_plugin_options&sub_page=$redirect");
        }
    }
}
$anps_plugin = new AnpsAdminPlugin();
