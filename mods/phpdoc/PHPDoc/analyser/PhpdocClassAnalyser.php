<?php
/**
* Analyses a class.
* 
* @version	$Id: PhpdocClassAnalyser.php,v 1.4 2000/12/03 22:37:36 uw Exp $
*/
class PhpdocClassAnalyser extends PhpdocAnalyser {

	/**
	* Class data.
	*
	* @var	array
	*/
	var $classes = array();
	
	/**
	* Name of the baseclass of the given classes.
	*
	* @var	string	
	*/
	var $baseclass = "";
	
	/**
	* Ordered list of all classes.
	*
	* @var	array
	*/ 
	var $classlist = array();
	
	/**
	* List of not inherited elements.
	*
	* @var	array
	*/
	var $notinherited = array(
															"class"	=> array(
																								"name"			=> true,
																								"extends"		=> true,
																								"undoc"			=> true,
																								"variables"	=> true,
																								"functions"	=> true,
																								"consts"		=> true,
																								"uses"			=> true,
																								"filename"	=> true,
																								"subclasses"=> true,
																								"path"			=> true,
																								"baseclass"	=> true,
																								"abstract"	=> true
																							),
																							
															"functions"	=> array(
																										"name"			=> true,
																										"undoc"			=> true,
																										"inherited"	=> true,
																										"overrides"	=> true,
																										"abstract"	=> true
																									),
																									
															"variables"	=> array(
																										"name"			=> true,
																										"undoc"			=> true,
																										"inherited"	=> true,
																										"overrides"	=> true,
																										"abstract"	=> true
																									),	
																									
															"uses"			=> array(
																										"name"			=> true,
																										"undoc"			=> true,
																										"inherited"	=> true,
																										"overrides"	=> true
																									),																																																								
															
															"consts"	=> array(
																										"name"			=> true,
																										"undoc"			=> true,
																										"inherited"	=> true,
																										"overrides"	=> true
																									)																														
													);
													

	/**
	* Puuuh - findUndocumented() needs this.
	*
	* @var	array
	* @see	findUndocumented()
	*/														
	var $undocumentedFields = array(
															"functions"	=> "function",
															"variables"	=> "variable",
															"uses"			=> "included file",
															"consts"		=> "constant"
													);

	/**
	* Sets the class data and the name of the baseclass.
	*
	* @param	array		Raw class data from the parser
	* @param	string	Name of the baseclass of the given classes
	* @access public
	*/														
	function setClasses($classes, $baseclass) {
		
		$this->classes = $classes;
		$this->baseclass = $baseclass;
		
	} // end func setClasses
													
	function analyse() {
		
		$this->flag_get = false;

		$this->updateAccessReturn();
		$this->updateBrothersSisters();
		$this->checkSee();
		
		$this->classlist = array();
		
		$this->buildBottomUpClasslist($this->baseclass);
		
	} // end func analyse

	/**
	* Returns an analysed class or false if there're no classes any more.
	*
	* @return	mixed	False if there no classes anymore, otherwise an array with 
	*								the data of the class.
	*	@access	public
	*/	
	function getClass() {
	
		if (!$this->flag_get) {
			reset($this->classlist);
			$this->flag_get = true;
		}
		if (list($k, $classname)=each($this->classlist)) {

			if (isset($this->classes[$classname]["path"]))
				$this->inheritClassElements($classname);	
			
			$this->checkFunctionArgs($classname);
			$this->findUndocumented($classname);
			
			$class = $this->classes[$classname];
			unset($this->classes[$classname]);
			return $class;
			
		} else {
		
			return false;
			
		}
	} // end func getClass

	/**
	* Looks for undocumented elements in a certain class
	* 
	* @param	string	Classname
	*/
	function findUndocumented($classname) {
		
		$file = $this->classes["filename"];
		if ($this->classes["undoc"])
			$this->warn->addDocWarning($file, "class", $name, "The class is not documented.", "missing");
			
		reset($this->undocumentedFields);
		while (list($index, $eltype)=each($this->undocumentedFields)) {
			if (!isset($this->classes[$index]))
				continue;
				
			reset($this->classes[$index]);
			while (list($elname, $data)=each($this->classes[$index]))
				if (isset($data["undoc"]) && $data["undoc"])
					$this->warn->addDocWarning($file, $eltype, $elname, "Undocumented element.", "missing");
					
		}
		
	} // end func findUndocumented
	
