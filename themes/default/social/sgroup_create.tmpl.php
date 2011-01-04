<?php
//Deprecated
//Use sgroup_edit.tmpl.php instead.
//keeping this just as a record
//@harris
?>

<div class="input-form">	
	<form action="<?php echo url_rewrite(AT_SOCIAL_BASENAME.'groups/create.php'); ?>" method="POST" >
		<div class="row">
			<label for="group_name"><?php echo _AT('group_name'); ?></label>
			<input type="text" size="60" name="group_name" id="group_name" value="<?php echo $_POST['group_name']; ?>"/>
		</div>

		<div class="row">
			<label for="group_type"><?php echo _AT('group_type'); ?></label>
			<select name="group_type" id="group_type">
			<?php foreach ($this->group_types as $type_id=>$type): ?>
				<option value="<?php echo $type_id;?>"><?php echo _AT($type);?></option>
			<?php endforeach; ?>
			</select>
		</div>

		<div class="row">
			<label for="logo"><?php echo _AT('group_logo'); ?></label>
			<input type="text" size="60" name="logo" id="logo" value="<?php echo $_POST['logo']; ?>"/>
		</diV>

		<div class="row">
			<label for="description"><?php echo _AT('description'); ?></label>
			<textarea cols="40" rows="5" name="description" id="description"><?php echo $_POST['description']; ?></textarea>
		</div>

		<div class="row">
			<input class="button" type="submit" name="create" value="<?php echo _AT('create'); ?>" />	
		</div>
	</form>
</div>