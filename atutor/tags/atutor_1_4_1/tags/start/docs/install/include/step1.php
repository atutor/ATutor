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

if (isset($_POST['submit'])) {
	if ($_POST['submit'] == 'I Agree') {	
		unset($_POST['submit']);
		$step++;
		unset($_POST['action']);
		return;
	} else {
		header('Location: index.php');
		exit;
	}
}

print_progress($step);
?>

<h3>GNU GPL for non-Commercial Use</h3>
<p>break down of the GPL license here</p>

<h3>ATutor License for Commercial Use</h3>
<p>break down of the ATutor license here</p>

<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" name="form">

<input type="hidden" name="action" value="process" />
<input type="hidden" name="step" value="1" />
<input type="hidden" name="new_version" value="<?php echo $new_version; ?>" />


<p>Stuff you have to agree to.</p>
<input type="submit" name="submit" class="button" value="I Agree" /> - <input type="submit" name="cancel" class="button" value="I Disagree" /><br />

</form>