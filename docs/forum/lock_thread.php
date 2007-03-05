<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2007 by Greg Gay & Joel Kronenberg        */
/* Adaptive Technology Resource Centre / University of Toronto  */
/* http://atutor.ca												*/
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.				*/
/****************************************************************/
// $Id$

$fid  = intval($_REQUEST['fid']);
 
if ($fid == 0) {
	$fid  = intval($_GET['fid']);
}
define('AT_INCLUDE_PATH', '../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');

authenticate(AT_PRIV_FORUMS);

$_pages['forum/index.php?fid='.$fid]['title']    = get_forum_name($fid);
$_pages['forum/index.php?fid='.$fid]['parent']   = 'forum/list.php';
$_pages['forum/index.php?fid='.$fid]['children'] = array('forum/new_thread.php?fid='.$fid);

$_pages['forum/new_thread.php?fid='.$fid]['title_var'] = 'new_thread';
$_pages['forum/new_thread.php?fid='.$fid]['parent']    = 'forum/index.php?fid='.$fid;

$_pages['forum/view.php']['title']  = $post_row['subject'];
$_pages['forum/view.php']['parent'] = 'forum/index.php?fid='.$fid;

$_pages['forum/lock_thread.php']['title_var'] = 'lock_thread';
$_pages['forum/lock_thread.php']['parent']    = 'forum/index.php?fid='.$fid;

if (isset($_POST['cancel'])) {
	$msg->addFeedback('CANCELLED');
	header('Location: '.AT_BASE_HREF.'forum/index.php?fid='.$fid);
	exit;
} else if (isset($_POST['submit'])) {
	$_POST['lock'] = intval($_POST['lock']);
	$_POST['pid']  = intval($_POST['pid']);
	$_POST['fid']  = intval($_POST['fid']);

	$sql	= "UPDATE ".TABLE_PREFIX."forums_threads SET locked=$_POST[lock], last_comment=last_comment, date=date WHERE post_id=$_POST[pid]";
	$result = mysql_query($sql, $db);

	if($_POST['lock'] == '1' || $_POST['lock'] == '2'){
		$msg->addFeedback('THREAD_LOCKED');
		header('Location: '.AT_BASE_HREF.'forum/index.php?fid='.$fid);
		exit;
	} else {
		$msg->addFeedback('THREAD_UNLOCKED');
		header('Location: '.AT_BASE_HREF.'forum/index.php?fid='.$fid);
		exit;
	}
}

require(AT_INCLUDE_PATH.'header.inc.php');

$pid  = intval($_GET['pid']);
$fid  = intval($_GET['fid']);
?>
<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
<input type="hidden" name="pid" value="<?php echo $pid?>">
<input type="hidden" name="fid" value="<?php echo $fid?>">

<div class="input-form" style="width: 40%;">

<?php if ($_GET['unlock']): ?>
	<div class="row">
		<input type="radio" name="lock" value="0" id="un"><label for="un"><?php echo _AT('unlock_thread'); ?></label>
	</div>

<?php endif; ?>
	<div class="row">
		<input type="radio" name="lock" value="1" id="rw" <?php
		if (($_GET['unlock'] == '') || ($_GET['unlock'] == 1)) {
			echo ' checked="checked"';
		}
		?>><label for="rw"><?php echo _AT('lock_no_read');  ?></label><br />
		<input type="radio" name="lock" value="2" id="w" <?php
		if ($_GET['unlock'] == 2) {
			echo ' checked="checked"';
		}
		?>><label for="w"><?php echo _AT('lock_no_post');  ?></label>
	</div>

	<div class="row buttons">
		<input name="submit" type="submit" value="<?php echo _AT('lock_submit');  ?>" />
		<input name="cancel" type="submit" value="<?php echo _AT('cancel');  ?>" />
	</div>
</div>

</form>
<?php require(AT_INCLUDE_PATH.'footer.inc.php'); ?>