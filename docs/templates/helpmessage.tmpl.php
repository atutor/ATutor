<?php 
/*
 * @author Jacek Materna
 *
 *	Four Savant variables: $item which is the processed ouput message content according to lang spec.
 *							$a, $b , $c , $d are boolean results to control the flow of code
 */
 
global $_my_uri, $_base_path;

// header
echo '<a name="help"></a>';
if ($a) {
	if($b){
		echo '<small>( <a href="' . $_my_uri . 'e=1#help">' . _AT('help') . '</a> )</small><br /><br />';

	}else{
		echo '<a href="' . $_my_uri . 'e=1#help"><img src="' . $_base_path . 'images/help_open.gif" class="menuimage"  alt="'._AT('help').'" border="0" /></a><br />';
	}
	return;
}
?>
<br />
<table border="0" class="hlpbox" cellpadding="3" cellspacing="2" width="90%" summary="" align="center">
<tr class="hlpbox">
<td>
	<h3>

<?php
if ($c) {
	echo '<a href="' . $_my_uri . '#help">';
	echo '<img src="' . $_base_path . 'images/help_close.gif" class="menuimage5" align="top" alt="'._AT('close_help').'" border="0" title="'._AT('close_help').'"/></a> ';
} else {
	echo '<img src="' . $_base_path . 'images/help.gif" class="menuimage5" align="top" alt="'._AT('help').'" border="0" /> ';
}
echo '<small>'._AT('help').'</small></h3>';

$body = '';
	
if (is_object($item)) {
	/* this is a PEAR::ERROR object.	*/
	/* for backwards compatability.		*/
	$body .= $item->get_message();
	$body .= '.<p>';
	$body .= '<small>';
	$body .= $item->getUserInfo();
	$body .= '</small></p>';

} else if (is_array($item)) {
	/* this is an array of items */
	$body .= '<ul>';
	foreach($item as $e => $info){
		$body .= '<li><small>'. $info .'</small></li>';
	}
	$body .= '</ul>';
} else {
	/* Single item in the message */
	$body .= '<ul>';
	$body .= '<li><small>'. $item .'</small></li>';
	$body .='</ul>';
}

// body
echo $body;

// footer
if($d){
?>
	<div align="right"><small><small><a href="<?php echo $_base_path; ?>help/about_help.php?h=1"><?php echo _AT('about_help'); ?></a>.</small></small></div>
<?php } 

echo '</td></tr></table><br />';

?>