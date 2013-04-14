<div class="input-form">
		<fieldset class="group_form"><legend class="group_form"><?php echo _AT('list_create_course_list'); ?></legend>
<form action="mods/_core/enrolment/verify_list.php" method="post">
<input type="hidden" name="from" value="create" />
<div>

	<div class="row">
		<?php echo _AT('import_sep_txt'); ?><br />
		<input type="radio" name="sep_choice" id="und" value="_" checked="checked" />
		<label for="und"><?php echo _AT('underscore'); ?></label>
		<input type="radio" name="sep_choice" id="per" value="." />
		<label for="per"><?php echo _AT('period'); ?></label>
	</div>

		
<table class="data static" summary="Create a course list by first name, last name, and email." rules="cols">
<thead>
<tr>
	<th>&nbsp;</th>
	<th><?php echo _AT('first_name'); ?></th>
	<th><?php echo _AT('last_name'); ?></th>
	<th><?php echo _AT('email'); ?></th>
</tr>
</thead>

<tfoot>
<tr>
	<td colspan="4">
		<input type="submit" name="submit" value="<?php echo _AT('list_add_course_list');  ?>" />
		<input type="submit" name="cancel" value="<?php echo _AT('cancel'); ?>" />
	</td>
</tr>
</tfoot>

<tbody>
<?php for ($i=1; $i <= 5; $i++): ?>
	<tr>
		<td><?php echo $i; ?></td>
		<td><input type="text" name="first_name<?php echo $i; ?>" /></td>
		<td><input type="text" name="last_name<?php echo $i; ?>" /></td>
		<td><input type="text" name="email<?php echo $i; ?>" /></td>
	</tr>
<?php endfor; ?>
</tbody>

</table>
</form>
</fieldset>
</div>