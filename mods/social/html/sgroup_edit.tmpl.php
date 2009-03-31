<div class="input-form">	
	<form action="<?php echo 'mods/social/groups/edit.php'; ?>" method="POST" enctype="multipart/form-data">
		<div class="row">
			<label for="group_name"><?php echo _AT('group_name'); ?></label>
			<input type="text" size="60" name="group_name" id="group_name" value="<?php echo $this->group_obj->getName(); ?>"/>
		</div>

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
			<label for="logo"><?php echo _AT('group_logo'); ?></label>
			<input type="file" size="60" name="logo" id="logo" />
		</diV>

		<div class="row">
			<label for="description"><?php echo _AT('description'); ?></label>
			<textarea cols="40" rows="5" name="description" id="description"><?php echo $this->group_obj->getDescription(); ?></textarea>
		</div>

		<div class="row">
			<input type="hidden" name="id" value="<?php echo $this->group_obj->getID();?>" /?>
			<input class="button" type="submit" name="save" value="<?php echo _AT('save'); ?>" />	
		</div>
	</form>
</div>