<?php
if (!defined('AT_INCLUDE_PATH')) { exit; }
if (!isset($this) || (isset($this) && (strtolower(get_class($this)) != 'module'))) { exit(__FILE__ . ' is not a Module'); }



$this->_pages['mods/_standard/profile_pictures/profile_picture.php']['title_var'] = 'picture';
$this->_pages['mods/_standard/profile_pictures/profile_picture.php']['parent']   = 'users/profile.php';

$this->_pages['users/profile.php']['children']  = array_merge((array) $this->_pages['users/profile.php']['children'],array('mods/_standard/profile_pictures/profile_picture.php'));

if (admin_authenticate(AT_ADMIN_PRIV_USERS, TRUE)) {
	$this->_pages['mods/_standard/profile_pictures/admin/profile_picture.php']['title_var'] = 'picture';
	$this->_pages['mods/_standard/profile_pictures/admin/profile_picture.php']['parent']   = 'admin/users.php';
}
?>