<?php
/****************************************************************/
/* ATutor														                            */
/****************************************************************/
/* Copyright (c) 2002-2008 by Greg Gay & Cindy Qi Li            */
/* Adaptive Technology Resource Centre / University of Toronto  */
/* http://atutor.ca												                      */
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.				        */
/****************************************************************/
// $Id: utf8conv.php 2008-01-23 14:49:24Z cindy $
/*
 * This script only works when being included in the scripts
 * where vitals.inc.php is included as:
 * 
 * define('AT_INCLUDE_PATH', '../../include/');
 * require (AT_INCLUDE_PATH.'vitals.inc.php');
 * 
 * structure of this document (in order):
 * 
 * 1. Unzip uploaded file to module's content directory
 * 2. Read content folder recursively to convert
 *    Read thru all html and xml file to find all character sets
 *    definitions. If number of character sets are:
 *    1) 1: perfect scenario, convert all given file types to it
 *    2) 0: display default charsets for user to choose one of them
 *          and convert all given file types, except html, xml, to it
 *    3) More than 1: display them for user to choose one and 
 *          convert all given file types, except html, xml, to it
 * 3. Zip converted files
 * 4. force zipped converted file to download
 * 5. clear all temp files
 *
 ***/

// Define character set to convert to
$charset_to = "UTF-8";

// Define all file types to be converted
$filetypes = ARRAY('html', 'xml', 'csv', 'txt', 'sql');

$filetypes = array_change_key_case($filetypes);   // use lower case

$default_charsets = ARRAY(
'ISO-8859-1','ISO-8859-2','ISO-8859-3','ISO-8859-4','ISO-8859-5','ISO-8859-6','ISO-8859-7',
'ISO-8859-8','ISO-8859-9','ISO-8859-10','ISO-8859-11','ISO-8859-13','ISO-8859-14',
'ISO-8859-15','ISO-8859-16','BIG5','EUC-JP','GB2312','US-ASCII','WINDOWS-874','WINDOWS-936',
'WINDOWS-1250','WINDOWS-1251','WINDOWS-1252','WINDOWS-1253','WINDOWS-1254','WINDOWS-1255',
'WINDOWS-1256','WINDOWS-1257','WINDOWS-1258');

$charsets = array();

// This function finds charset definition from html & xml files
function find_charset($filename)
{
	if (preg_match("/\.html$/i", $filename)) 
	{ 
		$pattern = '/<meta.*charset=(.*) /i';
		preg_match($pattern, file_get_contents($filename), $matches);
		
		// remove quote signs in the match
		$charset = strtoupper(trim(preg_replace('/(\'|\")/', '', $matches[1])));
	}
	
	if (preg_match("/\.xml$/i", $filename)) 
	{ 
		if (preg_match("#<charset>(.*)</charset>#i", file_get_contents($filename), $matches)) 
		{
			$charset  = strtoupper(trim($matches[1]));
		}
	}
	
	return $charset;
}

// This function finds all charsets defined in all html & xml files in the given zip
function find_all_charsets($path, $filename)
{
	global $charsets;
	
	$charset = find_charset($path);

	if (strlen($charset) > 0 && !in_array($charset, $charsets))
	{
			array_push($charsets, $charset);
	}
}

// This function does:
// 1. Find all "charset=XXX" in html <meta> tag and replace the tag 
//    value to "UTF-8"
// 2. Convert the file from old charset "XXX" to new charset "UTF-8"
// 3. Only process html and xml files
function convert($path, $filename)
{
	global $charset_from, $charset_to;
	global $filetypes;
	global $charsets;
	
	// 1. html & xml files:
	//    if charset is defined, convert from defined charset,
	//    otherwise, convert from $charset_from
	// 2. Other files with defined file type
	//    convert from $charset_from
	if ((in_array('html', $filetypes) && preg_match("/\.html$/i", $path)) ||
	    (in_array('xml', $filetypes) && preg_match("/\.xml$/i", $path))) 
	{ 
		$charset_in = find_charset($path);
	
		$content = file_get_contents($path);
		// convert file
		if (strlen($charset_in) > 0)
		{
			// replace old charset in <meta> tag to new charset
			$content = str_ireplace($charset_in,$charset_to,$content);
	
			// convert file from old charset to new charset
			$content = iconv($charset_in, $charset_to. '//IGNORE', $content);
		}
		else
		{
			$content = iconv($charset_from, $charset_to. '//IGNORE', $content);
		} // end inner if
	
		$fp = fopen($path,'w');
		fwrite($fp,$content);
		fclose($fp);
	}
	elseif (in_array(strtolower(substr($path, (strripos($path, '.')+1))),$filetypes))
	{
		$content = file_get_contents($path);
		$content = iconv($charset_from, $charset_to. '//IGNORE', $content);

		$fp = fopen($path,'w');
		fwrite($fp,$content);
		fclose($fp);
	}
}

