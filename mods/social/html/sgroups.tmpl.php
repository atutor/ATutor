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

<div class="input-form" style="width:40%;padding:1em;min-height:4.5em;">
	<div class="contentrow">
<h3><?php echo _AT('search_for_groups'); ?></h3>
	<form action="<?php echo 'mods/social/groups/search.php'; ?>" method="POST" id="search_group_form">
		<div class="row">

			<label for="search_groups"><?php echo _AT('search'); ?></label>
			<input type="text" size="60" name="search_groups_<?php echo $rand;?>" id="search_groups" value="<?php echo $last_search; ?>" onkeyup="showResults(this.value, 'livesearch', 'mods/social/groups/search.php')"/>
			<input type="hidden" name="rand_key" value="<?php echo $rand;?>"/>
			<input class="button" type="submit" name="search" value="<?php echo _AT('search'); ?>" />
			<div id="livesearch"></div>
		</div>
	</form>
			<span style="float:right"><a href="mods/social/groups/create.php"><?php echo _AT('create_group'); ?></a></span><br />
</div></div>

	<div class="" style="width:49%;float:left;" >
		<div class="headingbox"><h3><?php echo _AT('recently_joined'); ?></h3></div>
		<div class="contentbox">
			ToDO:
			Photoalbums
			Forums...etc
		</div>
	</div>

	<div class="" style="width:49%;float:right;" >
		<div class="headingbox"><h3><?php echo _AT('my_groups'); ?></h3></div>
		<div class="contentbox">
			<?php foreach ($this->my_groups as $i=>$grp): 
				$grp_obj = new SocialGroup($grp);
			?>
			<div class="contact_mini">
				<?php if ($grp_obj->getUser() == $_SESSION['member_id']): ?>
					<div style="float:right;margin:1em;"><a href="<?php echo $_base_href; ?>mods/social/groups/edit.php?id=<?php echo $grp;?>"><img src="<?php echo $_base_href; ?>mods/social/images/icon-settings.png" alt="<?php echo _AT('settings'); ?>" title="<?php echo _AT('settings'); ?>" border="0"/></a></div>
				<?php endif; ?>
				<?php if ($grp_obj->getUser() != $_SESSION['member_id']): ?>
					<div style="float:right;margin:1em;"><a href="<?php echo $_base_href; ?>mods/social/groups/view.php?id=<?php echo $grp.SEP;?>remove=1"><img src="<?php echo $_base_href; ?>mods/social/images/b_drop.png" alt="<?php echo _AT('delete'); ?>" title="<?php echo _AT('delete'); ?>" border="0"/></a></div>
				<?php endif; ?>



				<div class="box">
					<?php echo $grp_obj->getLogo(); ?>
					<a href="mods/social/groups/view.php?id=<?php echo $grp;?>"><h4><?php echo $grp_obj->getName(); ?></h4></a><br/>
					<?php echo _AT('type') .': '. $grp_obj->getGroupType();?><br/>
					<?php echo _AT('description') .': '. $grp_obj->getDescription();?><br/>
				</div><br />
			</div>
			<?php endforeach; ?>
		</div>
	</div>
</div>