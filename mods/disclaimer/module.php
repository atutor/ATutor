<?php
/*******
 * doesn't allow this file to be loaded with a browser.
 */
if (!defined('AT_INCLUDE_PATH')) { exit; }

/******
 * this file must only be included within a Module obj
 */
if (!isset($this) || (isset($this) && (strtolower(get_class($this)) != 'module'))) { exit(__FILE__ . ' is not a Module'); }

global $_config;
/*******
 * assign the instructor and admin privileges to the constants.
 */
define('AT_ADMIN_TERMS_AND_CONDITIONS', $this->getAdminPrivilege());

/*******
 * add the admin pages when needed.
 */
if (admin_authenticate(AT_ADMIN_TERMS_AND_CONDITION, TRUE) || admin_authenticate(AT_ADMIN_PRIV_ADMIN, TRUE)) {
	 $this->_pages['admin/config_edit.php']['children'][] = 'mods/disclaimer/tac_edit.php';
	 $this->_pages['mods/disclaimer/tac_edit.php']['title_var']  = 'disclaimer';
	 $this->_pages['mods/disclaimer/tac_edit.php']['parent']	 = 'admin/config_edit.php';
}

// The user cannot bypass the "terms and conditions" page 
//if($_config['enable_terms_and_conditions']==1 && !isset($_SESSION['agree_terms_and_conditions']) && !strstr($_SERVER['PHP_SELF'], 'terms_and_conditions.php')){
//	header('Location: '.AT_BASE_HREF.'mods/disclaimer/terms_and_conditions.php');
//	exit;
//}
//
//// destroy the session var at logout
//if (strstr($_SERVER['PHP_SELF'], 'logout.php')) {
//	unset($_SESSION['agree_terms_and_conditions']);
//}
?>