// This function display a drop-down box with all options as values in $charsets_array
function display_options($charsets_array)
{
?>
	<select name="charfrom" id="charfrom" class="input">
<?php
	foreach($charsets_array as $charset)
	{
?>
    <option><?php echo $charset; ?></option>
<?php
	}
?>
  </select>
<?php
}

// This function deletes $dir recrusively without deleting given dir itself.
function clear_dir($dir) {
	include_once(AT_INCLUDE_PATH . '/lib/filemanager.inc.php');
	
	if(!$opendir = @opendir($dir)) {
		return false;
	}
	
	while(($readdir=readdir($opendir)) !== false) {
		if (($readdir !== '..') && ($readdir !== '.')) {
			$readdir = trim($readdir);

			clearstatcache(); /* especially needed for Windows machines: */

			if (is_file($dir.'/'.$readdir)) {
				if(!@unlink($dir.'/'.$readdir)) {
					return false;
				}
			} else if (is_dir($dir.'/'.$readdir)) {
				/* calls lib function to clear subdirectories recrusively */
				if(!clr_dir($dir.'/'.$readdir)) {
					return false;
				}
			}
		}
	} /* end while */

	@closedir($opendir);
	
	return true;
}

// Main Convert process
$module_content_folder = AT_CONTENT_DIR . "utf8conv";
	
include_once(AT_INCLUDE_PATH . '/classes/pclzip.lib.php');
	
if (isset($_POST['Convert']))
{
	// clean up module content folder
	clear_dir($module_content_folder);
	
	// 1. unzip uploaded file to module's content directory
	$archive = new PclZip($_FILES['userfile']['tmp_name']);

	if ($archive->extract(PCLZIP_OPT_PATH, $module_content_folder) == 0)
	{
    clear_dir($module_content_folder);
    die("Cannot unzip file " . $_FILES['userfile']['tmp_name'] . "<br>Error : ".$archive->errorInfo(true));
  }
}

if (isset($_POST['Convert']) || $_POST['Go'])
{
  // 2. Read content folder recursively to convert.
  include_once("readDir.php");
  
	$dir = new readDir();
	// set the directory to read
	if (!$dir->setPath( $module_content_folder )) 
	{ 
		clear_dir($module_content_folder);
		die($dir->error());
	} 

	// set recursive reading of sub folders
	$dir->readRecursive(true); 
	
	// set a function to call when a new file is read
	if (!$dir->setEvent( 'readDir_file', 'find_all_charsets' ))
	{ 
		clear_dir($module_content_folder);
		die($dir->error());
	} 
	
	// read the dir
	if ( !$dir->read() ) 
	{ 
		clear_dir($module_content_folder);
		die($dir->error());
	}
	
	// If only one character set is found in all html & xml files
	if ((count($charsets) == 1 && $_POST['Convert']) ||
	    (count($charsets) != 1 && $_POST['Go']))
	{
		if (count($charsets) == 1 && $_POST['Convert'])
			$charset_from = $charsets[0];
		elseif (count($charsets) != 1 && $_POST['Go'])
			$charset_from = $_POST['charfrom'];

		// Real conversion
		$dir = new readDir();
		$dir->setPath( $module_content_folder );
		$dir->readRecursive(true); 
		$dir->setEvent( 'readDir_file', 'convert' );
		$dir->read();
	
	  // 3. ZIP converted files
	  if ($_POST['Convert'])  $orig_filename=$_FILES['userfile']['name'];
	  elseif ($_POST['Go'])   $orig_filename=$_POST['filename'];
	  
	  $zip_filename = AT_CONTENT_DIR . "/" . str_replace('.zip','_'.$charset_to . '.zip', $orig_filename);
	
	  $archive = new PclZip($zip_filename);
	  
	  if ($archive->add($module_content_folder, PCLZIP_OPT_REMOVE_PATH, $module_content_folder) == 0) {
	    clear_dir($module_content_folder);
	    die("Cannot zip converted files. <br>Error : ".$archive->errorInfo(true));
	  }
	
		// 4. force zipped converted file to download
		ob_end_clean();
	
		header('Content-Type: application/x-zip');
		header('Content-transfer-encoding: binary'); 
		header('Content-Disposition: attachment; filename="'.htmlspecialchars(basename($zip_filename)) . '"');
		header('Expires: 0');
		header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
		header('Pragma: public');
	
		readfile($zip_filename);
	
		// 5. clear all temp files
		unlink($zip_filename);
		clear_dir($module_content_folder);
	}
}
// End of main convert process

