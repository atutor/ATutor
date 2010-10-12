<?php
/**
 * SubRipSrt Class
 */
class SubRipSrt extends CaptionFormat
{
	private $textStyles = array();

	/**
	 * Imports a caption string into a CaptionCollection 
	 *
	 * @param String $theCCString the caption file as string
	 * @return CaptionCollection $myCcCollection A CaptionCollection Object
	 */
	public function importCC($theCCString) 
	{
		$ccImport = '';

        // clean malformed patterns creted when saving files on win notepad
        $toSearch = array(chr(13).chr(10));
        $toReplace = array(chr(10));
        $theCCString = str_replace($toSearch,$toReplace,$theCCString);
                
        // split each caption by \n\n
        $allCaptions=split(chr(10).chr(10),$theCCString);
                
        // create a collection object
        $myCollection = new CaptionCollection();
        
        $txtStyles = Array();
        
        $counter=0;
        
        foreach($allCaptions as $singleCaption)
        {
        	
        	//echo '<br/>'.chr(10).''.$counter;
        	
        	// split each line of the single caption
        	$captionParts=split(chr(10),$singleCaption);
        	
        	// add captions if minimal time and caption are set
        	if (isset($captionParts[1]) && isset($captionParts[2]))
        	{
	        	$counter++;
	        	$timeMark = '';
	        	$captionLines = '';
	        	
	        	// fix milisecond separator "," by "."
	       		$captionParts[1] = str_replace(',','.',$captionParts[1]);	
	        	
	        	// get time marks on line 2
	        	$timeMark = split('-->',$captionParts[1]);
	
	        	// get time in and out
	        	$timeIn = trim($timeMark[0]);
	        	$timeOut = trim($timeMark[1]);
	        	
				$captionLines = $captionParts[2]; // add caption line 1
				
	        	// if caption has two lines
	        	if (count($captionParts)==4)
	        	{	
	        		$captionLines .= ''.chr(10).$captionParts[3]; // add a new line + caption line 2
	        	}
	        	
	        	// Create a caption Object
	        	$theNewCaption = new Caption($timeIn,$timeOut,$captionLines,$txtStyles);
	        	
				// add caption to CaptionCollection
				$myCollection->addCaption($theNewCaption); 
	
	        	
	        	//echo ''.chr(10).'IN: '.$timeIn.'****'.' OUT:'.$timeOut;
	        	//echo ''.chr(10).''.$captionLines;
        	}
        	
        } // end foreach
	
	    return $myCollection;
	         
	} // end importCC()

	/**
	 * Exports a CaptionCollection object into a string
	 *
	 * @param CaptionCollection $theCollection A CaptionCollection Object
	 * @return String $captionString The caption as a String
	 */
	public function exportCC($theCollection)
	{
		
		$ccExport = '';
	
		$myCollection = $theCollection->getCollection();
  
		// fix time Srt Time Format
		$toSearch = array('.');
        $toReplace = array(',');
        
		$srtCounter = 0;
		
		foreach ($myCollection as $captionObj)
		{
			
			$srtCounter++;
			
			// fix QT time to SRT format, replace "." by ","
			$srtInTime = $captionObj->getInTime();
			$srtInTime  = str_replace($toSearch,$toReplace,$srtInTime);
			
			$srtOutTime = $captionObj->getOutTime();
			$srtOutTime  = str_replace($toSearch,$toReplace,$srtOutTime);
			
			$srtCaption = $captionObj->getCaption();
			$srtCaption = str_replace('<BR/>',chr(10),$srtCaption);
			$srtCaption = str_replace('<br/>',chr(10),$srtCaption);
			 
			$ccExport .= "$srtCounter\n".$srtInTime." --> ".$srtOutTime."\n".$srtCaption."\n\n";
			
		} // end foreach
		
		// Fix if there are more than the two empty line separator (standard)
		$ccExport = str_replace(chr(10).chr(10).chr(10),chr(10).chr(10),$ccExport);
		
		return $ccExport;
		
	} // end  exportCC()
	
	/**
	 * Verify if the caption file is a QText caption file 
	*/
	public function checkFormat($theCCString)
	{
		$isValid = false;
		$patternCheck = "/({(QTtext)})/"; // RegExp to look for QText 
		preg_match_all($patternCheck,$theCCString,$patternFound);
		
		if(count($patternFound)>0)
		{
			$isValid = true;
		}

		return $isValid;
		
	} // end  checkFormat()	

	/*
	 * Here functions to re-define
	 */
	public function getName()
	{
		return 'SubRip - Srt';
	}
	
	public function getAbout()
	{
		return '???';
	}
	
	public function getVersion()
	{
		return '???';
	}
		
	public function getFileExtension()
	{
		return 'srt';
	}
	
	public function getIdPattern()
	{
		$idPattern = '/([0-9]{2}:[0-9]{2}:[0-9]{2},[0-9]{3}) (-->) ([0-9]{2}:[0-9]{2}:[0-9]{2},[0-9]{3})/';
		//$idPattern = '/([0-9]{2}:[0-9]{2}:[0-9]{2},[0-9]{3})/';
		//$idPattern = '';
		//$idPattern .= '/'; // start pattern
		
		//$idPattern .= '([0-9]{2}\n)'; 
		 
		//$idPattern .= '/'; // end pattern

		return $idPattern;
	}
	
	public function allowsTextStyles()
	{
		return '0';
	}	
	
	public function template()
	{
		$ccTemplate = '
1
00:00:42,360 --> 00:00:48,360
With this device we can
give anything an attitude.

2
';
		
		return $ccTemplate;
	}
}  // end SubRipSrt 
?>