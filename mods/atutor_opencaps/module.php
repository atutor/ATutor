<?php
/*******
 * doesn't allow this file to be loaded with a browser.
 */
if (!defined('AT_INCLUDE_PATH')) { exit; }

/******
 * this file must only be included within a Module obj
 */
//if (!isset($this) || (isset($this) && (strtolower(get_class($this)) != 'module'))) { exit(__FILE__ . ' is not a Module'); }

/*******
 * assign the instructor and admin privileges to the constants.
 */
//define('AT_PRIV_ATOPENCAPS',       $this->getPrivilege());
//define('AT_ADMIN_PRIV_ATOPENCAPS', $this->getAdminPrivilege());

// Add menu item into "Manage" => "Content" 
$this->_pages['mods/_core/content/index.php']['children'] = array('mods/AtOpenCaps/index.php');


/*******
 * create a side menu box/stack.
 */
//$this->_stacks['AtOpenCaps'] = array('title_var'=>'AtOpenCaps', 'file'=>'mods/AtOpenCaps/side_menu.inc.php');
// ** possible alternative: **
//$this->addStack('OpenCaps', array('title_var' => 'OpenCaps', 'file' => './side_menu.inc.php');
//$this->addStack('OpenCaps', array('title_var' => 'OpenCaps', 'file' => 'side_menu.inc.php');

/*******
 * if this module is to be made available to students on the Home or Main Navigation.
 */
$_group_tool = $_student_tool = 'mods/AtOpenCaps/index.php';

/*******
 * add the admin pages when needed.

if (admin_authenticate(AT_ADMIN_PRIV_CMSMS_FEUSERS, TRUE) || admin_authenticate(AT_ADMIN_PRIV_ADMIN, TRUE)) {
	$this->_pages[AT_NAV_ADMIN] = array('mods/AtOpenCaps/index_admin.php');
	$this->_pages['mods/AtOpenCaps/index_admin.php']['title_var'] = 'Captioning';
	$this->_pages['mods/AtOpenCaps/index_admin.php']['parent']    = AT_NAV_ADMIN;
}
 */

/*******
 * instructor Manage section:
 */
//$this->_pages['mods/AtOpenCaps/index_instructor.php']['title_var'] = 'AtOpenCaps';
//$this->_pages['mods/AtOpenCaps/index_instructor.php']['parent']   = 'tools/index.php';
// ** possible alternative: **
// $this->pages['./index_instructor.php']['title_var'] = 'AtOpenCaps';
// $this->pages['./index_instructor.php']['parent']    = 'tools/index.php';




// adding module to content edit page
//$this->_pages['mods/AtOpenCaps/index_mystart.php']['title'] = 'edit_content_opencaps';
//$this->_pages['mods/AtOpenCaps/index_mystart.php']['parent'] = 'mods/_core/editor/edit_content.php';
//$this->_pages['mods/AtOpenCaps/index_instructor.php']['guide'] = 'instructor/?p=content_edit.php';


// ADD TO CONTENT
$this->_pages['mods/_core/content/index.php']['children'] = array('mods/AtOpenCaps/index.php');


/*******
 * student page.
 */
// this not working for some reason; and breaking the shift of AT language compatibility 
//$this->_pages['mods/AtOpenCaps/index.php']['title_var'] = _AT('atoc_moduleName');

$this->_pages['mods/AtOpenCaps/index.php']['title_var'] = 'Captioning';
$this->_pages['mods/AtOpenCaps/index.php']['img']       = 'mods/AtOpenCaps/images/AtOpenCaps.png';


/* public pages */
	//$this->_pages[AT_NAV_PUBLIC] = array('mods/AtOpenCaps/index_public.php');
	//$this->_pages['mods/AtOpenCaps/index_public.php']['title_var'] = 'AtOpenCaps';
	//$this->_pages['mods/AtOpenCaps/index_public.php']['parent'] = AT_NAV_PUBLIC;

/* my start page pages */
//$this->_pages[AT_NAV_START]  = array('mods/AtOpenCaps/index_mystart.php');
//$this->_pages['mods/AtOpenCaps/index_mystart.php']['title_var'] = 'AtOpenCaps';
//$this->_pages['mods/AtOpenCaps/index_mystart.php']['parent'] = AT_NAV_START;

function AtOpenCaps_get_group_url($group_id) {
	return 'mods/AtOpenCaps/index.php';
}
?>