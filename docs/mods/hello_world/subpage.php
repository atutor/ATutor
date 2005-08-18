<?php
define('AT_INCLUDE_PATH', '../../include/');
require (AT_INCLUDE_PATH.'vitals.inc.php');
require (AT_INCLUDE_PATH.'header.inc.php');
?>

<p>This is a sub page. The sub-navigation lists the parent page because of the <code>['parent']</code> entry in the <code>$_pages</code> array.</p>

<?php require (AT_INCLUDE_PATH.'footer.inc.php'); ?>