<?php
if (!defined('AT_INCLUDE_PATH')) { exit; }
if (!isset($this) || (isset($this) && (strtolower(get_class($this)) != 'module'))) { exit(__FILE__ . ' is not a Module'); }

// if this module is to be made available to students on the Home or Main Navigation
$_student_tool = 'directory.php';

$this->_pages['directory.php']['title_var'] = 'directory';
$this->_pages['directory.php']['img']       = 'images/home-directory.gif';

?>