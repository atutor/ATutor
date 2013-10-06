<?php
/****************************************************************************/
/* ATutor                                                                   */
/****************************************************************************/
/* Copyright (c) 2002-2010                                                  */
/* Inclusive Design Institute                                               */
/* http://atutor.ca                                                         */
/*                                                                          */
/* This program is free software. You can redistribute it and/or            */
/* modify it under the terms of the GNU General Public License              */
/* as published by the Free Software Foundation.                            */
/****************************************************************************/
// $Id$

define('AT_INCLUDE_PATH', '../../../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');

require(AT_INCLUDE_PATH.'header.inc.php'); 

	if (isset($_POST['poll_submit'], $_POST['choice'])) {
		$poll_id = intval($_POST['poll_id']);

		$sql = "INSERT INTO %spolls_members VALUES(%d, %d)";
		$result = queryDB($sql, array(TABLE_PREFIX, $poll_id, $_SESSION['member_id']));
		
		if($result > 0){
			$n = intval($_POST['choice']);
			$sql = "UPDATE %spolls SET count%d=count%d+1, total=total+1 WHERE poll_id=%d AND course_id=%d";
			$result = queryDB($sql, array(TABLE_PREFIX, $n, $n, $poll_id, $_SESSION[course_id]));
		}
	}

	if (!isset($include_all, $include_one)) {
		$include_one = ' checked="checked"';
	}

	$sql = "SELECT * FROM %spolls WHERE course_id=%d ORDER BY question";
	$rows_polls = queryDB($sql, array(TABLE_PREFIX, $_SESSION['course_id']));

	if(count($rows_polls) == 0){
		$msg->addInfo('NO_POLLS');
		$msg->printAll();
		require(AT_INCLUDE_PATH.'footer.inc.php'); 
		exit;
	}
    foreach($rows_polls as $row){
		echo '<form method="post" action="'.htmlspecialchars($_SERVER['REQUEST_URI'], ENT_QUOTES).'">';
		echo '<table width="70%" border="0" cellspacing="0" cellpadding="0" summary="" class="dropdown" align="center">';
		echo '<tr>';
		echo '<td valign="top" class="dropdown-heading" nowrap="nowrap" align="left"><strong>' . AT_print($row['question'], 'polls.question') . '</strong>';
		echo '<input type="hidden" name="poll_id" value="'.$row['poll_id'].'" /></td></tr>';

		if (!authenticate(AT_PRIV_POLLS, AT_PRIV_RETURN)) {
			$sql = "SELECT * FROM %spolls_members WHERE poll_id=%d AND member_id=%d";
			$my_poll_members = queryDB($sql, array(TABLE_PREFIX, $row['poll_id'], $_SESSION['member_id']));
		}

		if (authenticate(AT_PRIV_POLLS, AT_PRIV_RETURN) || count($my_poll_members) > 0) {
			for ($i=1; $i<= AT_NUM_POLL_CHOICES; $i++) {
				if ($row['choice' . $i]) {
					if ($row['total']) {
						$width = round($row['count' . $i] / $row['total'] * 110);
					} else {
						$width = 0;
					}

					echo '<tr>';
					echo '<td valign="top" class="dropdown" nowrap="nowrap" align="left">';
					echo '<small>' . AT_print($row['choice' . $i], 'polls.choice') . '</small><br />';
					echo '<img src="'.$_base_path . 'images/blue.gif" height="5" width="'.$width.'" alt="" /> '.$row['count' . $i];
					echo '</td></tr>';
				}
			}
		} else {
			for ($i=1; $i<= AT_NUM_POLL_CHOICES; $i++) {
				if ($row['choice' . $i]) {
					echo '<tr>';
					echo '<td valign="top" class="dropdown" nowrap="nowrap" align="left">';
					echo '<small><input type="radio" name="choice" value="'.$i.'" id="xc'.$i.$row['poll_id'].'" /><label for="xc'.$i.$row['poll_id'].'">' . AT_print($row['choice' . $i], 'polls.choice') . '</label></small></td></tr>';
				}
			}

			echo '<tr>';
			echo '<td valign="top" class="dropdown" nowrap="nowrap" align="center"><input type="submit" name="poll_submit" value="'._AT('submit').'" class="button" />';
			echo '<br /><small>'._AT('vote_to_see_results').'</small>';
			echo '</td></tr>';
		}
		
		echo '</table></form><br />';
	}

require(AT_INCLUDE_PATH.'footer.inc.php'); 
?>