<?php

/**
* 
* Handle templating functions to separate business logic from display
* logic.  See the docs at {@link http://phpsavant.com/}.
*
* This program is free software; you can redistribute it and/or modify
* it under the terms of the GNU Lesser General Public License as
* published by the Free Software Foundation; either version 2.1 of the
* License, or (at your option) any later version.
*
* This program is distributed in the hope that it will be useful, but
* WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
* Lesser General Public License for more details.
*
* @license http://www.gnu.org/copyleft/lesser.html LGPL
* 
* @author Paul M. Jones <pmjones@ciaweb.net>
* 
* @package Savant
* 
* @version 1.5 stable
*
* $Id: Savant.php,v 1.26 2004/05/14 21:45:33 pmjones Exp $
* 
*/

define ('SAVANT_ERROR',                       0);
define ('SAVANT_ERROR_PLUGIN_NOT_FOUND',      1);
define ('SAVANT_ERROR_ASSIGN',                2);
define ('SAVANT_ERROR_ASSIGN_REF',            3);
define ('SAVANT_ERROR_ASSIGN_OBJECT',         4);
define ('SAVANT_ERROR_ASSIGN_NOT_OBJECT',     5);
define ('SAVANT_ERROR_TEMPLATE',              6);
define ('SAVANT_ERROR_TEMPLATE_NOT_FOUND',    7);
define ('SAVANT_ERROR_TEMPLATE_STACK_EMPTY',  8);
define ('SAVANT_ERROR_FILTER',                9);
define ('SAVANT_ERROR_FILTER_NOT_FOUND',     10);
define ('SAVANT_ERROR_TOKEN_NOT_FOUND',      11);

require_once AT_INCLUDE_PATH . 'classes/XML/XML_HTMLSax/PEAR.php';

class Savant extends PEAR {
	
	
	// -----------------------------------------------------------------
	//
	// Properties
	//
	// -----------------------------------------------------------------
	
	/**
	* 
	* Directory separator.  Use '/' for Unix, '\' for Windows.
	* 
	* @access private
	* 
	* @var string
	* 
	*/
	
	var $_dir_sep = '/';
	
	
	/**
	* 
	* Localized error messages.
	* 
	* @access private
	* 
	* @var array
	* 
	*/
	
	var $_errors = array(
		SAVANT_ERROR                      => 'Savant unknown error',
		SAVANT_ERROR_PLUGIN_NOT_FOUND     => 'Savant plugin not found',
		SAVANT_ERROR_ASSIGN               => 'Savant assign() parameters not passed properly',
		SAVANT_ERROR_ASSIGN_REF           => 'Savant assignRef() parameters not passed properly',
		SAVANT_ERROR_ASSIGN_OBJECT        => 'Savant assignObject() parameters not passed properly',
		SAVANT_ERROR_ASSIGN_NOT_OBJECT    => 'Savant assignObject() value is not an object',
		SAVANT_ERROR_TEMPLATE             => 'Savant template error',
		SAVANT_ERROR_TEMPLATE_NOT_FOUND   => 'Savant template not found or not readable',
		SAVANT_ERROR_TEMPLATE_STACK_EMPTY => 'Savant template stack is empty',
		SAVANT_ERROR_FILTER               => 'Savant filter error',
		SAVANT_ERROR_FILTER_NOT_FOUND     => 'Savant filter not found',
		SAVANT_ERROR_TOKEN_NOT_FOUND      => 'Savant token not found'
	);
	
	
	/**
	* 
	* This stack keeps track of the current template file being
	* processed. The current template is always the last one in the
	* stack (count - 1).
	* 
	* @access private
	* 
	* @var array
	*
	* @see _pushTemplate()
	*
	* @see _popTemplate()
	*
	* @see _getTemplate()
	* 
	*/
	
	var $_template_stack = array();
	
	
	/**
	* 
	* Holds references of assigned variables (by-reference).
	* 
	* @access private
	* 
	* @var array
	* 
	*/
	
	var $_token_refs = array();
	
	
	/**
	* 
	* Holds copies of assigned variables (by-value and by-copy).
	* 
	* @access private
	* 
	* @var array
	* 
	*/
	
	var $_token_vars = array();
	
	
	/**
	* 
	* This associative array keeps track of filter aliases; the key is
	* the "source" (original) filter name, and the value is the "target"
	* (replacement) filter.
	* 
	* @access private
	* 
	* @var array
	*
	*/
	
	var $_filter_map = array();
	
	
	/**
	* 
	* This associative array holds an instance of every filter called as
	* an instance (as versus the filters called statically).  We hold on
	* to instances so that we don't create a new instance every time the
	* same filter is called; this allows for persistence of the filter.
	* The array key is the filter name, and the array value is an
	* instance of the filter class.
	* 
	* @access private
	* 
	* @var array
	*
	*/
	
	var $_filter_obj = array();
	
	
	/**
	* 
	* This associative array keeps track of whether filters are turned
	* off or on.  The key is the filter name, and the value is a boolean
	* true (if the filter is active) or false (if the filter is not).
	* 
	* @access private
	* 
	* @var array
	*
	*/
	
	var $_filter_set = array();
	
	
	/**
	*
	* Temp holding place for fetch() output (we use a property instead of
	* a local var to avoid conflicting with extracted token values).
	*
	* @access private
	*
	* @var string
	*
	*/
	
