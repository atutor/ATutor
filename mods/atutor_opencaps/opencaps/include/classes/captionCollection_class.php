<?php
/*
 * OpenCaps
 * http://opencaps.atrc.utoronto.ca
 * 
 * Copyright 2009 Antonio Gamba Bari
 * Adaptive Technology Resource Centre, University of Toronto
 * 
 * Licensed under the Educational Community License (ECL), Version 2.0. 
 * You may not use this file except in compliance with this License.
 * http://www.opensource.org/licenses/ecl2.php
 * 
 */

/**
 * This class represents a collection of captions 
 * 1. Create an instance
 * 2. load captions
 */
class CaptionCollection
{
	public $collectionName = ''; // the name that holds the entiry collection.. this particularly important for JSON export
	public $txtStylesGlobal = array(); // holds any global text style. (e.g. $txtStylesGlobal['text-align'] = 'center', $txtStylesGlobal['text-font'] = 'Arial', $txtStylesGlobal['text-size'] = '14', etc...)
	public $captionCollection = array(); // a collection of Caption objects
	
	/**
	* Class Constructor 
	*/
	public function __construct()
	{
		/*
		 * empty for now... (we don't know what the caption will contain...)
		 * It seems logical and much more practical 
		 * to create first an empty CaptionCollection  
		 * and then add to it as needed, 
		 * finally, get the collection object
		 */
	}
	
	/**
	 * Adds a Caption Object to the $captionCollection array 
	 * @param Object $theCcObject a Caption Object
	 * @return void
	 */	
	public function addCaption($theCcObject)
	{
		// add Caption to the Caption Collection 
		$this->captionCollection[] = $theCcObject; // this a php-based approach... java will need a push/count solution 
		
	} // end addCaptions()

	/**
	 * Sets all the Global text style attributes
	 * @param Array $theGlobalStyles Array containing all the global text styles  
	 */
	public function setTxtStylesGlobal($theGlobalStyles)
	{
		$this->txtStylesGlobal = $theGlobalStyles;
	}	
	
	/**
	 * Sets the value a single Global text style attribute
	 * @param String $theAtt Attribute name
	 * @param String $theValue Attribute Value 
	 */
	public function setTxtStylesGlobalAtt($theAtt,$theValue)
	{
		$this->txtStylesGlobal[$theAtt]=$theValue;
	} // end setTxtStylesGlobalAtt()
	
	/**
	 * Return this object
	 *
	 * @return CaptionCollection 
	 */
	public function getCollection()
	{
		return $this->captionCollection;
	}  

	public function toString()
	{
		$ccCount=0;
		echo '<br/><h3>Printing a Rosetta Collection</h3>';
		echo 'Total Captions Found: '.count($this->captionCollection);

		echo '<br/><br/><b>[Global]  Styles</b>';
		if (count($this->txtStylesGlobal)==0)
		{
			echo ' (NO text styles found)';
		}
		
		foreach ($this->txtStylesGlobal as $txtStyleName => $txtStyleValue)
		{
			echo '<br/> -----'.$txtStyleName.' = '.$txtStyleValue;
		}
		
		echo '<br/><br/><b>Printing Captions in the collection... </b>';
		foreach ($this->captionCollection as $captionObj)
		{
			$ccCount++;
			// building a new to string
			/* 
			
			echo "<br><b>In Time: </b>". $captionObj->getInTime()."";
			echo "<br><b>Out Time: </b>". $captionObj->getOutTime()."";
			echo "<br><b>Caption: </b>". $captionObj->getCaption()."";
			
            // display text styles
            foreach ($captionStylesFound as $txtStyle)
            {
            	$captionObj->
            	//$textStyles[] = $txtStyle;
            }
			*/
			// call Caption's toString();
			echo '<br/><br/>'.$ccCount;
			$captionObj->toString();

		} // foreach end 

	} //toString() end

} // end CaptionCollection Class  
?>