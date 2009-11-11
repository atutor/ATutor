<?php
/************************************************************************/
/* ATutor																*/
/************************************************************************/
/* Copyright (c) 2002-2008 by Greg Gay, Joel Kronenberg & Heidi Hazelton*/
/* Adaptive Technology Resource Centre / University of Toronto			*/
/* http://atutor.ca														*/
/*																		*/
/* This program is free software. You can redistribute it and/or		*/
/* modify it under the terms of the GNU General Public License			*/
/* as published by the Free Software Foundation.						*/
/************************************************************************/
if (!defined('AT_INCLUDE_PATH')) { exit; } 

global $onload;
$onload = 'document.form.title.focus();';

require(AT_INCLUDE_PATH.'header.inc.php');

if ($this->shortcuts): 
?>
<fieldset id="shortcuts"><legend><?php echo _AT('shortcuts'); ?></legend>
	<ul>
		<?php foreach ($this->shortcuts as $link): ?>
			<li><a href="<?php echo $link['url']; ?>"><?php echo $link['title']; ?></a></li>
		<?php endforeach; ?>
	</ul>
</fieldset>
<?php endif; ?>

<form action="<?php echo $_SERVER['PHP_SELF']; if ($this->cid > 0) echo '?cid='.$this->cid; else if ($this->pid > 0) echo '?pid='.$this->pid;?>" method="post" name="form"> 
<div class="input-form" style="width:80%;margin-left:1.5em;">
	<div class="row">
		<div class="required" title="<?php echo _AT('required_field'); ?>">*</div><label for="ftitle"><?php echo _AT('content_folder_title');  ?></label><br /><br />
		<input type="text" name="title" id="ftitle" size="70" class="formfield" value="<?php echo ContentManager::cleanOutput($this->ftitle); ?>" />
	</div>
	
	<div class="row">
		<?php echo _AT('release_date');  ?><br />
		<?php if ($_POST['day']) { ?>
			<?php
				$today_day   = $_POST['day'];
				$today_mon   = $_POST['month'];
				$today_year  = $_POST['year'];

				$today_hour  = $_POST['hour'];
				$today_min   = $_POST['min'];		
		}?>
		<?php require(AT_INCLUDE_PATH.'html/release_date.inc.php');	?>
		<?php echo _AT('applies_to_all_sub_pages'); ?>
	</div>

	<div class="row buttons">
		<input type="submit" name="submit" value="<?php echo _AT('save'); ?>" title="<?php echo _AT('save_changes'); ?> alt-s" accesskey="s" />
	</div>
</div>
</form>

<?php require(AT_INCLUDE_PATH.'footer.inc.php');?>