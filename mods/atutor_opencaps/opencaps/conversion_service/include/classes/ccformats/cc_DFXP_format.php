<?php
/**
 * DFXP Class
 */
class DFXP extends CaptionFormat
{
	private $textStyles = array();  
	

	/**
	 * Imports a caption string into a CaptionCollection 
	 *
	 * @param String $theCCString the caption file as string
	 * @return CaptionCollection $myCcCollection A CaptionCollection Object
	 */
	public function importCC($theCCString) {
		
		// include classes for XML parser
		include_once('include/classes/core_XmlTag.php');
		include_once('include/classes/core_XmlTagCollection.php');
		include_once('include/classes/core_XmlTagTrace.php');
		
		// create a xml parser 
		$myXmlParse = new XmlTagTrace($theCCString,'P',4);
		
		// print xml collection
		//$myXmlParse->toString();
		
		// get xml collection
		$myXmlTagCollection = $myXmlParse->getCollection();
		
        //include_once('CaptionCollection.php');
        $myCcCollection = new CaptionCollection();

        foreach ($myXmlTagCollection as $xmlTagObj) 
        {		
        	//echo '<br/>Begin Time: '.$xmlTagObj->getTagAttribute('BEGIN');
        	//echo '<br/>End Time: '.$xmlTagObj->getTagAttribute('END'); 
        	//echo '<br>Caption:'.$xmlTagObj->getTagValue();
        	
        	$txtStylesArr = Array();
        	$newCaption = new Caption($xmlTagObj->getTagAttribute('BEGIN').'0',$xmlTagObj->getTagAttribute('END').'0',$xmlTagObj->getTagValue(),$txtStylesArr);
        	
        	// add captions to the collection
			$myCcCollection->addCaption($newCaption);
			
        } // end for 
	
	    //$myCcCollection->toString();
	        
        return $myCcCollection;
	         
	} // end importCC()
	
	/**
	 * Exports a CaptionCollection object into a string
	 *
	 * @param CaptionCollection $theCollection A CaptionCollection Object
	 * @return String $captionString The caption as a String
	 */
	public function exportCC($theCollection)
	{
		$ttCaption = '';
	
		$myCollection = $theCollection->getCollection();
		
		// add header
		$ttCaption .= $this->getTTHeader();
		
		// Caption counter
		$capCount = 0;

		foreach ($myCollection as $captionObj)
		{
			$capCount++;
			
			// adding caption number - for debug purpuses 
			//$captionObj->getCaption() = "[CAP no. $capCount]" . " ". $captionObj->getCaption();
			
			// adapt \n character to <br/>
			//$fixCap = "[CAP no. $capCount] : ". CcUtil::ccNewLineToBr($captionObj->getCaption(),' <br/>'); 
			$fixCap = "". TxtFileTools::ccNewLineToBr($captionObj->getCaption(),' <br />');

			// convert qt to TT time format
			$ttTimeIn = $this->timeQtToTT($captionObj->getInTime());
			$ttTimeOut = $this->timeQtToTT($captionObj->getOutTime());
	
			// ading TTcaptions
			$ttCaption .= "".$this->getTTCaption($ttTimeIn,$ttTimeOut,$fixCap,$captionObj->getTextStyles());

			// show caption object
			//$captionObj->toString();
			
		} // end foreach
		
		//  close TT file
		$ttCaption .= $this->getTTClose();
		
		return $ttCaption;
		
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
		return 'Timed Text (TT) Authoring Format 1.0 – Distribution Format Exchange Profile (DFXP)';
	}
	
	public function getAbout()
	{
		return 'This is the most comprehensive caption format ever !!
		XML based.
		Additional information at <a href="http://www.w3.org/TR/2006/CR-ttaf1-dfxp-20061116/">W3-dfxp</a>';
	}
	
	public function getVersion()
	{
		return '1.0';
	}
		
	public function getFileExtension()
	{
		return 'dfxp.xml';
	}
	
	public function getIdPattern()
	{
		$idPattern = '/(<tt xmlns="http:\/\/www.w3.org\/2006\/04\/ttaf1)/';
		
		return $idPattern;
	}
	