	var $_output = '';
	
	
	/**
	* 
	* The set of base-paths for all user-defined class files.
	* Each should end in a directory separator; e.g., "/".
	* 
	* @access private
	* 
	* @var array
	* 
	*/
	
	var $_path = array(
		'plugin'   => array(),
		'filter'   => array(),
		'template' => array()
	);
	
	
	/**
	* 
	* This associative array keeps track of plugin aliases; the key is
	* the "source" (original) plugin name, and the value is the "target"
	* (replacement) plugin.
	* 
	* @access private
	* 
	* @var array
	*
	*/
	
	var $_plugin_map = array();
	
	
	/**
	* 
	* This associative array holds an instance of every plugin called as
	* an instance (as versus the plugins called statically).  We hold on
	* to instances so that we don't create a new instance every time the
	* same plugin is called; this allows for persistence of the plugin.
	* The array key is the plugin name, and the array value is an
	* instance of the plugin class.
	* 
	* @access private
	* 
	* @var array
	*
	*/
	
	var $_plugin_obj = array();
	
	
	/**
	* 
	* This holds the default template file name.
	*
	* Will be used by display() and fetch() if no template name is
	* passed to them.  Set with setTemplate().
	* 
	* @access private
	* 
	* @var string
	* 
	*/
	
	var $_defaultTemplate = null;
	
	
	// -----------------------------------------------------------------
	//
	// Constructor method
	//
	// -----------------------------------------------------------------
	
	
	/**
	* 
	* Constructor.
	* 
	* @param array $conf An associative array of configuration options,
	* currently 'dir_sep', 'template_path', 'plugin_path',
	* 'filter_path', and 'errors'. The 'template_path', 'plugin_path',
	* and 'filter_path' values may be individual strings or sequential
	* arrays of strings.
	* 
	*/
	
	function Savant($conf = array())
	{
		// force the config param to be an array
		settype($conf, 'array');
		
		// set the directory separator
		if (isset($conf['dir_sep'])) {
			$this->_dir_sep = $conf['dir_sep'];
		} else {
			$this->_dir_sep = DIRECTORY_SEPARATOR;
		}
		
		// add default plugin path
		$this->addPath('plugin', dirname(__FILE__) . '/Savant/plugins/');
		
		// add user-configured plugin paths
		if (isset($conf['plugin_path'])) {
			settype($conf['plugin_path'], 'array');
			krsort($conf['plugin_path']);
			foreach ($conf['plugin_path'] as $key => $val) {
				$this->addPath('plugin', $val);
			}
		} else {
			// default to the current directory (BC)
			$this->addPath('plugin', '');
		}
		
		// add default filter path
		$this->addPath('filter', dirname(__FILE__) . '/Savant/filters/');
		
		// add user-configured filter paths
		if (isset($conf['filter_path'])) {
			settype($conf['filter_path'], 'array');
			krsort($conf['filter_path']);
			foreach ($conf['filter_path'] as $key => $val) {
				$this->addPath('filter', $val);
			}
		} else {
			// default to the current directory (BC)
			$this->addPath('filter', '');
		}
		
		// add user-configured template paths
		if (isset($conf['template_path'])) {
			settype($conf['template_path'], 'array');
			krsort($conf['template_path']);
			foreach ($conf['template_path'] as $key => $val) {
				$this->addPath('template', $val);
			}
		} else {
			// default to the current directory (BC)
			$this->addPath('template', '');
		}
		
		// set user-defined error strings
		if (isset($conf['errors']) && is_array($conf['errors'])) {
			foreach ($conf['errors'] as $key => $val) {
				// becuase config files might treat number keys as
				// string keys, we force each to be an integer.
				$this->_errors[(int)$key] = $val;
			}
		}
	}
	
	
	// -----------------------------------------------------------------
	//
	// Token assignment methods
	//
	// -----------------------------------------------------------------
	
	
	/**
	* 
	* Assigns a token-name and value to $this->_token_vars for use in a
	* template.
	* 
	* There are three valid ways to assign values to a template.
	* 
	* Form 1: $args[0] is a string and $args[1] is mixed. This means
	* $args[0] is a token name and $args[1] is the token value (which
	* allows objects, arrays, strings, numbers, or anything else). 
	* $args[1] can be null, which means the corresponding token value in
	* the template will also be null.
	* 
	* Form 2: $args[0] is an array and $args[1] is not set. Assign a
	* series of tokens where the key is the token name, and the value is
	* token value.
	* 
	* Form 3: $args[0] is an object and $args[1] is not set.  Assigns
	* copies of all object variables (properties) to tokens; the token
	* name and value is a copy of each object property and value.
	* 
	* @access public
	* 
	* @param string|array|object $args[0] This param can be a string, an
	* array, or an object.  If $args[0] is a string, it is the name of a
	* variable in the template.  If $args[0] is an array, it must be an
	* associative array of key-value pairs where the key is a variable
	* name in the template and the value is the value for that variable
	* in the template.  If $args[0] is an object, copies of its
	* properties will be assigned to the template.
	* 
	* @param mixed $args[1] If $args[0] is an array or object, $args[1]
	* should not be set.  Otherwise, a copy of $args[1] is assigned to a
	* template variable named after $args[0].
	* 
	* @return bool|PEAR_Error Boolean true if all assignments were
	* committed, or a PEAR_Error object if there was an error.
	* 
	* @throws SAVANT_ERROR_ASSIGN Unknown reason for error, probably
	* because you passed $args[1] when $args[0] is an array or object.
	* 
	* @see assignRef()
	* 
	* @see assignObject()
	* 
	*/
	
