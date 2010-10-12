<?php


class XmlManager
{
	private $xmlDataString; // the xml data as string
	private $captionCollection; // the Caption Collection
	//

	function __construct($theXmlString)
	{
		$this->xmlDataString = $theXmlString;
		
		// create a caption Collection 
		$myCaptionCollcton = new CaptionCollection();
		$this->captionCollection = $myCaptionCollcton;
		
		// start loopXmlData
		$this->loopXmlData();
		
	} // end constructor  

	/**
	 * Parses an XML string into an myltidimentional array
	 *
	 * @param String $theXmlString The xml file as string
	 * @return Array $xmlInArray 
	 */
	public function loopXmlData()
	{
		// create XML parser
		$p = xml_parser_create();
		
		//xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0);

		xml_parse_into_struct($p, $this->xmlDataString, $vals, $index);
		
		// free parser 
		xml_parser_free($p);
		
		
		// create a Xmlparse Object tracing for <P> tags at level 4 (this the case of DFXP files) 
		$myXmlParse = new XmlParse('P','4');
		
		
		// start looping xml array
		for ($i = 0; $i < count($vals); $i++) 
		{
			$myXmlParse->setXmlData();
			
			
			// *************************************** GET CAPTION DATA ---- START
			
			// if P tag is complete 
			if($ccBuild == 3) 
			{
				// get caption value
				$ccText = $vals[$i]['value'];
				
				// get attributes array 
				$capAttrib = $vals[$i]['attributes'];
				
				// get time in and out of the caption
				$ccTimeIn = $capAttrib['BEGIN'];
				$ccTimeOut = $capAttrib['END'];
				
				$newCaptionDone = 1;

				$newCaption = new Caption($ccTimeIn,$ccTimeOut,$ccText,$noStyles);
				//$new
				//$countCC++;
			
			}  else if($ccBuild == 1) {
				
				
					
			// if there are other tags inside P
			} else if (($ccBuild == 2) && isset($vals[$i]['value'])) {
				
				// add other parts of the caption text
				$ccText .= $vals[$i]['value'];
			
			// if is the end of the caption
			} else if (($ccBuild == 4) && isset($vals[$i]['value'])) {
				
				// add other parts of the caption text if is set
					$ccText .= $vals[$i]['value'];
			}
			
			
			// *************************************** GET CAPTION DATA ---- END
			
			// just print each tag info
			echo '<br/>Tag: '.$vals[$i]['tag'];
			echo '<br/>Type: '.$vals[$i]['type'];
			echo '<br/>Level: '.$vals[$i]['level'];

			if(isset($vals[$i]['attributes']))
			{
				foreach ($vals[$i]['attributes'] as $theAtt => $theVal)
				{
					//echo '<br/>Attributes: '.$vals[$i]['attributes'];
					echo '<br/>------ '.$theAtt.': '.$theVal;
				}
			}
			
			if(isset($vals[$i]['value']))
			{
				echo '<br/>Value: '.$vals[$i]['value'];
			}
			
			echo '<hr>';
		} // end for 
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		/*
		echo "Index array\n";
		echo "Total in array Index: ".count($index)."\n";
		print_r($index);
		echo "\nVals array\n";
		echo "Total in array vals: ".count($vals)."\n";
		print_r($vals);
		*/
		
		
		
		/*
		foreach($vals as $val)
		{
			$totInArr = count($val);
			echo '<br/>tot in Val = '.$totInArr;
			//echo '<br/>Val = '.$val;
			foreach ($val as $theVal)
			{
				echo '<br/>the val = '.$theVal;
				
			}  // end for 2
			
		} // end for 1
		*/
		
		
		//return $vals;
		
	} // end loopXmlData()
	
	/**
	 * Print class Data
	 *
	 */
	public function toString()
	{
		echo '<br/> xmlDataString: '.$this->xmlDataString;
		echo '<br/> tagToTrace: '.$this->tagToTrace;
		echo '<br/> tagToTraceLevel: '.$this->tagToTraceLevel;
		
	}
} // end XmlManager class 
?>