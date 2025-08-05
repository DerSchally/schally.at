<?php
/***************************************************************************
 *                               em_finals.php
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

define('IN_PHPBB', true);
$phpbb_root_path = './';
include($phpbb_root_path . 'extension.inc');
include($phpbb_root_path . 'common.'.$phpEx);
include($phpbb_root_path . 'includes/functions_em.'.$phpEx);

//
// Start session management
//
$userdata = session_pagestart($user_ip, PAGE_EM);
init_userprefs($userdata);
//
// End session management
//

$save_emtipp   = ( isset($HTTP_POST_VARS['add_em_winner']) ) ? $HTTP_POST_VARS['add_em_winner'] : '';
if ( $save_emtipp != '' ) {
      $tipped_winner  = ( isset($HTTP_POST_VARS['em_winner']) ) ? $HTTP_POST_VARS['em_winner']  : 0;
      $tipped_user_id = $userdata['user_id'];
      save_em_tipp($tipped_user_id, $tipped_winner);
}

include($phpbb_root_path . 'language/lang_' . $board_config['default_lang'] . '/lang_em.'.$phpEx);

//
// Check login status
//
if ( !$userdata['session_logged_in'] )
{
	redirect(append_sid("login.$phpEx"));
}

// Load Config
$em_config    = array();  // Config values
$em_config    = get_em_config();

//
// Check auth status
//
if ( $em_config['restrict_to'] != 0 && !get_em_auth() && $userdata['user_level'] != ADMIN && $userdata['user_id'] != $em_config['em_mod_id'] )
{
	$auth_msg = sprintf($lang['em_access_denied'], '<a href="' . append_sid("groupcp.$phpEx?g=".$em_config['restrict_to']) . '" class="gen">', '</a>', '<a href="'.append_sid("index.$phpEx").'" class="gen">', '</a>');
        message_die(GENERAL_MESSAGE, $auth_msg);
}


//
// Set pagetitle, pageheader and templatefile
//
$page_title = $lang['em_title_home'];

include($phpbb_root_path . 'includes/page_header.'.$phpEx);

$template->set_filenames(array(
	'body' => 'em_tipp_body.tpl')
);

// Check buttons
$moderation    = ( isset($HTTP_POST_VARS['edit_results']) )  ? $HTTP_POST_VARS['edit_results']  : '';
$save_tipp     = ( isset($HTTP_POST_VARS['save_tipp']) )     ? $HTTP_POST_VARS['save_tipp']     : '';
$save_results  = ( isset($HTTP_POST_VARS['save_results']) )  ? $HTTP_POST_VARS['save_results']  : '';

//
// Init arrays               Array contains:
//                           ---------------
$games_data   = array();  // All games data
$em_teams     = array();  // All teama data (order by teamname)
$games_row    = array();  // Current games data
$teams_data   = array();  // All teams data
$tipps_data   = array();  // All tipps data
$results_data = array();  // All usertipps
$tabCount     = 1;

//
// Update database
//
if ( $save_tipp != '' ) {

      clear_tipps($userdata['user_id'], 1);
      $current_time = time();
      for ( $i = 57; $i < 65; $i++ ) {

          $tipp_home_val = ( isset($HTTP_POST_VARS['tipp_home'.$i]) )  ? $HTTP_POST_VARS['tipp_home'.$i] : '';
          $tipp_away_val = ( isset($HTTP_POST_VARS['tipp_away'.$i]) )  ? $HTTP_POST_VARS['tipp_away'.$i] : '';
          $tipp_time     = ( isset($HTTP_POST_VARS['tipp_time'.$i]) )  ? $HTTP_POST_VARS['tipp_time'.$i] : '';

          if ( $tipp_home_val != '' && $tipp_away_val != '' && $tipp_time != '' && $tipp_time > $current_time ) {
              save_tipp($i, $userdata['user_id'], abs(intval($tipp_home_val)), abs(intval($tipp_away_val)), intval($tipp_time));
          }
      }
      calculate_user_points($userdata['user_id']);

}
if ( $save_results != '' ) {
      $home_val = ( isset($HTTP_POST_VARS['home']) )    ? $HTTP_POST_VARS['home']    : '';
      $away_val = ( isset($HTTP_POST_VARS['away']) )    ? $HTTP_POST_VARS['away']    : '';
      $home_id  = ( isset($HTTP_POST_VARS['home_id']) ) ? $HTTP_POST_VARS['home_id'] : '';
      $away_id  = ( isset($HTTP_POST_VARS['away_id']) ) ? $HTTP_POST_VARS['away_id'] : '';
      $game_id  = ( isset($HTTP_POST_VARS['game_id']) ) ? $HTTP_POST_VARS['game_id'] : '';


      clear_result($game_id);

          if ( $home_val != '' && $away_val != '' ) {

              if ( abs(intval($home_val)) != abs(intval($away_val)) ) {


                 if ( abs(intval($home_val)) > abs(intval($away_val)) ) {

                    save_result($game_id, abs(intval($home_val)), abs(intval($away_val)), intval($home_id), intval($away_id), intval($HTTP_POST_VARS['game_status']));
                    if ($game_id == 64) {
                       clear_result(65);
                       save_result(65, intval($home_id), 0);
                    }
                 }
                 else {

                    save_result($game_id, abs(intval($home_val)), abs(intval($away_val)), intval($away_id), intval($home_id), intval($HTTP_POST_VARS['game_status']));
                    if ($game_id == 64) {
                       clear_result(65);
                       save_result(65, intval($away_id), 0);
                    }
                 }


              }
          }


      calculate_user_points();

}

//
// Load data
//
$teams_data   = get_em_teams();
$results_data = get_em_results();
$em_teams     = get_em_teams_as_row();
$tipps_data   = get_em_tipps();
$games_data   = get_em_finalgames();

//
// Listing
//
// Group loop
for ( $i = 0; $i < count($games_data); $i++ ) {

    // Get current game data
    $games_row = ( $i == 0 ) ? current($games_data) : next($games_data);

    // Group loop switches
    $template->assign_block_vars('grouprow', array(
		        'GROUP_NAME'   => $games_row[0]['game_group'],
                        'GROUP_AC'     => $games_row[0]['game_group'])
    );

    // Games loop
    for ( $j = 0; $j < count($games_row); $j++ ) {

        // Init some vars
        $game_id            = $games_row[$j]['game_id'];
        $game_time          = $games_row[$j]['game_time'];
        $goals_home         = '';
        $goals_away         = '';
        $goals_tipp_home    = '';
        $goals_tipp_away    = '';
        $em_tipps_pts       = '';
        $em_tipps_pts_align = 'center';

        // Get results for the game
        if ( isset($results_data[$game_id]) ) {
           $goals_home = $results_data[$game_id]['result_home'];
           $goals_away = $results_data[$game_id]['result_away'];
        }

        // Get tipps for the game
        if ( isset($tipps_data[$userdata['user_id']][$game_id]) ) {
           $goals_tipp_home = $tipps_data[$userdata['user_id']][$game_id]['tipp_home'];
           $goals_tipp_away = $tipps_data[$userdata['user_id']][$game_id]['tipp_away'];
           $em_tipps_pts1    = $tipps_data[$userdata['user_id']][$game_id]['tipp_points'];
           if ( $em_config['points_grafical'] == 1 ) {
             for ( $points = 0; $points < $em_tipps_pts1; $points++ ) {
//               $em_tipps_pts .= '<img src="./images/em/teamgeist.gif" alt="Teamgeist" border="0" />';
               $em_tipps_pts .= '<img src="./images/em/euro_cup_small.png" alt="Punkte" border="0" />';
             }
             $em_tipps_pts_align = 'left';
           }
           else {
               $em_tipps_pts  = ( $em_tipps_pts1 != 0 ) ? $em_tipps_pts1 : '';
           }
        }

        // Build result entries
        if ( $moderation != '' ) {
             $status_reg = (intval($results_data[$game_id]['result_status'] == 0)) ? ' checked="checked"' : '';
             $status_nv = ($results_data[$game_id]['result_status'] == 1) ? ' checked="checked"' : '';
             $status_ne = ($results_data[$game_id]['result_status'] == 2) ? ' checked="checked"' : '';
             $em_results  = '<form action="' . append_sid("./em_finals.".$phpEx) . '" name="save_a_result" method="POST" enctype="multipart/form-data"><input type="hidden" name="game_id" value="' . $game_id . '" /><input type="hidden" name="home_id" value="' . $teams_data[$games_row[$j]['game_home']]['team_id'] . '" /><input type="hidden" name="away_id" value="' . $teams_data[$games_row[$j]['game_away']]['team_id'] . '" /><input type="text" name="home" value="' . $goals_home . '" maxlength="2" size="2" class="post" tabindex="' . $tabCount++ . '" />&nbsp;&nbsp;<b>:</b>&nbsp;&nbsp;<input type="text" name="away" value="' . $goals_away . '" maxlength="2" size="2" class="post" tabindex="' . $tabCount++ . '" />&nbsp;<input type="submit" class="liteoption" name="save_results" value="' . $lang['l_em_round1_editresults1'] . '"><br />' . $lang['em_finalreg'] . '<input name="game_status" type="radio" value="0" ' . $status_reg . '> &nbsp;|&nbsp;' . $lang['em_finalnv'] . '<input name="game_status" type="radio" value="1"' . $status_nv . '>&nbsp;|&nbsp;' . $lang['em_finalne'] . '<input name="game_status" type="radio" value="2"' . $status_ne . '></form>';
        }
        else {
             $goals_home = ( $goals_home == '' ) ? '-' : $goals_home;
             $goals_away = ( $goals_away == '' ) ? '-' : $goals_away;
             $em_results  = $goals_home . '&nbsp;<b>:</b>&nbsp;' . $goals_away;
        }

        // Build tipps entries
        if ( $moderation != '' ) {
             $goals_tipp_home = ( $goals_tipp_home == '' ) ? '-' : $goals_tipp_home;
             $goals_tipp_away = ( $goals_tipp_away == '' ) ? '-' : $goals_tipp_away;
             $em_tipps  = $goals_tipp_home . '&nbsp;<b>:</b>&nbsp;' . $goals_tipp_away;
        }
        else {
             if ( $game_time < time() ) {
                $em_tipps  = $goals_tipp_home . '&nbsp;<b>:</b>&nbsp;' . $goals_tipp_away . '<input type="hidden" name="tipp_time' . $game_id . '" value="' . $games_row[$j]['game_time'] . '" /><input type="hidden" name="tipp_home' . $game_id . '" value="' . $goals_tipp_home . '" /><input type="hidden" name="tipp_away' . $game_id . '" value="' . $goals_tipp_away . '" />';
             }
             else {
                $em_tipps    = '<input type="hidden" name="tipp_time' . $game_id . '" value="' . $games_row[$j]['game_time'] . '" /><input type="text" name="tipp_home' . $game_id . '" value="' . $goals_tipp_home . '" maxlength="2" size="2" class="post" tabindex="' . $tabCount++ . '" />&nbsp;&nbsp;<b>:</b>&nbsp;&nbsp;<input type="text" name="tipp_away' . $game_id . '" value="' . $goals_tipp_away . '" maxlength="2" size="2" class="post" tabindex="' . $tabCount++ . '" />';
             }
        }

        // Game loop switches
        $template->assign_block_vars('grouprow.gamesrow', array(
		        'GAME_DATE'           => create_date($board_config['default_dateformat'], $games_row[$j]['game_time'], $board_config['board_timezone']),
                        'GAME_RESULT'         => $em_results,
                        'GAME_ID'             => $game_id,
                        'GAME_STATUS'      => (($results_data[$game_id]['result_status'] != 0) && ($moderation == '')) ? ($results_data[$game_id]['result_status'] == 1) ? '&nbsp;('.$lang['em_finalnv'].')' : '&nbsp;('.$lang['em_finalne'].')' : '',
                        'GAME_TIPP'           => $em_tipps,
                        'SHOWTIPPS'           => ($games_row[$j]['game_time'] < time()) ? '<br /><a href="'.append_sid('em_showtipps.'.$phpEx.'?game_id='.$games_row[$j]['game_id']).'" onclick="window.open(\''.append_sid('em_showtipps.'.$phpEx.'?game_id='.$games_row[$j]['game_id']).'\', \'_em_showtipps\', \'height=700,resizable=yes,scrollbars=yes,width=600\');return false;" target="_em_showtipps" class="nav"">'.$lang['em_showtipps'].'</a>' : '',
                        'GAME_TIPP_PTS'       => $em_tipps_pts,
                        'GAME_TIPP_PTS_ALIGN' => $em_tipps_pts_align,
                        'GAME_LOC'            => $games_row[$j]['game_loc'],
                        'GAME_LOCLINK'        => $games_row[$j]['game_loclink'],
                        'GAME_AWAY_TEAM'      => ( $games_row[$j]['game_away'] == 0 ) ? '--------': $teams_data[$games_row[$j]['game_away']]['team_name'],
                        'GAME_AWAY_IMG'       => ( $games_row[$j]['game_away'] == 0 ) ? 'none.gif': $teams_data[$games_row[$j]['game_away']]['team_img'],
                        'GAME_AWAY_LINK'      => ( $games_row[$j]['game_away'] == 0 ) ? 'http://de.uefa.com/competitions/euro/index.html': $teams_data[$games_row[$j]['game_away']]['team_link'],
                        'GAME_HOME_TEAM'      => ( $games_row[$j]['game_home'] == 0 ) ? '--------': $teams_data[$games_row[$j]['game_home']]['team_name'],
                        'GAME_HOME_IMG'       => ( $games_row[$j]['game_home'] == 0 ) ? 'none.gif': $teams_data[$games_row[$j]['game_home']]['team_img'],
                        'GAME_HOME_LINK'      => ( $games_row[$j]['game_home'] == 0 ) ? 'http://de.uefa.com/competitions/euro/index.html': $teams_data[$games_row[$j]['game_home']]['team_link']
                        )
        );
    }
}

// EM moderator buttons switch
if ( ($userdata['user_level'] == ADMIN || $userdata['user_id'] == $em_config['em_mod_id']) && $moderation == '' ) {
   $template->assign_block_vars('em_moderator_edit', array());
}
if ( $moderation == '' ) {
   $template->assign_block_vars('em_tipp_save', array());
}
$template->assign_block_vars('finals', array());
if ( $em_config['em_forum_id'] != 0 ) {
        $template->assign_block_vars('forum_enabled', array());
}

// Build EM champion
$first_game = get_first_game();
$users_winner_tipp = get_em_winner($userdata['user_id']);
if ( time() < $first_game[0]['game_time'] ) {
        $em_winner_combo  = '<select name="em_winner">';
        $em_winner_combo .= '<option value="0">' . $lang['l_em_winner_not_set'] . '</option>';
        $em_winner_combo .= '<option value="0">--------------</option>';
        for ( $r = 0; $r < count($em_teams); $r++ ) {
            $selected = ( $em_teams[$r]['team_id'] == $users_winner_tipp ) ? ' selected' : '';
            $em_winner_combo .= '<option value="' . $em_teams[$r]['team_id'] . '" ' . $selected . '>' . $em_teams[$r]['team_name'] . '</option>';
        }
        $em_winner_combo .= '</select>';

        $tipp_save_button = ( $users_winner_tipp == 0 ) ? $lang['l_em_winner_save'] : $lang['l_em_winner_edit'];
        $em_winner = $em_winner_combo . '&nbsp;<input type="submit" class="liteoption" name="add_em_winner" value="' . $tipp_save_button . '">';

}
else {
        $em_winner = ( $users_winner_tipp != 0 ) ? $lang['l_em_and_the_winner_is'] . ": <a href=\"" . $teams_data[$users_winner_tipp]['team_link'] . "\" target=\"_blank\">" . $teams_data[$users_winner_tipp]['team_name'] . "</a>" : $lang['l_em_winner_not_set'];
}


// General template vars and output
$template->assign_vars(array(
                             'L_EM_WELCOME_TITLE'     => $lang['l_em_round1_welcome_title'],
                             'L_EM_TITLE'             => $lang['l_em_round1_title'],
                             'L_EM_EXP'               => $lang['l_em_round1_exp'],
                             'L_EM_FINALS'            => $lang['l_em_nav_finals'],
                             'L_EM_STATS'             => $lang['l_em_nav_stats'],
                             'L_EM_WINNER_EXP'        => $lang['l_em_winner_exp'],
                             'L_EM_WINNER'            => $lang['l_em_stats_winnertipp'],
                             'EM_WINNER'              => $em_winner,
                             'L_EM_DATE'              => $lang['l_em_round1_date'],
                             'L_EM_LOCATION'          => $lang['l_em_round1_location'],
                             'L_EM_EVENT'             => $lang['l_em_round1_event'],
                             'L_EM_MOD'               => $lang['l_em_nav_mod'],
                             'L_EM_FORUM'             => $lang['l_em_nav_forum'],
                             'L_EM_RESULT'            => $lang['l_em_round1_result'],
                             'L_EM_TIPP'              => $lang['l_em_round1_tipp'],
                             'L_EM_TABLE'             => $lang['l_em_round1_table'],
                             'L_EM_ROUND1'            => $lang['l_em_nav_round1'],
                             'L_EM_POS'               => $lang['l_em_table_pos'],
                             'L_EM_TEAM'              => $lang['l_em_table_team'],
                             'L_EM_GOALS'             => $lang['l_em_table_goals'],
                             'L_EM_PLEASE_WAIT'       => $lang['l_em_please_wait'],
                             'L_EM_POINTS'            => $lang['l_em_table_points'],
                             'L_EM_SAVERESULTS'       => $lang['l_em_round1_saveresults'],
                             'L_EM_EDITRESULTS'       => $lang['l_em_round1_editresults'],
                             'L_EM_SAVETIPP'          => $lang['l_em_round1_savetipp'],
                             'L_EM_WINNERSTAT'		=>$lang['em_st_winnertips'],
                             'U_EM_WINNERSTAT'		=> append_sid("./em_winnerstat.".$phpEx),
                             'U_EM_MOD'               => append_sid("./privmsg.".$phpEx."?mode=post&u=".$em_config['em_mod_id']),
                             'U_EM_FORUM'             => append_sid("./viewforum.".$phpEx."?f=".$em_config['em_forum_id']),
                             'U_EM_FINALS'            => append_sid("./em_finals.".$phpEx),
                             'U_EM_ROUND1'            => append_sid("./em_round1.".$phpEx),
                             'U_EM_STATS'             => append_sid("./em_stats.".$phpEx),

                             'S_FORM_ACTION'          => append_sid("./em_finals.".$phpEx)
        )
);

$template->pparse('body');
include($phpbb_root_path . 'includes/page_tail.'.$phpEx);

?>