<?php
/************************************************************************/
/* ATutor																*/
/************************************************************************/
/* Copyright (c) 2002-2010                                              */
/* Inclusive Design Institute                                           */
/* http://atutor.ca														*/
/*																		*/
/* This program is free software. You can redistribute it and/or        */
/* modify it under the terms of the GNU General Public License          */
/* as published by the Free Software Foundation.                        */
/************************************************************************/
if (!defined('AT_INCLUDE_PATH')) { exit; } 

global $onload;
$onload = 'document.form.title.focus();';
?>
<form action="<?php echo $_SERVER['PHP_SELF']; if ($this->cid > 0) echo '?cid='.$this->cid; else if ($this->pid > 0) echo '?pid='.$this->pid;?>" method="post" name="form"> 
<div class="input-form" style="width:95%;margin-left:1.5em;">
<!-- <?php
if ($this->shortcuts): 
?>
 <fieldset id="shortcuts" style="margin-top:1em;float:right;clear:right;"><legend><?php echo _AT('shortcuts'); ?></legend>
	<ul>
		<?php foreach ($this->shortcuts as $link): ?>
			<li><a href="<?php echo $link['url']; ?>"><?php echo $link['title']; ?></a></li>
		<?php endforeach; ?>
	</ul>
</fieldset>
<?php endif; ?> -->
	<div class="row">
		<div style="font-weight:bold;"><span class="required" title="<?php echo _AT('required_field'); ?>">*</span><label for="ftitle"><?php echo _AT('content_folder_title');  ?></label></div>
		<input type="text" name="title" id="ftitle" size="70" class="formfield" value="<?php echo ContentManager::cleanOutput($this->ftitle); ?>" />
	</div>
	
	<div class="row">
		<div style="font-weight:bold;"><?php echo _AT('release_date');  ?></div>
		<?php if ($_POST['day']) { ?>
			<?php
				$today_day   = $_POST['day'];
				$today_mon   = $_POST['month'];
				$today_year  = $_POST['year'];

				$today_hour  = $_POST['hour'];
				$today_min   = $_POST['min'];		
		}?>
		<?php require(AT_INCLUDE_PATH.'html/release_date.inc.php');	?>
	</div>

	<?php if (isset($this->pretests)) {?>
	<div class="row">
		<div style="font-weight:bold;"><?php echo _AT('define_pretest'); ?></div>
		<p><?php echo _AT('about_pretest'); ?></p>
	</div>

	<div class="row">
	<table class="data" summary="" style="width: 98%" rules="cols">
	<thead>
	<tr>
		<th scope="col">&nbsp;</th>
		<th scope="col"><?php echo _AT('title');          ?></th>
		<th scope="col"><?php echo _AT('status');         ?></th>
		<th scope="col"><?php echo _AT('result_release'); ?></th>
		<th scope="col"><?php echo _AT('pass_score');	  ?></th>
		<th scope="col"><?php echo _AT('assigned_to');	  ?></th>
	</tr>
	</thead>
	<tbody>
	<?php foreach ($this->pretests as $row) { ?>
	<?php
		$checkMe = '';
		if (is_array($_POST['pre_tid']) && in_array($row['test_id'], $_POST['pre_tid'])){
			$checkMe = ' checked="checked"';
		} 
	?>
	<tr onmousedown="toggleTestSelect('r_<?php echo $row['test_id']; ?>');rowselect(this);" id="r_<?php echo $row['test_id']; ?>">
		<td><input type="checkbox" name="tid[]" value="<?php echo $row['test_id']; ?>" id="t<?php echo $row['test_id']; ?>" <?php echo $checkMe; ?> onmouseup="this.checked=!this.checked" /></td>
		<td><?php echo $row['title']; ?></td>
		<td><?php echo $row['status']; ?></td>
		<!-- <td><?php echo $row['availability']; ?></td> -->
		<td><?php echo $row['result_release']; ?></td>
		<td><?php echo $row['pass_score']; ?></td>
		<td><?php echo $row['assign_to']; ?></td>
	</tr>
	<?php } ?>
	</tbody>
	</table>
	</div>
<?php }?>

	<div class="row buttons">
		<input type="submit" name="submit" value="<?php echo _AT('save'); ?>" title="<?php echo _AT('save_changes'); ?> alt-s" accesskey="s" />
	</div>
</div>
</form>

<script language="javascript" type="text/javascript">
	function toggleTestSelect(r_id){
		var row = document.getElementById(r_id);
		var checkBox = row.cells[0].firstChild;

		if (checkBox.checked == true){
			checkBox.checked = false;
		} else {
			checkBox.checked = true;
		}
	}
</script>