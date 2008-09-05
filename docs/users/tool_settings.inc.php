 <fieldset> <legend><strong><?php echo _AT("support_tools"); ?></strong>  </legend>  

	<div class="row">
		<?php echo _AT('use_support_tools'); ?><br />
<?php
			if (isset($_POST["dictionary_val"]))
				$dict_val = $_POST["dictionary_val"];
			else
				$dict_val = $_SESSION['prefs']['PREF_DICTIONARY'];
			
			if ($dict_val == 1) $dict = ' checked ';

			if (isset($_POST["thesaurus_val"]))
				$thes_val = $_POST["thesaurus_val"];
			else
				$thes_val = $_SESSION['prefs']['PREF_THESAURUS'];
				
			if ($thes_val == 1) $thes = ' checked ';

			if (isset($_POST["note_taking_val"]))
				$notes_val = $_POST["note_taking_val"];
			else
				$notes_val = $_SESSION['prefs']['PREF_NOTE_TAKING'];
				
			if ($notes_val == 1) $notes = ' checked ';

			if (isset($_POST["calculator_val"]))
				$calc_val = $_POST["calculator_val"];
			else
				$calc_val = $_SESSION['prefs']['PREF_CALCULATOR'];
				
			if ($calc_val == 1) $calc = ' checked ';

			if (isset($_POST["peer_interaction_val"]))
				$peer_val = $_POST["peer_interaction_val"];
			else
				$peer_val = $_SESSION['prefs']['PREF_PEER_INTERACTION'];
				
			if ($peer_val == 1) $peer = ' checked ';

			if (isset($_POST["abacus_val"]))
				$abac_val = $_POST["abacus_val"];
			else
				$abac_val = $_SESSION['prefs']['PREF_ABACUS'];
				
			if ($abac_val == 1) $abac = ' checked ';

?>
		<input id="dict_val" name="dictionary_val" type="hidden" value="<?php echo $dict_val; ?>" >
		<input id="dict" name="dictionary" type="checkbox" <?php echo $dict; ?> onchange="changeVal('dict')"><label for="dict"><?php echo _AT("dictionary"); ?></label><br />
		
		<input id="thes_val" name="thesaurus_val" type="hidden" value="<?php echo $thes_val; ?>" >
		<input id="thes" name="thesaurus" type="checkbox" <?php echo $thes; ?> onchange="changeVal('thes')"><label for="thes"><?php echo _AT("thesaurus"); ?></label><br />

		<input id="notes_val" name="note_taking_val" type="hidden" value="<?php echo $notes_val; ?>" >
		<input id="notes" name="note_taking" type="checkbox" <?php echo $notes; ?> onchange="changeVal('notes')"><label for="notes"><?php echo _AT("note_taking"); ?></label><br />

		<input id="calc_val" name="calculator_val" type="hidden" value="<?php echo $calc_val; ?>" >
		<input id="calc" name="calculator" type="checkbox" <?php echo $calc; ?> onchange="changeVal('calc')"><label for="calc"><?php echo _AT("calculator"); ?></label><br />

		<input id="peer_val" name="peer_interaction_val" type="hidden" value="<?php echo $peer_val; ?>" >
		<input id="peer" name="peer_interaction" type="checkbox" <?php echo $peer; ?> onchange="changeVal('peer')"><label for="peer"><?php echo _AT("peer_interaction"); ?></label><br />

		<input id="abac_val" name="abacus_val" type="hidden" value="<?php echo $abac_val; ?>" >
		<input id="abac" name="abacus" type="checkbox" <?php echo $abac; ?> onchange="changeVal('abac')"><label for="abac"><?php echo _AT("abacus"); ?></label><br />
	</div>

<script language="javascript" type="text/javascript">
//<!--
function changeVal(val_name)
{
	if (eval('document.getElementById("'+ val_name +'").checked'))
		eval('document.getElementById("'+ val_name +'_val").value = 1');
	else
		eval('document.getElementById("'+ val_name +'_val").value = 0');
}
//-->
</script>

</fieldset>
