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
define('AT_ADMIN_PRIV_FLOWPLAYER', $this->getAdminPrivilege());

global $_custom_head;
$_custom_head .='<script type="text/javascript" src="'.AT_BASE_HREF.'mods/_standard/flowplayer/flowplayer-3.2.4.min.js"></script>'."\n";

?>