	function assign()
	{	
		// in Form 1, $args[0] is a string name and $args[1] is mixed.
		// in Form 2, $args[0] is an associative array.
		// in Form 3, $args[0] is an object.
		$args = func_get_args();
		$count = count($args);
		
		// -------------------------------------------------------------
		//
		// Now we assign variable copies.
		//
		
		// form 1 (string name and mixed value)
		// don't check isset() on $args[1] becuase a 'null' is not set,
		// and we might want to pass a null.
		if (is_string($args[0]) && $count > 1) {
			
			// keep a copy in the token vars array
			$this->_token_vars[$args[0]] = $args[1];
			
			// unset any reference with the same name
			$this->_unset_token('refs', $args[0]);
			
			// done!
			return true;
		}
		
		// form 2 (assoc array)
		if (is_array($args[0]) && $count == 1) {
		
			// keep copies in the token vars array
			$this->_token_vars = array_merge($this->_token_vars, $args[0]);
			
			// unset all references with matching names
			$keys = array_keys($args[0]);
			foreach ($keys as $val) {
				$this->_unset_token('refs', $val);
			}
			
			// done!
			return true;
		}
		
		// form 3 (object props)
		if (is_object($args[0]) && $count == 1) {
			
			// get the object properties
			$data = get_object_vars($args[0]);
			
			// keep copies of the object properties in the token_vars array
			$this->_token_vars = array_merge($this->_token_vars, $data);
			
			// unset all references with matching names
			$keys = array_keys($data);
			foreach ($keys as $val) {
				$this->_unset_token('refs', $val);
			}
			
			// done!
			return true;
		}
		
		
		// -------------------------------------------------------------
		//
		// Final error catch.  We should not have gotten to this point.
		//
		
		return $this->throwError(
			$this->_errors[SAVANT_ERROR_ASSIGN],
			SAVANT_ERROR_ASSIGN,
			array(
				'class' => get_class($this),
				'method' => 'assign',
				'file' => __FILE__,
				'line' => __LINE__
			)
		);
	}
	
	
	/**
	* 
	* Assign a token by reference.  This allows you change variable
	* values within the template and have those changes reflected back
	* at the calling logic script.  Works as with form 2 of assign().
	* 
	* @access public
	* 
	* @param string $name The template token-name for the reference.
	* 
	* @param mixed &$ref The variable passed by-reference.
	* 
	* @return bool|PEAR_Error Boolean true on success, or a PEAR_Error
	* on failure.
	* 
	* @throws SAVANT_ERROR_ASSIGN_REF Unknown reason for error.
	* 
	* @see assign()
	* 
	* @see assignObject()
	* 
	*/
	
	function assignRef($name, &$ref)
	{
		// look for the proper case: name and variable
		if (is_string($name) && isset($ref)) {
		
			// assign the token as a reference
			$this->_token_refs[$name] =& $ref;
			
			// unset any matching token vars
			$this->_unset_token('vars', $name);
			
			// done!
			return true;
		}
		
		// final error catch
		return $this->throwError(
			$this->_errors[SAVANT_ERROR_ASSIGN_REF],
			SAVANT_ERROR_ASSIGN_REF,
			array(
				'class' => get_class($this),
				'method' => 'assignRef',
				'file' => __FILE__,
				'line' => __LINE__
			)
		);
	}
	
	
	/**
	* 
	* This is similar to assignRef(), except that it is specifically for
	* assigning objects by reference.
	* 
	* @access public
	* 
	* @param string $name The template token-name for the object
	* reference.
	* 
	* @param mixed &$obj The object to be passed by-reference.
	* 
	* @return bool|PEAR_Error Boolean true on success, or a PEAR_Error
	* on failure.
	* 
	* @throws SAVANT_ERROR_ASSIGN_OBJECT Unknown reason for error.
	* 
	* @throws SAVANT_ERROR_ASSIGN_NOT_OBJECT The second argument was not
	* an object.
	* 
	* @see assign()
	* 
	* @see assignRef()
	* 
	*/
	
	function assignObject($name, &$obj)
	{
		// look for the proper case: name and object
		if (is_string($name) && is_object($obj)) {
		
			// assign the token as a reference
			$this->_token_refs[$name] =& $obj;
			
			// unset any matching token vars
			$this->_unset_token('vars', $name);
			
			// done!
			return true;
		}
		
		// error catch: not an object
		if (! is_object($obj)) {
		
			// the assignment is not a object
			return $this->throwError(
				$this->_errors[SAVANT_ERROR_ASSIGN_NOT_OBJECT] . " ('$name')",
				SAVANT_ERROR_ASSIGN_NOT_OBJECT,
				array(
					'class' => get_class($this),
					'method' => 'assignObject',
					'file' => __FILE__,
					'line' => __LINE__
				)
			);
		}
		
		// final error catch
		return $this->throwError(
			$this->_errors[SAVANT_ERROR_ASSIGN_OBJECT],
			SAVANT_ERROR_ASSIGN_OBJECT,
			array(
				'class' => get_class($this),
				'method' => 'assignObject',
				'file' => __FILE__,
				'line' => __LINE__
			)
		);
	}
	
	
	/**
	*
	* Unsets the the token array for either 'vars' or 'refs'.
	*
	* It used to be that Savant kept all token assignments in one array,
	* and then the fetch() method would use extract() to pull the tokens
	* into the local scope for the template.  However, extract() doesn't
	* play nice with references.  As a result, fetch() has to do two
	* "extractions": one for the token_vars array, and a different one for
	* the token_refs array.
	*
	* We want the developer to think of assigned tokens as being in one
	* array, though.  So if he assigns a token variable called "foo" and
	* then assigns a token reference also called "foo", the second
	* assignment should take precedence over the first.  Thus, the
	* assign*() methods call this function to unset any prior
	* occurrences of a matching token name in the token_vars or
	* token_refs array.
	*
	* @access public
	*
	* @param string $type The type of token to unset (either 'vars' or 'refs').
	*
	* @param string $key The token key name to unset.
	*
	* @return void
	*
	* @see assign()
	*
	* @see assignRef()
	*
	* @see assignObject()
	*
	*/
	
