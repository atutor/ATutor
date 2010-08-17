<?php
/************************************************************************/
/* ATutor																*/
/************************************************************************/
/* Copyright (c) 2002-2010                                              */
/* Inclusive Design Institute                                           */
/* http://atutor.ca                                                     */
/* This program is free software. You can redistribute it and/or        */
/* modify it under the terms of the GNU General Public License          */
/* as published by the Free Software Foundation.                        */
/************************************************************************/
// $Id$

define('AT_INCLUDE_PATH', '../../../include/');

global $db, $associated_forum;

$get_related_glossary = true;
require(AT_INCLUDE_PATH.'vitals.inc.php');
require(AT_INCLUDE_PATH.'lib/tinymce.inc.php');
require_once(AT_INCLUDE_PATH.'../mods/_core/file_manager/filemanager.inc.php');

$cid = intval($_REQUEST['cid']);

if ($_POST) {
	$do_check = TRUE;
} else {
	$do_check = FALSE;
}

require(AT_INCLUDE_PATH.'../mods/_core/editor/editor_tab_functions.inc.php');

if ($_POST['close'] || $_GET['close']) {
	if ($_GET['close']) {
		$msg->addFeedback('ACTION_COMPLETED_SUCCESSFULLY');
	} else {
		$msg->addFeedback('CLOSED');
		if ($cid == 0) {
			header('Location: '.AT_BASE_HREF.'mods/_core/content/index.php');
			exit;
		}
	}
	
	if ($_REQUEST['cid'] == 0) {
		header('Location: '.AT_BASE_HREF.'mods/_core/content/index.php');
		exit;
	}
	header('Location: '.$_base_path.url_rewrite('content.php?cid='.intval($_REQUEST['cid'])));
	exit;
}
	
$tabs = get_tabs();	
$num_tabs = count($tabs);
for ($i=0; $i < $num_tabs; $i++) {
	if (isset($_POST['button_'.$i]) && ($_POST['button_'.$i] != -1)) { 
		$current_tab = $i;
		$_POST['current_tab'] = $i;
		break;
	}
}

if (isset($_GET['tab'])) {
	$current_tab = intval($_GET['tab']);
}
if (isset($_POST['current_tab'])) {
	$current_tab = intval($_POST['current_tab']);
}

if (isset($_POST['submit_file'])) {
	paste_from_file(body_text);
} else if (isset($_POST['submit']) && ($_POST['submit'] != 'submit1')) {
	/* we're saving. redirects if successful. */
	save_changes(true, $current_tab);
}

if (isset($_POST['submit_file_alt'])) {
	paste_from_file(body_text_alt);
} else if (isset($_POST['submit']) && ($_POST['submit'] != 'submit1')) {
	/* we're saving. redirects if successful. */
	save_changes(true, $current_tab);
}

if (isset($_POST['submit'])) {
	/* we're saving. redirects if successful. */
	save_changes(true, $current_tab);
}

if (!isset($current_tab) && isset($_POST['button_1']) && ($_POST['button_1'] == -1) && !isset($_POST['submit'])) {
	$current_tab = 1;
} else if (!isset($current_tab)) {
	$current_tab = 0;
}

if ($cid) {
	$_section[0][0] = _AT('edit_content');
} else {
	$_section[0][0] = _AT('add_content');
}

if($current_tab == 0) {
    $_custom_head .= '
    <link rel="stylesheet" type="text/css" href="'.AT_BASE_HREF.'jscripts/infusion/framework/fss/css/fss-layout.css" />
    <link rel="stylesheet" type="text/css" href="'.AT_BASE_HREF.'jscripts/infusion/framework/fss/css/fss-text.css" />
    <script type="text/javascript" src="'.$_base_path.'mods/_core/editor/js/edit.js"></script>
    ';
}

if ($cid) {
	$result = $contentManager->getContentPage($cid);

	if (!($content_row = @mysql_fetch_assoc($result))) {
		require(AT_INCLUDE_PATH.'header.inc.php');
		$msg->printErrors('PAGE_NOT_FOUND');
		require (AT_INCLUDE_PATH.'footer.inc.php');
		exit;
	}

	$path	= $contentManager->getContentPath($cid);
	$content_test = $contentManager->getContentTestsAssoc($cid);

	if (defined('AT_FORCE_GET_FILE') && AT_FORCE_GET_FILE) {
		$course_base_href = 'get.php/';
	} else {
		$course_base_href = 'content/' . $_SESSION['course_id'] . '/';
	}

	if ($content_row['content_path']) {
		$content_base_href .= $content_row['content_path'].'/';
	}
} else {
	if (defined('AT_FORCE_GET_FILE') && AT_FORCE_GET_FILE) {
		$content_base_href = 'get.php/';
	} else {
		$content_base_href = 'content/' . $_SESSION['course_id'] . '/';
	}
}

