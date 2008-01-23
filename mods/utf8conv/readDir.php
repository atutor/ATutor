<?php
/* This is a php class to Read a directory recursively and executes defined events */
/* when each directory element is read                                             */

class readDir
{  
	var $path; 
	var $errtxt; 
	var $errcount; 
	var $recurse; 
	var $events; 
	var $handlers;  
	
	/*** Constructor (executed when we instatiate the class)*/ 
	function readDir()
	{ 
		$this->recursive = false; 
		$this->errcount = 0; 
		$this->events = array('readDir_dir', 'readDir_file'); 
		$this->handlers = array(); 
	}  
	
	/*** Set the directory to read* @param string full directory path*/ 
	function setPath( $path ) 
	{ 
		if (!is_dir($path)) 
		{ 
			$this->_error('The supplied argument, '.$path.', is not a valid directory path!'); 
			return false; 
		} 
		
		$this->path = $path; 
		return true; 
	}  
	
	/*** Set and event handler* @param string event name* @param string event handler function name*/ 
	function setEvent( $event, $handler ) 
	{ 
		if (in_array($event, $this->events) !== false) 
		{ 
			$this->handlers[$event] = $handler; 
		} 
		else 
		{ 
			$this->_error('Event Type specified does not exist.'); 
			return false; 
		} 
		return true; 
	}  
	
	/*** Set if we want to read through sub folders recursively* @param bool TRUE or FALSE*/ 
	function readRecursive( $bool = true ) 
	{ 
		$this->recurse = $bool; 
	}  
	
	/*** Read the directory*/ 
	function read() 
	{ 
		if ( !is_dir($this->path) ) 
		{ 
			$this->_error('Directory to read from is invalid.'.'Please use setPath() to defind a valid directory.'); 
			return false; 
		}  
		
		// all set, start reading 
		return $this->_read($this->path); 
	}  
		
	function _read($dir) 
	{ 
		if ($dh = opendir($dir)) 
		{ 
			$i = 0; 
			while ($el = readdir($dh)) 
			{ $path = $dir.'/'.$el;  
				
				if (is_dir($path) && $el != '.' && $el != '..') 
				{ 
					if ($this->_trigger('readDir_dir', $path, $el) == -1) 
					{ 
						closedir($dh); 
						return true; 
					}  
					
					if ($this->recurse) 
					{ 
						// read sub directories recursively 
						$this->_read($path); 
					} 
				} 
				elseif (is_file($path)) 
				{ 
					if ($this->_trigger('readDir_file', $path, $el) == -1) 
					{ 
						closedir($dh); 
						return true; 
					} 
				} 
				
				$i++; 
			}  
			
			closedir($dh); 
			return true; 
		} 
		
		else 
		{ 
			$this->_error('Could not open the directory, '.$path); 
		} 
		return false; 
	}  
	
	function _trigger($event, $path, $el) 
	{ 
		if ($this->handlers[$event]) 
		{ 
			if (!function_exists($this->handlers[$event])) 
			{ 
				$this->_error('User Function, '.$this->handlers[$event].', defined for the event, '.$event.', does not exist'); 
				return false; 
			} 
			
			return call_user_func($this->handlers[$event], $path, $el); 
		} 
	}  
	
	function _error($txt) 
	{ 
		$this->errcount++; 
		$this->errtxt = $txt; 
	}  
	
	/*** View the last error logged*/ 
	function error() 
	{ 
		return $this->errtxt; 
	}  
	
	/*** View the last error number*/ 
	function errorCount() 
	{ 
		return $this->errcount; 
	} 
} 
		
?>
