<div style="background-color: #f3f3f3; padding: 5px 10px; margin: 0px; border-top: 1px solid">
	<?php if ($this->weight): ?>
		<div style="float: right; width: 50%; text-align: right; font-weight: bold">
			<?php if ($this->score=='' && $this->score !== FALSE) echo '<span style="color:red">'._AT('unmarked').'</span>'; ?>
			<?php if ($this->score !== FALSE && $this->question_id): ?>
				<input type="text" name="scores[<?php echo $this->question_id; ?>]" value="<?php echo $this->score; ?>" size="5" style="font-weight: bold; text-align: right" maxlength="5"/> / 
			<?php elseif ($this->score !== FALSE): ?>
				<?php echo $this->score; ?> /
			<?php endif; ?>
			<?php echo $this->weight; ?> <?php echo _AT('points'); ?>
		</div>
	<?php endif; ?>

	<h4 style="color: black"><?php echo _AT('question'); ?> <?php echo $this->number; ?>: <span style="padding-left: 10px"><?php echo $this->type; ?></span></h4>
</span>
	</strong>
</div>
<div class="row" style="padding-bottom: 20px">