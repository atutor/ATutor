<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2004 by Greg Gay & Joel Kronenberg        */
/* Adaptive Technology Resource Centre / University of Toronto  */
/* http://atutor.ca												*/
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.				*/
/****************************************************************/

define('AT_INCLUDE_PATH', '../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');

authenticate(AT_PRIV_ADMIN);

$_section[0][0] = _AT('tools');
$_section[0][1] = 'tools/';
$_section[1][0] = _AT('course_copyright2');
if ($_POST['cancel']) {
	if ($_POST['pid'] != 0) {
		Header('Location: ../index.php?cid='.$_POST['pid'].';f='.urlencode_feedback(AT_FEEDBACK_CANCELLED));
		exit;
	}
	Header('Location: ../tools/index.php?f='.urlencode_feedback(AT_FEEDBACK_CANCELLED));
	exit;
}

if($_POST['update']){
	$head_sql ="UPDATE ".TABLE_PREFIX."courses SET copyright='".$_POST['copyright']."' WHERE course_id='$_SESSION[course_id]'";
	$result = mysql_query($head_sql, $db);
	$feedback[]=AT_FEEDBACK_COPYRIGHT_UPDATED;
}

require(AT_INCLUDE_PATH.'header.inc.php');

//$warning[]=AT_WARNING_SAVE_YOUR_WORK;
print_feedback($feedback);
print_errors($errors);
print_warnings($warnings);
print_help($help);

echo '<h2>';
	if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 2) {
		echo '<a href="tools/" class="hide" ><img src="images/icons/default/square-large-tools.gif" vspace="2" border="0"  class="menuimageh2" width="42" height="40" alt="" /></a> ';
	}
	if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 1) {
		echo '<a href="tools/" class="hide" >'._AT('tools').'</a>';
	}
echo '</h2>';

echo '<h3>';
	if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 2) {
		echo '&nbsp;<img src="images/icons/default/edit-header-large.gif"  class="menuimageh3" width="42" height="38" alt="" /> ';
	}
	if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 1) {
		echo _AT('course_copyright2');
	}
echo '</h3>';

//$help[]=AT_HELP_CREATE_HEADER;
//print_help($help);
?>

<p><?php echo _AT('copyright_txt2'); ?></p>

<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" name="form" enctype="multipart/form-data">
<input type="hidden" name="MAX_FILE_SIZE" value="204000" />
<table cellspacing="0" cellpadding="0" border="0" class="bodyline" summary="" align="center">
	<tr><td colspan="2" class="cat"><label for="copyright"><?php echo _AT('course_copyright'); ?></label></td></tr>
	<tr><td colspan="2" align="center" class="row1">
	
	<textarea name="copyright" rows="5" cols="65" class="formfield" id="copyright"><?php
		$getcopyright_sql="select copyright from ".TABLE_PREFIX."courses where course_id='$_SESSION[course_id]'";
		$result2=mysql_query($getcopyright_sql, $db);
		while($row=mysql_fetch_row($result2)){
			$show_edit_copyright = $row[0];
		}
		if (strlen($show_edit_copyright) > 0){
			echo $show_edit_copyright;
		}
	?></textarea>
	<input type="hidden" name="update" value="1" />
	<br />
	<input type="submit" value="<?php echo _AT('save_changes'); ?> Alt-s" accesskey="s" class="button"/> - <input type="submit" name="cancel" class="button" value="<?php echo _AT('cancel'); ?>" />
	</td></tr>
	</table>
</form>

<?php
	require(AT_INCLUDE_PATH.'footer.inc.php');
?>