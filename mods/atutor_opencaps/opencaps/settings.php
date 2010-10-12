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

if (isset($_POST['submit-name'])) {
	$this_proj->editName($_POST['name']);
} if (isset($_POST['submit-caps'])) {
	$this_proj->importCaptions($_FILES['caption_file']);
}


require(INCLUDE_PATH.'header.inc.php'); 

?>

<script language="javascript" type="text/javascript" src="js/settings.js"></script>

<div id="content">

	<h3>Project Settings</h3>
	<p>Edit your project's settings here.</p>
	
	<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" id="form" enctype="multipart/form-data">

		<h4>Project Name</h4>
		<p><?php echo $this_proj->name; ?> 
		<?php if (!isset($_SESSION['rid'])) { ?> (<a href="#" onclick="javascript:hide_all();$('#edit-name').show();">Edit</a>)</p> <?php } ?>
		 
		<div id="edit-name">
			New project name: <input type="input" name="name" value="" /> <input type="submit" class='button' style='width:6em;' name="submit-name" value="Submit" />
		</div>
		
		<h4>Captions</h4> 
		<p><a href="#" onclick="javascript:hide_all();$('#edit-caps').show();">Import caption file</a></p>
		
		<div id="edit-caps">
			<p>Uploading a new caption file will delete all existing captions. Make sure to backup your project (<a href="export.php">Export</a> Json file) before doing this.</p>
			Caption File: <input type="file" name="caption_file" /> <input type="submit" name="submit-caps" class='button' style='width:6em;' value="Submit" />
		</div>
	
		<!-- h4>Media File</h4> 
		<p><?php if (substr($this_proj->media_loc,0, 7) == "http://") echo $this_proj->media_loc; else echo end(explode("/", $this_proj->media_loc)); ?> (<a href="#" onclick="javascript:hide_all();$('#edit-media').show();">Edit</a>)</p -->
		
		<div id="edit-media">
			<p>Coming soon.<br />
			Upload a new version of your media. The duration of the new media file must match that of the existing media.</p>
			<!--  input type="file" name="media_file" /> <input type="submit" name="submit-media" class='button' style='width:6em;' value="Submit" / -->			
		</div>
			
		<!-- h4>Permissions</h4> 
		
		<p>People who can edit this project: You (<a href="#" onclick="javascript:hide_all();$('#edit-perms').show();">Edit</a>) </p>
		
		<div id="edit-perms">
			<p>Coming soon.<br />
			Permissions will allow for collaborative editing by letting you give other users access to your project.</p>
			
			<select name="collab" size="4" multiple="multiple">
			</select>
			<input type="submit" name="submit-perms" class='button' style='width:6em;' value="Submit" />	
		</div -->
		
	
	</form>
</div>
<?php require(INCLUDE_PATH.'footer.inc.php'); ?>
