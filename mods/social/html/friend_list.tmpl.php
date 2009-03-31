<?php require('notifications.tmpl.php'); ?>

<div style="float:right; margin-left:0.2em;width:39%;">
<div class="headingbox"><a href="mods/social/connections.php"><h3><?php echo _AT('connections'); ?></h3></a></div>
<?php
/**
 * Loop through all the friends and print out a list.  
 */
if (!empty($this->friends)): ?>
	<div class="contentbox">
	<?php foreach ($this->friends as $id=>$m_obj): 
		if (is_array($m_obj) && $m_obj['added']!=1){
			//skip over members that are not "my" friends
			continue;
		} ?>
		<div class="contact_mini">
			<ul>
			<li><a href="mods/social/sprofile.php?id=<?php echo $id;?>"><?php echo printSocialProfileImg($id); ?></a></li>
			<li><?php echo printSocialName($id); ?></li>
			<li><a style="vertical-align:top;" href="<?php echo url_rewrite('mods/social/index.php');?>?remove=yes<?php echo SEP;?>id=<?php echo $id;?>"><?php echo '[x]'; ?></a></li>
			</ul>
		</div>
	<?php endforeach; ?>
	</div>
<?php else: ?>
<?php echo _AT('no_friends'); ?>
<?php endif; ?>
</div>
