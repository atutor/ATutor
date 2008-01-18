<?php
/**
* Analyses a modulegroup.
*
* @version $Id: PhpdocModuleAnalyser.php,v 1.4 2000/12/03 22:37:36 uw Exp $
*/
class PhpdocModuleAnalyser extends PhpdocAnalyser {

	/**
	* Module data
	* @var	array
	*/
	var $modulegroup		= array();

	/**
	* List of all modules in the modulegroup
	* @var	array
	*/ 
	var $modulelist = array();
	
	/**
	* Puuuh - findUndocumented() needs this.
	* @var	array
	* @see	findUndocumented()
	*/														
	var $undocumentedFields = array(
															"functions"	=> "function",
															"uses"			=> "included file",
															"consts"		=> "constant"
													);

	/**
	* Sets the data of the modulegroup to analyse.
	* 
	* @param	array	Raw modulegroup data from the parser.
	* @access	public
	*/
	function setModulegroup($modulegroup) {
	
		$this->modulegroup = $modulegroup;	
		
	} // end func setModulegroup
	
	function analyse() {

		$this->flag_get = false;
		
		$this->buildModulelist();
		
		$this->updateAccessReturn();
		$this->updateBrothersSisters();
		$this->checkSee();
				
		$this->checkFunctionArgs();
		$this->findUndocumented();
		
	} // end func analyse
	
	/**
	* Returns a module from the modulegroup or false if there are no more modules.
	*
	* @return	mixed		False if there no more modules in the modulegroup otherwise
	* 								an array with the data of a module.
	* @access	public
	*/
	function getModule() {
	
		if (!$this->flag_get) {
			reset($this->modulelist);
			$this->flag_get = true;
		}
			
		if (list($modulename, $group) = each($this->modulelist)) {
			
			$module = $this->modulegroup[$group][$modulename];
			unset($this->modulegroup[$group][$modulename]);			
			return $module;
			
		} else {
		
			return false;
			
		}
		
	} // end func getModule
	
	function findUndocumented() {

		reset($this->modulegroup);
		while (list($group, $modules) = each($this->modulegroup)) {
			
			reset($modules);
			while (list($name, $module) = each($modules)) {
				
				reset($this->undocumentedFields);
				while (list($index, $eltype) = each($this->undocumentedFields)) {
					if (!isset($module[$index]))
						continue;
						
					$file = $module["filename"];
					
					reset($module[$index]);
					while (list($elname, $data) = each($module[$index]))
						if (isset($data["undoc"]) && $data["undoc"])
							$this->warn->addDocWarning($file, $eltype, $elname, "Undocumented element.", "missing");
				}
				
			}
			
		}		

	} // end func findUndocumented
	
	function checkFunctionArgs() {
	
		reset($this->modulegroup);
		while (list($group, $modules) = each($this->modulegroup)) {

			reset($modules);
			while (list($name, $module) = each($modules)) {
				if (!isset($module["functions"]))
					continue;

				$file = $module["filename"];
								
				reset($module["functions"]);
				while (list($fname, $function) = each($module["functions"])) {
					$this->modulegroup[$group][$name]["functions"][$fname]["params"] = $this->checkArgDocs($function["args"], $function["params"], $fname, $file, false);
					unset($this->modulegroup[$group][$name]["functions"][$fname]["args"]);
				}
				
			}
			
		}

	} // end func checkFunctionArgs
	
	/**
	* Builds an internal list of all modules in the modulegroup.
	* @see	$modulelist, $modulegroup
	*/
	function buildModulelist() {
	
		$this->modulelist = array();
		
		reset($this->modulegroup);
		while (list($group, $modules) = each($this->modulegroup)) {
		
			reset($modules);
			while (list($modulename, $data) = each($modules))
				$this->modulelist[$modulename] = $group;
				
		}
		
	}

		
	function updateBrothersSisters() {
	
		reset($this->modulelist);
		while (list($modulename, $group) = each($this->modulelist)) {
			$this->updateBrotherSisterElements($group, $modulename, "functions");
			$this->updateBrotherSisterElements($group, $modulename, "variables");
		}	
		
	} // end func updateBrothersSisters
	
	/**
	* @param	string	Modulegroupname
	* @param	string	Modulename
	* @param	string	Elementtype: functions, variables.
	* @return	boolean	
	*/
	function updateBrotherSisterElements($group, $modulename, $type) {
		
		if (!isset($this->modulegroup[$group][$modulename][$type])) 
			return false;
			
		reset($this->modulegroup[$group][$modulename][$type]);
		while (list($elementname, $data) = each($this->modulegroup[$group][$modulename][$type])) {
			
			if (isset($data["brother"])) {

				$name = ("functions" == $type) ? substr($data["brother"], 0, -2) : substr($data["brother"], 1);
				$name = strtolower($name);

				if (!isset($this->modulegroup[$group][$modulename][$type][$name])) {
				
					$this->warn->addDocWarning($this->modulegroup[$group][$modulename]["filename"], $type, $elementname, "Brother '$name' is unknown. Tags gets ignored.", "mismatch");
					unset($this->modulegroup[$group][$modulename][$type][$elementname]["brother"]);
					
				} else {
				
					$this->modulegroup[$group][$modulename][$type][$elementname]["brother"] = $name;
					$this->modulegroup[$group][$modulename][$type][$elementname] = $this->copyBrotherSisterFields($this->modulegroup[$group][$modulename][$type][$elementname], $this->modulegroup[$group][$modulename][$type][$name]);
					
				}

			}
			
		}
		
	} // end func updateBrotherSistersElements
	
