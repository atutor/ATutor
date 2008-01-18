<?php
/**
* Handles command line arguments.
*
* Be careful the source has not been tested yet, it's probably very buggy.
* Any help and comments are welcome...
*
* @author		Ulf Wendel <ulf@redsys.de>
* @version	$Id: PhpdocArgvHandler.php,v 1.2 2000/12/03 20:30:42 uw Exp $
*/
class PhpdocArgvHandler extends PhpdocObject {
	
	/**
	* Message explaining the usage of phpdoc on the command line.
	* 
	* Actually it's not the message itself but an array containing
	* the instructions. The array is indexed by the command line option e.g. "-h".
	* The array values hold a short message describing the  usage of the option.
	* 
	* @var		array
	* @access	private
	*/
	var $COMMANDS = array(
													"-f filename [, filename]" 	=> "name of files to parse",
													"-d directory"							=> "name of a directory to parse",
													"-p path"										=> "path of the files",
													"-t target" 								=> "path where to save the generated files, default is the current path",
													"-h"												=> "show this help message"
												);
	
	
	/**
	* Handle the command line values
	* 
	* handleArgv() looks for command line values and 
	* interprets them. If there're unknown command it prints
	* a message and calls die()
	*/	
	function handleArgv() {
		global $argv, $argc;
		
		// the first argv is the name of the script,
		// so there must be at least another one
		if ($argc<2) {
			$error = "\n\nCould not understand your request.\n\n";
			$error.= $this->getArgvHelpMessage();
			print $error;
			die();
		}
		
		$commands = 0;
		$errors = array();
		
		reset($argv);
		
		// skip the fist, it's the name of the php script
		next($argv);
		
		while (list($k, $arg)=each($argv)) {
			// valid command?
			if ("-"!=substr($arg, 0, 1))
				continue;
			
			$cmd 		= substr($arg, 1, 2);				
			$value 	= trim(substr($arg, 3));
			
			// all command line options except -h require values
			if (""==$value && "h"!=$cmd) {
				$errors[] = array( 
														"msg" 	=> sprintf("-%s: no value found", trim($cmd)),
														"type"	=> "argv"
													);
				// skip this command
				continue;
			}
			
			switch ($cmd) {
				case "f ":
					$files = explode(",", substr($arg, 3));
					$this->setFiles($files);
					$commands++;
					break;
				
				case "d ":
					$this->setDirectory($value);
					$commands++;
					break;
					
				case "p ":
					$this->setPath($value);
					$commands++;
					break;
					
				case "t ":
					$this->setTarget($value);
					$commands++;
					break;
				
				case "h ":
					$commands++;
					break;
					
				default:
					$errors[]="unknown command: '$arg'";
					break;
			}
			
		}
		
		// are there enough informations to start work?
		$errors = $this->checkStatus($errors);
		
		// check for errors and die() if neccessary
		if (count($errors)>0) {
			$error = "\n\nCould not understand your request.\n\n";
			reset($errors);
			while (list($k, $data)=each($errors)) 
				$error.=$data["msg"]."\n";
			
			$error.= $this->getArgvHelpMessage();
			print $error;
			die();
		}
				
		// no errors, but no recognized commands? die() if neccessary
		if (0==$commands) {
			$error = "\n\nCould not understand your request.\n\n";
			$error.= $this->getArgvHelpMessage();
			print $error;
			die();
		}
		
		// YEAH everything is fine, we can start working!
		$this->parse();		
	} // end func handleArgv
	
	/**
	* Returns the current help message of phpdoc
	* 
	* The message is not HTML formated, it could be shown 
	* on the command line. 
	*
	* @access	private
	* @return	string	$help_msg	Some instructions on available command line options
	* @see		handleArgv(), $COMMANDS
	*/	
	function getArgvHelpMessage() {
	
		$help_msg = "";
		
		// generate the message from the COMMAND array
		reset($this->COMMANDS);
		while (list($param, $explanation)=each($this->COMMANDS)) 
			$help_msg.= sprintf("%-28s%s\n", $param, $explanation);
		
		$help_msg.="\nFurter information can be found in the documentation.\n";
		return $help_msg;		
	} // end func getArgvHelpMessage
	
} // end class PhpdocArgvHandler
?>