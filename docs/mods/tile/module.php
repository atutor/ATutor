<?php
if (!defined('AT_INCLUDE_PATH')) { exit; }

// if this module is to be made available to students on the Home or Main Navigation
$_modules[] = 'tile.php';

$_module_pages['tools/content/index.php']['children'] = array('tools/tile/index.php');

//instructor pages
$_module_pages['tools/tile/index.php']['title_var']  = 'tile_search';
$_module_pages['tools/tile/index.php']['parent'] = 'tools/content/index.php';
$_module_pages['tools/tile/index.php']['guide'] = 'instructor/?p=4.4.tile_repository.php';

	$_module_pages['tools/tile/import.php']['title_var']    = 'import_content_package';
	$_module_pages['tools/tile/import.php']['parent']   = 'tools/tile/index.php';

//student pages
$_module_pages['tile.php']['title_var'] = 'tile_search';
$_module_pages['tile.php']['img']       = 'images/home-tile_search.gif';

?>