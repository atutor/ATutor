<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2004 by Greg Gay & Joel Kronenberg        */
/* Adaptive Technology Resource Centre / University of Toronto  */
/* http://atutor.ca												*/
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.				*/
/****************************************************************/

$fid  = intval($_POST['fid']);
 
if ($fid == 0) {
	$fid  = intval($_GET['fid']);
}
define('AT_INCLUDE_PATH', '../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');

authenticate(AT_PRIV_FORUMS);

$_section[0][0] = _AT('discussions');
$_section[0][1] = 'discussions/';
$_section[1][0] = _AT('forums');
$_section[1][1] = 'forum/list.php';
$_section[2][0] = get_forum_name($fid);
$_section[2][1] = 'forum/index.php?fid='.$fid;
$_section[3][0] = _AT('lock_thread');

require_once(AT_INCLUDE_PATH.'classes/Message/Message.class.php');

global $savant;
$msg =& new Message($savant);

if ($_POST['submit']){
	$_POST['lock'] = intval($_POST['lock']);
	$_POST['pid']  = intval($_POST['pid']);
	$_POST['fid']  = intval($_POST['fid']);

	$sql	= "UPDATE ".TABLE_PREFIX."forums_threads SET locked=$_POST[lock] WHERE post_id=$_POST[pid]";
	$result = mysql_query($sql, $db);

	if($_POST['lock'] == '1' || $_POST['lock'] == '2'){
		$msg->addFeedback('THREAD_LOCKED');
		header('Location: '.$_base_href.'forum/index.php?fid='.$fid);
		exit;
	} else {
		$msg->addFeedback('THREAD_UNLOCKED');
		header('Location: '.$_base_href.'forum/index.php?fid='.$fid);
		exit;
	}
}

require(AT_INCLUDE_PATH.'header.inc.php');

	echo '<h2>';
	if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 2) {
		echo '<img src="images/icons/default/square-large-discussions.gif" width="42" height="38" border="0" alt="" class="menuimage" /> ';
	}
	if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 1) {
		echo _AT('discussions');
	}
	echo '</h2>';
echo '<h3>';
if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 2) {
		echo '<img src="images/icons/default/forum-large.gif" width="42" height="38" border="0" alt="" class="menuimageh3" />';
}
echo '<a href="forum/index.php?fid='.$fid.'">'.AT_print(get_forum_name($fid), 'forums.title').'</a></h3>';

$pid  = intval($_GET['pid']);
$fid  = intval($_GET['fid']);
?>
<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
<input type="hidden" name="pid" value="<?php echo $pid?>">
<input type="hidden" name="fid" value="<?php echo $fid?>">
<table cellspacing="1" cellpadding="0" border="0" class="bodyline" align="center" summary="">
<tr>
	<th class="cyan"><b><?php echo _AT('lock_type');  ?>:</td>
</tr>
<?php if ($_GET['unlock']) { ?>
<tr>
	<td class="row1"><input type="radio" name="lock" value="0" id="un"><label for="un"><?php echo _AT('unlock'); ?></label></td>
</tr>
<tr><td height="1" class="row2" colspan="2"></td></tr>
<?php } ?>
<tr>
	<td class="row1"><input type="radio" name="lock" value="1" id="rw" <?php
		if (($_GET['unlock'] == '') || ($_GET['unlock'] == 1)) {
			echo ' checked="checked"';
		}
	?>><label for="rw"><?php echo _AT('lock_no_read');   ?></label></td>
</tr>
<tr><td height="1" class="row2" colspan="2"></td></tr>
<tr>
	<td class="row1"><input type="radio" name="lock" value="2" id="w" <?php
		if ($_GET['unlock'] == 2) {
			echo ' checked="checked"';
		}
		?>><label for="w"><?php echo _AT('lock_no_post');  ?></label><br /><br /></td>
</tr>
<tr><td height="1" class="row2" colspan="2"></td></tr>
<tr><td height="1" class="row2" colspan="2"></td></tr>
<tr>
	<td class="row1" colspan="2" align="center"><input name=submit class="button" type=submit value="<?php echo _AT('lock_submit');  ?>" /></td>
</tr>
</table>
</form>
<?php
	require(AT_INCLUDE_PATH.'footer.inc.php');
?>