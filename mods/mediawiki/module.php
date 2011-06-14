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
 * not technically need for instructors for the current version of the module
 */
define('AT_PRIV_MEDIAWIKI',       $this->getPrivilege());
define('AT_ADMIN_PRIV_MEDIAWIKI', $this->getAdminPrivilege());

/*******
 * create a side menu box/stack.
 */
// Sidemenu block is disbaled by default in this version of the module
//$this->_stacks['mediawiki'] = array('title_var'=>'mediawiki', 'file'=>'mods/mediawiki/side_menu.inc.php');
// ** possible alternative: **
// $this->addStack('mediawiki', array('title_var' => 'mediawiki', 'file' => './side_menu.inc.php');

/*******
 * create optional sublinks for module "detail view" on course home page
 * when this line is uncommented, "mods/mediawiki/sublinks.php" need to be created to return an array of content to be displayed
 */
$this->_list['mediawiki'] = array('title_var'=>'mediawiki','file'=>'mods/mediawiki/sublinks.php');

// Uncomment for tiny list bullet icon for module sublinks "icon view" on course home page
$this->_pages['mods/mediawiki/index.php']['icon']      = 'mods/mediawiki/mw_icon_sm.png';

// Uncomment for big icon for module sublinks "detail view" on course home page
//$this->_pages['mods/mediawiki/index.php']['img']      = 'mods/mediawiki/mediawiki.jpg';

// ** possible alternative: **
// the text to display on module "detail view" when sublinks are not available
$this->_pages['mods/mediawiki/index.php']['text']      = _AT('mediawiki_text');

/*******
 * if this module is to be made available to students on the Home or Main Navigation.
 */
$_group_tool = $_student_tool = 'mods/mediawiki/index.php';

/*******
 * add the admin pages when needed.
 */
if (admin_authenticate(AT_ADMIN_PRIV_MEDIAWIKI, TRUE) || admin_authenticate(AT_ADMIN_PRIV_ADMIN, TRUE)) {
	$this->_pages[AT_NAV_ADMIN] = array('mods/mediawiki/index_admin.php');
	$this->_pages['mods/mediawiki/index_admin.php']['title_var'] = 'mediawiki';
	$this->_pages['mods/mediawiki/index_admin.php']['parent']    = AT_NAV_ADMIN;
}

/*******
 * instructor Manage section:
 */
$this->_pages['mods/mediawiki/index_instructor.php']['title_var'] = 'mediawiki';
$this->_pages['mods/mediawiki/index_instructor.php']['parent']   = 'tools/index.php';
// ** possible alternative: **
// $this->pages['./index_instructor.php']['title_var'] = 'mediawiki';
// $this->pages['./index_instructor.php']['parent']    = 'tools/index.php';

/*******
 * student page.
 */
$this->_pages['mods/mediawiki/index.php']['title_var'] = 'mediawiki';
$this->_pages['mods/mediawiki/index.php']['img']       = 'mods/mediawiki/mw_logo.png';

// /* public pages */
// Uncomment the following three lines if MediaWiki should be accessible from public pages
// for users who are not logged into ATutor

// $this->_pages[AT_NAV_PUBLIC] = array('mods/mediawiki/index_public.php');
// $this->_pages['mods/mediawiki/index_public.php']['title_var'] = 'mediawiki';
// $this->_pages['mods/mediawiki/index_public.php']['parent'] = AT_NAV_PUBLIC;

/* my start page pages */
$this->_pages[AT_NAV_START]  = array('mods/mediawiki/index_mystart.php');
$this->_pages['mods/mediawiki/index_mystart.php']['title_var'] = 'mediawiki';
$this->_pages['mods/mediawiki/index_mystart.php']['parent'] = AT_NAV_START;

function mediawiki_get_group_url($group_id) {
	return 'mods/mediawiki/index.php';
}

?>