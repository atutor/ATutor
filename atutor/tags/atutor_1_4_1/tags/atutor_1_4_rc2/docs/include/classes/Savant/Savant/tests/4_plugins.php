<?php

/**
* 
* Tests default plugins
*
* @version $Id: 4_plugins.php,v 1.1 2004/04/06 17:56:26 joel Exp $
* 
*/

error_reporting(E_ALL);

require_once 'Savant.php';

$conf = array(
	'template_path' => 'test_templates',
	'plugin_path' => 'test_plugins'
);

$savant =& new Savant($conf);

$array = array(
	'key0' => 'val0',
	'key1' => 'val1',
	'key2' => 'val2',
);

$var1 = 'variable1';
$var2 = 'variable2';
$var3 = 'variable3';

$ref1 = 'reference1';
$ref2 = 'reference2';
$ref3 = 'reference3';

// assign vars
$savant->assign($var1, $var1);
$savant->assign($var2, $var2);
$savant->assign($var3, $var3);

// assigns $array to a variable $set
$savant->assign('set', $array);

// assigns the keys and values of array
$savant->assign($array);

// assign references
$savant->assignRef($ref1, $ref1);
$savant->assignRef($ref2, $ref2);
$savant->assignRef($ref3, $ref3);

// run through the template
$savant->display('plugins.tpl.php');

?>