include_once (AT_INCLUDE_PATH.'header.inc.php');

// Check ICONV library is installed and enabled
if (!extension_loaded('iconv') || !function_exists('iconv'))
{
	die ('<font color="red">Warning: This utility is not available as PHP ICONV module is not installed or not enabled.</font>');
}
?>

<HTML>

<SCRIPT LANGUAGE="JavaScript">
<!--

String.prototype.trim = function() {
	return this.replace(/^\s+|\s+$/g,"");
}

// This function validates if and only if a zip file is given
function validate_filename() {
  // check file type
  var file = document.frm_upload.userfile.value;
  if (!file || file.trim()=='') {
    alert('Please give a zip file!');
    return false;
  }
  
  if(file.slice(file.lastIndexOf(".")).toLowerCase() != '.zip') {
    alert('Please upload ZIP file only!');
    return false;
  }
}

//  End -->
//-->
</script>

<BODY>
  <FORM NAME="frm_upload" ENCTYPE="multipart/form-data" METHOD=POST ACTION="<?php echo $_SERVER['PHP_SELF']; ?>" >

  <TABLE>	
		<TR>
      <TD colspan="1">Upload a zip file to convert the character set to UTF-8:</TD>
		</TR>

		<TR>
			<INPUT TYPE="hidden" name="MAX_FILE_SIZE" VALUE="52428800">
			<TD><INPUT TYPE="file" NAME="userfile" SIZE=50></td>
			<TD><INPUT TYPE="submit" name="Convert" value="Convert" onClick="javascript: return validate_filename(); " class="submit" /></TD>
			<TD><INPUT TYPE="hidden" name="filename"></TD>
		</TR>
<?php
if ($_POST["Convert"] && count($charsets) != 1)
{
?>
		<TR>
<?php
	if (count($charsets) > 1)
	{
?>
      <TD colspan="2"><font color="red">Multiple character sets are found, please select one to convert from:</font></TD>
		</TR>
		<TR>
			<TD>
<?php
		display_options($charsets);
	}
	else
	{
?>
      <TD colspan="1"><font color="red">No character set is found, please select one to convert from:</font></TD>
		</TR>
		<TR>
			<TD>
<?php
		display_options($default_charsets);
	}
?>
			<INPUT TYPE="submit" name="Go" value="Go" class="submit" /></TD>
		</TR>

<?php
}
?>
  </TABLE>
	</FORM>
  <hr/>  
</BODY>	
</HTML>

<SCRIPT LANGUAGE="JavaScript">
<!--

<?php
// store the upload zip file name in a hidden field for the future use for charsets selection
if ($_POST['Convert'])
{
?>
document.frm_upload.filename.value = '<?php echo $_FILES['userfile']['name']; ?>';
<?php
}
?>

//  End -->
//-->
</script>

<?php include_once (AT_INCLUDE_PATH.'footer.inc.php'); ?>
