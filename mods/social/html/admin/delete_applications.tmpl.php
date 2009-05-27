<form class="input-form" action="<?php echo AT_SOCIAL_BASENAME;?>admin/delete_applications.php" method="POST">
<div class="gadget_wrapper">
<div class="headingbox"><h3><?php echo _AT('available_applications'); ?></h3></div>
<?php	
	foreach ($this->all_apps as $id=>$app_obj): 
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
		<label for="app_<?php echo $id;?>"><?php echo _AT('delete');?></label>
		<input type="checkbox" id="app_<?php echo $id;?>" name="apps[]" value="<?php echo $id; ?>" />
	</div>
	<div style="width:60%; margin-left:10em; padding-top:1.5em;">
		<?php echo $app_obj->getDescription(); ?><br/><br/>
		<?php echo $app_obj->getUrl(); ?><br/>
	</div>
	<br/>
</div>
<?php endforeach; ?>
<div class="row" style="float: right;"><input class="button" type="submit" name="delete" value="<?php echo _AT('delete');?>"/></div>
</form>