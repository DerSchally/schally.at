<?php
/***************************************************************************
 *                            admin_em_config.php
 *                            -------------------
 *   title                : EM WebTipp
 *   version              : 0.3.0
 *   begin                : Saturday, Feb 25, 2006
 *   copyright            : (C) 2006 AceVentura
 *   email                : 2714323@web.de
 *
 ***************************************************************************/

/***************************************************************************
 *
 *   This program is free software; you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *   the Free Software Foundation; either version 2 of the License, or
 *   (at your option) any later version.
 *
 ***************************************************************************/

define('IN_PHPBB', 1);

if( !empty($setmodules) )
{
	$filename = basename(__FILE__);
	$module['em_acp_menu_webtipp']['em_acp_menu_config'] = $filename;
	return;
}

//
// Load default header
//
$phpbb_root_path = "../";
require($phpbb_root_path . 'extension.inc');
require('pagestart.' . $phpEx);
include($phpbb_root_path . 'language/lang_' . $board_config['default_lang'] . '/lang_em.' . $phpEx);
include($phpbb_root_path . 'includes/functions_em.' . $phpEx);





$page_title =  $lang['em_title_home'];


//
// Pull all config data
//
$sql = "SELECT * FROM " . EM_CONFIG_TABLE;
if(!$result = $db->sql_query($sql))
{
	message_die(GENERAL_ERROR, "Could not query config information in em configuration", "", __LINE__, __FILE__, $sql);
}
else
{
	while( $row = $db->sql_fetchrow($result) )
	{
		$config_name = $row['config_name'];
		$config_value = $row['config_value'];
		$default_config[$config_name] = isset($HTTP_POST_VARS['submit']) ? str_replace("'", "\'", $config_value) : $config_value;

		$new[$config_name] = ( isset($HTTP_POST_VARS[$config_name]) ) ? $HTTP_POST_VARS[$config_name] : $default_config[$config_name];

		if( isset($HTTP_POST_VARS['submit']) )
		{
			$sql = "UPDATE " . EM_CONFIG_TABLE . " SET
				config_value = '" . str_replace("\'", "''", $new[$config_name]) . "'
				WHERE config_name = '$config_name'";
			if( !$db->sql_query($sql) )
			{
				message_die(GENERAL_ERROR, "Failed to update configuration for $config_name", "", __LINE__, __FILE__, $sql);
			}
		}
	}

	if( isset($HTTP_POST_VARS['submit']) )
	{
		$message = $lang['em_acp_config_updated'] . "<br /><br />" . sprintf($lang['Click_return_config'], "<a href=\"" . append_sid("admin_em_config.$phpEx") . "\">", "</a>") . "<br /><br />" . sprintf($lang['Click_return_admin_index'], "<a href=\"" . append_sid("index.$phpEx?pane=right") . "\">", "</a>");

		message_die(GENERAL_MESSAGE, $message);
	}
}

$template->set_filenames(array(
	'body' => 'admin/admin_em_config_body.tpl')
);


//
// Get all group data
//
$combo_groups_entries = '';
$sql = "SELECT * FROM " . GROUPS_TABLE . " WHERE group_single_user = 0";
if (!$result = $db->sql_query($sql))
{
	message_die(GENERAL_ERROR, 'Could not query groups data', '', __LINE__, __FILE__, $sql);
}
while ($row = $db->sql_fetchrow($result))
{
	$selected = ( $row['group_id'] == $new['restrict_to'] ) ? 'selected' : '';
	$combo_groups_entries .= '<option value="' . $row['group_id'] . '" ' . $selected . '>' . $row['group_name'] . '</option>';
}
$db->sql_freeresult($result);

//
// Generate groups combobox
//
$selected = ( $new['restrict_to'] == 0 ) ? 'selected' : '';
$group_combo  = '<select name="restrict_to">';
$group_combo .= '<option value="0" ' . $selected . '>' . $lang['em_acp_deactivated'] . '</option>';
$group_combo .= $combo_groups_entries;
$group_combo .= '</select>';


//
// Get all forum data
//
$combo_forums_entries = '';
$sql = "SELECT * FROM " . FORUMS_TABLE . " ORDER BY forum_order ASC";
if (!$result = $db->sql_query($sql))
{
	message_die(GENERAL_ERROR, 'Could not query forums data', '', __LINE__, __FILE__, $sql);
}
while ($row = $db->sql_fetchrow($result))
{
	$selected = ( $row['forum_id'] == $new['em_forum_id'] ) ? 'selected' : '';
	$combo_forums_entries .= '<option value="' . $row['forum_id'] . '" ' . $selected . '>' . $row['forum_name'] . '</option>';
}
$db->sql_freeresult($result);

//
// Generate forums combobox
//
$selected = ( $new['forum_id'] == 0 ) ? 'selected' : '';
$forums_combo  = '<select name="em_forum_id">';
$forums_combo .= '<option value="0" ' . $selected . '>' . $lang['em_acp_deactivated'] . '</option>';
$forums_combo .= $combo_forums_entries;
$forums_combo .= '</select>';

