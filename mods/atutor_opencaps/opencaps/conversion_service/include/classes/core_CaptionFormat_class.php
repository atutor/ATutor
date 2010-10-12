<?php
abstract class CaptionFormat
{
	
	/**
	 * This is the importCC() abstract method. All caption formats MUST override this method.  
	 *
	 * @param String $theCCString A caption file as a string
	 */
	abstract protected function importCC($theCCString);
	
	/**
	 * This is the exportCC() abstract method. All caption formats MUST override this method.
	 *
	 * @param unknown_type $theCollection
	 */
	abstract protected function exportCC($theCollection);
	//public function exportCC(&$theCollection) 	{ ; }
	
	/**
	 * This is the checkFormat() abstract method. All caption formats MUST override this method. and define
	 *
	 * @param unknown_type $theCCString
	 */
	abstract protected function checkFormat($theCCString);
	// new added must be redefine in subclass
	
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
		return '';
	}
	
	public function allowsTextStyles()
	{
		return '';
	}
	
	public function template()
	{
		$ccTemplate = '';
		
		return $ccTemplate;
	}
}

?>