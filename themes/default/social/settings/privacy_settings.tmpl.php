<br /><form action="<?php echo url_rewrite(AT_SOCIAL_BASENAME.'settings.php');?>" method="post">
<div class="input-form" style="padding:1em;">
<h4><?php echo _AT('privacy_settings'); ?></h4>
<div class="row"><?php echo _AT('privacy_control_blurb'); ?> </div>
<fieldset class="group_form"><legend class="group_form"><?php echo _AT('profile_control'); ?></legend>
<div class="row">
	<h5><?php echo _AT('basic_profile'); ?></h5>
		<div>
		<?php foreach ($this->controller->getPermissionLevels() as $control_id=>$control_string): 
			(isset($this->profile_prefs[AT_SOCIAL_PROFILE_BASIC][$control_id]))?$checked=' checked="checked"':$checked='';	?>		
		<label for="boxes_profile_<?php echo AT_SOCIAL_PROFILE_BASIC.'_'.$control_id; ?>"><?php echo $control_string; ?></label>
		<input type="checkbox" id="boxes_profile_<?php echo AT_SOCIAL_PROFILE_BASIC.'_'.$control_id; ?>" name="profile_prefs[<?php echo AT_SOCIAL_PROFILE_BASIC;?>][<?php echo $control_id; ?>]" value="1"  <?php echo $checked; ?> />
		<?php endforeach; ?>
		</div>
</div>
<div class="row">
	<h5><?php echo _AT('detailed_profile'); ?></h5>
	<div>
		<?php foreach ($this->controller->getPermissionLevels() as $control_id=>$control_string): 
			(isset($this->profile_prefs[AT_SOCIAL_PROFILE_PROFILE][$control_id]))?$checked=' checked="checked"':$checked='';	?>		
		<label for="boxes_profile_<?php echo AT_SOCIAL_PROFILE_PROFILE.'_'.$control_id; ?>"><?php echo $control_string; ?></label>
		<input type="checkbox" id="boxes_profile_<?php echo AT_SOCIAL_PROFILE_PROFILE.'_'.$control_id; ?>" name="profile_prefs[<?php echo AT_SOCIAL_PROFILE_PROFILE;?>][<?php echo $control_id; ?>]" value="1"  <?php echo $checked; ?> />
		<?php endforeach; ?>
	</div>
</div>
<div class="row">
	<h5><?php echo _AT('activities'); ?></h5>
	<div>
		<?php foreach ($this->controller->getPermissionLevels() as $control_id=>$control_string): 
			(isset($this->profile_prefs[AT_SOCIAL_PROFILE_STATUS_UPDATE][$control_id]))?$checked=' checked="checked"':$checked='';	?>		
		<label for="boxes_profile_<?php echo AT_SOCIAL_PROFILE_STATUS_UPDATE.'_'.$control_id; ?>"><?php echo $control_string; ?></label>
		<input type="checkbox" id="boxes_profile_<?php echo AT_SOCIAL_PROFILE_STATUS_UPDATE.'_'.$control_id; ?>" name="profile_prefs[<?php echo AT_SOCIAL_PROFILE_STATUS_UPDATE;?>][<?php echo $control_id; ?>]" value="1"  <?php echo $checked; ?> />
		<?php endforeach; ?>
	</div>
</div>
<!-- 
  Not implemented yet.

<div class="row">
	<h5><?php echo _AT('media'); ?></h5>
	<div>
		<?php foreach ($this->controller->getPermissionLevels() as $control_id=>$control_string): 
			(isset($this->profile_prefs[AT_SOCIAL_PROFILE_MEDIA][$control_id]))?$checked=' checked="checked"':$checked='';	?>		
		<label for="boxes_profile_<?php echo AT_SOCIAL_PROFILE_MEDIA.'_'.$control_id; ?>"><?php echo $control_string; ?></label>
		<input type="checkbox" id="boxes_profile_<?php echo AT_SOCIAL_PROFILE_MEDIA.'_'.$control_id; ?>" name="profile_prefs[<?php echo AT_SOCIAL_PROFILE_MEDIA;?>][<?php echo $control_id; ?>]" value="1"  <?php echo $checked; ?>>
		<?php endforeach; ?>
	</div>
