<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2009										*/
/* Inclusive Design Institute                                   */
/* http://atutor.ca												*/
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.				*/
/****************************************************************/
// $Id: edit.inc.php 10555 2010-12-17 20:10:32Z greg $

if (!defined('AT_INCLUDE_PATH')) { exit; }
?>
    <script type="text/javascript" language="javascript">
    //<!--
        ATutor.mods.editor.editor_pref = "<?php if(isset($_SESSION['prefs']['PREF_CONTENT_EDITOR'])) echo $_SESSION['prefs']['PREF_CONTENT_EDITOR'] ?>";
    //-->
    </script>
    <input type="hidden" name="displayhead" id="displayhead" value="<?php if ($_POST['displayhead']==1 || $_REQUEST['displayhead']==1 || $_GET['displayhead']==1) echo '1'; else echo '0'; ?>" />
    <input type="hidden" name="displaypaste" id="displaypaste" value="<?php if ($_POST['displaypaste']==1 || $_REQUEST['displaypaste']==1 || $_GET['displaypaste']==1) echo '1'; else echo '0'; ?>" />
    <input type="hidden" name="complexeditor" id="complexeditor" value="<?php if ($_POST['complexeditor']==1 || $_REQUEST['complexeditor']==1 || $_GET['complexeditor']==1) echo '1'; else echo '0'; ?>" />

	<div class="row">
	    <span>
		<span class="required" title="<?php echo _AT('required_field'); ?>">*</span><label for="ctitle"><strong><?php echo _AT('title');  ?></strong></label>
		<input type="text" name="title" id="ctitle" size="60" class="formfield" value="<?php echo ContentManager::cleanOutput($_POST['title']); ?>" />
        </span>
       <br /> <span class="nowrap">
        <label for="formatting_radios"><span class="required" title="<?php echo _AT('required_field'); ?>">*</span><strong><?php echo _AT('formatting'); ?></strong></label>
        <span id="formatting_radios">
            <input type="radio" name="formatting" value="0" id="text" <?php if ($_POST['formatting'] == 0) { echo 'checked="checked"'; } ?> />
            <label for="text"><?php echo _AT('plain_text'); ?></label>

            <input type="radio" name="formatting" value="1" id="html" <?php if ($_POST['formatting'] == 1) { echo 'checked="checked"'; } ?> />
            <label for="html"><?php echo _AT('html'); ?></label>
       
            <input type="radio" name="formatting" value="3" id="html_visual_editor" <?php if ($_POST['formatting'] == 3) { echo 'checked="checked"'; } ?> />
            <label for="html_visual_editor"><?php echo _AT('html') . ' - '. _AT('visual_editor'); ?></label>
       
            <input type="radio" name="formatting" value="2" id="weblink" <?php if ($_POST['formatting'] == 2) { echo 'checked="checked"'; } ?> />
            <label for="weblink"><?php echo _AT('weblink'); ?></label>
       </span>
       </span>
    </div>

<?php
		if ($content_row['content_path']) {
			echo '	<div class="row"><strong>'._AT('packaged_in').':</strong>&nbsp;&nbsp;'.$content_row['content_path'].'</div>';
		}
        if (trim($_POST['head']) == '<br />') {
	      $_POST['head'] = '';
        }
        if ($do_check) {
	       $_POST['head'] = $stripslashes($_POST['head']);
        }
?>
    <script type="text/javascript" language="javascript">
    //<!--
        ATutor.mods.editor.content_path = "<?php if(isset($content_row['content_path'])) echo $content_row['content_path'] ?>";
        ATutor.mods.editor.content_id = "<?php if(isset($cid)) echo $cid ?>";
        ATutor.mods.editor.head_enabled_title = "<?php echo _AT('customized_head_enabled_title'); ?>";
        ATutor.mods.editor.head_disabled_title = "<?php echo _AT('customized_head_disabled_title'); ?>";
        ATutor.mods.editor.paste_enabled_title = "<?php echo _AT('paste_enabled_title'); ?>";
        ATutor.mods.editor.paste_disabled_title = "<?php echo _AT('paste_disabled_title'); ?>";
        ATutor.mods.editor.fileman_enabled_title = "<?php echo _AT('fileman_enabled_title').' - '._AT('new_window'); ?>";
        ATutor.mods.editor.fileman_disabled_title = "<?php echo _AT('fileman_disabled_title'); ?>";
        ATutor.mods.editor.accessibility_enabled_title = "<?php echo _AT('accessibility_enabled').' - '._AT('new_window'); ?>";
        ATutor.mods.editor.accessibility_disabled_title = "<?php echo _AT('accessibility_disabled'); ?>";
        ATutor.mods.editor.processing_text = "<?php echo _AT('processing'); ?>";
    //-->
    </script>
    
    <div class="fl-container fl-fix">
      <ul id="content-tool-links">
        <?php 
        foreach ($_content_tools as $tool) {
        	echo '<li><img id="'.$tool["id"].'" class="'.$tool["class"].'" title="'.$tool['title'].'" src="'.$tool['src'].'" alt="'.$tool['alt'].'" height="30" width="30" />'.$tool["text"].'</li>'."\n";
        }
        ?>
      </ul>
   	</div> <!-- end toolbar -->

	<!-- Customized head -->
	<div id="head" class="row fl-fix">
        <label for="headtext"><?php echo _AT('customized_head');  ?>
        <small>(<?php echo _AT('customized_head_note'); ?>)</small></label>
        <input type="checkbox" name="use_customized_head" id="use_customized_head" value="1" <?php if ($_POST['use_customized_head']) { echo 'checked="checked"'; } ?> />
        <label for="use_customized_head"><?php echo _AT('use_customized_head'); ?></label>
		<textarea id="headtext" name="head" cols="" rows="10"><?php echo htmlspecialchars($_POST['head']); ?></textarea>	
	</div>
		
    <!-- Paste from file -->
   	<div id="paste" class="row">
       	<div><?php echo _AT('paste_file')?><small>(<?php echo _AT('html_only'); ?>)</small></div>
       	<input title="<?php echo _AT('browse_for_upload'); ?>" type="file" name="uploadedfile_paste" id="uploadedfile" class="formfield" size="20" /> 
       	<input type="submit" name="submit_file" value="<?php echo _AT('upload'); ?>"  class="button" />
    </div>
   
    <?php 
        // kludge #1548
        if (trim($_POST['body_text']) == '<br />') {
	       $_POST['body_text'] = '';
        }
        if ($do_check) {
	       $_POST['body_text'] = $stripslashes($_POST['body_text']);
        }
    ?>

    <div class="row">
        <span id="textSpan">
            <label for="body_text"><strong><?php echo _AT('body');  ?></strong></label><br />
		    <textarea name="body_text" id="body_text" cols="" rows="20"><?php echo htmlspecialchars($_POST['body_text']);?></textarea>
		</span>
		<span id="weblinkSpan">	
	        <label for="weblink_text"><?php echo _AT('weblink');  ?></label>
            <input name="weblink_text" id="weblink_text" value="<?php echo ($_POST['weblink_text']!=''?htmlspecialchars($_POST['weblink_text']):'http://'); ?>" />
		</span>
	</div>