if (($current_tab == 0) || ($_current_tab == 3)) {
    if ($_POST['formatting'] == null){ 
        // this is a fresh load from just logged in
	    if ($_SESSION['prefs']['PREF_CONTENT_EDITOR'] == 0) {
			$_POST['formatting'] = 0;
		} else {
			$_POST['formatting'] = 1;
		}
    }
}

require(AT_INCLUDE_PATH.'header.inc.php');

if ($current_tab == 0 || $current_tab == 3) 
{
    $simple = true;
    if ($_POST['complexeditor'] == '1') {
        $simple = false;
    }
    load_editor($simple, false, "none");    
}

//TODO*************BOLOGNA****************REMOVE ME**************/
//loading toolbar for insert discussion topic or web link into the content
if ($current_tab == 0){
    if(authenticate(AT_PRIV_CONTENT,AT_PRIV_RETURN)){
        $home_links = get_home_navigation();                        //vengono lette le caratteristiche di ogni modulo attivato nella home page.
        $main_links = get_main_navigation($current_page);           //vengono lette le caratteristiche di ogni modulo attivo nel main navigation

        $num = count($main_links);                                  //necessario elminare il primo e l'utlimo elemento poichè sono rispettivamente "Home" e "Manage"
        unset($main_links[0]);                                      //"Home" label
        unset($main_links[$num-1]);                                 //"Manage" label

        $all_tools = $home_links;                                   //$all_tools represent a merge between $home_links and main_links without repetitions.
        $check=false;
        foreach($main_links as $main) {
            foreach($home_links as $home) {
                if($home['title'] == $main['title']) {
                    $check=true;
                    break;
                }
            }
            if(!$check)
                $all_tools[]=$main;
            else
                $check=false;
        }
    }
}