	function _unset_token($type, $key)
	{
		if ($type == 'vars' && isset($this->_token_vars[$key])) {
			unset($this->_token_vars[$key]);
		}
		
		if ($type == 'refs' && isset($this->_token_refs[$key])) {
			unset($this->_token_refs[$key]);
		}
		
		return;
	}
	
	
	// -----------------------------------------------------------------
	//
	// Template parsing methods
	//
	// -----------------------------------------------------------------
	
	
	/**
	* 
	* Parse and display a template file using the values in
	* $this->_token_vars and $this->token_refs.
	* 
	* @param string $tpl The name of the .tpl.php template file to
	* parse; the base template path is automatically prefixed to the
	* name.
	* 
	* @return void|PEAR_Error No return on success, it just runs the
	* template script (which should probably generate some output ;-).
	* If the template file is not found, it returns a PEAR_Error object.
	* 
	* @throws SAVANT_ERROR_TEMPLATE_NOT_FOUND Could not find the requested
	* template script in the template path.
	* 
	*/
	
	function display($tpl = null)
	{
		// fetch the template results
		$result = $this->fetch($tpl);
		
		// return errors, or print the results
		if ($this->isError($result)) {
		
			// this will reset the called_from value
			// when the error returns from ::fetch()
			$result->userinfo['called_from'] =
				get_class($this) . '::display()';
			
			return $result;
			
		} else {
			echo $result;
		}
	}
	
	
	/**
	* 
	* Parse a template file using the token values in $this->_token_vars
	* and $this->_token_refs, and return the results as a string.
	* 
	* @param string $tpl The name of the .tpl.php template file to
	* parse; the base template path is automatically prefixed to the
	* name.
	* 
	* @return string|PEAR_Error The output of the the parsed template on
	* success; if the template file is not found, it returns a
	* PEAR_Error object.
	* 
	* @throws SAVANT_ERROR_TEMPLATE_NOT_FOUND Could not find the requested
	* template script in the template path.
	* 
	*/
	
	function fetch($tpl = null)
	{
		// use the default template if one is not passed
		if (is_null($tpl)) {
			$tpl = $this->_defaultTemplate;
		}
		
		// clear any previous output results
		$this->_output = '';
		
		// add the template request to the stack
		$result = $this->_pushTemplate($tpl);
		
		// was the template actually there?
		if ($this->isError($result)) {
			$result->userinfo['called_from'] =
				get_class($this) . '::fetch()';
			return $result;
		}
		
		// clear out these var so as not to introduce
		// them into the template by accident
		unset($tpl);
		unset($result);
		
		// extract the by-reference tokens into the local namespace.
		// unset any instance of $this (so as not to overwrite this
		// class instance) as well as __SAVANT__KEY__ and
		// __SAVANT__VAL__ (which we use for the extraction loop).
		// 
		// we do it this way because extract() will not extract
		// references properly. Yes, this is ugly.  If you have a better
		// way of preserving references, please let me know.
		if (is_array($this->_token_refs)) {
			
			// unset offending token names
			unset($this->_token_refs['this']);
			unset($this->_token_refs['__SAVANT__KEY__']);
			unset($this->_token_refs['__SAVANT__VAL__']);
			
			// loop through each token ref and bring it into the
			// local scope using a variable-variable.
			foreach ($this->_token_refs as
				$__SAVANT__KEY__ => $__SAVANT__VAL__) {
				
				$$__SAVANT__KEY__ =&
					$this->_token_refs[$__SAVANT__KEY__];
					
			}
			
			// unset these so they don't pollute the local scope
			// when we extract() token vars by-copy
			unset($__SAVANT__KEY__);
			unset($__SAVANT__VAL__);
		}
		
		// extract the by-copy token vars into the local namespace.
		// unset any instance of $this (so as not to overwrite this
		// class instance).
		if (is_array($this->_token_vars)) {
			unset($this->_token_vars['this']);
			extract($this->_token_vars);
		}
		
		// start capturing output into a buffer
		ob_start();
		
		// include the requested template filename in the local scope
		// (this will execute the template display logic).  don't pop
		// as we go; we may need to know the name of the current template
		// within the template itself.
		include($this->_getTemplate());
		
		// done with the requested template; get the buffer and 
		// clear it.
		$this->_output = ob_get_contents();
		ob_end_clean();
		
		// now apply filters; we do it in another method
		// so as not to corrupt the local namespace (we used extract() earlier,
		// which may have introduced references into the local 
		// namespace, and we don't want to change the values of those
		// references.
		$this->_applyFilters($this->_output);
		
		// and we're done!  pop the template off the stack
		// and return the results;
		$this->_popTemplate();
		return $this->_output;
	}
	
	
	/**
	*
	* Returns the full path for an arbitrary template file.
	*
	* Normally, inside a template file, if you want to include a template,
	* you need to use the PHP native include() or require() statement.
	* This is fine if you know the path to the file.  However, sometimes
	* you will want to grab the tempalte using the Savant-defined
	* template paths; you could do $this->display('template_name') from
	* within the template, but that incurs a terrible resource hit (as
	* the variables are re-introduced into a new display() space).
	*
	* This method is the middle way: it returns the path to a 
	* named template using the template paths, which you can then
	* include, like so:
	*
	* include $this->findTemplate('template.tpl.php');
	*
	* @access public
	*
	* @param string $tpl The template file name to look for.
	*
	* @return bool|string Boolean false if the file was not found 
	* anywhere in the template paths, or the full path to the file
	* if it was found.
	*
	*/
	
