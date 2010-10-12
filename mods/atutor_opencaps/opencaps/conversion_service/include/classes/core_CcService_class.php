<?php
class CcService
{	
	// Unique Variables
	private $ccResult; // $ccResult = 0 returns a caption string, $ccResult = 1 returns the caption URL
	private $ccSourceURL; // The URL where the caption source file is located
	
	// variables to be passed to ConversionManager Class 
	private $ccStringTarget; // Caption Target file as String
	private $ccSourceFileName; // File Name of the Source caption
	private $ccTypeTarget; // Class name of the target caption format (e.g. 'Sami', or 'QTtext')
	

	public function __construct($theCcResult,$theCcSourceURL,$theCcTypeTarget,$theFileName)
	{
		// assign parameters to class members 

		$this->ccResult = $theCcResult;
		$this->ccSourceURL = $theCcSourceURL;
				
		$this->ccStringTarget = '';
		$this->ccTypeTarget = $theCcTypeTarget;
		$this->ccSourceFileName = $theFileName;
			
		$this->_startService();
	}
	
	private function _startService()
	{
		global $rosettaCCSettings;
		
		// download remote caption file
		$myCCString = file_get_contents($this->ccSourceURL);
		
		$myCCString = stripslashes($myCCString);
		
			// Start a convertion Manager Instance
		$myCcManager = new ConversionManager($myCCString,$this->ccTypeTarget,$this->ccSourceFileName);
		
			// get the Caption Target URL
	$ccTargetUrl = $myCcManager->getCcTargetUrl();
			
	if($this->ccResult==0)
	{
		// get the target file as a String
		$ccTargetString = TxtFileTools::fileToString($ccTargetUrl); 
		echo $ccTargetString;
	}
	else if ($this->ccResult==1)
	{
		echo $rosettaCCSettings['uploadDir'].'/'.$ccTargetUrl;
	}
	

	}
}
?>