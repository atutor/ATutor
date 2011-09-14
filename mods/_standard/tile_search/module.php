<?php
if (!defined('AT_INCLUDE_PATH')) { exit; }

global $_config;

// if this module is to be made available to students on the Home or Main Navigation
$_student_tool = 'mods/_standard/tile_search/tile.php';

// Add menu item into "Manage" => "Content" 
$this->_pages['mods/_core/content/index.php']['children'] = array('mods/_standard/tile_search/index.php');

// admin page
if (admin_authenticate(AT_ADMIN_PRIV_ADMIN, TRUE)) {
	$this->_pages['admin/config_edit.php']['children'] = array('mods/_standard/tile_search/admin/module_setup.php');

	$this->_pages['mods/_standard/tile_search/admin/module_setup.php']['title_var'] = 'tile_setup';
	$this->_pages['mods/_standard/tile_search/admin/module_setup.php']['parent']    = 'admin/config_edit.php';
//	$this->_pages['mods/_standard/tile_search/admin/module_setup.php']['guide']     = 'admin/?p=tranformable_key.php';
}

// instructor page
$this->_pages['mods/_standard/tile_search/index.php']['title_var'] = 'tile_search';
$this->_pages['mods/_standard/tile_search/index.php']['parent'] = 'mods/_core/content/index.php';
//$this->_pages['mods/_standard/tile_search/index.php']['children'] = array('mods/_standard/tile_search/import.php');
$this->_pages['mods/_standard/tile_search/index.php']['guide'] = 'instructor/?p=tile_repository.php';

$this->_pages['mods/_standard/tile_search/import.php']['title_var'] = 'import';
$this->_pages['mods/_standard/tile_search/import.php']['parent'] = 'mods/_standard/tile_search/index.php';

// student page
$this->_pages['mods/_standard/tile_search/tile.php']['title_var'] = 'tile_search';
$this->_pages['mods/_standard/tile_search/tile.php']['img'] = 'images/home-tile_search.png';
$this->_pages['mods/_standard/tile_search/tile.php']['text'] = _AT('tile_search_text');

/* Constants for Transformable web service - @author Cindy */
// The URL to the transformable web service search entry
define('AT_TILE_SEARCH_URL', $_config['transformable_uri'].'search.php');

// The URL to view the transformable course
define('AT_TILE_VIEW_COURSE_URL', $_config['transformable_uri'].'home/course/index.php?_course_id=');

// The URL to export content package from transformable
define('AT_TILE_EXPORT_CP_URL', $_config['transformable_uri'].'home/ims/ims_export.php?course_id=');

// The URL to export common cartridge from transformable
define('AT_TILE_EXPORT_CC_URL', $_config['transformable_uri'].'home/imscc/ims_export.php?to_a4a=1&course_id=');

// The URL to import common cartridge into transformable
define('AT_TILE_IMPORT_URL', $_config['transformable_uri'].'home/ims/ims_import.php');

// The URLs to perform oauth authentication
define('AT_TILE_OAUTH_REGISTER_CONSUMER_URL', $_config['transformable_uri'].'oauth/register_consumer.php');
define('AT_TILE_OAUTH_REQUEST_TOKEN_URL', $_config['transformable_uri'].'oauth/request_token.php');
define('AT_TILE_OAUTH_AUTHORIZATION_URL', $_config['transformable_uri'].'oauth/authorization.php');
define('AT_TILE_OAUTH_ACCESS_TOKEN_URL', $_config['transformable_uri'].'oauth/access_token.php');

/* END - Constants for Transformable web service */

?>