	function updateAccessReturn() {
		
		reset($this->modulelist);
		while (list($modulename, $group) = each($this->modulelist)) {
		
			if (!isset($this->modulegroup[$group][$modulename]["access"]))
				$this->modulegroup[$group][$modulename]["access"] = "private";
				
			$this->updateAccessReturnElements($group, $modulename, "functions");
			$this->updateAccessReturnElements($group, $modulename, "variables");
			$this->updateAccessElements($group, $modulename, "consts");		
			
		}
				
	} // end func updateAccessReturn
	
	/**
	* @param	string	Modulegroup
	* @param	string	Modulename
	* @param	string	Elementtype: functions, variables, consts.
	* @return	boolean
	*/
	function updateAccessReturnElements($group, $modulename, $type) {
		
		if (!isset($this->modulegroup[$group][$modulename][$type]))
			return false;

		reset($this->modulegroup[$group][$modulename][$type]);
		while (list($elementname, $data) = each($this->modulegroup[$group][$modulename][$type])) {
		
			if (!isset($data["access"])) 
				$this->modulegroup[$group][$modulename][$type][$elementname]["access"] = "private";
				
			if (!isset($data["return"]))
				$this->modulegroup[$group][$modulename][$type][$elementname]["return"] = "void";
				
		}
				
	} // end func updateAccessReturnElements
	
	/**
	* @param	string	Modulegroup
	* @param	string	Modulename
	* @param	string	Elementtype: functions, variables, consts.
	* @return	boolean
	*/
	function updateAccessElements($group, $modulename, $type) {
		
		if (!isset($this->modulegroup[$group][$modulename][$type]))
			return false;
			
		reset($this->modulegroup[$group][$modulename][$type]);
		while (list($elementname, $data) = each($this->modulegroup[$group][$modulename][$type])) {
			
			if (!isset($data["access"])) 
				$this->modulegroup[$group][$modulename][$type][$elementname]["access"] = "private";
		
		}
		
	} // end func updateAccessElements
	
	function checkSee() {

		reset($this->modulegroup);
		while (list($group, $modules) = each($this->modulegroup)) {

			while (list($modulename, $module) = each($modules)) {
		
				$this->buildElementlist($group, $modulename);
				
				if (isset($module["functions"])) 
					$this->checkSeeElements($module["functions"], $group, $modulename, "functions");
					
				if (isset($module["variables"]))
					$this->checkSeeElements($module["variables"], $group, $modulename, "variables");
				
				if (isset($module["consts"])) 
					$this->checkSeeElements($module["consts"], $group, $modulename, "consts");
					
				if (isset($module["uses"]))
					$this->checkSeeElements($module["uses"], $group, $modulename, "uses");
				
			}	
			
		}
		
	} // end func checkSee

	/**
	* Checks see references in the given element array (functions, variables...)
	*
	* References to variables and functions within the same module get checked.
	* It the references element does not exist, the reference gets deleted and 
	* a doc warning gets generated.
	* 
	* @param	array		List of functions, variables,...
	* @param	string 	Name of the modulegroup that contains the given elements.
	* @param	string	Name of the module that contains the given elements.
	* @param	string	Elementtype: functions, variables, consts, uses.
	*/	
	function checkSeeElements($elements, $modulegroup, $modulename, $eltype) {

		reset($elements);
		while (list($elname, $element) = each($elements)) {
		
			if (isset($element["see"])) {
				
				if (isset($element["see"]["var"])) {
					
					reset($element["see"]["var"]);
					while (list($k, $variable) = each($element["see"]["var"])) 
						if (!isset($this->elementlist["variables"][strtolower($variable["name"])])) {
							$this->warn->addDocWarning($this->modulegroup[$modulegroup][$modulename]["filename"], "variables", $elname, "@see referrs to the variable '" . $variable["name"] . "' which is not defined in the class. Entry gets ignored.", "mismatch");
							unset($this->modulegroup[$modulegroup][$modulename][$eltype][$elname]["see"]["var"][$k]);
						}

				}
				
				if (isset($element["see"]["function"])) {
					
					reset($element["see"]["function"]);
					while (list($k, $function) = each($element["see"]["function"]))
						if (!isset($this->elementlist["functions"][strtolower(substr($function["name"], 0, -2))])) {
							$this->warn->addDocWarning($this->modulegroup[$modulename]["filename"], "functions", $elname, "@see referrs to the function '" . $function["name"] . "' which is not defined in the class. Entry gets ignored.", "mismatch");
							unset($this->modulegroup[$modulegroup][$modulename][$eltype][$elname]["see"]["function"][$k]);
						}

				}
				
			}
			
		}	
		
	} // end func checkSeeElement
	
	/**
	* Builds an array with all elements of a class and saves it to $this->elementlist.
	* 
	* @param	string 	Name of the modulegroup that contains the module.
	* @param	string 	Name of the module to scan.
	*/
	function buildElementlist($modulegroup, $modulename) {
		
		$elements = array();
		$fields = array("functions", "variables", "consts", "uses");
		
		reset($fields);
		while (list($k, $field) = each($fields)) 
			if (isset($this->modulegroup[$modulegroup][$modulename][$field])) {
				
				reset($this->modulegroup[$modulegroup][$modulename][$field]);
				while (list($element, ) = each($this->modulegroup[$modulegroup][$modulename][$field])) 
					$elements[$field][$element] = true;
					
			}
		
		$this->elementlist = $elements;
		
	} // end func buildElementlist
	
} // end class PhpdocModuleAnalyser
?>