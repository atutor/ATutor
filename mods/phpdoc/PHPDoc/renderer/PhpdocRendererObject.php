<?php
/**
* Superclass of all Renderer. 
*
* Derive all custom renderer from this class.
*
*/
class PhpdocRendererObject extends PhpdocObject {

	var $warn;

	var $accessor;

	/**
	* Extension for generated files.
	* @var	string	$file_extension
	*/
	var $file_extension = ".html";

} // end class PhpdocRendererObject
?>