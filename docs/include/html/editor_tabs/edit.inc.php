<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2008 by Greg Gay & Joel Kronenberg        */
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
		<div class="required" title="<?php echo _AT('required_field'); ?>">*</div><label for="ctitle"><?php echo _AT('title');  ?></label><br />
		<input type="text" name="title" id="ctitle" size="70" class="formfield" value="<?php echo ContentManager::cleanOutput($_POST['title']); ?>" />
	</div>
	
	<?php
		if ($content_row['content_path']) {
			echo '	<div class="row">'._AT('packaged_in').'<br />'.$content_row['content_path'].'</div>';
		}
	?>

	<div class="row">
		<label for="edithead"><?php echo _AT('customized_head');  ?></label>
		<input type="button" name="edithead" id="edithead" value="<?php echo _AT('edit'); ?>" onclick="switch_head_editor()" class="button"/><br />
		<small>&middot; <?php echo _AT('customized_head_note'); ?></small>
	</div>

<?php 
if (trim($_POST['head']) == '<br />') {
	$_POST['head'] = '';
}
if ($do_check) {
	$_POST['head'] = $stripslashes($_POST['head']);
}
?>

	<div class="row">
		<div id="headDiv" style="display:none">
			<input type="checkbox" name="use_customized_head" id="use_customized_head" value="1" <?php if ($_POST['use_customized_head']) { echo 'checked="checked"'; } ?> />
			<label for="use_customized_head"><?php echo _AT('use_customized_head'); ?></label><br />
			<label for="head"><?php echo _AT('customized_head'); ?></label><br /><textarea name="head" id="head" cols="" rows="10"><?php echo htmlspecialchars($_POST['head']); ?></textarea>	
		</div>
	</div>

	<div class="row">
		<?php echo _AT('formatting'); ?><br />

		<input type="radio" name="formatting" value="0" id="text" <?php if ($_POST['formatting'] == 0) { echo 'checked="checked"'; } ?> onclick="javascript: document.form.setvisualbutton.disabled=true; switch_body_weblink();" />
		<label for="text"><?php echo _AT('plain_text'); ?></label>

		<input type="radio" name="formatting" value="1" id="html" <?php if ($_POST['formatting'] == 1 || $_POST['setvisual']) { echo 'checked="checked"'; } ?> onclick="javascript: document.form.setvisualbutton.disabled=false; switch_body_weblink();"/>

		<label for="html"><?php echo _AT('html'); ?></label>

		<input type="hidden" name="displayhead" value="<?php if ($_POST['displayhead']==1 || $_REQUEST['displayhead']==1 || $_GET['displayhead']==1) echo '1'; else echo '0'; ?>" />
		<input type="hidden" name="setvisual" value="<?php if ($_POST['setvisual']==1 || $_REQUEST['setvisual']==1 || $_GET['setvisual']==1) echo '1'; else echo '0'; ?>" />
		<input type="hidden" name="settext" value="<?php if ($_POST['settext']==1 || $_REQUEST['settext']==1 || $_GET['settext']==1) echo '1'; else echo '0'; ?>" />
		<input type="button" name="setvisualbutton" value="<?php echo _AT('switch_visual'); ?>" onclick="switch_body_editor()" class="button" />
		
		<input type="radio" name="formatting" value="2" id="weblink" <?php if ($_POST['formatting'] == 2) { echo 'checked="checked"'; } ?> onclick="javascript: document.form.setvisualbutton.disabled=true; switch_body_weblink();"/>
		<label for="weblink"><?php echo _AT('weblink'); ?></label>


		<script type="text/javascript" language="javascript">
		//<!--
			document.write(" <a href=\"#\" onclick=\"window.open('<?php echo AT_BASE_HREF; ?>tools/filemanager/index.php?framed=1<?php echo SEP; ?>popup=1<?php echo SEP; ?>cp=<?php echo $content_row['content_path']; ?>','newWin1','menubar=0,scrollbars=1,resizable=1,width=640,height=490'); return false;\"><?php echo _AT('open_file_manager'); ?> </a>");
		//-->
		</script>
		<noscript>
			<a href="<?php echo AT_BASE_HREF; ?>tools/filemanager/index.php?framed=1"><?php echo _AT('open_file_manager'); ?></a>
		</noscript>			
	</div>
	<div class="row">
		<label for="body_text"><?php echo _AT('body');  ?></label><br />

