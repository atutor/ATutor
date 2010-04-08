<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2009										*/
/* Adaptive Technology Resource Centre / University of Toronto  */
/* http://atutor.ca												*/
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.				*/
/****************************************************************/
// $Id: edit.inc.php 8901 2009-11-11 19:10:19Z cindy $

if (!defined('AT_INCLUDE_PATH')) { exit; }
?>

    <input type="hidden" name="displayhead" id="displayhead" value="<?php if ($_POST['displayhead']==1 || $_REQUEST['displayhead']==1 || $_GET['displayhead']==1) echo '1'; else echo '0'; ?>" />
    <input type="hidden" name="displaytools" id="displaytools" value="<?php if ($_POST['displaytools']==1 || $_REQUEST['displaytools']==1 || $_GET['displaytools']==1) echo '1'; else echo '0'; ?>" />
    <input type="hidden" name="complexeditor" id="complexeditor" value="<?php if ($_POST['complexeditor']==1 || $_REQUEST['complexeditor']==1 || $_GET['complexeditor']==1) echo '1'; else echo '0'; ?>" />

	<div class="row">
	    <span>
		<span class="required" title="<?php echo _AT('required_field'); ?>">*</span><label for="ctitle"><?php echo _AT('title');  ?></label>
		<input type="text" name="title" id="ctitle" size="70" class="formfield" value="<?php echo ContentManager::cleanOutput($_POST['title']); ?>" />
        </span>
        <span class="nowrap">
        <label for="formatting_radios"><span class="required" title="<?php echo _AT('required_field'); ?>">*</span><?php echo _AT('formatting'); ?></label>
        <span class="bordered" id="formatting_radios">
            <input type="radio" name="formatting" value="0" id="text" <?php if ($_POST['formatting'] == 0) { echo 'checked="checked"'; } ?> onclick="ATutor.mods.editor.switch_content_type(this.value);" />
            <label for="text"><?php echo _AT('plain_text'); ?></label>

            <input type="radio" name="formatting" value="1" id="html" <?php if ($_POST['formatting'] == 1) { echo 'checked="checked"'; } ?> onclick="ATutor.mods.editor.switch_content_type(this.value, '<?php echo $_SESSION['prefs']['PREF_CONTENT_EDITOR']?>');" />
            <label for="html"><?php echo _AT('html'); ?></label>
       
            <input type="radio" name="formatting" value="2" id="weblink" <?php if ($_POST['formatting'] == 2) { echo 'checked="checked"'; } ?> onclick="ATutor.mods.editor.switch_content_type(this.value);" />
            <label for="weblink"><?php echo _AT('weblink'); ?></label>
       </span>
       </span>
    </div>

	<?php
		if ($content_row['content_path']) {
			echo '	<div class="row">'._AT('packaged_in').'<br />'.$content_row['content_path'].'</div>';
		}
        if (trim($_POST['head']) == '<br />') {
	      $_POST['head'] = '';
        }
        if ($do_check) {
	       $_POST['head'] = $stripslashes($_POST['head']);
        }
    ?>
    
    <!-- TODO LAW text to add here for title text on both links -->
    <div class="row fl-col-fixed">
       <a id="headtool" class="fl-force-left fl-col-flex" title="Click to show/hide customized head" ><img src="<?php echo $_base_path; ?>images/custom_head.jpg" alt="<?php echo _AT('customized_head');  ?>" height="30" width="30" /></a>
       <a id="tooltool" class="fl-col-flex" title="Click to show/hide tools" ><img src="<?php echo $_base_path; ?>images/tool_go.jpg" alt="<?php echo _AT('tools');  ?>" height="30" width="30" /></a>
    </div>   

	<div id="head" class="row">
        <label for="head"><?php echo _AT('customized_head');  ?></label>
        <small>(<?php echo _AT('customized_head_note'); ?>)</small>
        <input type="checkbox" name="use_customized_head" id="use_customized_head" value="1" <?php if ($_POST['use_customized_head']) { echo 'checked="checked"'; } ?> />
        <label for="use_customized_head"><?php echo _AT('use_customized_head'); ?></label>
		<textarea cols="" rows="10"><?php echo htmlspecialchars($_POST['head']); ?></textarea>	
	</div>
		
	<div id="tools" class="row bottom-margin">
        <label class="fl-force-left" for="toolbar"><?php echo _AT('tools');  ?></label>
        <div class="fl-container-flex66 fl-col-flex3" id="">
            <div class='fl-col'>
        <!-- ******** Tool Manager ******* -->
