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

if (AT_DEVEL_TRANSLATE == 1) { 
	$msg->addWarning('TRANSLATE_ON');	
}

require_once(AT_INCLUDE_PATH.'classes/Language/LanguageEditor.class.php');
require_once(AT_INCLUDE_PATH.'classes/Language/LanguagesParser.class.php');

if (isset($_POST['delete'])) {
	// check if this language is the only one that exists:
	if ($languageManager->getNumLanguages() == 1) {
		$msg->addError('LAST_LANGUAGE');
	} else {
		header('Location: language_delete.php?lang_code='.$_POST['lang_code']);
		exit;
	}
} else if (isset($_POST['export'])) {
	$language =& $languageManager->getLanguage($_POST['lang_code']);
	if ($language === FALSE) {
		$msg->addError('LANG_NOT_FOUND');
	} else {
		$languageEditor =& new LanguageEditor($language);
		$languageEditor->export();
	}
} else if (isset($_POST['edit'])) {
	header('Location: language_edit.php?lang_code='.$_POST['lang_code']);
	exit;
}

$button_state = '';
if (AT_DEVEL_TRANSLATE == 0) {
	$button_state = 'disabled="disabled"';
}

require(AT_INCLUDE_PATH.'header.inc.php'); 
?>

<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
<div class="input-form">
	<h3><?php echo _AT('manage_languages'); ?></h3>
	<div class="row">
		<p><?php echo _AT('manage_lang_howto'); ?></p>
	</div>

	<div class="row buttons">
		<?php $languageManager->printDropdown($code, 'lang_code', 'lang_code'); ?> 
		<?php if (defined('AT_DEVEL_TRANSLATE') && AT_DEVEL_TRANSLATE): ?>
			<input type="submit" name="edit" value="<?php echo _AT('edit'); ?>" />  
			<input type="submit" name="export" value="<?php echo _AT('export'); ?>"  /> 
			<input type="submit" name="delete" value="<?php echo _AT('delete'); ?>" /> 
			<?php echo _AT('or'); ?> <a href="admin/language_add.php"><?php echo _AT('add_a_new_language'); ?></a>
		<?php else: ?>
			<input type="submit" name="export" value="<?php echo _AT('export'); ?>" /> 
			<input type="submit" name="delete" value="<?php echo _AT('delete'); ?>" />
		<?php endif; ?>
	</div>
</div>
</form>

<form name="form1" method="post" action="admin/language_import.php" enctype="multipart/form-data" onsubmit="openWindow('<?php echo $_base_href; ?>tools/prog.php');">

<div class="input-form">
	<div class="row">
		<h3><?php echo _AT('import_a_new_lang'); ?></h3>
	</div>

	<div class="row">
		<p><?php echo _AT('import_lang_howto'); ?></p>
	</div>
	
	<div class="row">
		<label for="file"><?php echo _AT('import_a_new_lang'); ?></label><br />
		<input type="file" name="file" id="file" />
	</div>
	
	<div class="row buttons">
		<input type="submit" name="submit" value="<?php echo _AT('import'); ?>" />
	</div>

	<div class="row">
		<?php echo _AT('import_remote_language'); ?><br />
		<?php
				require_once(AT_INCLUDE_PATH.'classes/Language/RemoteLanguageManager.class.php');
				$remoteLanguageManager =& new RemoteLanguageManager();
				if ($remoteLanguageManager->getNumLanguages()) {
					$remoteLanguageManager->printDropdown('', 'language', 'id');
					echo '<input type="submit" name="submit_import" value="' . _AT('import') . '" class="button" />';
				} else {
					echo _AT('cannot_find_remote_languages');
				}

		?>
	</div>
</div>
</form>

<form method="get">
<div class="input-form">
	<div class="row">
		<h3><?php echo _AT('translate'); ?></h3>
	</div>

	<div class="row">
		<p><?php echo _AT('translate_lang_howto'); ?></p>
	</div>

	<div class="row buttons">
		<input type="button" onclick="javascript:window.open('<?php echo $_base_href; ?>admin/translate_atutor.php', 'newWin1', 'toolbar=0, location=0, directories=0, status=0, menubar=0, scrollbars=1, resizable=1, copyhistory=0, width=640, height=480')" value="<?php echo _AT('translate'); ?>" <?php echo $button_state; ?> />
	</div>
</div>
</form>

<?php require(AT_INCLUDE_PATH.'footer.inc.php'); ?>