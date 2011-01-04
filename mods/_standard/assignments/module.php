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
define('AT_PRIV_ASSIGNMENTS', $this->getPrivilege());

/*******
 * instructor Manage section:
 */
$this->_pages['mods/_standard/assignments/index_instructor.php']['title_var'] = 'assignments';
$this->_pages['mods/_standard/assignments/index_instructor.php']['parent']   = 'tools/index.php';
$this->_pages['mods/_standard/assignments/index_instructor.php']['children'] = array('mods/_standard/assignments/add_assignment.php');
$this->_pages['mods/_standard/assignments/index_instructor.php']['guide']     = 'instructor/?p=assignments.php';

	$this->_pages['mods/_standard/assignments/add_assignment.php']['title_var'] = 'add_assignment';
	$this->_pages['mods/_standard/assignments/add_assignment.php']['parent']    = 'mods/_standard/assignments/index_instructor.php';

	$this->_pages['mods/_standard/assignments/edit_assignment.php']['title_var'] = 'edit';
	$this->_pages['mods/_standard/assignments/edit_assignment.php']['parent']    = 'mods/_standard/assignments/index_instructor.php';

	$this->_pages['mods/_standard/assignments/delete_assignment.php']['title_var'] = 'delete';
	$this->_pages['mods/_standard/assignments/delete_assignment.php']['parent']    = 'mods/_standard/assignments/index_instructor.php';


?>