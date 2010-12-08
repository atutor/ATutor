<?php
if (!defined('AT_INCLUDE_PATH')) { exit; }
if (!isset($this) || (isset($this) && (strtolower(get_class($this)) != 'module'))) { exit(__FILE__ . ' is not a Module'); }

define('AT_PRIV_FILE_STORAGE',       $this->getPrivilege() );
// define('AT_ADMIN_PRIV_FILE_STORAGE',       $this->getAdminPrivilege() );


// if this module is to be made available to students on the Home or Main Navigation
$_group_tool = $_student_tool = 'mods/_standard/file_storage/index.php';

//modules sub-content
$this->_list['file_storage'] = array('title_var'=>'file_storage','file'=>'mods/_standard/file_storage/sublinks.php');

//student pages
$this->_pages['mods/_standard/file_storage/index.php']['title_var']  = 'file_storage';
$this->_pages['mods/_standard/file_storage/index.php']['img']        = 'images/home-file_storage.png';
$this->_pages['mods/_standard/file_storage/index.php']['icon']       = 'images/application_get.png';
$this->_pages['mods/_standard/file_storage/index.php']['guide']      = 'general/?p=file_storage.php';

$this->_pages['mods/_standard/file_storage/revisions.php']['title_var'] = 'revisions';
$this->_pages['mods/_standard/file_storage/revisions.php']['parent'] = 'mods/_standard/file_storage/index.php';
$this->_pages['mods/_standard/file_storage/revisions.php']['children'] = array(); // empty array creates a "back to" link to index.php

$this->_pages['mods/_standard/file_storage/comments.php']['title_var'] = 'comments';
$this->_pages['mods/_standard/file_storage/comments.php']['parent'] = 'mods/_standard/file_storage/index.php';
$this->_pages['mods/_standard/file_storage/comments.php']['children'] = array(); // empty array creates a "back to" link to index.php

$this->_pages['mods/_standard/file_storage/edit.php']['title_var'] = 'file_storage_edit_file';
$this->_pages['mods/_standard/file_storage/edit.php']['parent'] = 'mods/_standard/file_storage/index.php';
$this->_pages['mods/_standard/file_storage/edit.php']['children'] = array(); // empty array creates a "back to" link to index.php

$this->_pages['mods/_standard/file_storage/edit_folder.php']['title_var'] = 'file_storage_edit_folder';
$this->_pages['mods/_standard/file_storage/edit_folder.php']['parent'] = 'mods/_standard/file_storage/index.php';
$this->_pages['mods/_standard/file_storage/edit_folder.php']['children'] = array(); // empty array creates a "back to" link to index.php

$this->_pages['mods/_standard/file_storage/move.php']['title_var'] = 'file_storage_move';
$this->_pages['mods/_standard/file_storage/move.php']['parent'] = 'mods/_standard/file_storage/index.php';
$this->_pages['mods/_standard/file_storage/move.php']['children'] = array(); // empty array creates a "back to" link to index.php

$this->_pages['mods/_standard/file_storage/assignment.php']['title_var'] = 'hand_in';
$this->_pages['mods/_standard/file_storage/assignment.php']['parent'] = 'mods/_standard/file_storage/index.php';
$this->_pages['mods/_standard/file_storage/assignment.php']['children'] = array(); // empty array creates a "back to" link to index.php

$this->_pages['mods/_standard/file_storage/new.php']['title_var'] = 'file_storage_new_file';
$this->_pages['mods/_standard/file_storage/new.php']['parent'] = 'mods/_standard/file_storage/index.php';
$this->_pages['mods/_standard/file_storage/new.php']['children'] = array(); // empty array creates a "back to" link to index.php

$this->_pages['mods/_standard/file_storage/delete_revision.php']['title_var'] = 'delete';
$this->_pages['mods/_standard/file_storage/delete_revision.php']['parent'] = 'mods/_standard/file_storage/index.php';

$this->_pages['mods/_standard/file_storage/delete_comment.php']['title_var'] = 'delete';
//$this->_pages['file_storage/delete_comment.php']['parent'] = 'file_storage/comments.php';

function file_storage_get_group_url($group_id) {
	return 'mods/_standard/file_storage/index.php?ot='.WORKSPACE_GROUP.SEP.'oid='.$group_id;
}
?>