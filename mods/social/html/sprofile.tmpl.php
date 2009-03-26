<?php
//Profile template for social
?>
<div class="">
	<span>
		<?php echo printSocialProfileImg($this->profile['member_id']); ?>	
		<h2><?php echo printSocialName($this->profile['member_id']); ?></h2>
		
		<?php if ($this->scope=='owner'): ?>
		<strong><a href=<?php echo url_rewrite("mods/social/edit_profile.php");?>><?php echo _AT('edit_profile'); ?></a></strong>
		<?php endif; ?>
	</span>
	
	<div class="">
		<div class="row" style="width:40%; float:left;" >
			<?php if (PrivacyController::validatePrivacy(AT_SOCIAL_PROFILE_PROFILE, $this->relationship, $this->prefs)): ?>
			<div>
			<h5><?php echo _AT('profile'); ?></h5>
			<dl id="public-profile">
				<dt><?php echo _AT('expertise'); ?></dt>
				<dd><?php echo $this->profile['expertise']; ?></dd>
				
				<dt><?php echo _AT('email'); ?></dt>
				<dd><?php echo $this->profile['email']; ?></dd>

				<dt><?php echo _AT('gender'); ?></dt>
				<dd><?php echo $this->profile['gender']; ?></dd>

				<dt><?php echo _AT('dob'); ?></dt>
				<dd><?php echo $this->profile['dob']; ?></dd>

				<dt><?php echo _AT('phone'); ?></dt>
				<dd><?php echo $this->profile['phone']; ?></dd>

				<dt><?php echo _AT('country'); ?></dt>
				<dd><?php echo $this->profile['country']; ?></dd>

				<dt><?php echo _AT('address'); ?></dt>
				<dd><?php echo $this->profile['postal']; ?></dd>

				<dt><?php echo _AT('interests'); ?></dt>
				<dd><?php echo $this->profile['interests']; ?></dd>

				<dt><?php echo _AT('associations'); ?></dt>
				<dd><?php echo $this->profile['associations']; ?></dd>

				<dt><?php echo _AT('awards'); ?></dt>
				<dd><?php echo $this->profile['awards']; ?></dd>

				<dt><?php echo _AT('others'); ?></dt>
				<dd><?php echo $this->profile['others']; ?></dd>
			</dl>
			</div>
			<?php endif; ?>

			<?php if (PrivacyController::validatePrivacy(AT_SOCIAL_PROFILE_EDUCATION, $this->relationship, $this->prefs)): ?>
			<div>
				<h5><?php echo _AT('training_and_education'); ?></h5>
				<?php if (empty($this->education)): ?>
				<div> N/A </div>
				<?php else: ?>
				<table class="data static">	
					<thead>
						<th>School/Institution</th>
						<th>Degree/Program/Course</th>
						<th>Year</th>
					</thead>
					<tbody>
					<?php
						foreach($this->education as $edu){
							echo '<tr><td>'.$edu['university'].'</td>';
							echo '<td>'.$edu['degree'].'/'.$edu['field'].'/'.$edu['field'].'</td>';
							echo '<td>'.$edu['from'].'-'.$edu['to'].'</td></tr>';
						}							
					?>
					</tbody>
				</table>
				<?php endif; ?>
			</div>
			<?php endif; ?>

			<?php if (PrivacyController::validatePrivacy(AT_SOCIAL_PROFILE_POSITION, $this->relationship, $this->prefs)): ?>
			<div>
				<h5><?php echo _AT('credits_and_work_experience'); ?></h5>
				<?php if (empty($this->position)): ?>
				<div> N/A </div>
				<?php else: ?>
				<table class="data static">	
					<thead>
						<th>Company</th>
						<th>Title</th>
						<th>Year</th>
					</thead>
					<tbody>
					<?php
						foreach($this->position as $pos){
							echo '<tr><td>'.$pos['company'].'</td>';
							echo '<td>'.$pos['title'].'</td>';
							echo '<td>'.$pos['from'].'-'.$pos['to'].'</td></tr>';
						}							
					?>
					</tbody>
				</table>
				<?php endif; ?>
			</div>
			<?php endif; ?>
		</div>		

		<div class="row" style="float:left;" >
			<?php if (PrivacyController::validatePrivacy(AT_SOCIAL_PROFILE_STATUS_UPDATE, $this->relationship, $this->prefs)): ?>
			<div class="" style="width:250;">
				<h5><?php echo _AT('activities'); ?></h5>
				<ul>
					<?php
						foreach($this->activities as $activity){
							if ($_SESSION['member_id']== $this->profile['member_id']){
								echo '<li>'._AT('you').' '.$activity.'</li>';
							} else {
								echo '<li>'.printSocialName($this->profile['member_id']).' '.$activity.'</li>';	
							}
						}							
					?>
				</ul>
			</div>
			<?php endif; ?>

			<?php if (PrivacyController::validatePrivacy(AT_SOCIAL_PROFILE_CONNECTION, $this->relationship, $this->prefs)): ?>
			<div class="" style="width:400px">
				<h5><?php echo _AT('connections'); ?></h5>
				<div style="float:left;">
				<?php foreach($this->friends as $friend_id): ?>														
					<div style="float:left; margin-left:1em;">
					<a href="mods/social/sprofile.php?id=<?php echo $friend_id;?>"><?php echo printSocialProfileImg($friend_id); ?></a>	<br/>
					<?php echo printSocialName($friend_id); ?>
					</div>
				<?php endforeach; ?>
				</div>
			</div>
			<?php endif; ?>
			
		</div>
	</div>
</div>