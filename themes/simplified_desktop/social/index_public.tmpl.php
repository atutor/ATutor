<script src="<?php echo AT_SOCIAL_BASENAME; ?>lib/js/livesearch.js" type="text/javascript"></script>
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
<?php print_paginator($this->page, $this->num_pages, 'search_friends='.$this->search_field, 1);  ?>

<div class="input-form">
	<div class="contentrow">
		<h3><?php echo _AT('search_for_friends'); ?></h3>
		<form action="<?php echo url_rewrite(AT_SOCIAL_BASENAME.'index_public.php');?>" method="POST" id="search_friends_form">
			<label for="searchFriends" style="display:none;"><?php echo _AT('search'); ?></label>
			<input type="text" size="40" name="search_friends_<?php echo $rand;?>" id="search_friends" value="<?php echo $last_search; ?>" onkeyup="showResults(this.value, 'livesearch', '<?php echo AT_SOCIAL_BASENAME; ?>index_public.php')"/>
			<input type="submit" name="search" value="<?php echo _AT('search'); ?>">
			<input type="hidden" name="rand_key" value="<?php echo $rand; ?>" />
			
			<div id="livesearch"></div>
		</form>
	</div>
</div>
<div>
	<div class="headingbox"><h3><?php echo _AT('search_results'); ?></h3></div>
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
		<div>
			<div ><?php echo printSocialProfileImg($id); ?></div>
			<div>
				<?php 
					$member_obj = new Member($id);
					$profile = $member_obj->getAddress();
					echo printSocialName($id) . '<br/>';
					echo $profile['country'] . ' ' . $profile['province'] . '<br/>';
				?>
			</div>
			<div style="clear:both;"></div><br/>
		</div>	
	</div>
	<?php 
		endforeach; 
		else: 
			echo _AT('none_found');
	endif;
	?>
	</div>
	<!--
	<div style="float:right;">
		[-- TODO: Paginator --]
	</div>
	-->
</div>
</div>
<div style="clear:both;"></div>
<?php print_paginator($this->page, $this->num_pages, 'search_friends='.$this->search_field, 1);  ?>
