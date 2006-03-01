<?php
/************************************************************************/
/* ATutor																*/
/************************************************************************/
/* Copyright (c) 2002-2006 by Greg Gay, Joel Kronenberg & Heidi Hazelton*/
/* Adaptive Technology Resource Centre / University of Toronto			*/
/* http://atutor.ca														*/
/*																		*/
/* This program is free software. You can redistribute it and/or		*/
/* modify it under the terms of the GNU General Public License			*/
/* as published by the Free Software Foundation.						*/
/************************************************************************/
// $Id$

define('AT_INCLUDE_PATH', '../../include/');
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
	<div class="row">
		<input type="radio" name="create" value="automatic" id="automatic" checked="checked" /><label for="automatic">create multiple groups - Automatically creates <em>n</em> groups.</label>
		<p>(create groups to which you can add members later or create groups in which students are randomly distributed).</p>

		<input type="radio" name="create" value="manual" id="manual" /><label for="manual">create custom group - MANUAL. creates a SINGLE group to an existing Type.</label>
		<p>(create a single group and choose the members you want to add to it.)</p>
	</div>

	<div class="row buttons">
		<input type="submit" name="submit" value="<?php echo _AT('continue'); ?>" />
	</div>
</div>
</form>
<?php require(AT_INCLUDE_PATH.'footer.inc.php'); ?>