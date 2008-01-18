<?php
define('AT_INCLUDE_PATH', '../../include/');
require (AT_INCLUDE_PATH.'vitals.inc.php');
admin_authenticate(AT_ADMIN_PRIV_PHPDOC);
require (AT_INCLUDE_PATH.'header.inc.php');
?>

<p style="border:thin black solid;padding:1em; width:75%;">This utility will generate API documentation for ATutor. First click on Generate API Documentation below, then click on View API to open a framed display of the API documentation.</p>
<p>
| <a href="mods/phpdoc/PHPDoc/index.php">Generate API Documentation</a> |
<a href="mods/phpdoc/PHPDoc/apidoc/keep/index2.html" target="_new">View API</a> |
</p>

<?php require (AT_INCLUDE_PATH.'footer.inc.php'); ?>