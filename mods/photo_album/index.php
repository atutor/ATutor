<?php
/*==============================================================
  Photo Album
 ==============================================================
  Copyright (c) 2006 by Dylan Cheon & Kelvin Wong
  Institute for Assistive Technology / University of Victoria
  http://www.canassist.ca/                                    
                                                               
  This program is free software. You can redistribute it and/or
  modify it under the terms of the GNU General Public License  
  as published by the Free Software Foundation.                
 ==============================================================
 */
// $Id:

/**
 * @desc	This file generates the photo album thumbnail view
 * @author	Dylan Cheon & Kelvin Wong
 * @copyright	2006, Institute for Assistive Technology / University of Victoria 
 * @link	http://www.canassist.ca/                                    
 * @license GNU
 */

define('AT_INCLUDE_PATH', '../../include/');
require_once(AT_INCLUDE_PATH.'vitals.inc.php');
//$_custom_css = $_base_path . 'mods/photo_album/module.css'; // use a custom stylesheet
$_custom_css = $_base_path . 'mods/photo_album/module.css'; // use a custom stylesheet
$movedarray = array();
if($_POST['submit'] = "save"){
	foreach($_POST as $image_id => $_image_order){
	$movedarray[$image_id] =  $_image_order;
	$image_order = intval($image_order);
	$image_id = intval($image_id);
	
	if ($image_id > 0) {
		$o++;
		$sql = "UPDATE ".TABLE_PREFIX."pa_image set `order`='$o' WHERE `image_id` = $image_id";
		if($result = mysql_query($sql, $db)){
			$msg->addFeedback('IMAGE_ORDER_SAVED');
		}
	}
//debug($sql);	

	//$sql = "UPDATE ".TABLE_PREFIX."pa_image set `order`='$_image_order' WHERE `image_id` = $image_id";
	}
}
require_once (AT_INCLUDE_PATH.'header.inc.php');
?>

    <script type="text/javascript" src="<?php echo FLUID_URL; ?>/js/jquery/jquery-1.2.1.js" rsf:id="scr=contribute-script"></script>
    <script type="text/javascript" src="<?php echo FLUID_URL; ?>/js/jquery.ui-1.0/ui.mouse.js" rsf:id="scr=contribute-script"></script>
    <script type="text/javascript" src="<?php echo FLUID_URL; ?>/js/jquery.ui-1.0/ui.draggable.js" rsf:id="scr=contribute-script"></script>
    <script type="text/javascript" src="<?php echo FLUID_URL; ?>/js/jquery.ui-1.0/ui.droppable.js" rsf:id="scr=contribute-script"></script>
    <script type="text/javascript" src="<?php echo FLUID_URL; ?>/js/fluid/Fluid.js" rsf:id="scr=contribute-script"></script>
    <script type="text/javascript" src="<?php echo FLUID_URL; ?>/js/fluid/Reorderer.js" rsf:id="scr=contribute-script"></script>
<script>
imgMoved(){
int i = document.getElementById('<?php imgID?>');

}

</script>
<?php
/* This file is used to display the index page of photo album for everyone */
require_once ('define.php');
require_once ('classes/pa_index.class.php');
require_once ('HTML/Template/ITX.php');
clear_temp_folder();




$index=new Pa_Index();
unset($_SESSION['pa']);

