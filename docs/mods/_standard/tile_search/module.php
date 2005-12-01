<?php
if (!defined('AT_INCLUDE_PATH')) { exit; }
if (!isset($this) || (isset($this) && (strtolower(get_class($this)) != 'module'))) { exit(__FILE__ . ' is not a Module'); }

/* the URL to the WSDL of the TILE repository of choice. */
define('AT_TILE_WSDL', 'http://tile.atutor.ca/tile/services/search?wsdl');

/* the URL to the content package export servlet of the TILE repository of choice. */
define('AT_TILE_EXPORT', 'http://tile.atutor.ca/tile/servlet/export');

/* the URL to the content importing servlet of the TILE repository. */
define('AT_TILE_IMPORT', 'http://tile.atutor.ca/tile/servlet/put');

define('AT_TILE_PREVIEW', 'http://tile.atutor.ca/tile/servlet/view?view=item&');

// if this module is to be made available to students on the Home or Main Navigation
$_student_tool = 'tile.php';

$this->_pages['tools/content/index.php']['children'] = array('tools/tile/index.php');

//instructor pages
$this->_pages['tools/tile/index.php']['title_var']  = 'tile_search';
$this->_pages['tools/tile/index.php']['parent'] = 'tools/content/index.php';
$this->_pages['tools/tile/index.php']['guide'] = 'instructor/?p=4.4.tile_repository.php';

	$this->_pages['tools/tile/import.php']['title_var']    = 'import_content_package';
	$this->_pages['tools/tile/import.php']['parent']   = 'tools/tile/index.php';

//student pages
$this->_pages['tile.php']['title_var'] = 'tile_search';
$this->_pages['tile.php']['img']       = 'images/home-tile_search.gif';

?>