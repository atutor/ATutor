<?php

/**
* 
* Tests assign() issues
*
* @version $Id: 1_assign.php,v 1.1 2004/04/06 17:56:26 joel Exp $
* 
*/


error_reporting(E_ALL);

require_once 'Savant.php';
$savant =& new Savant(array('template_path' => 'test_templates'));

echo "<h1>assign 1 (string, mixed)</h1>";
$result = $savant->assign('variable', 'variable_value');
echo "result: <pre>";
print_r($result);
echo "</pre>";
echo "_tokens: <pre>";
print_r($savant->getTokens());
echo "</pre>";


echo "<h1>assign 2 (array)</h1>";
$result = $savant->assign(array('array1' => 'value1', 'array2' => 'value2'));
echo "result: <pre>";
print_r($result);
echo "</pre>";
echo "_tokens: <pre>";
print_r($savant->getTokens());
echo "</pre>";

echo "<h1>assign 3 (object)</h1>";
$object = new StdClass();
$object->obj1 = 'this';
$object->obj2 = 'that';
$object->obj3 = 'other';
$result = $savant->assign($object);
echo "result: <pre>";
print_r($result);
echo "</pre>";
echo "_tokens: <pre>";
print_r($savant->getTokens());
echo "</pre>";


echo "<h1>assignRef</h1>";
$reference = 'reference_value';
$result = $savant->assignRef('reference', $reference);
echo "result: <pre>";
print_r($result);
echo "</pre>";
echo "_tokens: <pre>";
print_r($savant->getTokens());
echo "</pre>";


echo "<h1>assignObject</h1>";
$object = new stdClass();
$result = $savant->assignObject('object', $object);
echo "result: <pre>";
print_r($result);
echo "</pre>";
echo "_tokens: <pre>";
print_r($savant->getTokens());
echo "</pre>";


echo "<h1>Assign variable without value</h1>";
$result = $savant->assign('variable_without_value');
echo "result: <pre>";
print_r($result);
echo "</pre>";
echo "_tokens: <pre>";
print_r($savant->getTokens());
echo "</pre>";


echo "<h1>Assign reference without value</h1>";
$result = $savant->assignRef('reference_without_value');
echo "result: <pre>";
print_r($result);
echo "</pre>";
echo "_tokens: <pre>";
print_r($savant->getTokens());
echo "</pre>";


echo "<h1>Assign object when value is not object</h1>";
$reference3 = 'failed!';
$result = $savant->assignObject('object2', $reference3);
echo "result: <pre>";
print_r($result);
echo "</pre>";
echo "_tokens: <pre>";
print_r($savant->getTokens());
echo "</pre>";


echo "<h1>Change reference values from logic</h1>";
$reference = 'CHANGED VALUE FROM LOGIC';
echo "_tokens: <pre>";
print_r($savant->getTokens());
echo "</pre>";


echo "<h1>Get an existing token</h1>";
echo "\$variable is <pre>";
print_r($savant->getTokens('variable'));
echo "</pre>";

echo "<h1>Get a nonexistent token</h1>";
echo "\$not_there is <pre>";
print_r($savant->getTokens('not_there'));
echo "</pre>";


$savant->display('assign.tpl.php');
echo "<p>After: $reference</p>";

?>