<div class="jb_view_container">
	<h4><?php echo $this->job_post['title'];?></h4>
	
	<div>
		<label><?php echo _AT('jb_categories'); ?></label>
		<?php if(is_array($this->job_post['categories'])):
				foreach($this->job_post['categories'] as $category): ?>
		<span><?php echo $this->job_obj->getCategoryNameById($category);?></span>
		<?php endforeach; else: ?>
		<span><?php echo $this->job_obj->getCategoryNameById($this->job_post['categories']);?></span>
		<?php endif; ?>
	</div>

	<div>
		<label><?php echo _AT('jb_company'); ?></label>
		<span><?php echo $this->job_post['company']; ?></span>
	</div>

	<div>
		<label><?php echo _AT('jb_closing_date'); ?></label>
		<span><?php echo $this->job_post['closing_date']; ?></span>
	</div>

	<div>
		<label><?php echo _AT('jb_description'); ?></label>
		<p><?php echo $this->job_post['description']; ?></p>
	</div>

</div>