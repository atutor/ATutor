<?php

define('AT_INCLUDE_PATH', '../../include/');
require_once(AT_INCLUDE_PATH.'vitals.inc.php');

$_custom_css = $_base_path.'mods/adobe_connect/module.css';


require(AT_INCLUDE_PATH.'header.inc.php');


echo '<div id="adobe_connect">';

echo _AT("adobe_connect_text").'<br /><br />';
echo '<a title="'._AT("adobe_connect_access").'" href="'.$_base_path.'mods/adobe_connect/loader.php" target="_blank" onClick="window.open(this.href, this.target, \'width=750,height=600\');return false;">'._AT("adobe_connect_access").'</a>';

echo '</div>';


require(AT_INCLUDE_PATH.'footer.inc.php');

?>
