<?php
/************************************************************************/
/* ATutor																*/
/************************************************************************/
/* Copyright (c) 2002-2008 by Greg Gay, Cindy Qi Li, Harris Wong		*/
/* Adaptive Technology Resource Centre / University of Toronto			*/
/* http://atutor.ca														*/
/*																		*/
/* This program is free software. You can redistribute it and/or		*/
/* modify it under the terms of the GNU General Public License			*/
/* as published by the Free Software Foundation.						*/
/************************************************************************/
// $Id: group_meeting.inc.php 7208 2008-01-09 16:07:24Z harrisw $

if (empty($_SESSION['groups'])) {
	echo '<div class="openmeetings"><h5>'._AT('openmeetings_group_conference').'</h5>';
	echo _AT('openmeetings_no_group_meetings').'</div>';
} else {
	echo '<div class="openmeetings"><h5>'._AT('openmeetings_group_conference').'</h5>';
	$group_list = implode(',', $_SESSION['groups']);
	$sql = "SELECT group_id, title FROM ".TABLE_PREFIX."groups WHERE group_id IN ($group_list) ORDER BY title";

	$result = mysql_query($sql, $db);

	echo '<ul>';
	//loop through each group and print out a link beside them 
	while ($row = mysql_fetch_assoc($result)) {
		//Check in the db and see if this group has a meeting alrdy, create on if not.
		$om_obj->setGid($row['group_id']);
		if ($om_obj->om_getRoom()){
			//Log into the room
			$room_id = $om_obj->om_addRoom($room_name);
			echo '<li>'.$row['title'].' <a href="mods/openmeetings/view_meetings.php?room_id='.$room_id.SEP.'sid='.$om_obj->getSid().'"> Room-id: '.$room_id.'</a>';
			if ($om_obj->isMine($room_id) || authenticate(AT_PRIV_OPENMEETINGS, true)) {
				//if 'I' created this room, then I will have the permission to remove it from the database.
				echo ' <a href="mods/openmeetings/openmeetings_delete.php?room_id='.$room_id.'">[Delete]</a>';
			}
			echo '</li>';
		} else {
			echo '<li>'.$row['title'].' <a href="mods/openmeetings/add_group_meetings.php?group_id='.$row['group_id'].'"> Start a conference </a>'.'</li>';
		}
	}
	echo '</ul></div>';
}

?>
