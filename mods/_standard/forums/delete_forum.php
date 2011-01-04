<?php
/****************************************************************************/
/* ATutor																	*/
/****************************************************************************/
/* Copyright (c) 2002-2010                                                  */
/* Inclusive Design Institute                                               */
/* http://atutor.ca															*/
/*																			*/
/* This program is free software. You can redistribute it and/or			*/
/* modify it under the terms of the GNU General Public License				*/
/* as published by the Free Software Foundation.							*/
/****************************************************************************/
// $Id: delete_forum.php 10142 2010-08-17 19:17:26Z hwong $

define('AT_INCLUDE_PATH', '../../../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');

authenticate(AT_PRIV_FORUMS);

require (AT_INCLUDE_PATH.'../mods/_standard/forums/lib/forums.inc.php');

if (isset($_POST['submit_no'])) {
	$msg->addFeedback('CANCELLED');
	header('Location: '.AT_BASE_HREF.'mods/_standard/forums/index.php');
	exit;
} else if (isset($_POST['submit_yes'])) {
	$_POST['fid'] = intval($_POST['fid']);

	// check if this forum is shared:
	// (if this forum is shared, then we do not want to delete it.)
	if (!is_shared_forum($_POST['fid']) && valid_forum_user($_POST['fid'])) {
		delete_forum($_POST['fid']);
		$msg->addFeedback('ACTION_COMPLETED_SUCCESSFULLY');
	} else {
		$msg->addError('FORUM_NO_DEL_SHARE');
	}
	
	header('Location: '.AT_BASE_HREF.'mods/_standard/forums/index.php');
	exit;
}

$_section[0][0] = _AT('discussions');
$_section[0][1] = 'discussions/';
$_section[1][0] = _AT('forums');
$_section[1][1] = 'forum/list.php';
$_section[2][0] = _AT('delete_forum');

require(AT_INCLUDE_PATH.'header.inc.php');


$_GET['fid'] = intval($_GET['fid']); 

$row = get_forum($_GET['fid'], $_SESSION['course_id']);

if (!is_array($row)) {
	$msg->addError('FORUM_NOT_ADDED');
} else { ?>
	<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
	<input type="hidden" name="delete_forum" value="true">
	<input type="hidden" name="fid" value="<?php echo $_GET['fid']; ?>">
		
	<?php
	$hidden_vars['delete_forum'] = TRUE;
	$hidden_vars['fid'] = $_GET['fid'];
			
	$confirm = array('DELETE_FORUM', AT_print($row['title'], 'forums.title'));
	$msg->addConfirm($confirm, $hidden_vars);
	$msg->printConfirm();
}


require(AT_INCLUDE_PATH.'footer.inc.php');
?>