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
<h3><?php echo _AT('search_for_groups'); ?></h3>
	<form action="<?php echo 'mods/social/groups/search.php'; ?>" method="POST" id="search_group_form">
		<div class="row">
			<label for="search_groups"><?php echo _AT('search'); ?></label>
				<input type="text" size="60" name="search_groups_<?php echo $rand;?>" id="search_groups" value="<?php echo $last_search; ?>" onkeyup="showResults(this.value, 'livesearch', 'mods/social/groups/search.php')"/>
			<input type="hidden" name="rand_key" value="<?php echo $rand;?>"/>
			<input class="button" type="submit" name="search" value="<?php echo _AT('search'); ?>" />
			<span style="float:right"><a href="mods/social/groups/create.php"><?php echo _AT('create_group'); ?></a></span><br />
			<div id="livesearch"></div>
		</div>
	</form>
</div>

	<div class="headingbox"><h3><?php echo _AT('search_results'); ?></h3></div>
	<div class="contentbox">
		<?php if (!empty($this->search_result)):
		foreach($this->search_result as $group_id=>$group_array): 
		$group_obj = $group_array['obj'];
		?>
		<div class="box">
			<dl id="public-profile">
				<dt><?php echo _AT('group_logo'); ?></dt>
				<dd><?php echo $group_obj->getLogo();?></dd>

				<dt><?php echo _AT('title'); ?></dt>
				<dd><a href="<?php echo url_rewrite('mods/social/groups/view.php?id='.$group_obj->getID()); ?>"><?php echo $group_obj->getName();?></a> 
				<?php if (in_array(new Member($_SESSION['member_id']), $group_obj->getGroupMembers())){
				echo '('._AT('group_joined').')';
				}
				?>
				</dd>

				<dt><?php echo _AT('group_type'); ?></dt>
				<dd><?php echo $group_obj->getGroupType();?></dd>

				<dt><?php echo _AT('size'); ?></dt>
				<dd><?php echo count($group_obj->getGroupMembers());?></dd>				
			</dl>
		</div>
		<?php endforeach; endif;?>
	</div>

</div>