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
$_section[0][0] = _AT('tools');
$_section[0][1] = 'tools/';
$_section[1][0] = _AT('custom_header');
if ($_POST['cancel']) {
		if ($_POST['pid'] != 0) {
			Header('Location: ../index.php?cid='.$_POST['pid'].';f='.urlencode_feedback(AT_FEEDBACK_CANCELLED));
			exit;
		}
		Header('Location: ../tools/index.php?f='.urlencode_feedback(AT_FEEDBACK_CANCELLED));
		exit;
	}

$filename = AT_INCLUDE_PATH.'../content/'.$_SESSION[course_id].'/temp_header_'.$_SESSION[course_id].'.html';
if($_POST['submit_file']=="Upload" && $_FILES['uploadedfile']['name']=='')	{
	$errors[]=AT_ERROR_FILE_NOT_SELECTED;

}else if($_FILES['uploadedfile']['name']!=''){
	$path_parts = pathinfo($_FILES['uploadedfile']['name']);
	$ext = strtolower($path_parts['extension']);
	if (in_array($ext, array("html", "htm" , "txt"))) {
	} else {
		$errors[]=AT_ERROR_UNSUPPORTED_FILE;
	}

}

if ($_POST['submit_file']) {

	if ($_FILES['uploadedfile']['name'] && (($_FILES['uploadedfile']['type'] == 'text/plain') || ($_FILES['uploadedfile']['type'] == 'text/html')) )	{
		$this_head = file_get_contents($_FILES['uploadedfile']['tmp_name']);
		$path_parts = pathinfo($_FILES['uploadedfile']['name']);
		$ext = strtolower($path_parts['extension']);
		if (in_array($ext, array('html', 'htm'))) {

			/* strip everything before <body> */
			$start_pos	= strpos(strtolower($this_head), '<body');
			if ($start_pos !== false) {

				$start_pos	+= strlen('<body');
				$end_pos	= strpos(strtolower($this_head), '>', $start_pos);
				$end_pos	+= strlen('>');

				$this_head = substr($this_head, $end_pos);
			}

			/* strip everything after </body> */
			$end_pos= strpos(strtolower($this_head), '</body>');
			if ($end_pos !== false) {
				$this_head = trim(substr($this_head, 0, $end_pos));
			}
			$this_head = addslashes($this_head);
			$head_sql ="UPDATE ".TABLE_PREFIX."courses SET header='$this_head' where course_id='$_SESSION[course_id]'";
			$result = mysql_query($head_sql, $db);

			if($result){
				$feedback[]=AT_FEEDBACK_HEADER_PASTED;
			}
		}
	}
}elseif($_POST['update']){
	$head_sql ="UPDATE ".TABLE_PREFIX."courses SET header='".$_POST['header']."', footer='".$_POST['footer']."', copyright='".$_POST['copyright']."' where course_id='$_SESSION[course_id]'";
	$result = mysql_query($head_sql, $db);
	$feedback[]=AT_FEEDBACK_HEADER_UPLOADED;

}
if($_GET['copy']==1){
		$default_head = AT_INCLUDE_PATH.'../templates/wrap_head_dflt.html';
		$default_foot = AT_INCLUDE_PATH.'../templates/wrap_foot_dflt.html';
		$fp = fopen ($default_head, r);
		$ft = fopen ($default_foot, r);
		$this_head=fread($fp, filesize($default_head));
		$this_foot=fread($ft, filesize($default_foot));

		fclose($fp);
		fclose($ft);
		$feedback[]=AT_FEEDBACK_DEFAULT_WRAP_TEMPLATE;
		$warning[]=AT_WARNING_SAVE_TEMPLATE;
}
if($_GET['copy']==2){
		$default_head = AT_INCLUDE_PATH.'../templates/wrap_head_right.html';
		$default_foot = AT_INCLUDE_PATH.'../templates/wrap_foot_right.html';
		$fp = fopen ($default_head, r);
		$ft = fopen ($default_foot, r);
		$this_head=fread($fp, filesize($default_head));
		$this_foot=fread($ft, filesize($default_foot));

		fclose($fp);
		fclose($ft);
		$feedback[]=AT_FEEDBACK_DEFAULT_WRAP_TEMPLATE;
		$warning[]=AT_WARNING_SAVE_TEMPLATE;
}

