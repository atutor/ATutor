<?php
/**
 * This class represents a sinlge caption and all the allowed features (time, text styles, etc.) 
 */

class Caption
{
	public $inTime;
	public $outTime;
	public $caption;
	public $textStyles = array(); // 
	
	/**
	 * Class Constructor: 
	 * @param String $theInTime
	 * @param String $theOutTime
	 * @param String $theCaption
	 * @param Array $theTextStyles as a reference
	 * @return void
	 */
	function __construct($theInTime, $theOutTime, $theCaption,$theTextStyles)
	{
		$this->inTime = $theInTime;
		$this->outTime = $theOutTime;
		$this->caption = $theCaption;
		$this->textStyles= $theTextStyles;
	} // __construct() end

		// SET 	Functions
		
	/**
	 * Sets value of caption IN time
	 * @param String $theInTime
	 * @return void  	
	 */
	public function setInTime($theInTime)
	{
		$this->inTime = $theInTime;
	}
	
	/**
	 * Sets value of caption OUT time
	 * @param String $theOutTime
	 * @return void  	
	 */
	public function setOutTime($theOutTime)
	{
		$this->outTime = $theOutTime;
	}	

	/**
	 * Sets value of a caption (multiple lines allowed)
	 * @param String $theCaption
	 * @return void  	
	 */
	public function setCaption($theCaption)
	{
		$this->caption = $theCaption;
	}	

	/**
	 * Sets value of Caption text style attribute
	 * @param String $theAtt Attribute name
	 * @param String $theValue Attribute Value 
	 */
	public function setTextAtribute($theAtt,$theValue)
	{
		$this->textStyles[$theAtt]=$theValue;
	}

		// GET 	Functions
		
	/**
	 * Gets value of Caption IN Time
	 * @return String inTime Caption  	
	 */
	public function getInTime()
	{
		return $this->inTime;
	}
	
	/**
	 * Gets value of Caption OUT Time
	 * @return String outTime Caption  	
	 */	
	public function getOutTime()
	{
		return $this->outTime;
	}
	

	/**
	 * Gets value of Caption text
	 * @return String inTime Caption text  	
	 */
	public function getCaption()
	{
		return $this->caption;
	}
		
	/**
	 * Gets text style array
	 * @return Array $theTextStyles Txt Styles Array  	
	 */	
	public function getTextStyles()
	{
		return $this->textStyles;	
	}


	/**
	 * Gets value of Caption text style attribute
	 * @param String $theAtt Attribute name
	 * @return String $Value Attribute Value  	
	 */	
	public function getTextAtribute($theAtt)
	{
		return $this->textStyles[$theAtt];
	}

	/**
	 * Print all values of the Caption as a String
	 */	
	public function toString()
	{
		echo "<br/><b>In Time: </b>". $this->getInTime()."";
		echo "<br/><b>Out Time: </b>". $this->getOutTime()."";
		echo "<br/><b>Caption: </b>". $this->getCaption()."";
		
		if (count($this->textStyles!=0))
		{
			if (count($this->textStyles)==0)
			{
				//echo '<br/>----(NO text styles found)';
			} else {
		
				echo '<br/><b>------[Caption] Styles: </b>';
				
				// Display all text attributes in the caption
				foreach ($this->textStyles as $styleName=>$styleValue)
				{
					echo '<br/> -----'.$styleName.' = '.$styleValue;
				} // foreach end
			}// end if
			
		} //if end
		
	}// toString() end 
	
	// return this caption object
	public function getThisCaption()
	{
		return $this;
	}
} // end class Caption
?>