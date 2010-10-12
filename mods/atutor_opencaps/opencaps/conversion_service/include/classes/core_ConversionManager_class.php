<?php
/**
 * Name: ConversionManager class
 * Author: Antonio Gamba Bari - antonio.gambabari@utoronto.ca. ARTC, University of Toronto.
 * Last Update: July 22, 2009
 * 
 * Description: This class performs all the conversion functions
 * 1. Start a conversion
 * 2. Retrieve a caption file
 * 3. Load all caption formats inc files
 * 3. Autodetect caption format
 * 4. Build a Collection of Captions
 * 5. Call export method from the proper CaptionFormat sub-class 
 */

class ConversionManager
{
	private $ccStringTarget; // Caption Target file as String
	private $ccSourceFileName; // File Name of the Source caption 
	private $ccTargetExt; // File Extension of the caption file  
	private $ccTypeSource; // Class name of the source caption format
	private $ccTypeTarget; // Class name of the target caption format
	private $ccFormats = Array(); // array containing all the class names of the supported caption formats
	private $ccTargetUrl; // relative path to target caption file
	public $ccImportedCollection; // The target CaptionCollection Object
	
	
	/**
	 * Class Constructor: Receives a caption string, parameters and returns the target caption as string
	 *
	 * @param String $theCaption The Caption File as String
	 * @param String $theTarget The target Type = Class name of target sub-class
	 * @param String $theFileName The name of the source caption file 
	 */
	public function __construct($theCaption,$theTarget,$theFileName)
	{
		global $rosettaCCSettings;
		/*
		 * start conversion 
		 */
		// assign parameters to class members 
		$this->ccTypeTarget = $theTarget;
		$this->ccSourceFileName = $theFileName;
		$this->ccTypeSource = ''; // initialize $ccTypeSource = ''
		
		// load caption formats 
		$this->ccFormats = CcUtilVital::ccFormatsLoad();

		/*
		 * run detection function on imported caption string
		 * Note: the type of the source caption is not a parametter. This means that for stability 
		 * purposes the conversion tool MUST identify the format. Otherwhise, many unexpected and 
		 * incorrect conversions may occur   
		 */ 
		$this->ccTypeSource = $this->_ccAutoDetect($theCaption);		
			
		// proceed only if caption format is detected  
		if($this->ccTypeSource!='')
		{
				//echo '<br/><br/> Caption format Detected: <b>'.$this->ccTypeSource.'</b>';

			/*
			 * Invoques import function in caption format subclass
			 * Note: here we know the name of the Caption format class from the $this->ccTypeSource (the value assigned by the autodetection function) 
			 */
			$ccSourceObj = new $this->ccTypeSource();
			
			// get a CaptionCollection Object 
			$this->ccImportedCollection = $ccSourceObj->importCC($theCaption); 
			
				//echo '<br/><br/>Uploaded Caption file was loaded into a CaptionCollection';
			
			// Initialize exported collection string
			$this->ccStringTarget = '';
			
			// run export function
			$this->ccStringTarget = $this->_ccExport();
			
			// print target format in a form for debuging purposes only
				//echo '<form><textarea name="textarea" cols="120" rows="10">'. $this->ccStringTarget . '</textarea></form>';
			
			//$this->_ccExport();
			
			// verify if the Target format has been provided 
			if ($this->ccStringTarget!='')
			{
				// Save collection in DB as serialized string
					//DbTools::collectionInBdSave($this->ccImportedCollection, $theFileName);
				
				// FINALLY !! if all goes right save exported caption into a file 
				$theConvertedFile = $this->_saveExported();
				// display player test option
					//PlayerTools::displayPlayerOp($theConvertedFile);
				
				// force downlaod: only when no comments or debuging mode 
						//TxtFileTools::downloadFile($theConvertedFile);
				
				// set the relative path of the target caption file
				$this->ccTargetUrl = $theConvertedFile;

			}

			
		} else {
			echo 'The format of source Caption was not recognized.';
		};
		
		// return $ccSourceFormat
				
	}// end __construct() 

	public function getCcTargetUrl()
	{
		return $this->ccTargetUrl;		
	}
	
