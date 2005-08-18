<?php
function get_privilege_bit() {

	return 1;
}


$privilege_bit = get_privilege_bit();
define('AT_PRIV_HELLO_WORLD', $privilege_bit);
$_privs[$privilege_bit] = array('name' => 'AT_PRIV_HELLO_WORLD');


// if this module is to be made available to students on the Home or Main Navigation
// make this module available as a Home page option:
$_modules[] = 'mods/hello_world/index.php';

// adding this module to the main page as a basic student tool:
$_pages['mods/hello_world/index.php']['title'] = 'Hello World';
$_pages['mods/hello_world/index.php']['img']   = 'mods/hello_world/images/hello_world.png';

// adding this modeul's instructor page to the course Manage page:
$_pages['mods/hello_world/instructor/index.php']['title']     = 'Hello World';
$_pages['mods/hello_world/instructor/index.php']['parent']    = 'tools/index.php';
$_pages['mods/hello_world/instructor/index.php']['privilege'] = AT_PRIV_ADMIN;


// adding a page to the course Manage page:
// deprecated
//$tools_list['modules/hello_world/instructor/index.php'] = AT_PRIV_ADMIN;
?>