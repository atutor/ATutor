<?php

/**
* 
* Tests multiple-path directory searches
*
* @version $Id: 6_paths.php,v 1.1 2004/04/06 17:56:26 joel Exp $
* 
*/

function preprint($val)
{
	echo "<pre>\n";
	print_r($val);
	echo "</pre>\n";
}

error_reporting(E_ALL);

require_once 'Savant.php';

$conf = array(
	'template_path' => 'test_templates',
	'plugin_path' => 'test_plugins',
	'filter_path' => 'test_filters'
);

$savant =& new Savant($conf);

echo "<h1>Paths to begin with</h1>\n";
preprint($savant->getPath('plugin'));
preprint($savant->getPath('filter'));
preprint($savant->getPath('template'));

echo "<h1>Add a path</h1>\n";
$savant->addPath('plugin', '/usr/lib/share');
preprint($savant->getPath('plugin'));

echo "<h1>Find an existing plugin (non-default)</h1>\n";
$file = $savant->_findFile('plugin', 'Savant_Plugin_cycle.php');
preprint($file);

echo "<h1>Find an existing plugin (default)</h1>\n";
$file = $savant->_findFile('plugin', 'Savant_Plugin_input.php');
preprint($file);

echo "<h1>Find a non-existent plugin</h1>\n";
$file = $savant->_findFile('template', 'no_such_template.tpl.php');
if ($file) {
	preprint($file);
} else {
	preprint("false or null");
}
?>