<?php
if (!defined('AT_INCLUDE_PATH')) { exit; }
if (!isset($this) || (isset($this) && (strtolower(get_class($this)) != 'module'))) { exit(__FILE__ . ' is not a Module'); }

define('AT_PRIV_ANNOUNCEMENTS', $this->getPrivilege());
//debug(AT_PRIV_ANNOUNCEMENTS);
//exit;
$this->_pages['mods/_standard/announcements/index.php']['title_var'] = 'announcements';
$this->_pages['mods/_standard/announcements/index.php']['guide']     = 'instructor/?p=announcements.php';
$this->_pages['mods/_standard/announcements/index.php']['parent']    = 'tools/index.php';
$this->_pages['mods/_standard/announcements/index.php']['children']  = array('mods/_standard/announcements/add_news.php');

	$this->_pages['mods/_standard/announcements/add_news.php']['title_var']  = 'add_announcement';
	$this->_pages['mods/_standard/announcements/add_news.php']['parent'] = 'mods/_standard/announcements/index.php';

	$this->_pages['mods/_standard/announcements/edit_news.php']['title_var']  = 'edit_announcement';
	$this->_pages['mods/_standard/announcements/edit_news.php']['parent'] = 'mods/_standard/announcements/index.php';

	$this->_pages['mods/_standard/announcements/delete_news.php']['title_var']  = 'delete_announcement';
	$this->_pages['mods/_standard/announcements/delete_news.php']['parent'] = 'mods/_standard/announcements/index.php';
	
if($_SESSION['is_admin'] > 0 || authenticate(AT_PRIV_ANNOUNCEMENTS, TRUE)){	
	$this->_pages_i['mods/_standard/announcements/add_news.php']['title_var']  = 'add_announcement';
	$this->_pages_i['mods/_standard/announcements/add_news.php']['other_parent'] = 'index.php';
	$this->_pages_i['index.php']['children']  = array('mods/_standard/announcements/add_news.php');
}
?>