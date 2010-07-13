<?php global $_config; ?>
<div class="input-form">
	<form action="" method="post">
		<div class="row">
			<label><?php echo _AT('jb_required_posting_approval'); ?></label>
			<label for="jb_posting_approval_yes"><?php echo _AT('yes'); ?></label>
			<input type="radio" id="jb_posting_approval_yes" name="jb_posting_approval" value="1" <?php echo ($_config['jb_posting_approval']==1)?'checked="checked"':''; ?> />
			<label for="jb_posting_approval_no"><?php echo _AT('no'); ?></label>
			<input type="radio" id="jb_posting_approval_no" name="jb_posting_approval" value="0" <?php echo ($_config['jb_posting_approval']==0)?'checked="checked"':''; ?> />
		</div>
		
		<div class="row">
			<input class="button" type="submit" name="submit" value="<?php echo _AT('submit'); ?>" />
		</div>
	</form>
</div>