	/**
	* Checks the function documentation of a certain class.
	*
	* @param	string	Classname
	*/
	function checkFunctionArgs($classname) {

		if (!isset($this->classes[$classname]["functions"]))
			return;
				
		$file = $this->classes[$classname]["filename"];
			
		reset($this->classes[$classname]["functions"]);
		while (list($fname, $function)=each($this->classes[$classname]["functions"])) {

			$inherited = isset($function["paraminherited"]);			
			$this->classes[$classname]["functions"][$fname]["params"] = $this->checkArgDocs($function["args"], $function["params"], $fname, $file, $inherited);
			unset($this->classes[$classname]["functions"][$fname]["args"]);

			if ($inherited)
				unset($this->classes[$classname]["functions"][$fname]["paraminherited"]);
				
		}
	} // end func checkFunctionArgs
	
	/**
	* Builds an internal list of all classes.
	* 
	* The analyser needs an ordered list of all classes
	* to inherit information effective.
	* 
	* @param	string	Name of the class that starts the recursive build process. 
	* @see	$classlist
	*/
	function buildBottomUpClasslist($classname) {
		
		if (isset($this->classes[$classname]["subclasses"])) {
			
			reset($this->classes[$classname]["subclasses"]);
			while (list($subclass, $v)=each($this->classes[$classname]["subclasses"]))
				$this->buildBottomUpClasslist($subclass);
			
			$this->classlist[] = $classname;
			
		} else {
		 
		 	$this->classlist[] = $classname;
		
		}
	} // end func buildBottomUpClasslist

	/**
	* Adds inherited elements to a class.
	* 
	* @param	string	Classname
	* @return	boolean	$ok
	* @see	$classes, $notinherited, addInheritedElements()
	*/	
	function inheritClassElements($classname) {
		
		if (!isset($this->classes[$classname]["path"]))
			return false;

		$undoc = $this->classes[$classname]["undoc"];
				
		$path = $this->classes[$classname]["path"];
		reset($path);
		while (list($k, $parentclass)=each($path)) {

			$this->addInheritedElements($classname, $parentclass, "functions");
			$this->addInheritedElements($classname, $parentclass, "variables");
			$this->addInheritedElements($classname, $parentclass, "consts");
			$this->addInheritedElements($classname, $parentclass, "uses");
			
			reset($this->classes[$parentclass]);
			while (list($field, $value)=each($this->classes[$parentclass])) 
				if (!isset($this->notinherited["class"][$field]) && !isset($this->classes[$classname][$field]))
					$this->classes[$classname][$field] = $value;
			
			if ($undoc && !$this->classes[$parentclass]["undoc"]) {
				$this->classes[$classname]["docinherited"] = true;
				$this->classes[$classname]["undoc"] = false;
				$undoc = false;
			}
			
		}	
		
		return true;
	} // end func inheritClassElements
	
	/**
	* Adds inherited functions, variables, constants or included files to a class.
	*  
	* @param	string 	Name of the class that inherits the informations.
	* @param	string	Name of the parentclass
	* @param	string	Type of elements inherited: "functions", "variables", "uses", "consts"
	* @return boolean	$ok
	* @see	$classes, $notinherited, isUndocumented()
	*/
	function addInheritedElements($classname, $parentclass, $type) {
	
		if (!isset($this->classes[$parentclass][$type]))
			return false;
			
		reset($this->classes[$parentclass][$type]);
		while (list($elementname, $data)=each($this->classes[$parentclass][$type])) {
			
			if (!isset($this->classes[$classname][$type][$elementname])) {

				$this->classes[$classname]["inherited"][$type][$parentclass][$elementname] = true;			

			} else {
		
				$this->classes[$classname][$type][$elementname]["overrides"] = $parentclass;
				$this->classes[$classname][$type][$elementname]["undoc"] = $this->isUndocumented($parentclass, $type, $elementname);
				$this->classes[$classname]["overrides"][$type][$parentclass][$elementname] = true;
				
				reset($data);
				while (list($field, $value)=each($data)) {
				
					if (!isset($this->classes[$classname][$type][$elementname][$field]) && !isset($this->notinherited[$type][$field])) {
						$this->classes[$classname][$type][$elementname][$field] = $value;
						if ("params"==$field && "functions"==$type) $this->classes[$classname][$type][$elementname]["paraminherited"] = true;
					}
						
				}
			}
			
		}
		
		return true;
	} // end func addInheritedElements

