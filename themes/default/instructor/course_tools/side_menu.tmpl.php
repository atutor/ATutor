<?php global $_stacks; global $system_courses;  ?>
<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" name="prefs">
<div class="input-form" style="width:90%">
	<fieldset class="group_form"><legend class="group_form"><?php echo _AT('side_menu'); ?></legend>
	<div class="row">
		<p><?php echo _AT('side_menu_text'); ?></p>
	</div>

	<div class="row">
		<?php
			$num_stack = count($_stacks);
			$side_menu = explode("|", $system_courses[$_SESSION['course_id']]['side_menu']);			

			for ($i=0; $i<$num_stack; $i++) {				
				echo '<select name="stack['.$i.']">';
				echo '<option value=""></option>';
				foreach ($_stacks as $name=>$info) {
					if (isset($info['title'])) {
						$title = $info['title'];
					} else {
						$title = _AT($info['title_var']);
					}
					echo '<option value="'.$name.'"';
					if (isset($side_menu[$i]) && ($name == $side_menu[$i])) {
						echo ' selected="selected"';
					}
					echo '>'.$title.'</option>';
				}
				echo '</select>';
				echo '<br />'; 
			} ?>
	</div>

	<div class="buttons">
		<input type="submit" name="submit" value="<?php echo _AT('apply'); ?>" accesskey="s" />
		<input type="submit" name="cancel" value="<?php echo _AT('cancel'); ?>" />
	</div>
	</fieldset>
</div>
</form>
