<?php
	/*******************************************
	*            em_winnerstat.php             *
	*            -----------------             *
	*                                          *
	*   date       : 16./17.6. / 1.7.2006      *
	*   version    : 0.3                       *
	*   (C)/author : B.Funke                   *
	*   URL        : http://forum.beehave.de   *
	*                                          *
	********************************************/

/* this script can be freely copied and used, as long as all provided files remain unchanged. */
/* For all further terms, the GNU GENERAL PUBLIC LICENSE applies to this MOD. */

$int_maxbar = 200;		# max. width of voting bar
$show_allteams = true;	# true|false - whether to also show teams without tips


// start
define('IN_PHPBB', true);
$phpbb_root_path = './';
include($phpbb_root_path . 'extension.inc');
include($phpbb_root_path . 'common.'.$phpEx);
include($phpbb_root_path . 'includes/functions_em.'.$phpEx);

// Start session management
$userdata = session_pagestart($user_ip, PAGE_EM);
init_userprefs($userdata);
// End session management

include($phpbb_root_path . 'language/lang_' . $board_config['default_lang'] . '/lang_em.'.$phpEx);

// load config
$em_config	= array();
$em_config	= get_em_config();

$games_data = array();
$games_data = get_em_finalgames();

$results_data = array();
$results_data = get_em_results();

$games_round1 = array();
$games_round1 = get_em_games();

// Set pagetitle, pageheader and templatefile
$page_title = $lang['em_title_home'];

include($phpbb_root_path . 'includes/page_header.'.$phpEx);

$template->set_filenames(array(
	'body' => 'em_winnerstat.tpl')
);


// get winner-tips
$sql_tips = "SELECT COUNT(*) AS anzahl, m.team_id, m.team_name, m.team_img, m.team_link
				FROM  ".EM_TIPPS_TABLE." t, ".EM_TEAMS_TABLE." m
				WHERE t.tipp_game = 65 AND
						m.team_id = t.tipp_home
				GROUP BY t.tipp_home, m.team_id, m.team_name, m.team_img, m.team_link
				ORDER BY anzahl DESC, m.team_name";
if( !($result_tips = $db->sql_query($sql_tips)) )
{
	message_die(GENERAL_ERROR, 'Could not get teams data', '', __LINE__, __FILE__, $sql_tips);
}

$arr_data = array();
$arr_teamids = array();
$anz_tips = 0;
$int_max = 0;
while ( $row_tips = $db->sql_fetchrow($result_tips) )
{
	$arr_data[] = $row_tips;
	$anz_tips += $row_tips['anzahl'];
	$int_max = max($row_tips['anzahl'], $int_max);
	$arr_teamids[] = $row_tips['team_id'];
}

if ($anz_tips == 0)
{
	message_die(GENERAL_ERROR, 'You don\'t need this without tips.', '', __LINE__, __FILE__, '');
}

if ($show_allteams)
{
	// get other teams
	$sql_tips2 = "SELECT team_id, team_name, team_img, team_link
					FROM  ".EM_TEAMS_TABLE."
					WHERE team_id NOT IN (".implode(",", $arr_teamids).")
					ORDER BY team_name";
	if( !($result_tips2 = $db->sql_query($sql_tips2)) )
	{
		message_die(GENERAL_ERROR, 'Could not get teams data', '', __LINE__, __FILE__, $sql_tips2);
	}

	while ( $row_tips2 = $db->sql_fetchrow($result_tips2) )
	{
		$arr_data[] = $row_tips2;
	}
}


$int_faktor = round($int_maxbar/($int_max/$anz_tips));
foreach($arr_data as $key => $value)
{
	$template->assign_block_vars('teamrow', array(
		'TEAM_POS'			=> ($key+1),
		'TEAM_NAME'			=> $value['team_name'],
		'TEAM_ANZ'			=> intval($value['anzahl']),
		'TEAM_FLAG'			=> $value['team_img'],
		'TEAM_URL'			=> $value['team_link'],
		'TEAM_STATUS'		=> get_teamstatus($value['team_id']),
		'POLL_IMGWIDTH'		=> ($value['anzahl']/$anz_tips*$int_faktor),
		'POLL_PERCENT'		=> sprintf("%.1d%%", ($value['anzahl']/$anz_tips*100))
		)
	);
}
$db->sql_freeresult($result_tips);


