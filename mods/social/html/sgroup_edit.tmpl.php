<?php 
if (isset($this->group_obj)){
	//edit
	$form_url = AT_SOCIAL_BASENAME.'groups/edit.php';
	$button_name = 'save';
	$name = $this->group_obj->getName();
	$logo = $this->group_obj->getLogo();
	$privacy = $this->group_obj->getPrivacy();
	$description = $this->group_obj->getDescription();
	$id = $this->group_obj->getID();
} else {
	//create new one
	$form_url = AT_SOCIAL_BASENAME.'groups/create.php';
	$button_name = 'create';
}
?>

<div class="input-form">	
	<form action="<?php echo $form_url; ?>" method="POST" enctype="multipart/form-data">
		<div class="row">
			<label for="group_name"><?php echo _AT('group_name'); ?></label>
			<input type="text" size="60" name="group_name" id="group_name" value="<?php echo $name; ?>"/>
		</div>

		<?php if (isset($this->group_obj)): ?>
		<div class="row">
			<label for="group_admin"><?php echo _AT('group_admin'); ?></label>
			<select name="group_admin" id="group_admin">
			<?php 
				foreach($this->group_obj->getGroupMembers() as $garbage=>$member_obj):
					$selected = '';
					if ($this->group_obj->getUser()==$member_obj->getID()){
						$selected = ' selected="selected"';
					} 
			?>
				<option value="<?php echo $member_obj->getID();?>" <?php echo $selected;?>><?php echo printSocialName($member_obj->getID());?></option>
			<?php endforeach; ?>
			</select>
		</div>
		<?php endif; ?>

		<div class="row">
			<label for="group_type"><?php echo _AT('group_type'); ?></label>
			<select name="group_type" id="group_type">
			<?php 
				foreach ($this->group_types as $type_id=>$type): 
					$selected = '';
					if ($this->group_obj->type_id==$type_id){
						$selected = ' selected="selected"';
					} 
			?>
				<option value="<?php echo $type_id;?>" <?php echo $selected;?>><?php echo _AT($type);?></option>
			<?php endforeach; ?>
			</select>
		</div>

		<div class="row">
			<?php
				if ($privacy==1){
					$private_selected = ' checked="checked"';
				}  else {
					$public_selected = ' checked="checked"';
				}
			?>
			<label><?php echo _AT('group_privacy');?></label><br/>
			<input type="radio" name="group_privacy" id="group_privacy_public" value="public" <?php echo $public_selected; ?>/>
			<label for="group_privacy_public"><?php echo _AT('group_privacy_public'); ?></label><br/>
			<input type="radio" name="group_privacy" id="group_privacy_private" value="private" <?php echo $private_selected; ?>/>
			<label for="group_privacy_private"><?php echo _AT('group_privacy_private'); ?></label>
		</div>

		<div class="row">
			<?php 
				if ($logo!='') {
					echo $logo;
				} 
			?>
			<label for="logo"><?php echo _AT('group_logo'); ?></label>
			<input type="file" size="40" name="logo" id="logo" />
		</diV>

		<div class="row">
			<label for="description"><?php echo _AT('description'); ?></label>
			<textarea cols="40" rows="5" name="description" id="description"><?php echo $description; ?></textarea>
		</div>

		<div class="row">
			<input type="hidden" name="id" value="<?php echo $id;?>" /?>
			<input class="button" type="submit" name="<?php echo $button_name; ?>" value="<?php echo _AT($button_name); ?>" />	
			<input class="button" type="submit" name="cancel" value="<?php echo _AT('cancel'); ?>" />	
		</div>
	</form>
</div>