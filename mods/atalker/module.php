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
define('AT_PRIV_ATALKER',       $this->getPrivilege());
define('AT_ADMIN_PRIV_ATALKER', $this->getAdminPrivilege());

/*******
 * if this module is to be made available to students on the Home or Main Navigation.
 */
$_student_tool = 'mods/atalker/index.php';

/*******
 * add the admin pages when needed.
 */
if (admin_authenticate(AT_ADMIN_PRIV_HELLO_WORLD, TRUE) || admin_authenticate(AT_ADMIN_PRIV_ADMIN, TRUE)) {
	$this->_pages[AT_NAV_ADMIN] = array('mods/atalker/admin/admin_index.php');
	$this->_pages['mods/atalker/admin/admin_index.php']['parent']    = AT_NAV_ADMIN;
	$this->_pages['mods/atalker/admin/admin_index.php']['title_var'] = 'atalker';
}

/*******
 * student  and instructor page.
 */
$this->_pages['mods/atalker/index.php']['title_var'] = 'atalker';
$this->_pages['mods/atalker/index.php']['img']       = 'mods/atalker/images/atalker.gif';


/* Modified by Eura Ercolani: mimetype support - BEGIN */

if(isset($_POST['mp3HiddenMimeType']))
{
	$_SESSION['mp3HiddenMimeType']=$_POST['mp3HiddenMimeType'];
}
/* Modified by Eura Ercolani: mimetype support - END */
/* Modified by Eura Ercolani: introducing global variabiles - BEGIN */
//ATutor root web folder
$_ATutor_home_path = '/home/eura/public_html/ATutor/';


/* Modified by Eura Ercolani: introducing global variables - END */

?>