<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2006 by Greg Gay & Joel Kronenberg        */
/* Adaptive Technology Resource Centre / University of Toronto  */
/* http://atutor.ca												*/
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.				*/
/****************************************************************/
// $Id$

define('AT_INCLUDE_PATH', '../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');

function print_folders($current_folder_id, $parent_folder_id, &$folders, $disable = FALSE) {
	if (!isset($folders[$parent_folder_id])) {
		return;
	}

	echo '<ul>';
	foreach ($folders[$parent_folder_id] as $folder_id => $folder_info) {
		echo '<li class="folders">';
		
		echo '<input type="radio" name="new_folder" value="'.$folder_id.'" id="f'.$folder_id.'"';
		if ($_GET['folders'] && in_array($folder_id, $_GET['folders'])) {
			$disable = TRUE;
		}
		if ($folder_id == $current_folder_id) {
			echo ' checked="checked"';
		}
		if ($disable) {
			echo ' disabled="disabled"';
		}
		echo '/><label for="f'.$folder_id.'">'.$folder_info['title'];
		if ($folder_id == $current_folder_id) {
			echo ' '._AT('current_location');
		}
		echo '</label>';
		
		print_folders($current_folder_id, $folder_id, $folders, $disable);
		if ($_GET['folders'] && in_array($folder_id, $_GET['folders'])) {
			$disable = FALSE;
		}
		echo '</li>';
	}
	echo '</ul>';
}

// retrieves the first file, then gets the other revisions.
// this will not be needed once authentication is added, which will authenticate the current file.
function get_revisions($file_id) {
	global $db;

	$sql = "SELECT * FROM ".TABLE_PREFIX."files WHERE file_id=$file_id";
	$result = mysql_query($sql, $db);
	$row = mysql_fetch_assoc($result);

	return array_merge(array_reverse(get_revisions_down_recursive($row['parent_file_id'])), array($row), get_revisions_recursive($file_id));
}

// private
function get_revisions_down_recursive($file_id) {
	global $db;

	if ($file_id == 0) {
		return array();
	}

	$sql = "SELECT * FROM ".TABLE_PREFIX."files WHERE file_id=$file_id";
	$result = mysql_query($sql, $db);
	$row = mysql_fetch_assoc($result);

	if (!$row) {
		return array();
	} else if (!$row['parent_file_id']) {
		return array($row);
	}

	return array_merge(array($row), get_revisions_down_recursive($row['parent_file_id']));
}

// private
function get_revisions_recursive($file_id) {
	global $db;

	if ($file_id == 0) {
		return array();
	}

	$sql = "SELECT * FROM ".TABLE_PREFIX."files WHERE parent_file_id=$file_id";
	$result = mysql_query($sql, $db);
	$row = mysql_fetch_assoc($result);

	if (!$row) {
		return array();
	}

	return array_merge(array($row), get_revisions_recursive($row['file_id']));
}

// returns the full path to the file (w/o the file name, just the path)
function get_file_path($file_id) {
	$end_part = substr($file_id, -WORKSPACE_PATH_DEPTH);
	$path = WORKSPACE_FILE_PATH;
	$dirs = max(-WORKSPACE_PATH_DEPTH, -strlen($file_id));
    for ($i = -1; $i >= $dirs; $i--) {
		$path .= substr($file_id, $i, 1) . DIRECTORY_SEPARATOR;
		if ($file_id < pow(10,WORKSPACE_PATH_DEPTH)) {
			if (!is_dir($path)) {
				@mkdir($path);
			}
		}
	}

	return $path;
}

function delete_file($file_id) {
	global $db;
	// should this function deal with authentication?
	$revisions = get_revisions($file_id);
	foreach ($revisions as $file) {
		$sql = "DELETE FROM ".TABLE_PREFIX."files WHERE file_id=$file[file_id]";
		mysql_query($sql, $db);

		$sql = "DELETE FROM ".TABLE_PREFIX."files_comments WHERE file_id=$file[file_id]";
		mysql_query($sql, $db);

		$path = get_file_path($file['file_id']);
		if (file_exists($path . $file['file_id'])) {
			@unlink($path . $file['file_id']);
		}
	}
}

