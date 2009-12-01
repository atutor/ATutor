<!--  compressed with java -jar {$path}/yuicompressor-2.3.5.jar -o {$file}-min.js {$file}.js -->
<script type="text/javascript"
	src="<?php echo AT_SHINDIG_URL; ?>/gadgets/js/rpc.js?c=1"></script>
<!-- header.inc.php has jscript/InfusionAll.js imported.  Jquery should be there with it
<script type="text/javascript" src="<?php echo AT_SOCIAL_BASENAME; ?>lib/js/jquery-1.3.2.js"></script>
-->
<script type="text/javascript"
	src="<?php echo AT_SOCIAL_BASENAME; ?>lib/js/prototype.js"></script>
<script type="text/javascript" src="<?php echo AT_SOCIAL_BASENAME; ?>lib/js/container.js"></script>

<h3><?php echo $this->app->getTitle(); ?></h3>
<div class="gadgets-gadget-content"><iframe width="95%"
	scrolling="<?php echo $this->app->getScrolling(); ?>"
	height="<?php $app=$this->app; echo $app->getHeight();?>px"
	frameborder="0" src="<?php echo $this->iframe_url;?>" class="gadgets-gadget"
	name="remote_iframe_<?php echo $this->app->getId();?>"
	id="remote_iframe_<?php echo $this->app->getId();?>"></iframe>
</div>