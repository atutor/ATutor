<?php
/************************************************************************/
/* ATutor																*/
/************************************************************************/
/* Copyright (c) 2002-2004 by Greg Gay, Joel Kronenberg & Heidi Hazelton*/
/* Adaptive Technology Resource Centre / University of Toronto			*/
/* http://atutor.ca														*/
/*																		*/
/* This program is free software. You can redistribute it and/or		*/
/* modify it under the terms of the GNU General Public License			*/
/* as published by the Free Software Foundation.						*/
/************************************************************************/

$page = 'language';
$_user_location = 'admin';

define('AT_INCLUDE_PATH', '../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');
if ($_SESSION['course_id'] > -1) { exit; }

if (isset($_POST['cancel'])) {
	Header('Location: language.php?lang_code='.$_POST['delete_lang'].SEP.'f='.urlencode_feedback(AT_FEEDBACK_CANCELLED));
	exit;
}

if (isset($_POST['submit'])) {
	$languageEditor =& new LanguageEditor($lang);
	$errors = $languageEditor->deleteLanguage($_POST['delete_lang']);

	if (!isset($errors)) {	
		Header('Location: language.php?f='.urlencode_feedback(AT_FEEDBACK_LANG_DELETED));
		exit;
	} else {
		$errors = AT_FEEDBACK_LANG_NOT_DELETED;
		Header('Location: language.php');
		exit;
	}
}

require(AT_INCLUDE_PATH.'header.inc.php'); 

echo '<h3>'._AT('language').'</h3>';
include(AT_INCLUDE_PATH . 'html/feedback.inc.php');
?>

<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
<input type="hidden" name="delete_lang" value="<?php echo $_GET['delete_lang']; ?>" />

<?php
	echo '<h4>'._AT('delete_language').'</h4>';

	$warnings[]=array(AT_WARNING_DELETE_LANG, $_GET['delete_lang']);
	include(AT_INCLUDE_PATH . 'html/feedback.inc.php');

?>
<input type="submit" name="submit" value="<?php echo _AT('delete'); ?>" class="button" /> - <input type="submit" name="cancel" class="button" value=" <?php echo _AT('cancel'); ?> " />
</form>

<?php require(AT_INCLUDE_PATH.'footer.inc.php'); ?>