//
// Get possible mods data
//
$combo_mod_entries = '';
$sql = "SELECT * FROM " . USERS_TABLE . " WHERE user_level > 0 ORDER BY user_id ASC";
if (!$result = $db->sql_query($sql))
{
	message_die(GENERAL_ERROR, 'Could not query user data', '', __LINE__, __FILE__, $sql);
}
while ($row = $db->sql_fetchrow($result))
{
	$selected = ( $row['user_id'] == $new['em_mod_id'] ) ? 'selected' : '';
	$combo_mod_entries .= '<option value="' . $row['user_id'] . '" ' . $selected . '>' . $row['username'] . '</option>';
}
$db->sql_freeresult($result);

//
// Generate possible mods combobox
//
$mods_combo  = '<select name="em_mod_id">';
$mods_combo .= $combo_mod_entries;
$mods_combo .= '</select>';

$teams_data = array();
$teams_data = get_em_teams_as_row();

$combo_wa = build_group_winner_combo('wa', $new['wa'], $teams_data);
$combo_ra = build_group_winner_combo('ra', $new['ra'], $teams_data);
$combo_wb = build_group_winner_combo('wb', $new['wb'], $teams_data);
$combo_rb = build_group_winner_combo('rb', $new['rb'], $teams_data);
$combo_wc = build_group_winner_combo('wc', $new['wc'], $teams_data);
$combo_rc = build_group_winner_combo('rc', $new['rc'], $teams_data);
$combo_wd = build_group_winner_combo('wd', $new['wd'], $teams_data);
$combo_rd = build_group_winner_combo('rd', $new['rd'], $teams_data);
//$combo_we = build_group_winner_combo('we', $new['we'], $teams_data);
//$combo_re = build_group_winner_combo('re', $new['re'], $teams_data);
//$combo_wf = build_group_winner_combo('wf', $new['wf'], $teams_data);
//$combo_rf = build_group_winner_combo('rf', $new['rf'], $teams_data);
//$combo_wg = build_group_winner_combo('wg', $new['wg'], $teams_data);
//$combo_rg = build_group_winner_combo('rg', $new['rg'], $teams_data);
//$combo_wh = build_group_winner_combo('wh', $new['wh'], $teams_data);
//$combo_rh = build_group_winner_combo('rh', $new['rh'], $teams_data);

//
// Generate points gfx active ComboBox
//
$pts_gfx_combo_entries  = '';
$selected1 = ( $new['points_grafical'] == '1' ) ? ' selected' : '';
$selected0 = ( $new['points_grafical'] == '0' ) ? ' selected' : '';
$pts_gfx_combo_entries .= '<option value="1" ' . $selected1 . '>' . $lang['l_em_grafical'] . '</option>';
$pts_gfx_combo_entries .= '<option value="0" ' . $selected0 . '>' . $lang['l_em_textual'] . '</option>';
$pts_gfx_combo  = '<select name="points_grafical">';
$pts_gfx_combo .= $pts_gfx_combo_entries;
$pts_gfx_combo .= '</select>';



