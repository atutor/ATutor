<div class="search_form">	
	<form action="<?php echo 'mods/social/add_friends.php'; ?>" method="POST" >
		<div class="gadget_title_bar"><?php echo _AT('searchForFriends'); ?></div>
		<div class="row">
			<label for="searchFriends"><?php echo _AT('search'); ?></label>
			<input type="text" size="60" name="searchFriends" id="searchFriends" value="<?php echo $_POST['searchFriends']; ?>"/>
			<label for ="myFriendsOnly"><?php echo _AT('myFriendsOnly'); ?></label><input type="checkbox" name="myFriendsOnly" id="myFriendsOnly" value="yes"/>
		</div>
		<div class="row">
			<input class="button" type="submit" name="search" value="<?php echo _AT('search'); ?>" />
		</div>
	</form>
	</div>
</div>

<div class="gadget_wrapper" style="float:left;" >
	<div class="gadget_title_bar"><?php echo _AT('connection'); ?></div>
	<div class="gadget_container">
		<?php 
		foreach ($this->friends as $i=>$obj):
			$id	= $obj->id;
			//check for search preference
			$profile = $obj->profile;
		?>
		<div class="contact_mini">
			<ul>
			<li><a href="mods/social/sprofile.php?id=<?php echo $id;?>"><?php echo printSocialProfileImg($id); ?></a></li>
			<li><?php echo printSocialName($id); ?></li>
			<li><a href="<?php echo url_rewrite('mods/social/index.php');?>?remove=yes<?php echo SEP;?>id=<?php echo $id;?>"><?php echo _AT('remove'); ?></a></li>
			</ul>
		</div>
		<?php endforeach; ?>
	</div>
</div>
