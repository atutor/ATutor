<?php
/**
 * QTSMIL Class
 */
class QTSMIL extends CaptionFormat
{

	private $textStyles = array();  
	
	/**
	 * Imports a caption string into a CaptionCollection 
	 *
	 * @param String $theCCString the caption file as string
	 * @return CaptionCollection $myCcCollection A CaptionCollection Object
	 */
	public function importCC($theCCString) {
		//global $ins, $caps, $outs, $num_clips,$proj_caption;
		$clips = array();
		$clip_string = '';
	    
		//$contents = file_get_contents($theCCFile);
		$contents = $theCCString;
        
        // clean malformed patterns creted when saving files on win notepad
        $toSearch = array(chr(13).chr(10));
        $toReplace = array(chr(10));
        $contents = str_replace($toSearch,$toReplace,$contents);
        
        // Defining QText known pattenrs;
        $pattern_QT_time_format = "\[([0-9]{2}:[0-9]{2}:[0-9]{2}.[0-9]{3})\]";
        $pattern_QT_time_format_magpie = "\[([0-9]{2}:[0-9]{2}:[0-9]{2}.[0-9]{2})\]";
        
        $num_clips = preg_match_all("/$pattern_QT_time_format/", $contents, $clips);
        
        // if caption file is not QT and comes from MapPie time format
		if ($num_clips == 0)
	        {
	            $num_clips = preg_match_all("/$pattern_QT_time_format_magpie/", $contents, $clips);
	        }
	            
	        //$num_clips = $num_clips/2; // this is not needed
	        
	        // create a collection object
	        include_once('CaptionCollection.php');
	        $myQTextCollection = new CaptionCollection();
	        
	        
	        // build data arrays looing through $clips QT format: 1) Split using QT time format, Find all times
	        for ($i=0; $i<$num_clips; $i=$i+2) {		
	                
                // 2) Find all content in between QT time START and END using /s flag to capture also break lines
                $match_this = '/\['.$clips[1][$i].'\]\n(.*)\n\['.$clips[1][$i+1].'\]\n/s';			
                preg_match($match_this, $contents, $clip_bit);
                
                // add captions to the collection
                $myQTextCollection->addCaptions($clips[1][$i],$clip_bit[1],$clips[1][$i+1]);
                
                /*
                $ins[] = $clips[1][$i];
                $caps[] = $clip_bit[1];
                $outs[] = $clips[1][$i+1];
				*/

	        } // end for 
	
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
	
		foreach ($myCollection as $captionObj)
		{
			
		} // end foreach
		
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
		return '';
	}
	
	public function getAbout()
	{
		return '';
	}
	
	public function getVersion()
	{
		return '';
	}
		
	public function getFileExtension()
	{
		return '';
	}
	
	public function getIdPattern()
	{
		$idPattern = '/unknown/';
		//
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

}  // end classQText 
?>