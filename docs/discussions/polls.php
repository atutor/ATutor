<?php
/****************************************************************************/
/* ATutor																	*/
/****************************************************************************/
/* Copyright (c) 2002-2005 by Greg Gay, Joel Kronenberg & Heidi Hazelton	*/
/* Adaptive Technology Resource Centre / University of Toronto				*/
/* http://atutor.ca															*/
/*																			*/
/* This program is free software. You can redistribute it and/or			*/
/* modify it under the terms of the GNU General Public License				*/
/* as published by the Free Software Foundation.							*/
/****************************************************************************/

define('AT_INCLUDE_PATH', '../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');

$_section[0][0] = _AT('discussions');
$_section[0][1] = 'discussions/index.php';
$_section[1][0] = _AT('polls');

require(AT_INCLUDE_PATH.'header.inc.php'); 

if ($_GET['col']) {
	$col = addslashes($_GET['col']);
} else {
	$col = 'created_date';
}

if ($_GET['order']) {
	$order = addslashes($_GET['order']);
} else {
	$order = 'DESC';
}

${'highlight_'.$col} = ' style="font-size: 1em;"';

$sql	= "SELECT * FROM ".TABLE_PREFIX."polls WHERE course_id=$_SESSION[course_id] ORDER BY $col $order";
$result = mysql_query($sql, $db);


echo '<h2>';
	if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 2) {
		echo '<a href="discussions/" class="hide" ><img src="images/icons/default/square-large-discussions.gif" vspace="2" border="0"  class="menuimageh2" width="42" height="40" alt="" /></a> ';
	}
	if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 1) {
		echo '<a href="discussions/" class="hide" >'._AT('discussions').'</a>';
	}
echo '</h2>';

echo '<h3>';
	if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 2) {
		echo '&nbsp;<img src="images/icons/default/polls-large.gif"  class="menuimageh3" width="42" height="38" alt="" /> ';
	}
	if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 1) {
		echo _AT('polls');
	}
echo '</h3>';

require_once(AT_INCLUDE_PATH.'classes/Message/Message.class.php');

global $savant;
$msg =& new Message($savant);
		
/* admin editing options: */
/* this session thing is a hack to temporarily prevent the en/dis editor link from affecting 'add poll' */
if (authenticate(AT_PRIV_POLLS, AT_PRIV_RETURN)) {
	unset($editors);
	$editors[] = array('priv' => AT_PRIV_POLLS, 'title' => _AT('add_poll'), 'url' => $_base_path.'editor/add_poll.php');
	print_editor($editors , $large = true);

	if (!$_SESSION['prefs'][PREF_EDIT]) {
		
		$help = array('ENABLE_EDITOR', $_my_uri);
		$msg->printHelps($help);
	}
}



if (!($row = mysql_fetch_assoc($result))) {
	echo '<p>'._AT('no_polls_found').'</p>';
} else {
	$msg->printAll();

	$num_rows = mysql_num_rows($result);
?>

<table cellspacing="1" cellpadding="0" border="0" class="bodyline" summary="" width="95%" align="center">
<tr>
	<th colspan="8" class="cyan"><?php 
		echo _AT('polls');
	?></th>
</tr>
<tr>
	<th scope="col" class="cat"><small<?php echo $highlight_question; ?>><?php echo _AT('question'); ?> <a href="<?php echo $_SERVER['PHP_SELF']; ?>?col=question<?php echo SEP; ?>order=asc" title="<?php echo _AT('question_ascending'); ?>"><img src="images/asc.gif" alt="<?php echo _AT('question_ascending'); ?>" style="height:0.50em; width:0.83em" border="0" height="7" width="11" /></a> <a href="<?php echo $_SERVER['PHP_SELF']; ?>?col=question<?php echo SEP; ?>order=desc" title="<?php echo _AT('question_descending'); ?>"><img src="images/desc.gif" alt="<?php echo _AT('question_descending'); ?>" style="height:0.50em; width:0.83em" border="0" height="7" width="11" /></a></small></th>

	<th scope="col" class="cat"><small<?php echo $highlight_created_date; ?>><?php echo _AT('created_date'); ?> <a href="<?php echo $_SERVER['PHP_SELF']; ?>?col=created_date<?php echo SEP; ?>order=asc" title="<?php echo _AT('created_date_ascending'); ?>"><img src="images/asc.gif" alt="<?php echo _AT('created_date_ascending'); ?>" style="height:0.50em; width:0.83em" border="0" height="7" width="11" /></a> <a href="<?php echo $_SERVER['PHP_SELF']; ?>?col=created_date<?php echo SEP; ?>order=desc" title="<?php echo _AT('created_date_descending'); ?>"><img src="images/desc.gif" alt="<?php echo _AT('created_date_descending'); ?>" style="height:0.50em; width:0.83em" border="0" height="7" width="11" /></a></small></th>

	<?php if ($_SESSION['prefs'][PREF_EDIT]): ?>
		<th class="cat"><small>&nbsp;</small></th>
	<?php endif; ?>
</tr>
<?php
	do {
		echo '<tr>';
		echo '<td class="row1"><a href="discussions/poll.php?id='.$row['poll_id'].'">'.AT_print($row['question'], 'polls.question').'</a></td>';
		echo '<td class="row1">'.$row['created_date'].'</td>';

		if ($_SESSION['prefs'][PREF_EDIT]) {
			echo '<td class="row1"><a href="editor/edit_poll.php?poll_id='.$row['poll_id'].'">'._AT('edit').'</a> | <a href="editor/delete_poll.php?pid='.$row['poll_id'].'">'._AT('delete').'</a></td>';
		}
		echo '</tr>';

		if ($count < $num_rows-1) {
			echo '<tr><td height="1" class="row2" colspan="3"></td></tr>';
		}
		$count++;
	} while ($row = mysql_fetch_assoc($result));
	echo '</table>';
}

require(AT_INCLUDE_PATH.'footer.inc.php'); 
?>