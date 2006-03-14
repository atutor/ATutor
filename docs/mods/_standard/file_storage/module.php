<?php
if (!defined('AT_INCLUDE_PATH')) { exit; }
if (!isset($this) || (isset($this) && (strtolower(get_class($this)) != 'module'))) { exit(__FILE__ . ' is not a Module'); }

define('AT_PRIV_FILE_STORAGE',       $this->getPrivilege() );


// if this module is to be made available to students on the Home or Main Navigation
$_group_tool = $_student_tool = 'file_storage/index.php';


//student pages
$this->_pages['file_storage/index.php']['title_var']  = 'file_storage';
$this->_pages['file_storage/index.php']['img']        = 'images/home-file_storage.gif';

$this->_pages['file_storage/revisions.php']['title_var'] = 'revisions';
$this->_pages['file_storage/revisions.php']['parent'] = 'file_storage/index.php';

$this->_pages['file_storage/comments.php']['title_var'] = 'comments';
$this->_pages['file_storage/comments.php']['parent'] = 'file_storage/index.php';

$this->_pages['file_storage/edit.php']['title_var'] = 'edit';
$this->_pages['file_storage/edit.php']['parent'] = 'file_storage/index.php';

$this->_pages['file_storage/edit_folder.php']['title_var'] = 'edit';
$this->_pages['file_storage/edit_folder.php']['parent'] = 'file_storage/index.php';

$this->_pages['file_storage/move.php']['title_var'] = 'move';
$this->_pages['file_storage/move.php']['parent'] = 'file_storage/index.php';

$this->_pages['file_storage/assignment.php']['title_var'] = 'assignment';
$this->_pages['file_storage/assignment.php']['parent'] = 'file_storage/index.php';

$this->_pages['file_storage/new.php']['title_var'] = 'new_file';
$this->_pages['file_storage/new.php']['parent'] = 'file_storage/index.php';

$this->_pages['file_storage/delete_revision.php']['title_var'] = 'delete';
$this->_pages['file_storage/delete_revision.php']['parent'] = 'file_storage/index.php';

$this->_pages['file_storage/delete_comment.php']['title_var'] = 'delete';
//$this->_pages['file_storage/delete_comment.php']['parent'] = 'file_storage/comments.php';
?>