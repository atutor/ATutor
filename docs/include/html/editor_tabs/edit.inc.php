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
if (!defined('AT_INCLUDE_PATH')) { exit; }



?>
		<tr>
			<td align="right" class="row1" valign="top"><?php
				print_popup_help('PASTE_FILE');
				?><strong><?php echo _AT('paste_file'); ?>:</strong></td>
			<td class="row1" valign="top"><input type="file" name="uploadedfile" class="formfield" size="20" /> <input type="submit" name="submit_file" value="<?php echo _AT('upload'); ?>" class="button" /><br />
				<small class="spacer">&middot;<?php echo _AT('html_only'); ?><br />
				&middot;<?php echo _AT('edit_after_upload'); ?></small>
			</td>
		</tr>
		<tr><td height="1" class="row2" colspan="2"></td></tr>
		<tr>
			<td align="center" class="row1" colspan="2"><strong><?php echo _AT('or'); ?></strong></td>
		</tr>
		<tr><td height="1" class="row2" colspan="2"></td></tr>
		<tr>
			<td class="row1" colspan="2"><br /><strong><label for="ctitle"><?php echo _AT('title');  ?>:</label></strong>
			<input type="text" name="title" size="40" class="formfield" value="<?php echo ContentManager::cleanOutput($_POST['title']); ?>" id="ctitle" /></td>
		</tr>
		<tr><td height="1" class="row2" colspan="2"></td></tr>
		<?php
			if ($content_row['content_path']) {
				echo '<tr>';
				echo '<td colspan="2" class="row1"><strong>'._AT('packaged_in').': <a href="'.$_base_href.'tools/file_manager.php?pathext='.urlencode($content_row['content_path'].'/').'">'.$content_row['content_path'].'</a></strong></td>';
				echo '</tr>';
				echo '<tr><td height="1" class="row2" colspan="2"></td></tr>';
			}
		?>
		<tr><td colspan="2" valign="top" align="left" class="row1">
			<?php print_popup_help('FORMATTING'); ?>
			<b><?php echo _AT('formatting'); ?>:</b>

			<input type="radio" name="formatting" value="0" id="text" <?php if ($_POST['formatting'] == 0) { echo 'checked="checked"'; } ?> onclick="javascript: document.form.setvisual.disabled=true;" <?php if ($_POST['setvisual'] && !$_POST['settext']) { echo 'disabled="disabled"'; } ?> />
			<label for="text"><?php echo _AT('plain_text'); ?></label>

			, <input type="radio" name="formatting" value="1" id="html" <?php if ($_POST['formatting'] == 1 || $_POST['setvisual']) { echo 'checked="checked"'; } ?> onclick="javascript: document.form.setvisual.disabled=false;"/>
			<label for="html"><?php echo _AT('html'); ?></label>


<?php   //Button for enabling/disabling visual editor
if (($_POST['setvisual'] && !$_POST['settext']) || $_GET['setvisual']){
	echo '<input type="hidden" name="setvisual" value="'.$_POST['setvisual'].'" />';
	echo '<input type="submit" name="settext" value="'._AT('switch_text').'" class="button" />';
} else {
	echo '<input type="submit" name="setvisual" value="'._AT('switch_visual').'" class="button" ';
	if ($_POST['formatting']==0) { echo 'disabled="disabled"'; }
	echo '/>';
}
?>

		</td></tr>


		<tr><td height="1" class="row2" colspan="2"></td></tr>

		<tr>
			<td colspan="2" valign="top" align="left" class="row1">
			<table cellspacing="0" cellpadding="0" width="100%" border="0" summary="">
			<tr><td class="row1"><?php print_popup_help('BODY'); ?><strong><label for="body_text"><?php echo _AT('body');  ?>:</label></strong>

			<br />

		
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
<?php } ?>

			<textarea  name="body_text" id="body_text" rows="25" class="formfield" style="width: 100%;"><?php echo ContentManager::cleanOutput($_POST['body_text']); ?></textarea>
			</td></tr></table>
			</td>
		</tr>
		<tr><td height="1" class="row2" colspan="2"></td></tr>
		<tr>
			<td class="row1" colspan="2"><?php require(AT_INCLUDE_PATH.'html/editor_tabs/content_code_picker.inc.php'); ?></td>
		</tr>
