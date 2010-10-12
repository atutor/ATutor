<?php
/**
 * RealText Class
 */
class RealText extends CaptionFormat
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
		return 'Real Time';
	}
	
	public function getAbout()
	{
		return 'This Caption format can be played by Real Player.';
	}
	
	public function getVersion()
	{
		return '???';
	}
		
	public function getFileExtension()
	{
		return 'rt';
	}
	
	public function getIdPattern()
	{
		$idPattern = '';
		$idPattern .= '/'; // open regex
		$idPattern .= '('; // start class pattern
		$idPattern .= '\<Time begin="';
		$idPattern .= '[0-9]{1}:[0-9]{2}:[0-9]{2}.[0-9]{1}"'; // 1 digit at the begining and 1 at the end
		$idPattern .= '|'; // or
		$idPattern .= '\<Time begin="';
		$idPattern .= '[0-9]{2}:[0-9]{2}:[0-9]{2}.[0-9]{1}"'; // 2 digit at the begining and 1 at the end
		$idPattern .= '|'; // or
		$idPattern .= '\<Time begin="';
		$idPattern .= '[0-9]{2}:[0-9]{2}:[0-9]{2}.[0-9]{2}"'; // 2 digit at the begining and 2 at the end
		$idPattern .= ')'; // end class pattern
		$idPattern .= '/'; // close regex
		
		return $idPattern;
	}
	
	public function allowsTextStyles()
	{
		return '1';
	}	
	public function template()
	{
		$ccTemplate = '
<window bgcolor="000000" wordwrap="true" duration="00:00:02.00">
<font size="+1" face="Arial" color="#FFFFFF">
<center>
<time begin="00:00:00.00"/><clear/>
First caption here.
<time begin="00:00:01.00"/><clear/>
Final caption here.
<time begin="00:00:02.00"/><clear/>
</center>
</font>
</window> 

.... or ..... 

<Time begin="01:99:99.01"/><clear/>
First caption here.
<Time begin="02:99:99.02"/><clear/>
Final caption here.
<Time begin="03:99:99.03"/><clear/>
';
		
		return $ccTemplate;
	}
	
} // end Class
?>