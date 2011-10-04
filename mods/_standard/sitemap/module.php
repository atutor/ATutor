<?php
if (!defined('AT_INCLUDE_PATH')) { exit; }
if (!isset($this) || (isset($this) && (strtolower(get_class($this)) != 'module'))) { exit(__FILE__ . ' is not a Module'); }

// if this module is to be made available to students on the Home or Main Navigation
$_student_tool = 'mods/_standard/sitemap/sitemap.php';

$this->_pages['mods/_standard/sitemap/sitemap.php']['title_var'] = 'sitemap';
$this->_pages['mods/_standard/sitemap/sitemap.php']['parent']    = 'index.php';
$this->_pages['mods/_standard/sitemap/sitemap.php']['img']       = 'images/home-site_map.png';
$this->_pages['mods/_standard/sitemap/sitemap.php']['text']      = _AT('sitemap_text');

?>