	function findTemplate($tpl)
	{
		$file = $this->_findFile('template', $tpl);
		
		// does that file exist?
		if (! $file) {
			
			// the template file is not there
			return $this->throwError(
				$this->_errors[SAVANT_ERROR_TEMPLATE_NOT_FOUND] .
					" ('$tpl')",
				SAVANT_ERROR_TEMPLATE_NOT_FOUND,
				array(
					'class' => get_class($this),
					'method' => 'findTemplate',
					'file' => __FILE__,
					'line' => __LINE__
				)
			);
				
		} else {
			return $file;
		}
	}
	
	
	/**
	*
	* Sets the default template for display() and fetch().
	*
	* @access public
	*
	* @param string $file The template file name to look for.
	*
	* @return bool|string Boolean false if template not found in the
	* template paths, or the full path to the default template file.
	*
	*/
	
	function setTemplate($file)
	{
		$this->_defaultTemplate = $this->findTemplate($file);
		return $this->_defaultTemplate;
	}
	
	
	/**
	* 
	* Push a template filename onto the stack; that will make it the
	* current template. This also checks to see if the template exists
	* in the template path set.
	* 
	* @access private
	* 
	* @param string $tpl The name of the template file.  This will be
	* prefixed with the template path where it is found.
	* 
	* @return boolean|PEAR_Error True on success, or a PEAR_Error on
	* failure.
	* 
	* @throws SAVANT_ERROR_TEMPLATE_NOT_FOUND The template was not found
	* in the template path set, or it is not readable.
	* 
	*/
	
	function _pushTemplate($tpl)
	{
		// find the file
		$file = $this->_findFile('template', $tpl);
		
		// does that file exist?
		if (! $file) {
			
			// the template file is not there
			return $this->throwError(
				$this->_errors[SAVANT_ERROR_TEMPLATE_NOT_FOUND] .
					" ('$tpl')",
				SAVANT_ERROR_TEMPLATE_NOT_FOUND,
				array(
					'class' => get_class($this),
					'method' => '_pushTemplate',
					'file' => __FILE__,
					'line' => __LINE__
				)
			);
				
		} else {
			// add the file name with prefix to the stack
			array_push($this->_template_stack, $file);
			return true;
		}
	}
	
	
	/**
	* 
	* Get the current template file name from the stack, with or without
	* the default path prefix.  This does not pop the name off the stack.
	* 
	* @access private
	* 
	* @return string|PEAR_Error The path and name of the template file,
	* or a PEAR_Error on failure.
	* 
	* @throws SAVANT_ERROR_TEMPLATE_STACK_EMPTY The template stack is
	* empty.
	* 
	*/
	
	function _getTemplate()
	{
		$k = count($this->_template_stack);
		
		if ($k > 0) {
			// there is at least one template on the stack
			return $this->_template_stack[$k - 1];
		} else {
			// no templates on the stack, so nothing to get
			return $this->throwError(
				$this->_errors[SAVANT_ERROR_TEMPLATE_STACK_EMPTY],
				SAVANT_ERROR_TEMPLATE_STACK_EMPTY,
				array(
					'class' => get_class($this),
					'method' => '_getTemplate',
					'file' => __FILE__,
					'line' => __LINE__
				)
			);
		}
	}
	
	
	/**
	* 
	* Pop a template file name off the stack.  Optionally prefixes the
	* default template path to the template file name.
	* 
	* @access private
	* 
	* @return string|PEAR_Error The path and name of the template file,
	* or a PEAR_Error on failure.
	* 
	* @throws SAVANT_ERROR_TEMPLATE_STACK_EMPTY The template stack is
	* empty.
	* 
	*/
	
	function _popTemplate()
	{
		$k = count($this->_template_stack);
		
		if ($k > 0) {
			// there is at least one template on the stack
			array_pop($this->_template_stack);
		} else {
			// no templates on the stack, so nothing to pop off
			return $this->throwError(
				$this->_errors[SAVANT_ERROR_TEMPLATE_STACK_EMPTY],
				SAVANT_ERROR_TEMPLATE_STACK_EMPTY,
				array(
					'class' => get_class($this),
					'method' => '_popTemplate',
					'file' => __FILE__,
					'line' => __LINE__
				)
			);
		}
	}
	
	
	// -----------------------------------------------------------------
	//
	// Plugin methods
	//
	// -----------------------------------------------------------------
	
	
	/**
	* 
	* Map (alias) one plugin name onto another.  This means that calls
	* to the original plugin using $this->plugin() and $this->splugin()
	* will be handled by the target plugin instead.
	* 
	* E.g., say you have a plugin called "stylesheet".  In your
	* templates, you would call it with $this->plugin('stylesheet').  If
	* you want to map all calls to 'stylesheet' to an extended version
	* of stylesheet without changing all your existing calls, you can
	* (in your template or in your business logic) call
	* $this->mapPlugin('stylesheet', 'extendedStyles') and then all
	* calls to 'stylesheet' will be handled by 'extendedStyles'.
	*
	* Note: your target plugin should exactly the same parameters as
	* the original.
	*
	* @access public
	* 
	* @return void
	* 
	*/
	
