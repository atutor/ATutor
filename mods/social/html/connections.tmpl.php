<script src="mods/social/lib/js/livesearch.js" type="text/javascript"></script>
<?php 
	//Add Friends Template
	//Generate a random number for the search input name fields, so that the browser will not remember any previous entries.
	$rand = md5(rand(0, time())); 
	if ($this->rand_key != ''){
		$last_search = $_POST['search_friends_'.$this->rand_key];
	} else {
		$last_search = $_POST['search_friends_'.$rand];	
	}
?>

<div class="input-form">
	<div class="row"><?php echo _AT('searchForFriends'); ?></div>
	<div class="row">
		<form action="<?php echo url_rewrite('mods/social/connections.php');?>" method="POST" id="search_friends_form">
			<label for="searchFriends"><?php echo _AT('search'); ?></label>
			<input type="text" size="60" name="search_friends_<?php echo $rand;?>" id="search_friends" value="<?php echo $_POST['search_friends_'.$last_search]; ?>" onkeyup="showResults(this.value, 'livesearch', 'mods/social/connections.php')"/>
			<label for ="myFriendsOnly"><?php echo _AT('myFriendsOnly'); ?></label>
			<?php 
			if (isset($_POST['myFriendsOnly'])){
				$mfo_checked = ' checked="checked"';
			}
			?>
			<input type="checkbox" name="myFriendsOnly" id="myFriendsOnly" value="yes" <?php echo $mfo_checked; ?>/>
			<input type="hidden" name="rand_key" value="<?php echo $rand; ?>"/>
			<input class="button" type="submit" name="search" value="<?php echo _AT('search'); ?>" />
			<div id="livesearch"></div>
		</form>
	</div>
</div>

<div class="">
<div class="box"><?php echo _AT('my_connections'); ?></div>
	<div class="box">
	<?php 
	if (!empty($this->friends)):
		$privacy_controller = new PrivacyController();
echo "<h2>There are ".sizeof($this->friends)." entries.</h2>";
		foreach ($this->friends as $id=>$person): 
			$privacy_obj = $privacy_controller->getPrivacyObject($id);
//			debug($privacy_obj->getSearch(), 'search'.$id);
			$relationship = $privacy_controller->getRelationship($id);

			if ((!isset($person['added']) || $person['added']!=1) && !PrivacyController::validatePrivacy(AT_SOCIAL_SEARCH_VISIBILITY, $relationship, $privacy_obj->getSearch())){
				//if this user doesn't want to be searched.
				continue;
			}
	?>
	<div class="contact_mini" >
		<?php if (isset($person['added']) && $person['added']==1): ?>
			<?php echo printSocialProfileImg($id); ?>
			<?php echo printSocialName($id); ?>
<!--		<a href="mods/social/remove_friend.php?id=<?php echo $id; ?>"><?php echo _AT('remove_friend'); ?></a>   -->
			<a style="vertical-align:top;" href="<?php echo url_rewrite('mods/social/index.php');?>?remove=yes<?php echo SEP;?>id=<?php echo $id;?>"><?php echo '[x]'; ?></a>
		<?php else: ?>
			<?php if (!isset($_POST['myFriendsOnly'])): ?>
			<?php echo printSocialProfileImg($id); ?>
			<?php echo printSocialName($id); ?>
			<a href="mods/social/connections.php?id=<?php echo $id; ?>"><?php echo _AT('add_to_friend'); ?></a> 
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
