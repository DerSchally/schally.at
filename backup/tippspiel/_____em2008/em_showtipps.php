<?php
	/*******************************************
	*             em_showtipps.php             *
	*             ----------------             *
	*                                          *
	*   date       : 6/2006                    *
	*   version    : 0.4                       *
	*   (C)/author : B.Funke                   *
	*   URL        : http://forum.beehave.de   *
	*                                          *
	********************************************/

/* this script can be freely copied and used, as long as all provided files remain unchanged. */
/* For all further terms, the GNU GENERAL PUBLIC LICENSE applies to this MOD. */

	$int_maxbar = 200;		# max. width of voting bar


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

	$int_gameid = min(64,max(1,intval($HTTP_GET_VARS['game_id'])));

	// logged in?
	if ( !$userdata['session_logged_in'] )
	{
		message_die(GENERAL_ERROR, "Please login.");
	}

	// load config
	$em_config	= array();
	$em_config	= get_em_config();


	// check auth status
	if ( $em_config['restrict_to'] != 0 && !get_em_auth() && $userdata['user_level'] != ADMIN && $userdata['user_id'] != $em_config['em_mod_id'] )
	{
		$auth_msg = sprintf($lang['em_access_denied'], '<a href="' . append_sid("groupcp.$phpEx?g=".$em_config['restrict_to']) . '" class="gen">', '</a>', '<a href="'.append_sid("index.$phpEx").'" class="gen">', '</a>');
			message_die(GENERAL_MESSAGE, $auth_msg);
	}

	// check for illegal calls
	$str_table = ($int_gameid > 48) ? EM_FINALS_TABLE : EM_GAMES_TABLE;
	$sql_match = "SELECT * FROM ".$str_table." WHERE game_id = ".$int_gameid;
	if (!($result_match = $db->sql_query($sql_match)))
	{
		message_die(GENERAL_ERROR, '', '', __LINE__, __FILE__, $sql_match);
	}
	$row_match = $db->sql_fetchrow($result_match);
	if (($row_match['game_time'] > time()) && (($userdata['user_level'] != ADMIN)))
	{
		message_die(GENERAL_ERROR, "Open at ".create_date($board_config['default_dateformat'], $row_match['game_time'], $board_config['board_timezone']));
	}

	// load results
	$results_data = array();
	$results_data = get_em_results();

	if ($int_gameid > 48)
	{
		$str_winner = ($int_gameid == 63) ? 'final_loser' : 'final_winner';
		$row_match['game_home'] = ($int_gameid > 56) ? $results_data[$row_match['game_home']][$str_winner] : $em_config[$row_match['game_home']];
		$row_match['game_away'] = ($int_gameid > 56) ? $results_data[$row_match['game_away']][$str_winner] : $em_config[$row_match['game_away']];
	}


	// load teams
	$em_teams	= array();
	$em_teams	= get_em_teams();

	// load result
	$sql_result = "SELECT * FROM ".EM_RESULTS_TABLE." WHERE result_game = ".$int_gameid;
	if (!($result_result = $db->sql_query($sql_result)))
	{
		message_die(GENERAL_ERROR, '', '', __LINE__, __FILE__, $sql_result);
	}
	$row_result = $db->sql_fetchrow($result_result);

	// Generate the page
	$page_title = $lang['em_title_home'];
	include($phpbb_root_path . 'includes/page_header.'.$phpEx);
	$template->set_filenames(array(
		'body' => 'em_showtipps.tpl')
	);

	// get tips
	$sql_tips = "SELECT t.tipp_home, t.tipp_away, t.tipp_points, u.username
					FROM ".EM_TIPPS_TABLE." t, ".USERS_TABLE." u
					WHERE t.tipp_game = ".$int_gameid." AND t.tipp_user = u.user_id
					ORDER BY t.tipp_points DESC, u.username";
	if (!($result_tips = $db->sql_query($sql_tips)))
	{
		message_die(GENERAL_ERROR, '', '', __LINE__, __FILE__, $sql_tips);
	}

	$int_count = 0;
	$int_winhome = 0;
	$int_winaway = 0;
	$int_tie = 0;
	$int_max = 0;
	$int_maxhome = 0;
	$int_maxaway = 0;
	$int_minhome = 0;
	$int_minaway = 0;
	$int_sumhome = 0;
	$int_sumaway = 0;
	$int_sumpoints = 0;

	$arr_results = array();
	$arr_counthomehigh = array();
	$arr_counthomelow = array();
	$arr_countawayhigh = array();
	$arr_countawaylow = array();

	while ($row_tips = $db->sql_fetchrow($result_tips))
	{
		$int_count++;

		$int_winhome = ($row_tips['tipp_home'] > $row_tips['tipp_away']) ? $int_winhome + 1 : $int_winhome;
		$int_winaway = ($row_tips['tipp_home'] < $row_tips['tipp_away']) ? $int_winaway + 1 : $int_winaway;
		$int_tie = ($row_tips['tipp_home'] == $row_tips['tipp_away']) ? $int_tie + 1 : $int_tie;
		$int_maxhome = max($int_maxhome, $row_tips['tipp_home']);
		$int_maxaway = max($int_maxaway, $row_tips['tipp_away']);
		$int_minhome = ($int_count == 1) ? $row_tips['tipp_home'] : min($int_minhome, $row_tips['tipp_home']);
		$int_minaway = ($int_count == 1) ? $row_tips['tipp_away'] : min($int_minaway, $row_tips['tipp_away']);
		$int_sumhome += $row_tips['tipp_home'];
		$int_sumaway += $row_tips['tipp_away'];
		$int_sumpoints += $row_tips['tipp_points'];

		$str_result = $row_tips['tipp_home'].':'.$row_tips['tipp_away'];
		$arr_results[$str_result]++;

		$arr_counthomehigh[$row_tips['tipp_home']]++;
		$arr_counthomelow[$row_tips['tipp_home']]++;
		$arr_countawayhigh[$row_tips['tipp_away']]++;
		$arr_countawaylow[$row_tips['tipp_away']]++;

		$template->assign_block_vars('tipps_row', array(
			'ROW_USERNAME' => ($userdata['username'] == $row_tips['username']) ? '<strong>'.$row_tips['username'].'</strong>' : $row_tips['username'],
			'ROW_TIPPHOME' => $row_tips['tipp_home'],
			'ROW_TIPPAWAY' => $row_tips['tipp_away'],
			'ROW_POINTS' => ($em_config['points_grafical'] == 0) ? $row_tips['tipp_points'] : str_repeat('<img src="./images/em/euro_cup_small.png" alt="Teamgeist" border="0" />', $row_tips['tipp_points']),
			'ROW_ALIGN' => ($em_config['points_grafical'] == 0) ? 'center' : 'left'
		));
	}

	if ($int_count == 0)
	{
		message_die(GENERAL_ERROR, "no tips");
	}

	foreach($arr_results as $value)
	{
		$int_max = max($int_max, $value);
	}
	$int_faktor = round($int_maxbar/($int_max/$int_count));

	arsort($arr_results);
	foreach($arr_results as $key => $value)
	{
		// calculate points for tips
		$int_points = 0;

		if (is_array($row_result))
		{
			$arr_tip = explode(":", $key);
			if (($arr_tip[0] == $row_result['result_home']) && ($arr_tip[1] == $row_result['result_away'])) { $int_points = $em_config['points_match']; }
			else if (($arr_tip[0] - $arr_tip[1]) == ($row_result['result_home'] - $row_result['result_away'])) { $int_points = $em_config['points_tordiff']; }
			else if ((($arr_tip[0] > $arr_tip[1]) && ($row_result['result_home'] > $row_result['result_away'])) || (($arr_tip[0] < $arr_tip[1]) && ($row_result['result_home'] < $row_result['result_away']))) { $int_points = $em_config['points_tendency']; }
		}

		$template->assign_block_vars('stats_row', array(
			'ROW_TIPP' => $key,
			'ROW_NUMBER' => $value,
			'ROW_POINTS' => ($em_config['points_grafical'] == 0) ? $int_points : str_repeat('<img src="./images/em/euro_cup_small.png" alt="Teamgeist" border="0" />', $int_points),
			'ROW_ALIGN' => ($em_config['points_grafical'] == 0) ? 'center' : 'left',
			'POLL_IMG' => $images['voting_graphic'][0],
			'POLL_IMGWIDTH' => ($int_count > 0) ? ($value/$int_count*$int_faktor) : 0,
			'POLL_PERCENT' => sprintf("%.1d%%", ($value/$int_count*100))
		));
	}

	$int_faktortips = round($int_maxbar/(max($int_winhome, $int_winaway, $int_tie)/$int_count));

	// assign variables
	$template->assign_vars(array(
		'L_MATCH' => $lang['l_em_round1_event'],
		'L_DETAILS' => '<a href="'.$row_match['game_loclink'].'" target="_blank">'.$row_match['game_loc'].'</a> - '.create_date($board_config['default_dateformat'], $row_match['game_time'], $board_config['board_timezone']),
		'L_FLAGHOME' => $em_teams[$row_match['game_home']]['team_img'],
		'L_TEAMHOME' => '<a href="'.$em_teams[$row_match['game_home']]['team_link'].'" target="_blank">'.$em_teams[$row_match['game_home']]['team_name'].'</a>',
		'L_TEAMNAMEHOME' => $em_teams[$row_match['game_home']]['team_name'],
		'L_FLAGAWAY' => $em_teams[$row_match['game_away']]['team_img'],
		'L_TEAMAWAY' => '<a href="'.$em_teams[$row_match['game_away']]['team_link'].'" target="_blank">'.$em_teams[$row_match['game_away']]['team_name'].'</a>',
		'L_TEAMNAMEAWAY' => $em_teams[$row_match['game_away']]['team_name'],
		'L_POINTS' => $lang['l_em_table_points'],
		'L_TIPP' => $lang['l_em_round1_tipp'],
		'L_TIPPS' => $lang['l_em_stats_made_tipps'],
		'L_USER' => $lang['Username'],
		'L_WINNER' => $lang['em_st_winner'],
		'L_TIE' => $lang['em_st_tie'],
		'L_MAX' => $lang['em_st_max'],
		'L_MIN' => $lang['em_st_min'],
		'L_AVERAGE' => $lang['em_st_average'],
		'L_NUMBER' => $lang['em_st_number'],
		'L_VPOINTS' => $lang['em_st_vpoints'],
		'L_PPUSER' => $lang['em_st_ppuser'],
		'N_RESULTHOME' => $row_result['result_home'],
		'N_RESULTAWAY' => $row_result['result_away'],
		'N_WINHOME' => $int_winhome,
		'N_WINHOMEPCT' => ($int_count > 0) ? sprintf("%.1d%%", ($int_winhome/$int_count*100)) : 0,
		'N_WINAWAY' => $int_winaway,
		'N_WINAWAYPCT' => ($int_count > 0) ? sprintf("%.1d%%", ($int_winaway/$int_count*100)) : 0,
		'N_WINTIE' => $int_tie,
		'N_WINTIEPCT' => ($int_count > 0) ? sprintf("%.1d%%", ($int_tie/$int_count*100)) : 0,
		'POLL_IMG' => $images['voting_graphic'][0],
		'POLL_IMGWIDTHOME' => ($int_count > 0) ? ($int_winhome/$int_count*$int_faktortips) : 0,
		'POLL_IMGWIDTAWAY' => ($int_count > 0) ? ($int_winaway/$int_count*$int_faktortips) : 0,
		'POLL_IMGWIDTHTIE' => ($int_count > 0) ? ($int_tie/$int_count*$int_faktortips) : 0,
		'N_TIPPS' => $int_count,
		'N_MAXHOME' => $int_maxhome,
		'N_MAXAWAY' => $int_maxaway,
		'N_MINHOME' => $int_minhome,
		'N_MINAWAY' => $int_minaway,
		'N_HOMEHIGH' => $arr_counthomehigh[$int_maxhome],
		'N_HOMELOW' => $arr_counthomelow[$int_minhome],
		'N_AWAYHIGH' => $arr_countawayhigh[$int_maxaway],
		'N_AWAYLOW' => $arr_countawaylow[$int_minaway],
		'N_AVERAGE' => ($int_count > 0) ? round($int_sumhome/$int_count).':'.round($int_sumaway/$int_count) : '-',
		'N_VPOINTS' => $int_sumpoints,
		'N_PPUSER' => ($int_count > 0) ? round($int_sumpoints/$int_count,1) : 0
		)
	);

	if (is_array($row_result))
	{
		 $template->assign_block_vars('switch_result', array());
	}

	$template->pparse('body');
?>