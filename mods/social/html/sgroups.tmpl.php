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
<div style="width:59%;float:left">
<?php include('tiny_sgroups.tmpl.php'); ?>
</div>
<div style="width:39%;float:right">
	<div class="input-form" style="padding:1em;min-height:4.5em;">
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
		</div>
	</div>

	<div class="headingbox"><h3><?php echo _AT('create_group'); ?></h3></div>
	<div class="contentbox">
		<p>blah^1000</p>
		<span><a href="mods/social/groups/create.php"><?php echo _AT('create_group'); ?></a></span><br />
	</div>
</div>