<?php


if (!defined('AT_INCLUDE_PATH')) { exit; }
if (!isset($this) || (isset($this) && (strtolower(get_class($this)) != 'module'))) { exit(__FILE__ . ' is not a Module'); }

// if this module is to be made available to students on the Home or Main Navigation
$_student_tool = 'mods/_core/imscp/export.php';

//instructors
//$this->_pages['mods/_core/imscp/index.php']['title_var'] = 'content_packaging';
//$this->_pages['mods/_core/imscp/index.php']['parent']    = 'tools/content/index.php';
//$this->_pages['mods/_core/imscp/index.php']['guide']     = 'instructor/?p=content_packages.php';

//students
//$this->_pages['export.php']['title_var'] = 'export_content';
//$this->_pages['export.php']['img']       = 'images/home-export_content.png';
//$this->_pages['export.php']['text']      = _AT('export_content_text');
//$this->_pages['export.php']['guide']     = 'general/?p=export_content.php';

?>