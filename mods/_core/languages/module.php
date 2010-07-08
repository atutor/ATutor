<?php
if (!defined('AT_INCLUDE_PATH')) { exit; }
if (!isset($this) || (isset($this) && (strtolower(get_class($this)) != 'module'))) { exit(__FILE__ . ' is not a Module'); }

define('AT_ADMIN_PRIV_LANGUAGES', $this->getAdminPrivilege());

if (admin_authenticate(AT_ADMIN_PRIV_LANGUAGES, TRUE) || admin_authenticate(AT_ADMIN_PRIV_ADMIN, TRUE)) {
	if (admin_authenticate(AT_ADMIN_PRIV_ADMIN, TRUE)) {
		$this->_pages['admin/config_edit.php']['children']  = array('mods/_core/languages/language.php');
		$this->_pages['mods/_core/languages/language.php']['parent'] = 'admin/config_edit.php';
	} else {
		$this->_pages[AT_NAV_ADMIN] = array('mods/_core/languages/language.php');
		$this->_pages['mods/_core/languages/language.php']['parent'] = AT_NAV_ADMIN;
	}

	//admin
	$this->_pages['mods/_core/languages/language.php']['title_var'] = 'languages';
	$this->_pages['mods/_core/languages/language.php']['guide']     = 'admin/?p=languages.php';
	$this->_pages['mods/_core/languages/language.php']['children']  = array('mods/_core/languages/language_import.php', 'mods/_core/languages/language_editor.php', 'mods/_core/languages/language_translate.php');

		$this->_pages['mods/_core/languages/language_add.php']['title_var'] = 'add_language';
		$this->_pages['mods/_core/languages/language_add.php']['parent']    = 'mods/_core/languages/language.php';

		$this->_pages['mods/_core/languages/language_edit.php']['title_var'] = 'edit_language';
		$this->_pages['mods/_core/languages/language_edit.php']['parent']    = 'mods/_core/languages/language.php';

		$this->_pages['mods/_core/languages/language_delete.php']['title_var'] = 'delete_language';
		$this->_pages['mods/_core/languages/language_delete.php']['parent']    = 'mods/_core/languages/language.php';

	$this->_pages['mods/_core/languages/language_import.php']['title_var'] = 'import';
	$this->_pages['mods/_core/languages/language_import.php']['parent']    = 'mods/_core/languages/language.php';

	$this->_pages['mods/_core/languages/language_translate.php']['title_var'] = 'translate';
	$this->_pages['mods/_core/languages/language_translate.php']['parent']    = 'mods/_core/languages/language.php';

	$this->_pages['mods/_core/languages/language_editor.php']['title_var'] = 'editor';
	$this->_pages['mods/_core/languages/language_editor.php']['parent']    = 'mods/_core/languages/language.php';

	$this->_pages['mods/_core/languages/language_term.php']['title_var'] = 'editor';
}
?>