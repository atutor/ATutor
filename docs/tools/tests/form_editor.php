<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2005 by Greg Gay & Joel Kronenberg        */
/* Adaptive Technology Resource Centre / University of Toronto  */
/* http://atutor.ca												*/
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.				*/
/****************************************************************/
// $Id: form_editor.php 2616 2004-12-01 17:49:41Z shozubq $

$page = 'form_editor';
define('AT_INCLUDE_PATH', '../../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');

authenticate(AT_PRIV_TEST_CREATE);

$area = $_GET['area'];

$onload = 'onload="init();"';


global $myLang;
global $page;
global $savant;
global $errors, $onload;
global $_base_href;
global $_user_location;
global $_base_path;
global $cid;
global $contentManager;
global $_section;
global $addslashes;


if (defined('AT_FORCE_GET_FILE') && AT_FORCE_GET_FILE) {
	$_tmp_base_href = $_base_href . 'get.php/';
} else {
	$_tmp_base_href = 'content/' . $_SESSION['course_id'] . '/';
}

	$visuallang["zh"] = "b5";
	$visuallang["cs"] = "cz";
	$visuallang["da"] = "da";
	$visuallang["de"] = "de";
//	$visuallang[""] = "ee";     //no clue what this language is
	$visuallang["el"] = "el";
	$visuallang["en"] = "en";
	$visuallang["es"] = "es";
	$visuallang["fi"] = "fi";
	$visuallang["fr"] = "fr";
	$visuallang["gb"] = "gb";
	$visuallang["he"] = "he";
	$visuallang["hu"] = "hu";
	$visuallang["it"] = "it";
	$visuallang["ja"] = "ja-euc";
//	$visuallang[""] = "ja-jis";    //language not provided by ATutor
//	$visuallang[""] = "ji-sjis";   //language not provided by ATutor
//	$visuallang[""] = "ja-utf8";   //language not provided by ATutor
	$visuallang["lt"] = "lt";
	$visuallang["lv"] = "lv";
	$visuallang[""] = "nb";
	$visuallang["nl"] = "nl";
	$visuallang["nos"] = "no";
	$visuallang["pl"] = "pl";
	$visuallang["ptb"] = "pt_br";
	$visuallang["ro"] = "ro";
	$visuallang["ru"] = "ru";
	$visuallang["sv"] = "se";
//	$visuallang[""] = "si";     //no clue what this language is
	$visuallang["vi"] = "vn";

	if ($visuallang[$_SESSION['lang']] != "") {
		$uselang = $visuallang[$_SESSION['lang']];
	} else {
		$uselang = "en";
	}

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html lang="en">
<head>
	<title>ATutor Form Editor</title>

	<link rel="stylesheet" href="<?php echo $_base_path.'themes/'.$_SESSION['prefs']['PREF_THEME']; ?>/styles.css" type="text/css" />
	<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />
		<base href="<?php echo $_base_href; ?>" />

</head>

<body <?php echo $onload; ?> >

<script type="text/javascript"><!--
  _editor_url = "<?php echo $_base_path; ?>jscripts/htmlarea/";
  _editor_lang = "<?php echo $uselang; ?>";

function init() {
	document.form.body_text.value = window.opener.document.getElementById("<?php echo $area; ?>").value;
	initEditor();
}

//--></script>

<script type="text/javascript" src="<?php echo $_base_path; ?>jscripts/htmlarea/htmlarea.js"></script>

<script type="text/javascript" defer="defer"><!--
	var editor = null;

	function initEditor() {
		editor = new HTMLArea("body_text");
		var config = editor.config; // this is the default configuration

		// to keep relative links relative:
		config.relativeURL = true;

		// to change the base href of the editor:
		config.baseURL = "<?php echo $_tmp_base_href; ?>";

		// Choose buttons/functionality [refer to htmlarea.js for instructions]
		config.toolbar = [
			['formatblock', 'space', "bold", "italic", "underline", "separator", "strikethrough", "subscript", "superscript", "separator", "copy", "cut", "paste", "separator", "lefttoright", "righttoleft", "separator", "justifyleft", "justifycenter", "justifyright", "justifyfull"],
			["insertorderedlist", "insertunorderedlist", "outdent", "indent", "separator", "inserthorizontalrule", "createlink", "insertimage", "inserttable", "htmlmode", "separator", "undo", "redo", "separator", "popupeditor", "separator", "about", "space", "space", "separator", "space", "space"]
			];

		editor.generate();	
	}

	function mycode(editor, id) {
		editor.surroundHTML('[code]', '[/code]');
	}

//-->
</script>
<div align="right"><a href="javascript:window.close()"><?php echo _AT('close_window'); ?></a></div>
<form name="form">
	<table cellspacing="1" cellpadding="0" width="99%" border="0" class="bodyline" align="center" summary="">
		<tr>
			<th class="cyan">
				<?php 
					if (preg_match ('/choice/', $area))
						echo _AT('choice') . ' ' . (intval(substr($area ,-1 , 1))+1);
					else 
						echo _AT($area);
				?>
			</th>
		</tr>
		<tr>
			<td colspan="2" valign="top" align="left" class="row1">
				<table cellspacing="0" cellpadding="0" width="98%" border="0" summary="">
				<tr>
					<td class="row1" align="left">	
						<textarea name="body_text" id="body_text" rows="8" class="formfield" style="width: 99%;"></textarea>
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
	var content = editor.getInnerHTML();
	window.opener.document.getElementById(field).value = content;
}
-->
</script>


<iframe src="<?php echo $_base_path; ?>tools/filemanager/index.php?framed=1" name="filemanager" width="98%" height="480">
</iframe>

</body>
</html>