	public function allowsTextStyles()
	{
		return '1';
	}
	
	public function template()
	{
		
	}


/*////////////////////////////////////////////////////////
        Functions for TT - Timed Text Conversion
//////////////////////////////////////////////////////*/


/**
 * creates a TT Caption 
 * @return String $ttCaption A TT formatted caption 
 * @param string $capInTime Caption start time (e.g 0:00:00.80)
 * @param string $capOutTime Caption end time (e.g 0:00:00.80)
 * @param String $caption caption, with all styles
 * @param Array $txtStyles Array with text styles in the caption
 */
private function getTTCaption($capInTime,$capOutTime,$caption,$txtStyles)
{
        
// <p begin="0:00:00.00" end="0:00:00.80">1 begining</p>

        $ttCaption = "";
        
		// Find if text alignment in $txtStyles array
		if (isset($txtStyles['text-align']))
		{
			if ($txtStyles['text-align']=='right')
			{
        // Add right alignment style
        $ttCaption = '
        <p begin="'.$capInTime.'" end="'.$capOutTime.'" style="txtRight">'.$caption.'</p>';				
			} 
			else if ($txtStyles['text-align']=='left')
			{
        // Add right alignment style
        $ttCaption = '
        <p begin="'.$capInTime.'" end="'.$capOutTime.'" style="txtLeft">'.$caption.'</p>';
			}
			else if ($txtStyles['text-align']=='center')
			{
        $ttCaption = '
        <p begin="'.$capInTime.'" end="'.$capOutTime.'">'.$caption.'</p>';

			}
			
		} else {
			// no text style, creates a plain caption   
        $ttCaption = '
        <p begin="'.$capInTime.'" end="'.$capOutTime.'">'.$caption.'</p>';
		
		} // end if
        


        return $ttCaption;
        
} // end getTTCaption()

/**
 * Converts QT time to TT Time; drops last right number on the time format
 * @return String $ttTime TT time format 00:01:10.28"
 * @param String $qtTime QT time Format; "00:01:10.280"
 */
private function timeQtToTT($qtTime)
{
        $ttTime = substr($qtTime, 0, 11); // returns "d"   ;
        return $ttTime;
} // end timeQtToTT

/// TT Header 
private function getTTHeader()
{
// common vars
$textFont = "Arial";
$textFontWeight = "normal";
$textFontStyle = "normal";
$textSize = "12";
$textJustify = "center";

// unique vars 
$capTitle = 'This is a sample SAMI 1.0 caption';
$textHtmlColor = "white";
$bgHtmlColor = "black";
$capLangName = "English";
$capLangCode= "EN-US-CC";

$capTT_header = '<?xml version="1.0" encoding="UTF-8"?>
<tt xmlns="http://www.w3.org/2006/04/ttaf1"
      xmlns:tts="http://www.w3.org/2006/04/ttaf1#styling" xml:lang="en">
  <head>
    <styling>
      <style id="txtRight" tts:textAlign="right" tts:color="cyan"/>
      <style id="txtLeft" tts:textAlign="left" tts:color="#FCCA03"/>
      <style id="defaultSpeaker" tts:fontSize="'.$textSize.'px" tts:fontFamily="'.$textFont.'" tts:fontWeight="'.$textFontWeight.'" tts:fontStyle="'.$textFontStyle.'" tts:textDecoration="none" tts:color="'.$textHtmlColor.'" tts:backgroundColor="'.$bgHtmlColor.'" tts:textAlign="'.$textJustify.'" />
      <style id="defaultCaption" tts:fontSize="14px" tts:fontFamily="SansSerif" tts:fontWeight="normal" tts:fontStyle="normal" tts:textDecoration="none" tts:color="white" tts:backgroundColor="black" tts:textAlign="left" />
    </styling>
  </head>
  <body id="ccbody" style="defaultSpeaker">
    <div xml:lang="en">';
    
        return $capTT_header;

} // end getTTHeader()


// Time Text Close
private function getTTClose()
{
// alternative close	"<div xml:lang="en"/>"
$capTT_close = '
    </div>
  </body>
</tt>'
;
    return $capTT_close;
}
	
}  // end classQText 
?>



