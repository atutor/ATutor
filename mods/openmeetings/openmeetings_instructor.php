<?php
define('AT_INCLUDE_PATH', '../../include/');
require (AT_INCLUDE_PATH.'vitals.inc.php');
authenticate(AT_PRIV_OPENMEETINGS);
require ('openmeetings.class.php');
require (AT_INCLUDE_PATH.'header.inc.php');

//local variables
$course_id = $_SESSION['course_id'];

//Initiate Openmeeting
$om_obj = new Openmeetings($course_id, $_SESSION['member_id']);

//Login
$om_obj->om_login();

//Handle form action
if (isset($_POST['submit']) && isset($_POST['room_id'])) {
	//delete course
	$_POST['room_id'] = intval($_POST['room_id']);
	$om_obj->om_deleteRoom($_POST['room_id']);

} elseif (isset($_POST['submit'])){
	//mysql escape
	$_POST['openmeetings_num_of_participants']	= intval($_POST['openmeetings_num_of_participants']);
	$_POST['openmeetings_ispublic']				= intval($_POST['openmeetings_ispublic']);
	$_POST['openmeetings_vid_w']				= intval($_POST['openmeetings_vid_w']);
	$_POST['openmeetings_vid_h']				= intval($_POST['openmeetings_vid_h']);
	$_POST['openmeetings_show_wb']				= intval($_POST['openmeetings_show_wb']);
	$_POST['openmeetings_wb_w']					= intval($_POST['openmeetings_wb_w']);
	$_POST['openmeetings_wb_h']					= intval($_POST['openmeetings_wb_h']);
	$_POST['openmeetings_show_fp']				= intval($_POST['openmeetings_show_fp']);
	$_POST['openmeetings_fp_w']					= intval($_POST['openmeetings_fp_w']);
	$_POST['openmeetings_fp_h']					= intval($_POST['openmeetings_fp_h']);
} 

//Get the room id
//TODO: Course title added/removed after creation.  Affects the algo here.
if (isset($_SESSION['course_title']) && $_SESSION['course_title']!=''){
	$room_name = $_SESSION['course_title'];
} else {
	$room_name = 'course_'.$course_id;
}

//Log into the room
$room_id = $om_obj->om_getRoom($room_name);
debug($room_id, 'You have a room'); 
if ($room_id == false):
?>

