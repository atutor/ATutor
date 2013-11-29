<?php  
/*
 * @author Jacek Materna
 *
 *	One Savant variable: $item which is the processed ouput message content according to lang spec.
 */
 
global $_base_href; ?>

<div id="info" role="alert">
    <a href="#" class="message_link" onclick="return false;"><img src="<?php echo $this->img; ?>close_icon.png" alt="<?php echo _AT('close'); ?>"/></a>
	<?php if (is_array($this->item)) : ?>
		<ul>
		<?php foreach($this->item as $i) : ?>
			<li><?php echo $i; ?></li>
		<?php endforeach; ?>
		</ul>
	<?php endif; ?>
</div>