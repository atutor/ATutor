<?php
/************************************************************************/
/* ATutor																*/
/************************************************************************/
/* Copyright (c) 2002-2005 by Greg Gay, Joel Kronenberg & Heidi Hazelton*/
/* Adaptive Technology Resource Centre / University of Toronto			*/
/* http://atutor.ca														*/
/*																		*/
/* This program is free software. You can redistribute it and/or		*/
/* modify it under the terms of the GNU General Public License			*/
/* as published by the Free Software Foundation.						*/
/************************************************************************/
// $Id$

$page = 'language';
$_user_location = 'admin';

define('AT_INCLUDE_PATH', '../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');
if ($_SESSION['course_id'] > -1) { exit; }

require_once(AT_INCLUDE_PATH.'classes/Language/LanguageEditor.class.php');
require_once(AT_INCLUDE_PATH.'classes/Language/LanguagesParser.class.php');
require_once(AT_INCLUDE_PATH.'classes/Message/Message.class.php');

global $savant;
$msg =& new Message($savant);

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
} else if (isset($_POST['translate'])) {
	header('Location: translate_atutor.php?lang_code='.$_POST['lang_code']);
	exit;
}

require(AT_INCLUDE_PATH.'header.inc.php'); 

echo '<h3>'._AT('language').'</h3>';

$msg->printAll();
?>
<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
	<table cellspacing="1" cellpadding="0" border="0" class="bodyline" width="80%" summary="" align="center">
		<tr>
			<th class="cyan"><?php echo _AT('manage_languages'); ?></th>
		</tr>
		<tr>
			<td colspan="2" class="row1"><small><?php echo _AT('manage_lang_howto'); ?></small></td>
		</tr>
		<tr><td height="1" class="row2"></td></tr>
		<?php if (defined('AT_DEVEL_TRANSLATE') && AT_DEVEL_TRANSLATE): ?>
			<tr><td height="1" class="row2"></td></tr>
			<tr>
				<td align="center" class="row1"><?php 
						$languageManager->printDropdown($code, 'lang_code', 'lang_code'); 
				?> 
						<input type="submit" name="edit" value="<?php echo _AT('edit'); ?>" class="button" /> - 
						<input type="submit" name="translate" value="<?php echo _AT('translate'); ?>" class="button" /> -
						<input type="submit" name="export" value="<?php echo _AT('export'); ?>" class="button" /> -
						<input type="submit" name="delete" value="<?php echo _AT('delete'); ?>" class="button" /> - 
						<?php echo _AT('or'); ?> <a href="admin/language_add.php"><?php echo _AT('add_a_new_language'); ?></a></td>
			</tr>
		<?php else: ?>
			<tr><td height="1" class="row2"></td></tr>
			<tr>
				<td align="center" class="row1"><?php $languageManager->printDropdown($code, 'lang_code', 'lang_code'); ?> <input type="submit" name="export" value="<?php echo _AT('export'); ?>" class="button" /> | <input type="submit" name="delete" value="<?php echo _AT('delete'); ?>" class="button" /></td>
			</tr>
		<?php endif; ?>
	</table>
</form>

<br />
<form name="form1" method="post" action="admin/language_import.php" enctype="multipart/form-data" onsubmit="openWindow('<?php echo $_base_href; ?>tools/prog.php');">
	<table cellspacing="1" cellpadding="0" border="0" class="bodyline" width="80%" summary="" align="center">
	<tr>
		<th class="cyan"><?php echo _AT('import_a_new_lang'); ?></th>
	</tr>
	<tr><td height="1" class="row2"></td></tr>
	<tr>
		<td class="row1" colspan="2"><small><?php echo _AT('import_lang_howto'); ?><br /><strong><?php echo _AT('import_a_new_lang'); ?>:</strong> <input type="file" name="file" class="formfield" /> <input type="submit" name="submit" value="<?php echo _AT('import'); ?>" class="button" /></small></td>
	</tr>
	<tr><td height="1" class="row2"></td></tr>
	<tr><td height="1" class="row2"></td></tr>
	<tr>
		<td class="row1" colspan="2"><small><?php echo _AT('import_remote_language'); ?> <?php
				require_once(AT_INCLUDE_PATH.'classes/Language/RemoteLanguageManager.class.php');
				$remoteLanguageManager =& new RemoteLanguageManager();
				if ($remoteLanguageManager->getNumLanguages()) {
					$remoteLanguageManager->printDropdown('', 'language', 'id');
					echo '<input type="submit" name="submit_import" value="' . _AT('import') . '" class="button" />';
				} else {
					echo _AT('cannot_find_remote_languages');
				}

		?></small></td>
	</tr>
	</table>
</form>

<?php require(AT_INCLUDE_PATH.'footer.inc.php'); ?>