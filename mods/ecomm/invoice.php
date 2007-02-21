<?php

$_user_location	= 'users';
define('AT_INCLUDE_PATH', '../../include/');
require (AT_INCLUDE_PATH.'vitals.inc.php');

require (AT_INCLUDE_PATH.'html/frameset/header.inc.php');
debug($_GET);

require (AT_INCLUDE_PATH.'html/frameset/footer.inc.php');
?>