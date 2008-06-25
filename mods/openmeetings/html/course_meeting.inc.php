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
// $Id: course_meeting.inc.php 7208 2008-01-09 16:07:24Z harrisw $

//Check if the room is open, if not.  Print error msg to user.
if (!$om_obj->om_getRoom()):
?>
	<div class="openmeetings">
		<h5><?php echo _AT('openmeetings_course_conference'); ?></h5>
		<?php echo _AT('openmeetings_no_course_meetings'); ?>
	</div>

<?php
else:
	//Get the room id
	//TODO: Course title added/removed after creation.  Affects the algo here.
	if (isset($_SESSION['course_title']) && $_SESSION['course_title']!=''){
		$room_name = $_SESSION['course_title'];
	} else {
		$room_name = 'course_'.$course_id;
	}

	//Log into the room
	$room_id = $om_obj->om_addRoom($room_name);
	?>
	<div class="openmeetings">
		<h5><?php echo _AT('openmeetings_course_conference'); ?></h5>
		<ul>
			<li><a href="mods/openmeetings/view_meetings.php?room_id=<?php echo $room_id . SEP; ?>sid=<?php echo $om_obj->getSid(); ?>"><?php echo $_SESSION['course_title']; ?></a></li>
		</ul>
	</div><br/>

<?php endif; ?>