$template->assign_vars(array(

	'L_ADMIN_EM_CONFIG_TITLE'             => $lang['em_acp_config_title'],
	'L_ADMIN_EM_CONFIG_TITLE_EXP'         => $lang['em_acp_config_title_exp'],
	'L_ADMIN_EM_CONFIG_BUTTON'            => $lang['em_acp_config_button'],
	'L_ADMIN_EM_CONFIG_GENERAL'           => $lang['em_acp_config_general'],
	'L_ADMIN_EM_CONFIG_TEAMWINNER'        => $lang['em_acp_config_teamwinner'],

	'L_ADMIN_EM_CONFIG_GROUP'             => $lang['em_acp_config_group'],
	'L_ADMIN_EM_CONFIG_GROUP_EXP'         => $lang['em_acp_config_group_exp'],
	'L_ADMIN_EM_CONFIG_FORUM'             => $lang['em_acp_config_forum'],
	'L_ADMIN_EM_CONFIG_FORUM_EXP'         => $lang['em_acp_config_forum_exp'],
	'L_ADMIN_EM_CONFIG_MOD'               => $lang['em_acp_config_mod'],
	'L_ADMIN_EM_CONFIG_MOD_EXP'           => $lang['em_acp_config_mod_exp'],
	'L_ADMIN_EM_CONFIG_PTS_GFX'           => $lang['em_acp_config_pts_gfx'],
	'L_ADMIN_EM_CONFIG_PTS_GFX_EXP'       => $lang['em_acp_config_pts_gfx_exp'],
	'L_ADMIN_EM_CONFIG_POINTSWINNER'      => $lang['em_acp_config_pointswinner'],
	'L_ADMIN_EM_CONFIG_POINTSWINNER_EXP'  => $lang['em_acp_config_pointswinner_exp'],
        'L_ADMIN_EM_CONFIG_POINTSMATCH'       => $lang['em_acp_config_pointsmatch'],
	'L_ADMIN_EM_CONFIG_POINTSMATCH_EXP'   => $lang['em_acp_config_pointsmatch_exp'],
	'L_ADMIN_EM_CONFIG_POINTSTORDIFF'     => $lang['em_acp_config_pointstordiff'],
	'L_ADMIN_EM_CONFIG_POINTSTORDIFF_EXP' => $lang['em_acp_config_pointstordiff_exp'],
        'L_ADMIN_EM_CONFIG_POINTSTEND'        => $lang['em_acp_config_pointstend'],
	'L_ADMIN_EM_CONFIG_POINTSTEND_EXP'    => $lang['em_acp_config_pointstend_exp'],
        'L_ADMIN_EM_CONFIG_TEAMS'             => $lang['em_acp_config_teams'],
	'L_ADMIN_EM_CONFIG_TEAMS_EXP'         => $lang['em_acp_config_teams_exp'],
	
	'L_ADMIN_EM_CONFIG_WA' => $lang['em_acp_config_wa'],
	'L_ADMIN_EM_CONFIG_RA' => $lang['em_acp_config_ra'],
	'L_ADMIN_EM_CONFIG_WB' => $lang['em_acp_config_wb'],
	'L_ADMIN_EM_CONFIG_RB' => $lang['em_acp_config_rb'],
	'L_ADMIN_EM_CONFIG_WC' => $lang['em_acp_config_wc'],
	'L_ADMIN_EM_CONFIG_RC' => $lang['em_acp_config_rc'],
	'L_ADMIN_EM_CONFIG_WD' => $lang['em_acp_config_wd'],
	'L_ADMIN_EM_CONFIG_RD' => $lang['em_acp_config_rd'],
//	'L_ADMIN_EM_CONFIG_WE' => $lang['em_acp_config_we'],
//	'L_ADMIN_EM_CONFIG_RE' => $lang['em_acp_config_re'],
//	'L_ADMIN_EM_CONFIG_WF' => $lang['em_acp_config_wf'],
//	'L_ADMIN_EM_CONFIG_RF' => $lang['em_acp_config_rf'],
//	'L_ADMIN_EM_CONFIG_WG' => $lang['em_acp_config_wg'],
//	'L_ADMIN_EM_CONFIG_RG' => $lang['em_acp_config_rg'],
//	'L_ADMIN_EM_CONFIG_WH' => $lang['em_acp_config_wh'],
//	'L_ADMIN_EM_CONFIG_RH' => $lang['em_acp_config_rh'],

	'ADMIN_EM_CONFIG_GROUP'              => $group_combo,
	'ADMIN_EM_CONFIG_FORUM'              => $forums_combo,
	'ADMIN_EM_CONFIG_MOD'                => $mods_combo,
	'ADMIN_EM_CONFIG_PTS_GFX'            => $pts_gfx_combo,
	'ADMIN_EM_CONFIG_POINTSWINNER'       => $new['points_winner'],
	'ADMIN_EM_CONFIG_POINTSTEND'         => $new['points_tendency'],
	'ADMIN_EM_CONFIG_POINTSMATCH'        => $new['points_match'],
        'ADMIN_EM_CONFIG_POINTSTORDIFF'      => $new['points_tordiff'],
	'ADMIN_EM_CONFIG_TEAMWA'             => $combo_wa,
	'ADMIN_EM_CONFIG_TEAMRA'             => $combo_ra,
	'ADMIN_EM_CONFIG_TEAMWB'             => $combo_wb,
	'ADMIN_EM_CONFIG_TEAMRB'             => $combo_rb,
	'ADMIN_EM_CONFIG_TEAMWC'             => $combo_wc,
	'ADMIN_EM_CONFIG_TEAMRC'             => $combo_rc,
	'ADMIN_EM_CONFIG_TEAMWD'             => $combo_wd,
	'ADMIN_EM_CONFIG_TEAMRD'             => $combo_rd,
//	'ADMIN_EM_CONFIG_TEAMWE'             => $combo_we,
//	'ADMIN_EM_CONFIG_TEAMRE'             => $combo_re,
//	'ADMIN_EM_CONFIG_TEAMWF'             => $combo_wf,
//	'ADMIN_EM_CONFIG_TEAMRF'             => $combo_rf,
//	'ADMIN_EM_CONFIG_TEAMWG'             => $combo_wg,
//	'ADMIN_EM_CONFIG_TEAMRG'             => $combo_rg,
//	'ADMIN_EM_CONFIG_TEAMWH'             => $combo_wh,
//	'ADMIN_EM_CONFIG_TEAMRH'             => $combo_rh,

	'U_FORM_ACTION'                      => append_sid("admin_em_config.$phpEx"))
);

$template->pparse('body');
include('./page_footer_admin.'.$phpEx);

?>
