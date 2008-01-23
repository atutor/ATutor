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
// $Id: index_admin.php 2008-01-23 14:49:24Z cindy $

define('AT_INCLUDE_PATH', '../../include/');
require (AT_INCLUDE_PATH.'vitals.inc.php');
admin_authenticate(AT_ADMIN_PRIV_HELLO_WORLD);

$charset_to = "UTF-8";

// This function does:
// 1. Find all "charset=XXX" in html <meta> tag and replace the tag 
//    value to "UTF-8"
// 2. Convert the file from old charset "XXX" to new charset "UTF-8"
// 3. Only process html and xml files
function convert_charset($path, $filename)
{
	global $charset_to;
	
	if (preg_match("/\.html$|\.xml$/i", $path)) 
	{ 
		$content = file_get_contents($path);

		if (!content)
		{
			echo("Error: Unable to read content of ". $path ."<br>");
			return;
		}
		
		// Find old charset to convert file
		$pattern = '/<meta.*charset=(.*) /i';
		preg_match($pattern, $content, $matches);
		
		// remove quote signs in the match
		$charset_in = preg_replace('/(\'|\")/', '', $matches[1]);
		
		// replace old charset in <meta> tag to new charset
		$content = str_ireplace($charset_in,$charset_to,$content);

		// convert file from old charset to new charset
		$content = iconv($charset_in, $charset_to. '//IGNORE', $content);

		$fp = fopen($path,'w');
		fwrite($fp,$content);
		fclose($fp);
	}  
}

// This function deletes $dir recrusively without deleting given dir itself.
function clear_dir($dir) {
	require(AT_INCLUDE_PATH . '/lib/filemanager.inc.php');
	
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
if (isset($_POST['submit']))
{
	$module_content_folder = AT_CONTENT_DIR . "utf8conv";
	
	require(AT_INCLUDE_PATH . '/classes/pclzip.lib.php');
	
	// unzip uploaded file to module's content directory
	$archive = new PclZip($_FILES['userfile']['tmp_name']);

	if ($archive->extract(PCLZIP_OPT_PATH, $module_content_folder) == 0)
	{
    die("Error : ".$archive->errorInfo(true));
  }
  
  // Read content folder recursively to convert.
  require("readDir.php");
  
	$dir = new readDir(); // instantiate our class 
	
	// set the directory to read
	if (!$dir->setPath( $module_content_folder )) 
	{ 
		die($dir->error());
	} 
	
	// set recursive reading of sub folders
	$dir->readRecursive(true); 
	
	// set a function to call when a new file is read
	if (!$dir->setEvent( 'readDir_file', 'convert_charset' )) 
	{ 
		die($dir->error());
	} 
	
	// read the dir
	if ( !$dir->read() ) 
	{ 
		die($dir->error());
	}  

  // ZIP converted files
  $zip_filename = AT_CONTENT_DIR . "/" . str_replace('.zip','_'.$charset_to . '.zip', $_FILES['userfile']['name']);

  $archive = new PclZip($zip_filename);

  if ($archive->create($module_content_folder, PCLZIP_OPT_REMOVE_PATH, $module_content_folder) == 0) {
    die("Error : ".$archive->errorInfo(true));
  }

	// force zipped converted file to download
	ob_end_clean();

	header('Content-Type: application/x-zip');
	header('Content-transfer-encoding: binary'); 
	header('Content-Disposition: attachment; filename="'.htmlspecialchars(basename($zip_filename)) . '"');
	header('Expires: 0');
	header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
	header('Pragma: public');
	header('Content-Length: '.filesize($zip_filename));

	readfile($zip_filename);

	// clear all temp files
	unlink($zip_filename);
	clear_dir($module_content_folder);
}
// End of main convert process

require (AT_INCLUDE_PATH.'header.inc.php');

?>

<HTML>

<SCRIPT LANGUAGE="JavaScript">
<!--

String.prototype.trim = function() {
	return this.replace(/^\s+|\s+$/g,"");
}

// This function validates if and only if a zip file is given
function SubmitFile() {
  // check file type
  var file = document.frm_upload.userfile.value;
  if (!file || file.trim()=='') {
    alert('Please give a zip file!');
    return;
  }
  
  while (file.indexOf("\\") != -1)
      file = file.slice(file.indexOf("\\") + 1);
  
  var ext = file.slice(file.lastIndexOf(".")).toLowerCase();
  if(ext != '.zip') {
    alert('Please upload ZIP file only!');
    return false;
  }
}

//  End -->
//-->
</script>

<BODY>
  <FORM NAME="frm_upload" ENCTYPE="multipart/form-data" METHOD=POST ACTION="<?php echo $_SERVER['PHP_SELF']; ?>" onsubmit="return SubmitFile()">

  <TABLE>	
		<TR>
      <strong>Upload a zip file to convert the character set to UTF-8</strong><br />
		</TR>

		<TR>
			<INPUT TYPE="hidden" name="MAX_FILE_SIZE" VALUE="52428800">
			<TD><INPUT TYPE="file" NAME="userfile" SIZE=50></td>
			<TD><INPUT TYPE="submit" name="submit" value="Convert"></TD>

		</TR>
  </TABLE>	
	</FORM>
  <hr/>  
</BODY>	
</HTML>


<?php require (AT_INCLUDE_PATH.'footer.inc.php'); ?>
