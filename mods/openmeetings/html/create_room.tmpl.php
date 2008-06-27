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
// $Id: create_room.tmpl.php 7208 2008-01-09 16:07:24Z harrisw $
?>
<div class="row">
	<p><label for="openmeetings_roomtype"><?php echo _AT('openmeetings_roomtype'); ?></label></p>
	<?php
		($_POST['openmeetings_roomtype']== 1)? $om_roomtype_conference = 'checked="checked"' : $om_roomtype_audience = 'checked="checked"';
	?>
	<input type="radio" name="openmeetings_roomtype" id="openmeetings_roomtype_conference" value="1" <?php echo $om_roomtype_conference;?>/><label for="openmeetings_roomtype_conference"><?php echo _AT('openmeetings_conference');  ?></label> 
	<input type="radio" name="openmeetings_roomtype" id="openmeetings_roomtype_audience" value="0" <?php echo $om_roomtype_audience;?>/><label for="openmeetings_roomtype_audience"><?php echo _AT('openmeetings_audience');  ?></label> 
</div>
<div class="row">
	<p><label for="openmeetings_num_of_participants"><?php echo _AT('openmeetings_num_of_participants'); ?></label></p>	
	<input type="text" name="openmeetings_num_of_participants" value="<?php echo $_POST['openmeetings_num_of_participants']; ?>" id="openmeetings_num_of_participants" size="80" style="min-width: 95%;" />
</div>
<div class="row">
	<p><label for="openmeetings_ispublic"><?php echo _AT('openmeetings_ispublic'); ?></label></p>
	<?php
		($_POST['openmeetings_ispublic']== 1)? $om_ispublic_y = 'checked="checked"' : $om_ispublic_n = 'checked="checked"';
	?>
	<input type="radio" name="openmeetings_ispublic" id="openmeetings_ispublic_y" value="1" <?php echo $om_ispublic_y;?>/><label for="openmeetings_ispublic_y"><?php echo _AT('yes');  ?></label> 
	<input type="radio" name="openmeetings_ispublic" id="openmeetings_ispublic_n" value="0" <?php echo $om_ispublic_n;?>/><label for="openmeetings_ispublic_n"><?php echo _AT('no');  ?></label> 
</div>

<!-- Video settings -->
<div class="row">
	<p><label for="openmeetings_vid_w"><?php echo _AT('openmeetings_vid_w'); ?></label></p>	
	<input type="text" name="openmeetings_vid_w" value="<?php echo $_POST['openmeetings_vid_w']; ?>" id="openmeetings_vid_w" size="20" />
</div>
<div class="row">
	<p><label for="openmeetings_vid_h"><?php echo _AT('openmeetings_vid_h'); ?></label></p>	
	<input type="text" name="openmeetings_vid_h" value="<?php echo $_POST['openmeetings_vid_h']; ?>" id="openmeetings_vid_h" size="20" />
</div>

<!-- Whiteboard settings -->
<div class="row">
	<?php
		($_POST['openmeetings_show_wb']== 1)? $om_show_wb_y = 'checked="checked"' : $om_show_wb_n = 'checked="checked"';
	?>
	<p><label for="openmeetings_show_wb"><?php echo _AT('openmeetings_show_wb'); ?></label></p>	
	<input type="radio" name="openmeetings_show_wb" id="openmeetings_show_wb_enabled" value="1" <?php echo $om_show_wb_y;?> onclick="om_toggler('openmeetings_wb', true);"/>
	<label for="openmeetings_show_wb_enabled"><?php echo _AT('enable');  ?></label> 
	<input type="radio" name="openmeetings_show_wb" id="openmeetings_show_wb_disabled" value="0" <?php echo $om_show_wb_n;?> onclick="om_toggler('openmeetings_wb', false);"/><label for="openmeetings_show_wb_disabled"><?php echo _AT('disable');  ?></label> 
</div>
<div class="row">
	<p><label for="openmeetings_wb_w"><?php echo _AT('openmeetings_wb_w'); ?></label></p>	
	<input type="text" name="openmeetings_wb_w" value="<?php echo $_POST['openmeetings_wb_w']; ?>" id="openmeetings_wb_w" size="20" />
</div>
<div class="row">
	<p><label for="openmeetings_wb_h"><?php echo _AT('openmeetings_wb_h'); ?></label></p>	
	<input type="text" name="openmeetings_wb_h" value="<?php echo $_POST['openmeetings_wb_h']; ?>" id="openmeetings_wb_h" size="20" />
</div>

<!-- File Panel settings -->
<div class="row">
	<?php
		($_POST['openmeetings_show_fp']== 1)? $om_show_fp_y = 'checked="checked"' : $om_show_fp_n = 'checked="checked"';
	?>
	<p><label for="openmeetings_show_fp"><?php echo _AT('openmeetings_show_fp'); ?></label></p>	
	<input type="radio" name="openmeetings_show_fp" id="openmeetings_show_fp_enabled" value="1" <?php echo $om_show_fp_y;?> onclick="om_toggler('openmeetings_fp', true);"/><label for="openmeetings_show_fp_enabled"><?php echo _AT('enable');  ?></label> 
	<input type="radio" name="openmeetings_show_fp" id="openmeetings_show_fp_disabled" value="0" <?php echo $om_show_fp_n;?> onclick="om_toggler('openmeetings_fp', false);"/><label for="openmeetings_show_fp_disabled"><?php echo _AT('disable');  ?></label> 
</div>
<div class="row">
	<p><label for="openmeetings_fp_w"><?php echo _AT('openmeetings_fp_w'); ?></label></p>	
	<input type="text" name="openmeetings_fp_w" value="<?php echo $_POST['openmeetings_fp_w']; ?>" id="openmeetings_fp_w" size="20" />
</div>
<div class="row">
	<p><label for="openmeetings_fp_h"><?php echo _AT('openmeetings_fp_h'); ?></label></p>	
	<input type="text" name="openmeetings_fp_h" value="<?php echo $_POST['openmeetings_fp_h']; ?>" id="openmeetings_fp_h" size="20" />
</div>