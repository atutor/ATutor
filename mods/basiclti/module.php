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
	$this->_pages['mods/basiclti/index_admin.php']['children']    = array('mods/basiclti/tool/admin_create.php');
       $this->_pages['mods/basiclti/tool/admin_create.php']['title_var'] = 'bl_create';
       $this->_pages['mods/basiclti/tool/admin_create.php']['parent'] = 'mods/basiclti/index_admin.php';
       $this->_pages['mods/basiclti/tool/admin_view.php']['title_var'] = 'bl_view';
       $this->_pages['mods/basiclti/tool/admin_view.php']['parent'] = 'mods/basiclti/index_admin.php';
       $this->_pages['mods/basiclti/tool/admin_edit.php']['title_var'] = 'bl_edit';
       $this->_pages['mods/basiclti/tool/admin_edit.php']['parent'] = 'mods/basiclti/index_admin.php';
       $this->_pages['mods/basiclti/tool/admin_delete.php']['title_var'] = 'bl_delete';
       $this->_pages['mods/basiclti/tool/admin_delete.php']['parent'] = 'mods/basiclti/index_admin.php';
}

/*******
 * instructor Manage section:
 */
if ( authenticate(AT_PRIV_BASICLTI, TRUE) ) {
	$this->_pages['mods/basiclti/tool/content_edit.php']['title_var'] = 'bl_content';
	$this->_pages['mods/basiclti/tool/content_edit.php']['parent'] = 'index.php';


	$this->_pages['mods/basiclti/index_instructor.php']['title_var'] = 'basiclti';
	$this->_pages['mods/basiclti/index_instructor.php']['parent']   = 'tools/index.php';
	$this->_pages['mods/basiclti/index_instructor.php']['children'] = array('mods/basiclti/tool/instructor_create.php');
	$this->_pages['mods/basiclti/tool/instructor_create.php']['title_var'] = 'bl_create';
	$this->_pages['mods/basiclti/tool/instructor_create.php']['parent'] = 'mods/basiclti/index_instructor.php';
	$this->_pages['mods/basiclti/tool/instructor_view.php']['title_var'] = 'bl_view';
	$this->_pages['mods/basiclti/tool/instructor_view.php']['parent'] = 'mods/basiclti/index_instructor.php';
	$this->_pages['mods/basiclti/tool/instructor_edit.php']['title_var'] = 'bl_edit';
	$this->_pages['mods/basiclti/tool/instructor_edit.php']['parent'] = 'mods/basiclti/index_instructor.php';
	$this->_pages['mods/basiclti/tool/instructor_delete.php']['title_var'] = 'bl_delete';
	$this->_pages['mods/basiclti/tool/instructor_delete.php']['parent'] = 'mods/basiclti/index_instructor.php';
}

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
/* The element of content tool bar that is displayed on "Edit Content" => "Content" tab */

$this->_content_tools[] = array("id"=>"basiclti_tool",
                                "class"=>"fl-col clickable",
                                "src"=>AT_BASE_HREF."mods/basiclti/images/basiclti-icon.png",
                                "title"=>_AT('basiclti_tool'),
                                "alt"=>_AT('basiclti_tool'),
                                "text"=>_AT('basiclti_content_text'),
                                "js"=>AT_BASE_HREF."mods/basiclti/content_tool_action.js");


/*******
 * Register the entry of the callback class. Make sure the class name is properly namespaced, 
 * for instance, prefixed with the module name, to enforce its uniqueness.
 * This class must be defined in "ModuleCallbacks.class.php".
 * This class is an API that contains the static methods to act on core functions.
 */
$this->_callbacks['basiclti'] = 'BasicLTICallbacks';

function basiclti_get_group_url($group_id) {
	return 'mods/basiclti/index.php';
}


?>
