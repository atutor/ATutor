<?php

/**
* 
* Tests filters and plugins
*
* @version $Id: 5_filters.php,v 1.7 2004/03/04 01:01:00 pmjones Exp $
* 
*/


error_reporting(E_ALL);

require_once 'Savant.php';

$conf = array(
	'template_path' => 'test_templates',
	'plugin_path' => 'test_plugins',
	'filter_path' => 'test_filters'
);

$savant =& new Savant($conf);

// set up filters
$savant->setFilter('colorizeCode');
$savant->setFilter('trimwhitespace');
$savant->setFilter('fester');

// run through the template
$savant->display('filters.tpl.php');

// do it again to test object persistence
$savant->display('filters.tpl.php');



?>