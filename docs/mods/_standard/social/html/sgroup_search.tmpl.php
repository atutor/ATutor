<script src="<?php echo AT_SOCIAL_BASENAME; ?>lib/js/livesearch.js" type="text/javascript"></script>
<?php 
	//Generate a random number for the search input name fields, so that the browser will not remember any previous entries.
	$rand = md5(rand(0, time())); 
	if ($this->rand_key != ''){
		$last_search = $_POST['search_groups_'.$this->rand_key];
	} else {
		$last_search = $_POST['search_groups_'.$rand];	
	}	
	$last_search = htmlentities($last_search, ENT_COMPAT, 'UTF-8');	//wants to convert only double quotes, not single.
?>
<div class="input-form" style="width:40%;padding:1em;min-height:4.5em;">
<h3><?php echo _AT('search_for_groups'); ?></h3>
	<form action="<?php echo AT_SOCIAL_BASENAME.'groups/search.php'; ?>" method="POST" id="search_group_form">
		<div class="row">
			<label for="search_groups"><?php echo _AT('search'); ?></label>
				<input type="text" size="60" name="search_groups_<?php echo $rand;?>" id="search_groups" value="<?php echo $last_search; ?>" onkeyup="showResults(this.value, 'livesearch', '<?php echo AT_SOCIAL_BASENAME; ?>groups/search.php')"/>
			<input type="hidden" name="rand_key" value="<?php echo $rand;?>"/>
			<input class="button" type="submit" name="search" value="<?php echo _AT('search'); ?>" />
			<span style="float:right"><a href="<?php echo AT_SOCIAL_BASENAME; ?>groups/create.php"><?php echo _AT('create_group'); ?></a></span><br />
			<div id="livesearch"></div>
		</div>
	</form>
</div>

	<div class="headingbox"><h3><?php echo _AT('search_results'); ?></h3></div>
	<div class="contentbox">
		<?php if (!empty($this->search_result)):
		foreach($this->search_result as $group_id=>$group_array): 
		$grp_obj = $group_array['obj'];
		?>
		<div class="box">
			<div style="float:left;">
			<?php echo $grp_obj->getLogo(); ?>
			
			</div>
			<div style="float:left; padding-left:0.5em;">
			<a href="<?php echo url_rewrite(AT_SOCIAL_BASENAME.'groups/view.php?id='.$grp_obj->getId());?>"><h4><?php echo $grp_obj->getName(); ?></h4></a><br/>
				<?php echo _AT('type') .': '. $grp_obj->getGroupType();?><br/>
				<?php echo _AT('description') .': <br/>'. $grp_obj->getDescription();?><br/>
			</div>
			<div style="clear:both;"></div>
		</div>
		<?php endforeach; 
		else: 
			echo _AT('none_found');
		endif;?>
	</div>

</div>