if ($index->isError()!=true){	//if there is no error in index object, display the index page
	$_SESSION['pa']['course_id']=$index->getVariable('course_id');
	
	/* display index page from here */
	$template=new HTML_Template_ITX("./Template");
	$template->loadTemplatefile("index.tpl.php", true, true);
	
	/* display images */
	$template->setCurrentBlock("IMAGE_START");
	$template->setVariable("IMAGE_PAGE_TITLE", _AT('pa_title_index'));
	
	$template->setVariable("MAIN_URL", BASE_PATH.'index.php');
	$template->setVariable("MAIN_TITLE", _AT('pa_tag_course_photo_alt'));
	
	$template->setVariable("MY_PHOTO_URL", BASE_PATH.'my_photo.php');
	$template->setVariable("MY_PHOTO_TITLE", _AT('pa_tag_my_photo_alt'));
	
	$template->setVariable("MY_COMMENT_URL", BASE_PATH.'my_comment.php');
	$template->setVariable("MY_COMMENT_TITLE", _AT('pa_tag_my_comment_alt'));
	
	
	$image_array=$index->getVariable('image_array');
	for ($i=0; $i < count($image_array); $i++) {
		$template->setCurrentBlock("IMAGE_DISPLAY");
		$template->setVariable("IMAGE_ID",$image_array[$i]['image_id']);
		

$template->setVariable("TABINDEX", $image_array[$i]['order']);
$template->setVariable("MOVED_IMG", $movedarray[$i]);
		$template->setVariable("LINK", $image_array[$i]['link']);
		$count=get_total_comment_number(STUDENT, $index->getVariable('course_id'), APPROVED, $image_array[$i]['image_id']);
		if ($count >0 ){
			$template->setVariable("IMAGE_TITLE", $image_array[$i]['title']." [".$count."]");
		} else {
			$template->setVariable("IMAGE_TITLE", $image_array[$i]['title']);
		}
		$template->setVariable("IMAGE_SRC", $get_file.$image_array[$i]['location'].urlencode($image_array[$i]['thumb_image_name']));
		$template->setVariable("IMAGE_ALT", $image_array[$i]['alt']);
		$template->parseCurrentBlock("IMAGE_DISPLAY");
	}
	
	if ($index->getVariable('show_modification_buttons')==true){
		$template->setCurrentBlock("IMAGE_ADD_BUTTON");
		$template->setVariable("FORM_NAME", "thumb_form");
		$template->setVariable("SAVE_FORM_NAME", "save_form");
		$template->setVariable("ACTION", UPLOAD_ACTION);
		$template->setVariable("SAVE_ACTION", $_SERVER['PHP_SELF']);
		$template->setVariable("ADD_STRING", _AT('pa_button_add_image'));
		$template->setVariable("CHOOSE_VALUE", IMAGE);
		$template->parseCurrentBlock("IMAGE_ADD_BUTTON");
	}

	/* Display page table */
	$page_array=&$index->getVariable('page_array');
	$current=$index->getVariable('current_page');
	if ($index->getVariable('show_page_left_buttons')==true){
		$first_button=_AT('pa_tag_first_page_button');
		$previous_button=_AT('pa_tag_previous_page_button');
		$template->setCurrentBlock("B_DATA_PART");
		$template->setVariable("B_DATA", '<li><a href=\''.BASE_PATH.'index.php?current_page=1\'><img src=\''.FIRST_PAGE_IMAGE.'\' alt=\''.$first_button.'\' width=\'30\' height=\'20\'/></a></li>');
		$template->parseCurrentBlock("B_DATA_PART");
		$template->setCurrentBlock("B_DATA_PART");
		$template->setVariable("B_DATA", '<li><a href=\''.BASE_PATH.'index.php?current_page='.($current-1).'\'><img src=\''.PRE_IMAGE.'\' alt=\''.$previous_button.'\' width=\'30\' height=\'20\'/></a></li>');
		$template->parseCurrentBlock("B_DATA_PART");
	}
	
	for ($i=$page_array['start']; $i<=$page_array['end']; $i++){
		if ($i==$current){
			$template->setCurrentBlock("B_DATA_PART");
			$template->setVariable("B_DATA", '<li class=\'current\'>'.$i.'</li>');
			$template->parseCurrentBlock("B_DATA_PART");
		} else {
			$template->setCurrentBlock("B_DATA_PART");
			$template->setVariable("B_DATA", '<li><a href=\''.BASE_PATH.'index.php?current_page='.$i.'\'>'.$i.'</a></li>');
			$template->parseCurrentBlock("B_DATA_PART");
		}
	}
		
	if ($index->getVariable('show_page_right_buttons')==true){
		$next_button=_AT('pa_tag_next_page_button');
		$last_button=_AT('pa_tag_last_page_button');
		$template->setCurrentBlock("B_DATA_PART");
		$template->setVariable("B_DATA", '<li><a href=\''.BASE_PATH.'index.php?current_page='.($current+1).'\'><img src=\''.NEXT_IMAGE.'\' alt=\''.$next_button.'\' width=\'30\' height=\'20\'/></a></li>');
		$template->parseCurrentBlock("B_DATA_PART");
		$template->setCurrentBlock("B_DATA_PART");
		$template->setVariable("B_DATA", '<li><a href=\''.BASE_PATH.'index.php?current_page='.$page_array['last_page'].'\'><img src=\''.LAST_PAGE_IMAGE.'\' alt=\''.$last_button.'\' width=\'30\' height=\'20\'/></a></li>');
		$template->parseCurrentBlock("B_DATA_PART");
	}
		
	$template->parseCurrentBlock("B_DATA_PART");
	$template->parseCurrentBlock("IMAGE_START");
	$template->parseCurrentBlock();
	$template->show();
} else {
	$msg->addError('pa_obj_pa_index');
	redirect('../../index.php');
}

?>
        <script type="text/javascript"  rsf:id="init-script">
          fluid.initLightbox ("gallery:::gallery-thumbs:::", "message-bundle:");
        </script>


<?php 
debug($movedarray);
debug($_POST);
require_once(AT_INCLUDE_PATH.'footer.inc.php'); ?>
