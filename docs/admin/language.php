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
//debug($_POST);
if (isset($_POST['cancel'])) {
	Header('Location: language.php?lang_code='.$code.SEP.'f='.urlencode_feedback(AT_FEEDBACK_CANCELLED));
	exit;
}

if (isset($_POST['delete'])) {
	Header('Location: delete_lang.php?delete_lang='.$code);
	exit;
}

if (isset($_POST['export'])) {	
	$languageEditor =& new LanguageEditor($code);
	$errors = $languageEditor->export();

	if (!isset($errors)) {			
		Header('Location: language.php?lang_code='.$_POST['code'].SEP.'f='.urlencode_feedback(AT_FEEDBACK_LANG_UPDATED));
		exit;
	} 
}

if (isset($_POST['submit'])) {

	if (isset($_POST['add'])) {
		if ($languageManager->exists($code)) {
			$languageEditor =& new LanguageEditor($code);
			$errors = $languageEditor->addLanguage($_POST);

			if (!isset($errors)) {			
				Header('Location: language.php?lang_code='.$_POST['code'].SEP.'f='.urlencode_feedback(AT_FEEDBACK_LANG_ADDED));
				exit;
			} 
		} else {
			$errors[] = AT_ERROR_LANG_EXISTS;
		}
	} else {

		if (!$languageManager->exists($code)) {
			
			$languageEditor =& new LanguageEditor($code);
			$errors = $languageEditor->updateLanguage($_POST);

			if (!isset($errors)) {			
				Header('Location: language.php?lang_code='.$code.SEP.'f='.urlencode_feedback(AT_FEEDBACK_LANG_UPDATED));
				exit;
			} 
		} else {
			$errors[] = AT_ERROR_LANG_NOT_FOUND;
		}
	}
}

require(AT_INCLUDE_PATH.'header.inc.php'); 

echo '<h3>'._AT('language').'</h3>';
include(AT_INCLUDE_PATH . 'html/feedback.inc.php');
?>

<form name="form1" method="post" action="admin/language.php">

<table cellspacing="1" cellpadding="0" border="0" class="bodyline" width="80%" summary="" align="center">
	<tr>
		<td class="cyan" colspan="2"><?php echo _AT('manage_languages'); ?></td>
	</tr>
	<tr>
		<td colspan="2"><small><?php echo _AT('manage_lang_howto'); ?></small></td>
	</tr>

<?php if (!isset($_POST['edit']) && !$_REQUEST['add'] && !isset($_POST['submit'])) { 

	if (AT_DEVEL_TRANSLATE) { ?>
	<tr>
		<td><br /><a href="admin/language.php?add=1"><?php echo _AT('add_language'); ?></a></p></td>
	</tr>
	<?php } ?>
	<tr><td>
		<p align="center"><?php $languageManager->printDropdown($code, 'lang_code', 'lang_code'); ?> <input type="submit" name="edit" value="<?php echo _AT('edit'); ?>" class="button" /> | <input type="submit" name="delete" value="<?php echo _AT('delete'); ?>" class="button" /> | <input type="submit" name="export" value="<?php echo _AT('export'); ?>" class="button" /></p>	
		</td>
	</tr>

<?php
} else {

	if (isset($_POST['edit'])) {
		$lang =& $languageManager->getLanguage($code);
		$lang_code	= explode('_', $lang->getCode());
		$lang_code	= $lang_code[0];
		$locale		= $lang_code[1];
		$charset	= $lang->getCharacterSet();
		$direction	= $lang->getDirection();
		$reg_exp	= $lang->getRegularExpression();
		$native_name= $lang->getNativeName();
		$eng_name	= $lang->getEnglishName();

	} else {
		$lang_code	= $_POST['code'];
		$locale		= $_POST['locale'];;
		$charset	= $_POST['charset'];;
		$direction	= $_POST['direction'];
		$reg_exp	= $_POST['reg_exp'];
		$native_name= $_POST['native_name'];
		$eng_name	= $_POST['english_name'];
	}
?>
<tr><td>
	<input type="hidden" name="old_code" value="<?php echo $lang_code;?>" />

	<p align="center"><strong><?php 
	if (isset($_POST['edit'])) {	
		echo _AT('edit').' '.$eng_name.'/'.$native_name;
	} else {
		echo _AT('add_language');
		echo '<input type="hidden" name="add" value="1" />';
	}
	?></strong></p>

	<table cellspacing="1" cellpadding="5" border="0" summary="" align="center">
	<tr>
		<td align="right"><label for="code"><?php echo _AT('code'); ?>:</label></td>
		<td align="left"><input id="code" name="code" type="text" size="2" maxlength="2" value="<?php echo $lang_code;?>" /></td>
	</tr>
	<tr>
		<td align="right"><label for="locale"><?php echo _AT('locale'); ?>:</label></td>
		<td align="left"><input id="locale" name="locale" type="text" size="2" maxlength="2" value="<?php echo $locale;?>" /></td>
	</tr>
	<tr>
		<td align="right"><label for="charset"><?php echo _AT('charset'); ?>:</label></td>
		<td align="left"><input id="charset" name="charset" type="text" size="31" maxlength="20" value="<?php echo $charset;?>" /></td>
	</tr>
	<tr>
		<td align="right"><label for="ltr"><?php echo _AT('direction'); ?>:</label></td>
		<?php 
			if ($direction == 'rtl') { $rtl = 'checked="checked"';  $ltr='';  } 
			else { $rtl = '';  $ltr='checked="checked"'; }
		?>
		<td align="left"><input id="ltr" name="direction" type="radio" value="ltr" <?php echo $ltr; ?> /><label for="ltr"><?php echo _AT('ltr'); ?></label>, <input id="rtl" name="direction" type="radio" value="rtl" <?php echo $rtl; ?> /><label for="rtl"><?php echo _AT('rtl'); ?></label></td>
	</tr>
	<tr>
		<td align="right"><label for="reg_exp"><?php echo _AT('reg_exp'); ?>:</label></td>
		<td align="left"><input id="reg_exp" name="reg_exp" type="text" size="31" value="<?php echo $reg_exp; ?>" /></td>
	</tr>
	<tr>
		<td align="right"><label for="nname"><?php echo _AT('name_in_language'); ?>:</label></td>
		<td align="left"><input id="nname" name="native_name" type="text" size="31" maxlength="20" value="<?php echo $native_name; ?>" /></td>
	</tr>
	<tr>
		<td align="right"><label for="ename"><?php echo _AT('name_in_english'); ?>:</label></td>
		<td align="left"><input id="ename" name="english_name" type="text" size="31" maxlength="20" value="<?php echo $eng_name;?>" /></td>
	</tr>
	<tr>
		<td align="center" colspan="2"><br /><input type="submit" name="submit" value="<?php echo _AT('submit'); ?>" class="button" /> | <input type="submit" name="cancel" value="<?php echo _AT('cancel'); ?>" class="button" />
		</td>
	</tr>
	</table>
	</td></tr>
<?php } ?>
</form>
</table>

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



<?php require(AT_INCLUDE_PATH.'footer.inc.php'); ?>