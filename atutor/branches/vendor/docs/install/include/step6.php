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

//require 'include/config_template.php';

if(isset($_POST['submit'])) {
	unset($errors);

	unset($_POST['submit']);
	$step++;
	return;
	
}

print_progress($step);

if (isset($errors)) {
	echo $errors;
	echo '<p class="error">Please edit your information and try again.</p>';
}

?>

<br /><p class="heading">Languages</p>

<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" name="form">
<input type="hidden" name="step" value="6" />

<center><table width="65%" class="tableborder" cellspacing="0" cellpadding="1">
<tr>
	<td class="row1" align="right">x:</td>
	<td class="row1"> x</td>
</tr>
<tr>
	<td class="row1" align="right">x:</td>
	<td class="row1"> x</td>
</tr>
<tr>
	<td class="row1" align="right">x:</td>
	<td class="row1"> x</td>
</tr>

</table></center>

<br /><br /><p align="center"><input type="submit" class="button" value="Next » " name="submit" /></p>

</form>