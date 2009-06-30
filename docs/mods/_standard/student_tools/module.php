<?php
/*******
 * doesn't allow this file to be loaded with a browser.
 */
if (!defined('AT_INCLUDE_PATH')) { exit; }

/******
 * this file must only be included within a Module obj
 */
if (!isset($this) || (isset($this) && (strtolower(get_class($this)) != 'module'))) { exit(__FILE__ . ' is not a Module'); }

$_student_tool = 'mods/_standard/student_tools/index.php';


/*******
 * instructor Manage section:
 */
$this->_pages['mods/_standard/student_tools/instructor_index.php']['title_var'] = 'student_tools';
$this->_pages['mods/_standard/student_tools/instructor_index.php']['parent']    = 'tools/index.php';
$this->_pages['mods/_standard/student_tools/instructor_index.php']['guide']     = 'instructor/?p=fha_student_tools.php';
$this->_pages['mods/_standard/student_tools/index.php']['title_var'] = 'student_tools';
$this->_pages['mods/_standard/student_tools/index.php']['img']       = 'mods/_standard/student_tools/icon.gif';
$this->_pages['mods/_standard/student_tools/index.php']['text']      = _AT('student_tools_text');
?>