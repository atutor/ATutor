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
// $Id$
if (!defined('AT_INCLUDE_PATH')) { exit; }

// Javascript codes for the visual editor
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

<script type="text/javascript"><!--
  _editor_url = "<?php echo $_base_path; ?>jscripts/htmlarea/";
  _editor_lang = "<?php echo $uselang; ?>";
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

		// register custom buttons
		config.registerButton("my-glossary", "Add term", _editor_url+"images/myglossary.gif", false, myglossary);

		// Choose buttons/functionality [refer to htmlarea.js for instructions]
		config.toolbar = [
			['formatblock', 'space', "bold", "italic", "underline", "separator", "strikethrough", "subscript", "superscript", "separator", "copy", "cut", "paste", "separator", "lefttoright", "righttoleft", "separator", "justifyleft", "justifycenter", "justifyright", "justifyfull"],
			["insertorderedlist", "insertunorderedlist", "outdent", "indent", "separator", "inserthorizontalrule", "createlink", "insertimage", "inserttable", "htmlmode", "separator", "undo", "redo", "separator", "popupeditor", "separator", "about", "space", "space", "separator", "separator", "space", "space", "my-glossary"]
			];

		editor.generate();
	}

	function myglossary(editor, id) {
		editor.surroundHTML('[?]', '[/?]');
	} 

	function mycode(editor, id) {
		editor.surroundHTML('[code]', '[/code]');
	} 

//--></script>