	/**
	 * Gets the file name without extension of a file
	 *
	 * @param String $theFilename Name of the file
	 * @return String $targetFileName
	 */
	private function _buildExportFileName()
	{
		$targetFileName = substr($this->ccSourceFileName, 0, -3);
		return $targetFileName;
	}
	
	private function _saveExported()
	{
			//global $rosettaCCSettings;
		// build the proper name to save converted caption
		$theCCname = $this->_buildExportFileName().$this->ccTargetExt;
		
			//$fullFileUrl = $rosettaCCSettings['uploadDir'].'/'.$theCCname;
		 
		// save exported collection in a file 
		TxtFileTools::stringToFile($theCCname,$this->ccStringTarget);

		// return the generated file name
		return $theCCname;
		
		// download the generated caption file 
		//CcUtil::downloadFile($fullFileUrl);
				  
	}

	/**
	 * Gets a CaptionCollection object and return the target caption file as string
	 *
	 * @return unknown
	 */
	private function _ccExport()
	{
		$ccTargetString = '';

		//echo '<br/><br/>...creating an object of the target format: '.$this->ccTypeTarget;
		//echo '<br/><br/>I AM a '.$ccObjExp->getName().' Object';

		if ($this->ccTypeTarget!='0')
		{
			// create an instance of the target Caption Format
			$ccObjExp = new $this->ccTypeTarget();
			
			// get the extension of the target Caption
			$this->ccTargetExt = $ccObjExp->getFileExtension();
			//echo '<br/>The extension of the Target Caption is: '. $this->ccTargetExt; 
			
			// call export method in caption format instance
			$ccTargetString = $ccObjExp->exportCC($this->ccImportedCollection);

		} // end if
		
		//echo '<br/>Target Collection: <br/>'.$ccTargetString;
		return $ccTargetString;
	} // end _ccExport()
	
	/**
	 * Import caption as a sting and return a Caption Collection
	 * @param String $theCaption Caption file as a String	 
	 * @param String $theCcFormatClassName The class name of the caption source
	 * @return CaptionCollection $importedCollection Return a Caption Collection Object  
	 */
	private function _ccImport($theCaption,$theCcFormatClassName)
	{
		// Create an instance of the source Caption Format
		$myCcSourceObj = new $theCcFormatClassName();
		
		// import the caption into a CaptionCollection Object
		return $myCcSourceObj->importCC($theCaption);
		
	} // _ccImport() end

	/**
	 * Auto detect format of a caption string using the unique pattern provided by each caption format 
	 * @param String $theCaption Caption file as a String	 
	 * @return String $formatfound Return detected caption format or '' if not found  
	 */
	private function _ccAutoDetect($theCaption)
	{
		//echo '<br/>Total Formats to Auto-detect: '.count($this->ccFormats);
		
		$formatfound = '';
		
		// instanciate each caption format sub-class and call getIdPattern() 
		foreach ($this->ccFormats as $ccId)
		{ 
			// testing here a polymorphic behaviour 
				//echo '<br/>.....Detecting format: '.$ccId;
			$theCcIdPattern = '';
			$ccObj = new $ccId(); // Create an instance of a caption format
			$theCcIdPattern = $ccObj->getIdPattern(); // get the pattern identifying the caption format
			//echo '<br/>***** '.$ccId.' = '.$theCcIdPattern;
			
			// look for the pattern in the caption string
			if (preg_match($theCcIdPattern,$theCaption)==1)
			{
					//echo '<br/>!! Caption Format Dettected!! ***** '.$ccId.' = '.$theCcIdPattern; 
					//echo '<br/>Caption Format Dettected: <b>'.$ccId.'</b>';
				
				// set the detected format
				$formatfound = $ccId;
				
				return $formatfound;
				break; // stop detecting caption format
			}

		} // foreach end

		return $formatfound;
		
	} // _ccAutoDetect() end
	
	public function getCaptionFormats()
	{
		return $this->ccFormats;
	}
	
	public function getImportedCollection()
	{
		return $this->ccImportedCollection;
	}

	
} // class end 
?>