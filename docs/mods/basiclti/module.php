<?php
/*******
 * doesn't allow this file to be loaded with a browser.
 */
if (!defined('AT_INCLUDE_PATH')) { exit; }

error_reporting(E_ALL & ~E_NOTICE);
ini_set("display_errors", 1);

/******
 * this file must only be included within a Module obj
 */
if (!isset($this) || (isset($this) && (strtolower(get_class($this)) != 'module'))) { exit(__FILE__ . ' is not a Module'); }

/*******
 * assign the instructor and admin privileges to the constants.
 */
define('AT_PRIV_BASICLTI',       $this->getPrivilege());
define('AT_ADMIN_PRIV_BASICLTI', $this->getAdminPrivilege());

/*******
 * set savant variable and constants
 */
global $savant;
require(AT_INCLUDE_PATH.'../mods/basiclti/include/constants.inc.php');
$savant->addPath('template', AT_BL_INCLUDE.'html/');

/*******
 * create a side menu box/stack.
 */
$this->_stacks['basiclti'] = array('title_var'=>'basiclti', 'file'=>'mods/basiclti/side_menu.inc.php');
// ** possible alternative: **
// $this->addStack('basiclti', array('title_var' => 'basiclti', 'file' => './side_menu.inc.php');

/*******
 * create optional sublinks for module "detail view" on course home page
 * when this line is uncommented, "mods/basiclti/sublinks.php" need to be created to return an array of content to be displayed
 */
//$this->_list['basiclti'] = array('title_var'=>'basiclti','file'=>'mods/basiclti/sublinks.php');

// Uncomment for tiny list bullet icon for module sublinks "icon view" on course home page
//$this->_pages['mods/basiclti/index.php']['icon']      = 'mods/basiclti/basiclti_sm.jpg';

// Uncomment for big icon for module sublinks "detail view" on course home page
//$this->_pages['mods/basiclti/index.php']['img']      = 'mods/basiclti/basiclti.jpg';

// ** possible alternative: **
// the text to display on module "detail view" when sublinks are not available
$this->_pages['mods/basiclti/index.php']['text']      = _AT('basiclti_text');

/*******
 * if this module is to be made available to students on the Home or Main Navigation.
 */
$_group_tool = $_student_tool = 'mods/basiclti/index.php';

/*******
 * add the admin pages when needed.
 */
if (admin_authenticate(AT_ADMIN_PRIV_BASICLTI, TRUE) || admin_authenticate(AT_ADMIN_PRIV_ADMIN, TRUE)) {
	$this->_pages[AT_NAV_ADMIN] = array('mods/basiclti/index_admin.php');
	$this->_pages['mods/basiclti/index_admin.php']['title_var'] = 'basiclti';
	$this->_pages['mods/basiclti/index_admin.php']['parent']    = AT_NAV_ADMIN;
	$this->_pages['mods/basiclti/index_admin.php']['children']    = array('mods/basiclti/admin/create.php');
                $this->_pages['mods/basiclti/admin/create.php']['title_var'] = 'bl_create';
                $this->_pages['mods/basiclti/admin/create.php']['parent'] = 'mods/basiclti/index_admin.php';
                $this->_pages['mods/basiclti/admin/view_tool.php']['title_var'] = 'bl_view';
                $this->_pages['mods/basiclti/admin/view_tool.php']['parent'] = 'mods/basiclti/index_admin.php';
                $this->_pages['mods/basiclti/admin/edit_tool.php']['title_var'] = 'bl_edit';
                $this->_pages['mods/basiclti/admin/edit_tool.php']['parent'] = 'mods/basiclti/index_admin.php';
                $this->_pages['mods/basiclti/admin/delete_tool.php']['title_var'] = 'bl_delete';
                $this->_pages['mods/basiclti/admin/delete_tool.php']['parent'] = 'mods/basiclti/index_admin.php';
}

/*******
 * instructor Manage section:
 */
$this->_pages['mods/basiclti/index_instructor.php']['title_var'] = 'basiclti';
$this->_pages['mods/basiclti/index_instructor.php']['parent']   = 'tools/index.php';
$this->_pages['mods/basiclti/index_instructor.php']['children'] = array('mods/basiclti/index_instructor.php');
// ** possible alternative: **
// $this->pages['./index_instructor.php']['title_var'] = 'basiclti';
// $this->pages['./index_instructor.php']['parent']    = 'tools/index.php';

/*******
 * student page.
 */
$this->_pages['mods/basiclti/index.php']['title_var'] = 'basiclti';
$this->_pages['mods/basiclti/index.php']['img']       = 'mods/basiclti/basiclti.jpg';

/* public pages */
$this->_pages[AT_NAV_PUBLIC] = array('mods/basiclti/index_public.php');
$this->_pages['mods/basiclti/index_public.php']['title_var'] = 'basiclti';
$this->_pages['mods/basiclti/index_public.php']['parent'] = AT_NAV_PUBLIC;

/* my start page pages */
$this->_pages[AT_NAV_START]  = array('mods/basiclti/index_mystart.php');
$this->_pages['mods/basiclti/index_mystart.php']['title_var'] = 'basiclti';
$this->_pages['mods/basiclti/index_mystart.php']['parent'] = AT_NAV_START;

function basiclti_get_group_url($group_id) {
	return 'mods/basiclti/index.php';
}
?>
