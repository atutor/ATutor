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

define('AT_INCLUDE_PATH', '../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');

authenticate(AT_PRIV_ANNOUNCEMENTS);

	if ($_POST['cancel']) {
		Header('Location: ../index.php?f='.urlencode_feedback(AT_FEEDBACK_CANCELLED));
		exit;
	}

if ($_POST['delete_news']) {
	$_POST['form_news_id'] = intval($_POST['form_news_id']);

	$sql = "DELETE FROM ".TABLE_PREFIX."news WHERE news_id=$_POST[form_news_id] AND course_id=$_SESSION[course_id]";
	$result = mysql_query($sql, $db);

	header('Location: ../index.php?f='.urlencode_feedback(AT_FEEDBACK_NEWS_DELETED));
	exit;
}

$_section[0][0] = _AT('delete_announcement');

require(AT_INCLUDE_PATH.'header.inc.php');
?>
<h2><?php echo _AT('delete_announcement'); ?></h2>
<?php

	$_GET['aid'] = intval($_GET['aid']); 

	$sql = "SELECT * FROM ".TABLE_PREFIX."news WHERE news_id=$_GET[aid] AND member_id=$_SESSION[member_id] AND course_id=$_SESSION[course_id]";

	$result = mysql_query($sql,$db);
	if (mysql_num_rows($result) == 0) {
		$errors[]=AT_ERROR_ANN_NOT_FOUND;
		print_errors($errors);
	} else {
		$row = mysql_fetch_assoc($result);
?>
	<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
	<input type="hidden" name="delete_news" value="true">
	<input type="hidden" name="form_news_id" value="<?php echo $row['news_id']; ?>">
	<?php
		$warnings[]=array(AT_WARNING_DELETE_NEWS, AT_print($row['title'], 'news.title'));
		print_warnings($warnings);

	?>
	<input type="submit" name="submit" value="<?php echo _AT('delete'); ?>" class="button"> - <input type="submit" name="cancel" class="button" value=" <?php echo _AT('cancel'); ?> " />
	</form>
	<?php
}
require(AT_INCLUDE_PATH.'footer.inc.php');
?>