require(AT_INCLUDE_PATH.'header.inc.php');

$warning[]=AT_WARNING_SAVE_YOUR_WORK;
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
		echo _AT('header_editor');
	}
echo '</h3>';

$help[]=AT_HELP_CREATE_HEADER;
print_help($help);
?>
<p align="center"><small>(<a href="frame.php?p=<?php echo urlencode($_my_uri); ?>"><?php echo _AT('open_frame');?></a>) <br />
[ <a href="<?php echo $PHP_SELF ?>?copy=1"><?php  echo _AT('load_left'); ?></a> |
<a href="<?php echo $PHP_SELF ?>?copy=2"><?php  echo _AT('load_right'); ?></a> ]</small></p>

<form action="<?php echo $PHP_SELF; ?>" method="post" name="form" enctype="multipart/form-data">
<input type="hidden" name="MAX_FILE_SIZE" value="204000" />
<table cellspacing="1" cellpadding="0" border="0" class="bodyline" summary="" align="center">
	<tr>
	<td colspan="2" class="cat"><?php print_popup_help(AT_HELP_CUSTOM_HEADER); ?><label for="header"><?php  echo _AT('course_header'); ?></label></td>
	</tr>
	<tr>
	<td align="right" class="row1" valign="top"><b><?php  echo _AT('paste_file'); ?>:</b></td>
	<td class="row1"><input type="file" name="uploadedfile" class="formfield" size="20" /> <input type="submit" name="submit_file" value="<?php echo _AT('upload'); ?>" class="button" /><br />
	<small class="spacer"><?php  echo _AT('html_only'); ?><br />
	<?php  echo _AT('edit_after_upload'); ?></small>
	</td>
	</tr>
	<tr><td height="1" class="row2" colspan="2"></td></tr>
	<tr><td colspan="2" align="center" class="row1">
	<textarea name="header" rows="15" cols="65" class="formfield" id="header"><?php
	if($_GET['copy']==1){
		echo $this_head;
	} elseif($_GET['copy']==2){
		echo $this_head;
	} else{
		$gethead_sql="select header from ".TABLE_PREFIX."courses where course_id='$_SESSION[course_id]'";
		$result=mysql_query($gethead_sql, $db);
		while($row=mysql_fetch_row($result)){
			$show_edit_head= $row[0];

		}
		if(strlen($show_edit_head)>0){
			echo $show_edit_head;
		}else{
			echo '<!-- '._AT('default').' -->'.$_SESSION['course_title'];
		}
	}

	?></textarea>
	</td>
	</tr>
	<tr><td height="1" class="row2" colspan="2"></td></tr>
	<tr><td colspan="2" class="cat"><label for="footer"><?php echo _AT('course_footer'); ?></label></td>
	<td height="1" class="row2" colspan="2"></td></tr>
	<tr><td colspan="2" align="center" class="row1">
	
	<textarea name="footer" rows="5" cols="65" class="formfield" id="footer"><?php
	if($_GET['copy']==1){
		echo $this_foot;
	} elseif($_GET['copy']==2){
		echo $this_foot;
	} else{
		$getfoot_sql="select footer from ".TABLE_PREFIX."courses where course_id='$_SESSION[course_id]'";
		$result1=mysql_query($getfoot_sql, $db);
		while($row=mysql_fetch_row($result1)){
			$show_edit_foot= $row[0];

		}
		if(strlen($show_edit_foot)>0){
			echo $show_edit_foot;
		}else{
			echo '<!-- '._AT('course_footer').' -->';
		}
	}
	?></textarea>
	</td>
	</tr>

	<tr><td height="1" class="row2" colspan="2"></td></tr>
	<tr><td colspan="2" class="cat"><label for="copyright"><?php echo _AT('course_copyright'); ?></label></td></tr>
	<tr><td height="1" class="row2" colspan="2"></td></tr>
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
	<input type="submit" value="<?php echo _AT('save_header'); ?> Alt-s" accesskey="s" class="button"/> - <input type="submit" name="cancel" class="button" value="<?php echo _AT('cancel'); ?>" />
	</td></tr>
	</table>
</form>

<?php
	require(AT_INCLUDE_PATH.'footer.inc.php');
?>