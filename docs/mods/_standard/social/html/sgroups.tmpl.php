<script src="<?php echo AT_SOCIAL_BASENAME; ?>lib/js/livesearch.js" type="text/javascript"></script>
<?php 
	//Generate a random number for the search input name fields, so that the browser will not remember any previous entries.
	$rand = md5(rand(0, time())); 
	if ($this->rand_key != ''){
		$last_search = $_POST['search_groups_'.$this->rand_key];
	} else {
		$last_search = $_POST['search_groups_'.$rand];	
	}
?>
<div style="width:59%;float:left">
<?php include('tiny_sgroups.tmpl.php'); ?>
</div>
<div style="width:39%;float:right">
	<div class="input-form" style="padding:1em;min-height:4.5em;">
		<div class="contentrow">
			<h3><?php echo _AT('search_for_groups'); ?></h3>
			<form action="<?php echo AT_SOCIAL_BASENAME.'groups/search.php'; ?>" method="POST" id="search_group_form">
				<div class="row">
					<label for="search_groups"><?php echo _AT('search'); ?></label>
					<input type="text" size="60" name="search_groups_<?php echo $rand;?>" id="search_groups" value="<?php echo $last_search; ?>" onkeyup="showResults(this.value, 'livesearch', '<?php echo AT_SOCIAL_BASENAME; ?>groups/search.php')"/>
					<input type="hidden" name="rand_key" value="<?php echo $rand;?>"/>
					<input class="button" type="submit" name="search" value="<?php echo _AT('search'); ?>" />
					<div id="livesearch"></div>
				</div>
				<div class="row"><a href="<?php echo AT_SOCIAL_BASENAME.'groups/search.php?search_groups_'.$rand.'='.$last_search.SEP.'rand_key='.$rand; ?>"><?php echo _AT('browse_all');?></a></div>
			</form>		
		</div>		
	</div>
</div>
<div style="float:right;clear:right;width:39%;">
	<div class="input-form" style="padding:1em;min-height:4.5em;">
	<h3><?php echo _AT('create_group'); ?></h3>
		<p><?php echo _AT('create_group_blurb');  ?></p>
		<span><a href="<?php echo AT_SOCIAL_BASENAME; ?>groups/create.php"><?php echo _AT('create_group'); ?></a></span><br />
	</div>
</div>
</div>
