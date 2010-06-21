<div class="input-form">
<form action="" method="post">
	<div class="row"><?php echo _AT('jb_subscribe_blub');?></div>
	<div class="row">
	<?php foreach ($this->categories as $category): ?>
	<input type="checkbox" name="jb_subscribe_categories[]" value="<?php echo $category['id']; ?>" id="<?php echo 'jb_subscribe_category_'.$category['id']; ?>" />
	<label for="<?php echo 'jb_subscribe_category_'.$category['id']; ?>"><?php echo $this->job_obj->getCategoryNameById($category['id']); ?></label><br/>
	<?php endforeach; ?>
	</div>
	<div class="row">
		<input type="hidden" name="token" value="<?php echo sha1($_SESSION['member_id'].$_SESSION['token']); ?>"/>
		<input type="submit" name="submit" value="<?php echo _AT('submit'); ?>" class="button" />
		<input type="reset" name="reset" value="<?php echo _AT('reset'); ?>" class="button" />
	</div>
</form>
</div>