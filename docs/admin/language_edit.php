<?php
/************************************************************************/
/* ATutor																*/
/************************************************************************/
/* Copyright (c) 2002-2006 by Greg Gay, Joel Kronenberg & Heidi Hazelton*/
/* Adaptive Technology Resource Centre / University of Toronto			*/
/* http://atutor.ca														*/
/*																		*/
/* This program is free software. You can redistribute it and/or		*/
/* modify it under the terms of the GNU General Public License			*/
/* as published by the Free Software Foundation.						*/
/************************************************************************/
// $Id$

define('AT_INCLUDE_PATH', '../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');
admin_authenticate(AT_ADMIN_PRIV_LANGUAGES);
if (!AT_DEVEL_TRANSLATE) { exit; }

require_once(AT_INCLUDE_PATH . 'classes/Language/LanguageEditor.class.php'); 

$lang =& $languageManager->getLanguage($_GET['lang_code']);
if ($lang === FALSE) {
	require(AT_INCLUDE_PATH.'header.inc.php'); 
	echo '<h3>'._AT('edit_language').'</h3>';
	$msg->addError('NO_LANGUAGE');
	
	$msg->printAll();

	require(AT_INCLUDE_PATH.'footer.inc.php'); 
	exit;
}

if (isset($_POST['cancel'])) {
	$msg->addFeedback('CANCELLED');
	header('Location: language.php');
	exit;
} else if (isset($_POST['submit'])) {
	$languageEditor =& new LanguageEditor($_GET['lang_code']);
	$state = $languageEditor->updateLanguage($_POST, $languageManager->exists($_POST['code'], $_POST['locale']));

	if (!$msg->containsErrors() && $state !== FALSE) {
		$msg->addFeedback('LANG_UPDATED');
		header('Location: language.php');
		exit;
	}
}

require(AT_INCLUDE_PATH.'header.inc.php'); 


$msg->printAll();

if (!isset($_POST['submit'])) {
	$_POST['code']         = $lang->getParentCode();
	$_POST['locale']       = $lang->getLocale();
	$_POST['charset']      = $lang->getCharacterSet();
	$_POST['direction']    = $lang->getDirection();
	$_POST['reg_exp']      = $lang->getRegularExpression();
	$_POST['native_name']  = $lang->getNativeName();
	$_POST['english_name'] = $lang->getEnglishName();
}

?>

<form method="post" action="<?php echo $_SERVER['PHP_SELF'] . '?lang_code=' . $_GET['lang_code']; ?>">

<input type="hidden" name="old_code" value="<?php echo $lang->getCode(); ?>" />

<div class="input-form" style="width:60%">
	<div class="row">
		<div class="required" title="<?php echo _AT('required_field'); ?>">*</div><label for="code"><?php echo _AT('lang_code'); ?></label><br />
		<input id="code" name="code" type="text" size="2" maxlength="2" class="formfield" value="<?php echo $_POST['code']; ?>" />
	</div>

	<div class="row">
		<label for="locale"><?php echo _AT('locale'); ?></label><br />
		<input id="locale" name="locale" type="text" size="2" maxlength="2" class="formfield" value="<?php echo $_POST['locale']; ?>" />
	</div>

	<div class="row">
		<div class="required" title="<?php echo _AT('required_field'); ?>">*</div><label for="charset"><?php echo _AT('charset'); ?></label><br />
		<input id="charset" name="charset" type="text" size="31" maxlength="20" class="formfield" value="<?php echo $_POST['charset']; ?>" />
	</div>

	<div class="row">
		<label for="ltr"><?php echo _AT('direction'); ?></label><br />
		<?php 
			if ($_POST['direction'] == 'rtl') { 
				$rtl = 'checked="checked"';  
				$ltr='';  
			} else { 
				$rtl = '';  
				$ltr='checked="checked"'; 
			}
		?>
		<input id="ltr" name="direction" type="radio" value="ltr" <?php echo $ltr; ?> /><label for="ltr"><?php echo _AT('ltr'); ?></label>, <input id="rtl" name="direction" type="radio" value="rtl" <?php echo $rtl; ?> /><label for="rtl"><?php echo _AT('rtl'); ?></label>
	</div>

	<div class="row">
		<div class="required" title="<?php echo _AT('required_field'); ?>">*</div><label for="reg_exp"><?php echo _AT('reg_exp'); ?></label><br />
		<input id="reg_exp" name="reg_exp" type="text" size="31" class="formfield" value="<?php echo $_POST['reg_exp']; ?>" />
	</div>

	<div class="row">
		<div class="required" title="<?php echo _AT('required_field'); ?>">*</div><label for="nname"><?php echo _AT('name_in_language'); ?></label><br />
		<input id="nname" name="native_name" type="text" size="31" maxlength="20" class="formfield" value="<?php echo $_POST['native_name']; ?>" />
	</div>

	<div class="row">
		<div class="required" title="<?php echo _AT('required_field'); ?>">*</div><label for="ename"><?php echo _AT('name_in_english'); ?></label><br />
		<input id="ename" name="english_name" type="text" size="31" maxlength="20" class="formfield" value="<?php echo $_POST['english_name'];?>" />
	</div>


	<div class="row buttons">
		<input type="submit" name="submit" value="<?php echo _AT('submit'); ?>" /> <input type="submit" name="cancel" value="<?php echo _AT('cancel'); ?>" />		
	</div>
</div>
</form>

<?php require(AT_INCLUDE_PATH.'footer.inc.php');  ?>