<?php
// 
/**
 * What is like to be an XML Tag?   
 */

class XmlTagCollection
{
	private $xmlTagCollection = array(); // a collection of XML tag objects
	
	public function addXmlTagObject($theXmlTagObject)
	{
		$this->xmlTagCollection[] = $theXmlTagObject;
	}
	
	public function getXmlTagCollection()
	{
		//echo '<br><br>XmlTagCollection Class -> getXmlTagCollection()<br>Tot in XmlTagCollection: '.count($this->xmlTagCollection);
		return $this->xmlTagCollection;
	}
	
	/**
	 * Print all values of the XML tag object 
	 */	
	public function toString()
	{
		echo "<br><br>Total <b>".$this->xmlTagCollection."</b> Tags = ".count($this->xmlTagCollection)."";
			
		// Display all tag attributes in the tag
		foreach ($this->xmlTagCollection as $xmlTagObject)
		{
			$xmlTagObject->toString();
		} // foreach end
		
	}// toString() end 
	
} // end class XmlTagCollection
?>