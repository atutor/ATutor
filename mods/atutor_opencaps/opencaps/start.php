<?php 
/*
 * OpenCaps
 * http://opencaps.atrc.utoronto.ca
 * 
 * Copyright 2009 Heidi Hazelton
 * Adaptive Technology Resource Centre, University of Toronto
 * 
 * Licensed under the Educational Community License (ECL), Version 2.0. 
 * You may not use this file except in compliance with this License.
 * http://www.opensource.org/licenses/ecl2.php
 * 
 */

define('INCLUDE_PATH', 'include/');
require(INCLUDE_PATH.'vitals.inc.php');

unset($_SESSION['pid']);

if (DISABLE_LOCAL && isset($systems[0]['url'])) {
	header("Location:start_remote.php?r=0");
	exit;
} else if (DISABLE_LOCAL) {
	$_SESSION['errors'][] = "Your administrator has disabled local captioning and no remote systems have been configured.";
	include(INCLUDE_PATH.'basic_header.inc.php'); 
	include(INCLUDE_PATH.'footer.inc.php'); 
	exit;
}

if (isset($_POST['submit_login'])) {	
	$this_user->login($_POST['login'], $_POST['password']);
} 

//valid post
if (isset($_GET['submit']) && $_GET['submit'] && empty($_POST)) {
	$_SESSION['errors'][] = "Can't create project. If you uploaded a file, it may be too large.";
	
//file size	
} else if (isset($_POST['submit_new']) && ($_FILES["media_file"]["error"] == UPLOAD_ERR_INI_SIZE || $_FILES["media_file"]["error"] == UPLOAD_ERR_FORM_SIZE)) { 
    $_SESSION['errors'][] = "Can't create project. If you uploaded a file, it may be too large.";
    
} else if (isset($_POST['submit_new'])) {
	$this_proj = new project();
				
	//valid URL
	if (isset($_POST['media_url']) && !empty($_POST['media_url']) && $_POST['media_url']!="http://") {
		$this_ext = explode(".", $_POST['media_url']);
		$this_ext = end($this_ext);
		
		if (!file_get_contents($_POST['media_url']) || !in_array($this_ext, $supported_ext) ) {
			$_SESSION['errors'][] = "Invalid URL. Make sure the URL is correct and that the media file is a supported format.";			
		} else {
			$this_proj->createNew($addslashes($_POST['projname']), $_POST['media_url'], $_FILES['caption_file']);
		}
	}
	
	//valid file upload
	else if (!empty($_FILES['media_file'])) {
		$this_ext = explode(".", $_FILES['media_file']['name']);
		$this_ext = end($this_ext);		
	
		if (!in_array($this_ext, $supported_ext) ) {
			$_SESSION['errors'][] = "Incorrect upload format.";			
		} else {						
			$pid = $this_proj->createNew($addslashes($_POST['projname']), $_FILES['media_file'], $_FILES['caption_file']);
		}
	} 
	
	if (empty($_SESSION['errors'])) 
		header("Location:editor.php");
}

if (intval(ini_get('upload_max_filesize')) < MAX_FILE_SIZE/1048576)
	$max = intval(ini_get('upload_max_filesize'));
else
	$max = MAX_FILE_SIZE/1048576;

include(INCLUDE_PATH.'basic_header.inc.php'); 

unset($_SESSION['rid']);

if ($_SESSION['mid'] == "99999")
	unset($_SESSION['mid']);
	
?>

<script language="JavaScript" src="js/start.js" type="text/javascript"></script>

<h1 style="margin-top:10px;"><img src="images/logo.png" alt="OpenCaps - a free, online caption editor" title="OpenCaps - a free, online caption editor" style="margin-top:7px;" /></h1>
<p>Start Captioning!</p>

<div id="start-tabs"></div>
<div id="start-container">	
	<?php if (!$_SESSION['mid'] || !isset($_SESSION['valid_user']) || !$_SESSION['valid_user']) { ?>
	<h2>Login</h2>

	<p>To start a new captioning project or to return to an ongoing project, please login below. If you are new here, quickly <a href="register.php">register</a> with us!</p>
	
	<form action="start.php" method="post" id="form" >	
		<dl class="col-list" style="width:33%; margin-left:auto; margin-right:auto;">
			<dt><label for="login">Login:</label></dt> 
				<dd><input name="login" type="text" id="login" value="" /></dd>
			<dt><label for="pswd">Password:</label></dt> 
				<dd><input name="password" type="password" id="pswd" value="" /></dd>
		</dl>
		<div style="text-align:right">
			<input type="submit" name="submit_login" value="Submit" class="button" style="width:5em; margin-right:10px;" />
		</div>
	</form>
	<?php } else {?>
	<div>
		<h2 style="font-weight:bold">New Project</h2>
		<p>Begin adding captions to a new video.</p>
		
		<img src="images/asterisk_yellow.png" alt="asterisk" /> <a href="#" onclick="javascript:startNew();" style="margin-top:30px;">Start New Project</a>
		<form action="start.php?submit=1" method="post" id="form_new" enctype="multipart/form-data" onsubmit="javascript: return validateNewForm();">
			<input type="hidden" name="MAX_FILE_SIZE" value="<?php echo MAX_FILE_SIZE; ?>" />
		
			<div id="start-entry">
			
			<em>Project Name</em><br /> <input type='text' name='projname' style="width:200px;" value="<?php echo $this_proj->name; ?>" /><br /><br />
	
			<em>Video File</em><br />
			URL: <input type="text" name="media_url" value="<?php echo isset($_POST['media_url']) ? $_POST['media_url'] : 'http://'; ?>" style="width:200px;" /><br />
			<strong>or</strong><br />
			Upload: <input type='file' name='media_file' style="width:250px;" /><br /><br />  
			<span style="font-size:smaller;">Max upload size: <?php echo $max; ?>Mb.<br /> 
			Supported formats: <?php echo implode(', ', $supported_ext); ?>.</span><br /><br />
			
			<em>Caption file (optional)</em><br />
			<input type="file" name="caption_file" style="width:200px;" /><br />
			<span style="font-size:smaller;">Supported formats: QTtext, DFXP, SubRip, OpenCaps-JSON.</span>
	
			<div style='text-align:right;'><input type='submit' class='button' style='width:6em;margin-top:5px;' name='submit_new' value='Submit' /></div>		
			</div>		
		</form>
		<br style="clear:both" />
	</div>
	<div>
		<h2 style="font-weight:bold">Open Existing Project</h2>
		<p>Continue working on a project.</p>
		<img src="images/asterisk_yellow.png" alt="asterisk" />  <a href="#" onclick="javascript:startOpen();" style="margin-top:30px;">Open Project</a>
	
		<form action="javascript:processOpen();" method="post" id="form_open" enctype="multipart/form-data" onsubmit="javascript: return validateOpenForm();">
			<div id="open-entry">
				<div id="projects"></div>					
			</div>
		</form>
	</div>
	<?php } ?>
	<br style="clear:both" />
</div>

</body>
</html>
