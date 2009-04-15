<?php
//Profile template for social
?>
	
<div class="headingbox">

	<h3><?php echo printSocialName($this->profile['member_id'], false); ?></h3>
</div>	
<div class="contentbox">
	<div style="float:right;margin-bottom:-15em;">
		<?php echo printSocialProfileImg($this->profile['member_id']); ?>
	</div>
	<?php if (PrivacyController::validatePrivacy(AT_SOCIAL_PROFILE_PROFILE, $this->relationship, $this->prefs)): ?>
	<div>
	<?php if ($this->scope=='owner'): ?>
	<div style="float:left; border:thin #cccccc solid;">
		<a href=<?php echo url_rewrite("mods/social/edit_profile.php");?>><img src="<?php echo $_base_href; ?>mods/social/images/edit_profile.gif" alt="<?php echo _AT('edit_profile'); ?>" title="<?php echo _AT('edit_profile'); ?>" border="0"/></a>
	</div><br />
	<?php endif; ?>



	<h4><?php echo _AT('social_profile'); ?></h4>
	<dl id="public-profile">
		<?php if($this->profile['occupation']){ ?>
		<dt><?php echo _AT('occupation'); ?></dt>
		<dd><?php echo $this->profile['occupation']; ?></dd>
		<?php }?>
		<?php if($this->profile['expertise']){ ?>
		<dt><?php echo _AT('expertise'); ?></dt>
		<dd><?php echo $this->profile['expertise']; ?></dd>
		<?php }?>
		<?php if ($this->relationship==AT_SOCIAL_FRIENDS_VISIBILITY || $this->relationship==AT_SOCIAL_OWNER_VISIBILITY): ?>
		<?php if($this->profile['email']): ?>
		<dt><?php echo _AT('email'); ?></dt>
		<dd><?php echo $this->profile['email']; ?></dd>
		<?php endif; ?>
		<?php endif; ?>
		<?php if($this->profile['gender']){ ?>
		<dt><?php echo _AT('gender'); ?></dt>
		<dd><?php echo $this->profile['gender']; ?></dd>
		<?php }?>
		<?php if($this->profile['dob']){ ?>
		<dt><?php echo _AT('dob'); ?></dt>
		<dd><?php echo $this->profile['dob']; ?></dd>
		<?php }?>
		<?php if($this->profile['phone']){ ?>
		<dt><?php echo _AT('phone'); ?></dt>
		<dd><?php echo $this->profile['phone']; ?></dd>
		<?php }?>
		<?php if($this->profile['country']){ ?>
		<dt><?php echo _AT('country'); ?></dt>
		<dd><?php echo $this->profile['country']; ?></dd>
		<?php }?>
		<?php if($this->profile['postal']){ ?>
		<dt><?php echo _AT('street_address'); ?></dt>
		<dd><?php echo $this->profile['postal']; ?></dd>
		<?php }?>
		<?php if($this->profile['interests']){ ?>
		<dt><?php echo _AT('interests'); ?></dt>
		<dd><?php echo $this->profile['interests']; ?></dd>
		<?php }?>
		<?php if($this->profile['associations']){ ?>
		<dt><?php echo _AT('associations'); ?></dt>
		<dd><?php echo $this->profile['associations']; ?></dd>
		<?php }?>
		<?php if($this->profile['awards']){ ?>
		<dt><?php echo _AT('awards'); ?></dt>
		<dd><?php echo $this->profile['awards']; ?></dd>
		<?php }?>
		<?php if($this->profile['others']){ ?>
		<dt><?php echo _AT('others'); ?></dt>
		<dd><?php echo $this->profile['others']; ?></dd>
		<?php }?>
	</dl>
	</div>
	<?php endif; ?>

	<?php if (PrivacyController::validatePrivacy(AT_SOCIAL_PROFILE_EDUCATION, $this->relationship, $this->prefs)): ?>
	<div>
		<?php if (!empty($this->education)){ ?>
		<h4><?php echo _AT('training_and_education'); ?></h4>
		<table class="data static">	
			<thead>
				<th> <?php echo _AT('institution'); ?></th>
				<th> <?php echo _AT('degrees'); ?></th>
				<th> <?php echo _AT('year'); ?></th>
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
		<?php } ?>
	</div>
	<?php endif; ?>

	<?php if (PrivacyController::validatePrivacy(AT_SOCIAL_PROFILE_POSITION, $this->relationship, $this->prefs)): ?>
	<div>
		<?php if (!empty($this->position)){ ?>
		<h4><?php echo _AT('credits_and_work_experience'); ?></h4>
		<table class="data static">	
			<thead>
				<th><?php echo _AT('company'); ?></th>
				<th><?php echo _AT('title'); ?></th>
				<th><?php echo _AT('year'); ?></th>
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
		<?php } ?>
	</div>
	<?php endif; ?>

	<?php if (PrivacyController::validatePrivacy(AT_SOCIAL_PROFILE_MEDIA, $this->relationship, $this->prefs)): ?>
	<div>
		<?php if (!empty($this->websites)){ ?>
		<h4><?php echo _AT('websites'); ?></h4>
		<table class="data static">	
			<thead>
				<th><?php echo _AT('site_name'); ?></th>
				<th><?php echo _AT('url'); ?></th>
			</thead>
			<tbody>
			<?php
				foreach($this->websites as $sites){
					echo '<tr><td>'.$sites['site_name'].'</td>';
					echo '<td><a href="'.$sites['url'].'">'.$sites['url'].'</a></td></tr>';
				}							
			?>
			</tbody>
		</table>
		<?php } ?>
	</div>
	<?php endif; ?>

	</div>	<br />	

	<?php if (PrivacyController::validatePrivacy(AT_SOCIAL_PROFILE_STATUS_UPDATE, $this->relationship, $this->prefs)): ?>
	<div class="headingbox" style="width:250;">
		<h4><?php echo _AT('activities'); ?></h4></div>
	<div class="contentbox" id="activity">
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
	</div><br />
	<?php endif; ?>

	<?php if (PrivacyController::validatePrivacy(AT_SOCIAL_PROFILE_CONNECTION, $this->relationship, $this->prefs)): ?>
	<div class="headingbox">
		<h5><?php echo _AT('connections'); ?></h5>
	</div>
	<div class="contentbox">
		<?php foreach($this->friends as $friend_id): ?>													
			<div style="float:left; margin-left:1em;">
			<?php echo printSocialProfileImg($friend_id); ?><br/>
			<?php echo printSocialName($friend_id); ?>
			</div>

		<?php endforeach; ?>
	</div>
	<?php endif; ?>
</div>