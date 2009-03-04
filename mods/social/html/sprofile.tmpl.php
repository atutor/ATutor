<?php
//Profile template for social
?>
<div class="">
	<?php if (PrivacyController::validatePrivacy(AT_SOCIAL_PROFILE_PROFILE, $this->relationship, $this->prefs)): ?>
	<div class="">
		<h2 class="page-title"><?php echo printSocialName($this->profile['member_id']); ?></h2>
		<?php if ($this->scope=='owner'): ?>
		<b><a href=<?php echo url_rewrite("mods/social/edit_profile.php");?>><?php echo _AT('edit_profile'); ?></a></b>
		<?php endif; ?>
	</div>

	<div class="">
		<div class="" style="width:40%; float:left;" >
			<dl>
				<dt><?php echo _AT('picture'); ?></dt>
				<dd><?php echo printSocialProfileImg($this->profile['member_id']); ?></dd>

				<dt><?php echo _AT('email'); ?></dt>
				<dd><?php echo $this->profile['email']; ?></dd>

				<dt><?php echo _AT('gender'); ?></dt>
				<dd><?php echo $this->profile['gender']; ?></dd>

				<dt><?php echo _AT('dob'); ?></dt>
				<dd><?php echo $this->profile['dob']; ?></dd>

				<dt><?php echo _AT('status'); ?></dt>
				<dd><?php echo $this->profile['status']; ?></dd>

				<dt><?php echo _AT('phone'); ?></dt>
				<dd><?php echo $this->profile['phone']; ?></dd>

				<dt><?php echo _AT('country'); ?></dt>
				<dd><?php echo $this->profile['country']; ?></dd>
			</dl>
		</div>
		<?php endif; ?>

		<div class="" style="width:40%; float:right;" >
			<?php if (PrivacyController::validatePrivacy(AT_SOCIAL_PROFILE_CONNECTION, $this->relationship, $this->prefs)): ?>
			<div class="" style="width:250;" >
				<h5><?php echo _AT('connections'); ?></h5>
				<ul>
					<?php
						foreach($this->friends as $friend){
							//echo '<li><a href="mods/social/sprofile.php?id='.$friend->id.'"><img src="get_profile_img.php?id='.$friend->id.'" alt="Profile Picture" /></a></li>';
							echo '<li><a href="'.url_rewrite('mods/social/sprofile.php?id='.$friend->id).'">'.printSocialName($friend->id).'</a></li>';
						}							
					?>
				</ul>
			</div>
			<?php endif; ?>

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
		</div>
	</div>
</div>