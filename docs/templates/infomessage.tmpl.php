<?php  
/*
 * @author Jacek Materna
 *
 *	One Savant variable: $item which is the processed ouput message content according to lang spec.
 */
 
 global $_base_href;
 
// header
echo '<br /><table border="0" cellpadding="3" cellspacing="2" width="90%" summary="" align="center"  class="hlpbox">' .
		'<tr class="hlpbox"><td><h3><img src="' . $_base_href . 'images/infos.gif" align="top" class="menuimage5" alt="' .
		_AT('info') . '" /><small>' . _AT('info') . '</small></h3>';

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
echo '</td></tr></table>';

?>