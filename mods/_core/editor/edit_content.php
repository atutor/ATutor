<?php
/************************************************************************/
/* ATutor                                                                */
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

authenticate(AT_PRIV_CONTENT);
tool_origin();

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
        
        $return_url = $_SESSION['tool_origin']['url'];
        tool_origin('off');
		header('Location: '.$return_url);
		exit;
        }
    }
    
    if ($_REQUEST['cid'] == 0) {
        $return_url = $_SESSION['tool_origin']['url'];
        tool_origin('off');
		header('Location: '.$return_url);
		exit;
    }
    
        $return_url = $_base_path.url_rewrite('content.php?cid='.intval($_REQUEST['cid'])); //$_SESSION['tool_origin']['url'];
        tool_origin('off');
		header('Location: '.$return_url);
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
    paste_from_file();
} else if (isset($_POST['submit']) && ($_POST['submit'] != 'submit1')) {
    /* we're saving. redirects if successful. */
    save_changes(true, $current_tab);
}

if (isset($_POST['submit_file_alt'])) {
    paste_from_file();
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
    global $_content_tools;
    
    $_custom_head .= '
   <!-- <link rel="stylesheet" type="text/css" href="'.AT_BASE_HREF.'jscripts/infusion/framework/fss/css/fss-layout.css" />  -->
    <link rel="stylesheet" type="text/css" href="'.AT_BASE_HREF.'jscripts/infusion/framework/fss/css/fss-text.css" />
    ';
    $_content_tools = is_array($_content_tools) ? $_content_tools : array();
    
    $current_tool_pos = 0;
    $_content_tools[] = array("id"=>"previewtool", 
                              "class"=>"fl-col clickable", 
                              "src"=>AT_BASE_HREF."images/preview.png",
                              "title"=>_AT('preview').' - '._AT('new_window'),
                              "alt"=>_AT('preview').' - '._AT('new_window'),
                              "text"=>_AT('preview'), 
                              "js"=>AT_BASE_HREF."mods/_core/editor/js/edit.js",
                              "position"=>++$current_tool_pos);
    
    $_content_tools[] = array("id"=>"accessibilitytool", "class"=>"fl-col", "text"=>_AT('accessibility'), "position"=>++$current_tool_pos);
    $_content_tools[] = array("id"=>"headtool", "class"=>"fl-col", "text"=>_AT('customized_head'), "position"=>++$current_tool_pos);
    $_content_tools[] = array("id"=>"pastetool", "class"=>"fl-col", "text"=>_AT('paste'), "position"=>++$current_tool_pos);
    $_content_tools[] = array("id"=>"filemantool", "class"=>"fl-col", "text"=>_AT('files'), "position"=>++$current_tool_pos);

    foreach ($_content_tools as $tool) {
        if (isset($tool["js"])) {
            $_custom_head .= '<script type="text/javascript" src="'.$tool["js"].'"></script>'."\n";
        }
    }
}

if ($cid) {
    $result = $contentManager->getContentPage($cid);
    foreach($result as $content_row){
        if (!$content_row) {
            require(AT_INCLUDE_PATH.'header.inc.php');
            $msg->printErrors('PAGE_NOT_FOUND');
            require (AT_INCLUDE_PATH.'footer.inc.php');
            exit;
        }
    }
    $path    = $contentManager->getContentPath($cid);
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
        if ($_SESSION['prefs']['PREF_CONTENT_EDITOR'] == 0) { // plain text in text area
            $_POST['formatting'] = 0;
        } else if ($_SESSION['prefs']['PREF_CONTENT_EDITOR'] == 1) { // html with text area
            $_POST['formatting'] = 1;
        } else if ($_SESSION['prefs']['PREF_CONTENT_EDITOR'] == 2) { // html with visual editor
            $_POST['formatting'] = 3;
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

// Generate the last content tool elements
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
            if(!$check && $main['tool_file'] != '') {
                $_content_tools[] = array("class"=>"fl-col clickable tool", 
                                          "src"=>$main['img'], 
                                          "alt"=>$main['alt'],
                                          "title"=>$main['title'],
                                          "text"=>$main['title'],
                                          "position"=>++$current_tool_pos);
    
                if(isset($main['tool_file'])) {
                  //  echo '<!-- TODO LAW note problem here with one tool_file variable for multiple tools -->'."\n";
                    echo '  <script type="text/javascript" language="javascript">'."\n";
                    echo '  //<!--'."\n";
                    echo '  ATutor.mods.editor.tool_for = "' . urlencode($main['title']) . '";'."\n";
                    echo '  //-->'."\n";
                    echo '  </script>'."\n";
                }
                
            }
            else {
                $check=false;
            }
        }
    }
}

// The customized function to sort multi-dimentional array $_content_tools
function compare($x, $y) {
    if (!isset($x["position"])) return 1;
    if (!isset($y["position"])) return -1;
    if ( $x["position"] == $y["position"] )
        return 0;
    else if ( $x["position"] < $y["position"] )
        return -1;
    else
        return 1;
}

// Sort $_content_tools. Always append the element with empty "position" to the end of the result array.
usort($_content_tools, 'compare');

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
//            $word[$i] = htmlentities_utf8($word[$i]);
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
            echo '<input type="hidden" name="glossary_defs['.AT_print($w, 'glossary.word').']" value="'.AT_print($d, 'glossary.definition').'" />';
        }
        if (isset($_POST['related_term'])) {
            foreach($_POST['related_term'] as $w => $d) {
                echo '<input type="hidden" name="related_term['.AT_print($w, 'glossary.word').']" value="'.AT_print($d, 'glossary.definition').'" />';
            }
        }
    }

    // adapted content
    $sql = "SELECT pr.primary_resource_id, prt.type_id
              FROM %sprimary_resources pr, %sprimary_resources_types prt
             WHERE pr.content_id = %d
               AND pr.language_code = '%s'
               AND pr.primary_resource_id = prt.primary_resource_id";
    $all_types_result = queryDB($sql, array(TABLE_PREFIX, TABLE_PREFIX, $cid, $_SESSION['lang'])); 
    $i = 0;
    foreach($all_types_result as $type){
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
            
                foreach($content_test as $content_test_row){
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

            $sql = "SELECT * FROM %scontent_prerequisites WHERE content_id=%d AND type='%s'";
            $pretests_result = queryDB($sql, array(TABLE_PREFIX, $cid, CONTENT_PRE_TEST));
            
            foreach($pretests_result as $pretest_row){
                echo '<input type="hidden" name="pre_tid['.$i++.']" value="'.$pretest_row['item_id'].'" />';
            }
        }
    } 
    if (!isset($_POST['allow_test_export']) && $current_tab != 4) {
        //export flag handling.
        // THIS CONDITION DOES NOT APPEAR TO BE IN USE, allow_test_export ALWAYS = 0
        
        $sql = "SELECT `allow_test_export` FROM %scontent WHERE content_id=%d";
        $result2 = queryDB($sql, array(TABLE_PREFIX, $_REQUEST['cid']));
        
        if ($result2){
            $c_row = $result2;
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

<div>
    <?php output_tabs($current_tab, $changes_made); ?>
</div>

<span style="clear:both;"/></span>
<div class="input-form">

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
