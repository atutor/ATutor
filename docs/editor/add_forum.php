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
// $Id$
define('AT_INCLUDE_PATH', '../include/');
require (AT_INCLUDE_PATH.'vitals.inc.php');

authenticate(AT_PRIV_FORUMS);

if ($_POST['cancel']) {
	$msg->addFeedback('CANCELLED');
	header('Location: '.$_base_href.'tools/forums/index.php');
	exit;
}

if ($_POST['add_forum'] && (authenticate(AT_PRIV_FORUMS, AT_PRIV_RETURN))) {
	if ($_POST['title'] == '') {
		$msg->addError('FORUM_TITLE_EMPTY');
	}

	if (!$msg->containsErrors()) {
		require (AT_INCLUDE_PATH.'lib/forums.inc.php');
		add_forum($_POST);
		
		$msg->addFeedback('FORUM_ADDED');
		header('Location: '.$_base_href.'tools/forums/index.php');
		exit;
	}
}

$_section[0][0] = _AT('discussions');
$_section[0][1] = 'discussions/';
$_section[1][0] = _AT('forums');
$_section[1][1] = 'forum/list.php';
$_section[2][0] = _AT('add_forum');

$onload = 'onLoad="document.form.title.focus()"';

require(AT_INCLUDE_PATH.'header.inc.php');

$msg->printErrors();

?>

<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" name="form">
<input type="hidden" name="add_forum" value="true">

<div class="input-form">
	<div class="row">
		<label for="title"><?php echo _AT('title'); ?></label><br />
		<input type="text" name="title" size="40" id="title" />
	</div>
	<div class="row">
		<label for="body"><?php echo _AT('description'); ?></label><br />
		<textarea name="body" cols="45" rows="10" id="body" wrap="wrap"></textarea>
	</div>
	<div class="row buttons">
		<input type="submit" name="submit" value="<?php echo _AT('save'); ?>" accesskey="s" /> 
		<input type="submit" name="cancel" value="<?php echo _AT('cancel'); ?>" />
	</div>
</div>
</form>

<?php require(AT_INCLUDE_PATH.'footer.inc.php'); ?>