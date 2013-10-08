<?php  
/*
 * @author Jacek Materna
 *
 *	One Savant variable: $item which is the processed ouput message content according to lang spec.
 */
 
 global $_base_href;
 
?>
<div id="warning" role="alert">
<a href="<?php echo $_SERVER['PHP_SELF']; ?>" class="message_link"><img src="<?php echo $this->img; ?>close_icon.png" alt="<?php echo _AT('close'); ?>"/></a>
	<?php if (is_array($this->item)) : ?>
		<ul>
		<?php foreach($this->item as $e) : ?>
			<li><?php echo $e; ?></li>
		<?php endforeach; ?>
		</ul>
	<?php endif; ?>
</div>