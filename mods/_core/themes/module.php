<?php
if (!defined('AT_INCLUDE_PATH')) { exit; }
if (!isset($this) || (isset($this) && (strtolower(get_class($this)) != 'module'))) { exit(__FILE__ . ' is not a Module'); }

define('AT_ADMIN_PRIV_THEMES', $this->getAdminPrivilege());

if (admin_authenticate(AT_ADMIN_PRIV_THEMES, TRUE) || admin_authenticate(AT_ADMIN_PRIV_ADMIN, TRUE)) {
	if (admin_authenticate(AT_ADMIN_PRIV_ADMIN, TRUE)) {
		$this->_pages['admin/config_edit.php']['children']      = array('mods/_core/themes/index.php');
		$this->_pages['mods/_core/themes/index.php']['parent'] = 'admin/config_edit.php';
	} else {
		$this->_pages[AT_NAV_ADMIN] = array('mods/_core/themes/index.php');
		$this->_pages['mods/_core/themes/index.php']['parent'] = AT_NAV_ADMIN;
	}


	//admin
	$this->_pages['mods/_core/themes/index.php']['title_var'] = 'themes';
	$this->_pages['mods/_core/themes/index.php']['guide']     = 'admin/?p=themes.php';
	$this->_pages['mods/_core/themes/index.php']['children']  = array('mods/_core/themes/install_themes.php');
	$this->_pages['mods/_core/themes/install_themes.php']['guide']   = 'admin/?p=importing_themes.php';	

	$this->_pages['mods/_core/themes/delete.php']['title_var'] = 'delete';
	$this->_pages['mods/_core/themes/delete.php']['parent']    = 'mods/_core/themes/index.php';

	$this->_pages['mods/_core/themes/install_themes.php']['title_var'] = 'install_themes';
	$this->_pages['mods/_core/themes/install_themes.php']['parent'] = 'mods/_core/themes/index.php';

	$this->_pages['mods/_core/themes/theme_install_step_1.php']['title_var'] = 'details';
	$this->_pages['mods/_core/themes/theme_install_step_1.php']['parent']    = 'mods/_core/themes/install_themes.php';

	$this->_pages['mods/_core/themes/theme_install_step_2.php']['title_var'] = 'details';
	$this->_pages['mods/_core/themes/theme_install_step_2.php']['parent']    = 'mods/_core/themes/install_themes.php';

	$this->_pages['mods/_core/themes/theme_install_step_3.php']['title_var'] = 'details';
	$this->_pages['mods/_core/themes/theme_install_step_3.php']['parent']    = 'mods/_core/themes/install_themes.php';

	$this->_pages['mods/_core/themes/version_history.php']['title_var'] = 'version_history';
	$this->_pages['mods/_core/themes/version_history.php']['parent']    = 'mods/_core/themes/install_themes.php';

}
?>