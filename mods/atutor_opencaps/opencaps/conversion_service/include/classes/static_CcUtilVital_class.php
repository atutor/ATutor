<?php
class CcUtilVital
{

	/**
	 * Loads all Caption Formats and return an array with class names
	 * Essential !!
	 *
	 */
	static public function ccFormatsLoad()
	{
		global $rosettaCCSettings; // load global vars
		
		$ccFormats = array(); // stores all caption formats allowed
		
		$dir = $rosettaCCSettings['ccformats'];
		$ccFormatsString = '';
		$ccFileNameFormatRegex = '/cc_(.*?)_format/'; // (e.g. "cc_(QTtext)_format.php")
		$countCcFiles = 0;

		// verify if the directory exists
		if ($handle = opendir($dir)) 
		{
		    // read all files in the dir and include Caption Format files
		    while (false !== ($file = readdir($handle))) 
		    {
		        if (!is_dir($file))
		        {
		        	// build a string with all file names
		        	$ccFormatsString .= ''.$file.';';
		        	
		        	// include the caption format file
					include_once($dir.'/'.$file);
		        	
		        	$countCcFiles++;
		        }
			}
		} 
			// close dir handler 
			closedir($handle);

			// get all Class names from the string using regex
			preg_match_all($ccFileNameFormatRegex, $ccFormatsString, $ccFormatsPat);
			
			// add Class names to $ccFormats global Array
			for ($j=0;$j<count($ccFormatsPat[1]);$j++)
			{
				$ccFormats[] = $ccFormatsPat[1][$j];
			}
			// sort $ccFormats array 
			asort($ccFormats);
			
			return $ccFormats;
			 
	} // ccFormatsLoad() end

	
	
	
} // end class CcUtilEssential  
?>