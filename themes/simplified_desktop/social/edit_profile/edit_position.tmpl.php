<?php
	global $addslashes;
	//escape all strings
	$company		= htmlentities_utf8($this->company);
	$title			= htmlentities_utf8($this->profile_title);
	$description	= htmlentities_utf8($this->description, false);
	$from			= htmlentities_utf8($this->from);
	$to				= htmlentities_utf8($this->to);
?>
<script type="text/javascript" src="jscripts/lib/calendar.js"></script>
<script type="text/javascript"> 
<!--
	//overwrite calendar dates range settings.
	scwBaseYear			= scwDateNow.getFullYear()-50; 
	scwDropDownYears	= 70; 
-->
</script>
<div class="headingbox"><h3><?php if($_GET['id']){echo _AT('edit_position');}else{echo  _AT('add_new_position');}?></h3></div>
<div class="contentbox">
<form method="post" action="<?php echo url_rewrite(AT_SOCIAL_BASENAME.'edit_profile.php'); ?>">
	<dl id="public-profile">
		<div class="row">
		<dt><label for="company"><?php echo _AT('company'); ?></label></dt>
		<dd><input type="text" id="company" name="company" value="<?php echo $company; ?>" /></dd>
		</div>
		<div class="row">
		<dt><label for="title"><?php echo _AT('position'); ?></label></dt>
		<dd><input type="text" id="title" name="title" value="<?php echo $title; ?>" /></dd>
		</div>
		<div class="row">
		<dt><label for="from"><?php echo _AT('from'); ?></label></dt>
		<dd><input type="text" id="from" name="from" value="<?php echo $from; ?>" />
		<img src='images/calendar.gif' style="vertical-align: middle; cursor: pointer;" onclick="scwShow(scwID('from'),event);"  alt="<?php echo _AT('date'); ?>"/></dd>
		</div>
		<div class="row">
		<dt><label for="to"><?php echo _AT('to'); ?></label></dt>
		<dd><input type="text" id="to" name="to" value="<?php echo $to; ?>" />
		<img src='images/calendar.gif' style="vertical-align: middle; cursor: pointer;" onclick="scwShow(scwID('to'),event);"  alt="<?php echo _AT('date'); ?>"/></dd>
		</div>
		<dt><label for="description"><?php echo _AT('description'); ?></label>	</dt>
		<dd><textarea name="description" id="description" cols="40" rows="5"><?php echo $description; ?></textarea></dd>
	</dl>
	<input type="hidden" name="id" value="<?php echo $this->id; ?>" />
	
		<input type="hidden" name="id" value="<?php echo $this->id; ?>" />
		<?php if($_GET['id']){ ?>
		<input type="hidden" name="edit" value="position" />
		<?php } else { ?>
		<input type="hidden" name="add" value="position" />
		<?php } ?>
	<input type="submit" class="button" name="submit" value="<?php echo _AT('save'); ?>" />
	<input type="submit" class="button" name="cancel" value="<?php echo _AT('cancel'); ?>" />
</form>
</div>