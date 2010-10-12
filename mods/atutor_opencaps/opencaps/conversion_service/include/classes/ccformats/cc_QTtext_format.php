<?php
/**
 * QTtext Class
 */
class QTtext extends CaptionFormat
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
		//echo 'show the replaced TQtext:<form><textarea name="textarea" cols="120" rows="5">'. $theCCString. '</textarea></form>';
		
		// clean malformed patterns creted when files were saved on win notepad
        //$toSearch = array(chr(13).chr(10));
        //$toReplace = array(chr(10));
		
		$toSearch = array(chr(13).chr(10),chr(13),chr(10).chr(32).chr(10),chr(10).chr(10) ); // this default break line in Capscribe Desktop - Mac OS
        $toReplace = array(chr(10),chr(10),chr(10),chr(10));
		
        //$toSearch = chr(13); // this default break line in Capscribe Desktop - Mac OS
        //$toReplace = chr(10);
		
		$theCCString = str_replace($toSearch,$toReplace,$theCCString);
	
		// get all lines in array
		$allLines = split(chr(10),$theCCString);
		$controlTime = 0; // control time marks IN and OUT
		$controlCaptionLines = 0; // control the number of lines of one caption 
		$addCaptionControl = 0; //Start Adding caption when = 1
		$isTime=0;
		$captionCounter = 0; // Total number of captions found
		
		$timesIn = array();
		$timesOut = array();
		$captions = array();
		
        // Defining QText known pattenrs;
		$qtPatternRegEx = "/({(.*?)})/";  
		$pattern_QT_time_format_000 = "\[([0-9]{2}:[0-9]{2}:[0-9]{2}.[0-9]{3})\]";
        $pattern_QT_time_format_00 = "\[([0-9]{2}:[0-9]{2}:[0-9]{2}.[0-9]{2})\]";
        $pattern_QT_time_format_selected = '';
	    
        // determine if miliseconds are in 2 of 3 digit format 
        if (preg_match("/$pattern_QT_time_format_000/", $theCCString))
        {
        	$pattern_QT_time_format_selected = $pattern_QT_time_format_000;
        } 
        else if (preg_match("/$pattern_QT_time_format_00/", $theCCString)) 
        {
        	$pattern_QT_time_format_selected = $pattern_QT_time_format_00;
        }
	        
        //  loop through caption lines
        for ($i=0; $i<count($allLines);$i++)
        {
        	//echo '<br/>';
        	//echo '<br/>Line ('.$i.') *** '.($allLines[$i]).'';
        	
        	// is this a time mark ?
        	$isTime = preg_match("/$pattern_QT_time_format_selected/", $allLines[$i]);
        	
        	// if it is a time mark, start converting else is a caption n lines
        	if ($isTime==1)
        	{
        		// clean up time mark '[]' characters
        		$allLines[$i] = str_replace('[','',$allLines[$i]);
        		$allLines[$i] = str_replace(']','',$allLines[$i]);
        		
        		// Is this the First time mark?
	        	if ($controlTime == 0)
	        	{
	        		// ok get the first time mark and wait for the time out mark 
	        		$timesIn[] = $allLines[$i]; // mm this is not java style... FIX ME!!
	        		$controlTime = 2;
	        		// start caption text and wait for n lines
	        		$captions[$captionCounter]= '';
					$addCaptionControl = 1;
	        		$currentIndex = $captionCounter;
	        		$captionCounter++;
	        			//echo '------ This The first time TIME MARK';
	        	}
        		
        	    // Is this the Time IN of the caption?
	        	else if ($controlTime == 1)
	        	{
	        		// add time in
	        		$timesIn[] = $allLines[$i]; // mm this is not java style... FIX ME!!
	        		$controlTime = 2;
	        		// start caption text and wait for n lines
	        		$captions[$captionCounter]= '';
	        		$addCaptionControl = 1;
	        		$currentIndex = $captionCounter;
	        		$captionCounter++;
	        			//echo '------ This is IN TIME MARK';
	        	}

        	    // Is this the Time Out of the caption?
	        	else if ($controlTime == 2)
	        	{
	        		// add time in
	        		$timesOut[] = $allLines[$i]; // mm this is not java style... FIX ME!!
	        		$controlTime = 1;
	        		$addCaptionControl = 0;
	        			//echo '------ This is OUT TIME MARK';
	        	}
	        	
        	} else {
        		
        		// start adding captions
        		if ($addCaptionControl==1)
        		{
        			// add new line to the current caption
        			$captions[$currentIndex] .= $allLines[$i].chr(10);
        			//echo '------ The caption'.$captions[$currentIndex];
        		}
        	} // end if
        	
        } // end for 
        
        // create a collection object
        $myQTextCollection = new CaptionCollection();
        
        /*
        echo '<br/><br/>Total Captions Found:  '.$captionCounter;
        echo '<br/><br/>Total TIME IN:  '.count($timesIn);
        echo '<br/><br/>Total TIME OUT:  '.count($timesOut);
        echo '<br/><br/>Total CAPTIONS:  '.count($captions);
        */
        
        // test in a text area
        	//echo '<form><textarea cols="100" rows="10">';
	    ///*
        for ($j=0;$j<$captionCounter;$j++)
	    {
            
            // get all QT styles found in caption text
            preg_match_all($qtPatternRegEx,$captions[$j],$captionStylesFound);

            // clean all QT styles from the caption  
            $captions[$j] = str_replace($captionStylesFound[0], "", $captions[$j]);
            
            // show the plain text Caption
            //echo '<br/><br/>'.$captions[$j];
            
            /*
            // show all QT found in caption line
            foreach ($captionStylesFound[0] as $allStyleAttrib => $allStyleValue)
            {
            	echo '<br/>'.$allStyleAttrib.' = '.$allStyleValue;
            }
            */        

			$textStyles = Array();
			
            // add text styles features to caption text styles array
            if (isset($captionStylesFound[0]))
            {
                foreach ($captionStylesFound[0] as $txtStyle)
                {
                	//$textStyles[] = $txtStyle;
                	//echo '<br/>'.$txtStyle;
                	
                	//adding only known text styles
                	if ($txtStyle == '{justify:center}')
                	{
                		$textStyles['text-align'] = 'center';
                	} else if ($txtStyle == '{justify:left}')
                	{
                		$textStyles['text-align'] = 'left';
                	}	                		
                	else if ($txtStyle == '{justify:right}')
                	{
                		$textStyles['text-align'] = 'right';
                	} else {
                		// adding QT formats
                		//$textStyles['QT'.$txtStyle] = $txtStyle.'-- NOT used yet';
                	} // end if else
                } // end for each
            } // end if

            // add a capton only if all values are set
                
			if (isset($timesIn[$j]) && isset($timesOut[$j]) && isset($captions[$j]))
			{
				// Create a Caption Object
				$newCaption = new Caption($timesIn[$j],$timesOut[$j],$captions[$j],$textStyles);
                
                // print added caption
                //echo $newCaption->toString(); 

                // add caption object to the collection
                $myQTextCollection->addCaption($newCaption);
                }
            
            /*
            echo chr(10).'IN:  '.$timesIn[$j];
	    	echo chr(10).'OUT:  '.$timesOut[$j];
	    	echo chr(10).'Caption:  '.$captions[$j];
	    	*/
                //echo chr(10).'Caption:  '.$captions[$j];
                
	    }// end for    
	    //*/

			//echo '</textarea></form>';	     
        
			
        // print for test
        //echo $myQTextCollection->toString();
			
        return $myQTextCollection;
	         
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
		
		// add QT header
		$ccExport .= $this->getHeader()."\r\n";
		
		foreach ($myCollection as $captionObj)
		{
			// add time in
			$ccExport .= '['.$captionObj->getInTime().']'."\r\n";
			
			// Convert <br/> into \n character
			$fixCaption = str_replace('<BR />',"\r\n",$captionObj->getCaption());
			//echo '<br/>'.$fixCaption;
			
			// get getTextStyles array from caption object
			$ccTxtStyles = $captionObj->getTextStyles();
			
				//$ccTxtStyles = $this->getDefaultStyles($captionObj->getTextAtribute('text-align'));
			
			//  check if the caption has text styles
			if (count($ccTxtStyles)!=0)
			{
				// loop all txt styles
				foreach ($ccTxtStyles as $txtAttrib => $txtValue)
				{
					// HERE parse any known txt style and apply it to the caption
					
					// only for text alignment in QT
					if ($txtValue=='center' || $txtValue=='left' || $txtValue=='right')
					{
						// get the txt align default values
						$thisTxtAlign = $this->getDefaultStyles($txtValue,strlen($fixCaption));
							//echo '<br/>'.$fixCaption;
							//echo '<br/> Lenght: '.strlen($fixCaption);
						
						// add default text styles to the caption 
						$fixCaption = $thisTxtAlign.$fixCaption;
					} // end if
					
				} // end for
					
			} // end if txt styles
			
			// add caption(s) to export string 
			//$ccExport .= $fixCaption;
			$ccExport .= $fixCaption."\r\n";
			
			// add time out
			$ccExport .= '['.$captionObj->getOutTime().']'."\r\n";
			
		} // end foreach
		
		
		// Remove any doble break line (already done in import string.. but just in case captions lines bring this issue;) 
		//-- $ccExport = str_replace(chr(10).chr(10),chr(10),$ccExport);
		
		return $ccExport;
		
	} // end  exportCC()
	
	/**
	 * Verify if the caption file is a QText caption file
	 *
	 * @param String $theCCString A caption file as text = string
	 * @return Boolean
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

	/**
	 * Get the name of the Caption Format Class
	 *
	 * @return String $captionFormatname The name of the Caption Format
	 */
	public function getName()
	{
		$captionFormatname = 'Quick Time Text';
		
		return $captionFormatname;
	}
	
	/**
	 * Get the Description of the Caption Format
	 *
	 * @return String $captionAbout Description of the Caption Format
	 */
	public function getAbout()
	{
		$captionAbout = 'This format can be used by Quick Time Player.
		This is the native format of CAPSCRIBE !!! 
		Additional information can be found at <a href="http://www.apple.com/quicktime/tutorials/texttracks.html">Apple Website</a>';
		
		return $captionAbout;
		
		
	}
	
	/**
	 * Get the version of the caption format class
	 *
	 * @return String $theVersion The Version of the Caption Format as String
	 */
	public function getVersion()
	{
		$theVersion = '1.0';
		 
		return $theVersion;
	}
		
	public function getFileExtension()
	{
		$theExtension = 'txt';
		 
		return $theExtension;
	}
	
	public function getIdPattern()
	{
		$idPattern = '/({QTtext})/';
		//$idPattern = "/({(.*?)})/";
		//$qtPatternRegEx = "/({(.*?)})/";
		
		return $idPattern;
	}
	
	public function allowsTextStyles()
	{
		return '1';
	}	

	public function template()
	{
		$ccTemplate = '
{QTtext}{font: Comic Sans MS}{italic}{size:18}{textColor: 65280,65280,65280}{backColor: 0,0,0}{justify:center}{timeScale: 1000}{timeStamps:absolute}{keyedtext:on}{language:0}{height:156}{width:383}{textEncoding:0}
[00:00:00.002]
caption 1 line1
caption 1 line2
[00:00:02.849]
[00:00:02.851]
caption 2 line1
caption 2 line2
[00:00:04.950]
[00:00:04.952]
caption 3
[00:00:06.082]

... or ...

{QTtext} {font:Tahoma}
{plain} {size:20}
{timeScale:30}
{width:160} {height:32}
{timeStamps:absolute} {language:0}
[00:00:00.00]
caption 1 - using Bold style?
[00:00:02.84]
 
[00:00:02.85]
caption 2
[00:00:04.95]
 
[00:00:04.95]
caption 3
[00:00:06.08]

';
		
		return $ccTemplate;
	}

