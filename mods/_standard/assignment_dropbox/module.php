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
 * if this module is to be made available to students on the Home or Main Navigation.
 */
$_student_tool = 'mods/_standard/assignment_dropbox/index.php';

/*******
 * create optional sublinks for module "detail view" on course home page
 * when this line is uncommented, "mods/assignment_dropbox/sublinks.php" need to be created to return an array of content to be displayed
 */
$this->_list['assignment_dropbox'] = array('title_var'=>'assignment_dropbox','file'=>'mods/_standard/assignment_dropbox/sublinks.php');

/*******
 * add the admin pages when needed.
 */
//if (admin_authenticate(AT_ADMIN_PRIV_assignment_dropbox, TRUE) || admin_authenticate(AT_ADMIN_PRIV_ADMIN, TRUE)) {
//	$this->_pages[AT_NAV_ADMIN] = array('mods/assignment_dropbox/index_admin.php');
//	$this->_pages['mods/_standard/assignment_dropbox/index_admin.php']['title_var'] = 'assignment_dropbox';
//	$this->_pages['mods/_standard/assignment_dropbox/index_admin.php']['parent']    = AT_NAV_ADMIN;
//}

/*******
 * student page.
 */
$this->_pages['mods/_standard/assignment_dropbox/index.php']['title_var'] = 'assignment_dropbox';
$this->_pages['mods/_standard/assignment_dropbox/index.php']['icon']      = 'mods/_standard/assignment_dropbox/assignment_dropbox_sm.png';
$this->_pages['mods/_standard/assignment_dropbox/index.php']['img']       = 'mods/_standard/assignment_dropbox/assignment_dropbox.png';
$this->_pages['mods/_standard/assignment_dropbox/index.php']['text']      = _AT('assignment_dropbox_text');
$this->_pages['mods/_standard/assignment_dropbox/index.php']['parent']   = 'mods/_standard/assignments/index_instructor.php';

$this->_pages['mods/_standard/assignments/index_instructor.php']['children'] = 
       array_merge(is_array($this->_pages['mods/_standard/assignments/index_instructor.php']['children']) ? $this->_pages['mods/_standard/assignments/index_instructor.php']['children'] : array(), array('mods/_standard/assignment_dropbox/index.php'));

?>