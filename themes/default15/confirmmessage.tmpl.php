<?php  
/*
 * @author Jacek Materna
 *
 *	One Savant variable: $item which is the processed ouput message content according to lang spec.
 */
 
 global $_base_href;
 
// header
?>

<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
<?php if(isset($this->hidden_vars)): ?>
	<?php echo $this->hidden_vars; ?>
<?php endif; ?>

<div class="input-form">
	<div class="row">
		<?php if (is_array($this->item)) : ?>
			<?php foreach($this->item as $e) : ?>
				<p><?php echo $e; ?></p>
			<?php endforeach; ?>
		<?php endif; ?>
	</div>

	<div class="row buttons">
		<input type="submit" name="submit_yes" value="<?php echo _AT('submit_yes'); ?>" /> 
		<input type="submit" name="submit_no" value="<?php echo _AT('submit_no'); ?>" />
	</div>
</div>
</form>