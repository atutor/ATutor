<?php
if (!defined('AT_INCLUDE_PATH')) { exit; }
if (!isset($this) || (isset($this) && (strtolower(get_class($this)) != 'module'))) { exit(__FILE__ . ' is not a Module'); }


$this->_stacks['announcement_subscription'] = array('title_var'=>'announcement_subscription', 'file'=>'mods/announcement_subscription/side_menu.inc.php');
?>
