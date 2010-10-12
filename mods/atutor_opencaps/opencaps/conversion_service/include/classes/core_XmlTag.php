<?php
// 
/**
 * XML Parser: this class defines an XML Tag   
 */

class XmlTag
{
	private $tagName;
	private $tagType;
	private $tagLevel;
	private $tagValue;
	private $tagAttrib = array();

	// working on...
	private $tagState; // True or false ; 
	//private $tagState; // 1=complete, 2=open, 3=cdata, 4=close
	
	/**
	 * Constructor: creates a XML tag object
	 *
	 * @param String $theTagName
	 * @param String $theTagType
	 * @param String $theTagLevel
	 * @param String $theTagValue
	 * @param Array $theTagAtrib
	 */
	function __construct($theTagName,$theTagType,$theTagLevel,$theTagValue,$theTagAtrib)

	// Constructor without attributes
	//function __construct($theTagName,$theTagType,$theTagLevel,$theTagValue)
	{
		$this->tagName = $theTagName;
		$this->tagType = $theTagType;
		$this->tagLevel = $theTagLevel;
		$this->tagValue = $theTagValue;
		$this->tagAttrib = $theTagAtrib;
		
	} // __construct() end
		
	public function getTagValue()
	{
		return $this->tagValue;		
	}
	
	public function getTagAttribute($attribName)
	{
		if (isset($this->tagAttrib[$attribName]))
		{
			return $this->tagAttrib[$attribName];
		} else {
			return '';
		}

	}
	 
	/**
	 * Adds a value to the xml tagValue
	 *
	 * @param String $theTagValueAdded The value to be added
	 */
	public function addToTagValue($theTagValueAdded)
	{
		$this->tagValue .= $theTagValueAdded;
	}

	/**
	 * Adds a child xml tag as a value to other parent tag value. 
	 * 
	 * @param String $theTagAdded The tag yo be 
	 * @param String $theTagState
	 */
	public function addChildTagAsValue($theTagAdded,$theTagState,$theTagValue)
	{
		
		// add <BR> Tag
		if (($theTagAdded=='BR') && ($theTagState=='complete'))
		{
			$this->tagValue .= '<BR/>'.$theTagValue;
		}
		
		// open and close Bold tag 
		if (($theTagAdded=='B') && ($theTagState=='complete'))
		{
			$this->tagValue .= '<b>'.$theTagValue.'</b>';
		}

		// open and close Italics tag 
		if (($theTagAdded=='I') && ($theTagState=='complete'))
		{
			$this->tagValue .= '<i>'.$theTagValue.'</i>';
		}

		if (($theTagAdded=='U') && ($theTagState=='complete'))
		{
			$this->tagValue .= '<u>'.$theTagValue.'</u>';
		}
		
	} // end addChildTagAsValue


	/**
	 * Adds an attribute and value to the XML tag   
	 *
	 * @param String $attName
	 * @param String $attValue
	 */
	public function addAttribute($attName,$attValue)
	{
		$this->tagAttrib[$attName] = $attValue;
		
	} // end addAttribute()
	
	/**
	 * Set the state of the XML tag
	 *
	 * @param Boolean $theState True or False
	 */
	public function setTagState($theState)
	{
		$this->tagState = $theState;
		
	} // end setTagState()

	/**
	 * Print all values of the XML tag as a String
	 */	
	public function toString()
	{
		// check if the tag is ready to show
		if ($this->tagState==true)
		{
			echo "<br><br><b>Tag Name: </b>". $this->tagName;
			echo "<br><b>Tag Type: </b>". $this->tagType;
			echo "<br><b>Tag Level: </b>". $this->tagLevel;
			echo "<br><b>Tag Value: </b>". $this->tagValue;
			
			// print data if the tag has attributes
			if (count($this->tagAttrib!=0))
			{
				echo '<br/><b>Tag Attributes: </b>';
				
				// Display all tag attributes; name and value
				foreach ($this->tagAttrib as $attribName => $attribValue)
				{
					echo '<br/>------'.$attribName.' = '.$attribValue;
				} // foreach end
			
			} else {
				echo '<br/>NO Attributes found.';
			} //end if attributes
			
		// end if is tag is ready
		} else {
			echo '<br/><br/> --- ops!! The XML tag is not ready .... Showing NO data';
		} // end if XML tag is ready
		
	}// toString() end
	
	
} // end class XmlParse
?>