<?php
/*******
 * doesn't allow this file to be loaded with a browser.
 */
if (!defined('AT_INCLUDE_PATH')) { exit; }

/******
 * this file must only be included within a ModuleProxy obj
 */
if (!isset($this) || (isset($this) && (strtolower(get_class($this)) != 'moduleproxy'))) { exit(__FILE__ . ' is not a ModuleProxy'); }

/*******
 * assign the instructor and admin privileges to the constants.
 */
define('AT_PRIV_HELLO_WORLD', $this->getPrivilege());
define('AT_ADMIN_PRIV_HELLO_WORLD', $this->getAdminPrivilege());

/*******
 * create a side menu box/stack.
 */
$_module_stacks['hello_world'] = array('title_var'=>'hello_world', 'file'=>'mods/hello_world/side_menu.inc.php');

/*******
 * if this module is to be made available to students on the Home or Main Navigation.
 */
$_student_tool = 'mods/hello_world/index.php';

/*******
 * add the admin pages when needed.
 */
if (admin_authenticate(AT_ADMIN_PRIV_HELLO_WORLD, TRUE) || admin_authenticate(AT_ADMIN_PRIV_ADMIN, TRUE)) {
	$_module_pages[AT_NAV_ADMIN] = array('mods/hello_world/index_admin.php');
	$_module_pages['mods/hello_world/index_admin.php']['parent']    = AT_NAV_ADMIN;
	$_module_pages['mods/hello_world/index_admin.php']['title_var'] = 'hello_world';
}

/*******
 * instructor Manage section:
 */
$_module_pages['mods/hello_world/index_instructor.php']['title_var'] = 'hello_world';
$_module_pages['mods/hello_world/index_instructor.php']['parent']   = 'tools/index.php';


/*******
 * student page.
 */
$_module_pages['mods/hello_world/index.php']['title_var'] = 'hello_world';
$_module_pages['mods/hello_world/index.php']['img']       = 'mods/hello_world/hello_world.jpg';

?>