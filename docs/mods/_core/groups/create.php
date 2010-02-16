<?php
/************************************************************************/
/* ATutor																*/
/************************************************************************/
/* Copyright (c) 2002-2008 by Greg Gay, Joel Kronenberg & Heidi Hazelton*/
/* Adaptive Technology Resource Centre / University of Toronto			*/
/* http://atutor.ca														*/
/*																		*/
/* This program is free software. You can redistribute it and/or		*/
/* modify it under the terms of the GNU General Public License			*/
/* as published by the Free Software Foundation.						*/
/************************************************************************/
// $Id: create.php 7482 2008-05-06 17:44:49Z greg $

define('AT_INCLUDE_PATH', '../../../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');
authenticate(AT_PRIV_GROUPS);

if (isset($_GET['submit'], $_GET['create']) && ($_GET['create'] == 'automatic')) {
	header('Location: create_automatic.php');
	exit;
} else if (isset($_GET['submit'], $_GET['create']) && ($_GET['create'] == 'manual')) {
	header('Location: create_manual.php');
	exit;
}

require(AT_INCLUDE_PATH.'header.inc.php');
?>
<form method="get" action="<?php echo $_SERVER['PHP_SELF']; ?>">
<div class="input-form">
	<fieldset class="group_form"><legend class="group_form"><?php echo _AT('create_groups'); ?></legend>
	<div class="row">
		<input type="radio" name="create" value="automatic" id="automatic" checked="checked" /><label for="automatic"><?php echo _AT('groups_create_automatic'); ?></label>
	</div>

	<div class="row">
		<input type="radio" name="create" value="manual" id="manual" /><label for="manual"><?php echo _AT('groups_create_manual'); ?></label>
	</div>

	<div class="row buttons">
		<input type="submit" name="submit" value="<?php echo _AT('continue'); ?>" />
	</div>
	</fieldset>
</div>
</form>
<?php require(AT_INCLUDE_PATH.'footer.inc.php'); ?>