</div>
  -->
<div class="row">
	<h5><?php echo _AT('connections'); ?></h5>
	<div>
		<?php foreach ($this->controller->getPermissionLevels() as $control_id=>$control_string): 
			(isset($this->profile_prefs[AT_SOCIAL_PROFILE_CONNECTION][$control_id]))?$checked=' checked="checked"':$checked='';	?>		
		<label for="boxes_profile_<?php echo AT_SOCIAL_PROFILE_CONNECTION.'_'.$control_id; ?>"><?php echo $control_string; ?></label>
		<input type="checkbox" id="boxes_profile_<?php echo AT_SOCIAL_PROFILE_CONNECTION.'_'.$control_id; ?>" name="profile_prefs[<?php echo AT_SOCIAL_PROFILE_CONNECTION;?>][<?php echo $control_id; ?>]" value="1"  <?php echo $checked; ?> />
		<?php endforeach; ?>
	</div>
</div>
<div class="row">
	<h5><?php echo _AT('education'); ?></h5>
	<div>
		<?php foreach ($this->controller->getPermissionLevels() as $control_id=>$control_string): 
			(isset($this->profile_prefs[AT_SOCIAL_PROFILE_EDUCATION][$control_id]))?$checked=' checked="checked"':$checked='';	?>		
		<label for="boxes_profile_<?php echo AT_SOCIAL_PROFILE_EDUCATION.'_'.$control_id; ?>"><?php echo $control_string; ?></label>
		<input type="checkbox" id="boxes_profile_<?php echo AT_SOCIAL_PROFILE_EDUCATION.'_'.$control_id; ?>" name="profile_prefs[<?php echo AT_SOCIAL_PROFILE_EDUCATION;?>][<?php echo $control_id; ?>]" value="1"  <?php echo $checked; ?> />
		<?php endforeach; ?>
	</div>
</div>
<div class="row">
	<h5><?php echo _AT('position'); ?></h5>
	<div>
		<?php foreach ($this->controller->getPermissionLevels() as $control_id=>$control_string): 
			(isset($this->profile_prefs[AT_SOCIAL_PROFILE_POSITION][$control_id]))?$checked=' checked="checked"':$checked='';	?>		
		<label for="boxes_profile_<?php echo AT_SOCIAL_PROFILE_POSITION.'_'.$control_id; ?>"><?php echo $control_string; ?></label>
		<input type="checkbox" id="boxes_profile_<?php echo AT_SOCIAL_PROFILE_POSITION.'_'.$control_id; ?>" name="profile_prefs[<?php echo AT_SOCIAL_PROFILE_POSITION;?>][<?php echo $control_id; ?>]" value="1"  <?php echo $checked; ?> />
		<?php endforeach; ?>
	</div>
</div>
</fieldset>

<fieldset class="group_form"><legend class="group_form"><?php echo _AT('search_control'); ?></legend>
<div class="row">
	<h5><?php echo _AT('search_visibility'); ?></h5>
		<div>
		<?php foreach ($this->controller->getPermissionLevels() as $control_id=>$control_string): 
			(isset($this->search_prefs[AT_SOCIAL_SEARCH_VISIBILITY][$control_id]))?$checked=' checked="checked"':$checked='';	?>		
		<label for="boxes_search_<?php echo AT_SOCIAL_SEARCH_VISIBILITY.'_'.$control_id; ?>"><?php echo $control_string; ?></label>
		<input type="checkbox" id="boxes_search_<?php echo AT_SOCIAL_SEARCH_VISIBILITY.'_'.$control_id; ?>" name="search_prefs[<?php echo AT_SOCIAL_SEARCH_VISIBILITY;?>][<?php echo $control_id; ?>]" value="1"  <?php echo $checked; ?> />
		<?php endforeach; ?>
		</div>
