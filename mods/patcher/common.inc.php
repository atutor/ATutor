<?php
function print_errors( $errors, $notes='' ) {
	?>
	<br />
	<table border="0" class="errbox" cellpadding="3" cellspacing="2" width="90%" summary="" align="center">
	<tr class="errbox">
		<td>
		<h3 class="err"><img src="images/bad.gif" align="top" alt="" class="img" /> Warning</h3>
		<?php
			echo '<ul>';
			foreach ($errors as $p) {
				echo '<li>'.$p.'</li>';
			}
			echo '</ul>';
		?>
		</td>
	</tr>
	<tr>
		<td>
		<?php echo $notes; ?>
		</td>
	</tr>
	</table>	<br />
<?php
}

function print_feedback( $feedback, $notes='' ) {
	?>
	<br />
	<table border="0" class="fbkbox" cellpadding="3" cellspacing="2" width="90%" summary="" align="center">
	<tr class="fbkbox">
	<td><h3 class="feedback2"><img src="images/feedback.gif" align="top" alt="" class="img" /> The patch is installed successfully!</h3>
		<?php
			echo '<ul>';
			foreach ($feedback as $p) {
				echo '<li>'.$p.'</li>';
			}
			echo '</ul>';
		?></td>
	</tr>
	<tr>
		<td>
		<?php echo $notes; ?>
		</td>
	</tr>
	</table>
	<br />
<?php
}

?>
