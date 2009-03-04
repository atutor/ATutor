<!--  compressed with java -jar {$path}/yuicompressor-2.3.5.jar -o {$file}-min.js {$file}.js -->
<script type="text/javascript"
	src="<?php echo AT_SHINDIG_URL; ?>/gadgets/js/rpc.js?c=1"></script>
<script type="text/javascript"
	src="http://ajax.googleapis.com/ajax/libs/prototype/1.6.0.2/prototype.js"></script>
<script type="text/javascript" src="mods/social/lib/js/container.js"></script>

<h3><?php echo _AT('your_application'); ?></h3>
<div class="gadgets-gadget-content"><iframe width="800px"
	scrolling="<?=$this->gadget['scrolling'] || $this->gadget['scrolling'] == 'true' ? 'yes' : 'no'?>"
	height="<?php $app=$this->app; echo $app->getHeight();?>px"
	frameborder="no" src="<?php echo $this->iframe_url;?>" class="gadgets-gadget"
	name="remote_iframe_<?php echo $this->gadget['mod_id'];?>"
	id="remote_iframe_<?php echo $this->gadget['mod_id'];?>"></iframe></div>
</div>