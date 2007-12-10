<?php
/************************************************************************/
/* ATutor																*/
/************************************************************************/
/* Copyright (c) 2002-2003 by Greg Gay, Joel Kronenberg, Heidi Hazelton	*/
/* http://atutor.ca														*/
/*																		*/
/* This program is free software. You can redistribute it and/or		*/
/* modify it under the terms of the GNU General Public License			*/
/* as published by the Free Software Foundation.						*/
/************************************************************************/
if (!defined('AT_INCLUDE_PATH')) { exit; }
session_unset();	//clear session before using it
$_SESSION = array();
if (isset($_POST['submit'])) {
	if ($_POST['submit'] == 'I Agree') {
		unset($_POST['submit']);
		$step++;
		unset($_POST['action']);
		return;
	} else {
		exit;
	}
}

print_progress($step);
?>
<p>ATutor is licensed under the terms of the <a href="http://atutor.ca/services/licensing_gpl.php" target="_new">GNU General Public License (GPL)</a>, which essentially allows for the free distribution and modification of ATutor. ATutor has its own license that governs its use outside the bounds of the GPL.</p>

<p>Please see <a href="http://atutor.ca/services/licensing.php" target="_new">atutor.ca</a> for additional details.</p>

<p>If you do not agree to the Terms of Use then you may not install and use ATutor.</p>

<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" name="form">
	<input type="hidden" name="action" value="process" />
	<input type="hidden" name="step" value="1" />
	<input type="hidden" name="new_version" value="<?php echo $new_version; ?>" />
	<div align="center">
		<input type="submit" name="submit" class="button" value="I Agree" /> - <input type="submit" name="submit" class="button" value="I Disagree" /><br />
	</div>
</form>