<?php
/************************************************************************/
/* ATutor																*/
/************************************************************************/
/* Copyright (c) 2002-2006 by Greg Gay, Joel Kronenberg & Heidi Hazelton*/
/* Adaptive Technology Resource Centre / University of Toronto			*/
/* http://atutor.ca														*/
/*																		*/
/* This program is free software. You can redistribute it and/or		*/
/* modify it under the terms of the GNU General Public License			*/
/* as published by the Free Software Foundation.						*/
/************************************************************************/
// $Id$

if (!defined('AT_INCLUDE_PATH')) { exit; }

define('WORKSPACE_AUTH_NONE',  0);
define('WORKSPACE_AUTH_READ',  1);
define('WORKSPACE_AUTH_WRITE', 2); 
define('WORKSPACE_AUTH_RW',    3); // to save time

/**
 * given an owner_type and owner_id
 * returns false if user cannot read or write to this workspace
 * returns WORKSPACE_AUTH_READ if the user can read
 * returns WORKSPACE_AUTH_WRITE if the user can write
 */
function fs_authenticate($owner_type, $owner_id) {
	if (($owner_type == WORKSPACE_PERSONAL) && $_SESSION['member_id'] && ($owner_id == $_SESSION['member_id'])) {

		return WORKSPACE_AUTH_RW;

	} else if ($owner_type == WORKSPACE_ASSIGNMENT && authenticate(AT_PRIV_ASSIGNMENTS, AT_PRIV_RETURN)) {
		// instructors have read only access to assignments

		return WORKSPACE_AUTH_READ;
	
	} else if ($owner_type == WORKSPACE_GROUP) {
		if (isset($_SESSION['groups'][$owner_id])) {
			global $db;
			$sql = "SELECT * FROM ".TABLE_PREFIX."file_storage_groups WHERE group_id=$owner_id";
			$result = mysql_query($sql, $db);
			if (mysql_fetch_assoc($result)) {
				return WORKSPACE_AUTH_RW;
			}
		}

	} else if ($owner_type == WORKSPACE_COURSE) {
		if (($owner_id == $_SESSION['course_id']) && authenticate(AT_PRIV_FILE_STORAGE, AT_PRIV_RETURN)) {
			return WORKSPACE_AUTH_RW;
		} else if ($owner_id == $_SESSION['course_id']) {
			return WORKSPACE_AUTH_READ;
		}
	}

	return WORKSPACE_AUTH_NONE;
}

/**
 * returns the localised name of the specified workspace
 */
function fs_get_workspace($owner_type, $owner_id) {
	if ($owner_type == WORKSPACE_PERSONAL) {
		return _AT('my_files');

	} else if ($owner_type == WORKSPACE_COURSE) {
		return _AT('course_files');

	} else if ($owner_type == WORKSPACE_GROUP) {
		global $db;
		$sql = "SELECT title FROM ".TABLE_PREFIX."groups WHERE group_id=$owner_id";
		$result = mysql_query($sql, $db);
		$row    = mysql_fetch_assoc($result);
		return $row['title'];

	} else if ($owner_type == WORKSPACE_ASSIGNMENT) {
		$row    = fs_get_assignment($owner_id);
		return ($row ? $row['title'] : false);
	}
}

/**
 * returns the assignment row specified by $assignment_id
 * false if not found.
 */
function fs_get_assignment($assignment_id) {
	global $db;
	$sql = "SELECT assignment_id, title, assign_to, date_due, date_cutoff, UNIX_TIMESTAMP(date_cutoff) AS u_date_cutoff, multi_submit FROM ".TABLE_PREFIX."assignments WHERE assignment_id=$assignment_id AND course_id=$_SESSION[course_id]";
	$result = mysql_query($sql, $db);
	$row    = mysql_fetch_assoc($result);
	return $row;
}

