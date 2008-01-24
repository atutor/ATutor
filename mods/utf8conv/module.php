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
define('AT_PRIV_UTF8CONV',       $this->getPrivilege());
define('AT_ADMIN_PRIV_UTF8CONV', $this->getAdminPrivilege());

/*******
 * add the admin pages when needed.
 */
if (admin_authenticate(AT_ADMIN_PRIV_UTF8CONV, TRUE) || admin_authenticate(AT_ADMIN_PRIV_ADMIN, TRUE)) {
	$this->_pages['admin/courses.php']['children'] = array('mods/utf8conv/index_admin.php');
	$this->_pages['mods/utf8conv/index_admin.php']['title_var'] = 'utf8conv';
	$this->_pages['mods/utf8conv/index_admin.php']['parent']   = 'admin/courses.php';
}

/*******
 * instructor Manage section:
 */
$this->_pages['tools/content/index.php']['children'] = array('mods/utf8conv/index_instructor.php');
$this->_pages['mods/utf8conv/index_instructor.php']['title_var'] = 'utf8conv';
$this->_pages['mods/utf8conv/index_instructor.php']['parent']   = 'tools/content/index.php';

function utf8conv_get_group_url($group_id) {
	return 'mods/utf8conv/index.php';
}
?>