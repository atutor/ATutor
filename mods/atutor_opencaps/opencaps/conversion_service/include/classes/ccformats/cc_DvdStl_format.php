<?php
/**
 * DvdStl Class
 */
class DvdStl extends CaptionFormat
{
	/**
	 * Imports a caption string into a CaptionCollection 
	 *
	 * @param String $theCCString the caption file as string
	 * @return CaptionCollection $myCcCollection A CaptionCollection Object
	 */
	public function importCC($theCCString)
	{
		
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
		
		foreach ($myCollection as $captionObj)
		{
			$fixCap = TxtFileTools::ccNewLineToBr($captionObj->getCaption(),' ');
			
			$ccExport .= $captionObj->getInTime().', '.$captionObj->getOutTime().", ".$fixCap."\n\n";
			
		} // end foreach
		
		return $ccExport;
		
	} // end  exportCC()
	
	/**
	 * Verify if the caption file is a srt caption file 
	*/
	public function checkFormat($theCCString)
	{
		
	} // end checkFormat()
	
	/*
	 * Here functions to re-define
	 */
	public function getName()
	{
		return 'Spruce STL';
	}
	
	public function getAbout()
	{
		return 'Caption format used by DVD Studio and Avid in mac OS.
		Very popular.
		This format can be used to create DVD captions on Mac OS';
	}
	
	public function getVersion()
	{
		return '???';
	}
		
	public function getFileExtension()
	{
		return 'stl';
	}
	
	public function getIdPattern()
	{
		//$idPattern = '/([0-9]{2}:[0-9]{2}:[0-9]{2}.[0-9]{2})(.*)([0-9]{2}:[0-9]{2}:[0-9]{2}.[0-9]{2})(.*)/';
		$idPattern = '/unknownZZZ/';
		//00:00:03:24 , 00:00:06:29 , Did you read the paper today?
		return $idPattern;
	}

	public function allowsTextStyles()
	{
		return '1';
	}
	
	public function template()
	{
		$ccTemplate = '//English subtitles
$FontName           = Arial
$FontSize           = 36
$HorzAlign          = Center
$VertAlign          = Bottom
$XOffset            = 0
$YOffset            = 0
$ColorIndex1        = 0
$ColorIndex2        = 2
$ColorIndex3        = 8
$ColorIndex4        = 3
$Contrast1          = 15
$Contrast2          = 0
$Contrast3          = 15
$Contrast4          = 0
$ForceDisplay       = FALSE
$FadeIn             = 3
$FadeOut            = 7
$TapeOffset         = FALSE

00:00:03:24 , 00:00:06:29 , Did you read the paper today?
00:00:10:07 , 00:00:12:21 , No, did Edwards quote me right?
00:00:14:19 , 00:00:25:28 , Actually, ^IBrillstein^I said you were | unimaginably full of yourself
';
	}

} // end Dvd Class

?>