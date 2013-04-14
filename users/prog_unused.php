<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2010                                      */
/* Inclusive Design Institute                                   */
/* http://atutor.ca												*/
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.				*/
/****************************************************************/
// $Id$

$_user_location	= 'users';
define('AT_INCLUDE_PATH', '../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html lang="en">
<head>
	<title><?php echo _AT('upload_progress'); ?></title>
	<?php if ($_GET['frame']) { ?>
		<meta http-equiv="refresh" content="3;URL=prog.php?frame=1" /> 
	<?php } ?>
	<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />
    <script src="<?php echo $_base_path; ?>jscripts/infusion/InfusionAll.js" type="text/javascript"></script>
    <script language="javascript" type="text/javascript">
    //<!--
    jQuery.noConflict();
    //-->
    </script>
    <script src="<?php echo $_base_path; ?>jscripts/ATutor.js" type="text/javascript"></script>   
    <link rel="stylesheet" href="../stylesheet.css" type="text/css" />
    <style id="pref_style" type="text/css"></style> 
</head>
<body <?php
	if ($_SESSION['done']) {
		echo 'onLoad="parent.window.close();"';
	}

?>>

<?php if (!$_GET['frame']) { 
	
	$_SESSION['done'] = 0;
	session_write_close();
?>
&nbsp;<a href="javascript:window.close();"><?php echo _AT('close'); ?></a>
<h3><?php echo _AT('upload_progress'); ?></h3>
<p><small><?php echo _AT('window_auto_close'); ?></small></p>

<br /><br />
<table border="0" align="center">
<tr>
	<td><img src="../images/transfer.gif" height="20" width="90" alt="file upload in progress..."></td>
	<td valign="middle"><iframe src="prog.php?frame=1" width="100" height="25" frameborder="0" scrolling=no marginwidth="0" marginheight="1">
</iframe>
<?php } else { 
	if (!$_GET['t']) {
		$newest_file_name = '';
		$newest_file_time = 0;
		// get the name of the temp file.
		if ($dir = @opendir('/tmp')) {
			while (($file = readdir($dir)) !== false) {
				if ((strlen($file) == 9) && (substr($file, 0, 3) == 'php')) {
					$filedata = stat('/tmp/'.$file);
					if ($filedata['mtime'] > $newest_file_time) {
						$newest_file_time = $filedata['mtime'];
						$newest_file_name = $file;
						$size = $filedata['size'] / 1024;
					}
				}
			}
			closedir($dir);
		}
	} else {
		$filedata = stat('/tmp/'.$_GET['t']);
		$size = $filedata['size'] / 1024;
	}

	echo '<small>';
	// not sure where these are displayed in the progress popup
	if ($size == '') {
		echo '<strong>Unknown</strong> '._AT('kb');
	} else {
		echo $size.' '._AT('kb');
	}
	echo '</small>';
} ?></td>
</tr>
</table>
<script type="text/javascript">
//<!--
    <?php require_once(AT_INCLUDE_PATH.'../jscripts/ATutor_js.php'); ?>
//-->
</script>
</body>
</html>
