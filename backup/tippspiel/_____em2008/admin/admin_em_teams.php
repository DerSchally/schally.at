<?php
/***************************************************************************
 *                            admin_em_teams.php
 *                            -------------------
 *   title                : EM WebTipp
 *   version              : 0.1.1
 *   begin                : Wednesday, Jun 06, 2007
 *   copyright            : (C) 2007 raphael
 *   email                : raphael.phpbb@gmail.com
 *   based on             : WM WebTipp (C) AceVentura
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
	$module['em_acp_menu_webtipp']['em_acp_menu_teams'] = $filename;
	return;
}

//
// Load default header
//
$phpbb_root_path = "../";
require($phpbb_root_path . 'extension.inc');
require('pagestart.' . $phpEx);
include($phpbb_root_path . 'language/lang_' . $board_config['default_lang'] . '/lang_em.' . $phpEx);

$page_title =  $lang['em_title_home'];

//
// Pull all team data
//

$template->set_filenames(array(
	"body" => "admin/admin_em_teams_body.tpl")
	);

$sql = "SELECT * FROM " . EM_TEAMS_TABLE . "
	WHERE team_id > 0";
if( !$result = $db->sql_query($sql) )
{
	message_die(GENERAL_ERROR, "Could not query team information in em configuration", "", __LINE__, __FILE__, $sql);
}
else
{
	while($row = $db->sql_fetchrow($result))
	{
		$team_id[] = $row['team_id'];
		$team_group[] = $row['team_group'];
		$team_name[] = $row['team_name'];
		$team_image[] = $row['team_img'];
		$team_link[] = $row['team_link'];
	}
}

$template->assign_vars(array(

	'L_ADMIN_EM_CONFIG_TITLE'		 => $lang['em_acp_config_title'],
	'L_ADMIN_EM_CONFIG_TITLE_EXP'		 => $lang['em_acp_config_teams_exp'],
	'L_ADMIN_EM_CONFIG_BUTTON'		 => $lang['em_acp_config_button'],
	'L_ADMIN_EM_CONFIG_TEAMS'		 => $lang['em_acp_config_teams'],
	'L_ADMIN_EM_CONFIG_ID'			 => $lang['em_acp_config_id'],
	'L_ADMIN_EM_CONFIG_NAME'		 => $lang['em_acp_config_name'],
	'L_ADMIN_EM_CONFIG_IMAGE'		 => $lang['em_acp_config_image'],
	'L_ADMIN_EM_CONFIG_LINK'		 => $lang['em_acp_config_link'],
	'L_GROUP'					 => $lang['l_em_round1_group'])
);

for ($i=0;$i < 16;$i++)
{
$template->assign_block_vars('teams_switch', array(

	'ADMIN_EM_CONFIG_TEAMGROUP'		 => $team_group[$i],
	'ADMIN_EM_CONFIG_TEAMID'		 => $team_id[$i],
	'ADMIN_EM_CONFIG_TEAMNAME'		 => $team_name[$i],
	'ADMIN_EM_CONFIG_TEAMIMAGE'	 	 => $team_image[$i],
	'ADMIN_EM_CONFIG_TEAMLINK'	 	 => $team_link[$i],

	'U_FORM_ACTION'                      => append_sid("admin_em_teams.$phpEx"))
);
}

if( isset($HTTP_POST_VARS['submit']) )
{
	$t_id		 = $_POST["t_id"];
	$t_group	 = $_POST["t_group"];
	$t_name	 = $_POST["t_name"];
	$t_image	 = $_POST["t_image"];
	$t_link	 = $_POST["t_link"];

	for ($i=0;$i < 32;$i++)
	{

		$sql = "UPDATE " . EM_TEAMS_TABLE . " SET team_group = '" . str_replace("\'", "''", $t_group[$i]) . "', team_name = '" . str_replace("\'", "''", $t_name[$i]) . "', team_img = '" . str_replace("\'", "''", $t_image[$i]) . "', team_link = '" . str_replace("\'", "''", $t_link[$i]) . "' WHERE team_id = '$t_id[$i]' ";

		if( !($result = $db->sql_query($sql)) )
		{
			message_die(GENERAL_ERROR, "Could not update team information in em configuration", "", __LINE__, __FILE__, $sql);
		}
	}
	$message = $lang['em_acp_teams_updated'] . "<br /><br />" . sprintf($lang['Click_return_config'], "<a href=\"" . append_sid("admin_em_teams.$phpEx") . "\">", "</a>") . "<br /><br />" . sprintf($lang['Click_return_admin_index'], "<a href=\"" . append_sid("index.$phpEx?pane=right") . "\">", "</a>");
	message_die(GENERAL_MESSAGE, $message);
}

$template->pparse('body');
include('./page_footer_admin.'.$phpEx);

?>
