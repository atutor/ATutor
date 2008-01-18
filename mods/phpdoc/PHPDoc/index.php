<?php
define('AT_INCLUDE_PATH', '../../../include/');
require (AT_INCLUDE_PATH.'vitals.inc.php');
//$_custom_css = $_base_path . 'mods/hello_world/module.css'; // use a custom stylesheet
require (AT_INCLUDE_PATH.'header.inc.php');
?>

					<h1>Welcome to PHPDoc!</h1>
				
					<p> By default PHPDoc scans the ATutor include directory, where 99% of the API exists. If you wish to change that, edit the path in the source code of this file, redefining the value of "setSourceDirectory", then reload this page.
					</p>
				<hr>
				<pre>
				<?php
				$start = time();
			
				// WARNING: long runtimes! Make modifications 
				// to the php[3].ini if neccessary. A P3-500 
				// needs slightly more than 30 seconds to 
				// document phpdoc itself.
						
				// Directory with include files
				define("PHPDOC_INCLUDE_DIR", AT_INCLUDE_PATH."../mods/phpdoc/PHPDoc/");
				// Important: set this to the Linebreak sign of your system!
				define("PHPDOC_LINEBREAK", "\r\n");
		
				// main PHPDoc Include File
				include(AT_INCLUDE_PATH."../mods/phpdoc/PHPDoc/prepend.php");		
		
				$doc = new Phpdoc;
				
				// Sets the name of your application.
				// The name of the application gets used in many default templates.
				$doc->setApplication("ATutor LMS API");
				
				// directory where your source files reside:
				$doc->setSourceDirectory(AT_INCLUDE_PATH);
				
				// save the generated docs here:
				$doc->setTarget(AT_INCLUDE_PATH."../mods/phpdoc/PHPDoc/apidoc/keep/");
				
				// use these templates:
				$doc->setTemplateDirectory(AT_INCLUDE_PATH."../mods/phpdoc/PHPDoc/renderer/html/templates/");
				
				// source files have one of these suffixes:
				$doc->setSourceFileSuffix( array ("php", "inc") );
		
				// parse and generate the xml files
				$doc->parse();
				
				// turn xml in to html using templates
				$doc->render();
				
				printf("%d seconds needed\n\n.", time() - $start);
				?>
				</pre>	
				<hr>
					<h2>Finished!</h2>
					<p>The generated XML and HTML files can be found in
					the directory specified with setTarget() in the source code of this file. By default this location is "installationdir/apidoc/".
					Within this directory is another directory named "keep/". It contains a stylesheet file and 
					a frameset you can use to browse the HTML files. </p>

					<p><a href="mods/phpdoc/PHPDoc/apidoc/keep/index2.html" target="atutorapi">View API Documentation</a> (opens a new window)</p>

					<p>For assistance with PHPDoc, please contact the developer, at:
					<p>
					<a href="mailto:ulf.wendel@phpdoc.de">ulf.wendel@phpdoc.de</a>

<?php require (AT_INCLUDE_PATH.'footer.inc.php'); ?>
