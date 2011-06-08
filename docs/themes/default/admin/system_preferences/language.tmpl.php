<?php global $languageManager;?>
<form name="form" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">

<table summary="" class="data" rules="cols">
<colgroup>
	<col />
	<col class="sort" />
	<col span="3" />
</colgroup>
<thead>
<tr>
	<th scope="col">&nbsp;</th>
	<th scope="col"><?php echo _AT('name_in_language'); ?></th>
	<th scope="col"><?php echo _AT('name_in_english'); ?></th>
	<th scope="col"><?php echo _AT('lang_code'); ?></th>
	<th scope="col"><?php echo _AT('charset'); ?></th>
</tr>
</thead>
<tfoot>
<tr>
	<td colspan="5">
		<?php if (defined('AT_DEVEL_TRANSLATE') && AT_DEVEL_TRANSLATE): ?>
			<input type="submit" name="edit" value="<?php echo _AT('edit'); ?>" />  
			<input type="submit" name="export" value="<?php echo _AT('export'); ?>"  /> 
			<input type="submit" name="delete" value="<?php echo _AT('delete'); ?>" /> 
			<?php echo _AT('or'); ?> <a href="mods/_core/languages/language_add.php"><?php echo _AT('add_a_new_language'); ?></a>
		<?php else: ?>
			<input type="submit" name="export" value="<?php echo _AT('export'); ?>" /> 
			<input type="submit" name="delete" value="<?php echo _AT('delete'); ?>" />
		<?php endif; ?>
	</td>
</tr>
</tfoot>
<tbody>
	<?php foreach ($languageManager->getAvailableLanguages() as $codes): ?>
		<?php $language = current($codes); ?>
		<tr onmousedown="document.form['m<?php echo $language->getCode(); ?>'].checked = true; rowselect(this);" id="r_<?php echo $language->getCode(); ?>">
			<td><input type="radio" name="id" value="<?php echo $language->getCode(); ?>" id="m<?php echo $language->getCode(); ?>" /></td>
			<td><label for="m<?php echo $language->getCode(); ?>"><?php echo $language->getNativeName(); ?></label></td>
			<td><?php echo $language->getEnglishName(); ?></td>
			<td><?php echo strtolower($language->getCode()); ?></td>
			<td><?php echo strtolower($language->getCharacterSet()); ?></td>
		</tr>
	<?php endforeach; ?>
</tbody>
</table>
</form>
