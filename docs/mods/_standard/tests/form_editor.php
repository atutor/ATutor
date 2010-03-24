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
// $Id: form_editor.php 8138 2008-10-30 19:21:08Z hwong $

$page = 'form_editor';
define('AT_INCLUDE_PATH', '../../../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');

require(AT_INCLUDE_PATH.'lib/tinymce.inc.php');

authenticate(AT_PRIV_TESTS);

$area = $_GET['area'];
$number = null;

$parts = explode('_', $area, 2);
if (is_numeric($parts[1])) {
	$parts[1]++;
	$title = _AT($parts[0]) . ' ' . $parts[1];
} else {
	$title = _AT($area);
}

$onload = 'onload="init();"';


global $myLang;
global $page;
global $savant;
global $errors, $onload;
global $_user_location;
global $_base_path;
global $cid;
global $contentManager;
global $_section;
global $addslashes;


if (defined('AT_FORCE_GET_FILE') && AT_FORCE_GET_FILE) {
	$_tmp_base_href = AT_BASE_HREF . 'get.php/';
} else {
	$_tmp_base_href = 'content/' . $_SESSION['course_id'] . '/';
}

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html lang="en">
<head>
	<title><?php echo _AT('form_editor'); ?></title>

	<link rel="stylesheet" href="<?php echo $_base_path.'themes/'.$_SESSION['prefs']['PREF_THEME']; ?>/styles.css" type="text/css" />
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<base href="<?php echo AT_BASE_HREF; ?>" />
	<script src="<?php echo $_base_path; ?>jscripts/infusion/InfusionAll.js" type="text/javascript"></script>

</head>

<body <?php echo $onload; ?> >

<script type="text/javascript"><!--
function init() {
	tinyMCE.activeEditor.setContent(window.opener.document.getElementById("<?php echo $area; ?>"). value);
}
//--></script>

<?php load_editor(); ?>


<div align="right"><br /><a href="javascript:window.close()"><?php echo _AT('close_window'); ?></a></div>
<form name="form">
	<table cellspacing="1" cellpadding="0" width="99%" border="0" class="bodyline" align="center" summary="">
		<tr>
			<th class="cyan"><?php echo $title; ?></th>
		</tr>
		<tr>
			<td colspan="2" valign="top" align="left" class="row1">
				<table cellspacing="0" cellpadding="0" width="98%" border="0" summary="">
				<tr>
					<td class="row1" align="left">	
						<textarea name="body_text" id="body_text" rows="15" class="formfield" style="width: 99%;"></textarea>
					</td>
				</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td height="1" class="row2" colspan="2"></td>
		</tr>
		<tr>
			<td colspan="2" valign="top" align="center" class="row1">
				<input type="button" name="paste"  value="<?php echo _AT('paste');  ?>" class="button" onclick="javascript:insertTo('<?php echo $area; ?>');" />
			</td>
		</tr>
	</table>
</form>
<br />


<script type="text/javascript">
<!--
function insertTo(field) {
		if (window.opener.document.getElementById(field)) {
			//use the following if we want to apply absolute path.	-harris.
			//window.opener.document.getElementById(field).value = tinyMCE.activeEditor.getContent({format : 'raw'});
			window.opener.document.getElementById(field).value = tinyMCE.activeEditor.getContent();
		}
}
-->
</script>


<iframe src="<?php echo $_base_path; ?>mods/_core/file_manager/index.php?framed=1<?php echo SEP; ?>popup=1" name="filemanager" width="98%" height="480">
</iframe>

</body>
</html>