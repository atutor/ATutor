<?php
//Add Friends Template
?>
<div class="search_form">
	<div class="gadget_title_bar"><?php echo _AT('searchForFriends'); ?></div>
	<div class="row">
		<form action="<?php echo url_rewrite('/mods/social/add_friends.php');?>" method="POST" >
			<label for="searchFriends"><?php echo _AT('search'); ?></label>
			<input type="text" size="60" name="searchFriends" id="searchFriends" value="<?php echo $_POST['searchFriends']; ?>"/>
			<label for ="myFriendsOnly"><?php echo _AT('myFriendsOnly'); ?></label>
			<?php 
			if (isset($_POST['myFriendsOnly'])){
				$mfo_checked = ' checked="checked"';
			}
			?>
			<input type="checkbox" name="myFriendsOnly" id="myFriendsOnly" value="yes" <?php echo $mfo_checked; ?>/>
			<input class="button" type="submit" name="search" value="<?php echo _AT('search'); ?>" />
		</form>
	</div>
</div>

<div class="gadget_wrapper">
<div class="gadget_title_bar"><?php echo _AT('my_connections'); ?></div>
	<div class="gadget_container">
	<?php 
	if (!empty($this->friends)):
		$privacy_controller = new PrivacyController();
echo "<h2>There are ".sizeof($this->friends)." entries.</h2>";
		foreach ($this->friends as $id=>$person): 
			$privacy_obj = $privacy_controller->getPrivacyObject($id);
//			debug($privacy_obj->getSearch(), 'search');
			$relationship = $privacy_controller->getRelationship($id);
			if (!PrivacyController::validatePrivacy(AT_SOCIAL_SEARCH_VISIBILITY, $relationship, $privacy_obj->getSearch())){
				//if this user doesn't want to be searched.
				continue;
			}
	?>
	<div class="contact_mini" >
		<?php if (isset($person['added']) && $person['added']==1): ?>
			<?php echo printSocialProfileImg($id); ?>
			<?php echo printSocialName($id); ?>
<!--		<a href="mods/social/remove_friend.php?id=<?php echo $id; ?>"><?php echo _AT('remove_friend'); ?></a>   -->
		<?php else: ?>
			<?php if (!isset($_POST['myFriendsOnly'])): ?>
			<?php echo printSocialProfileImg($id); ?>
			<?php echo printSocialName($id); ?>
			<a href="mods/social/add_friends.php?id=<?php echo $id; ?>"><?php echo _AT('add_to_friend'); ?></a> 
			<?php endif; ?>
		<?php endif; ?>
	</div>
	<?php 
		endforeach; 
	endif;
	?>
	</div>
	<div style="float:right;">
		[-- TODO: Paginator --]
	</div>
</div>
</div>