$cid = intval($_REQUEST['cid']);
$pid = intval($_REQUEST['pid']);
?>
<form action="<?php echo $_SERVER['PHP_SELF']; ?>?cid=<?php echo $cid; ?>" method="post" name="form" enctype="multipart/form-data">
<?php

	if ($cid) {
		//$content_row = sql_quote($content_row);
		if (isset($_POST['current_tab'])) {
			//$changes_made = check_for_changes($content_row);
		} else {
			$changes_made = array();

			$_POST['formatting'] = $content_row['formatting'];
			$_POST['head'] = $content_row['head'];
			$_POST['use_customized_head'] = $content_row['use_customized_head'];
			$_POST['title']      = $content_row['title'];
			$_POST['body_text']  = $content_row['text'];
			$_POST['weblink_text'] = $content_row['text'];
			$_POST['keywords']   = $content_row['keywords'];
			$_POST['test_message'] = $content_row['test_message'];
			$_POST['allow_test_export'] = $content_row['allow_test_export'];

			$_POST['day']   = substr($content_row['release_date'], 8, 2);
			$_POST['month'] = substr($content_row['release_date'], 5, 2);
			$_POST['year']  = substr($content_row['release_date'], 0, 4);
			$_POST['hour']  = substr($content_row['release_date'], 11, 2);
			$_POST['min']= substr($content_row['release_date'], 14, 2);

			$_POST['ordering'] = $content_row['ordering'];
			$_POST['related'] = $contentManager->getRelatedContent($cid);
			
			$_POST['pid'] = $pid = $content_row['content_parent_id'];

			$_POST['related_term'] = $glossary_ids_related;
		}

	} else {
		$cid = 0;
		if (!isset($_POST['current_tab'])) {
			$_POST['day']  = date('d');
			$_POST['month']  = date('m');
			$_POST['year'] = date('Y');
			$_POST['hour'] = date('H');
			$_POST['min']  = 0;

			if (isset($_GET['pid'])) {
				$pid = $_POST['pid'] = intval($_GET['pid']);
				$_POST['ordering'] = count($contentManager->getContent($pid))+1;
			} else {
				$_POST['pid'] = 0;
				$_POST['ordering'] = count($contentManager->getContent(0))+1;
			}
		}
	}
	
	echo '<input type="hidden" name="cid" value="'.$cid.'" />';
	echo '<input type="hidden" name="title" value="'.htmlspecialchars($stripslashes($_POST['title'])).'" />';
	if ($_REQUEST['sub'] == 1)
	{
		echo '<input type="hidden" name="sub" value="1" />';
		echo '<input type="hidden" name="folder_title" value="'.htmlspecialchars($stripslashes($_POST['folder_title'])).'" />';
	}
	echo '<input type="submit" name="submit" style="display:none;"/>';
	if (($current_tab != 0) && (($_current_tab != 3))) {
        echo '<input type="hidden" name="body_text" value="'.htmlspecialchars($stripslashes($_POST['body_text'])).'" />';
        echo '<input type="hidden" name="weblink_text" value="'.htmlspecialchars($stripslashes($_POST['weblink_text'])).'" />';
        echo '<input type="hidden" name="head" value="'.htmlspecialchars($stripslashes($_POST['head'])).'" />';
		echo '<input type="hidden" name="use_customized_head" value="'.(($_POST['use_customized_head']=="") ? 0 : $_POST['use_customized_head']).'" />';
        echo '<input type="hidden" name="displayhead" id="displayhead" value="'.$_POST['displayhead'].'" />';
        echo '<input type="hidden" name="complexeditor" id="complexeditor" value="'.$_POST['complexeditor'].'" />';
        echo '<input type="hidden" name="formatting" value="'.$_POST['formatting'].'" />';
	}

	echo '<input type="hidden" name="ordering" value="'.$_POST['ordering'].'" />';
	echo '<input type="hidden" name="pid" value="'.$pid.'" />';

	echo '<input type="hidden" name="day" value="'.$_POST['day'].'" />';
	echo '<input type="hidden" name="month" value="'.$_POST['month'].'" />';
	echo '<input type="hidden" name="year" value="'.$_POST['year'].'" />';
	echo '<input type="hidden" name="hour" value="'.$_POST['hour'].'" />';
	echo '<input type="hidden" name="minute" value="'.$_POST['minute'].'" />';
	echo '<input type="hidden" name="min" value="'.$_POST['min'].'" />';
	
	echo '<input type="hidden" name="alternatives" value="'.$_POST['alternatives'].'" />';
	
	echo '<input type="hidden" name="current_tab" value="'.$current_tab.'" />';

	if (is_array($_POST['related']) && ($current_tab != 1)) {
		foreach($_POST['related'] as $r_id) {
			echo '<input type="hidden" name="related[]" value="'.$r_id.'" />';
		}
	}
	echo '<input type="hidden" name="keywords" value="'.htmlspecialchars(stripslashes($_POST['keywords'])).'" />';

	//content test association
	echo '<input type="hidden" name="test_message" value="'.$_POST['test_message'].'" />';
	
	/* get glossary terms */
	$matches = find_terms(stripslashes($_POST['body_text']));
	$num_terms = count($matches[0]);
	$matches = $matches[0];
	$word = str_replace(array('[?]', '[/?]'), '', $matches);

	if (is_array($word)) {
		/* update $_POST['glossary_defs'] with any new/changed terms */
		for($i=0; $i<$num_terms; $i++) {
			$word[$i] = htmlentities_utf8($word[$i]);
			if (!isset($_POST['glossary_defs'][$word[$i]])) {
				$_POST['glossary_defs'][$word[$i]] = $glossary[$word[$i]];
			}
		}
	}

	if (is_array($_POST['glossary_defs']) && ($current_tab != 2)) {
		foreach($_POST['glossary_defs'] as $w => $d) {

			/* this term still exists in the content */
			if (!in_array($w, $word)) {
				unset($_POST['glossary_defs'][$w]);
				continue;
			}
			echo '<input type="hidden" name="glossary_defs['.$w.']" value="'.htmlspecialchars(stripslashes($d)).'" />';
		}
		if (isset($_POST['related_term'])) {
			foreach($_POST['related_term'] as $w => $d) {
				echo '<input type="hidden" name="related_term['.$w.']" value="'.$d.'" />';
			}
		}
	}

	// adapted content
	$sql = "SELECT pr.primary_resource_id, prt.type_id
	          FROM ".TABLE_PREFIX."primary_resources pr, ".
	                 TABLE_PREFIX."primary_resources_types prt
	         WHERE pr.content_id = ".$cid."
	           AND pr.language_code = '".$_SESSION['lang']."'
	           AND pr.primary_resource_id = prt.primary_resource_id";
	$all_types_result = mysql_query($sql, $db);
	
	$i = 0;
	while ($type = mysql_fetch_assoc($all_types_result)) {
		$row_alternatives['alt_'.$type['primary_resource_id'].'_'.$type['type_id']] = 1;
	}
	
	if ($current_tab != 3 && isset($_POST['use_post_for_alt']))
	{
		echo '<input type="hidden" name="use_post_for_alt" value="1" />';
		if (is_array($_POST)) {
			foreach ($_POST as $alt_id => $alt_value) {
				if (substr($alt_id, 0 ,4) == 'alt_'){
					echo '<input type="hidden" name="'.$alt_id.'" value="'.$alt_value.'" />';
				}
			}
		}
	}
	
	//tests
	if ($current_tab != 4){
		// set content associated tests
		if (is_array($_POST['tid'])) {
			foreach ($_POST['tid'] as $i=>$tid){
				echo '<input type="hidden" name="tid['.$i.']" value="'.$tid.'" />';
			}
		}
		else
		{
			$i = 0;
			if ($content_test){
				while ($content_test_row = mysql_fetch_assoc($content_test)){
					echo '<input type="hidden" name="tid['.$i++.']" value="'.$content_test_row['test_id'].'" />';
				}
			}
		}
		
		// set pre-tests
		if (is_array($_POST['pre_tid'])) {
			foreach ($_POST['pre_tid'] as $i=>$pre_tid){
				echo '<input type="hidden" name="pre_tid['.$i.']" value="'.$pre_tid.'" />';
			}
		}
		else
		{
			$i = 0;
			$sql = 'SELECT * FROM '.TABLE_PREFIX."content_prerequisites WHERE content_id=$cid AND type='".CONTENT_PRE_TEST."'";
			$pretests_result = mysql_query($sql, $db);
			while ($pretest_row = mysql_fetch_assoc($pretests_result)) {
					echo '<input type="hidden" name="pre_tid['.$i++.']" value="'.$pretest_row['item_id'].'" />';
			}
		}
	} 
	if (!isset($_POST['allow_test_export']) && $current_tab != 4) {
		//export flag handling.
		$sql = "SELECT `allow_test_export` FROM ".TABLE_PREFIX."content WHERE content_id=$_REQUEST[cid]";
		$result2 = mysql_query($sql, $db);
		if ($result2){
			$c_row = mysql_fetch_assoc($result2);
		}
		if (intval($c_row['allow_test_export'])==1){
			echo '<input type="hidden" name="allow_test_export" value="1" />';
		} else {
			echo '<input type="hidden" name="allow_test_export" value="0" />';
		}
	} else {
		echo '<input type="hidden" name="allow_test_export" value="'.intval($_POST['allow_test_export']).'" />';
	}

	if ($do_check) {
		$changes_made = check_for_changes($content_row, $row_alternatives);
	}
