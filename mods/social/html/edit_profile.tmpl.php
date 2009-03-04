<h2><?php echo $this->profile['first_name'].' '.$this->profile['last_name']; ?></h2>

<ul>
	<li>
		<div>
		<strong><?php echo _AT('position'); ?></strong><br/>
		<?php 
		if (!empty($this->position)):
			//note: $id is just a array holder, it does not represent $row[id]
			foreach ($this->position as $id=>$row): ?>
		<div class="profile_container">
			<div class="top_right"><a href="<?php echo url_rewrite('mods/social/edit_profile.php?edit=position'.SEP.'id='.$row['id']); ?>"><?php echo _AT('edit'); ?></a> | <a href=""><?php echo _AT('remove'); ?></a></div>
			<div><?php echo _AT('company') . ': ' . $row['company']; ?></div>
			<div><?php echo _AT('title') . ': ' . $row['title']; ?></div>
			<div><?php echo _AT('range') . ': ' . $row['from'] . ' - ' . $row['to']; ?></div>
		</div>
		<?php
			endforeach;
		endif; ?>
			<p><a href=""><?php echo _AT('add_new_position'); ?></a></p>
		</div>
	</li>
	<li>
		<strong><?php echo _AT('education'); ?></strong><br/>
		<?php 	
		if (!empty($this->education)):
			foreach ($this->education as $id=>$row): ?>
		<div class="profile_container">
			<div class="top_right"><a href="<?php echo url_rewrite('mods/social/edit_profile.php?edit=education'.SEP.'id='.$row['id']); ?>"><?php echo _AT('edit'); ?></a> | <a href=""><?php echo _AT('remove'); ?></a></div>
			<div><?php echo _AT('university') . ': ' . $row['university']; ?></div>
			<div><?php echo _AT('location') . ': ' . $row['country'] . ', ' . $row['province']; ?></div>
			<div><?php echo _AT('degree') . ': ' . $row['degree']; ?></div>
			<div><?php echo _AT('field') . ': ' . $row['field']; ?></div>
			<div><?php echo _AT('description') . ': ' . $row['description']; ?></div>
			<div><?php echo _AT('range') . ': ' . $row['from'] . ' - ' . $row['to']; ?></div>
		</div>
		<?php 
			endforeach; 
		endif; ?>
			<p><a href=""><?php echo _AT('add_new_education'); ?></a></p>
	</li>
	<li>
		<strong><?php echo _AT('websites'); ?></strong><br/>
		<?php 	
		if (!empty($this->websites)):
			foreach ($this->websites as $id=>$row): ?>
		<div class="profile_container">
			<div class="top_right"><a href="<?php echo url_rewrite('mods/social/edit_profile.php?edit=websites'.SEP.'id='.$row['id']); ?>"><?php echo _AT('edit'); ?></a> | <a href=""><?php echo _AT('remove'); ?></a></div>
			<div><?php echo _AT('site_name') . ': ' . $row['site_name']; ?></div>
			<div><?php echo _AT('url') . ': ' . $row['url']; ?></div>
		</div>
		<?php 
			endforeach; 
		endif; ?>
		<p><a href=""><?php echo _AT('add_new_websites'); ?></a></p>
	</li>

	<li>
		<strong><?php echo _AT('interests'); ?></strong><br/>
		<?php if (!empty($this->profile['interests'])): ?>
		<div class="profile_container">
			<div><?php echo $this->profile['interests']; ?></div>
		</div>
		<?php else: ?>
		<p><a href=""><?php echo _AT('add_new_interest'); ?></a></p>
		<?php endif; ?>
	</li>

	<li>
		<strong><?php echo _AT('associations'); ?></strong><br/>
		<?php if (!empty($this->profile['associations'])): ?>
		<div class="profile_container">
			<div><?php echo $this->profile['associations']; ?></div>
		</div>
		<?php else: ?>
		<p><a href=""><?php echo _AT('add_new_association'); ?></a></p>
		<?php endif; ?>
	</li>

	<li>
		<strong><?php echo _AT('awards'); ?></strong><br/>
		<?php if (!empty($this->profile['awards'])): ?>
		<div class="profile_container">
			<div><?php echo $this->profile['awards']; ?></div>
		</div>
		<?php else: ?>
		<p><a href=""><?php echo _AT('add_new_award'); ?></a></p>
		<?php endif; ?>
	</li>
</ul>