	function mapPlugin($original, $target)
	{
		$this->_plugin_map[$original] = $target;
	}
	
	
	/**
	* 
	* Execute a plugin and output its results to the browser.
	* 
	* @access public
	* 
	* @param string $name The name of the plugin class to execute, not
	* including the "Savant_Plugin_" prefix.
	* 
	* @return void|PEAR_Error A PEAR_Error is the plugin does not load,
	* or no return if the output is displayed.
	* 
	* @see splugin()
	* 
	*/
	
	function plugin($name)
	{
		// call $this->splugin() with all the arguments passed this method
		$args = func_get_args();
		$result = call_user_func_array(array(&$this, 'splugin'), $args);
		
		// return errors or print results
		if ($this->isError($result)) {
			$result->userinfo['called_from'] = get_class($this) .
				'::plugin()';
			return $result;
		} else {
			echo $result;
		}
	}
	
	
	/**
	* 
	* Execute a plugin and return the results (sort of like "sprintf").
	* 
	* @access public
	* 
	* @param string $requested_name The name of the plugin class to
	* execute, not including the "Savant_Plugin_" prefix.  We call this
	* the "requested" name because the plugin requested may be mapped to
	* a different plugin, in which case the mapped plugin wil be
	* executed, not the requested one.
	* 
	* @return mixed|PEAR_Error A PEAR_Error is the plugin does not load,
	* or the results of the plugin operations.
	* 
	*/
	
	function splugin($requested_name)
	{
		// attempt to load the plugin on-the-fly
		$result = $this->_loadPlugin($requested_name);
		if ($this->isError($result)) {
			$result->userinfo['called_from'] = get_class($this) .
				'::splugin()';
			return $result;
		}
		
		// find the mapped name (this is the same as the requested name
		// for non-mapped plugins, or the alias-target plugins name for
		// remapped plugins.
		$name = $this->_plugin_map[$requested_name];
		
		// get the arguments passed to this method
		$args = func_get_args();
		
		// first argument is always the plugin name; shift the first
		// argument off the front of the array and reduce the number of
		// array elements.
		array_shift($args);
		
		// add a reference to this Savant instance to the arguments
		array_unshift($args, $this);
		
		// prefix the actual plugin name with "Savant_Plugin_"
		$class = "Savant_Plugin_$name";
		
		
		// check if the Savant_Plugin_$name class should be called statically
		// or as an instance (based on having a constructor).
		//
		// ugly hack:
		// 
		// becuase PHP adds a constructor function to all classes that
		// extend from any other class with a constructor, we can't
		// depend on merely having a constructor (or not) as a definite
		// way of determining if we should call a plugin statically or
		// by instance.
		// 
		// however, if PHP adds the constructor function on its own, it
		// will be the last one in the method list.  so: we check to see
		// if the constructor is last; if so, it's not "really" there,
		// and we call statically, but if it's not last, it is "really"
		// there, and we call as an instance.
		//
		// among other things, this means that your constructor method
		// should not be the last method in your plugin.
		
		$call_as_instance = false;
		
		$methods = get_class_methods($class);
		$last = count($methods) - 1;
		$tmpname = strtolower($class);
		
		foreach ($methods as $key => $val) {
			if ($key != $last && $val == $tmpname) {
				$call_as_instance = true;
				break;
			}
		}
		
		// conserve memory
		unset($tmpname);
		unset($last);
		unset($methods);
		
		// call the plugin as an instance or as static?
		if ($call_as_instance) {
			
			// calling as an object instance.
			// do we already have an instance of the plugin?
			if (! isset($this->_plugin_obj[$name]) ||
				! is_object($this->_plugin_obj[$name])) {
				
				// no, so create one
				$this->_plugin_obj[$name] =& new $class($this);
				
			}
			
			// now call the plugin
			$result = call_user_func_array(
				array(&$this->_plugin_obj[$name], $name),
				$args
			);
			
		} else {
			
			// default: call the self-named main method as static.
			$result = call_user_func_array(array($class, $name), $args);
			
		}
		
		return $result;
	}
	
	
	/**
	* 
	* Loads a plugin class definition file.  Searches the user-defined
	* plugin directory first, and then the default Savant package
	* plugins.
	* 
	* @access private
	* 
	* @param string $requested_name The name of the plugin class to
	* load, not including the "Savant_Plugin_" prefix or the ".php"
	* suffix.  We call this the "requested" name because the plugin
	* requested may be mapped to a different plugin, in which case the
	* mapped plugin wil be loaded, not the requested one.
	* 
	* @return boolean|PEAR_Error True if the plugin is successfully
	* loaded for the first time, false if the plugin is already loaded,
	* or a PEAR_Error on failure to load at all.
	* 
	*/
	
