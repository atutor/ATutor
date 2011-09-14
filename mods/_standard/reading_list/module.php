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
define('AT_PRIV_READING_LIST',       $this->getPrivilege());


/*******
 * if this module is to be made available to students on the Home or Main Navigation.
 */
$_student_tool = 'mods/_standard/reading_list/index.php';

//modules sub-content
$this->_list['reading_list'] = array('title_var'=>'reading_list','file'=>'mods/_standard/reading_list/sublinks.php');

/*******
 * instructor Manage section:
 */
$this->_pages['mods/_standard/reading_list/index_instructor.php']['title_var'] = 'reading_list';
$this->_pages['mods/_standard/reading_list/index_instructor.php']['parent']   = 'tools/index.php';
$this->_pages['mods/_standard/reading_list/index_instructor.php']['children'] = array('mods/_standard/reading_list/display_resources.php');
$this->_pages['mods/_standard/reading_list/index_instructor.php']['guide'] = 'instructor/?p=reading_list.php';


	$this->_pages['mods/_standard/reading_list/add_resource_url.php']['title_var'] = 'rl_add_resource_url';
	$this->_pages['mods/_standard/reading_list/add_resource_url.php']['parent']    = 'mods/_standard/reading_list/display_resources.php';

	$this->_pages['mods/_standard/reading_list/add_resource_book.php']['title_var'] = 'rl_add_resource_book';
	$this->_pages['mods/_standard/reading_list/add_resource_book.php']['parent']    = 'mods/_standard/reading_list/display_resources.php';

	$this->_pages['mods/_standard/reading_list/add_resource_handout.php']['title_var'] = 'rl_add_resource_handout';
	$this->_pages['mods/_standard/reading_list/add_resource_handout.php']['parent']    = 'mods/_standard/reading_list/display_resources.php';

	$this->_pages['mods/_standard/reading_list/add_resource_av.php']['title_var'] = 'rl_add_resource_av';
	$this->_pages['mods/_standard/reading_list/add_resource_av.php']['parent']    = 'mods/_standard/reading_list/display_resources.php';

	$this->_pages['mods/_standard/reading_list/add_resource_file.php']['title_var'] = 'rl_add_resource_file';
	$this->_pages['mods/_standard/reading_list/add_resource_file.php']['parent']    = 'mods/_standard/reading_list/display_resources.php';

	$this->_pages['mods/_standard/reading_list/edit_reading_book.php']['title_var'] = 'rl_edit_reading_book';
	$this->_pages['mods/_standard/reading_list/edit_reading_book.php']['parent']    = 'mods/_standard/reading_list/index_instructor.php';

	$this->_pages['mods/_standard/reading_list/edit_reading_url.php']['title_var'] = 'rl_edit_reading_url';
	$this->_pages['mods/_standard/reading_list/edit_reading_url.php']['parent']    = 'mods/_standard/reading_list/index_instructor.php';

	$this->_pages['mods/_standard/reading_list/edit_reading_handout.php']['title_var'] = 'rl_edit_reading_handout';
	$this->_pages['mods/_standard/reading_list/edit_reading_handout.php']['parent']    = 'mods/_standard/reading_list/index_instructor.php';

	$this->_pages['mods/_standard/reading_list/edit_reading_file.php']['title_var'] = 'rl_edit_reading_file';
	$this->_pages['mods/_standard/reading_list/edit_reading_file.php']['parent']    = 'mods/_standard/reading_list/index_instructor.php';

	$this->_pages['mods/_standard/reading_list/edit_reading_av.php']['title_var'] = 'rl_edit_reading_av';
	$this->_pages['mods/_standard/reading_list/edit_reading_av.php']['parent']    = 'mods/_standard/reading_list/index_instructor.php';

	$this->_pages['mods/_standard/reading_list/delete_reading.php']['title_var'] = 'rl_delete_reading';
	$this->_pages['mods/_standard/reading_list/delete_reading.php']['parent']    = 'mods/_standard/reading_list/index_instructor.php';

	$this->_pages['mods/_standard/reading_list/new_reading_book.php']['title_var'] = 'rl_new_reading_book';
	$this->_pages['mods/_standard/reading_list/new_reading_book.php']['parent']    = 'mods/_standard/reading_list/index_instructor.php';

	$this->_pages['mods/_standard/reading_list/new_reading_url.php']['title_var'] = 'rl_new_reading_url';
	$this->_pages['mods/_standard/reading_list/new_reading_url.php']['parent']    = 'mods/_standard/reading_list/index_instructor.php';

	$this->_pages['mods/_standard/reading_list/new_reading_av.php']['title_var'] = 'rl_new_reading_av';
	$this->_pages['mods/_standard/reading_list/new_reading_av.php']['parent']    = 'mods/_standard/reading_list/index_instructor.php';

	$this->_pages['mods/_standard/reading_list/new_reading_handout.php']['title_var'] = 'rl_new_reading_handout';
	$this->_pages['mods/_standard/reading_list/new_reading_handout.php']['parent']    = 'mods/_standard/reading_list/index_instructor.php';

	$this->_pages['mods/_standard/reading_list/new_reading_file.php']['title_var'] = 'rl_new_reading_file';
	$this->_pages['mods/_standard/reading_list/new_reading_file.php']['parent']    = 'mods/_standard/reading_list/index_instructor.php';

	$this->_pages['mods/_standard/reading_list/display_resources.php']['title_var'] = 'rl_display_resources';
	$this->_pages['mods/_standard/reading_list/display_resources.php']['parent']    = 'mods/_standard/reading_list/index_instructor.php';

	$this->_pages['mods/_standard/reading_list/display_resource.php']['title_var'] = 'rl_display_resource';
	$this->_pages['mods/_standard/reading_list/display_resource.php']['parent']    = 'mods/_standard/reading_list/index.php';

	$this->_pages['mods/_standard/reading_list/delete_resource.php']['title_var'] = 'rl_delete_resource';
	$this->_pages['mods/_standard/reading_list/delete_resource.php']['parent']    = 'mods/_standard/reading_list/index_instructor.php';


/*******
 * student page.
 */
$this->_pages['mods/_standard/reading_list/index.php']['title_var'] = 'reading_list';
$this->_pages['mods/_standard/reading_list/index.php']['img']       = 'images/home-reading_list.png';
$this->_pages['mods/_standard/reading_list/index.php']['icon']       = 'images/home-reading_list_sm.png';

$this->_pages['mods/_standard/reading_list/index.php']['children'] = array('mods/_standard/reading_list/reading_details.php');

	$this->_pages['mods/_standard/reading_list/reading_details.php']['title_var'] = 'rl_display_resources';
	$this->_pages['mods/_standard/reading_list/reading_details.php']['parent']    = 'mods/_standard/reading_list/index.php';
?>