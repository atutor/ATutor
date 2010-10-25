<?php

if (!defined('AT_INCLUDE_PATH')) { exit;}

if (!isset($this) ||
   (isset($this) && (strtolower(get_class($this) != 'Module')))) {
    exit(__FILE__ . ' is not a module');
}


define('AT_PRIV_ADOBE_CONNECT', $this->getPrivilege());
define('AT_ADMIN_PRIV_ADOBE_CONNECT', $this->getAdminPrivilege());


$_student_tool = 'mods/adobe_connect/index.php';


// admin
if (admin_authenticate(AT_ADMIN_PRIV_ADOBE_CONNECT, TRUE) || admin_authenticate(AT_ADMIN_PRIV_ADMIN, TRUE)) {
        $this->_pages[AT_NAV_ADMIN] = array('mods/adobe_connect/index_admin.php');
        $this->_pages['mods/adobe_connect/index_admin.php']['title_var'] = 'adobe_connect';
        $this->_pages['mods/adobe_connect/index_admin.php']['parent']    = AT_NAV_ADMIN;
}


// course -> home
$this->_pages['mods/adobe_connect/index.php']['title_var'] = 'adobe_connect';
$this->_pages['mods/adobe_connect/index.php']['img']       = 'mods/adobe_connect/adobe_connect.gif';


?>