	/**
	* Returns true if the requested element is undocumented and false if it's documented.
	*
	* The function checks if the element might inherit documentation
	* from any parentclass. 
	*
	* @param	string	Name of the class of the element
	* @param	string	Element type: functions, variables, uses, consts.
	* @param	string	Element name
	* @return	boolean	$ok
	*/	
	function isUndocumented($classname, $type, $elementname) {

		if ( !isset($this->classes[$classname][$type][$elementname]) ||	$this->classes[$classname][$type][$elementname]["undoc"] ||	!isset($this->classes[$classname]["path"]) ) 
			return true;
		
		$path = $this->classes[$classname]["path"];
		while (list($k, $parentclass)=each($path))
			if ($this->isUndocumented($parentclass, $type, $elementname))
				return true;
		
		return false;
	} // end func isUndocumented
	
	function updateBrothersSisters() {
		
		reset($this->classes);
		while (list($classname, $data)=each($this->classes)) {
			$this->updateBrotherSisterElements($classname, "functions");
			$this->updateBrotherSisterElements($classname, "variables");
		}	
		
	} // end func updateBrothersSisters
	
	/**
	* @param	string	Name of the class to update
	* @param	string	Elementtype: functions, variables, ...
	* @return	boolean
	*/
	function updateBrotherSisterElements($classname, $type) {
		
		if (!isset($this->classes[$classname][$type])) 
			return false;
			
		reset($this->classes[$classname][$type]);
		while (list($elementname, $data) = each($this->classes[$classname][$type])) {
			
			if (isset($data["brother"])) {

				$name = ( "functions" == $type ) ? substr($data["brother"], 0, -2) : substr($data["brother"], 1);
				$name = strtolower($name);

				if (!isset($this->classes[$classname][$type][$name])) {
				
					$this->warn->addDocWarning($this->classes[$classname]["filename"], $type, $elementname, "Brother '$name' is unknown. Tags gets ignored.", "mismatch");
					unset($this->classes[$classname][$type][$elementname]["brother"]);
					
				} else {
				
					$this->classes[$classname][$type][$elementname]["brother"] = $name;
					$this->classes[$classname][$type][$elementname] = $this->copyBrotherSisterFields($this->classes[$classname][$type][$elementname], $this->classes[$classname][$type][$name]);

				}

			}
			
		}
		
	} // end func updateBrotherSisterElements
	
	function updateAccessReturn() {
		
		reset($this->classes);
		while (list($classname, $data)=each($this->classes)) {
			
			if (!isset($data["access"]))
				$this->classes[$classname]["access"] = "private";
				
			$this->updateAccessReturnElements($classname, "functions");
			$this->updateAccessElements($classname, "variables");
			$this->updateAccessElements($classname, "consts");
			
		}
				
	} // end func updateAccessReturn
	
	/**
	* Updates access and return for certain elements.
	* 
	* This function should only be used to update functions.
	* Functions that have the same name as the class (constructors)
	* get return void and access public. Functions without 
	* access get access public and functions without return get
	* return void.
	* 
	* @param	string	Classname
	* @param	string	Element type: functions (, variables, consts, uses)
	* @return	boolean	$ok
	* @see	updateAccessReturn()
	*/
	function updateAccessReturnElements($classname, $type) {
		
		if (!isset($this->classes[$classname][$type]))
			return false;

		reset($this->classes[$classname][$type]);
		while (list($elementname, $data)=each($this->classes[$classname][$type])) {
		
			if (!isset($data["access"])) 
				$this->classes[$classname][$type][$elementname]["access"] = ("functions" == $type && strtolower($elementname) == strtolower($classname)) ? "public" : "private";
				
			if (!isset($data["return"]))
				$this->classes[$classname][$type][$elementname]["return"] = "void";
			else 
				if ("functions" == $type && $elementname == $classname) {
					$this->warn->addDocWarning($this->classes[$classname]["filename"], "functions", $elementname, "The constructor can't have a return value. @return gets ignored.", "mismatch");
					$this->classes[$classname]["functions"][$elementname]["return"] = "void";
				}
				
		}
				
	} // end func updateAccessReturnElements
	
