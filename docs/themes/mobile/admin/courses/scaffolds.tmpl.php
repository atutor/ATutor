<?php global $_config;?>

<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" name="scaffolds">
<div class="input-form">
 <fieldset class="group_form"> <legend class="group_form"><strong><?php echo _AT("support_tools"); ?></strong>  </legend>  
	<div class="row">
		<p><?php echo _AT('scaffold_text'); ?></p>
	</div>
	<div class="row">

		<label for="encyclopedia"><?php echo _AT('encyclopedia'); ?></label><br /><input type="text" id="encyclopedia"  name="encyclopedia" value="<?php echo $_config['encyclopedia']; ?>"  size="40"/><br />
		<label for="dictionary"><?php echo _AT('dictionary'); ?></label><br /><input type="text" id="dictionary"  name="dictionary" value="<?php echo $_config['dictionary']; ?>"  size="40"/><br />
		<label for="thesaurus"><?php echo _AT('thesaurus'); ?></label><br /><input type="text" id="thesaurus"  name="thesaurus" value="<?php echo $_config['thesaurus']; ?>" size="40"/><br />
		<label for="atlas"><?php echo _AT('atlas'); ?></label><br /><input type="text" id="atlas"  name="atlas" value="<?php echo $_config['atlas']; ?>"  size="40"/><br />
		<label for="calculator"><?php echo _AT('calculator'); ?></label><br /><input type="text" id="calculator"  name="calculator" value="<?php echo $_config['calculator']; ?>"  size="40"/><br />
		<label for=""><?php echo _AT('note_taking'); ?></label><br /><input type="text" id="note_taking"  name="note_taking" value="<?php echo $_config['note_taking']; ?>"  size="40"/>	<br />
		<label for="abacas"><?php echo _AT('abacus'); ?></label><br /><input type="text" id="abacas"  name="abacas" value="<?php echo $_config['abacas']; ?>"  size="40"/><br />
	</div>
	<div class="buttons">
		<input type="submit" name="submit" value="<?php echo _AT('save'); ?>" accesskey="s" />
		<input type="submit" name="cancel" value="<?php echo _AT('cancel'); ?>"  />
	</div>
</fieldset>
</div>
</form>