<?php
if (!defined('AT_INCLUDE_PATH')) { exit; }
if (!isset($this) || (isset($this) && (strtolower(get_class($this)) != 'module'))) { exit(__FILE__ . ' is not a Module'); }

$this->_pages['users/profile_picture.php']['title_var'] = 'picture';
$this->_pages['users/profile_picture.php']['parent']   = 'users/profile.php';

$this->_pages['users/profile.php']['children']  = array('users/profile_picture.php');

?>