if (isset($_POST['cancel'])) {
	$msg->addFeedback('CANCELLED');
	header('Location: index.php?folder='.abs($_POST['folder']));
	exit;
} else if (isset($_POST['submit'])) {
	$_POST['new_folder'] = abs($_POST['new_folder']);

	// authenticate new_folder with owner_type and owner_id //
	

	if ($_POST['folder'] == $_POST['new_folder']) {
		// src = dest
		$msg->addFeedback('CANCELLED');
		header('Location: index.php?folder='.$_POST['new_folder']);
		exit;
	}

	if (isset($_POST['files'])) {
		foreach ($_POST['files'] as $file) {
			$file = abs($file);
			// check if this file name already exists
			$sql = "SELECT file_name FROM ".TABLE_PREFIX."files WHERE file_id=$file";
			$result = mysql_query($sql, $db);
			$row = mysql_fetch_assoc($result);

			$sql = "SELECT file_id FROM ".TABLE_PREFIX."files WHERE folder_id={$_POST['new_folder']} AND file_id<>$file AND file_name='{$row['file_name']}' AND parent_file_id=0 ORDER BY file_id DESC LIMIT 1";
			$result = mysql_query($sql, $db);
			if ($row = mysql_fetch_assoc($result)) {
				delete_file($row['file_id']);
			}

			$sql = "UPDATE ".TABLE_PREFIX."files SET folder_id={$_POST['new_folder']} WHERE file_id=$file";
			mysql_query($sql, $db);
		}
		$msg->addFeedback('FILES_MOVED');
	}

	if (isset($_POST['folders'])) {
		foreach ($_POST['folders'] as $folder) {
			$file = abs($file);
			$sql = "UPDATE ".TABLE_PREFIX."folders SET parent_folder_id={$_POST['new_folder']} WHERE folder_id=$folder";
			mysql_query($sql, $db);
		}
		$msg->addFeedback('DIRS_MOVED');
	}
	header('Location: index.php?folder='.$_POST['new_folder']);
	exit;
}

require(AT_INCLUDE_PATH.'header.inc.php');

$folder_id = abs($_GET['folder']);
//debug($_GET);

if ($_SESSION['workspace'] == WORKSPACE_COURSE) {
	$owner_id = $_SESSION['course_id'];
} else if ($_SESSION['workspace'] == WORKSPACE_PERSONAL) {
	$owner_id = $_SESSION['member_id'];
} else if ($_SESSION['workspace'] == WORKSPACE_GROUP) {
	$owner_id = $group_id;
}

$folders = array();
$sql = "SELECT folder_id, parent_folder_id, title FROM ".TABLE_PREFIX."folders WHERE owner_type=$_SESSION[workspace] AND owner_id=$owner_id ORDER BY parent_folder_id, title";
$result = mysql_query($sql, $db);
while ($row = mysql_fetch_assoc($result)) {
	$folders[$row['parent_folder_id']][$row['folder_id']] = $row;
}

//debug($_GET);
?>

<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
<?php if ($_GET['files']): foreach ($_GET['files'] as $tmpfile): ?>
	<input type="hidden" name="files[]" value="<?php echo $tmpfile; ?>" />
<?php endforeach; endif; ?>

<?php if ($_GET['folders']): foreach ($_GET['folders'] as $tmpfolder): ?>
	<input type="hidden" name="folders[]" value="<?php echo $tmpfolder; ?>" />
<?php endforeach; endif; ?>

<input type="hidden" name="folder" value="<?php echo $folder_id; ?>" />
<div class="input-form">
	<div class="row">
		<p><?php echo _AT('select_directory'); ?></p>
	</div>

	<div class="row">
		<ul>
			<li class="folders"><input type="radio" name="new_folder" value="0" id="fhome" <?php
				if ($folder_id == 0) {
					echo ' checked="checked"';
				}
			?>/><label for="fhome"><?php echo _AT('my_files'); ?>
			<?php 
				if ($folder_id == $current_folder_id) {
					echo ' '._AT('current_location');
				}
			?>
			<?php print_folders($folder_id, 0, $folders); ?>
			</li>
		</ul>
	</div>

	<div class="row buttons">
		<input type="submit" name="submit" value="<?php echo _AT('move'); ?>" />
		<input type="submit" name="cancel" value="<?php echo _AT('cancel'); ?>" />
	</div>
</div>
</form>

<?php require(AT_INCLUDE_PATH.'footer.inc.php'); ?>