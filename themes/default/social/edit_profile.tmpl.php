<div class="social-wrapper">

<?php include(AT_SOCIAL_INCLUDE."profile_menu.inc.php")  ?>
<h2><?php echo $this->profile['first_name'].' '.$this->profile['last_name']; ?></h2>
<ul>
	<li>
		<div>
		<strong><?php echo _AT('position'); ?></strong>	<br/>
		<?php 
		if (!empty($this->position)):
			//note: $id is just a array holder, it does not represent $row[id]
			foreach ($this->position as $id=>$row): ?>
		<div class="profile_container">
			<div class="top_right" style="border:thin #cccccc solid;"><a href="<?php echo url_rewrite(AT_SOCIAL_BASENAME.'edit_profile.php?edit=position'.SEP.'id='.$row['id']); ?>"><img src="<?php echo $_base_href.AT_SOCIAL_BASENAME; ?>images/edit_profile.gif" alt="<?php echo _AT('edit'); ?>" title="<?php echo _AT('edit'); ?>" border="0" /></a>  <a href="<?php echo url_rewrite(AT_SOCIAL_BASENAME.'edit_profile.php?delete=position'.SEP.'id='.$row['id']); ?>"><img src="<?php echo $_base_href.AT_SOCIAL_BASENAME; ?>images/b_drop.png" alt="<?php echo _AT('remove'); ?>" title="<?php echo _AT('remove'); ?>" border="0" /></a></div>
			<div><?php echo _AT('company') . ': ' . AT_print($row['company'], 'social.company'); ?></div>
			<div><?php echo _AT('position') . ': ' . AT_print($row['title'], 'social.title'); ?></div>
			<div><?php echo _AT('from') . ': ' . AT_print($row['from'], 'social.from');?></div>
			<div><?php echo _AT('to') . ': ' . AT_print($row['to'], 'social.to'); ?></div>
			<div><?php echo _AT('description') . ': ' . AT_print($row['description'], 'social.description'); ?></div>
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
			<div><?php echo _AT('university') . ': ' . AT_print($row['university'], 'social.university'); ?></div>
			<div><?php echo _AT('location') . ': ' . AT_print($row['country'], 'social.country') . ', ' . AT_print($row['province'], 'social.province'); ?></div>
			<div><?php echo _AT('degree') . ': ' . AT_print($row['degree'], 'social.degree'); ?></div>
			<div><?php echo _AT('field') . ': ' . AT_print($row['field'], 'social.field'); ?></div>
			<div><?php echo _AT('from') . ': ' . AT_print($row['from'], 'social.from');?></div>
			<div><?php echo _AT('to') . ': ' . AT_print($row['to'], 'social.to'); ?></div>
			<div><?php echo _AT('description') . ': ' . AT_print($row['description'], 'social.description'); ?></div>
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
			<div><?php echo _AT('site_name') . ': ' . AT_print($row['site_name'], 'social.site_name'); ?></div>
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
			<div class="top_right" style="border:thin #cccccc solid;"><a href="<?php echo url_rewrite(AT_SOCIAL_BASENAME.'edit_profile.php?edit=interests'.SEP.'id='.$_SESSION['member_id']); ?>"><img src="<?php echo $_base_href.AT_SOCIAL_BASENAME; ?>images/edit_profile.gif" alt="<?php echo _AT('edit'); ?>" title="<?php echo _AT('edit'); ?>" border="0" /></a>  <a href="<?php echo url_rewrite(AT_SOCIAL_BASENAME.'edit_profile.php?delete=interests'); ?>"><img src="<?php echo $_base_href.AT_SOCIAL_BASENAME; ?>images/b_drop.png" alt="<?php echo _AT('remove'); ?> ?>" title="<?php echo _AT('remove'); ?>" border="0" /></a></div>
			<div><?php echo AT_print($this->profile['interests'], 'social.interests'); ?></div>
		</div>
		<?php else: ?>
		<p><a href="<?php echo url_rewrite(AT_SOCIAL_BASENAME.'edit_profile.php?add=interests'); ?>"><?php echo _AT('add_new_interest'); ?></a></p>
		<?php endif; ?>
	</li>

	<li>
		<strong><?php echo _AT('associations'); ?></strong><br/>
		<?php if (!empty($this->profile['associations'])): ?>
		<div class="profile_container">
			<div class="top_right" style="border:thin #cccccc solid;"><a href="<?php echo url_rewrite(AT_SOCIAL_BASENAME.'edit_profile.php?edit=associations'.SEP.'id='.$_SESSION['member_id']); ?>"><img src="<?php echo $_base_href.AT_SOCIAL_BASENAME; ?>images/edit_profile.gif" alt="<?php echo _AT('edit'); ?>" title="<?php echo _AT('edit'); ?>" border="0" /></a>  <a href="<?php echo url_rewrite(AT_SOCIAL_BASENAME.'edit_profile.php?delete=associations'); ?>"><img src="<?php echo $_base_href.AT_SOCIAL_BASENAME; ?>images/b_drop.png" alt="<?php echo _AT('remove'); ?> ?>" title="<?php echo _AT('remove'); ?>" border="0" /></a></div>
			<div><?php echo AT_print($this->profile['associations'], 'social.associations'); ?></div>
		</div>
		<?php else: ?>
		<p><a href="<?php echo url_rewrite(AT_SOCIAL_BASENAME.'edit_profile.php?add=associations'); ?>"><?php echo _AT('add_new_association'); ?></a></p>
		<?php endif; ?>
	</li>

	<li>
		<strong><?php echo _AT('awards'); ?></strong><br/>
		<?php if (!empty($this->profile['awards'])): ?>
		<div class="profile_container">
			<div class="top_right" style="border:thin #cccccc solid;"><a href="<?php echo url_rewrite(AT_SOCIAL_BASENAME.'edit_profile.php?edit=awards'.SEP.'id='.$_SESSION['member_id']); ?>"><img src="<?php echo $_base_href.AT_SOCIAL_BASENAME; ?>images/edit_profile.gif" alt="<?php echo _AT('edit'); ?>" title="<?php echo _AT('edit'); ?>" border="0" /></a>  <a href="<?php echo url_rewrite(AT_SOCIAL_BASENAME.'edit_profile.php?delete=awards'); ?>"><img src="<?php echo $_base_href.AT_SOCIAL_BASENAME; ?>images/b_drop.png" alt="<?php echo _AT('remove'); ?> ?>" title="<?php echo _AT('remove'); ?>" border="0" /></a></div>
			<div><?php echo AT_print($this->profile['awards'], 'social.awards'); ?></div>
		</div>
		<?php else: ?>
		<p><a href="<?php echo url_rewrite(AT_SOCIAL_BASENAME.'edit_profile.php?add=awards'); ?>"><?php echo _AT('add_new_award'); ?></a></p>
		<?php endif; ?>
	</li>
	<li>
		<strong><?php echo _AT('representation'); ?></strong><br/>
		<?php if (!empty($this->representation)): ?>
		<div class="profile_container">
			<div class="top_right" style="border:thin #cccccc solid;">
			<?php foreach($this->representation as $row=>$value){  ?>
			<a href="<?php echo url_rewrite(AT_SOCIAL_BASENAME.'edit_profile.php?edit=representation'.SEP.'id='.$value['rep_id']); ?>"><img src="<?php echo $_base_href.AT_SOCIAL_BASENAME; ?>images/edit_profile.gif" alt="<?php echo _AT('edit'); ?>" title="<?php echo _AT('edit'); ?>" border="0" /></a>  <a href="<?php echo url_rewrite(AT_SOCIAL_BASENAME.'edit_profile.php?delete=representation'); ?>"><img src="<?php echo $_base_href.AT_SOCIAL_BASENAME; ?>images/b_drop.png" alt="<?php echo _AT('remove'); ?> ?>" title="<?php echo _AT('remove'); ?>" border="0" /></a></div>

				<dl class="public-profile">
				<dt><?php echo _AT('name') . ':' ?></dt> <dd>  &nbsp;<?php echo AT_print($value['rep_name'], 'social.representation_name'); ?></dd>
				<dt><?php echo _AT('title') . ': ' ?></dt> <dd>  &nbsp;<?php echo AT_print($value['rep_title'], 'social.representation_title'); ?></dd>
				<dt><?php echo _AT('phone') . ':' ?></dt> <dd>  &nbsp;<?php echo AT_print($value['rep_phone'], 'social.representation_phone');?></dd>
				<dt><?php echo _AT('email') . ': ' ?></dt> <dd>  &nbsp;<?php echo  AT_print($value['rep_email'], 'social.representation_email'); ?></dd>
				<dt><?php echo _AT('street_address') . ': ' ?></dt> <dd>  &nbsp;<?php echo AT_print($value['rep_address'], 'social.representation_address'); ?></dd>
				</dl>
			<?php } ?>
		</div>
		<?php else: ?>
		<p><a href="<?php echo url_rewrite(AT_SOCIAL_BASENAME.'edit_profile.php?add=representation'); ?>"><?php echo _AT('add_new_representation'); ?></a></p>
		<?php endif; ?>

	</li>
	<li>
		<strong><?php echo _AT('alt_contact'); ?></strong><br/>
		<?php if (!empty($this->contact)): ?>
		<div class="profile_container">
			<div class="top_right" style="border:thin #cccccc solid;">
			<?php foreach($this->contact as $row=>$value){  ?>

			<a href="<?php echo url_rewrite(AT_SOCIAL_BASENAME.'edit_profile.php?edit=contact'.SEP.'id='.$value['contact_id']); ?>"><img src="<?php echo $_base_href.AT_SOCIAL_BASENAME; ?>images/edit_profile.gif" alt="<?php echo _AT('edit'); ?>" title="<?php echo _AT('edit'); ?>" border="0" /></a>  <a href="<?php echo url_rewrite(AT_SOCIAL_BASENAME.'edit_profile.php?delete=contact'); ?>"><img src="<?php echo $_base_href.AT_SOCIAL_BASENAME; ?>images/b_drop.png" alt="<?php echo _AT('remove'); ?> ?>" title="<?php echo _AT('remove'); ?>" border="0" /></a></div>

				<dl class="public-profile">
				<dt><?php echo _AT('name') . ':' ?></dt> <dd>  &nbsp;<?php echo AT_print($value['con_name'], 'social.contact_name'); ?></dd>
				<dt><?php echo _AT('phone') . ':' ?></dt> <dd> &nbsp; <?php echo AT_print($value['con_phone'], 'social.contact_phone');?></dd>
				<dt><?php echo _AT('email') . ': ' ?></dt> <dd> &nbsp; <?php echo  AT_print($value['con_email'], 'social.contact_email'); ?></dd>
				<dt><?php echo _AT('street_address') . ': ' ?></dt> <dd> &nbsp; <?php echo AT_print($value['con_address'], 'social.contact_address'); ?></dd>
				</dl>
			<?php } ?>
		</div>
		<?php else: ?>
		<p><a href="<?php echo url_rewrite(AT_SOCIAL_BASENAME.'edit_profile.php?add=contact'); ?>"><?php echo _AT('add_new_contact'); ?></a></p>
		<?php endif; ?>
	</li>
	<li>
		<strong><?php echo _AT('personal'); ?></strong><br/>
		<?php if (!empty($this->personal)): ?>
		<div class="profile_container">
			<div class="top_right" style="border:thin #cccccc solid;">
			<a href="<?php echo url_rewrite(AT_SOCIAL_BASENAME.'edit_profile.php?edit=personal'.SEP.'id='.$this->personal['per_id']); ?>"><img src="<?php echo $_base_href.AT_SOCIAL_BASENAME; ?>images/edit_profile.gif" alt="<?php echo _AT('edit'); ?>" title="<?php echo _AT('edit'); ?>" border="0" /></a>  <a href="<?php echo url_rewrite(AT_SOCIAL_BASENAME.'edit_profile.php?delete=personal'); ?>"><img src="<?php echo $_base_href.AT_SOCIAL_BASENAME; ?>images/b_drop.png" alt="<?php echo _AT('remove'); ?> ?>" title="<?php echo _AT('remove'); ?>" border="0" /></a></div>

				<dl class="public-profile">
				<dt><?php echo _AT('per_weight') . ':' ?></dt> <dd> &nbsp;<?php echo AT_print($this->personal['per_weight'], 'social.personal_name');?></dd>
				<dt><?php echo _AT('per_height') . ': ' ?></dt> <dd> &nbsp; <?php echo  AT_print($this->personal['per_height'], 'social.personal_height'); ?></dd>
				<dt><?php echo _AT('per_hair') . ': ' ?></dt> <dd> &nbsp; <?php echo AT_print($this->personal['per_hair'], 'social.personal_hair'); ?></dd>
				<dt><?php echo _AT('per_eyes') . ':' ?></dt> <dd> &nbsp; <?php echo AT_print($this->personal['per_eyes'], 'social.personal_eyes');?></dd>
				<dt><?php echo _AT('per_ethnicity') . ': ' ?></dt> <dd> &nbsp; <?php echo AT_print($this->personal['per_ethnicity'], 'social.personal_ethnicity'); ?></dd>
				<dt><?php echo _AT('per_languages') . ': ' ?></dt> <dd> &nbsp; <?php echo AT_print($this->personal['per_languages'], 'social.personal_languages'); ?></dd>
				<dt><?php echo _AT('per_disabilities') . ': ' ?></dt> <dd>  &nbsp;<?php echo AT_print($this->personal['per_disabilities'], 'social.personal_disabilities'); ?></dd>
				</dl>
		</div>
		<?php else: ?>
		<p><a href="<?php echo url_rewrite(AT_SOCIAL_BASENAME.'edit_profile.php?add=personal'); ?>"><?php echo _AT('add_new_personal'); ?></a></p>
		<?php endif; ?>
	</li>


</ul>
<div style="clear:both;"></div>
</div>