<?php
/*******
 * doesn't allow this file to be loaded with a browser.
 */
if (!defined('AT_INCLUDE_PATH')) { exit; }

/******
 * this file must only be included within a ModuleProxy obj
 */
if (!isset($this) || (isset($this) && (strtolower(get_class($this)) != 'module'))) { exit(__FILE__ . ' is not a Module'); }

/*******
 * assign the instructor and admin privileges to the constants.
 */
define('AT_PRIV_EWIKI',       $this->getPrivilege());
define('AT_ADMIN_PRIV_EWIKI', $this->getAdminPrivilege());

/*******
 * if this module is to be made available to students on the Home or Main Navigation.
 */
$_student_tool = 'mods/wiki/index.php';


/*******
 * instructor Manage section:
 */
$this->_pages['mods/wiki/index.php']['title_var'] = 'wiki';
$this->_pages['mods/wiki/index.php']['parent']   = 'tools/index.php';
$this->_pages['mods/wiki/index.php']['img']       = 'mods/wiki/tlogo.png';


?>