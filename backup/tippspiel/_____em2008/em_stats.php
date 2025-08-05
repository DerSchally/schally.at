<?php
/***************************************************************************
 *                               em_stats.php
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

include($phpbb_root_path . 'language/lang_' . $board_config['default_lang'] . '/lang_em.'.$phpEx);

//
// Set pagetitle, pageheader and templatefile
//
$page_title = $lang['em_title_home'];

include($phpbb_root_path . 'includes/page_header.'.$phpEx);

$template->set_filenames(array(
	'body' => 'em_stats_body.tpl')
);

//
// Init arrays               Array contains:
//                           ---------------
$em_config     = array();  // Config values
$em_users_data = array();  // All userdata from users with at least 1 EM point

//
// Load data
//
$em_config     = get_em_config();
$em_users_data = get_em_users();
$team_data     = get_em_teams();
$all_user_data = get_all_the_users();
$champion_tips = get_em_champion_tipps();

//
// Listing
//
// Users loop
$int_userpkt = -1;
$int_userpos = 0;

for ( $i = 0; $i < count($em_users_data); $i++ )
{
   // absolute position now
   $int_userpos_absolute = $i+1;

   // user position yesterday
   $int_userpos_yesterday = get_rank_of_yesterday($em_users_data[$i]['tipp_user']);

   // compare the absolute positions
   if($int_userpos_yesterday < $int_userpos_absolute )
   {
      $userpos_change = '<img src="./images/em/down.gif" alt="Down" border="0" />';
   }
   else if ($int_userpos_yesterday == $int_userpos_absolute )
   {
      $userpos_change = '<img src="./images/em/stay.gif" alt="Stay" border="0" />';
   }
   else if ($int_userpos_yesterday > $int_userpos_absolute )
   {
      $userpos_change = '<img src="./images/em/up.gif" alt="Up" border="0" />';
   }
   $int_userposalt = $int_userpos;
   $int_userpos = ($int_userpkt != $em_users_data[$i]['user_points']) ? $int_userpos + 1 : $int_userpos;

   // Group loop switches
   $template->assign_block_vars('userrow', array(
      'USER_POS'         => ($int_userpos != $int_userposalt) ? $int_userpos : '',
      'USER_NAME'         => $all_user_data[$em_users_data[$i]['tipp_user']],
      'USER_PROFILE_LINK'   => append_sid("profile.".$phpEx."?mode=viewprofile&u=".$em_users_data[$i]['tipp_user']),
      'USER_EM_TIPP'      => ( array_key_exists($em_users_data[$i]['tipp_user'] , $champion_tips) ) ? "<a href=\"" . $team_data[$champion_tips[$em_users_data[$i]['tipp_user']]]['team_link'] . "\" target=\"_blank\">" . $team_data[$champion_tips[$em_users_data[$i]['tipp_user']]]['team_name'] . "</a>" : $lang['l_em_winner_not_set'],
      'USER_EM_MADE'      => $em_users_data[$i]['user_total_tipps'],
      'USERTIPPS' => '<br /><a href="'.append_sid('em_usertipps.'.$phpEx.'?user_id='.$em_users_data[$i]['tipp_user']).'" onclick="window.open(\''.append_sid('em_usertipps.'.$phpEx.'?user_id='.$em_users_data[$i]['tipp_user']).'\', \'_em_usertipps\', \'height=580,resizable=yes,scrollbars=yes,width=780\');return false;" target="_em_usertipps" class="nav"">'.$lang['em_showtipps'].'</a>',
      'USER_POINTS_RESULT' => $em_users_data[$i]['user_points_result'],
      'USER_POINTS_DIFFERENCE' => $em_users_data[$i]['user_points_difference'],
      'USER_POINTS_TENDENCY' => $em_users_data[$i]['user_points_tendency'],
      'USER_EM_POS_CHANGE' => $userpos_change,
      'USER_POINTS'      => $em_users_data[$i]['user_points'])
   );

   $int_userpkt = $em_users_data[$i]['user_points'];
}

// Check if tippforum is enabled
if ( $em_config['em_forum_id'] != 0 ) {
        $template->assign_block_vars('forum_enabled', array());
}


// Assign vars
$template->assign_vars(array(
                             'L_EM_WELCOME_TITLE' => $lang['l_em_round1_welcome_title'],
                             'L_EM_TITLE'         => $lang['l_em_stats_title'],
                             'L_EM_STATS_USER'    => $lang['l_em_stats_user'],
                             'L_EM_STATS_POINTS'  => $lang['l_em_stats_points'],
                             'L_EM_STATS_MADE'    => $lang['l_em_stats_made_tipps'],
                             'L_EM_MOD'           => $lang['l_em_nav_mod'],
                             'L_EM_FORUM'         => $lang['l_em_nav_forum'],
                             'L_EM_STATS_POS'     => $lang['l_em_stats_pos'],
                             'L_EM_STATS_WINNER'  => $lang['l_em_stats_winnertipp'],
                             'L_EM_EXP'           => $lang['l_em_stats_exp'],
                             'L_EM_ROUND1'        => $lang['l_em_nav_round1'],
                             'L_EM_FINALS'        => $lang['l_em_nav_finals'],
                             'L_EM_STATS'         => $lang['l_em_nav_stats'],
                             'L_EM_WINNERSTAT'		=> $lang['em_st_winnertips'],
                             'U_EM_WINNERSTAT'		=> append_sid("./em_winnerstat.".$phpEx),
                             'U_EM_MOD'           => append_sid("./privmsg.".$phpEx."?mode=post&u=".$em_config['em_mod_id']),
                             'U_EM_FORUM'         => append_sid("./viewforum.".$phpEx."?f=".$em_config['em_forum_id']),
                             'U_EM_FINALS'        => append_sid("./em_finals.".$phpEx),
                             'U_EM_STATS'         => append_sid("./em_stats.".$phpEx),
                             'U_EM_ROUND1'        => append_sid("./em_round1.".$phpEx),
                             'L_PICTURE_CUP'     => 'cup.gif',
                             'L_PICTURE_BALL'     => 'ball.gif',
                             'L_PICTURE_SHOE'     => 'shoe.gif',
                             'L_EM_HITS'     => $lang['l_em_hits'],
                             'L_EM_GOALDIFF'     => $lang['l_em_goaldiff'],
                             'L_EM_TENDENCY'     => $lang['l_em_tendency']
        )
);

$template->pparse('body');
include($phpbb_root_path . 'includes/page_tail.'.$phpEx);

?>