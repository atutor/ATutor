<?php  
/*
 * @author Jacek Materna
 *
 *	One Savant variable: $item which is the processed ouput message content according to lang spec.
 */
 
 global $_base_href;
 
// header
?><br />
<table border="0" cellpadding="3" cellspacing="2" width="90%" summary="" align="center"  class="cnfrmbox">
<tr class="cnfrmbox">
	<td><h3><img src="<?php echo $_base_href; ?>images/question.gif" align="top" alt="<?php echo _AT('confirmation'); ?>" /><small><?php echo _AT('confirmation'); ?></small></h3>

	<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
	<?php if(isset($hidden_vars)): ?>
		<?php echo $hidden_vars; ?>
	<?php endif; ?>


<?php if (is_array($item)) : ?>
	<ul>
		<?php foreach($item as $e) : ?>
			<li><small><?php echo $e; ?></small></li>
		<?php endforeach; ?>
	</ul>
<?php endif; ?>
	<br />
	<div align="center"><input type="submit" name="submit_yes" value="<?php echo _AT('submit_yes'); ?>" class="button button_yes" /> - <input type="submit" name="submit_no" value="<?php echo _AT('submit_no'); ?>" class="button button_no" /></div>
	<br />
	</form>
	</td>
</tr>
</table>

