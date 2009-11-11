<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2008 by Greg Gay & Joel Kronenberg        */
/* Adaptive Technology Resource Centre / University of Toronto  */
/* http://atutor.ca												*/
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.				*/
/****************************************************************/
// $Id$

define('AT_INCLUDE_PATH', '../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');
admin_authenticate(AT_ADMIN_PRIV_LANGUAGES);

if (isset($_POST['svn_submit'])) {
	$languageManager->liveImport($addslashes($_POST['import_lang']));
	header('Location: '.$_SERVER['PHP_SELF']);
	exit;
}

require(AT_INCLUDE_PATH.'header.inc.php');


$button_state = '';
if (!defined('AT_DEVEL_TRANSLATE') || !AT_DEVEL_TRANSLATE) {
	$button_state = 'disabled="disabled"';
}

?>

<form method="get">
<div class="input-form">
	<div class="row">
		<h3><?php echo _AT('translate'); ?></h3>
	</div>

	<div class="row">
		<p><?php echo _AT('translate_lang_howto'); ?></p>
	</div>

	<div class="row buttons">
		<input type="button" onclick="javascript:window.open('<?php echo AT_BASE_HREF; ?>admin/translate_atutor.php', 'newWin1', 'toolbar=0, location=0, directories=0, status=0, menubar=0, scrollbars=1, resizable=1, copyhistory=0, width=640, height=480')" value="<?php echo _AT('translate'); ?>" <?php echo $button_state; ?> />
	</div>
</div>
</form>

<?php if (defined('AT_DEVEL_TRANSLATE') && AT_DEVEL_TRANSLATE): ?>
	<form name="form1" method="post" action="admin/language_translate.php">
		<div class="input-form">
			<div class="row">
				Import partial language from the <em>live ATutor language database</em> to your local installation for translating.
			</div>
			<div class="row">
				<?php
					require_once(AT_INCLUDE_PATH.'classes/Language/RemoteLanguageManager.class.php');
					$remoteLanguageManager = new RemoteLanguageManager();
					$remoteLanguageManager->printDropdown($_SESSION['lang'], 'import_lang', 'import_lang');
				?>
			</div>

			<div class="row buttons">
				<input type="submit" name="svn_submit" value="<?php echo _AT('import'); ?>" />
			</div>
		</div>
	</form>
<?php endif; ?>

<?php require(AT_INCLUDE_PATH.'footer.inc.php'); ?>