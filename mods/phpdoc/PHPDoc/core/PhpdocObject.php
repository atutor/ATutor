<?php
/**
* Common base class of all phpdoc classes
*
* As a kind of common base class PhpdocObject holds 
* configuration values (e.g. error handling) and debugging
* methods (e.g. introspection()). It does not have a constructor,
* so you can always inheritig Phpdoc classes from this 
* class without any trouble.
*  
* @author		Ulf Wendel <ulf.wendel@phpdoc.de>
* @version 	$Id: PhpdocObject.php,v 1.2 2000/12/03 20:30:42 uw Exp $
* @package	PHPDoc
*/
class PhpdocObject {

	/** 
	* Variable containing the latest exceptions.
	*
	* The way PHPDoc handles errors is a little different from the
	* official PEAR way. PHPDoc methods do not return 
	* error objects but save them to the class variable $err and try
	* to return a value that indicates that an error occured.
	*
	* @var		array
	* @access	public
	*/
	var $err = array();

	/**
	* Default applicationname for the generated HTML files.
	* 
	* @var string	
	*/
	var $application = "PHPDoc";
	
	/**
	* Use to save warnings.
	* 
	* @var	array
	*/
	var $warn;
	
	/**
	* Flag determining wheter to print some status messages or not (default: false)
	*
	* @var		boolean	$flag_output
	* @see		setFlagOutput()
	* @since  0.3
	*/
	var $flag_output = false;

	/**
	* Sets the output flag - if set to true out() and outl() print messages
	*
	* @param	boolean	$flagOutput
	* @access	public
	* @see		$flag_output, out(), outl()
	* @since	0.3	
	*/
	function setFlagOutput($flagOutput) {
		$this->flag_output = ($flagOutput) ? true : false;
	} // end func setFlagOutput
	
	/**
	* Print a string and flushes the output buffer
	* @param	string 	$message
	*/
	function out($message) {
		if (false == $this->flag_output)
			return;
			
		print $message;
		flush();
	} // end func out
	
	/**
	* Encodes an element name so that it can be used as a file name.
	* @param	string	element name
	* @return	string 	url name
	*/
	function nameToUrl($name) {
		return preg_replace("@[\s\./\\:]@", "_", $name);
	} // end func nameToUrl

	
	/**
	* Print a string, the specified HTML line break sign and flushes the output buffer
	* @param	string 	$message
	*/
	function outl($message) {
		if (false == $this->flag_output) 
			return;
			
		print "$message\n";
		flush();
	} // end func outl
	
	/**
	* Dumps objects and arrays.
	* 
	* Use this function to get an idea of the internal datastructures used. 
	* The function dumps arrays and objects. It renders the content in 
	* an HTML table. Play with it, you'll see it's very helpful
	* for debugging.
	* 
	* @param	string	$title	Optional title used in the HTML Table
	* @param	mixed		$data		Optional array or object that you want to dump. 
	* 												Fallback to $this.
	* @param	boolean	$userfunction	Optional flag. If set to false userfunction
	*													in an object are not shown (default). If set to 
	*													true, userfunctions are rendered
	*
	*	@access		public
	* @version	0.2
	*/
	function introspection($title="", $data = "", $userfunction = true) {
		
		if (""==$data)
			$data = $this;
		
		printf('<table border="1" cellspacing="4" cellpadding="4" bordercolor="Silver">%s',
							$this->CR_HTML
						);	
						
		if (""!=$title)
			printf('<tr>%s<td colspan=4><b>%s</b></td>%s</tr>%s', 
								$this->CR_HTML,
								$title,
								$this->CR_HTML,
								$this->CR_HTML
							);
		
		reset($data);
		while (list($k, $v)=each($data)) {
		
			if ("user function"==gettype($v) && !$userfunction) 
				continue;
			
			if (is_array($v) || is_object($v)) {
				
				
				$color="navy";
					
				printf('<tr>
									<td align="left" valign="top">
										<font color="%s"><pre><b>%s</b></pre></font>
									</td>
									<td align="left" valign="top"><font color="%s"><pre>=></pre></font></td>
									<td align="left" valign="top" colspan=2>',
										$color,
										$k,
										$color,
										str_replace("<", "&lt;", $v)
								);
								
				$this->introspection("", $v, $userfunction);
					
				printf('</td>%s</tr>%s', $this->CR_HTML, $this->CR_HTML);
				
			}	else {
				
				$color="black";
					
				printf('<tr>
									<td align="left" valign="top">
										<font color="%s"><pre><b>%s</b></pre></font>
									</td>
									<td align="left" valign="top"><pre><font color="%s">=></pre></font></td>
									<td align="left" valign="top"><pre><font color="%s">[%s]</font></pre></td>
									<td align="left" valign="top"><pre><font color="%s">"%s"</font></pre></td>
								</tr>',
									$color,
									$k,
									$color,
									$color,
									gettype($v),
									$color,
									str_replace("<", "&lt;", $v)
								);
			}
		}
		print '</table>'.$this->CR_HTML;
	} // end func introspection
	
} // end class PhpdocObject
?>