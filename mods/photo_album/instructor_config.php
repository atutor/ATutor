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
 * @desc	This file generates the config page for instructor panel
 * @author	Dylan Cheon & Kelvin Wong
 * @copyright	2006, Institute for Assistive Technology / University of Victoria 
 * @link	http://www.canassist.ca/                                    
 * @license GNU
 */
define('AT_INCLUDE_PATH', '../../include/');
require (AT_INCLUDE_PATH.'vitals.inc.php');
require (AT_INCLUDE_PATH.'header.inc.php');
?>

<?php
require_once ('define.php');
require_once ('HTML/Template/ITX.php');
require_once ('include/data_func.php');

$_SESSION['pa']['course_id']=$_SESSION['course_id'];
if (isset($_POST['submit']) && (isset($_POST['radiobutton']))){
	if ($_POST['radiobutton']=='make_enabled'){
		modify_config_mode($_SESSION['pa']['course_id'], CONFIG_ENABLED);
	} else if ($_POST['radiobutton']=='make_disabled'){
		modify_config_mode($_SESSION['pa']['course_id'], CONFIG_DISABLED);
	}
}

$template=new HTML_Template_ITX("./Template");
$template->loadTemplateFile("instructor_config.tpl.php");
$template->setVariable("TITLE", _AT('pa_title_instructor_config'));
$template->setVariable("CONFIG_NOTE", _AT('pa_note_instructor_config'));

$mode=get_config_mode($_SESSION['pa']['course_id']);
$template->setVariable("CONFIG_STRING", _AT('pa_tag_config_string'));
if ($mode==CONFIG_ENABLED){
	$template->setVariable("CONFIG_VALUE", _AT('pa_tag_config_enabled'));
	$template->setVariable("CHECKED1", "checked=\"checked\"");
} else {
	$template->setVariable("CONFIG_VALUE", _AT('pa_tag_config_disabled'));
	$template->setVariable("CHECKED2", "checked=\"checked\"");
}

$template->setVariable("FORM_NAME", "config_form");
$template->setVariable("ACTION", $_SERVER['PHP_SELF']);
$template->setVariable("RADIO_VALUE1", "make_enabled");
$template->setVariable("RADIO_STRING1", _AT('yes'));
$template->setVariable("RADIO_VALUE2", "make_disabled");
$template->setVariable("RADIO_STRING2", _AT('no'));
$template->setVariable("SUBMIT_VALUE", _AT('pa_button_config_change'));

$template->show();
?>
<?php require_once(AT_INCLUDE_PATH.'footer.inc.php'); ?>