<?php
    echo "<div class='fl-text-align-center'>"._AT('tools_manager')."</div>";
    $count = 0;
    foreach($all_tools as $tool) {
        if($tool['tool_file'] != '' && $tool['table'] != '') {
            $sql_assoc = "SELECT * FROM ".TABLE_PREFIX.$tool['table']." WHERE content_id='$cid'";
            $result_assoc = mysql_query($sql_assoc,$db);

            //TODO LAW note that the text 'added' and 'none' below need to be moved to language files
            if($num_row = mysql_num_rows($result_assoc)){
                $tool['alt'] = $tool['title'].' added';
            } else {
                $tool['alt'] = $tool['title'].' none';
            }

            $count++; ?>
            <!-- TODO LAW can this click handler be moved to edit.js -->
            <script type="text/javascript" language="javascript">
                document.write(" <a href=\"#\" onclick=\"window.open('<?php echo AT_BASE_HREF; ?>mods/_core/tool_manager/index.php?framed=1<?php echo SEP; ?>popup=1<?php echo SEP; ?>tool_file=<?php echo $tool['tool_file'].SEP;?>cid=<?php echo $cid;?>','newWin2','menubar=0,scrollbars=1,resizable=1,width=600,height=400'); return false;\"><img class='fl-centered fl-fix' id=\"<?php echo $tool['title'];?>\" src='<?php echo $tool['img']; ?>' alt='<?php echo $tool['alt'];?>' title='<?php echo $tool['title'];?>' height='30' border='0'/></a>");
            </script>
        <?php }
    }
    if($count == 0){
        echo '<em>'._AT('none_found').'</em>';
    } ?>
            </div> <!-- end col -->
<!-- ****** end Tool Manager ***** -->

        <!-- Paste from file tool -->
        <div class='fl-col' id="pastetool">
            <div class='fl-text-align-center'><?php echo _AT('paste_file')?></div>
            <div class='fl-text-align-center'><small><?php echo _AT('html_only'); ?></small></div>

            <!-- TODO LAW can this click handler be moved to edit.js -->
            <!-- TODO LAW still trying to change the paste tool to use an icon and a dialog -->
                <script type="text/javascript" language="javascript">
                //<!--
//                    document.write(" <a href=\"#\" onclick=\"window.open('<?php echo AT_BASE_HREF; ?>mods/_core/editor/editor_tabs/pastefromfile.php','newWin1','menubar=0,scrollbars=1,resizable=1,width=640,height=490'); return false;\"><img class='fl-centered' src=\"<?php echo $_base_path; ?>images/paste_plain.png\" alt=\"Paste from file\" height='16' width='16' /></a>");
                //-->
                </script>
            <!-- TODO LAW language to be moved to language files -->
            <input title="Browse for file to upload" type="file" name="uploadedfile_paste" id="uploadedfile" class="formfield" size="20" /> <input type="submit" name="submit_file" value="<?php echo _AT('upload'); ?>"  class="button" />
        </div> <!--  end col -->
        
        <!-- File manager tool -->
        <div class='fl-col' id="filemantool">
            <div class='fl-text-align-center'><?php echo _AT('file_manager'); ?></div>

            <!-- TODO LAW alt text for language files -->
                <script type="text/javascript" language="javascript">
                //<!--
                     ATutor.content_path = "<?php if(isset($content_row['content_path'])) echo $content_row['content_path'] ?>";
                     document.write(' <a id="file_manager_launcher" href="#"><img class="fl-centered" src="<?php echo $_base_path; ?>images/file-manager.png" alt="Open file manager" height="30" width="30" /></a>');
                //-->
                </script>
                <noscript>
                    <a href="<?php echo AT_BASE_HREF; ?>mods/_core/file_manager/index.php?framed=1"><img class='fl-centered' src="<?php echo $_base_path; ?>images/file-manager.png" alt="Open file manager" height='30' width='30' /></a>
                </noscript>    
        </div> <!-- end col -->    
        </div>
	
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
            <label for="body_text"><?php echo _AT('body');  ?></label>
		    <textarea name="body_text" id="body_text" cols="" rows="20"><?php echo htmlspecialchars($_POST['body_text']);?></textarea>
		</span>
		<span id="weblinkSpan">	
	        <label for="weblink_text"><?php echo _AT('weblink');  ?></label>
            <input name="weblink_text" id="weblink_text" value="<?php echo ($_POST['weblink_text']!=''?htmlspecialchars($_POST['weblink_text']):'http://'); ?>" />
		</span>
	</div>