</div>
<!-- These settings will be implemented after
[[[Following are to be implemented...]]]
<div class="row">
	<h5><?php echo _AT('search_profile'); ?></h5>
		<div>
		<?php foreach ($this->controller->getPermissionLevels() as $control_id=>$control_string): 
			(isset($this->search_prefs[AT_SOCIAL_SEARCH_PROFILE][$control_id]))?$checked=' checked="checked"':$checked='';	?>		
		<label for="boxes_search_<?php echo AT_SOCIAL_SEARCH_PROFILE.'_'.$control_id; ?>"><?php echo $control_string; ?></label>
		<input type="checkbox" id="boxes_search_<?php echo AT_SOCIAL_SEARCH_PROFILE.'_'.$control_id; ?>" name="search_prefs[<?php echo AT_SOCIAL_SEARCH_PROFILE;?>][<?php echo $control_id; ?>]" value="1"  <?php echo $checked; ?> />
		<?php endforeach; ?>
		</div>
</div>
<div class="row">
	<h5><?php echo _AT('search_connections'); ?></h5>
		<div>
		<?php foreach ($this->controller->getPermissionLevels() as $control_id=>$control_string): 
			(isset($this->search_prefs[AT_SOCIAL_SEARCH_CONNECTION][$control_id]))?$checked=' checked="checked"':$checked='';	?>		
		<label for="boxes_search_<?php echo AT_SOCIAL_SEARCH_CONNECTION.'_'.$control_id; ?>"><?php echo $control_string; ?></label>
		<input type="checkbox" id="boxes_search_<?php echo AT_SOCIAL_SEARCH_CONNECTION.'_'.$control_id; ?>" name="search_prefs[<?php echo AT_SOCIAL_SEARCH_CONNECTION;?>][<?php echo $control_id; ?>]" value="1"  <?php echo $checked; ?> />
		<?php endforeach; ?>
		</div>
</div>
<div class="row">
	<h5><?php echo _AT('search_education'); ?></h5>
		<div>
		<?php foreach ($this->controller->getPermissionLevels() as $control_id=>$control_string): 
			(isset($this->search_prefs[AT_SOCIAL_SEARCH_EDUCATION][$control_id]))?$checked=' checked="checked"':$checked='';	?>		
		<label for="boxes_search_<?php echo AT_SOCIAL_SEARCH_EDUCATION.'_'.$control_id; ?>"><?php echo $control_string; ?></label>
		<input type="checkbox" id="boxes_search_<?php echo AT_SOCIAL_SEARCH_EDUCATION.'_'.$control_id; ?>" name="search_prefs[<?php echo AT_SOCIAL_SEARCH_EDUCATION;?>][<?php echo $control_id; ?>]" value="1"  <?php echo $checked; ?> />
		<?php endforeach; ?>
		</div>
</div>
<div class="row">
	<h5><?php echo _AT('search_position'); ?></h5>
		<div>
		<?php foreach ($this->controller->getPermissionLevels() as $control_id=>$control_string): 
			(isset($this->search_prefs[AT_SOCIAL_SEARCH_POSITION][$control_id]))?$checked=' checked="checked"':$checked='';	?>		
		<label for="boxes_search_<?php echo AT_SOCIAL_SEARCH_POSITION.'_'.$control_id; ?>"><?php echo $control_string; ?></label>
		<input type="checkbox" id="boxes_search_<?php echo AT_SOCIAL_SEARCH_POSITION.'_'.$control_id; ?>" name="search_prefs[<?php echo AT_SOCIAL_SEARCH_POSITION;?>][<?php echo $control_id; ?>]" value="1"  <?php echo $checked; ?> />
		<?php endforeach; ?>
		</div>
</div>
-->
</fieldset>


<div class="row">
	<input type="hidden" name="n" value="privacy_settings" />
	<input class="button" type="submit" name="submit" value="<?php echo _AT('save'); ?>"/>
</div>
</div>
</form>