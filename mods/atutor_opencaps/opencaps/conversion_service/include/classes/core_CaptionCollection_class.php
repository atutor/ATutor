<?php

/**
 * This class represents a collection of captions 
 * 1. Create an instance
 * 2. load captions
 */
class CaptionCollection
{
	public $collectionName = ''; // The name that wraps the collection. 
	public $txtStylesGlobal = array(); // Holds any global text style. (e.g. $txtStylesGlobal['text-align'] = 'center', $txtStylesGlobal['text-font'] = 'Arial', $txtStylesGlobal['text-size'] = '14', etc...)
	public $captionCollection = array(); // The collection of Caption objects
	
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
	 * Sets the value of a single Global text style attribute
	 * @param String $theAtt Attribute name
	 * @param String $theValue Attribute Value 
	 */
	public function setTxtStylesGlobalAtt($theAtt,$theValue)
	{
		$this->txtStylesGlobal[$theAtt]=$theValue;
	} // end setTxtStylesGlobalAtt()
	
	/**
	 * Sets the collection Name
	 *
	 * @param String $theCollectionName
	 */
	public function setCollectionName($theCollectionName)
	{
		$this->collectionName = $theCollectionName;
	}
	
	/**
	 * Gets Collection Name
	 *
	 * @return String $collectionName
	 */
	public function getCollectionName()
	{
		return $this->collectionName;
	}
	
	/**
	 * Returns this CaptionCollection object
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
		echo '<br/><h3>Collection Name: '.$this->getCollectionName().'</h3>';
		echo 'Total Captions Found: '.count($this->captionCollection);

		if (count($this->txtStylesGlobal)!=0)
		{
			echo '<br/><br/><b>[Global]  Styles</b>';
			
			foreach ($this->txtStylesGlobal as $txtStyleName => $txtStyleValue)
			{
				echo '<br/> -----'.$txtStyleName.' = '.$txtStyleValue;
			}
		} 
		
		echo '<br/><br/><b>Printing Captions in the collection... </b>';
		
		foreach ($this->captionCollection as $captionObj)
		{
			$ccCount++;

			// call Caption's toString();
			echo '<br/><br/>'.$ccCount;
			$captionObj->toString();

		} // foreach end 

	} //toString() end

} // end CaptionCollection Class  
?>