<?php

/**
* 
* Colorizes all text between <code>...</code> tags with PHP's
* highlight_string function.  Additionally, this will convert HTML
* entities within <code> blocks, so you can demonstrate HTML tags
* without them being honored in the browser.  Also converts tabs to four
* spaces.
* 
* To mark the beginning of php code in a <code> block, use the custom
* tag <php>, and use </php> to mark the end. You can't use the normal
* php code tags because Savant will try to execute that code when the
* template runs.
* 
* @author Paul M. Jones <pmjones@ciaweb.net>
* 
* @param object &$savant The calling Savant object.
* 
* @param string &$text The source text to be filtered.
*
* @version $Id: Savant_Filter_colorizeCode.php,v 1.1 2004/04/06 17:56:27 joel Exp $
* 
*/

require_once 'Savant/Filter.php';

class Savant_Filter_colorizeCode extends Savant_Filter {

	function colorizeCode(&$savant, &$text)
	{
		// break the source into blocks by the beginning <code> tag.
		// this will remove the text "<code>" from the block, so
		// we'll need to add it in again later.
		$blocks = explode('<code>', $text);
		
		// loop through each block and convert text within
		// <code></code> tags.
		foreach ($blocks as $key => $val) {
		
			// now find then the ending </code> within the block
			$pos = strpos($val, '</code>');
			
			if ($pos === false) {
				
				// there was no </code> tag -- do nothing
				
			} else {
				
				// change all <php> and </php> tags
				$val = str_replace('<php>', '<?php', $val);
				$val = str_replace('</php>', '?>', $val); // <?php
				
				// $tmp[0] will be the part before </code>, and
				// thus the part we want to modify.
				// 
				// $tmp[1] will be the part after the
				// <code></code> block, which we will leave
				// alone.
				// 
				// this will remove the text "</code>" from the
				// text, so we'll need to add it in again when modifying
				// the text.
				$tmp = explode('</code>', $val);
				
				// set entities by highlighting the string. we do the
				// output buffering ob() thing because the native
				// highlight_string() dumps the output to the screen
				// instead of returning to a variable (before PHP
				// 4.2.2).
				$tmp[0] = trim($tmp[0]);
				
				ob_start();
				highlight_string($tmp[0]);
				$tmp[0] = ob_get_contents();
				ob_end_clean();
				
				// remove break tags from the highlighted text
				$tmp[0] = str_replace("<br />", "\n", $tmp[0]);
				
				// convert tabs to 4-spaces and then
				// re-surround with <code> tags
				$tmp[0] = str_replace("\t", '    ', $tmp[0]);
				
				// save the modified text in the block
				$blocks[$key] = $tmp[0] . $tmp[1];
			}
			
		}
		
		// reassemble the blocks
		$text = implode('', $blocks);
	}
}