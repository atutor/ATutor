<?php

define('AT_INCLUDE_PATH', '../../../include/');
require_once(AT_INCLUDE_PATH.'vitals.inc.php');
require_once('ErrorHandler.class.php');

echo '<html><body>';
$err =& new ErrorHandler();
$err->setFlags(true, true, true, true, true, true);

$hello = null;

if (array_shift($hello)) { jay; }

$_to = 'jacek.materna@rogers.com';

$err->setRecipients($_to);
$err->mailError();

echo '</body></html>';

?>