<?php
/*
Plugin Name: Anps Theme plugin
Plugin URI: http://www.anpsthemes.com
Description: Anps theme plugin
Author: Anpsthemes
Version: 1.0.1
Author URI: http://www.anpsthemes.com
Text Domain: anps_theme_plugin
Domain Path: /languages
*/

if(!defined('WPINC')) {
    die;
}

$version = '';
if (is_admin()) {
    if (!function_exists('get_plugin_data')) {
        require_once ABSPATH . 'wp-admin/includes/plugin.php';
    }

    $version = get_plugin_data(__FILE__);
    $version = $version['Version'];
}

/* Plugin constants */
define('ANPS_PLUGIN_VERSION', $version);
define('ANPS_PLUGIN_PATH', plugin_dir_path(__FILE__));
define('ANPS_PLUGIN_URL', plugin_dir_url(__FILE__));

/*updates*/
require 'plugin-updates/plugin-update-checker.php';
$AnpsUpdateChecker = Puc_v4_Factory::buildUpdateChecker(
	 'http://astudio.si/preview/plugins/ident/update.json',
	__FILE__,
	'anps_theme_plugin'
);

function anps_include_html($content) {
	echo '<input id="anps-theme-plugin-url" type="hidden" value="' . plugins_url() . '/anps_theme_plugin">';
}
add_action('wp_footer', 'anps_include_html');

// include helpers
include_once 'helpers.php';

/* Portfolio */
include_once 'portfolio.php';
add_action('init', 'anps_portfolio');
function anps_portfolio() {
    new Portfolio();
}
/* Team */
include_once 'team.php';
add_action('init', 'anps_team');
function anps_team() {
    new AnpsTeam();
}
/* Footer banner */
include_once 'footer_banner.php';
add_action('init', 'footer_banner');
function footer_banner() {
    new Footer_banner();
}
/* Load plugin admin_view.php page */
if(!function_exists('anps_plugin_options_do_page')) {
    function anps_plugin_options_do_page() {
        include_once ANPS_PLUGIN_PATH.'/views/admin_view.php';
    }
}
/* Include google analytics */
function anps_ga_wp_footer() {
    if(get_option('anps_google_analytics', '')!='') {
        echo get_option('anps_google_analytics', '');
    }
}
add_action('wp_footer', 'anps_ga_wp_footer');