/**
 * retrieve folder(s) specified by $folder_id
 * $folder_id the ID of the single folder, or array of IDs
 * if $folder_id is an array then returns an array of folder rows
 * if $folder_id is an int then returns the single row array
 *
 * This function does not authenticate the $folder_id for the assignment.
 *
 * Note: This function checks if the $owner_type is an Assignment.
 *
 */
function fs_get_folder_by_id($folder_id, $owner_type, $owner_id) {
	global $db;

	$rows = array();

	if ($owner_type == WORKSPACE_ASSIGNMENT) {
		// get the folder row from the assignments table

		$sql = "SELECT assign_to FROM ".TABLE_PREFIX."assignments WHERE assignment_id=$owner_id AND course_id=$_SESSION[course_id]";
		$result = mysql_query($sql, $db);
		$row  = mysql_fetch_assoc($result);
		if ($row['assign_to']) {
			$sql = "SELECT title FROM ".TABLE_PREFIX."groups WHERE group_id=$folder_id";
			$result = mysql_query($sql, $db);
			$row = mysql_fetch_assoc($result);

			$rows = array('title' => $row['title'], 'folder_id' => $folder_id);
		} else {
			$rows = array('title' => get_login($folder_id), 'folder_id' => $folder_id);
		}
	} else {
		if (is_array($folder_id)) {
			$folder_id_list = implode(',', $folder_id);

			$sql = "SELECT folder_id, title, parent_folder_id FROM ".TABLE_PREFIX."folders WHERE folder_id IN ($folder_id_list) AND owner_type=$owner_type AND owner_id=$owner_id ORDER BY title";
			$result = mysql_query($sql, $db);
			while ($row = mysql_fetch_assoc($result)) {
				$rows[] = $row;
			}

		} else {
			$sql = "SELECT folder_id, title, parent_folder_id FROM ".TABLE_PREFIX."folders WHERE folder_id=$folder_id AND owner_type=$owner_type AND owner_id=$owner_id";
			$result = mysql_query($sql, $db);
			$rows = mysql_fetch_assoc($result);
		}
	}
	return $rows;
}

/**
 * retrieve folder(s) specified by $parent_folder_id
 *
 * Note: This function checks if the $owner_type is an Assignment.
 *
 */
function fs_get_folder_by_pid($parent_folder_id, $owner_type, $owner_id) {
	global $db;

	$rows = array();
	if ($owner_type == WORKSPACE_ASSIGNMENT) {
		// get the folder row from the assignments table
		// does not currently support sub-folders for assignments
		if ($parent_folder_id == 0) {
			$sql = "SELECT assign_to FROM ".TABLE_PREFIX."assignments WHERE assignment_id=$owner_id AND course_id=$_SESSION[course_id]";
			$result = mysql_query($sql, $db);
			$row  = mysql_fetch_assoc($result);
			if ($row['assign_to']) {
				$sql = "SELECT G.group_id AS folder_id, G.title FROM ".TABLE_PREFIX."groups G INNER JOIN ".TABLE_PREFIX."file_storage_groups FS USING (group_id) WHERE G.type_id=$row[assign_to] ORDER BY G.title";
			} else {
				global $system_courses;

				$sql = "SELECT E.member_id AS folder_id, M.login AS title FROM ".TABLE_PREFIX."course_enrollment E INNER JOIN ".TABLE_PREFIX."members M USING (member_id) WHERE E.course_id=$_SESSION[course_id] AND E.approved='y' AND E.privileges=0 AND E.member_id<>{$system_courses[$_SESSION[course_id]][member_id]} ORDER BY M.login";
			}
			$result = mysql_query($sql, $db);
			while ($row = mysql_fetch_assoc($result)) {
				$rows[] = $row;
			}
		}
	} else {
		$sql = "SELECT folder_id, title FROM ".TABLE_PREFIX."folders WHERE parent_folder_id=$parent_folder_id AND owner_type=$owner_type AND owner_id=$owner_id ORDER BY title";
		$result = mysql_query($sql, $db);
		while ($row = mysql_fetch_assoc($result)) {
			$rows[] = $row;	
		}
	}
	return $rows;
}

