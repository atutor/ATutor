<?php
/**
 * Sami Class
 */
class Sami extends CaptionFormat
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
		$ccTarget = '';
		
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
	
		// start SAMI Caption
		$samiCaption = "";
	
		// add header
		$samiCaption .= $this->getSamiHeader();
		
		// Caption counter
		$capCount = 0;

		// Building Sami caption 
		$fileContent = '';
		foreach ($myCollection as $captionObj)
		{
	
			$capCount++;

			// convert to qt to Sami time format
			$samiTimeIn = TimeUtil::timeQtToSami($captionObj->getInTime());
			$samiTimeOut = TimeUtil::timeQtToSami($captionObj->getOutTime());
			
			$captionStyles = $captionObj->getTextStyles();
			
			$fixCap = TxtFileTools::ccNewLineToBr($captionObj->getCaption(),' <br/>');
			
			// ading caption to String 
				//$samiCaption .= "". $this->getSamiCaption($samiTime,$fixCap,"QT");
			
			// new adding empty caption when time out
			$samiCaption .= "". $this->getSamiCaption($samiTimeIn,$samiTimeOut,$fixCap,$captionObj->getTextStyles());
        
		} // end for each caption 

		//  close SAMi file
		$samiCaption .= $this->getSamiClose();
		
		$samiCaption .= "";
		
		$ccExport = $samiCaption;
				
		return $ccExport;
		
	} // end  exportCC()
	
	/**
	 * Verify if the caption file is a SAMI caption file 
	*/
	public function checkFormat($theCCString)
	{
		
	} // end checkFormat()

	/*
	 * Here functions to re-define
	 */
	public function getName()
	{
		return 'SAMI';
	}
	
	public function getAbout()
	{
		return 'This caption format can be played by Windows Media Player on Windows OS. ';
	}
	
	public function getVersion()
	{
		return '1.0';
	}
		
	public function getFileExtension()
	{
		return 'smi';
	}
	
	public function getIdPattern()
	{
		$idPattern = '/(<SAMI>)/';
		
		return $idPattern;
	}

	public function allowsTextStyles()
	{
		return '1';
	}
	
	public function template()
	{
		$ccTemplate = '';
		
		return $ccTemplate;
	}


/*////////////////////////////////////////////////////////
        Functions for SAMI conversion
//////////////////////////////////////////////////////*/

/**
 * creates a SAMI Caption 
 * @return String $samiCaption A SAMI formatted caption 
 * @param int $capInTime Caption start time in miliseconds 1 sec = 1000
 * @param int $capOutTime Caption end time in miliseconds 1 sec = 1000
 * @param String $caption caption, with all styles
 * @param Array $txtStyles Array with text styles in the caption 
 */
private function getSamiCaption($capInTime,$capOutTime,$caption,$txtStyles)
{
	$samiCaption = "";
	$captionReFormated = "";
                
	// Find if text alignment in $txtStyles array
	if (isset($txtStyles['text-align']))
	{
		if ($txtStyles['text-align']=='right')
		{
			$caption = '<table align=right><span style="color:#00FFFF">'.$caption.'</span></table>';
		}
		else if ($txtStyles['text-align']=='left')
		{
			$caption = '<table align=left><span style="color:#FCCA03">'.$caption.'</span></table>';
		}
		else if ($txtStyles['text-align']=='center')
		{
			$caption = '<table align=center>'.$caption.'</table>';
		}
	}
                
                
    // Create SAMI Caption
	$samiCaption = '
        <SYNC start='.$capInTime.'><P>'.$caption.'</P></SYNC>';
                
        // add an empty caption when caption finishes if caption's lenght is more than x sec
        // prevents caption for displaying it if next caption is to ahead  //// still working
		$captionLenght = $capOutTime-$capInTime; 
        if ($captionLenght>2000)
        {
	$samiCaption .= '
        <SYNC start='.$capOutTime.'><P>&nbsp;</P></SYNC>';
        }
        
        return $samiCaption;
        
} // end getSamiCaption


// SAMI Header 
private function getSamiHeader()
{
// common vars
$textFont = "Arial";
$textFontWeight = "normal";
$textSize = "18";
$textJustify = "center";

// unique vars for SAMI
$capTitle = 'This is a sample SAMI 1.0 caption';
$textHtmlColor = "#FFFFFF";
$bgHtmlColor = "#000000";
$capLangName = "English";
$capLangCode= "EN-US-CC";

$capSAMI_header = '<SAMI>
<HEAD>
<TITLE>'.$capTitle.'</TITLE>
<STYLE TYPE="text/css">
<!--
P { margin:  2px 20% 0px 20%; font-size:'.$textSize.'; font-family: '.$textFont.'; font-weight: '.$textFontWeight.'; color: '.$textHtmlColor.'; background-color: '.$bgHtmlColor.'; text-align: '.$textJustify.'; }
.ENUSCC { name: '.$capLangName.'; lang: '.$capLangCode.'; }
.txtRight { font-size:'.$textSize.'; font-family: '.$textFont.'; font-weight: '.$textFontWeight.'; color: #00FFFF; background-color: '.$bgHtmlColor.'; text-align: right; } 
.txtLeft { font-size:'.$textSize.'; font-family: '.$textFont.'; font-weight: '.$textFontWeight.'; color: #FCCA03; background-color: '.$bgHtmlColor.'; text-align: left; } 
-->
</STYLE>
</HEAD>
<BODY>';
    return $capSAMI_header;
} // end getSamiHeader()


// SAMI Close
private function getSamiClose()
{
$capSAMI_close = '
</BODY>
</SAMI>';
    return $capSAMI_close;
}

	
	
} // end CCsami Class
?>