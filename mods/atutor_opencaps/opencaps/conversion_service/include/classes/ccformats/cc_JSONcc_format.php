<?php
/**
 * JSONcc Class
 */
class JSONcc extends CaptionFormat
{
	/**
	 * Imports a caption string into a CaptionCollection 
	 *
	 * @param String $theCCString the caption file as string
	 * @return CaptionCollection $myCcCollection A CaptionCollection Object
	 */
	public function importCC($theJsonString)
	{		
		$theCcArray = json_decode($theJsonString,true);
		
		// Create a Caption Collection Object
		$theCollection = new CaptionCollection();
	
		// set all global text styles using JSON-decoded array ???????
		$theCollection->setTxtStylesGlobal($theCcArray['global_caption_styles']);

		// check if there are captions in the captionCollection array and then add them to the Collection
		if (count($theCcArray['clip_collection']['clips'])!=0 )
		{			
			foreach ($theCcArray['clip_collection']['clips'] as $CapArray)
			{
				// create a Caption Object
				$newCaptionObj = new Caption($CapArray['inTime'],$CapArray['outTime'],$CapArray['caption_text'],$CapArray['caption_styles']);
								
				// add caption object to the CaptionCollection 
				$theCollection->addCaption($newCaptionObj);
			} // end foreach	
		}// end if
				
		return $theCollection; 
		
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
  
		// Use php built-in json encoder
		$ccExport = json_encode($theCollection);

		return $ccExport;
		
	} // end  exportCC()
	
	/**
	 * Verify if the caption file is a JSON caption file 
	*/
	public function checkFormat($theCCString)
	{
		
	} // end checkFormat()

	/*
	 * Here functions to re-define
	 */
	public function getName()
	{
		return 'JSON';
	}
	
	public function getAbout()
	{
		return 'JSON is a data representation model + much more. Captions can be played on any browser/OS using JavaScrinpt. However, plaing the video/audio binary files will require additional plugins such as QuickTime, Windows Media Player, etc... in order to load in a browser ';
	}
	
	public function getVersion()
	{
		return '1.0';
	}
		
	public function getFileExtension()
	{
		return 'JSON';
	}
	
	public function getIdPattern()
	{
		//$idPattern = '/(.*)/'; // match any pattern
		$idPattern = '/"clips":\[/'; 
		
		
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

	
	
} // end CCJSON Class
?>