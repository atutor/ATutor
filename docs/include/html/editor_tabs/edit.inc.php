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
		<input type="text" name="title" size="70" class="formfield" value="<?php echo ContentManager::cleanOutput($_POST['title']); ?>" id="ctitle" />
	</div>
	
	<?php
		if ($content_row['content_path']) {
			echo '	<div class="row">'._AT('packaged_in').'<br />'.$content_row['content_path'].'</div>';
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
		<?php endif; ?>
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