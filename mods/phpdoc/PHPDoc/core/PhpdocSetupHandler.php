<?php
/**
* Handles the "setup".
*
* This class provides all methods neccessary to "setup" Phpdoc and check the 
* current setup.
* 
* @version 	$Id: PhpdocSetupHandler.php,v 1.5 2000/12/03 20:30:42 uw Exp $
* @author		Ulf Wendel <ulf@redsys.de>
*/
class PhpdocSetupHandler extends PhpdocArgvHandler {

	/**
	* Name of the target directory.
	*
	* @var		string	$target
	* @access	private
	*/								
	var $target = "";	
	
	/**
	* Name of the application parsed
	*
	* @var	string	$application
	* @see	setApplication()
	*/
	var $application = "PHPDoc";
	
	/**
	* Basedir for all file operations
	*
	* @var	string	$basedir
	* @see	setApplication()
	*/
	var $basedir = "";
    
	/**
	* Suffix for all rendered files in the application (except for the xml files).
	*
	* @var	string	targetFileSuffix
	* @see	setTargetFileSuffix()
	*/
	var	$targetFileSuffix = ".html";
	
	/**
	* Suffix of all source code files in the application
	*
	* If you used other file suffixes than ".php" in you have to override this.
	*
	* variable using setSourceFileSuffix()
	* @var	array	sourceFileSuffix
	* @see	setSourceFileSuffix()
	*/
	var	$sourceFileSuffix = array ( "php" );
	
	/**
	* Directory with the php sources to parse.
	*
	* @var	string	
	* @see	setSourceDirectory()
	*/
	var $sourceDirectory = "";	
	
	/**
	* Sets the name of the directory with the source to scan.
	*
	* @param	string
	* @access	public
	*/
	function setSourceDirectory($sourcedir) {
		$this->sourceDirectory = $this->getCheckedDirname($sourcedir);
	} // end end func setSourceDirectory
	
	/**
	* Sets the name of the directory with the templates.
	*
	* @param	string
	* @access	public
	*/
	function setTemplateDirectory($sourcedir) {
		$this->templateRoot = $this->getCheckedDirname($sourcedir);
	} // end func setTemplateDirectory
	
	/**
	* Sets the name of your application. 
	* 
	* The application name gets used on many places in the default templates.
	* 
	* @param	string	$application	name of the application
	* @return	bool		$ok
	* @throws	PhpdocError	
	* @access	public
	*/	
	function setApplication($application) {
		if (""==$application) {
			$this->err[] = new PhpdocError("No application name given.", __FILE__, __LINE__);
			return false;
		}
		
		$this->application = $application;
		return true;
	} // end func setApplication
	
	/**
	* Suffix for all rendered files in the application (not for the xml files)
	*
	* By default the the suffix is set to ".html".
	*
	* @param	string	$suffix		string with the suffix
	* @return	bool		$ok
	* @see		$targetFileSuffix
	* @author	Thomas Weinert <subjective@subjective.de>
	*/
	function setTargetFileSuffix($suffix) {
		if ("" != $suffix && "." != $suffix[0]) {
			$this->err[] = new PhpdocError("Make sure that the file extension starts with a dot.", __FILE__, __LINE__);
			return false; 
		}
		
		$this->targetFileSuffix = $suffix;
		return true;
	}

	/**
	* Suffix of all source code files in the application
	*
	* By default only files with the suffix ".php" are recognized as
	* php source code files and parsed. If you used other
	* suffixes such as ".inc" you have to tell phpdoc to parse
	* them.
	*
	* @param	mixed	$suffix		string with one suffix or array of suffixes
	* @return	bool	$ok
	* @throws	PhpdocError
	* @see		$sourceFileSuffix
	*/
	function setSourceFileSuffix($suffix) {
		if ( (!is_array($suffix) && "" == $suffix) || (is_array($suffix) && 0 == count($suffix)) ) {
			$this->err[] = new PhpdocError("No suffix specified.", __FILE__, __LINE__);
			return false;
		}
		if (!is_array($suffix)) 
			$suffix = array($suffix);		
		
		$this->sourceFileSuffix = $suffix;	
		return true;
	} // end func setSourceFileSuffix

	/**
	* Sets the target where the generated files are saved.
	* 
	* @param	string	$target
	* @return	bool		$ok 
	* @throws PhpdocError
	* @access	public
	*/
	function setTarget($target) {
		if ("" == $target) {
			$this->err[] = new PhpdocError("No target specified.", __FILE__, __LINE__);
			return false;
		}
		
		if (!is_dir($target)) {
			$ok = mkdir($target, 0755);
			if (!$ok) {
				$this->err[] = new PhpdocError("setTarget(), can't create a directory '$target'.", __FILE__, __LINE__);
				return false;
			}
		}
			
		$this->target = $this->getCheckedDirname($target);
		return true;
	} // end func setTarget

	/**
	* Checks the current status of the object. Are all necessary informations to start parsing available?
	* @param	mixed		$errors
	* @return	array		$errors
	*/
	function checkStatus($errors = "") {
		if (!is_array($errors))
			$errors = array();
/*
				
		if (0==count($this->files) && ""==$this->directory) 
			$errors[] = array (
													"msg" 	=> "No source files or source directory specified.",
													"type"	=> "misconfiguration",
													"errno"	=> 6
												);
												
		if (0!=count($this->files) && ""!=$this->directory) 
			$errors[] = array(
													"msg"		=> "Define eighter some files or a diretory.",
													"type"	=> "misconfiguration",
													"errno"	=> 7
											);
	*/	
		return $errors;
	} // end func checkStatus
	
	/**
	* Adds a slash at the end of the given filename if neccessary.
	*
	* @param	string	Directoryname
	* @return	string	Directoryname
	*/
	function getCheckedDirname($dirname) {

		if ("" != $dirname && "/" != substr($dirname, -1)) 
			$dirname .= "/";
			
		return $dirname;
	} // end func getCheckedDirname

} // end class PhpdocSetupHandler
?>