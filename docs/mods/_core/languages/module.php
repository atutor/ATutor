<?php
if (!defined('AT_INCLUDE_PATH')) { exit; }
if (!isset($this) || (isset($this) && (strtolower(get_class($this)) != 'module'))) { exit(__FILE__ . ' is not a Module'); }

define('AT_ADMIN_PRIV_LANGUAGES', $this->getAdminPrivilege());

if (admin_authenticate(AT_ADMIN_PRIV_LANGUAGES, TRUE) || admin_authenticate(AT_ADMIN_PRIV_ADMIN, TRUE)) {
	if (admin_authenticate(AT_ADMIN_PRIV_ADMIN, TRUE)) {
		$this->_pages['admin/config_edit.php']['children']  = array('admin/language.php');
		$this->_pages['admin/language.php']['parent'] = 'admin/config_edit.php';
	} else {
		$this->_pages[AT_NAV_ADMIN] = array('admin/language.php');
		$this->_pages['admin/language.php']['parent'] = AT_NAV_ADMIN;
	}

	//admin
	$this->_pages['admin/language.php']['title_var'] = 'languages';
	$this->_pages['admin/language.php']['guide']     = 'admin/?p=languages.php';

		$this->_pages['admin/language_add.php']['title_var'] = 'add_language';
		$this->_pages['admin/language_add.php']['parent']    = 'admin/language.php';

		$this->_pages['admin/language_edit.php']['title_var'] = 'edit_language';
		$this->_pages['admin/language_edit.php']['parent']    = 'admin/language.php';

		$this->_pages['admin/language_delete.php']['title_var'] = 'delete_language';
		$this->_pages['admin/language_delete.php']['parent']    = 'admin/language.php';
}
?>