	function _loadPlugin($requested_name)
	{
		// see if we're attempting to load a plugin that is mapped
		// to another plugin.
		if (isset($this->_plugin_map[$requested_name])) {
			// get the mapped name (different if it's mapped, the
			// same if it's not)
			$name = $this->_plugin_map[$requested_name];
		} else {
			// not in the map, so register it
			$name = $requested_name;
			$this->_plugin_map[$name] = $name;
		}
		
		// is it already defined?  if so, don't define it again.
		if (class_exists("Savant_Plugin_$name")) {
			return false;
		}
		
		// get the plugin from the path
		$file = $this->_findFile('plugin', "Savant_Plugin_$name.php");
		
		// did we find it?
		if (! $file) {
		
			// could not find either the user-defined or
			// default plugin location.
			return $this->throwError(
				$this->_errors[SAVANT_ERROR_PLUGIN_NOT_FOUND] . " ('$name')",
				SAVANT_ERROR_PLUGIN_NOT_FOUND,
				array(
					'class' => get_class($this),
					'method' => '_loadPlugin',
					'file' => __FILE__,
					'line' => __LINE__
				)
			);
			
		} else {
			
			// found it! load the plugin class file.
			include_once($file);
			return true;
		
		}
	}
	
	
	// -----------------------------------------------------------------
	//
	// Filter methods
	//
	// -----------------------------------------------------------------
	
	
	/**
	* 
	* Set a filter to be active or inactive; loads the filter on-the-fly
	* as necessary. Active filters will automatically be applied to
	* display() and fetch() results.
	* 
	* @access public
	* 
	* @param string $requested_name The filter name we're requesting,
	* without the "Savant_Filter_" prefix or the ".php" suffix.
	* 
	*/
	
	function setFilter($requested_name, $active = true)
	{
		// attempt to load the filter on-the-fly
		$result = $this->_loadFilter($requested_name);
		
		// did it work?
		if ($this->isError($result)) {
			
			// no :-(
			$result->userinfo['called_from'] = get_class($this) .
				'::setFilter()';
				
			return $result;
			
		} else {
		
			// yes :-)
			$this->_filter_set[$requested_name] = $active;
			return true;
			
		}
	}
	
	
	/**
	* 
	* Map (alias) one filter name onto another.
	* 
	* @access public
	* 
	* @return void
	* 
	*/
	
	function mapFilter($original, $target)
	{
		$this->_filter_map[$original] = $target;
	}
	
	
	/**
	* 
	* Loads a filter class definition file.  Searches the user-defined
	* filter directory first, and then the default Savant package
	* filters.
	* 
	* @access private
	* 
	* @param string $requested_name The name of the filter class to
	* load, not including the "Savant_Filter_" prefix or the ".php"
	* suffix.  We call this the "requested" name because the filter
	* requested may be mapped to a different filter, in which case the
	* mapped filter wil be loaded, not the requested one.
	* 
	* @return boolean|PEAR_Error True if the filter is successfully
	* loaded for the first time, false if the filter is already loaded,
	* or a PEAR_Error on failure to load at all.
	* 
	*/
	
	function _loadFilter($requested_name)
	{
		// see if we're attempting to load a filter that is mapped
		// to another filter.
		if (isset($this->_filter_map[$requested_name])) {
			// get the mapped name (different if it's mapped, the
			// same if it's not)
			$name = $this->_filter_map[$requested_name];
		} else {
			// not in the map, so register it
			$name = $requested_name;
			$this->_filter_map[$name] = $name;
		}
		
		// is it already defined?  if so, don't define it again.
		if (class_exists("Savant_Filter_$name")) {
			return false;
		}
		
		// stores whichever filter location we end up using
		$file = $this->_findFile('filter', "Savant_Filter_$name.php");
		
		// look for the filter file
		if (! $file) {

			// could not find either the user-defined or
			// default filter location.
			return $this->throwError(
				$this->_errors[SAVANT_ERROR_FILTER_NOT_FOUND] . " ('$name')",
				SAVANT_ERROR_FILTER_NOT_FOUND,
				array(
					'class' => get_class($this),
					'method' => '_loadFilter',
					'file' => __FILE__,
					'line' => __LINE__
				)
			);
			
		} else {
			
			// found it! load the filter class file.
			include_once($file);
			return true;
			
		}
	}
	
	
	/**
	* 
	* Applies all filters set to "true", in order, to the output of the
	* template script.
	* 
	* @param string &$text The output of the template script.
	* 
	* @return void
	* 
	*/
	
