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
 * assign the instructor and admin privileges to the constants.
 */
define('AT_PRIV_PDF_CONVERTER',       $this->getPrivilege());
define('AT_ADMIN_PRIV_PDF_CONVERTER', $this->getAdminPrivilege());

/*******
 * create a side menu box/stack.
 */
$this->_stacks['pdf_converter'] = array('title_var'=>'pdf_converter', 'file'=>'mods/pdf_converter/side_menu.inc.php');

$this->_pages['mods/pdf_converter/index.php']['img']  = 'mods/pdf_converter/pdf_icon.jpg';


?>