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
define('AT_PRIV_FHA_REFRESHER',       $this->getPrivilege());

/*******
 * instructor Manage section:
 */
$this->_pages['mods/fha_refresher/index.php']['title_var'] = 'fha_refresher';
$this->_pages['mods/fha_refresher/index.php']['parent']   = 'tools/index.php';

?>