// Assign vars
$template->assign_vars(array(
	'L_EM_WELCOME_TITLE'=> $lang['l_em_round1_welcome_title'],
	'L_EM_TITLE'		=> $lang['em_st_winnertips'],
	'L_EM_STATS_POS'	=> $lang['l_em_stats_pos'],
	'L_EM_ROUND1'		=> $lang['l_em_nav_round1'],
	'L_EM_FINALS'		=> $lang['l_em_nav_finals'],
	'L_EM_STATS'		=> $lang['l_em_nav_stats'],
	'L_EM_FORUM'		=> $lang['l_em_nav_forum'],
	'L_EM_MOD'			=> $lang['l_em_nav_mod'],
	'L_STATUS'			=> $lang['em_status'],
	'U_EM_MOD'			=> append_sid("./privmsg.".$phpEx."?mode=post&u=".$em_config['em_mod_id']),
	'U_EM_FORUM'		=> append_sid("./viewforum.".$phpEx."?f=".$em_config['em_forum_id']),
	'U_EM_FINALS'		=> append_sid("./em_finals.".$phpEx),
	'U_EM_STATS'		=> append_sid("./em_stats.".$phpEx),
	'U_EM_WINNERSTAT'	=> append_sid("./em_winnerstat.".$phpEx),
	'U_EM_ROUND1'		=> append_sid("./em_round1.".$phpEx),
	'L_TEAM'			=> $lang['l_em_table_team'],
	'L_ANZAHL'			=> $lang['em_st_number'],
	'POLL_IMG'			=> $images['voting_graphic'][0]
	)
);

// Check if tippforum is enabled
if ( $em_config['em_forum_id'] != 0 )
{
        $template->assign_block_vars('forum_enabled', array());
}

$template->pparse('body');
include($phpbb_root_path . 'includes/page_tail.'.$phpEx);



// gets the team status
function get_teamstatus($team_id)
{
	global $db, $lang, $games_data, $results_data;

	reset($games_data);

	for ($i=0;$i<count($games_data);$i++)
	{
		// Get current game data
		$games_row = ($i==0) ? current($games_data) : next($games_data);

		// Games loop
		for ($j=0;$j<count($games_row);$j++ )
		{
	        $game_id = $games_row[$j]['game_id'];

			if (($games_row[$j]['game_home'] == $team_id) || ($games_row[$j]['game_away'] == $team_id))
			{
				$str_status = $games_row[$j]['game_group'];

				if (isset($results_data[$game_id]))
				{
					if ((($games_row[$j]['game_home'] == $team_id) && ($results_data[$game_id]['result_home'] < $results_data[$game_id]['result_away'])) ||
						(($games_row[$j]['game_away'] == $team_id) && ($results_data[$game_id]['result_home'] > $results_data[$game_id]['result_away'])))
					{
						if ($game_id > 24)
						{
							$str_status .= ' ('.$lang['em_lost'].')';
						}
						else
						{
							$str_status .= ' ('.$lang['em_out'].')';
						}
					}
					else
					{
						$str_status .= ' ('.$lang['em_won'].')';
						$str_status = ($game_id == 64) ? $lang['em_st_winner08'] : $str_status;
					}
				}
			}
		}
	}

	if ((!empty($str_status)) && (strpos($str_status, $lang['l_em_kleinesfinale']) === false))
	{
		$str_status = (strpos($str_status, $lang['em_lost']) > 0) ? $str_status : '<b>'.$str_status.'</b>';
	}
	return (empty($str_status)) ? (time() < 1191682800 ) ? $lang['l_em_nav_round1'] : $lang['em_out'] : $str_status;
}
?>
