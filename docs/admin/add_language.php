<?php
/************************************************************************/
/* ATutor																*/
/************************************************************************/
/* Copyright (c) 2002-2004 by Greg Gay, Joel Kronenberg & Heidi Hazelton*/
/* Adaptive Technology Resource Centre / University of Toronto			*/
/* http://atutor.ca														*/
/*																		*/
/* This program is free software. You can redistribute it and/or		*/
/* modify it under the terms of the GNU General Public License			*/
/* as published by the Free Software Foundation.						*/
/************************************************************************/

$page = 'language';
$_user_location = 'admin';

define('AT_INCLUDE_PATH', '../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');
if ($_SESSION['course_id'] > -1) { exit; }
if (!AT_DEVEL_TRANSLATE) { exit; }

if ($_POST['cancel']) {
	Header('Location: language.php?f='.urlencode_feedback(AT_FEEDBACK_CANCELLED));
	exit;
}

if ($_POST['submit']) {

	require_once(AT_INCLUDE_PATH . 'classes/Language/LanguageEditor.class.php'); 
	
	$languageEditor =& new LanguageEditor('');
	$errors = $languageEditor->addLanguage($_POST);

	if (empty($errors)) {			
		Header('Location: language.php?f='.urlencode_feedback(AT_FEEDBACK_LANG_ADDED));
		exit;
	} 
}

require(AT_INCLUDE_PATH.'header.inc.php'); 

echo '<h3>'._AT('add_language').'</h3>';
include(AT_INCLUDE_PATH . 'html/feedback.inc.php');
?>

<br /><form name="form1" method="post" action="admin/add_language.php">
<input type="hidden" name="import" value="1" />
<table cellspacing="1" cellpadding="5" border="0" summary="" width="70%" align="center">
<tr>
	<td align="right"><?php echo _AT('code'); ?>:</td>
	<td align="left"><input name="code" type="text" size="5" value="" /></td>
</tr>
<tr>
	<td align="right"><?php echo _AT('charset'); ?>:</td>
	<td align="left"><input name="charset" type="text" size="31" maxlength="20" value="" /></td>
</tr>
<tr>
	<td align="right"><?php echo _AT('direction'); ?>:</td>
	<td align="left"><input name="direction" type="radio" value="ltr" checked="checked" /><?php echo _AT('ltr'); ?>, <input name="direction" type="radio" value="rtl" /><?php echo _AT('rtl'); ?></td>
</tr>
<tr>
	<td align="right"><?php echo _AT('reg_exp'); ?>:</td>
	<td align="left"><input name="reg_exp" type="text" size="31" value="" /></td>
</tr>
<tr>
	<td align="right"><?php echo _AT('name_in_language'); ?>:</td>
	<td align="left"><input name="native_name" type="text" size="31" maxlength="20" value="" /></td>
</tr>
<tr>
	<td align="right"><?php echo _AT('name_in_english'); ?>:</td>
	<td align="left"><input name="english_name" type="text" size="31" maxlength="20" value="" /></td>
</tr>

<tr>
	<td colspan="2" align="center"><input type="submit" name="submit" value="<?php echo _AT('submit'); ?>" class="button" /> - <input type="submit" name="cancel" value="<?php echo _AT('cancel'); ?>" class="button" /><br /><br /></td>
</tr>
</table>
</form>

<?php require(AT_INCLUDE_PATH.'footer.inc.php');  ?>