<?php
if (!defined('AT_INCLUDE_PATH')) { exit; }

// if this module is to be made available to students on the Home or Main Navigation
$_student_tool = 'mods/_standard/tile_search/tile.php';

// Add menu item into "Manage" => "Content" 
$this->_pages['mods/_core/content/index.php']['children'] = array('mods/_standard/tile_search/index.php');

// instructor page
$this->_pages['mods/_standard/tile_search/index.php']['title_var'] = 'tile_search';
$this->_pages['mods/_standard/tile_search/index.php']['parent'] = 'mods/_core/content/index.php';
$this->_pages['mods/_standard/tile_search/index.php']['children'] = array('mods/_standard/tile_search/import.php');
$this->_pages['mods/_standard/tile_search/index.php']['guide'] = 'instructor/?p=tile_repository.php';

$this->_pages['mods/_standard/tile_search/import.php']['title_var'] = 'import';
$this->_pages['mods/_standard/tile_search/import.php']['parent'] = 'mods/_standard/tile_search/index.php';

// student page
$this->_pages['mods/_standard/tile_search/tile.php']['title_var'] = 'tile_search';
$this->_pages['mods/_standard/tile_search/tile.php']['img'] = 'images/home-tile_search.png';
$this->_pages['mods/_standard/tile_search/tile.php']['text'] = _AT('tile_search_text');
?>