	/**
	* Updates access tags.
	*
	* @param	string	Classname
	* @param	string	Element type: functions, variables, consts (, uses)
	* @see	updateAccessReturnElements()
	*/
	function updateAccessElements($classname, $type) {
		
		if (!isset($this->classes[$classname][$type]))
			return false;
			
		reset($this->classes[$classname][$type]);
		while (list($elementname, $data)=each($this->classes[$classname][$type])) {
			
			if (!isset($data["access"])) 
				$this->classes[$classname][$type][$elementname]["access"] = ("functions" == $type && $elementname == $classname) ? "public" : "private";
		
		}
		
	} // end func updateAccessElements
	
	function checkSee() {
		
		reset($this->classes);
		while (list($classname, $class) = each($this->classes)) {
		
			$this->buildElementlist($classname);
			
			if (isset($class["functions"])) 
				$this->checkSeeElements($class["functions"], $classname, "functions");
				
			if (isset($class["variables"]))
				$this->checkSeeElements($class["variables"], $classname, "variables");
			
			if (isset($class["consts"])) 
				$this->checkSeeElements($class["consts"], $classname, "consts");
				
			if (isset($class["uses"]))
				$this->checkSeeElements($class["uses"], $classname, "uses");
			
		}
		
	} // end func checkSee

	/**
	* Checks see references in the given element array (functions, variables...)
	*
	* References to variables and functions within the same class get checked.
	* It the references element does not exist, the reference gets deleted and 
	* a doc warning gets generated.
	* 
	* @param	array		List of functions, variables,...
	* @param	string	Name of the class that contains the given elements.
	* @param	string	Elementtype: functions, variables, consts, uses.
	*/	
	function checkSeeElements($elements, $classname, $eltype) {
		
		reset($elements);
		while (list($elname, $element) = each($elements)) {
		
			if (isset($element["see"])) {
				
				if (isset($element["see"]["var"])) {
					
					reset($element["see"]["var"]);
					while (list($k, $variable) = each($element["see"]["var"])) 
						if (!isset($this->elementlist["variables"][strtolower($variable["name"])])) {
							$this->warn->addDocWarning($this->classes[$classname]["filename"], "variables", $elname, "@see referrs to the variable '" . $variable["name"] . "' which is not defined in the class. Entry gets ignored.", "mismatch");
							unset($this->classes[$classname][$eltype][$elname]["see"]["var"][$k]);
						}

				}
				
				if (isset($element["see"]["function"])) {
					
					reset($element["see"]["function"]);
					while (list($k, $function) = each($element["see"]["function"]))
						if (!isset($this->elementlist["functions"][strtolower(substr($function["name"], 0, -2))])) {
							$this->warn->addDocWarning($this->classes[$classname]["filename"], "functions", $elname, "@see referrs to the function '" . $function["name"] . "' which is not defined in the class. Entry gets ignored.", "mismatch");
							unset($this->classes[$classname][$eltype][$elname]["see"]["function"][$k]);
						}

				}
				
			}
			
		}	
		
	} // end func checkSeeElement
	
	/**
	* Builds an array with all elements of a class and saves it to $this->elementlist.
	* 
	* @param	string 	Name of the class to scan.
	*/
	function buildElementlist($classname) {
		
		$elements = array();
		$fields = array("functions", "variables", "consts", "uses");
		
		reset($fields);
		while (list($k, $field) = each($fields)) 
			if (isset($this->classes[$classname][$field])) {
				
				reset($this->classes[$classname][$field]);
				while (list($element, ) = each($this->classes[$classname][$field])) 
					$elements[$field][$element] = true;
					
			}
		
		$this->elementlist = $elements;
		
	} // end func buildElementlist

} // end class PhpdocClassAnalyser
?>