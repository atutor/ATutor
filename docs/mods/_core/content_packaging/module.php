<?php
if (!defined('AT_INCLUDE_PATH')) { exit; }
if (!isset($this) || (isset($this) && (strtolower(get_class($this)) != 'module'))) { exit(__FILE__ . ' is not a Module'); }

// if this module is to be made available to students on the Home or Main Navigation
$_student_tool = 'export.php';

//instructors
$this->_pages['tools/ims/index.php']['title_var'] = 'content_packaging';
$this->_pages['tools/ims/index.php']['parent']    = 'tools/content/index.php';
$this->_pages['tools/ims/index.php']['guide']     = 'instructor/?p=4.2.content_packages.php';

//students
$this->_pages['export.php']['title_var'] = 'export_content';
$this->_pages['export.php']['img']       = 'images/home-export_content.gif';
$this->_pages['export.php']['guide']     = 'general/?p=6.1.export_content.php';

?>