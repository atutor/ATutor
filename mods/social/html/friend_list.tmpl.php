<div style="float:right; clear:right; margin-left:0.2em;width:39%;">
<?php require('notifications.tmpl.php'); ?>
</div>
<div style="float:right; clear:right; margin-left:0.2em;width:39%; ">
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
			<div style="float:right;"><a style="vertical-align:top;" href="<?php echo url_rewrite('mods/social/index.php');?>?remove=yes<?php echo SEP;?>id=<?php echo $id;?>"><img src="<?php echo $_base_href; ?>mods/social/images/b_drop.png" alt="<?php echo _AT('delete'); ?>" title="<?php echo _AT('delete'); ?>" border="0"/></a></div>
			<ul>
			<li><a href="mods/social/sprofile.php?id=<?php echo $id;?>"><?php echo printSocialProfileImg($id); ?></a><br /><?php echo printSocialName($id); ?></li>

			</ul>
		</div>
	<?php endforeach; ?>
	</div>
<?php else: ?>
<?php echo _AT('no_friends'); ?>
<?php endif; ?>
</div>
