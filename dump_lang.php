#!/usr/bin/php
<?php
define('AT_INCLUDE_PATH', 'docs/include/');

require(AT_INCLUDE_PATH . 'cvs_development.inc.php');

$sql	= 'SELECT `variable`,`key`,`text`,`context` FROM lang_base L ORDER BY `variable`, `key`';
$result = mysql_query($sql, $lang_db);

echo mysql_error();
ob_start();
while($row = mysql_fetch_assoc($result)) {
	$row['text'] = mysql_real_escape_string($row['text']);
	$row['context'] = mysql_real_escape_string($row['context']);
	echo "INSERT INTO `lang_base` VALUES ('$row[variable]','$row[key]','$row[text]',NOW(),'$row[context]');\n";
}
ob_flush();

?>