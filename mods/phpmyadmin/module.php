<?php
if (!defined('AT_INCLUDE_PATH')) { exit; }
if (!isset($this) || (isset($this) && (strtolower(get_class($this)) != 'module'))) { exit(__FILE__ . ' is not a Module'); }

/*******
 * assign the admin privilege to the constants.
 */
define('AT_ADMIN_PRIV_PHPMYADMIN', $this->getAdminPrivilege());


/*******
 * add the admin pages when needed.
 */
if (admin_authenticate(AT_ADMIN_PRIV_PHPMYADMIN, TRUE) || admin_authenticate(AT_ADMIN_PRIV_ADMIN, TRUE)) {

	$this->_pages[AT_NAV_ADMIN] = array('mods/phpmyadmin/phpmyadmin.php');
	$this->_pages['mods/phpmyadmin/phpmyadmin.php']['parent']    = AT_NAV_ADMIN;
	$this->_pages['mods/phpmyadmin/phpmyadmin.php']['title_var'] = 'phpmyadmin';
}

?>