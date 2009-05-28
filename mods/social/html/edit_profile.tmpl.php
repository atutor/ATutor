<h2><?php echo $this->profile['first_name'].' '.$this->profile['last_name']; ?></h2>


<?php include(AT_SOCIAL_INCLUDE."profile_menu.inc.php")  ?>

<ul>
	<li>
		<div>
		<strong><?php echo _AT('position'); ?></strong>	<br/>
		<?php 
		if (!empty($this->position)):
			//note: $id is just a array holder, it does not represent $row[id]
			foreach ($this->position as $id=>$row): ?>
		<div class="profile_container">
			<div class="top_right" style="border:thin #cccccc solid;"><a href="<?php echo url_rewrite(AT_SOCIAL_BASENAME.'edit_profile.php?edit=position'.SEP.'id='.$row['id']); ?>"><img src="<?php echo $_base_href.AT_SOCIAL_BASENAME; ?>images/edit_profile.gif" alt="<?php echo _AT('edit'); ?>" title="<?php echo _AT('edit'); ?>" border="0" /></a>  <a href="<?php echo url_rewrite(AT_SOCIAL_BASENAME.'edit_profile.php?delete=position'.SEP.'id='.$row['id']); ?>"><img src="<?php echo $_base_href.AT_SOCIAL_BASENAME; ?>images/b_drop.png" alt="<?php echo _AT('remove'); ?> ?>" title="<?php echo _AT('remove'); ?>" border="0" /></a></div>
			<div><?php echo _AT('company') . ': ' . htmlentities_utf8($row['company']); ?></div>
			<div><?php echo _AT('title') . ': ' . htmlentities_utf8($row['title']); ?></div>
			<div><?php echo _AT('from') . ': ' . htmlentities_utf8($row['from']);?></div>
			<div><?php echo _AT('to') . ': ' . htmlentities_utf8($row['to']); ?></div>
			<div><?php echo _AT('description') . ': ' . htmlentities_utf8($row['description']); ?></div>
		</div>
		<?php
			endforeach;
		endif; ?>
			<p><a href="<?php echo url_rewrite(AT_SOCIAL_BASENAME.'edit_profile.php?add=position'); ?>"><?php echo _AT('add_new_position'); ?></a></p>
		</div>
	</li>
	<li>
		<strong><?php echo _AT('education'); ?></strong><br/>
		<?php 	
		if (!empty($this->education)):
			foreach ($this->education as $id=>$row): ?>
		<div class="profile_container">
			<div class="top_right" style="border:thin #cccccc solid;"><a href="<?php echo url_rewrite(AT_SOCIAL_BASENAME.'edit_profile.php?edit=education'.SEP.'id='.$row['id']); ?>"><img src="<?php echo $_base_href.AT_SOCIAL_BASENAME; ?>images/edit_profile.gif" alt="<?php echo _AT('edit'); ?>" title="<?php echo _AT('edit'); ?>" border="0" /></a>  <a href="<?php echo url_rewrite(AT_SOCIAL_BASENAME.'edit_profile.php?delete=education'.SEP.'id='.$row['id']); ?>"><img src="<?php echo $_base_href.AT_SOCIAL_BASENAME; ?>images/b_drop.png" alt="<?php echo _AT('remove'); ?> ?>" title="<?php echo _AT('remove'); ?>" border="0" /></a></div>
			<div><?php echo _AT('university') . ': ' . htmlentities_utf8($row['university']); ?></div>
			<div><?php echo _AT('location') . ': ' . htmlentities_utf8($row['country']) . ', ' . htmlentities_utf8($row['province']); ?></div>
			<div><?php echo _AT('degree') . ': ' . htmlentities_utf8($row['degree']); ?></div>
			<div><?php echo _AT('field') . ': ' . htmlentities_utf8($row['field']); ?></div>
			<div><?php echo _AT('from') . ': ' . htmlentities_utf8($row['from']);?></div>
			<div><?php echo _AT('to') . ': ' . htmlentities_utf8($row['to']); ?></div>
			<div><?php echo _AT('description') . ': ' . htmlentities_utf8($row['description']); ?></div>
		</div>
		<?php 
			endforeach; 
		endif; ?>
			<p><a href="<?php echo url_rewrite(AT_SOCIAL_BASENAME.'edit_profile.php?add=education'); ?>"><?php echo _AT('add_new_education'); ?></a></p>
	</li>
	<li>
		<strong><?php echo _AT('websites'); ?></strong><br/>
		<?php 	
		if (!empty($this->websites)):
			foreach ($this->websites as $id=>$row): ?>
		<div class="profile_container">
			<div class="top_right" style="border:thin #cccccc solid;"><a href="<?php echo url_rewrite(AT_SOCIAL_BASENAME.'edit_profile.php?edit=websites'.SEP.'id='.$row['id']); ?>"><img src="<?php echo $_base_href.AT_SOCIAL_BASENAME; ?>images/edit_profile.gif" alt="<?php echo _AT('edit'); ?>" title="<?php echo _AT('edit'); ?>" border="0" /></a>  <a href="<?php echo url_rewrite(AT_SOCIAL_BASENAME.'edit_profile.php?delete=websites'.SEP.'id='.$row['id']); ?>"><img src="<?php echo $_base_href.AT_SOCIAL_BASENAME; ?>images/b_drop.png" alt="<?php echo _AT('remove'); ?> ?>" title="<?php echo _AT('remove'); ?>" border="0" /></a></div>
			<div><?php echo _AT('site_name') . ': ' . htmlentities_utf8($row['site_name']); ?></div>
			<div><?php echo _AT('url') . ': ' . $row['url']; ?></div>
		</div>
		<?php 
			endforeach; 
		endif; ?>
		<p><a href="<?php echo url_rewrite(AT_SOCIAL_BASENAME.'edit_profile.php?add=websites'); ?>"><?php echo _AT('add_new_website'); ?></a></p>
	</li>

	<li>
		<strong><?php echo _AT('interests'); ?></strong><br/>
		<?php if (!empty($this->profile['interests'])): ?>
		<div class="profile_container">
			<div class="top_right" style="border:thin #cccccc solid;"><a href="<?php echo url_rewrite(AT_SOCIAL_BASENAME.'edit_profile.php?edit=interests'.SEP.'id='.$row['id']); ?>"><img src="<?php echo $_base_href.AT_SOCIAL_BASENAME; ?>images/edit_profile.gif" alt="<?php echo _AT('edit'); ?>" title="<?php echo _AT('edit'); ?>" border="0" /></a>  <a href="<?php echo url_rewrite(AT_SOCIAL_BASENAME.'edit_profile.php?delete=interests'); ?>"><img src="<?php echo $_base_href.AT_SOCIAL_BASENAME; ?>images/b_drop.png" alt="<?php echo _AT('remove'); ?> ?>" title="<?php echo _AT('remove'); ?>" border="0" /></a></div>
			<div><?php echo htmlentities_utf8($this->profile['interests']); ?></div>
		</div>
		<?php else: ?>
		<p><a href="<?php echo url_rewrite(AT_SOCIAL_BASENAME.'edit_profile.php?add=interests'); ?>"><?php echo _AT('add_new_interest'); ?></a></p>
		<?php endif; ?>
	</li>

	<li>
		<strong><?php echo _AT('associations'); ?></strong><br/>
		<?php if (!empty($this->profile['associations'])): ?>
		<div class="profile_container">
			<div class="top_right" style="border:thin #cccccc solid;"><a href="<?php echo url_rewrite(AT_SOCIAL_BASENAME.'edit_profile.php?edit=associations'.SEP.'id='.$row['id']); ?>"><img src="<?php echo $_base_href.AT_SOCIAL_BASENAME; ?>images/edit_profile.gif" alt="<?php echo _AT('edit'); ?>" title="<?php echo _AT('edit'); ?>" border="0" /></a>  <a href="<?php echo url_rewrite(AT_SOCIAL_BASENAME.'edit_profile.php?delete=associations'); ?>"><img src="<?php echo $_base_href.AT_SOCIAL_BASENAME; ?>images/b_drop.png" alt="<?php echo _AT('remove'); ?> ?>" title="<?php echo _AT('remove'); ?>" border="0" /></a></div>
			<div><?php echo htmlentities_utf8($this->profile['associations']); ?></div>
		</div>
		<?php else: ?>
		<p><a href="<?php echo url_rewrite(AT_SOCIAL_BASENAME.'edit_profile.php?add=associations'); ?>"><?php echo _AT('add_new_association'); ?></a></p>
		<?php endif; ?>
	</li>

	<li>
		<strong><?php echo _AT('awards'); ?></strong><br/>
		<?php if (!empty($this->profile['awards'])): ?>
		<div class="profile_container">
			<div class="top_right" style="border:thin #cccccc solid;"><a href="<?php echo url_rewrite(AT_SOCIAL_BASENAME.'edit_profile.php?edit=awards'.SEP.'id='.$row['id']); ?>"><img src="<?php echo $_base_href.AT_SOCIAL_BASENAME; ?>images/edit_profile.gif" alt="<?php echo _AT('edit'); ?>" title="<?php echo _AT('edit'); ?>" border="0" /></a>  <a href="<?php echo url_rewrite(AT_SOCIAL_BASENAME.'edit_profile.php?delete=awards'); ?>"><img src="<?php echo $_base_href.AT_SOCIAL_BASENAME; ?>images/b_drop.png" alt="<?php echo _AT('remove'); ?> ?>" title="<?php echo _AT('remove'); ?>" border="0" /></a></div>
			<div><?php echo htmlentities_utf8($this->profile['awards']); ?></div>
		</div>
		<?php else: ?>
		<p><a href="<?php echo url_rewrite(AT_SOCIAL_BASENAME.'edit_profile.php?add=awards'); ?>"><?php echo _AT('add_new_award'); ?></a></p>
		<?php endif; ?>
	</li>
</ul>