/*////////////////////////////////////////////////////////
        Functions for QText conversion
//////////////////////////////////////////////////////*/
	
	/**
	 * Creates the QText default header.. soon this method will receive all the needed parameters
	 *
	 * @return String $myHeader The QText default header
	 */
	private function getHeader()
	{
		/*
		{font: Tahoma}
		{plain}
		{size:18}
		{textColor: 45824,45824,45824}
		{backColor: 0,0,0}
		{justify:center}
		{timeScale: 1000}
		{timeStamps:absolute}
		{keyedtext:on}
		{language:0}
		{height:114}
		{width:334}
		{textEncoding:0}
		 */
		
		// common vars
		$textFont = "Comic Sans MS";
		$textSize = "18";
		$textJustify = "center";

		// Unique QText header Variables		
		$myHeader = '{QTtext}';
		$myHeader .= '{font: '.$textFont.'}';
		//$myHeader .= '{plain}';
		//$myHeader .= '{size: '.$textSize.'}';
		$myHeader .= '{textColor: 65280,65280,0}';
		$myHeader .= '{backColor: 0,0,0}';
		$myHeader .= '{justify:'.$textJustify.'}';
		$myHeader .= '{timeScale: 1000}';
		$myHeader .= '{timeStamps:absolute}';
		$myHeader .= '{keyedtext:on}'; ///  WHAT IS THIS????? FIX ME
		$myHeader .= '{language:0}'; // working on ...
		$myHeader .= '{width:114}'; // working on ...
		$myHeader .= '{height:334}'; // working on ...
		$myHeader .= '{textEncoding:0}';
		// ... and much more can be set here acording to QT specification ...
		
		/*
		{anti-alias: onOrOff }
		{doNotDisplay: onOrOff }
		{doNotAutoScale: onOrOff }
		*/
		
		$myHeader = '{QTtext}{font: Tahoma}{plain}{size:18}{textColor: 45824,45824,45824}{backColor: 0,0,0}{justify:center}{timeScale: 1000}{timeStamps:absolute}{keyedtext:on}{language:0}{height:114}{width:334}{textEncoding:0}';
		
		return $myHeader;
	}	// end getHeader
	
	/**
	 * Sets default QT text styles at the caption level. No text style implemented yet at the word level 
	 * @param String $txtAlign The text alignment, (e.g. 'left', 'right') if null assumed center
	 * @return String $myStyles the QText styles to be added before the caption text.
	 */
	private function getDefaultStyles($txtAlign,$txtLength )
	{
		// {hilitecolor:3000,0,0}{hilite:0,103}{font:Tahoma}{size:18}{textcolor:45824,45824,45824}{backcolor:0,0,0}{justify:center}{plain}( 
		$myStyles = '';
		//$myStyles .= '{hilitecolor:3000,0,0}{hilite:0,139}{font:Tahoma}';
		$myStyles .= '{hilitecolor:3000,0,0}{hilite:0,'.$txtLength.'}{font:Tahoma}{size:18}{backcolor:0,0,0}';
		
		if ($txtAlign == 'left')
		{
			$myStyles .= '{textcolor:65280,55040,18432}{justify:'.$txtAlign.'}';
		} 
		else if ($txtAlign == 'right')
		{
			$myStyles .= '{textcolor:26112,65280,65280}{justify:'.$txtAlign.'}';
		}
		else 
		{
			$myStyles .= '{textcolor:45824,45824,45824}{justify:center}';
		}
		// and much more to be added here... !! 
		
		/**
		{plain} Plain text.
		{bold} Bold text.
		{italic} Italic text.
		{underline} Underlined text.
		{outline} Outlined text.
		{shadow} Text with a drop shadow.
		{condense} Text with spacing between characters decreased.
		{extend} Text with spacing between characters increased. 
		 */
		//$myStyles .= '{plain}';
		
		return $myStyles;
	
	}
}  // end classQText 
?>