/**
 * outputs the folders as a <ul> list.
 *
 * $current_folder_id the current folder id, used for pre-selecting the radio button
 * $parent_folder_id the folder id to display children of
 * $folders the array of folders returned from get_folders()
 * $disable whether or not the radio button is available
 */
function fs_print_folders($current_folder_id, $parent_folder_id, &$folders, $disable = FALSE) {
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
		
		fs_print_folders($current_folder_id, $folder_id, $folders, $disable);
		if ($_GET['folders'] && in_array($folder_id, $_GET['folders'])) {
			$disable = FALSE;
		}
		echo '</li>';
	}
	echo '</ul>';
}

/**
 * returns an array of all the revisions for the given file_id
 *
 * $file_id ID of a file in a revision sequence. can be any revision, does not have to be the latest.
 * This function is recursive and uses fs_get_revisions_down_recursive() and fs_get_revisions_recurisve() below.
 */
function fs_get_revisions($file_id, $owner_type, $owner_id) {
	global $db;

	$sql = "SELECT * FROM ".TABLE_PREFIX."files WHERE file_id=$file_id AND owner_type=$owner_type AND owner_id=$owner_id";
	$result = mysql_query($sql, $db);
	if ($row = mysql_fetch_assoc($result)) {
		return array_merge(array_reverse(fs_get_revisions_down_recursive($row['parent_file_id'])), array($row), fs_get_revisions_recursive($file_id));
	}
	return array();
}

/**
 * recursively retrieves all the revisions of the file.
 * recurses DOWN the revisions path.
 * PRIVATE! use fs_get_revisions() above.
 */
function fs_get_revisions_down_recursive($file_id) {
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

	return array_merge(array($row), fs_get_revisions_down_recursive($row['parent_file_id']));
}

/**
 * recursively retrieves all the revisions of the file.
 * recurses UP the revisions path.
 * PRIVATE! use fs_get_revisions() above.
 */
function fs_get_revisions_recursive($file_id) {
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

	return array_merge(array($row), fs_get_revisions_recursive($row['file_id']));
}

/**
 * returns the full path based on $file_id with trailing slash.
 *
 * Ex. if file_id is 2345 and WORKSPACE_PATH_DEPTH is set to 3 then
 * the path returned will be WORKSPACE_FILE_PATH.'5/4/3/'
 *
 * If the path does not exist within the WORKSPACE_FILE_PATH then attempts
 * to create it.
 */
