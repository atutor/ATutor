<div style="float:right;width:100%;">
<?php require('notifications.tmpl.php'); ?>
</div>
<div style="float:right;min-width:100%;">
	<div class="headingbox">
	<a href="<?php echo AT_SOCIAL_BASENAME; ?>connections.php"><h3><?php echo _AT('connections'); ?></h3></a></div>
	<div class="contentbox">
	<?php
	/**
	 * Loop through all the friends and print out a list.  
	 */
	if (!empty($this->friends)): ?>
		<?php foreach ($this->friends as $id=>$m_obj): 
			if (is_array($m_obj) && $m_obj['added']!=1){
				//skip over members that are not "my" friends
				continue;
			} ?>
			<div style="width:100%;">
				<!-- don't want delete on the front page
				<div style="float:right;">
					<a style="vertical-align:top;" href="<?php echo url_rewrite('mods/social/index.php');?>?remove=yes<?php echo SEP;?>id=<?php echo $id;?>"><img src="<?php echo $_base_href; ?>mods/social/images/b_drop.png" alt="<?php echo _AT('delete'); ?>" title="<?php echo _AT('delete'); ?>" border="0"/></a>
				</div>
				-->
				<div style="width:110px; float:left; padding-bottom:0.2em;">
					<?php echo printSocialProfileImg($id); ?><br />
					<?php echo printSocialName($id); ?>
				</div>
			</div>
		<?php endforeach; ?>
		<div style="clear:both;"><a href="<?php echo AT_SOCIAL_BASENAME; ?>connections.php"><?php echo _AT('show_all');?></a></div>
	<?php else: ?>
	<?php echo _AT('no_friends'); ?>
	<?php endif; ?>
	</div>
</div>
