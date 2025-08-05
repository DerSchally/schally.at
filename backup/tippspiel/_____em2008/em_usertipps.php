<?php
	/*******************************************
	*             em_usertipps.php             *
	*             ----------------             *
	*                                          *
	*   date       : 7/2006                    *
	*   version    : 0.2                       *
	*   (C)/author : B.Funke                   *
	*   URL        : http://forum.beehave.de   *
	*                                          *
	********************************************/

/* this script can be freely copied and used, as long as all provided files remain unchanged. */
/* For all further terms, the GNU GENERAL PUBLIC LICENSE applies to this MOD. */

	$int_maxbar = 200;		# max. width of voting bar
	$admin_sees_all = false; 	# true | false: switch, whether the admin can see future tips


	// Start
	define('IN_PHPBB', true);

	$phpbb_root_path = './';
	include($phpbb_root_path . 'extension.inc');
	include($phpbb_root_path . 'common.'.$phpEx);
	include($phpbb_root_path . 'includes/functions_em.'.$phpEx);

	$gen_simple_header = TRUE;


	// Start session management
	$userdata = session_pagestart($user_ip, PAGE_EM);
	init_userprefs($userdata);

	include($phpbb_root_path . 'language/lang_' . $board_config['default_lang'] . '/lang_em.'.$phpEx);

	$int_userid = intval($HTTP_GET_VARS['user_id']);

	// logged in?
	if ( !$userdata['session_logged_in'] )
	{
		message_die(GENERAL_ERROR, "Please login.");
	}

	// load config
	$em_config = array();
	$em_config = get_em_config();


	// check auth status
	if ( $em_config['restrict_to'] != 0 && !get_em_auth() && $userdata['user_level'] != ADMIN && $userdata['user_id'] != $em_config['em_mod_id'] )
	{
		$auth_msg = sprintf($lang['em_access_denied'], '<a href="' . append_sid("groupcp.$phpEx?g=".$em_config['restrict_to']) . '" class="gen">', '</a>', '<a href="'.append_sid("index.$phpEx").'" class="gen">', '</a>');
			message_die(GENERAL_MESSAGE, $auth_msg);
	}


	// get username
	$sql_user = "SELECT username FROM ".USERS_TABLE." WHERE user_id = ".$int_userid;
	if (!($result_user = $db->sql_query($sql_user)))
	{
		message_die(GENERAL_ERROR, '', '', __LINE__, __FILE__, $sql_user);
	}
	if ($db->sql_numrows($result_user) == 0)
	{
		message_die(GENERAL_ERROR, "user not found");
	}
	$row_user = $db->sql_fetchrow($result_user);


	// get data
	$teams_data = array();
	$teams_data = get_em_teams();

	$results_data = array();
	$results_data = get_em_results();

	$tips_data = array();
	$tips_data = get_em_tipps();

	$finals_data = array();
	$finals_data = get_em_finalgames();

	$arr_usertips = array();
	$arr_userpkt = array();
	$int_count = 0;
	$int_allpoints = 0;
	$int_allgoals = 0;
	$int_htdiff = 0;
	$str_htdifft = "";

	$page_title = $lang['em_title_home'];
	include($phpbb_root_path . 'includes/page_header.'.$phpEx);
	$template->set_filenames(array(
		'body' => 'em_usertipps.tpl')
	);


	// get games for round 1
	$sql_games = "SELECT * FROM ".EM_GAMES_TABLE." ORDER BY game_time";
	if (!($result_games = $db->sql_query($sql_games)))
	{
		message_die(GENERAL_ERROR, '', '', __LINE__, __FILE__, $sql_games);
	}

	while ($row_games = $db->sql_fetchrow($result_games))
	{
		$int_points = $tips_data[$int_userid][$row_games['game_id']]['tipp_points'];
		$int_allpoints += $tips_data[$int_userid][$row_games['game_id']]['tipp_points'];

		$int_goalhome = $results_data[$row_games['game_id']]['result_home'];
		$int_goalaway = $results_data[$row_games['game_id']]['result_away'];

		$int_tiphome = $tips_data[$int_userid][$row_games['game_id']]['tipp_home'];
		$int_tipaway = $tips_data[$int_userid][$row_games['game_id']]['tipp_away'];
		$int_allgoals += ($int_tiphome + $int_tipaway);

		if (isset($int_tiphome) && isset($int_tipaway))
		{
			$str_usertip = $int_tiphome.' : '.$int_tipaway;
			$arr_usertips[$str_usertip]++;
			$int_count++;

			if (abs($int_tiphome - $int_tipaway) > $int_htdiff)
			{
				$int_htdiff = max($int_htdiff, abs($int_tiphome - $int_tipaway));
				$str_htdifft = $str_usertip;
			}

			if ($row_games['game_time'] < time()) { $arr_userpkt[3]++; } 
			else if (($int_tiphome == $int_goalhome) && ($int_tipaway == $int_goalaway)) { $arr_userpkt[0]++; }
			else if (($int_tiphome - $int_tipaway) == ($int_goalhome - $int_goalaway)) { $arr_userpkt[1]++; }
			else if ((($int_tiphome > $int_tipaway) && ($int_goalhome > $int_goalaway)) || (($int_tiphome < $int_tipaway) && ($int_goalhome < $int_goalaway))) { $arr_userpkt[2]++; }
			else { $arr_userpkt[3]++; }
		}
		else
		{
			$arr_userpkt[4]++;
		}

		$template->assign_block_vars('games_row', array(
			'GAME_TIME' => create_date($board_config['default_dateformat'], $row_games['game_time'], $board_config['board_timezone']),
			'TEAM_HOME' => $teams_data[$row_games['game_home']]['team_name'],
			'TEAM_AWAY' => $teams_data[$row_games['game_away']]['team_name'],
			'FLAG_HOME' => $teams_data[$row_games['game_home']]['team_img'],
			'FLAG_AWAY' => $teams_data[$row_games['game_away']]['team_img'],
			'LINK_HOME' => $teams_data[$row_games['game_home']]['team_link'],
			'LINK_AWAY' => $teams_data[$row_games['game_away']]['team_link'],
			'GOAL_HOME' => $int_goalhome,
			'GOAL_AWAY' => $int_goalaway,
			'TIPP_HOME' => (($row_games['game_time'] < time()) || (($admin_sees_all == true) && ($userdata['user_level'] == ADMIN))) ? $int_tiphome : '-', 
         		'TIPP_AWAY' => (($row_games['game_time'] < time()) || (($admin_sees_all == true) && ($userdata['user_level'] == ADMIN))) ? $int_tipaway : '-',
         		'POINTS' => ($em_config['points_grafical'] == 0) ? ($int_points == 0) ? '-' : $int_points : str_repeat('<img src="./images/em/euro_cup_small.png" alt="Points" title="Points" border="0" />', $int_points),
			'POINTS_ALIGN' => ($em_config['points_grafical'] == 0) ? 'center' : 'center'
		));
	}

	// loop for finals
	for($i=0;$i<count($finals_data);$i++)
	{
	    $finals_row = ($i==0) ? current($finals_data) : next($finals_data);

		$template->assign_block_vars('final_row', array(
			'FINAL_NAME' => $finals_row[0]['game_group']
			)
		);

		for($j=0;$j<count($finals_row);$j++)
		{
			$int_gameid = $finals_row[$j]['game_id'];
			$int_points = $tips_data[$int_userid][$int_gameid]['tipp_points'];
			$int_allpoints += $tips_data[$int_userid][$int_gameid]['tipp_points'];

			if (($finals_row[$j]['game_time'] < time()) || (($admin_sees_all == true) && ($userdata['user_level'] == ADMIN)))
			{
				$int_tiphome = $tips_data[$int_userid][$int_gameid]['tipp_home'];
				$int_tipaway = $tips_data[$int_userid][$int_gameid]['tipp_away'];
				$int_allgoals += ($int_tiphome + $int_tipaway);

				if (isset($int_tiphome) && isset($int_tipaway))
				{
					$str_usertip = $int_tiphome.' : '.$int_tipaway;
					$arr_usertips[$str_usertip]++;
					$int_count++;

					if (abs($int_tiphome - $int_tipaway) > $int_htdiff)
					{
						$int_htdiff = max($int_htdiff, abs($int_tiphome - $int_tipaway));
						$str_htdifft = $str_usertip;
					}

					$int_goalhome = (isset($results_data[$int_gameid])) ? $results_data[$int_gameid]['result_home'] : '';
					$int_goalaway = (isset($results_data[$int_gameid])) ? $results_data[$int_gameid]['result_away'] : '';

					if (isset($int_goalhome) && isset($int_goalaway)) 
					{
						if ($row_games['game_time'] > time())
						{
							$arr_userpkt[3]++; 
						}
						else
						if (($int_tiphome == $int_goalhome) && ($int_tipaway == $int_goalaway))
						{
							 $arr_userpkt[0]++;
						}
				 		else
						if (($int_tiphome - $int_tipaway) == ($int_goalhome - $int_goalaway))
						{
							 $arr_userpkt[1]++;
						}
						else
						if ((($int_tiphome > $int_tipaway) && ($int_goalhome > $int_goalaway)) || (($int_tiphome < $int_tipaway) && ($int_goalhome < $int_goalaway)))
						{
							 $arr_userpkt[2]++;
						}
						else
						{
							$arr_userpkt[3]++; 
						}
					}
				}
				else
				{
					$arr_userpkt[4]++;
				}
			}

			$template->assign_block_vars('final_row.fgames_row', array(
				'GAME_TIME' => create_date($board_config['default_dateformat'], $finals_row[$j]['game_time'], $board_config['board_timezone']),
				'TEAM_HOME' => ($finals_row[$j]['game_home'] == 0 ) ? '--------': $teams_data[$finals_row[$j]['game_home']]['team_name'],
				'TEAM_AWAY' => ($finals_row[$j]['game_away'] == 0 ) ? '--------': $teams_data[$finals_row[$j]['game_away']]['team_name'],
				'FLAG_HOME' => ($finals_row[$j]['game_home'] == 0 ) ? 'none.gif': $teams_data[$finals_row[$j]['game_home']]['team_img'],
				'FLAG_AWAY' => ($finals_row[$j]['game_away'] == 0 ) ? 'none.gif': $teams_data[$finals_row[$j]['game_away']]['team_img'],
				'LINK_HOME' => ($finals_row[$j]['game_home'] == 0 ) ? 'http://de.uefa.com/competitions/euro/index.html': $teams_data[$finals_row[$j]['game_home']]['team_link'],
				'LINK_AWAY' => ($finals_row[$j]['game_home'] == 0 ) ? 'http://de.uefa.com/competitions/euro/index.html': $teams_data[$finals_row[$j]['game_away']]['team_link'],
				'GOAL_HOME' => (isset($results_data[$int_gameid])) ? $results_data[$int_gameid]['result_home'] : '',
				'GOAL_AWAY' => (isset($results_data[$int_gameid])) ? $results_data[$int_gameid]['result_away'] : '',
				'TIPP_HOME' => (($finals_row[$j]['game_time'] < time()) || (($admin_sees_all == true) && ($userdata['user_level'] == ADMIN))) ? $int_tiphome : '-',
				'TIPP_AWAY' => (($finals_row[$j]['game_time'] < time()) || (($admin_sees_all == true) && ($userdata['user_level'] == ADMIN))) ? $int_tipaway : '-',
				'POINTS' => ($em_config['points_grafical'] == 0) ? ($int_points == 0) ? '-' : $int_points : str_repeat('<img src="./images/em/euro_cup_small.png" alt="Points" title="Points" border="0" />', $int_points),
				'POINTS_ALIGN' => ($em_config['points_grafical'] == 0) ? 'center' : 'center'
			));
		}
	}


	$int_max = 0;
	foreach($arr_usertips as $value)
	{
		$int_max = max($int_max, $value);
	}
	$int_faktor = round($int_maxbar/($int_max/$int_count));

	arsort($arr_usertips);
	foreach($arr_usertips as $key => $value)
	{
		$template->assign_block_vars('stats_row', array(
			'ROW_TIPP' => $key,
			'ROW_NUMBER' => $value,
			'POLL_IMG' => $images['voting_graphic'][0],
			'POLL_IMGWIDTH' => ($value/$int_count*$int_faktor),
			'POLL_PERCENT' => sprintf("%.1d%%", ($value/$int_count*100))
		));
	}

	$int_faktortips = round($int_maxbar/(max($arr_userpkt[0], $arr_userpkt[1], $arr_userpkt[2], $arr_userpkt[3], $arr_userpkt[4])/64));

	// assign variables
	$template->assign_vars(array(
		'L_MATCH' => $lang['l_em_round1_event'],
		'L_GAMETIME' => $lang['l_em_round1_date'],
		'L_TIPP' => $lang['l_em_round1_tipp'],
		'L_RESULT' => $lang['l_em_round1_result'],
		'L_POINTS' => $lang['l_em_table_points'],
		'L_HEAD' => sprintf($lang['em_ut_tipps'], '<strong>'.$row_user['username'].'</strong>'),
		'L_NUMBER' => $lang['em_st_number'],
		'L_TIPPS' => $lang['l_em_stats_made_tipps'],
		'N_TIPPS' => $int_count,
		'POLL_IMG' => $images['voting_graphic'][0],
		'L_ERGEBNIS' => $lang['em_ut_match'],
		'N_ERGEBNIS' => intval($arr_userpkt[0]),
		'P_ERGEBNIS' => ($arr_userpkt[0]/31*$int_faktortips),
		'P_ERGEBNISPCT' => sprintf("%.1d%%", ($arr_userpkt[0]/31*100)),
		'L_TOFDIFF' => $lang['em_ut_tordiff'],
		'N_TORDIFF' => intval($arr_userpkt[1]),
		'P_TORDIFF' => ($arr_userpkt[1]/31*$int_faktortips),
		'P_TORDIFFPCT' => sprintf("%.1d%%", ($arr_userpkt[1]/31*100)),
		'L_TENDENZ' => $lang['em_ut_tend'],
		'N_TENDENZ' => intval($arr_userpkt[2]),
		'P_TENDENZ' => ($arr_userpkt[2]/31*$int_faktortips),
		'P_TENDENZPCT' => sprintf("%.1d%%", ($arr_userpkt[2]/31*100)),
		'L_NULL' => $lang['em_ut_null'],
		'N_NULL' => intval($arr_userpkt[3]),
		'P_NULL' => ($arr_userpkt[3]/31*$int_faktortips),
		'P_NULLPCT' => sprintf("%.1d%%", ($arr_userpkt[3]/31*100)),
		'L_KEINTIPP' => $lang['l_em_winner_not_set'],
		'N_KEINTIPP' => intval($arr_userpkt[4]),
		'P_KEINTIPP' => ($arr_userpkt[4]/31*$int_faktortips),
		'P_KEINTIPPPCT' => sprintf("%.1d%%", ($arr_userpkt[4]/31*100)),
		'N_POINTS' => $int_allpoints,
		'L_PUNKTEPT' => $lang['em_ut_punktept'],
		'N_PUNKTEPT' => ($int_count > 0) ? round($int_allpoints/$int_count,2) : 0,
		'L_TORE' => $lang['em_ut_tore'],
		'N_TORE' => $int_allgoals,
		'L_TOREPS' => $lang['em_ut_torept'],
		'N_TOREPS' => ($int_count > 0) ? round($int_allgoals/$int_count,2) : 0,
		'L_HTDIFF' => $lang['em_ut_htdiff'],
		'N_HTDIFF' => intval($int_htdiff),
		'N_HTDIFFT' => $str_htdifft
		)
	);


	$template->pparse('body');
?>