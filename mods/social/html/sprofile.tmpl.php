<?php
//Profile template for social
?>

<div>
	<div><h2><?php echo printSocialName($this->profile['member_id'], false); ?></h2></div>
	<div style="float:left; width:40%;">		
		<div class="headingbox">
			<h5><?php echo _AT('profile'); ?></h5>
		</div>
		<div class="contentbox">
		<?php if ($this->scope=='owner'): ?>
		<div style="float:right; border:thin #cccccc solid;">
			<a href=<?php echo url_rewrite(AT_SOCIAL_BASENAME."edit_profile.php");?>><img src="<?php echo $_base_href.AT_SOCIAL_BASENAME;?>images/edit_profile.gif" alt="<?php echo _AT('edit_profile'); ?>" title="<?php echo _AT('edit_profile'); ?>" border="0"/></a>
		</div>		
		<?php endif; ?>
		<?php echo printSocialProfileImg($this->profile['member_id']); ?>
		<dl>
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
	</div>

	<div style="float:left; width:59%;">	
		<?php if (PrivacyController::validatePrivacy(AT_SOCIAL_PROFILE_EDUCATION, $this->relationship, $this->prefs)): ?>
			<?php if (!empty($this->education)){ ?>
			<div>
				<div class="headingbox"><h5><?php echo _AT('training_and_education'); ?></h5></div>
				<div class="contentbox">
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
				</div>
			</div><br/>
			<?php } ?>
		<?php endif; ?>

		<?php if (PrivacyController::validatePrivacy(AT_SOCIAL_PROFILE_POSITION, $this->relationship, $this->prefs)): ?>
		
			<?php if (!empty($this->position)){ ?>
			<div>
				<div class="headingbox"><h5><?php echo _AT('credits_and_work_experience'); ?></h5></div>
				<div class="contentbox">
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
				</table></div>
			</div><br/>
			<?php } ?>		
		<?php endif; ?>

		<?php if (PrivacyController::validatePrivacy(AT_SOCIAL_PROFILE_MEDIA, $this->relationship, $this->prefs)): ?>
		<div>
			<?php if (!empty($this->websites)): ?>
			<div class="headingbox"><h5><?php echo _AT('websites'); ?></h5></div>
			<div class="contentbox">
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
			</div><br/>
			<?php endif; ?>
		</div>
		<?php endif; ?>

		<?php if (PrivacyController::validatePrivacy(AT_SOCIAL_PROFILE_STATUS_UPDATE, $this->relationship, $this->prefs)): ?>
		<div class="headingbox">
			<h5><?php echo _AT('activities'); ?></h5></div>
		<div class="contentbox" id="activity">
			<ul>
				<?php
					foreach($this->activities as $id=>$activity){
						if ($_SESSION['member_id']== $this->profile['member_id']){
							echo '<li>'._AT('you');
							echo ' '.$activity.' ';
							echo '<a href="'.url_rewrite(AT_SOCIAL_BASENAME.'sprofile.php?delete='.$id).'"><img src="'.$_base_href.'mods/social/images/b_drop.png" alt="'._AT('remove').'" title="'._AT('remove').'" border="0" /></a></li>';
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
			<?php if (sizeof($this->friends)>0):
					foreach($this->friends as $friend_id): ?>													
				<div style="float:left; margin-left:1em;">
				<?php echo printSocialProfileImg($friend_id); ?><br/>
				<?php echo printSocialName($friend_id); ?>
				</div>
			<?php 	endforeach;
				else: 
					echo _AT('no_friends');
				endif; ?>
		</div><br/>

		<?php if (isset($this->mutual_friends)): ?>
		<div class="headingbox">
			<h5><?php echo _AT('mutual_connections'); ?></h5>
		</div>
		<div class="contentbox">
			<?php foreach($this->mutual_friends as $friend_id): ?>
				<div style="float:left; margin-left:1em;">
				<?php echo printSocialProfileImg($friend_id); ?><br/>
				<?php echo printSocialName($friend_id); ?>
				</div>
			<?php endforeach; ?>
		</div>
		<?php endif; //this->mutual_friends != empty ?>
		<?php endif; ?>
	</div>
</div>

