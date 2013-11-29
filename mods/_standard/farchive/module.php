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
define('AT_PRIV_FARCHIVE',       $this->getPrivilege());
//define('AT_ADMIN_PRIV_FARCHIVE', $this->getAdminPrivilege());

/*******
 * instructor Manage section:
 */

$this->_pages['mods/_standard/farchive/index_instructor.php']['title_var'] = 'farchive_export';
$this->_pages['mods/_standard/farchive/index_instructor.php']['parent']    = 'mods/_standard/forums/index.php';
$this->_pages['mods/_standard/farchive/index_instructor.php']['guide']    = 'instructor/?p=forum_export.php';
$this->_pages['mods/_standard/forums/index.php']['children']  = array('mods/_standard/farchive/index_instructor.php');

if($_SESSION['is_admin'] > 0 || authenticate(AT_PRIV_FARCHIVE, TRUE)){	
$this->_pages_i['mods/_standard/farchive/index_instructor.php']['title_var'] = 'farchive_export';
$this->_pages_i['mods/_standard/farchive/index_instructor.php']['other_parent']    = 'mods/_standard/forums/forum/list.php';
$this->_pages_i['mods/_standard/forums/forum/list.php']['children']  = array('mods/_standard/farchive/index_instructor.php');
}
?>