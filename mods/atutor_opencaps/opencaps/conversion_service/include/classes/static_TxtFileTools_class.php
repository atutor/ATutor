<?php
class TxtFileTools
{


	/**
	 * Saves a String into a file and return the file URL
	 *
	 * @param String $theFileName The file Name
	 * @param String $theString The value to save as a String
	 * @return String $fullFileUrl The full path of where file is saved  
	 */
	static public function stringToFile($theFileName, $theString) 
	{
		global $rosettaCCSettings;
		
		$fullFileUrl = $rosettaCCSettings['uploadDir'].'/'.$theFileName;
		
		// save file to disk 
		file_put_contents($fullFileUrl, $theString);
		
			//echo '<br/><br/>Target Caption File saved in: <b>'.$theFileName.'</b> <a href="'.$fullFileUrl.'"> [Download Caption]</a>';
		
		return $fullFileUrl;
		
	} // stringToFile

	/**
	 * Loads a Caption File and return it as String
	 * @param $theFileName The full URL of the file
	 * @return $ccString The caption file as String
	 */
	static public function fileToString($theFileName) 
	{
		global $rosettaCCSettings;
		
		$ccString = file_get_contents($rosettaCCSettings['uploadDir'].'/'.$theFileName);
		
		/*
          clean malformed pattern for ANSI files:
          This is harmless for any well formed caption.
          fixing captions saved in win notepad
        */
        $toSearch = array(chr(13).chr(10));
        $toReplace = array(chr(10));
        $contents = str_replace($toSearch,$toReplace,$ccString);
        
        return $ccString;
	}	

	/**
	 * Forces a browser to download a file stored in the server 
	 * @param $exfile The full URL of the file
	 */
	static public function downloadFile($exfile) 
	{
		// verify if the file exists
		if (file_exists($exfile)) 
		{
			header('Content-Description: File Transfer');
			header('Content-Type: application/octet-stream');
			header('Content-Disposition: attachment; filename='.basename($exfile));
			header('Content-Transfer-Encoding: binary');
			header('Expires: 0');
			header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
			header('Pragma: public');
			header('Content-Length: ' . filesize($exfile));
			//header("Content-type: $type"); ????????
			ob_clean();
			flush();
			readfile($exfile);
			exit;
	                
		} // end if
	        
	} // end downloadFile()	

	/**
	 * Replaces all <br/> tags in a string with chr(10) characters
	 *
	 * @param String $theHtmlString The string containign <br> HTML tags 
	 * @return unknown
	 */
	static public function ccBrToNewLine($theHtmlString)
	{
		$a = array(chr(10));
		$b = array('<br>','<BR>','<br/>','<BR/>' );
		$stringWithBreakLines = str_replace($a, $b, $theHtmlString);
		return $stringWithBreakLines;
	}

	/**
	 * Replaces the break line character "char(10)" by a specified character 
	 *
	 * @param String $theCaption The caption containing char(10) characters as line separators 
	 * @param char $theChar The String used to replace the break line character
	 * @return unknown
	 */static public function ccNewLineToBr($theCaption, $theChar)
	{
		$a = ''.chr(10);
		$b = $theChar;
		$newString = str_replace($a, $b, $theCaption);
		return $newString;
	}
	
} // end class TxtFileTools
?>