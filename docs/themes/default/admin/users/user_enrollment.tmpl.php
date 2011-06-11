<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>?id=<?php echo $this->id; ?>">
<input type="hidden" name="id" value="<?php echo $this->id; ?>"/>
<div class="input-form" style="min-width: 400px; width: 45%; float: left; margin: 5px">
	<div class="row">
	<h3><?php echo _AT('instructor'); ?></h3>
		<?php if ($this->instruct): ?>
			<ul>
			<?php foreach ($this->instruct as $cid): ?>
				<li><?php echo $this->system_courses[$cid]['title']; ?></li>
			<?php endforeach; ?>
			</ul>
		<?php else: ?>
			<?php echo _AT('none'); ?>
		<?php endif; ?>
	</div>
	<div class="row buttons">
		<input type="submit" name="cancel" value="<?php echo _AT('cancel'); ?>"/>
	</div>
</div>

<div class="input-form" style="min-width: 400px; width: 45%; float: left; margin: 5px">
	<div class="row">
	<h3><?php echo _AT('enrolled'); ?></h3>
		<?php if ($this->enrolled): ?>
			<ul>
			<?php foreach ($this->enrolled as $cid): ?>
				<li><input type="checkbox" name="enrolled[]" value="<?php echo $cid; ?>" id="c<?php echo $cid; ?>"/><label for="c<?php echo $cid; ?>"><?php echo $this->system_courses[$cid]['title']; ?></label></li>
			<?php endforeach; ?>
			</ul>
		<?php else: ?>
			<?php echo _AT('none'); ?>
		<?php endif; ?>
	</div>
	<div class="row buttons">
	<?php if ($this->enrolled): ?>
		<input type="submit" name="enrolled_unenroll" value="<?php echo _AT('unenroll'); ?>"/>
	<?php endif; ?>
		<input type="submit" name="cancel" value="<?php echo _AT('cancel'); ?>"/>
	</div>

</div>

<div class="input-form" style="min-width: 400px; width: 45%; float: left; margin: 5px">
	<div class="row">
	<h3><?php echo _AT('pending_enrollment'); ?></h3>
		<?php if ($this->pending): ?>
			<ul>
			<?php foreach ($this->pending as $cid): ?>
				<li><input type="checkbox" name="pending[]" value="<?php echo $cid; ?>" id="c<?php echo $cid; ?>"/><label for="c<?php echo $cid; ?>"><?php echo $this->system_courses[$cid]['title']; ?></label></li>
			<?php endforeach; ?>
			</ul>
		<?php else: ?>
			<?php echo _AT('none'); ?>
		<?php endif; ?>
	</div>
	<div class="row buttons">
	<?php if ($this->pending): ?>
		<input type="submit" name="pending_remove" value="<?php echo _AT('remove'); ?>"/>
		<input type="submit" name="pending_enroll" value="<?php echo _AT('enroll'); ?>"/>
	<?php endif; ?>
		<input type="submit" name="cancel" value="<?php echo _AT('cancel'); ?>"/>
	</div>	
</div>

<div class="input-form" style="min-width: 400px; width: 45%; float: left; margin: 5px">
	<div class="row">
		<h3><?php echo _AT('not_enrolled');?></h3>
			<?php if ($this->not_enrolled): ?>
				<ul>
				<?php foreach ($this->not_enrolled as $cid): ?>
					<li><input type="checkbox" name="not_enrolled[]" value="<?php echo $cid; ?>" id="c<?php echo $cid; ?>"/><label for="c<?php echo $cid; ?>"><?php echo $this->system_courses[$cid]['title']; ?></label></li>
				<?php endforeach; ?>
				</ul>
			<?php else: ?>
				<?php echo _AT('none'); ?>
			<?php endif; ?>
	</div>
	<div class="row buttons">
	<?php if ($this->not_enrolled): ?>
		<input type="submit" name="not_enrolled_enroll" value="<?php echo _AT('enroll'); ?>"/>
	<?php endif; ?>
		<input type="submit" name="cancel" value="<?php echo _AT('cancel'); ?>"/>
	</div>
</div>
</form>