?>

<div class="editor_wrapper">

<div align="center">
	<?php output_tabs($current_tab, $changes_made); ?>
</div>
<div class="input-form" style="width: 95%;">

	<?php if ($changes_made): ?>
		<div class="unsaved">
			<span style="color:red;"><?php echo _AT('save_changes_unsaved'); ?></span> 
			<input type="submit" name="submit" value="<?php echo _AT('save'); ?>" title="<?php echo _AT('save_changes'); ?> alt-s" accesskey="s" style="border: 1px solid red;" /> 
			<input type="submit" name="close" class="button green" value="<?php echo _AT('close'); ?>" />  <input type="checkbox" id="close" name="save_n_close" value="1" <?php if ($_SESSION['save_n_close']) { echo 'checked="checked"'; } ?> />
			<label for="close"><?php echo _AT('close_after_saving'); ?></label>
		</div>

	<?php else: ?>
		<div class="saved">
			<?php //if ($cid) { echo _AT('save_changes_saved'); } ?> <input type="submit" name="submit" value="<?php echo _AT('save'); ?>" title="<?php echo _AT('save_changes'); ?> alt-s" accesskey="s" class="button"/> <input type="submit" name="close" value="<?php echo _AT('close'); ?>"  class="button"/> <input type="checkbox" style="border:0px;" id="close" name="save_n_close" value="1" <?php if ($_SESSION['save_n_close']) { echo 'checked="checked"'; } ?>/><label for="close"><?php echo _AT('close_after_saving'); ?></label>
		</div>
	<?php endif; ?>
	<?php include('editor_tabs/'.$tabs[$current_tab][1]); ?>
</div></div>
</form>

<?php require(AT_INCLUDE_PATH.'footer.inc.php'); ?>
