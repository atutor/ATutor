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
		<form action="<?php echo url_rewrite('mods/social/groups/list.php');?>" method="POST" id="search_friends_form">
			<label for="searchFriends" style="display:none;"><?php echo _AT('search'); ?></label>
			<input type="text" size="60" name="search_friends_<?php echo $rand;?>" id="search_friends" value="<?php echo $last_search; ?>" onkeyup="showResults(this.value, 'livesearch', 'mods/social/groups/list.php')"/>
			<input type="submit" name="search" value="<?php echo _AT('search'); ?>">
			<input type="hidden" name="rand_key" value="<?php echo $rand; ?>" />
			
			<div id="livesearch"></div>
		</form>
	</div>
</div>
<div class="" style="float:left; width:59%">
	<div class="headingbox"><h3><?php echo _AT('connections'); ?></h3></div>
	<div class="contentbox">
	<?php 
	if (!empty($this->grp_members)):
		echo "<h4>"._AT('there_are_entries', sizeof($this->grp_members))."</h4>";
		foreach ($this->grp_members as $id=>$person_obj): 
	?>
	<div class="contact_mini" >
		<?php if($_SESSION['member_id']==$this->grp_obj->getUser()): ?>
		<div style="float:right;"><a href="mods/social/groups/list.php?remove=yes<?php echo SEP;?>id=<?php echo $this->grp_obj->getID(); ?><?php echo SEP;?>member_id=<?php echo $person_obj->getID(); ?>"><img src="<?php echo $_base_href; ?>mods/social/images/b_drop.png" alt="<?php echo _AT('remove_group_member'); ?>" title="<?php echo _AT('remove_group_member'); ?>" border="0"/></a> </div>
		<?php endif; ?>
		<div>
			<div style="float:left;"><?php echo printSocialProfileImg($person_obj->getID()); ?></div>
			<div style="padding-left:0.5em; float:left;">
				<?php 
					$profile = $person_obj->getDetails();
					echo printSocialName($person_obj->getID()) . '<br/>';
					echo $profile['country'] . ' ' . $profile['province'] . '<br/>';
				?>
			</div>
			<div style="clear:both;"></div><br/>
		</div>
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

