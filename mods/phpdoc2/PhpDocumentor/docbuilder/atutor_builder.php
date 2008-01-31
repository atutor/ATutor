<?php
if (!function_exists('version_compare'))
{
    print "phpDocumentor requires PHP version 4.1.0 or greater to function";
    exit;
}

if ('@DATA-DIR@' != '@'.'DATA-DIR@') {
    // set up include path so we can find all files, no matter what
    $root_dir = 'PhpDocumentor';
    /**
    * common file information
    */
    include_once("$root_dir/phpDocumentor/common.inc.php");
    $GLOBALS['_phpDocumentor_install_dir'] = 'PhpDocumentor';
    // find the .ini directory by parsing phpDocumentor.ini and extracting _phpDocumentor_options[userdir]
    $ini = phpDocumentor_parse_ini_file('@DATA-DIR@/PhpDocumentor/phpDocumentor.ini', true);
    if (isset($ini['_phpDocumentor_options']['userdir']))
    {
        $configdir = $ini['_phpDocumentor_options']['userdir'];
    } else {
        $configdir = '@DATA-DIR@/user';
    }
} else {
    // set up include path so we can find all files, no matter what
    $GLOBALS['_phpDocumentor_install_dir'] = dirname(dirname(realpath(__FILE__)));
    $root_dir = dirname(dirname(__FILE__));
    /**
    * common file information
    */
    include_once("$root_dir/phpDocumentor/common.inc.php");
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
}



// allow the user to change this at runtime
if (!empty($_REQUEST['altuserdir'])) $configdir = $_REQUEST['altuserdir'];
?>

<form name="dataForm" action="atutor_builder.php" method="get">
	<table>
	<tr>
		<td>Title: <input type="text" name="setting_title" value="ATutor 1.6" /></td>
	</tr>

	<tr>
		<td>Package Name: <input type="text" name="setting_defaultpackagename" value="ATutor 1.6" /></td>
	</tr>

	<tr>
		<td>Save to...: <input type="text" name="setting_target" value="C:/xampp/htdocs/atutor155/ATutor_16/mods/phpdoc2/PhpDocumentor/apidoc/" /></td>
	</tr>

	<tr>
		<td>Template: <input type="text" name="setting_output" value="HTML:frames:DOM/earthli"/></td>
	</tr>

	<tr>
		<td>Source: <input type="text" name="setting_directory" value="C:/xampp/htdocs/atutor155/ATutor_16/include"/></td>
	</tr>

	<tr>
		<td>Ignore these files: <input type="text" name="setting_ignore" value="*.svn"/></td>
	</tr>

	<tr>
		<td><input type="SUBMIT" value="Go" name="submitButton" /></td>
	</tr>
	</table>
	<input type="hidden" name="dataform" value="true">
</form>

<?php
// Find out if we are submitting and if we are, send it
// This code originally by Joshua Eichorn on phpdoc.php
//
if (isset($_GET['dataform']) && empty($_REQUEST['altuserdir'])) {
	foreach ($_GET as $k=>$v) {
		if (strpos( $k, 'setting_' ) === 0) {
			$_GET['setting'][substr( $k, 8 )] = $v;
		}
	}

	echo "<strong>Parsing Files ...</strong>";
	flush();
	echo "<div style='overflow:auto; height:300px;'><pre>\n";
	/** phpdoc.inc */
//	include("$root_dir/phpDocumentor/phpdoc.inc");
	echo "</pre></div>\n";
	echo "<h1>Operation Completed!!</h1>";
} else {
	echo "Waiting to go...";
}

?>