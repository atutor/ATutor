<?php require(AT_INCLUDE_PATH.'lib/output.inc.php'); 
		$_payload = getTranslatedCodeStr($payload);

		while( list($key, $item) = each($_payload) ) {
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
			
			echo '<br /><table border="0" class="fbkbox" cellpadding="0"' .
					' cellspacing="2" width="90%" summary="" align="center">' .
					'<tr class="fbkbox"><td><h3><img src="' . $base_href . 
					'/images/feedback_x.gif" align="top" alt="' . _AT('feedback') .
					'" class="menuimage5" /><small>' . _AT('feedback') . '</small></h3>'; 
			
			echo $body;
			
			echo '</td></tr></table><br />';
		}
?>