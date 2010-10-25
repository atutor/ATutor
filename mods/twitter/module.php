<?php
/*******
 */
if (!defined('AT_INCLUDE_PATH')) { exit; }

/******
 */
if (!isset($this) || (isset($this) && (strtolower(get_class($this)) != 'module'))) { exit(__FILE__ . ' is not a Module'); }


/*******
 * assign the instructor and admin privileges to the constants.
 */
define('AT_PRIV_TWITTER',       $this->getPrivilege());
define('AT_ADMIN_PRIV_TWITTER', $this->getAdminPrivilege());

/*******
 */
$_student_tool = 'mods/twitter/index.php';
/*******
 * add the admin pages when needed.
 */
if (admin_authenticate(AT_ADMIN_PRIV_TWITTER, TRUE) || admin_authenticate(AT_ADMIN_PRIV_ADMIN, TRUE)) {
	$this->_pages[AT_NAV_ADMIN] = array('mods/twitter/index_admin.php');
	$this->_pages['mods/twitter/index_admin.php']['title_var'] = 'twitter';
	$this->_pages['mods/twitter/index_admin.php']['parent']    = AT_NAV_ADMIN;
}

/*******
 * instructor Manage section:
 */
$this->_pages['mods/twitter/index_instructor.php']['title_var'] = 'twitter';
$this->_pages['mods/twitter/index_instructor.php']['parent']   = 'tools/index.php';

/*******
 * student page.
 */
$this->_pages['mods/twitter/index.php']['title_var'] = 'twitter';
$this->_pages['mods/twitter/index.php']['img']       = 'mods/twitter/twitter.png';


/* public pages */
$this->_pages[AT_NAV_PUBLIC] = array('mods/twitter/index_public.php');
$this->_pages['mods/twitter/index_public.php']['title_var'] = 'twitter';
$this->_pages['mods/twitter/index_public.php']['parent'] = 'login.php';
$this->_pages['login.php']['children'] = array('mods/twitter/index_public.php');
?>

