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

require_once(AT_INCLUDE_PATH.'classes/Language/LanguageEditor.class.php');
require_once(AT_INCLUDE_PATH.'classes/Language/LanguagesParser.class.php');

if (isset($_POST['delete'])) {
	// check if this language is the only one that exists:
	if ($languageManager->getNumLanguages() == 1) {
		$msg->addError('LAST_LANGUAGE');
	} else {
		header('Location: language_delete.php?lang_code='.$_POST['id']);
		exit;
	}
} else if (isset($_POST['export'])) {
	$language =& $languageManager->getLanguage($_POST['id']);
	if ($language === FALSE) {
		$msg->addError('LANG_NOT_FOUND');
	} else {
		$languageEditor =& new LanguageEditor($language);
		$languageEditor->export();
	}
} else if (isset($_POST['edit'])) {
	header('Location: language_edit.php?lang_code='.$_POST['id']);
	exit;
}

if (AT_DEVEL_TRANSLATE == 1) { 
	$msg->addWarning('TRANSLATE_ON');	
}

require(AT_INCLUDE_PATH.'header.inc.php');
?>

<form name="form" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">

<table summary="" class="data" rules="cols">
<colgroup>
	<col />
	<col class="sort" />
	<col span="3" />
</colgroup>
<thead>
<tr>
	<th scope="col">&nbsp;</th>
	<th scope="col"><?php echo _AT('name_in_language'); ?></th>
	<th scope="col"><?php echo _AT('name_in_english'); ?></th>
	<th scope="col"><?php echo _AT('lang_code'); ?></th>
	<th scope="col"><?php echo _AT('charset'); ?></th>
</tr>
</thead>
<tfoot>
<tr>
	<td colspan="5">
		<?php if (defined('AT_DEVEL_TRANSLATE') && AT_DEVEL_TRANSLATE): ?>
			<input type="submit" name="edit" value="<?php echo _AT('edit'); ?>" />  
			<input type="submit" name="export" value="<?php echo _AT('export'); ?>"  /> 
			<input type="submit" name="delete" value="<?php echo _AT('delete'); ?>" /> 
			<?php echo _AT('or'); ?> <a href="admin/language_add.php"><?php echo _AT('add_a_new_language'); ?></a>
		<?php else: ?>
			<input type="submit" name="export" value="<?php echo _AT('export'); ?>" /> 
			<input type="submit" name="delete" value="<?php echo _AT('delete'); ?>" />
		<?php endif; ?>
	</td>
</tr>
</tfoot>
<tbody>
	<?php foreach ($languageManager->getAvailableLanguages() as $codes): ?>
		<?php $language = current($codes); ?>
		<tr onmousedown="document.form['m<?php echo $language->getCode(); ?>'].checked = true; rowselect(this);" id="r_<?php echo $language->getCode(); ?>">
			<td><input type="radio" name="id" value="<?php echo $language->getCode(); ?>" id="m<?php echo $language->getCode(); ?>" /></td>
			<td><label for="m<?php echo $language->getCode(); ?>"><?php echo $language->getNativeName(); ?></label></td>
			<td><?php echo $language->getEnglishName(); ?></td>
			<td><?php echo strtolower($language->getCode()); ?></td>
			<td><?php echo strtolower($language->getCharacterSet()); ?></td>
		</tr>
	<?php endforeach; ?>
</tbody>
</table>
</form>

<?php require(AT_INCLUDE_PATH.'footer.inc.php'); ?>