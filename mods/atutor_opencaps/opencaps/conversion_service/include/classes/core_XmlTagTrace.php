<?php
// 
/**
 * This class provides the functionality to trace an xml tag 
 */

class XmlTagTrace
{
	private $tagToTrace; // the xml tag to be collected
	private $tagToTraceLevel; // The level of the xml tag bo be collected/traced
	private $xmlString; // Xml data as string
	
	private $xmlTag; // temporary object to store XML object while reading xml array
	private $xmlTagState; // true or false if tracing tag is done
	private $myXmlCollection; // collection of the traced xml tag
	
	/**
	 * Class Constructor: starts tracing a XML tag. It recieves a xml TagName and xml TagLevel
	 * @param String $theXmlString XML data as String
	 * @param String $theTagToTrace XML Tag to trace 
	 * @param String $theTagToTraceLevel Level of the XML tag to trace
	 */
	function __construct($theXmlString,$theTagToTrace,$theTagToTraceLevel)
	{
	
		// set the XML data
		//$this->xmlString = $theXmlString;
		
		// set the tag to trace and the starting level
		$this->tagToTrace = $theTagToTrace;
		$this->tagToTraceLevel = $theTagToTraceLevel;
		
		// create XML tag Collection
		$this->myXmlCollection = new XmlTagCollection();
		
		// start tracing
		$this->traceXmlTag($theXmlString);		
	} // __construct() end

	/**
	 * Returns the XML tag Collection
	 *
	 */
	public function getCollection()
	{
		//get from here of from XmlTagCollection collection object???
		return $this->myXmlCollection->getXmlTagCollection();
		//return $this->myXmlCollection;
		//echo '<br>From '
		
			
	}// end getCollection()
	
	/**
	 * Creates an XML parcer and Iterates through array
	 *
	 * @param unknown_type $theXmlString
	 */
	private function traceXmlTag($theXmlString)
	{
		// create XML parser
		$p = xml_parser_create();
		
		// parse XML data into array
		xml_parse_into_struct($p, $theXmlString, $xmlVals, $xmlIndex);
		
		// free XML parser 
		xml_parser_free($p);
		
		// initialize XML object ????
		//$myXmlTag = new XmlTag()

		
		// start looping xml array
		for($i = 0; $i < count($xmlVals); $i++) 
		{
			// initialize XML data
			$theTagName='';
			$theTagType='';
			$theTagLevel='';
			$theTagValue='';
			$theTagAtrib = Array();
			
			// verify data before adding
			if (isset($xmlVals[$i]['tag']))
			{
				$theTagName = $xmlVals[$i]['tag'];
			}
			
			if (isset($xmlVals[$i]['type']))
			{
				$theTagType = $xmlVals[$i]['type'];
			}
			
			if (isset($xmlVals[$i]['level']))
			{
				$theTagLevel = $xmlVals[$i]['level'];
			}
			
			if (isset($xmlVals[$i]['value']))
			{
				$theTagValue = $xmlVals[$i]['value'];
			}

			if (isset($xmlVals[$i]['attributes']))
			{
				$theTagAtrib = $xmlVals[$i]['attributes'];
			}			
			
			// set xml data
			$this->setXmlData($theTagName,$theTagType,$theTagLevel,$theTagValue,$theTagAtrib);
			
		
		} // end for loop xml array
		
	} // end traceXmlTag()
	
	/**
	 * Recieves Xml data from each tag and determines if values should be collected 
	 * this to trace <P> and <div> tags  
	 *
	 * @param String $theTagName
	 * @param String $theTagType
	 * @param String $theTagLevel
	 * @param String $theTagValue
	 * @param String $theTagAtrib
	 */
	private function setXmlData($theTagName,$theTagType,$theTagLevel,$theTagValue,$theTagAtrib)
	{
		
		//echo '<br/>'.$this->tagToTrace;
		
		// verify also if the level is lower !!!!
		
		//echo '<br/>'.$theTagName;
		// verify if this data is from the xml tag being traced/collected
		if ($theTagName == $this->tagToTrace)
		{
			
			//echo '<br/>'.$theTagName.' TAG IS THE SAME';
			//echo '<br/>Value: '.$theTagValue.'';
			
			
			// if tag is complete. The tag has NO children
			if ($theTagType=='complete')
			{
				// create a XmlTag object
				$this->xmlTag = new XmlTag($theTagName,$theTagType,$theTagLevel,$theTagValue,$theTagAtrib);
				
				// tracing this tag is done.  
				$this->xmlTagState = true;
				
				$this->xmlTag->setTagState(true);
				
				//echo '<br/>'.$theTagName.' TAG is complete !!!';
				
				//$this->xmlTag->toString();
				
				// add to collection
				$this->myXmlCollection->addXmlTagObject($this->xmlTag);
				
				// reset temp object
				$this->xmlTag = null;

			} // end if complete

			// if tag is open. tag do have children
			else if ($theTagType=='open')
			{
				// create a new XmlTag object
				$this->xmlTag = new XmlTag($theTagName,$theTagType,$theTagLevel,$theTagValue,$theTagAtrib);
				
				// tracing this tag is NOT done.  
				$this->xmlTagState = false;
				
				//echo '<br/>'.$theTagName.' TAG is Open !!!';
				
			} // end if

			// if tag cdata. Getting data of the traced Tag, after any children data
			else if ($theTagType=='cdata')
			{
				// add value to traced tag 
				// OJO !! This skips any child value 
				$this->xmlTag->addToTagValue($theTagValue);
				
				// tracing this tag is NOT done.  
				$this->xmlTagState = false;
				//echo '<br/>'.$theTagName.' TAG is cdata!!!';
				
				
			} // end if

			// if tag close. Getting data of the traced Tag is done
			else if ($theTagType=='close')
			{
				// add value of traced tag 
				$this->xmlTag->addToTagValue($theTagValue);
				
				// tracing this tag is NOT done.  
				$this->xmlTagState = true;
				
				$this->xmlTag->setTagState(true);
				//echo '<br/>'.$theTagName.' TAG is close!!!';
				
				//add to collection
				$this->myXmlCollection->addXmlTagObject($this->xmlTag);
				
				// reset temp object
				$this->xmlTag = null;
				
			} // end if
			
			
		}// end if tracing xml tag

		/*
		 * if the tag is a children. the tagToTraceLevel is lower than the current TagLevel 
		 * this for all tags like <br/>, <b>, <i>, <u>, or even for <span> inside our traced tag
		 */
		if ($this->tagToTraceLevel<$theTagLevel && (isset($this->xmlTag)))
		{
			// adding children data to tag as value 
			$this->xmlTag->addChildTagAsValue($theTagName,$theTagType,$theTagValue);
			
		} // end if
		
		
	} // end setXmlData()
	
	/**
	 * Print all values of the XML tag as a String
	 */	
	public function toString()
	{
		$this->myXmlCollection->toString();

		
	}// toString() end 
	
} // end class XmlTagTrace
?>