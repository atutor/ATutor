<?php
if (!defined('AT_INCLUDE_PATH')) { exit; }
if (!isset($this) || (isset($this) && (strtolower(get_class($this)) != 'module'))) { exit(__FILE__ . ' is not a Module'); }

//define('AT_PRIV_TOOLBAR', $this->getPrivilege());

$this->_pages['tools/toolmanager/index.php']['title_var'] = 'tool_manager'; 
$this->_pages['tools/toolmanager/index.php']['parent']    = 'tools/index.php';

?>