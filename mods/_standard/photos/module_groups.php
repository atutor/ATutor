<?php
/****************************************************************************/
/* ATutor																	*/
/****************************************************************************/
/* Copyright (c) 2002-2010                                                  */
/* Inclusive Design Institute                                               */
/* http://atutor.ca															*/
/*																			*/
/* This program is free software. You can redistribute it and/or			*/
/* modify it under the terms of the GNU General Public License				*/
/* as published by the Free Software Foundation.							*/
/****************************************************************************/
// $Id$
/**
 * create an album for photo with the following attributes
 *
 * album type: course albums
 * album permission: private
 * album location: n/a
 * album description: group description/na
 * album creator: instructor_id
 */
require_once (AT_INCLUDE_PATH.'../mods/_standard/photos/include/lib.inc.php');
require_once (AT_INCLUDE_PATH.'../mods/_standard/photos/include/classes/PhotoAlbum.class.php');
function photos_create_group($group_id) {
    $group_id = intval($group_id);
    //get group name

    $sql = "SELECT title FROM %sgroups WHERE group_id=%d";
    $group_info = queryDB($sql, array(TABLE_PREFIX, $group_id), TRUE);

    $pa = new PhotoAlbum();
    $album_name = $group_info['title'] . '(' . _AT('group') . ')';
    $album_location = _AT('na');
    $album_description = _AT('na');
    $album_type = AT_PA_TYPE_COURSE_ALBUM;
    $album_permission = AT_PA_PRIVATE_ALBUM;

    $album_id = $pa->createAlbum($album_name, $album_location, $album_description, $album_type, $album_permission, $_SESSION['member_id'], 0);
    if ($album_id === false){
        //TODO: sql failure.
        $msg->addError('PA_CREATE_ALBUM_FAILED');
        $result = false;
    } else {

        $sql = "INSERT INTO %spa_groups (group_id, album_id) VALUES (%d, %d)";
        $result = queryDB($sql, array(TABLE_PREFIX, $group_id, $album_id));
    }
}

// delete group
function photos_delete_group($group_id) {

	$group_id = intval($group_id);

	$sql = "SELECT album_id FROM %spa_groups WHERE group_id=%d";
	$rows_groups = queryDB($sql, array(TABLE_PREFIX, $group_id));
	
	foreach($rows_groups as $row){
        $pa = new PhotoAlbum($row['album_id']);
		$pa->deleteAlbum();
	}

	$sql = "DELETE FROM %spa_groups WHERE group_id=%d";
	$result = queryDB($sql, array(TABLE_PREFIX, $group_id));
}

?>