<?php
/*******
 * doesn't allow this file to be loaded with a browser.
 */
if (!defined('AT_INCLUDE_PATH')) { exit; }

/******
 * this file must only be included within a Module obj
 */
if (!isset($this) || (isset($this) && (strtolower(get_class($this)) != 'module'))) { exit(__FILE__ . ' is not a Module'); }

/*******
 * create a side menu box/stack.
 */
$this->_stacks['cpref_switch'] = array('title_var'=>'cpref_switch', 'file'=>dirname(__FILE__).'/side_menu.inc.php');

global $_custom_css, $_base_path;

$_custom_css = $_base_path . 'mods/cpref_switch/module.css'; // use a custom stylesheet

?>