<?php 
/*
 * @author Jacek Materna
 *
 *	Four Savant variables: $item which is the processed ouput message content according to lang spec.
 *							$a, $b , $c , $d are boolean results to control the flow of code
 */
 
global $_my_uri, $_base_path;

// header
echo '<a name="help"></a>'."\n";
if ($this->a) {
	if($b){
		echo '<small>( <a href="' . $_my_uri . 'e=1#help">' . _AT('help') . '</a> )</small><br /><br />'."\n";

	}else{
		echo '<a href="' . $_my_uri . 'e=1#help"><img src="' . $_base_path . 'images/help_open.gif" class="menuimage"  alt="'._AT('help').'" border="0" /></a><br />'."\n";
	}
	return;
}
?>

<div id="help">
	<?php if (is_array($this->item)) : ?>
		<ul>
		<?php foreach($this->item as $e) : ?>
			<li><?php echo $e; ?></li>
		<?php endforeach; ?>
		</ul>
		<?php
		if($this->d){
?>
	<div align="right"><small><small><a href="<?php echo $_base_path; ?>help/about_help.php?h=1"><?php echo _AT('about_help'); ?></a>.</small></small></div>
<?php }  ?>
	<?php endif; ?>
</div>