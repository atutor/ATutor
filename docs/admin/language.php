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

$code = $_REQUEST['lang_code'];

if (isset($_POST['cancel'])) {
	Header('Location: language.php?lang_code='.$code.SEP.'f='.urlencode_feedback(AT_FEEDBACK_CANCELLED));
	exit;
}

if (isset($_POST['delete'])) {
	Header('Location: delete_lang.php?delete_lang='.$code);
	exit;
}

if (isset($_POST['export'])) {
	
	$languageEditor =& new LanguageEditor($lang);
	$errors = $languageEditor->export();

	if (!isset($errors)) {			
		Header('Location: language.php?lang_code='.$_POST['code'].SEP.'f='.urlencode_feedback(AT_FEEDBACK_LANG_UPDATED));
		exit;
	} 
}

if (isset($_POST['submit'])) {
	
	$languageEditor =& new LanguageEditor($lang);
	$errors = $languageEditor->updateLanguage($_POST);

	if (!isset($errors)) {			
		Header('Location: language.php?lang_code='.$_POST['code'].SEP.'f='.urlencode_feedback(AT_FEEDBACK_LANG_UPDATED));
		exit;
	} 
}

require(AT_INCLUDE_PATH.'header.inc.php'); 

echo '<h3>'._AT('language').'</h3>';
include(AT_INCLUDE_PATH . 'html/feedback.inc.php');
?>

<form name="form1" method="post" action="admin/language.php">

<table cellspacing="1" cellpadding="0" border="0" class="bodyline" width="80%" summary="" align="center">

<?php 
if (!isset($_POST['edit'])) { 
	
	if (AT_DEVEL_TRANSLATE) { ?>
	<tr>
		<td class="cyan" colspan="2"><?php echo _AT('manage_languages'); ?></td>
	</tr>
	<tr>
		<td colspan="2"><small><?php echo _AT('manage_lang_howto'); ?></small>
		
		<p><br /><a href="admin/add_language.php"><?php echo _AT('add_language'); ?></a></p>
		<?php } ?>

		<p align="center"><?php $languageManager->printDropdown($code, 'lang_code', 'lang_code'); ?> <input type="submit" name="edit" value="<?php echo _AT('edit'); ?>" class="button" /> | <input type="submit" name="delete" value="<?php echo _AT('delete'); ?>" class="button" /> | <input type="submit" name="export" value="<?php echo _AT('export'); ?>" class="button" /></p>	
		</td>
	</tr>
</table>

<?php
} else {
	$lang =& $languageManager->getLanguage($code);
?>
	<input type="hidden" name="old_code" value="<?php echo $lang->getCode();?>" />

	<p align="center"><strong><?php echo _AT('edit').' '.$lang->getEnglishName().'/'.$lang->getNativeName();?></strong></p>

	<table cellspacing="1" cellpadding="5" border="0" summary="" align="center">
	<tr>
		<td align="right"><?php echo _AT('code'); ?>:</td>
		<td align="left"><input name="code" type="text" size="5" value="<?php echo $lang->getCode();?>" /></td>
	</tr>
	<tr>
		<td align="right"><?php echo _AT('locale'); ?>:</td>
		<td align="left"><input name="locale" type="text" size="5" value="" /></td>
	</tr>
	<tr>
		<td align="right"><?php echo _AT('charset'); ?>:</td>
		<td align="left"><input name="charset" type="text" size="31" maxlength="20" value="<?php echo $lang->getCharacterSet();?>" /></td>
	</tr>
	<tr>
		<td align="right"><?php echo _AT('direction'); ?>:</td>
		<?php 
			if ($lang->getDirection == 'rtl') { $rtl = 'checked="checked"';  $ltr='';  } 
			else { $rtl = '';  $ltr='checked="checked"'; }
		?>
		<td align="left"><input name="direction" type="radio" value="ltr" <?php echo $ltr; ?> /><?php echo _AT('ltr'); ?>, <input name="direction" type="radio" value="rtl" <?php echo $rtl; ?> /><?php echo _AT('rtl'); ?></td>
	</tr>
	<tr>
		<td align="right"><?php echo _AT('reg_exp'); ?>:</td>
		<td align="left"><input name="reg_exp" type="text" size="31" value="<?php echo $lang->getRegularExpression();?>" /></td>
	</tr>
	<tr>
		<td align="right"><?php echo _AT('name_in_language'); ?>:</td>
		<td align="left"><input name="native_name" type="text" size="31" maxlength="20" value="<?php echo $lang->getNativeName();?>" /></td>
	</tr>
	<tr>
		<td align="right"><?php echo _AT('name_in_english'); ?>:</td>
		<td align="left"><input name="english_name" type="text" size="31" maxlength="20" value="<?php echo $lang->getEnglishName();?>" /></td>
	</tr>
	<tr>
		<td align="center" colspan="2"><br /><input type="submit" name="submit" value="<?php echo _AT('submit'); ?>" class="button" /> | <input type="submit" name="cancel" value="<?php echo _AT('cancel'); ?>" class="button" />
		</td>
	</tr>
	</table>
<?php } ?>
</form>


<?php if (!isset($_POST['edit'])) {  ?>
	<br /><form name="form1" method="post" action="admin/import_lang.php" enctype="multipart/form-data" onsubmit="openWindow('<?php echo $_base_href; ?>tools/prog.php');">
	<input type="hidden" name="import" value="1" />
	<table cellspacing="1" cellpadding="0" border="0" class="bodyline" width="80%" summary="" align="center">
	<tr>
		<td class="cyan" colspan="2"><?php echo _AT('import_a_new_lang'); ?></td>
	</tr>
	<tr>
		<td colspan="2"><small><?php echo _AT('import_lang_howto'); ?></small></td>
	</tr>
	<tr>
		<td align="center" colspan="2"><br /><strong><?php echo _AT('import_a_new_lang'); ?></strong>: <input type="file" name="file" class="formfield" /> | <input type="submit" name="submit" value="<?php echo _AT('import'); ?>" class="button" /><br /><br /></td>
	</tr>
	</table>
</form>
<?php } ?>


<?php require(AT_INCLUDE_PATH.'footer.inc.php'); ?>