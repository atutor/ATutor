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
// $Id: edit_room.inc.php 7208 2008-01-09 16:07:24Z harrisw $

?>
<form action="<?php $_SERVER['PHP_SELF']; ?>" method="post">
<div class="input-form">
	<div class="row"><?php echo _AT('openmeetings_existing_room', $_SERVER['PHP_SELF'].'?action=view'.SEP.'room_id='.$room_id.SEP.'sid='.$om_obj->getSid()); ?></div>
	<div class="row"><?php echo _AT('openmeetings_deleting_warning'); ?></div>
	<div class="row buttons">
		<input type="hidden" name="room_id" value="<?php echo $room_id?>" />		
		<input type="submit" name="edit_room" value="<?php echo _AT('openmeetings_edit_room'); ?>"  />
		<input type="submit" name="delete_room" value="<?php echo _AT('openmeetings_delete_room'); ?>"  />
		<input type="submit" name="create_room" value="<?php echo _AT('openmeetings_create_room'); ?>"  />
	</div>
</div>

</form>