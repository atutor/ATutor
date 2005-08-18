<?php
define('AT_INCLUDE_PATH', '../../../include/');
require (AT_INCLUDE_PATH.'vitals.inc.php');
authenticate(AT_PRIV_ADMIN);


require (AT_INCLUDE_PATH.'header.inc.php');
?>

<p>Hello Mr. Instructor World!</p>

<p>Use the <a href="tools/modules.php">Student Tools</a> section to enable the student version of this module.</p>

<?php require (AT_INCLUDE_PATH.'footer.inc.php'); ?>