<!--  compressed with java -jar {$path}/yuicompressor-2.3.5.jar -o {$file}-min.js {$file}.js -->
<script type="text/javascript"
	src="<?php echo AT_SHINDIG_URL; ?>/gadgets/js/rpc.js?c=1"></script>
<script type="text/javascript" src="<?php echo AT_SOCIAL_BASENAME; ?>lib/js/jquery-1.3.2.js"></script>
<script type="text/javascript"
	src="<?php echo AT_SOCIAL_BASENAME; ?>lib/js/prototype.js"></script>
<script type="text/javascript" src="<?php echo AT_SOCIAL_BASENAME; ?>lib/js/container.js"></script>

<?php	
	foreach ($this->list_of_my_apps as $id=>$app_obj): 
?>
<div class="gadget_wrapper">
<div class="headingbox">
	<div style="float:right">
		<a href="<?php echo $_base_href.AT_SOCIAL_BASENAME; ?>applications.php?app_id=<?php echo $app_obj->getId().SEP;?>delete=1"><img src="<?php echo $_base_href.AT_SOCIAL_BASENAME; ?>images/b_drop.png" border="0" alt="<?php echo _AT('delete'); ?>" title="<?php echo _AT('delete'); ?>" style="float:right;" /></a>

		<a href="<?php echo AT_SOCIAL_BASENAME.'applications.php?app_id='.$id.SEP.'settings=1'; ?>"><img src="<?php echo $_base_href.AT_SOCIAL_BASENAME;?>images/icon-settings.png" alt="<?php echo _AT('settings');?>" title="<?php echo _AT('settings');?>" border="0" style="float:right;" /></a>
	</div>	
	<h3><?php echo $app_obj->getAppLink($app_obj->getTitle(), $id); ?></h3>
</div>
<div class="contentbox" style="padding:0.5em;">
<?php
	//the name and id here in the iframe is used by the container.js to identify the caller.
	//Simply, the id is used here to generate the $(this.f)
	//Originally it was using the ModID, I changed it to appId.
	//@harris
?>
	<iframe 
	scrolling="<?php echo $app_obj->getScrolling(); ?>"
	height="<?php echo $app_obj->getHeight();?>px" width="100%"
	frameborder="0" src="<?php echo $app_obj->getIframeUrl($_REQUEST['id'], 'profile', $_GET['appParams']);?>" class="gadgets-gadget"
	name="remote_iframe_<?php echo $app_obj->getId(); ?>"
	id="remote_iframe_<?php echo $app_obj->getId(); ?>"></iframe>	
</div></div><br />
<?php endforeach; ?>