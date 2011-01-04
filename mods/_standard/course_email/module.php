<?php
if (!defined('AT_INCLUDE_PATH')) { exit; }
if (!isset($this) || (isset($this) && (strtolower(get_class($this)) != 'module'))) { exit(__FILE__ . ' is not a Module'); }

define('AT_PRIV_COURSE_EMAIL', $this->getPrivilege());

$this->_pages['mods/_standard/course_email/course_email.php']['title_var'] = 'course_email';
$this->_pages['mods/_standard/course_email/course_email.php']['parent']    = 'tools/index.php';
$this->_pages['mods/_standard/course_email/course_email.php']['guide']     = 'instructor/?p=course_email.php';


?>