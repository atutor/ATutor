<?php
/**
* Analyses parsing data.
*
* Analyse means:
*   - update brother/sister
*   - update access/return
*   - inherit elements
*   - inherit information
*
* @version $Id: PhpdocAnalyser.php,v 1.5 2000/12/03 22:37:36 uw Exp $
*/
class PhpdocAnalyser extends PhpdocObject {

	/**
	* Flag indicating that getModule/getClass was called.
	*
	* @var	boolean
	*/
	var $flag_get = false;
	
	/**
	* List of all elements of a certain class/module.
	*
	* The array is used to look up see references
	* 
	* @var	array		Format: elementlist[ eltype ][ elname ] = true
	* @see					buildElementlist()
	*/
	var $elementlist = array();
	
		/**
	* Adds a suffix to the number like 1st, 2nd and 3th
	*
	* @param integer $nr number to format
	* @return string
	* @author Thomas Weinert <subjective@subjective.de>
	*/
	function addNumberSuffix($nr) {
	
		$last_nr = substr($nr, -1, 1);

		switch ($last_nr) {
			case 1: 
				return ($nr."st"); 
				break;

			case 2:
				return ($nr."nd"); 
				break;

			default: 
				return ($nr."th");
		}

	} // end func addNumberSuffix

	/**
	* Starts the analysing of the raw parsing data.
	*
	* @access			public
	* @abstract
	*/
	function analyse() {
		;
	} // end func analyse

	/**
	* Handles brother and sister.
	*
	* @abstract
	* @see			updateBrotherSisterElements()
	*/
	function updateBrothersSisters() {
		;
	} // end func updateBrothersSisters

	/**
	* Updates certain elements that use brother and sister.
	*
	* @return	boolean	$ok
	*/
	function updateBrotherSisterElements() {
		return false;
	} // end func updateBrotherSisterElements
	
	/**
	* Copies fields from a brother or sister to the current element.
	* 
	* @param	array	Data of the target element that has a brother/sister tag
	* @param	array	Data of the element that is referenced by brother/sister
	*/
	function copyBrotherSisterFields($target, $from) {
		
		reset($from);
		while (list($k, $v) = each($from)) 
			if (!isset($target[$k]) || "" == $target[$k]) 
				$target[$k] = $v;
				
		return $target;
	} // end func copyBrotherSisterFields

	/**
	* Updates the access and return tag values.
	*
	* @see			updateAccessReturnElements(), updateAccessElements()
	* @abstract
	*/
	function updateAccessReturn() {
		;
	} // end func updateAccessReturn

	/**
	* Updates access and return for certain elements.
	*
	* This function should only be used to update functions.
	* Functions that have the same name as the class (constructors)
	* get return void and access public. Functions without
	* access get access public and functions without return get return void.
	*
	* @return	boolean	$ok
	* @see		updateAccessReturn()
	* @abstract
	*/
	function updateAccessReturnElements() {
		;
	} // end func updateAccessReturnElements

	/**
	* Updates access tags.
	*
	* @see			updateAccessReturnElements()
	* @abstract
	*/
	function updateAccessElements() {
		;
	} // end func updateAccessElements

	/**
	* Compares the param tags with the function head found.
	*
	* @abstract
	*/
	function checkFunctionArgs() {
		;
	} // end func checkFunctionArgs

	/**
	* Looks for undocumented elements and adds a warning if neccessary.
	*
	* @abstract
	*/
	function findUndocumented() {
		;
	} // end func findUndocumented
	
	/**
	* Checks all see references in the given classes/modulegroup.
	* 
	* @abstract
	*/
	function checkSee() {
		;				
	} // end func checkSee
	
	/**
	* Checks see references in the given elementlist.
	* 
	* @abstract
	*/
	function checkSeeElement() {
		;
	} // end func checkSeeElement
	
	/**
	* Build a list of all elemente (functions, variables,...) of a certain class/module
	* 
	* @abstract
	* @see			$elementlist
	*/
	function buildElementlist() {
		; 
	} // end func buildElementlist

