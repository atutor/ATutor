<div class="test_instruction">
	<?php if ($this->weight): ?>
		<div class="test_points">
			<?php if ($this->score=='' && $this->score !== FALSE) echo '<span style="color:red">'._AT('unmarked').'</span>'; ?>
			<?php if ($this->score !== FALSE && $this->question_id): ?>
				<input type="text" name="scores[<?php echo $this->question_id; ?>]" value="<?php echo $this->score; ?>" size="5" style="font-weight: bold; text-align: right" maxlength="5"/> / 
			<?php elseif ($this->score !== FALSE): ?>
				<?php echo $this->score; ?> /
			<?php endif; ?>
			<?php echo $this->weight; ?> <?php echo _AT('points'); ?>
		</div>
	<?php endif; ?>

	<h3 style="color: black"><?php echo _AT('question'); ?> <?php echo $this->number; ?>: <span style="padding-left: 10px"><?php echo $this->type; ?></span></h3>
</span>
	</strong>
</div>
<div class="row" style="padding-bottom: 20px">