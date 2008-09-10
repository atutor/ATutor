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
define('AT_PRIV_SUPPORT_TOOLS',       $this->getPrivilege());
define('AT_ADMIN_PRIV_SUPPORT_TOOLS', $this->getAdminPrivilege());

/*******
 * create a side menu box/stack.
 */
$this->_stacks['support_tools'] = array('title_var'=>'support_tools','file'=>AT_INCLUDE_PATH.'../mods/_standard/support_tools/side_menu.inc.php');

		//$this->_pages['admin/modules/scaffolds.php']['title_var'] = 'support_tools';
		//$this->_pages['admin/courses.php']['children'] = array('admin/modules/scaffolds.php');

		//$this->_pages['admin/modules/scaffolds.php']['title_var'] = 'support_tools';
if (admin_authenticate(AT_ADMIN_PRIV_SUPPORT_TOOLS, TRUE) || admin_authenticate(AT_ADMIN_PRIV_ADMIN, TRUE)) {
	if (admin_authenticate(AT_ADMIN_PRIV_ADMIN, TRUE)) {
		$this->_pages['admin/modules/scaffolds.php']['parent']    = 'admin/courses.php';
		$this->_pages['admin/modules/scaffolds.php']['title_var'] = 'support_tools';
		$this->_pages['admin/courses.php']['children'] = array('admin/modules/scaffolds.php');
		//$this->_pages['admin/forums.php']['parent']    = 'admin/courses.php';
	} 
}
?>