<div class="input-form">	
	<form action="<?php echo 'mods/social/groups/browse.php'; ?>" method="POST" >
		<div class="row">
			<span style="float:right"><a href="mods/social/groups/create.php">Create a new group</a></span>
			<label for="searchFriends"><?php echo _AT('search'); ?></label>
			<input type="text" size="60" name="searchFriends" id="searchFriends" value="<?php echo $_POST['searchFriends']; ?>"/>
			<input class="button" type="submit" name="search" value="<?php echo _AT('search'); ?>" />
			
		</div>
	</form>

	<div class="" style="width:50%;float:left;" >
		<div class="box"><?php echo _AT('recently_joined'); ?></div>
		<div class="box">
			sigh
		</div>
	</div>

	<div class="" style="width:50%;float:right;" >
		<div class="box"><?php echo _AT('my_groups'); ?></div>
		<div class="box">
			<?php 
			foreach ($this->my_groups as $i=>$grp):
				$id	= $grp->id;
			?>
			<div class="contact_mini">
				<ul>
				<li><a href="mods/social/groups/view.php?id=<?php echo $id;?>"><?php echo printSocialProfileImg($id); ?></a></li>
				<li><?php echo printSocialName($id); ?></li>
				</ul>
			</div>
			<?php endforeach; ?>
		</div>
	</div>
</div>