	function _applyFilters(&$text)
	{
		foreach ($this->_filter_set as $requested_name => $active) {
		
			// only apply filters that are set as active
			if ($active === true) {
				
				// get the mapped name of the requested filter (which
				// means the filter to be applied may be different from
				// the one requested)
				$name = $this->_filter_map[$requested_name];
				
				// get the class name
				$class = "Savant_Filter_$name";
				
				// check if the Savant_Filter_$name class should be
				// called statically or as an instance (based on having
				// a constructor).
				// 
				// ugly hack:
				// 
				// becuase PHP adds a constructor function to all
				// classes that extend from any other class with a
				// constructor, we can't depend on merely having a
				// constructor (or not) as a definite way of determining
				// if we should call a filter statically or by instance.
				// 
				// however, if PHP adds the constructor function on its
				// own, it will be the last one in the method list.  so:
				// we check to see if the constructor is last; if so,
				// it's not "really" there, and we call statically, but
				// if it's not last, it is "really" there, and we call
				// as an instance.
				// 
				// among other things, this means that your constructor
				// method should not be the last method in your filter.
				
				$call_as_instance = false;
				
				$methods = get_class_methods($class);
				$last = count($methods) - 1;
				$tmpname = strtolower($class);
				
				foreach ($methods as $key => $val) {
					if ($key != $last && $val == $tmpname) {
						$call_as_instance = true;
						break;
					}
				}
				
				// conserve memory
				unset($tmpname);
				unset($last);
				unset($methods);
				
				// call the filter as an instance or as static?
				if ($call_as_instance) {
					
					// calling as an object instance.
					// do we already have an instance of the filter?
					if (! isset($this->_filter_obj[$name]) ||
						! is_object($this->_filter_obj[$name])) {
						
						// no, so create one
						$this->_filter_obj[$name] =& new $class($this);
						
					}
					
					// now call the filter instance.
					$this->_filter_obj[$name]->$name($this, $text);
					
				} else {
					
					// default: call the self-named main method as
					// static.
					// 
					// another ugly hack: use eval() to call the
					// static method.
					// 
					// yes, eval() sucks.  it's the only way to maintain
					// backwards compatibility and avoid call-time
					// pass-by-reference. if you don't like it, add a
					// constructor function to your filter class and the
					// filter will be called as an instance.
					
					eval($class .'::'. $name . '($this, $text);');
					
					
					// the following line is the previous, offensive,
					// call-by-reference. we do the eval() above because
					// call_user_func() does not honor reference in the
					// target function parameters.
					// 
					// call_user_func(array($class, $name), &$this, &$text);
					
				}
			}
		}
	}
	
	
	// -----------------------------------------------------------------
	//
	// All-purpose
	//
	// -----------------------------------------------------------------
	
	
	/**
	* 
	* Add a path to a path array.
	* 
	* @access public
	* 
	* @param string $type The path-type to add (plugin, filter, or
	* template).
	* 
	* @param string $dir The directory to add to the path-type.
	* 
	* @return void
	* 
	*/
	
	function addPath($type, $dir)
	{
		$dir = $this->_fixPath($dir);
		if (! isset($this->_path[$type])) {
			$this->_path[$type] = array($dir);
		} else {
			array_unshift($this->_path[$type], $dir);
		}
	}
	
	
	/**
	* 
	* Get back the current names and values of one or all assigned
	* tokens, whether assigned by-copy/by-value or by-reference.
	* 
	* @access public
	* 
	* @param string $key The token key value to retrieve; if not
	* specified, returns all tokens as an associative array.
	* 
	* @return mixed An associative array of all assigned tokens if $key
	* is not specified, or a particular token value if $key is
	* specified.  If $key is not a token variable or reference, returns
	* a PEAR_Error object.
	* 
	*/
	
	function getTokens($key = null)
	{
		if (is_null($key)) {
		
			$result = array_merge($this->_token_vars, $this->_token_refs);
			ksort($result);
			return $result;
			
		} elseif (isset($this->_token_vars[$key])) {
		
			return $this->_token_vars[$key];
			
		} elseif (isset($this->_token_refs[$key])) {
		
			return $this->_token_refs[$key];
			
		} else {
		
			return $this->throwError(
				$this->_errors[SAVANT_ERROR_TOKEN_NOT_FOUND],
				SAVANT_ERROR_TOKEN_NOT_FOUND,
				array(
					'class' => get_class($this),
					'method' => 'getTokens',
					'file' => __FILE__,
					'line' => __LINE__
				)
			);
		}
	}
	
	
	/**
	* 
	* Get the current path array for a path-type.
	* 
	* @access public
	* 
	* @param string $type The path-type to look up (plugin, filter, or
	* template).  If not set, returns all path types.
	* 
	* @return array The array of paths for the requested type.
	* 
	*/
	
	function getPath($type = null)
	{
		if (is_null($type)) {
			return $this->_path;
		} elseif (! isset($this->_path[$type])) {
			return array();
		} else {
			return $this->_path[$type];
		}
	}
	
	
	/**
	* 
	* Searches a series of paths for a given file.
	* 
	* @param array $type The type of paths to search (template, plugin,
	* or filter).
	* 
	* @param string $file The file name to look for.
	* 
	* @return string|bool The full path and file name for the target file,
	* or boolean false if the file is not found in any of the paths.
	*
	*/
	
	function _findFile($type, $file)
	{
		// get the set of paths
		$set = $this->getPath($type);
		
		// start looping through them
		foreach ($set as $path) {
			$fullname = $path . $file;
			if (file_exists($fullname) && is_readable($fullname)) {
				return $fullname;
			}
		}
		
		// could not find the file in the set of paths
		return false;
	}
	
	
	/**
	* 
	* Append a trailing '/' to paths, unless the path is empty.
	* 
	* @author Ben Jones <ben.jones@healthleaders.com>
	* 
	* @access private
	* 
	* @param string $path The file path to fix
	* 
	* @return string The fixed file path
	* 
	*/
	
	function _fixPath($path)
	{
		$len = strlen($this->_dir_sep);
		
		if (! empty($path) &&
			substr($path, -1 * $len, $len) != $this->_dir_sep)	{
			return $path . $this->_dir_sep;
		} else {
			return $path;
		}
	}
}
?>