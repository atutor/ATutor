<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2003 by Greg Gay & Joel Kronenberg        */
/* Adaptive Technology Resource Centre / University of Toronto  */
/* http://atutor.ca												*/
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.				*/
/****************************************************************/


	define('AT_INCLUDE_PATH', '../include/');
	require(AT_INCLUDE_PATH.'vitals.inc.php');
	
	if ($_POST['cancel']) {
		Header('Location: '.$_base_href.'glossary/?f='.urlencode_feedback(AT_FEEDBACK_CANCELLED));
		exit;
	}

	if ($_POST['submit']) {

		$_POST['gid'] = intval($_POST['gid']);

		$sql = "DELETE FROM ".TABLE_PREFIX."glossary WHERE word_id=$_POST[gid] AND course_id=$_SESSION[course_id]";
		$result = mysql_query($sql, $db);

		$sql = "UPDATE ".TABLE_PREFIX."glossary SET related_word_id=0 WHERE related_word_id=$_POST[gid] AND course_id=$_SESSION[course_id]";
		$result = mysql_query($sql, $db);

		Header('Location: ../glossary/?L='.strtoupper(substr($_POST['word'], 0, 1)).SEP.'f='.urlencode_feedback(AT_FEEDBACK_GLOSSARY_DELETE2));
		exit;
	} else if ($_POST['submit_no']) {

		Header('Location: ../glossary/?L='.strtoupper(substr($_POST['word'], 0, 1)).SEP.'f='.urlencode_feedback(AT_FEEDBACK_CANCELLED));
		exit;
	}

	$_section[0][0] = _AT('delete_this_term1');

	require(AT_INCLUDE_PATH.'header.inc.php');

	echo '<h3>'._AT('delete_this_term1').'</h3>';

	$_GET['gid'] = intval($_GET['gid']);

	if ($_GET['gid'] == 0) {
		$errors[]=AT_ERROR_GLOS_ID_MISSING;
		print_errors($errors);
		require(AT_INCLUDE_PATH.'footer.inc.php');
		exit;
	}

	echo '<form action="'.$_SERVER['PHP_SELF'].'" method="post">';
	echo '<input type="hidden" name="word" value="'.$_GET['t'].'">';
	echo '<input type="hidden" name="gid" value="'.$_GET['gid'].'">';
	echo '<p>';

		$warnings[]=AT_WARNING_GLOSSARY_REMAINS2;
		$warnings[]=AT_WARNING_GLOSSARY_DELETE;
		print_warnings($warnings);

	echo '<input type="submit" name="submit" value="'._AT('yes_delete').'" class="button">';
	echo ' - <input type="submit" name="submit_no" value="'._AT('no_cancel').'" class="button">';

	echo '</form>';
	echo '</p>';


	require(AT_INCLUDE_PATH.'footer.inc.php');
?>