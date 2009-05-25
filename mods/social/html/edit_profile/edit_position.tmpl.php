<?php
	global $addslashes;

	//escape all strings
	$company		= $addslashes($this->company);
	$title			= $addslashes($this->title);
	$description	= $addslashes($this->description);
	$from			= $addslashes($this->from);
	$to				= $addslashes($this->to);
?>
<script type='text/javascript' src='jscripts/calendar.js'></script>
<div class="headingbox"><h3><?php if($_GET['id']){echo _AT('edit_position');}else{echo  _AT('add_new_position');}?></h3></div>
<div class="contentbox">
<form method="POST" action="<?php echo url_rewrite(AT_SOCIAL_BASENAME.'edit_profile.php'); ?>">
	<dl id="public-profile">

		<dt><label for="company"><?php echo _AT('company'); ?></label></dt>
		<dd><input type="text" id="company" name="company" value="<?php echo $company; ?>" /></dd>
	
		<dt><label for="title"><?php echo _AT('title'); ?></label></dt>
		<dd><input type="text" id="title" name="title" value="<?php echo $title; ?>" /></dd>
	
		<dt><label for="from"><?php echo _AT('from'); ?></label></dt>
		<dd><input type="text" id="from" name="from" value="<?php echo $from; ?>" />
		<img src='images/calendar.gif' style="vertical-align: middle; cursor: pointer;" onclick="scwShow(scwID('from'),event);" /></dd>

		<dt><label for="to"><?php echo _AT('to'); ?></label></dt>
		<dd><input type="text" id="to" name="to" value="<?php echo $to; ?>" />
		<img src='images/calendar.gif' style="vertical-align: middle; cursor: pointer;" onclick="scwShow(scwID('to'),event);" /></dd>
	
		<dt><label for="description"><?php echo _AT('description'); ?></label>	</dt>
		<dd><textarea name="description" id="description" cols="40" rows="5"><?php echo $description; ?></textarea></dd>

	<input type="hidden" name="id" value="<?php echo $this->id; ?>" />
	
		<input type="hidden" name="id" value="<?php echo $this->id; ?>" />
		<?php if($_GET['id']){ ?>
		<input type="hidden" name="edit" value="position" />
		<?php } else { ?>
		<input type="hidden" name="add" value="position" />
		<?php } ?>
	<input type="submit" class="button" name="submit" value="<?php echo _AT('save'); ?>" />
	<input type="submit" class="button" name="cancel" value="<?php echo _AT('cancel'); ?>" />
</div>
</form>