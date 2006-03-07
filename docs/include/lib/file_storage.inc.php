<?php

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

function get_file_extension($file_name) {
	$ext = pathinfo($file_name);
	return $ext['extension'];
}

function get_file_type_icon($file_name) {
	global $mime;
	$ext = get_file_extension($file_name);

	if (isset($mime[$ext]) && $mime[$ext][1]) {
		return $mime[$ext][1];
	}
	return 'generic';
}

function delete_folder($folder_id) {
	if (!$folder_id) { return; }

	global $db;

	$sql = "SELECT folder_id FROM ".TABLE_PREFIX."folders WHERE parent_folder_id=$folder_id";
	$result = mysql_query($sql, $db);
	while ($row = mysql_fetch_assoc($result)) {
		delete_folder($row['folder_id']);
	}

	$sql = "DELETE FROM ".TABLE_PREFIX."folders WHERE folder_id=$folder_id";
	mysql_query($sql, $db);

	// delete this file's folders (we only select the latest versions because
	// the delete_file() function takes care of the revisions for us
	$sql = "SELECT file_id FROM ".TABLE_PREFIX."files WHERE folder_id=$folder_id";
	$result = mysql_query($sql, $db);
	while ($row = mysql_fetch_assoc($result)) {
		delete_file($row['file_id']);
	}
}

function download_folder($folder_id, &$zipfile, $path = '') {
	global $db;
	$sql = "SELECT title FROM ".TABLE_PREFIX."folders WHERE folder_id=$folder_id";
	$result = mysql_query($sql, $db);
	$parent_row = mysql_fetch_assoc($result);

	$zipfile->create_dir($path . $parent_row['title']);

	$sql = "SELECT file_id, file_name, UNIX_TIMESTAMP(date) AS date FROM ".TABLE_PREFIX."files WHERE folder_id=$folder_id AND parent_file_id=0";
	$result = mysql_query($sql, $db);
	while ($row = mysql_fetch_assoc($result)) {
		$file_path = get_file_path($row['file_id']) . $row['file_id'];

		$zipfile->add_file(file_get_contents($file_path), $path . $parent_row['title'] .'/' . $row['file_name'], $row['date']);
	}

	$sql = "SELECT folder_id FROM ".TABLE_PREFIX."folders WHERE parent_folder_id=$folder_id";
	$result = mysql_query($sql, $db);
	while ($row = mysql_fetch_assoc($result)) {
		download_folder($row['folder_id'], $zipfile, $path . $parent_row['title'] . '/');
	}
}


function get_folder_path($folder_id, $workspace, $owner_id) {
	$folder_path = get_folder_path_recursive($folder_id, $workspace, $owner_id);

	return array_reverse($folder_path);
}

// private!
function get_folder_path_recursive($folder_id, $workspace, $owner_id) {
	global $db;

	if ($folder_id == 0) {
		return array();
	}

	$sql = "SELECT folder_id, title, parent_folder_id FROM ".TABLE_PREFIX."folders WHERE folder_id=$folder_id AND owner_type=$workspace AND owner_id=$owner_id";
	$result = mysql_query($sql, $db);
	$row = mysql_fetch_assoc($result);

	return array_merge(array($row), get_folder_path_recursive($row['parent_folder_id'], $workspace, $owner_id));
}

?>