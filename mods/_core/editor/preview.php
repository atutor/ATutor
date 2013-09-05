<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2010                                      */
/* Inclusive Design Institute                                   */
/* http://atutor.ca												*/
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.				*/
/****************************************************************/
// $Id$

define('AT_INCLUDE_PATH', '../../../include/');

require(AT_INCLUDE_PATH.'vitals.inc.php');
require(AT_INCLUDE_PATH.'../mods/_core/editor/editor_tab_functions.inc.php');

$cid = intval($_POST['cid']);

if ($cid == 0) {
	require(AT_INCLUDE_PATH.'header.inc.php');
	$missing_fields[] = _AT('content_id');
	$msg->printErrors(array('SAVE_BEFORE_PROCEED', $missing_fields));
	require (AT_INCLUDE_PATH.'footer.inc.php');
	exit;
}

$rows_content = $contentManager->getContentPage($cid);

if(count($rows_content) == 0){
	require(AT_INCLUDE_PATH.'header.inc.php');
	$msg->printErrors('PAGE_NOT_FOUND');
	require (AT_INCLUDE_PATH.'footer.inc.php');
	exit;
}

if (defined('AT_FORCE_GET_FILE') && AT_FORCE_GET_FILE) {
	$course_base_href = 'get.php/';
} else {
	$course_base_href = 'content/' . $_SESSION['course_id'] . '/';
}

foreach($rows_content as $content_row){
    if ($content_row['content_path']) {
        $content_base_href .= $content_row['content_path'].'/';
    }
}
require(AT_INCLUDE_PATH.'header.inc.php');

?>
	<div class="row">
	<?php 
		echo '<h2>'.AT_print($stripslashes($_POST['title']), 'content.title').'</h2>';
		if ($_POST['formatting'] == CONTENT_TYPE_WEBLINK) {
		    $url = $_POST['weblink_text'];
            $validated_url = isValidURL($url);
            if (!validated_url || $validated_url !== $url) {
                $msg->addError(array('INVALID_INPUT', _AT('weblink')));
                $msg->printErrors();
            } else {
                  echo format_content($url, $_POST['formatting'], array());
            }
        } else {
            echo format_content($stripslashes($_POST['body_text']), $_POST['formatting'], $_POST['glossary_defs']);
        }
    ?>		
	</div>
<?php 
require(AT_INCLUDE_PATH.'footer.inc.php');
?>