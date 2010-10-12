<?php
/**
 * MPlayer Class
 */
class MPlayer extends CaptionFormat
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
		return'';
			         
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
			
		} // end foreach
		
	} // end  exportCC()
	
	/**
	 * Verify if the caption file is a QText caption file 
	*/
	public function checkFormat($theCCString)
	{
		return '';
		
	} // end  checkFormat()	

	/*
	 * Here functions to re-define
	 */
	public function getName()
	{
		return 'MPlayer';
	}
	
	public function getAbout()
	{
		return 'This is the native format used by the popular and open source Mplayer video player. 
		If you have a video file, Mplayer will be able to play it almost on any OS (Win, Mac, Linux), and use many different caption formats as well !!
		The best candidate !!';
	}
	
	public function getVersion()
	{
		return '???';
	}
		
	public function getFileExtension()
	{
		return 'mpl';
	}
	
	public function getIdPattern()
	{
		$idPattern = '/unknown/';
		//
		return $idPattern;
	}
	
	public function allowsTextStyles()
	{
		return '????';
	}
	
	public function template()
	{
		$ccTemplate = '
0,85,0,caption 1 
85,148,0,caption 2
148,182,0,caption 3
182,269,0,caption 4
269,358,0,caption 5
358,414,0,caption 6
414,497,0,caption 7
497,534,0,caption 8
589,642,0,caption 9
642,694,0,caption 10
694,783,0,caption 11
783,844,0,caption 12
844,896,0,caption 13
		
		';
		
		return $ccTemplate;
	}
	
}  // end MPlayer class 
?>