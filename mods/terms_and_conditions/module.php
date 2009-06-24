<?php
/*******
 * doesn't allow this file to be loaded with a browser.
 */
if (!defined('AT_INCLUDE_PATH')) { exit; }

/******
 * this file must only be included within a Module obj
 */
if (!isset($this) || (isset($this) && (strtolower(get_class($this)) != 'module'))) { exit(__FILE__ . ' is not a Module'); }

/*******
 * assign the instructor and admin privileges to the constants.
 */
define('AT_ADMIN_TERMS_AND_CONDITIONS', $this->getAdminPrivilege());

/*******
 * add the admin pages when needed.
 */
if (admin_authenticate(AT_ADMIN_TERMS_AND_CONDITION, TRUE) || admin_authenticate(AT_ADMIN_PRIV_ADMIN, TRUE)) {
	 $this->_pages['admin/config_edit.php']['children'][] = 'mods/terms_and_conditions/tac_edit.php';
	 $this->_pages['mods/terms_and_conditions/tac_edit.php']['title_var']  = 'terms_and_conditions';
	 $this->_pages['mods/terms_and_conditions/tac_edit.php']['parent']	 = 'admin/config_edit.php';
}
?>