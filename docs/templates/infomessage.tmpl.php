<?php 
require_once(AT_INCLUDE_PATH.'lib/output.inc.php'); 

while( list($key, $item) = each($payload) ) {
	$body = '';
	
	$item = getTranslatedCodeStr($item);
	
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
	
	echo '<br /><table border="0" cellpadding="3" cellspacing="2" width="90%" summary="" align="center"  class="hlpbox">' .
			'<tr class="hlpbox"><td><h3><img src="' . $base_href . 'images/infos.gif" align="top" class="menuimage5" alt="' .
			_AT('info') . '" /><small>' . _AT('info') . '</small></h3>';

	echo $body;
	
	echo '</td></tr></table>';
}
?>