<?php
/*
 * Available param to edit
             "SID"						=> $parameters["SID"],
			'name'						=> $parameters["name"],
			'roomtypes_id'				=> 1,
			'comment'					=> 'Room created by ATutor',
*			'numberOfPartizipants'		=> 16,
*			'ispublic'					=> true,
*			'videoPodWidth'				=> 270, 
*			'videoPodHeight'			=> 280,
			'videoPodXPosition'			=> 2, 
			'videoPodYPosition'			=> 2, 
			'moderationPanelXPosition'	=> 400, 
*			'showWhiteBoard'			=> true, 
			'whiteBoardPanelXPosition'	=> 276, 
			'whiteBoardPanelYPosition'	=> 2, 
*			'whiteBoardPanelHeight'		=> 592, 
*			'whiteBoardPanelWidth'		=> 660, 
*			'showFilesPanel'			=> true, 
			'filesPanelXPosition'		=> 2, 
			'filesPanelYPosition'		=> 284, 
*			'filesPanelHeight'			=> 310, 
*			'filesPanelWidth'			=> 270
*/
?>
<form action="<?php $_SERVER['PHP_SELF']; ?>" method="post">
	<div class="input-form">
		<div class="row">
			<p><label for="openmeetings_num_of_participants"><?php echo _AT('openmeetings_num_of_participants'); ?></label></p>	
			<input type="text" name="openmeetings_num_of_participants" value="<?php echo $_POST['openmeetings_num_of_participants']; ?>" id="openmeetings_num_of_participants" size="80" style="min-width: 95%;" />
		</div>
		<div class="row">
			<p><label for="openmeetings_ispublic"><?php echo _AT('openmeetings_ispublic'); ?></label></p>	
			<input type="radio" name="openmeetings_ispublic" id="openmeetings_ispublic_y" value="1"/><label for="openmeetings_ispublic_y"><?php echo _AT('yes');  ?></label> 
			<input type="radio" name="openmeetings_ispublic" id="openmeetings_ispublic_n" value="0"/><label for="openmeetings_ispublic_n"><?php echo _AT('no');  ?></label> 
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
			<p><label for="openmeetings_show_wb"><?php echo _AT('openmeetings_show_wb'); ?></label></p>	
			<input type="radio" name="openmeetings_show_wb" id="openmeetings_show_wb_enabled" value="1"/><label for="openmeetings_show_wb_enabled"><?php echo _AT('enable');  ?></label> 
			<input type="radio" name="openmeetings_show_wb" id="openmeetings_show_wb_disabled" value="0"/><label for="openmeetings_show_wb_disabled"><?php echo _AT('disable');  ?></label> 
		</div>
		<div class="row">
			<p><label for="openmeetings_wb_w"><?php echo _AT('openmeetings_wb_w'); ?></label></p>	
			<input type="text" name="openmeetings_wb_w" value="<?php echo $_POST['openmeetings_wb_w']; ?>" id="openmeetings_wb_w" size="20" />
		</div>
		<div class="row">
			<p><label for="openmeetings_vid_h"><?php echo _AT('openmeetings_vid_h'); ?></label></p>	
			<input type="text" name="openmeetings_wb_h" value="<?php echo $_POST['openmeetings_wb_h']; ?>" id="openmeetings_wb_h" size="20" />
		</div>

		<!-- File Panel settings -->
		<div class="row">
			<p><label for="openmeetings_show_fp"><?php echo _AT('openmeetings_show_fp'); ?></label></p>	
			<input type="radio" name="openmeetings_show_fp" id="openmeetings_show_fp_enabled" value="1"/><label for="openmeetings_show_fp_enabled"><?php echo _AT('enable');  ?></label> 
			<input type="radio" name="openmeetings_show_fp" id="openmeetings_show_fp_disabled" value="0"/><label for="openmeetings_show_fp_disabled"><?php echo _AT('disable');  ?></label> 
		</div>
		<div class="row">
			<p><label for="openmeetings_fp_w"><?php echo _AT('openmeetings_fp_w'); ?></label></p>	
			<input type="text" name="openmeetings_fp_w" value="<?php echo $_POST['openmeetings_fp_w']; ?>" id="openmeetings_fp_w" size="20" />
		</div>
		<div class="row">
			<p><label for="openmeetings_fp_h"><?php echo _AT('openmeetings_fp_h'); ?></label></p>	
			<input type="text" name="openmeetings_fp_h" value="<?php echo $_POST['openmeetings_fp_h']; ?>" id="openmeetings_fp_h" size="20" />
		</div>

		<div class="row buttons">
			<input type="submit" name="submit" value="<?php echo _AT('openmeetings_start'); ?>"  />
		</div>
	</div>
</form>
<?php
else: 
?>
<form action="<?php $_SERVER['PHP_SELF']; ?>" method="post">
<div class="input-form">
	<div class="row">You already have started a room, would you like to close the current one and start a new one?</div>
	<div class="row">(Note, once the room is closed, all chat logs and associated room materials will be deleted.)</div>
	<div class="row buttons">
		<input type="hidden" name="room_id" value="<?php echo $room_id?>" />
		<input type="submit" name="submit" value="<?php echo _AT('yes'); ?>"  />
		<input type="submit" name="cancel" value="<?php echo _AT('cancel'); ?>"  />
	</div>
</div>
</form>
<?php
endif;
?>

<?php require (AT_INCLUDE_PATH.'footer.inc.php'); ?>