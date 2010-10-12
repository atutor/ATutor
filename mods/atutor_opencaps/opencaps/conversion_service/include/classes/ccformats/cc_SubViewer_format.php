<?php
/**
 * SubViewer Class
 */
class SubViewer extends CaptionFormat
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
			// add in time
			$ccExport.= chr(10).$captionObj->getInTime();
			
			$ccExport.= ',';
			
			// add out time
			$ccExport.= $captionObj->getOutTime();
			
			// fix caption to subViewer v. 1
			$fixCaption = str_replace(chr(10),'',$captionObj->getCaption());
			$fixCaption = str_replace('<BR/>','',$fixCaption);
			
			// add caption
			$ccExport.= chr(10).$fixCaption.chr(10);

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
		return 'SubViewer - Sub';
	}
	
	public function getAbout()
	{
		return '????';
	}
	
	public function getVersion()
	{
		return '???';
	}
		
	public function getFileExtension()
	{
		return 'sub';
	}
	
	public function getIdPattern()
	{
		$idPattern = '/([0-9]{2}:[0-9]{2}:[0-9]{2},[0-9]{2})(,)([0-9]{2}:[0-9]{2}:[0-9]{2},[0-9]{2})/';

		return $idPattern;
	}
	
	public function allowsTextStyles()
	{
		return '0';
	}
	
	public function template()
	{
		$ccTemplate = '
00:04:35.03,00:04:38.82
Hello guys... please seat down...

00:05:00.19,00:05:03.47
M. Franklin,[br]are you crazy?

????
[00:00:00]
caption 1 - using Bold style?
[00:00:02]

';
		
		return $ccTemplate;
	}
	
} // end SubViewer Class
?>