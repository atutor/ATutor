<?php
/************************************************************************/
/* ATutor																*/
/************************************************************************/
/* Copyright (c) 2002-2005 by Greg Gay, Joel Kronenberg & Heidi Hazelton*/
/* Adaptive Technology Resource Centre / University of Toronto			*/
/* http://atutor.ca														*/
/*																		*/
/* This program is free software. You can redistribute it and/or		*/
/* modify it under the terms of the GNU General Public License			*/
/* as published by the Free Software Foundation.						*/
/************************************************************************/

$page = 'themes';
$_user_location = 'admin';

define('AT_INCLUDE_PATH', '../../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');


if (isset($_POST['cancel'])) {
	header('Location: index.php?theme_code='.$_GET['theme_name'].SEP.'f='.urlencode_feedback(AT_FEEDBACK_CANCELLED));
	exit;
}

if (isset($_POST['submit'])) {
	require_once(AT_INCLUDE_PATH.'lib/themes.inc.php');
	
	delete_theme ($_POST['theme_code']);

	header('Location: index.php?f='.urlencode_feedback(AT_FEEDBACK_THEME_DELETED));
	exit;
}

require(AT_INCLUDE_PATH.'header.inc.php'); 

echo '<br /> <h2>';
echo ' <a href="admin/themes/index.php" >'._AT('themes').'</a>';
echo '</h2>';

echo '<h3>';
echo _AT('delete');
echo '</h3>';

include(AT_INCLUDE_PATH . 'html/feedback.inc.php');
?>

<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
<input type="hidden" name="theme_code" value="<?php echo $_GET['theme_code']; ?>" />

<?php
	$warnings[] = array(AT_WARNING_DELETE_THEME, $_GET['theme_code']);
	include(AT_INCLUDE_PATH . 'html/feedback.inc.php');

?>
	<div align="center">
		<input type="submit" name="submit" value="<?php echo _AT('delete'); ?>" class="button" /> - 
		<input type="submit" name="cancel" class="button" value=" <?php echo _AT('cancel'); ?> " />
	</div>
</form>

<?php require(AT_INCLUDE_PATH.'footer.inc.php'); ?>