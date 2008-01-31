<?php
define('AT_INCLUDE_PATH', '../../../include/');
require (AT_INCLUDE_PATH.'vitals.inc.php');
admin_authenticate(AT_ADMIN_PRIV_PHPDOC);
$_custom_css = $_base_path . 'mods/phpdoc2/module.css'; // use a custom stylesheet

if (!function_exists('version_compare'))
{
    print "phpDocumentor requires PHP version 4.1.0 or greater to function";
    exit;
}

// Find out if we are submitting and if we are, send it
// This code originally by Joshua Eichorn on phpdoc.php
//
if (isset($_GET['dataform']) && empty($_REQUEST['altuserdir'])) {
	foreach ($_GET as $k=>$v) {
		if (strpos( $k, 'setting_' ) === 0) {
			$_GET['setting'][substr( $k, 8 )] = $v;
		}
	}

	//ATutor presets
	$_GET['setting']['output']				= 'HTML:frames:DOM/earthli';		//Theme
	$_GET['setting']['directory']			= AT_INCLUDE_PATH;					//Source codes location, use "include/"
	$_GET['setting']['defaultpackagename']	= 'ATutor '._AT('atutor_version');	//ATutor package

	//Check if the directory is writable, if not, halt.
	if (!(is_dir($_GET['setting']['target']) && is_writable($_GET['setting']['target']))) {
		$msg->addError(array('API_DIRECTORY_NOT_WRITABLE', $_GET['setting']['target']));
		debug($_GET);exit;
		header("Location: index.php");
		exit;
	}
	echo "<strong>Parsing Files ...</strong><br/>";
	exit;
	flush();
	echo "<div style='overflow:auto; height:300px;'><pre>\n";
	/** phpdoc.inc */
	include("phpDocumentor/phpdoc.inc");
	echo "</pre></div>\n";
	echo "<strong>Operation Completed!!</strong>";
} 


// set up include path so we can find all files, no matter what
$GLOBALS['_phpDocumentor_install_dir'] = dirname(dirname(realpath(__FILE__)));
$root_dir = '';

/**
* common file information
*/
include_once("phpDocumentor/common.inc.php");
// add my directory to the include path, and make it first, should fix any errors
if (substr(PHP_OS, 0, 3) == 'WIN')
{
	ini_set('include_path',$GLOBALS['_phpDocumentor_install_dir'].';'.ini_get('include_path'));
} else {
	ini_set('include_path',$GLOBALS['_phpDocumentor_install_dir'].':'.ini_get('include_path'));
}
// find the .ini directory by parsing phpDocumentor.ini and extracting _phpDocumentor_options[userdir]
$ini = phpDocumentor_parse_ini_file($_phpDocumentor_install_dir . PATH_DELIMITER . 'phpDocumentor.ini', true);
if (isset($ini['_phpDocumentor_options']['userdir']))
{
	$configdir = $ini['_phpDocumentor_options']['userdir'];
} else {
	$configdir = $_phpDocumentor_install_dir . '/user';
}


/* Template starts here */
require (AT_INCLUDE_PATH.'header.inc.php'); ?>
<div id="phpdoc2_installer">
<form name="dataForm" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
	<table>
	<tr><td class="title"><label for="setting_title">API Title: </label></td>
		<td><input type="text" name="setting_title" id="setting_title" value="ATutor 1.6" size="40" /></td></tr>
	<tr><td class="title"><label for="setting_target">Save to... (relative path is allowed): </label></td>
		<td><input type="text" name="setting_target" id="setting_target" value="../apidoc" size="40" /></td></tr>
	<tr><td class="title"><label for="setting_ignore">These files will be ignored: </label></td>
		<td><input type="text" name="setting_ignore" id="setting_ignore" value="*.svn" size="40" /></td></tr>
	<tr>
		<td><input type="SUBMIT" value="Go" name="submitButton" /></td>
	</tr>
	</table>
	<input type="hidden" name="dataform" value="true">
</form>
</div>
<?php require (AT_INCLUDE_PATH.'footer.inc.php'); ?>
