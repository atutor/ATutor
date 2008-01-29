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
 * @desc	This file generates the index page in the administrator panel
 * @author	Dylan Cheon & Kelvin Wong
 * @copyright	2006, Institute for Assistive Technology / University of Victoria 
 * @link	http://www.canassist.ca/                                    
 * @license GNU
 */
 
define('AT_INCLUDE_PATH', '../../include/');
require (AT_INCLUDE_PATH.'vitals.inc.php');
admin_authenticate(AT_ADMIN_PRIV_PHOTO_ALBUM);
require (AT_INCLUDE_PATH.'header.inc.php');
?>

<?php
require_once('define.php');
require_once('HTML/Template/ITX.php');
require_once('include/data_func.php');
require_once('include/general_func.php');

$courses=get_course_list();
if (empty($courses)){
	$msg->addError('pa_var_course_empty');
	redirect('../../admin/index.php');
} else {
	$link=$base_path.'mods/photo_album/admin_image_list.php';
	$template=new HTML_Template_ITX("./Template");
	$template->loadTemplatefile("index_admin.tpl.php", true, true);

	$template->setVariable("TITLE", _AT('pa_title_admin_index'));	
	$template->setVariable("MESSAGE", _AT('pa_note_admin'));
	$template->setVariable("FORM_NAME", "photo_form");
	$template->setVariable("FORM_ACTION", $link);
	$template->setVariable("SELECT_NAME", "course_id");
	
	for ($i=0; $i<count($courses); $i++){
		$template->setCurrentBlock("OPTION_VALUE");
		$template->setVariable("TABINDEX", $i);
		$template->setVariable("VALUE", $courses[$i]['id']);
		$template->setVariable("TEXT", $courses[$i]['title']);
		$template->parseCurrentBlock("OPTION_VALUE");
	}

	$template->setVariable("SUBMIT_NAME", "submit");
	$template->setVariable("SUBMIT_VALUE", _AT('pa_tag_go'));
	
	$template->parseCurrentBlock();
	$template->show();	
}
?>
<?php require (AT_INCLUDE_PATH.'footer.inc.php'); ?>