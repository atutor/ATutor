<?php 
if (empty($this->my_apps)):
	echo _AT('no_gadgets_installed'); 
else: ?>
<br />
<form action="<?php echo url_rewrite(AT_SOCIAL_BASENAME.'settings.php');?>" method="POST">
<div class="input-form"  style="padding:1em;">
	<h4><?php echo _AT('application_settings'); ?></h4>
	<div class="row"><?php echo _AT('application_control_blurb'); ?></div>
	<?php foreach($this->my_apps as $id=>$app_obj): ?>
	<div class="row" style="width:60%; border-bottom:1px solid #bbb;">
		<div style="float:left;"><?php echo $app_obj->getTitle(); ?></div>
		<div style="float:right;">
			<label for="app_<?php echo $app_obj->getID();?>"><?php echo _AT('show_on_home_page'); ?></label>
			<?php (isset($this->home_display[$id]))? $checked = ' checked="checked"': $checked = ''; ?>
			<input type="checkbox" id="app_<?php echo $app_obj->getID();?>" name="app[<?php echo $app_obj->getID();?>]" value="1" <?php echo $checked; ?>/>
		</div>
		<div><br/></div>
	</div>
	<?php endforeach;?>
	<div class="row">
		<input type="hidden" name="n" value="application_settings" />
		<input class="button" type="submit" name="submit" value="<?php echo _AT('save'); ?>"/>
	</div>
</div>
</form>
<?php endif; ?>
