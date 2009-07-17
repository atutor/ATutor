<!--  compressed with java -jar {$path}/yuicompressor-2.3.5.jar -o {$file}-min.js {$file}.js -->
<script type="text/javascript"
	src="<?php echo AT_SHINDIG_URL; ?>/gadgets/js/rpc.js?c=1"></script>
<script type="text/javascript" src="<?php echo AT_SOCIAL_BASENAME; ?>lib/js/jquery-1.3.2.js"></script>
<script type="text/javascript"
	src="http://ajax.googleapis.com/ajax/libs/prototype/1.6.0.2/prototype.js"></script>
<script type="text/javascript" src="mods/social/lib/js/container.js"></script>
<div class="search_form">	
		<div class="headingbox"><h3><?php echo _AT('add_application'); ?></h3></div>
		<div class="contentbox">
			<form method="POST" action="<?php echo url_rewrite(AT_SOCIAL_BASENAME."applications.php"); ?>">
			<label for="app_url"><?php echo _AT('add_application_url'); ?>: </label>
			<input id="app_url" name="app_url" type="text" />
			<input type="hidden" name="add_application" value="1" />
			<input type="submit" value="<?php echo _AT('add_application'); ?>" class="button" />
			</form>

			<form method="POST" action="<?php echo url_rewrite(AT_SOCIAL_BASENAME."applications.php"); ?>">
			<?php if (!isset($this->list_of_all_apps)): ?>
			<input type="hidden" name="show_applications" value="1" />
			<input type="submit" value="<?php echo _AT('show_available_applications'); ?>" class="button" />
			<?php else: ?>
			<input type="submit" value="<?php echo _AT('show_your_applications'); ?>" class="button" />	
			<?php endif; ?>
			</form>
			<button type="button"  onclick="poptastic('http://www.google.com/ig/directory?synd=open&source=gghp')" class="button" /><?php echo _AT('find_gadgets'); ?></button>
		</div>
</div>

<?php if (isset($this->list_of_all_apps) && !empty($this->list_of_all_apps)): ?>
<div class="gadget_wrapper">
<div class="headingbox"><h3><?php echo _AT('available_applications'); ?></h3></div>
<?php	
	foreach ($this->list_of_all_apps as $id=>$app_obj): 
	//skip the ones that are installed already
	if ($this->list_of_my_apps[$id]!=null){
		continue;
	}
	$author = ($app_obj->getAuthor()!='')?$app_obj->getAuthor():_AT('unknown'); 	
?>
<div class="contentbox" style="padding:1em;">	
	<div style="float:left; ">
		<?php echo $app_obj->getAppLink($app_obj->getTitle(), $id); ?><br/>
		<?php echo $app_obj->getAppLink('<img src="'.$app_obj->getThumbnail().'"/>', $id); ?><br/>
		<?php echo _AT('by'); ?> 
		<?php if ($app_obj->getAuthorEmail()!=''): ?>
			<a href="<?php echo $app_obj->getAuthorEmail(); ?>"><?php echo $author; ?></a>
		<?php else: echo $author; ?>
		<?php endif; ?>			
	</div>

	<div style="float: right;">
	<!--
		<a href="<?php echo  'mods/social/applications.php?app_id='.$id.SEP.'settings=1'; ?>"><img src="<?php echo $_base_href;?>mods/social/images/icon-settings.png" alt="<?php echo _AT('settings');?>" title="<?php echo _AT('settings');?>" border="0"/></a>
	-->
		<a href="<?php echo AT_SOCIAL_BASENAME.'applications.php?app_id='.$id.SEP.'add=1'; ?>"><img src="<?php echo $_base_href.AT_SOCIAL_BASENAME;?>images/plus_icon.gif" alt="<?php echo _AT('add_to_mygadgets');?>" title="<?php echo _AT('add_to_mygadgets');?>" border="0"/></a>
	</div>
	<div style="width:60%; margin-left:10em; padding-top:1.5em;">
		<?php echo $app_obj->getDescription(); ?><br/><br/>
		<?php echo $app_obj->getUrl(); ?><br/>
	</div>
	<br/>
</div>
<?php endforeach; ?>
</div>
<?php else: ?>

<div class="gadget_wrapper">
<div class="headingbox"><h3><?php echo _AT('your_applications'); ?></h3></div>
<?php	
	foreach ($this->list_of_my_apps as $id=>$app_obj): 
	$author = ($app_obj->getAuthor()!='')?$app_obj->getAuthor():_AT('unknown'); 	
?>
<div class="contentbox" style="padding:1em;">	
	<div style="float:left; ">
		<?php echo $app_obj->getAppLink($app_obj->getTitle(), $id); ?><br/>
		<?php echo $app_obj->getAppLink('<img src="'.$app_obj->getThumbnail().'"/>', $id); ?><br/>
		<?php echo _AT('by'); ?> 
		<?php if ($app_obj->getAuthorEmail()!=''): ?>
			<a href="<?php echo $app_obj->getAuthorEmail(); ?>"><?php echo $author; ?></a>
		<?php else: echo $author; ?>
		<?php endif; ?>			
	</div>

	<div style="float: right;">
		<a href="<?php echo AT_SOCIAL_BASENAME.'applications.php?app_id='.$id.SEP.'settings=1'; ?>"><img src="<?php echo $_base_href.AT_SOCIAL_BASENAME;?>images/icon-settings.png" alt="<?php echo _AT('settings');?>" title="<?php echo _AT('settings');?>" border="0"/></a>
	
		<a href="<?php echo AT_SOCIAL_BASENAME.'applications.php?app_id='.$id.SEP.'delete=1'; ?>"><img src="<?php echo $_base_href.AT_SOCIAL_BASENAME;?>images/b_drop.png" alt="<?php echo _AT('delete');?>" title="<?php echo _AT('delete');?>" border="0"/></a>
	</div>
	<div style="width:60%; margin-left:10em; padding-top:1.5em;">
		<?php echo $app_obj->getDescription(); ?><br/><br/>
		<?php echo $app_obj->getUrl(); ?><br/>
	</div>
	
</div>
<?php endforeach; ?>
</div>
<?php if(empty($id)){
echo '<div class="contentbox" style="padding:1em;">';
echo _AT('no_gadgets_installed');
echo '</div>';
}
?>
<?php endif; ?>
