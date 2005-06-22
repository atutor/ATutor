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

?>
	<div class="row">
		<label for="ctitle"><?php echo _AT('title');  ?></label><br />
		<input type="text" name="title" size="40" class="formfield" value="<?php echo ContentManager::cleanOutput($_POST['title']); ?>" id="ctitle" />
	</div>
	
	<?php
		if ($content_row['content_path']) {
			echo '	<div class="row">'._AT('packaged_in').'<br /> <a href="'.$_base_href.'tools/filemanager/index.php?pathext='.urlencode($content_row['content_path'].'/').'">'.$content_row['content_path'].'</a></div>';
		}
	?>
	<div class="row">
		<?php echo _AT('formatting'); ?><br />

		<input type="radio" name="formatting" value="0" id="text" <?php if ($_POST['formatting'] == 0) { echo 'checked="checked"'; } ?> onclick="javascript: document.form.setvisual.disabled=true;" <?php if ($_POST['setvisual'] && !$_POST['settext']) { echo 'disabled="disabled"'; } ?> />
		<label for="text"><?php echo _AT('plain_text'); ?></label>

		, <input type="radio" name="formatting" value="1" id="html" <?php if ($_POST['formatting'] == 1 || $_POST['setvisual']) { echo 'checked="checked"'; } ?> onclick="javascript: document.form.setvisual.disabled=false;"/>
		<label for="html"><?php echo _AT('html'); ?></label>

		<?php if (($_POST['setvisual'] && !$_POST['settext']) || $_GET['setvisual']) : ?>
			<input type="hidden" name="setvisual" value="<?php echo $_POST['setvisual']; ?>" />
			<input type="submit" name="settext" value="<?php echo _AT('switch_text'); ?>" />
		<?php else: ?>
			<input type="submit" name="setvisual" value="<?php echo _AT('switch_visual'); ?>" <?php if ($_POST['formatting']==0) { echo 'disabled="disabled"'; } ?> />
		<?php endif; 
		// If user has privleges to use Filemanager, display the link for the Filemanager.
		if (authenticate(AT_PRIV_FILES, true)){ ?>
			<script type="text/javascript" language="javascript">
			// <!--
				document.write(" <a onclick=\"window.open('<?php echo $_base_href; ?>tools/filemanager/index.php?popup=1','newWin1','toolbar=0,location=0,directories=0,status=0,menubar=0,scrollbars=1,resizable=1,copyhistory=0,width=640,height=490')\" style=\"cursor: pointer; text-decoration:underline;\" ><?php echo _AT('open_file_manager'); ?> </a>");
			//-->
			</script>
			<noscript>
				<a href="<?php echo $_base_href; ?>tools/filemanager/index.php"><?php echo _AT('open_file_manager'); ?></a>
			</noscript>			
		<?php } else { 
			// If user does not have privleges to use Filemanager.
			// Do nothing. (i.e. do not show link for Filemanager.
		} ?>
	</div>
	<div class="row">
		<label for="body_text"><?php echo _AT('body');  ?></label><br />
		
<?php 
// Javascript codes for the visual editor
if (($_POST['setvisual'] && !$_POST['settext']) || $_GET['setvisual']){
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
<?php }

// kludge #1548
if (trim($_POST['body_text']) == '<br />') {
	$_POST['body_text'] = '';
}

?>

		<textarea name="body_text" id="body_text" cols="" rows="20"><?php echo ContentManager::cleanOutput($_POST['body_text']); ?></textarea>	
	</div>
	<div class="row">
		<?php require(AT_INCLUDE_PATH.'html/editor_tabs/content_code_picker.inc.php'); ?>
	</div>

	<div class="row">
		<strong><?php echo _AT('or'); ?></strong> <?php echo _AT('paste_file'); ?><br />
		<input type="file" name="uploadedfile" class="formfield" size="20" /> <input type="submit" name="submit_file" value="<?php echo _AT('upload'); ?>" /><br />
		<small class="spacer">&middot;<?php echo _AT('html_only'); ?><br />
		&middot;<?php echo _AT('edit_after_upload'); ?></small>
	</div>