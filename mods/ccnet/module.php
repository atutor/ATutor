<?php
if (!defined('AT_INCLUDE_PATH')) { exit; }
if (!isset($this) || (isset($this) && (strtolower(get_class($this)) != 'module'))) { exit(__FILE__ . ' is not a Module'); }

/*******
 * assign the instructor and admin privileges to the constants.
 */
define('AT_PRIV_CCNET',       $this->getPrivilege());
define('AT_ADMIN_PRIV_CCNET', $this->getAdminPrivilege());

/*******
 * if this module is to be made available to students on the Home or Main Navigation.
 */
$_student_tool = 'mods/ccnet/index.php';

/*******
 * add the admin pages when needed.
 */
if (admin_authenticate(AT_ADMIN_PRIV_CCNET, TRUE) || admin_authenticate(AT_ADMIN_PRIV_ADMIN, TRUE)) {
	$this->_pages[AT_NAV_ADMIN] = array('mods/ccnet/ccnet.php');
	$this->_pages['mods/ccnet/ccnet.php']['parent']    = AT_NAV_ADMIN;
	$this->_pages['mods/ccnet/ccnet.php']['title_var'] = 'ccnet';
}

/*******
 * instructor Manage section:
 */
$this->_pages['mods/ccnet/index.php']['title_var'] = 'ccnet';
$this->_pages['mods/ccnet/index.php']['parent']   = 'tools/index.php';


/*******
 * student page.
 */
$this->_pages['mods/ccnet/index.php']['title_var'] = 'ccnet';
$this->_pages['mods/ccnet/index.php']['img']       = 'mods/ccnet/ccnet_logo.jpg';

?>