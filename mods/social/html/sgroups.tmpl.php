<script src="mods/social/lib/js/livesearch.js" type="text/javascript"></script>
<?php 
	//Generate a random number for the search input name fields, so that the browser will not remember any previous entries.
	$rand = md5(rand(0, time())); 
	if ($this->rand_key != ''){
		$last_search = $_POST['search_groups_'.$this->rand_key];
	} else {
		$last_search = $_POST['search_groups_'.$rand];	
	}
?>

<div class="input-form">	
	<form action="<?php echo 'mods/social/groups/search.php'; ?>" method="POST" id="search_group_form">
		<div class="row">
			<span style="float:right"><a href="mods/social/groups/create.php">Create a new group</a></span>
			<label for="search_groups"><?php echo _AT('search'); ?></label>
			<input type="text" size="60" name="search_groups_<?php echo $rand;?>" id="search_groups" value="<?php echo $last_search; ?>" onkeyup="showResults(this.value, 'livesearch', 'mods/social/groups/search.php')"/>
			<input type="hidden" name="rand_key" value="<?php echo $rand;?>"/>
			<input class="button" type="submit" name="search" value="<?php echo _AT('search'); ?>" />
			<div id="livesearch"></div>
		</div>
	</form>


	<div class="" style="width:50%;float:left;" >
		<div class="box"><?php echo _AT('recently_joined'); ?></div>
		<div class="box">
			ToDO:
			Photoalbums
			Forums...etc
		</div>
	</div>

	<div class="" style="width:50%;float:right;" >
		<div class="box"><?php echo _AT('my_groups'); ?></div>
		<div class="box">
			<?php foreach ($this->my_groups as $i=>$grp): 
				$grp_obj = new SocialGroup($grp);
			?>
			<div class="contact_mini">
				<div class="box">
					<a href="mods/social/groups/view.php?id=<?php echo $grp;?>"><?php echo $grp_obj->getName(); ?></a><br/>
					<?php echo _AT('type') .': '. $grp_obj->getGroupType();?><br/>
					<?php echo _AT('description') .': '. $grp_obj->getDescription();?><br/>
				</div>
			</div>
			<?php endforeach; ?>
		</div>
	</div>
</div>