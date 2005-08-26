<?php
if (!defined('AT_INCLUDE_PATH')) { exit; }

define('AT_PRIV_COURSE_EMAIL', $this->getPrivilege());

$_module_pages['tools/course_email.php']['title_var'] = 'course_email';
$_module_pages['tools/course_email.php']['parent']    = 'tools/index.php';
$_module_pages['tools/course_email.php']['guide']     = 'instructor/?p=5.0.course_email.php';

?>