<?php
/************************************************************************/
/* ATutor																*/
/************************************************************************/
/* Copyright (c) 2002-2004 by Greg Gay, Joel Kronenberg & Heidi Hazelton*/
/* Adaptive Technology Resource Centre / University of Toronto			*/
/* http://atutor.ca														*/
/*																		*/
/* This program is free software. You can redistribute it and/or		*/
/* modify it under the terms of the GNU General Public License			*/
/* as published by the Free Software Foundation.						*/
/************************************************************************/
// $Id: banner.php,v 1.3 2004/04/19 18:14:09 heidi Exp $

define('AT_INCLUDE_PATH', '../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');
$_section[0][0] =  _AT('tools');
$_section[0][1] = 'tools/';
$_section[1][0] =  _AT('course_banner');

$infos[]=AT_INFOS_HEADFOOT_DEPRECATED;

$sql="SELECT header, footer FROM ".TABLE_PREFIX."courses WHERE course_id='$_SESSION[course_id]'";
$result=mysql_query($sql, $db);
if ($row = mysql_fetch_assoc($result)) {
	if ($row['header'] != '') {			
		$infos[]=array(AT_INFOS_HEADFOOT_DEPRECATED_DL_H, $_SERVER['PHP_SELF'].'?dl=header');

		if (isset($_GET['dl']) && $_GET['dl']=='header') {
			header('Content-Type: '.$mime[$ext]);
			header('Content-Type: application/force-download');
			header('Content-transfer-encoding: binary'); 
			header('Content-Length: '.strlen($row['header']));
			header('Content-Disposition: attachment; filename="header.html"');
			
			echo $row['header'];
			exit;
		}
	}
	if ($row['footer'] != '') {		
		$infos[]=array(AT_INFOS_HEADFOOT_DEPRECATED_DL_F, $_SERVER['PHP_SELF'].'?dl=footer');
		if (isset($_GET['dl']) && $_GET['dl']=='footer') {
			header('Content-Type: '.$mime[$ext]);
			header('Content-Type: application/force-download');
			header('Content-transfer-encoding: binary'); 
			header('Content-Length: '.strlen($row['footer']));
			header('Content-Disposition: attachment; filename="footer.html"');
			
			echo $row['footer'];
			exit;
		}
	}
}

//get vars from db
$sql	= "SELECT banner_text, banner_styles FROM ".TABLE_PREFIX."courses WHERE course_id=$_SESSION[course_id] ";
$result = mysql_query($sql, $db);
if ($row = mysql_fetch_assoc($result)) {
	$banner_text_html	= $row['banner_text'];
	$banner_styles		= unserialize($row['banner_styles']);

	if ($banner_text_html == '') {
		$default_checked = 'checked = "checked"';
		$custom_checked	= '';
		$banner_text_html = '<h2>'._AT('course_name').'</h2>';
	} else {
		$default_checked = '';
		$custom_checked = 'checked = "checked"';
	}
}

if ($_POST['update']){
	//make an array of the stylesheet items
	$banner_styles = array();
	$banner_styles['bg_colour']		= $_POST['bg_colour'];
	$banner_styles['bg_img']		= $_POST['bg_img'];
	$banner_styles['font_colour']	= $_POST['font_colour'];

	//save array to db
	if ($_POST['banner_text'] == 'custom' && $_POST['banner_text_html'] != '') {
		$banner_text = $_POST['banner_text_html'];
	} else {
		$banner_text = '';
	}

	$sql ="UPDATE ".TABLE_PREFIX."courses SET banner_styles='".serialize($banner_styles)."', banner_text='".$banner_text."' WHERE course_id='$_SESSION[course_id]'";
	$result = mysql_query($sql, $db);
	$feedback[]=AT_FEEDBACK_HEADER_UPLOADED;
}

require(AT_INCLUDE_PATH.'header.inc.php');

echo '<h2>';
	if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 2) {
		echo '<a href="tools/" class="hide"><img src="images/icons/default/square-large-tools.gif"  class="menuimageh2" border="0" vspace="2" width="42" height="40" alt="" /></a>';
	}
	if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 1) {
		echo ' <a href="tools/" class="hide">'._AT('tools').'</a>';
	}
echo '</h2>';

echo '<h3>';
	if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 2) {
		echo '&nbsp;<img src="images/icons/default/css-editor-large.gif"  class="menuimageh3" width="42" height="38" alt="" /> ';
	}
	if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 1) {
		echo _AT('course_banner');
	}
echo '</h3>';

//$warnings[]=AT_WARNING_SAVE_YOUR_WORK;
print_feedback($feedback);
print_errors($errors);
print_help($help);
print_infos($infos);
//print_warnings($warnings);  

?>
<br />
<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" name="form" enctype="multipart/form-data">
<input type="hidden" name="MAX_FILE_SIZE" value="204000" />
<table cellspacing="1" cellpadding="0" border="0" class="bodyline" summary="" align="center">
	<tr>
		<td colspan="2" class="cat"><label for="styles"><?php echo _AT('course_styles'); ?></label></td>
	</tr>
	<tr>
		<td class="row1">Text: </td>
		<td class="row1"><input type="radio" name="banner_text" value="default" <?php echo $default_checked; ?> id="default" onclick="disableCustom();" /> <label for="default">Default</label><br />
		<input type="radio" name="banner_text" value="custom" <?php echo $custom_checked; ?> id="custom" onclick="enableCustom();" /><label for="custom">Custom:</label>
		<p align="center"> <textarea name="banner_text_html" rows="5" cols="50" class="formfield" id="b_text" 
		<?php 
			if ( $custom_checked == '') { 
				echo 'disabled="disabled"'; 				
			}
		?>><?php echo $banner_text_html; ?></textarea></p>
		</td>
	</tr>
	<tr>
		<td class="row1">Background Colour: </td>
		<td class="row1"><input type="text" name="banner_styles['bg_colour']" value="<?php echo $banner_styles['bg_colour']; ?>" /></td>
	</tr>
	<tr>
		<td class="row1">Background Image URL: </td>
		<td class="row1"><input type="text" name="banner_styles['bg_img']" value="<?php echo $banner_styles['bg_img']; ?>" /></td>
	</tr>
	<tr>
		<td class="row1">Font Colour: </td>
		<td class="row1"><input type="text" name="banner_styles['font_colour']" value="<?php echo $banner_styles['font_colour']; ?>" /></td>
	</tr>
	<tr>
		<td align="center" class="row1" colspan="2">		
		<input type="hidden" name="update" value="1" /><br />
		<input type="submit" name="submit" value="<?php echo _AT('save_styles'); ?> Alt-s" accesskey="s" class="button"/>
		</td>
	</tr>

</table>
</form>
 
<?php
require(AT_INCLUDE_PATH.'footer.inc.php');
?>
<script language="javascript" type="text/javascript">
function disableCustom() { document.form.banner_text_html.disabled = true; }
function enableCustom()  { document.form.banner_text_html.disabled = false; }
</script>