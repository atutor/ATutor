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

<div class="input-form" style="float:right; width:34%;padding:1em;min-height:4.5em;">
	<div class="contentrow">
		<h3><?php echo _AT('search_for_friends'); ?></h3>
		<form action="<?php echo url_rewrite('mods/social/connections.php');?>" method="POST" id="search_friends_form">
			<label for="searchFriends" style="display:none;"><?php echo _AT('search'); ?></label>
			<input type="text" size="60" name="search_friends_<?php echo $rand;?>" id="search_friends" value="<?php echo $last_search; ?>" onkeyup="showResults(this.value, 'livesearch', 'mods/social/connections.php')"/>
			<input type="submit" name="search" value="<?php echo _AT('search'); ?>">
			<?php 
			if (isset($_POST['myFriendsOnly'])){
				$mfo_checked = ' checked="checked"';
			}
			?>
			<br/> <div style="float:right;"><input type="checkbox" name="myFriendsOnly" id="myFriendsOnly" value="<?php echo _AT('yes'); ?>" <?php echo $mfo_checked; ?> />
			<label for ="myFriendsOnly"><?php echo _AT('my_friends_only'); ?></label></div>
			<input type="hidden" name="rand_key" value="<?php echo $rand; ?>" />
			
			<div id="livesearch"></div>
		</form>
	</div>
</div>
<div class="" style="float:left; width:59%">
	<div class="headingbox"><h3><?php echo _AT('connections'); ?></h3></div>
	<div class="contentbox">
	<?php 
	if (!empty($this->friends)):
		$privacy_controller = new PrivacyController();
		echo "<h4>"._AT('there_are_entries', sizeof($this->friends))."</h4>";
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
			<a style="vertical-align:top;float:right;" href="<?php echo url_rewrite('mods/social/index.php');?>?remove=yes<?php echo SEP;?>id=<?php echo $id;?>"><img src="<?php echo $_base_href; ?>mods/social/images/b_drop.png" alt="<?php echo _AT('delete'); ?>" title="<?php echo _AT('delete'); ?>" border="0"/></a>
			<div>
				<div style="float:left;"><?php echo printSocialProfileImg($id); ?></div>
				<div style="padding-left:0.5em; float:left;">
					<?php 
						$member_obj = new Member($id);
						$profile = $member_obj->getDetails();
						echo printSocialName($id) . '<br/>';
						echo $profile['country'] . ' ' . $profile['province'] . '<br/>';
					?>
				</div>
				<div style="clear:both;"></div><br/>
			</div>
			
		<?php else: ?>
			<?php if (!isset($_POST['myFriendsOnly'])): ?>
			<div style="float:right;"><a href="mods/social/connections.php?id=<?php echo $id; ?>"><img src="<?php echo $_base_href; ?>mods/social/images/plus_icon.gif" alt="<?php echo _AT('add_to_friends'); ?>" title="<?php echo _AT('add_to_friends'); ?>" border="0"/></a> </div>
			<div>
				<div style="float:left;"><?php echo printSocialProfileImg($id); ?></div>
				<div style="padding-left:0.5em; float:left;">
					<?php 
						$member_obj = new Member($id);
						$profile = $member_obj->getDetails();
						echo printSocialName($id) . '<br/>';
						echo $profile['country'] . ' ' . $profile['province'] . '<br/>';
					?>
				</div>
				<div style="clear:both;"></div><br/>
			</div>

			<?php endif; ?>
		<?php endif; ?>
	</div>
	<?php 
		endforeach; 
	endif;
	?>
	</div>
	<!--
	<div style="float:right;">
		[-- TODO: Paginator --]
	</div>
	-->
</div>

