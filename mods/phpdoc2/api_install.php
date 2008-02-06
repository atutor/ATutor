<?php
define('AT_INCLUDE_PATH', '../../include/');
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
	$_GET['setting']['defaultpackagename']	= 'ATutor '.VERSION;				//ATutor package

	//Check if the directory is writable, if not, halt.
	if (!(is_dir($_GET['setting']['target']) && is_writable($_GET['setting']['target']))) {
		$msg->addError(array('API_NOT_WRITABLE', $_GET['setting']['target']), "directory");
		header("Location: api_install.php");
		exit;
	} elseif(!(is_file($_GET['setting']['target'].'/index.html') && is_writable($_GET['setting']['target'].'/index.html'))) {
		//Check if the index.html file is writable
		$msg->addError(array('API_NOT_WRITABLE', $_GET['setting']['target'].'/index.html'), "file");
		header("Location: api_install.php");
		exit;
	} else {
		//Output api convertion steps
		require (AT_INCLUDE_PATH.'header.inc.php'); 		
		echo "<strong>Parsing Files ...</strong><br/>";
		flush();
		echo "<div id='api_process_box'><pre>\n";
		/** phpdoc.inc */
		include("PhpDocumentor/phpDocumentor/phpdoc.inc");
		echo "</pre></div>\n";
		echo "<strong>Operation Completed!!</strong> <a href=\"mods/phpdoc2/".$_GET['setting']['target']."\" target=\"atutor_api\">View API Here</a>.";
		require (AT_INCLUDE_PATH.'footer.inc.php');
		exit;
	}
}


// set up include path so we can find all files, no matter what
$GLOBALS['_phpDocumentor_install_dir'] = dirname(dirname(realpath(__FILE__)));
$root_dir = '';

/**
* common file information
*/
include_once("PhpDocumentor/phpDocumentor/common.inc.php");
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
<form name="dataForm" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="GET">
	<table>
	<tr><td class="title"><label for="setting_title">API Title: </label></td>
		<td><input type="text" name="setting_title" id="setting_title" value="<?php echo 'ATutor '.VERSION; ?>" size="40" /></td></tr>
	<tr><td class="title"><label for="setting_target">Save to... (relative path is allowed): </label></td>
		<td><input type="text" name="setting_target" id="setting_target" value="apidoc" size="40" /></td></tr>
	<tr><td class="title"><label for="setting_ignore">These files will be ignored: </label></td>
		<td><input type="text" name="setting_ignore" id="setting_ignore" value="*.svn" size="40" /></td></tr>
	<tr>
		<td><input class="buttons" type="SUBMIT" value="Go" name="submitButton" /></td>
	</tr>
	</table>
	<input type="hidden" name="dataform" value="true">
</form>
	<div class="license">
		<p>This feature is provided by <a href="http://www.phpdoc.org/" target="phpdoc">phpDocumentor 1.4.1</a>.</p>
	</div>
</div>

<?php require (AT_INCLUDE_PATH.'footer.inc.php'); ?>