	/**
	* Compares the argument list generated from the function head with the param tags found.
	*
	* PHPDoc is able to recognize these documentation mistakes:
	* - too few or too many param tags
	* - name does not match or is missing
	* - type does not match or is missing
	* - trouble with inherited elements
	*
	* @param	array		Function arguments found by the parser
	* @param	array 	Paramarray
	* @param	string	Functionname
	* @param	string	Filename
	* @param	boolean	Param tags inherited?
	* @return	array		$params	Param array
	*/
	function checkArgDocs($args, $params, $elname, $elfile, $inherited = false) {
	
		// "param" contains the information from the @param tags.
		$num_args		= count($args);
		$num_params = count($params);

		// no args? return...
		if (0 == $num_args && 0 == $num_params)
			return array();

		// no args but @param used
		if (0 == $num_args && $num_params > 0) {
		
			if (!$inherited) {
			
				$msg = "Function head shows no parameters, remove all @param tags.";
				$this->warn->addDocWarning($elfile, "function", $elname, $msg, "mismatch");

			} else {

				if ("void" != $params[0]["type"]) {
	
					$msg = "The function inherited some parameter documentation from it's parentclass but PHPDoc could not find
									arguments in the function head. Add @param void to the doc comment to avoid confusion.";
					$this->warn->addDocWarning($elfile, "function", $elname, $msg, "mismatch");
				
				}
			
			}

			return array();
			
		}

		// compare the informations from the parser with the @param tags
		reset($args);
		while (list($k, $arg) = each($args)) {

			if (isset($params[$k])) {

				if ($arg["optional"])
					$params[$k]["default"] = $arg["default"];

				if (!$inherited) {

					if ("" != $arg["type"] && "" != $params[$k]["type"] && "mixed" != $params[$k]["type"] && strtolower($arg["type"]) != strtolower($params[$k]["type"])) {

						$type = $arg["type"];
						$msg = sprintf("%s parameter type '%s' does match the the documented type '%s', possible error consider an update to '@param %s %s %s' or '@param %s %s', the variable name is optional.",
															$this->addNumberSuffix($k + 1),
															$arg["name"],
															$params[$k]["type"],
															$type,
															$arg["name"],
															(isset($params[$k]["desc"])) ? $params[$k]["desc"] : "(description)",
															$type,
															(isset($params[$k]["desc"])) ? $params[$k]["desc"] : "(description)"
													);

						$this->warn->addDocWarning($elfile, "function", $elname, $msg, "mismatch");

					} else if ("" != $params[$k]["type"]) {

						$type = $params[$k]["type"];

					} else {

						$msg = sprintf('Type missing for the %s parameter, "mixed" assumed.', $this->addNumberSuffix($k));
						$this->warn->addDocWarning($elfile, "function", $elname, $msg, "missing");
						$type = "mixed";

					}

					$params[$k]["type"] = $type;

				} else {

					if ("" != $params[$k]["type"] && strtolower($arg["type"]) != strtolower($params[$k]["type"])) {

						$type = (""!=$args["type"]) ? $arg["type"] : $params[$k]["type"];
						$msg = sprintf("Possible documentation error due to inherited information.
														The type of the %s parameter '%s' does not match the documented type '%s'.
														Override the inherited documentation if neccessary.",
															$this->addNumberSuffix($k),
															$arg["type"],
															$params[$k]["type"]
													);
						$this->warn->addDocWarning($elfile, "function", $elname, $msg, "mismatch");
				
					} else if ("" != $params[$k]["type"]) {
		
						$type = $params[$k]["type"];
			
					} else {
			
						$type = "mixed";
						$msg = sprintf('Type missing for the %d parameter, "mixed" assumed. Override the inherited documentation if neccessary.', $k);
						$this->warn->addDocWarning($elfile, "function", $elname, $msg, "mismatch");

					}

					$params[$k]["type"] = $type;
				
				}

				if ("" != $params[$k]["name"] && $arg["name"] != $params[$k]["name"]) {

					$msg = sprintf("%s parameter '%s' does not match the documented name '%s', update the tag to '@param %s %s %s' or '@param %s %s', the variable name is optional.",
												$this->addNumberSuffix($k+1),
												$arg["name"],
												$params[$k]["name"],
												$type,
												$arg["name"],
												(isset($params[$k]["desc"])) ? $params[$k]["desc"] : "(description)",
												$type,
												(isset($params[$k]["desc"])) ? $params[$k]["desc"] : "(description)"
											);

					$this->warn->addDocWarning($elfile, "function", $elname, $msg, "mismatch");
					$params[$k]["name"] = $arg["name"];

				} else if ("" == $params[$k]["name"]) {

					$params[$k]["name"] = $arg["name"];

				}

			} else {

				$msg = sprintf("%s parameter '%s' is not documented add '@param %s [description]' to the end of the @param[eter] list.",
												$this->addNumberSuffix($k+1),
												$arg["name"],
												("" == $arg["type"]) ? "(object objectname|type)" : $arg["type"]
											);

				$params[$k]["name"]		= $arg["name"];
				$params[$k]["undoc"]	= true;

				if ("" != $arg["type"])
					$params[$k]["type"] = $arg["type"];

				$this->warn->addDocWarning($elfile, "function", $elname, $msg, "missing");
			}

		}

		// more @params specified than variables where found in the function head, delete them
		if ($num_params > $num_args) {

			$msg = "The parser found '$num_args' parameter but '$num_params' @param[eter] tags. You should update the @param[eter] list.";
			$this->warn->addDocWarning($elfile, "function", $elname, $msg, "mismatch");
			for ($i = $k + 1;  $i < $num_params; ++$i)
				unset($params[$i]);

		}

		return $params;
	} // end func checkArgDocs

} // end func PhpdocAnalyser
?>