<?php 

// kludge #1548
if (trim($_POST['body_text']) == '<br />') {
	$_POST['body_text'] = '';
}
if ($do_check) {
	$_POST['body_text'] = $stripslashes($_POST['body_text']);
}

?>
		<textarea name="body_text" id="body_text" cols="" rows="20"><?php echo htmlspecialchars($_POST['body_text']); ?></textarea>	
		<input name="weblink_text" id="weblink_text" value="http://" style="display:none; width:60%;"/>
	</div>
	
	<div class="row">
		<?php require(AT_INCLUDE_PATH.'html/editor_tabs/content_code_picker.inc.php'); ?>
	</div>

	<div class="row">
		<strong><?php echo _AT('or'); ?></strong> <label for="uploadedfile"><?php echo _AT('paste_file'); ?></label><br />
		<input type="file" name="uploadedfile_paste" id="uploadedfile" class="formfield" size="20" /> <input type="submit" name="submit_file" value="<?php echo _AT('upload'); ?>"  class="button" /><br />
		<small class="spacer">&middot;<?php echo _AT('html_only'); ?><br />
		&middot;<?php echo _AT('edit_after_upload'); ?></small>
	</div>

	<script type="text/javascript" language="javascript">
	//<!--
	function on_load()
	{
		if (document.getElementById("text").checked)
			document.form.setvisualbutton.disabled = true;
			
		if (document.form.displayhead.value==1)
		{
			document.getElementById("headDiv").style.display = '';
			document.form.edithead.value = "<?php echo _AT('hide'); ?>"
		}
			
		if (document.form.setvisual.value==1)
		{
			tinyMCE.execCommand('mceAddControl', false, 'body_text');
			document.form.formatting[0].disabled = "disabled";
			document.form.setvisualbutton.value = "<?php echo _AT('switch_text'); ?>";
		}
		else
		{
			document.form.setvisualbutton.value = "<?php echo _AT('switch_visual'); ?>";
		}
	}
	
	// show/hide "cusomized head" editor
	function switch_head_editor()
	{
		if (document.form.edithead.value=="<?php echo _AT('edit'); ?>")
		{
			document.form.edithead.value = "<?php echo _AT('hide'); ?>"
			document.getElementById("headDiv").style.display = "";
			document.form.displayhead.value=1;
		}
		else
		{
			document.form.edithead.value = "<?php echo _AT('edit'); ?>"
			document.getElementById("headDiv").style.display = "none";
			document.form.displayhead.value=0;
		}
	}
	
	// switch between text, visual editor for "body text"
	function switch_body_editor()
	{
		if (document.form.setvisualbutton.value=="<?php echo _AT('switch_visual'); ?>")
		{
			tinyMCE.execCommand('mceAddControl', false, 'body_text');
			document.form.setvisual.value=1;
			document.form.settext.value=0;
			document.form.formatting[0].disabled = "disabled";
			document.form.setvisualbutton.value = "<?php echo _AT('switch_text'); ?>";
		}
		else
		{
			tinyMCE.execCommand('mceRemoveControl', false, 'body_text');
			document.form.setvisual.value=0;
			document.form.settext.value=1;
			document.form.formatting[0].disabled = "";
			document.form.setvisualbutton.value = "<?php echo _AT('switch_visual'); ?>";
		}
	}

	//switch between weblinks.
	function switch_body_weblink(){
		alert(document.form.formatting.value);
		if (document.form.formatting.value==2){
			document.form.body_text.style.display = "none";
			document.form.weblink_text.style.display = "inline";
		} else {
			document.form.body_text.style.display = "inline";
			document.form.weblink_text.style.display = "none";
		}
	}
	//-->
	</script>
