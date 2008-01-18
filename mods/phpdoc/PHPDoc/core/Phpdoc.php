<?php
/**
* Coordinates several Phpdoc Object to parse and render source files.
* 
* @access		public
* @version	$Id: Phpdoc.php,v 1.4 2000/12/03 20:30:42 uw Exp $
*/
class Phpdoc extends PhpdocSetupHandler {

	/**
	* Result from the indexer
	*
	* @var	array
	* @see	render()
	*/
	var $indexer_result = array();

	/**
	* Print status messages
	*/
	var $flag_output = true;

	/**
	* Calls the command line handler if necessary.
	*
	* @global array $argc, string $PHP_SELF
	*/
	function Phpdoc() {
		global $argc, $PHP_SELF;

		$this->target = $PHP_SELF."apidoc/";

		if ($argc>1) 
			$this->handleArgv();

	} // end constructor

	/**
	* Starts the parser. 
	*
	* @return	bool		$ok
	* @throws	PhpdocError
	* @access	public
	*/
	function parse() {

		$this->warn = new PhpdocWarning;

		$errors = $this->checkStatus();
		if (0 != count($errors)) {

			reset($errors);
			while (list($k, $error)=each($errors))
				$this->err[] = new PhpdocError($error["msg"]."Errno = ".$error["errno"], 9, __FILE__, __LINE__);

			return false;
		}

		$this->outl("Parser starts...");

		// create some objects
		$fileHandler 		= new PhpdocFileHandler;
		$parser 				= new PhpdocParser(true);
		$classAnalyser	= new PhpdocClassAnalyser;
		$moduleAnalyser = new PhpdocModuleAnalyser;

		$indexer	= new PhpdocIndexer;				

		$classExporter	= new PhpdocXMLClassExporter();
		$classExporter->setPath($this->target);

		$moduleExporter = new PhpdocXMLModuleExporter();
		$moduleExporter->setPath($this->target);

		$indexExporter = new PhpdocXMLIndexExporter();
		$indexExporter->setPath($this->target);

		$warningExporter = new PhpdocXMLWarningExporter();
		$warningExporter->setPath($this->target);

		// This will change one fine day! 
		$parser->warn 				= $this->warn;
		$classAnalyser->warn 	= $this->warn;
		$moduleAnalyser->warn = $this->warn;
		$classExporter->warn 	= $this->warn;
		$moduleExporter->warn = $this->warn;
		$indexer->warn 				= $this->warn; 

		$sourcefiles = $fileHandler->getFilesInDirectory($this->sourceDirectory, $this->sourceFileSuffix);
		$parser->setPhpSourcecodeFiles($fileHandler->get($sourcefiles));

		$this->outl("... preparse to find modulegroups and classtrees.");
		$parser->preparse();

		$this->outl("... parsing classes.");
		while ($classtree = $parser->getClassTree()) {

			$classAnalyser->setClasses( $classtree, $parser->current_baseclass );
			$classAnalyser->analyse();

			while ($class = $classAnalyser->getClass()) {
				$indexer->addClass($class);
				$classExporter->export($class);
			}

			if (floor(phpversion()) > 3) {

				$indexExporter->exportClasstree($indexer->getClasstree(), $parser->current_baseclass);

			} else {

				$classtree = $indexer->getClasstree();
				$base = $parser->current_baseclass;
				$indexExporter->exportClasstree($classtree, $base);

			}

		}

		$this->outl("... parsing modules.");
		while ($modulegroup = $parser->getModulegroup()) {	

			$moduleAnalyser->setModulegroup( $modulegroup );
			$moduleAnalyser->analyse();

			while ($module = $moduleAnalyser->getModule()) {
				$indexer->addModule($module);
				$moduleExporter->export($module);
			}

			if (floor(phpversion()) > 3) {

				$indexExporter->exportModulegroup($indexer->getModulegroup());

			} else {

				$modulegroup = $indexer->getModulegroup();
				$indexExporter->exportModulegroup($modulegroup);

			}

		}

		$this->outl("... writing packagelist.");
		if (floor(phpversion()) > 3) {

			$indexExporter->exportPackagelist($indexer->getPackages());
			$indexExporter->exportElementlist($indexer->getElementlist());

		} else {

			$packages = $indexer->getPackages();
			$indexExporter->exportPackagelist($packages);
			$elements = $indexer->getElementlist();
			$indexExporter->exportElementlist($elements);

		}

		$warningExporter->export($parser->warn->getWarnings(), "parser");
		$warningExporter->export($moduleAnalyser->warn->getWarnings(), "moduleanalyser");
		$warningExporter->export($classAnalyser->warn->getWarnings(), "classanalyser");

		$this->outl("Parser finished.");
		return true;
	} // end func parse

	/**
	* Renders the PHPDoc XML files as HTML files 
	*
	* @param	string	Targetformat, currently only "html" is available.
	* @param	string 	Target directory for the html files
	* @param	string	Directory with the html templates
	* @return	bool		$ok
	* @throws	PhpdocError
	* @access	public
	*/
	function render($type = "html", $target = "", $template = "") {

		$this->outl("Starting to render...");
		$target = ("" == $target) ? $this->target : $this->getCheckedDirname($target);
		$template =	("" == $template) ? $this->templateRoot : $this->getCheckedDirname($template);				

		switch(strtolower($type)) {

			case "html":
			default:
				$renderer = new PhpdocHTMLRendererManager($target, $template, $this->application, $this->targetFileSuffix);
				break;
		}

		$fileHandler 		= new PhpdocFileHandler;
		$files = $fileHandler->getFilesInDirectory($target, "xml");
		$len = strlen($target);

		$tpl = new IntegratedTemplate($this->templateRoot);
		$tpl->loadTemplateFile("xmlfiles.html");
		$tpl->setCurrentBlock("file_loop");

		// Do not change the file prefixes!
		reset($files);
		while (list($k, $file) = each($files)) {

			$tpl->setVariable("FILE", substr($file, $len));
			$tpl->parseCurrentBlock();

			if ("class_" == substr($file, $len, 6)) {

				$renderer->render(substr($file, $len), "class");

			} else if ("module_" == substr($file, $len, 7)) {

				$renderer->render(substr($file, $len), "module");

			} else if ("classtree_" == substr($file, $len, 10)) {

				$renderer->render(substr($file, $len), "classtree");

			}	else if ("modulegroup_" ==  substr($file, $len, 12)) {

				$renderer->render(substr($file, $len), "modulegroup");

			} else if ("warnings_" == substr($file, $len, 9)) {

				$renderer->render(substr($file, $len), "warning");

			}

		}

		$renderer->finish();	
		$fileHandler->createFile($target."phpdoc_xmlfiles".$this->targetFileSuffix, $tpl->get());

		$this->outl($this->finishInstructions);
		return true;
	} // end func	render

} // end class Phpdoc
?>