function fs_get_file_path($file_id) {
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

/**
 * delete a given file, its revisions, and comments.
 *
 * $file_id the ID of the file to delete. can be any ID within a revision sequence.
 */
function fs_delete_file($file_id, $owner_type, $owner_id) {
	global $db;
	$revisions = fs_get_revisions($file_id, $owner_type, $owner_id);
	foreach ($revisions as $file) {
		$sql = "DELETE FROM ".TABLE_PREFIX."files WHERE file_id=$file[file_id] AND owner_type=$owner_type AND owner_id=$owner_id";
		mysql_query($sql, $db);

		if (mysql_affected_rows($db) == 1) {
			$sql = "DELETE FROM ".TABLE_PREFIX."files_comments WHERE file_id=$file[file_id]";
			mysql_query($sql, $db);

			$path = fs_get_file_path($file['file_id']);
			if (file_exists($path . $file['file_id'])) {
				@unlink($path . $file['file_id']);
			}
		}
	}
}

/**
 * returns only the extension part of the specified file name
 *
 * $file_name the full name of the file.
 */
function fs_get_file_extension($file_name) {
	$ext = pathinfo($file_name);
	return $ext['extension'];
}

/**
 * returns the image name (w/o the ".gif" ending) of the icon to use
 * for the given file name.
 * if no icon is specified (by mime.inc.php) then returns "generic"
 */
function fs_get_file_type_icon($file_name) {
	global $mime;
	if (!isset($mime)) {
		require(AT_INCLUDE_PATH.'lib/mime.inc.php');
	}
	$ext = fs_get_file_extension($file_name);

	if (isset($mime[$ext]) && $mime[$ext][1]) {
		return $mime[$ext][1];
	}
	return 'generic';
}

/**
 * deletes the folder, its sub-folders and associated files.
 *
 * $folder_id the ID of the folder to delete, recursively, with content.
 */
function fs_delete_folder($folder_id, $owner_type, $owner_id) {
	if (!$folder_id) { return; }

	global $db;

	$rows = fs_get_folder_by_pid($folder_id, $owner_type, $owner_id);
	foreach ($rows as $row) {
		fs_delete_folder($row['folder_id'], $owner_type, $owner_id);
	}

	$sql = "DELETE FROM ".TABLE_PREFIX."folders WHERE folder_id=$folder_id AND owner_type=$owner_type AND owner_id=$owner_id";
	mysql_query($sql, $db);

	// delete this file's folders (we only select the latest versions because
	// the delete_file() function takes care of the revisions for us
	$sql = "SELECT file_id FROM ".TABLE_PREFIX."files WHERE folder_id=$folder_id AND parent_file_id=0 AND owner_type=$owner_type AND owner_id=$owner_id";
	$result = mysql_query($sql, $db);
	while ($row = mysql_fetch_assoc($result)) {
		fs_delete_file($row['file_id'], $owner_type, $owner_id);
	}
}

/**
 * archives a folder into a specified zip handler.
 *
 * $folder_id the ID of the folder to archive recursively, with content.
 * $zipfile reference to the zipFile object.
 * $path the absolute path to the current folder.
 */
function fs_download_folder($folder_id, &$zipfile, $owner_type, $owner_id, $path = '') {
	global $db;

	$parent_row = fs_get_folder_by_id($folder_id, $owner_type, $owner_id);
	//$sql = "SELECT title FROM ".TABLE_PREFIX."folders WHERE folder_id=$folder_id AND owner_type=$owner_type AND owner_id=$owner_id";
	//$result = mysql_query($sql, $db);
	if ($parent_row) {
		$zipfile->create_dir($path . $parent_row['title']);
	}

	$sql = "SELECT file_id, file_name, UNIX_TIMESTAMP(date) AS date FROM ".TABLE_PREFIX."files WHERE folder_id=$folder_id AND parent_file_id=0 AND owner_type=$owner_type AND owner_id=$owner_id";
	$result = mysql_query($sql, $db);
	while ($row = mysql_fetch_assoc($result)) {
		$file_path = fs_get_file_path($row['file_id']) . $row['file_id'];

		$zipfile->add_file(file_get_contents($file_path), $path . $parent_row['title'] .'/' . $row['file_name'], $row['date']);
	}

	$rows = fs_get_folder_by_pid($folder_id, $owner_type, $owner_id);
	foreach ($rows as $row) {
		fs_download_folder($row['folder_id'], $zipfile, $owner_type, $owner_id, $path . $parent_row['title'] . '/');
	}
}

/**
 * returns the full path to the current folder
 *
 * $folder_id the current folder
 * $workspace the owner_type of this folder
 * $owner_id the ID of the owner.
 */
function fs_get_folder_path($folder_id, $owner_type, $owner_id) {
	$folder_path = fs_get_folder_path_recursive($folder_id, $owner_type, $owner_id);

	return array_reverse($folder_path);
}

/**
 * recursively return the path to the current folder
 * PRIVATE! do not call directly, use get_folder_path() above.
 */
function fs_get_folder_path_recursive($folder_id, $owner_type, $owner_id) {
	global $db;

	if ($folder_id == 0) {
		return array();
	}

	$row = fs_get_folder_by_id($folder_id, $owner_type, $owner_id);

	return array_merge(array($row), fs_get_folder_path_recursive($row['parent_folder_id'], $owner_type, $owner_id));
}

/**
 * deletes all the files, folders, comments, revisions, etc.. in the specified workspace.
 */
function fs_delete_workspace($owner_type, $owner_id) {
	global $db;

	$sql = "SELECT folder_id, owner_type, owner_id FROM ".TABLE_PREFIX."folders WHERE owner_type=$owner_type AND owner_id=$owner_id AND parent_folder_id=0";
	$result = mysql_query($sql, $db);
	while ($row = mysql_fetch_assoc($result)) {
		fs_delete_folder($row['folder_id'], $row['owner_type'], $row['owner_id']);
	}

	$sql = "SELECT file_id, owner_type, owner_id FROM ".TABLE_PREFIX."files WHERE owner_type=$owner_type AND owner_id=$owner_id";
	$result = mysql_query($sql, $db);
	while ($row = mysql_fetch_assoc($result)) {
		fs_delete_file($row['file_id'], $row['owner_type'], $row['owner_id']);
	}
}

/**
 * copies a file to another workspace.
 * currently only used for submitting assignments.
 **/
function fs_copy_file($file_id, $src_owner_type, $src_owner_id, $dest_owner_type, $dest_owner_id, $dest_folder_id) {
	global $db;

	$sql = "SELECT file_name, file_size, description FROM ".TABLE_PREFIX."files WHERE file_id=$file_id AND owner_type=$src_owner_type AND owner_id=$src_owner_id";
	$result = mysql_query($sql, $db);
	if (!$row = mysql_fetch_assoc($result)) {
		return false;
	}
	$sql = "INSERT INTO ".TABLE_PREFIX."files VALUES (0, $dest_owner_type, $dest_owner_id, $_SESSION[member_id], $dest_folder_id, 0, NOW(), 0, 0, '$row[file_name]', '$row[file_size]', '$row[description]')";
	$result = mysql_query($sql, $db);

	$id = mysql_insert_id($db);

	$src_file  = fs_get_file_path($file_id) . $file_id;
	$dest_file = fs_get_file_path($id) . $id;
	copy($src_file, $dest_file);
}

/**
 * used with usort() to sort the revisions array returned from fs_get_revisions()
 * $col is a valid array key to sort by
 * $order is either 'asc' or 'desc'
 */
function fs_revisions_sort_compare($a, $b) {
	global $col, $order;

	if ($order == 'asc') {
		return strcasecmp($a[$col], $b[$col]);
	}
	return strcasecmp($b[$col], $a[$col]);
}

/**
 * copies a directory to another workspace.
 * not currently used anywhere.
 */
/***
function fs_copy_folder($folder_id, $src_owner_type, $src_owner_id, $dest_owner_type, $dest_owner_id, $dest_parent_folder_id) {
	global $db;

	$folder = fs_get_folder_by_id($folder_id, $src_owner_type, $src_owner_id);
	if (!$folder) {
		return false;
	}

	$sql = "INSERT INTO ".TABLE_PREFIX."folders VALUES (0, $dest_parent_folder_id, $dest_owner_type, $dest_owner_id, '$folder[title]')";
	$result = mysql_query($sql, $db);
	$id = mysql_insert_id($db);

	$sql = "SELECT file_id FROM ".TABLE_PREFIX."files WHERE folder_id=$folder_id AND owner_type=$src_owner_type AND owner_id=$src_owner_id";
	$result = mysql_query($sql, $db);
	while ($row = mysql_fetch_assoc($result)) {
		fs_copy_file($row['file_id'], $src_owner_type, $src_owner_id, $dest_owner_type, $dest_owner_id, $id);
	}

	$folders = fs_get_folder_by_pid($folder_id, $src_owner_type, $src_owner_id);
	foreach ($folders as $folder) {
		fs_copy_folder($folder['folder_id'], $src_owner_type, $src_owner_id, $dest_owner_type, $dest_owner_id, $id);
	}
}
*/
?>