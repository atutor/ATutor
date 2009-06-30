<?php
if (!defined('AT_INCLUDE_PATH')) { exit; }
if (!isset($this) || (isset($this) && (strtolower(get_class($this)) != 'module'))) { exit(__FILE__ . ' is not a Module'); }

// if this module is to be made available to students on the Home or Main Navigation
$_student_tool = 'sitemap.php';

$this->_pages['sitemap.php']['title_var'] = 'sitemap';
$this->_pages['sitemap.php']['parent']    = 'index.php';
$this->_pages['sitemap.php']['img']       = 'images/home-site_map.png';
$this->_pages['sitemap.php']['text']      = _AT('sitemap_text');

?>