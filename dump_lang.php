#!/usr/bin/php
<?php
define('AT_INCLUDE_PATH', 'docs/include/');

require(AT_INCLUDE_PATH . 'cvs_development.inc.php');

$sql	= 'SELECT `variable`,`key`,`text`,`context` FROM lang_base WHERE project="atutor" ORDER BY `variable`, `key`';
$result = mysql_query($sql, $lang_db);

echo mysql_error();
ob_start();
echo "# Table structure for table 'lang_base'
#

CREATE TABLE `lang_base` (
  `variable` varchar(30) NOT NULL default '',
  `key` varchar(50) NOT NULL default '',
  `text` text NOT NULL,
  `revised_date` datetime NOT NULL default '0000-00-00 00:00:00',
  `context` text NOT NULL,
  PRIMARY KEY  (`variable`,`key`)
) TYPE=MyISAM;";

while($row = mysql_fetch_assoc($result)) {
	$row['text'] = mysql_real_escape_string($row['text']);
	$row['context'] = mysql_real_escape_string($row['context']);
	echo "INSERT INTO `lang_base` VALUES ('$row[variable]','$row[key]','$row[text]',NOW(),'$row[context]');\n";
}
ob_flush();

?>