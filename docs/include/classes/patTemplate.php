<?PHP

/**
* Variable prefix
* @access	public
* @const patTEMPLATE_TAG_START
*/
define( "patTEMPLATE_TAG_START", "{" );

/**
* Variable sufix
* @const patTEMPLATE_TAG_END
* @access	public
*/
define( "patTEMPLATE_TAG_END",   "}" );

/**
* Template type Standard
* @const patTEMPLATE_TYPE_STANDARD
*/
define( "patTEMPLATE_TYPE_STANDARD",  "STANDARD" );
/**
* Template type OddEven
* @const patTEMPLATE_TYPE_ODDEVEN
*/
define( "patTEMPLATE_TYPE_ODDEVEN",   "ODDEVEN" );
/**
* Template type Condition
* @const patTEMPLATE_TYPE_CONDITION
*/
define( "patTEMPLATE_TYPE_CONDITION", "CONDITION" );
/**
* Template type SimpleCondition
* @const patTEMPLATE_TYPE_SIMPLECONDITION
*/
define( "patTEMPLATE_TYPE_SIMPLECONDITION", "SIMPLECONDITION" );

/**
* Easy-to-use but powerful template engine
*
* Features include: several templates in one file, automatic repetitions, global variables,
* alternating lists, conditions, and much more
*
* @package		patTemplate
* @access		public	
* @author		Stephan Schmidt <schst@php-tools.de>
* @version		2.4 ($Id: patTemplate.php,v 1.1 2004/04/05 14:58:51 joel Exp $)
*/
class	patTemplate
{

/**
* Constructor
*
* Create new patTemplate object
* You can choose between two outputs you want tp generate: html (default) or tex (LaTex).
* When "tex" is used the patTemplate markings used for variables are changed as LaTex makes use of the default patTemplate markings.
* You can also change the markings later by calling setTags();
*
* @access	public
* @param	string	$type		type of output you want to generate.
*/
	function	patTemplate( $type = "html" )
	{
		//	Directory, where Templates are stored
		$this->basedir			=	"";

		//	counter for template iterations
		$this->iteration		=	array();
		
		//	Filenames of the templates
		$this->filenames		=	array();

		//	HTML/Text of unparsed templates
		$this->plain_templates	=	array();

		//	HTML/Text of parsed templates
		$this->parsed_templates	=	array();

		//	Amount and names of all templates
		$this->cnt_templates	=	0;
		$this->templates		=	array();

		//	These vars will be set for all added Templates
		$this->subtemplates		=	array();

		$this->variables		=	array();
		$this->globals			=	array();

		$this->attributes		=	array();
		
		//	Does one of the templates contain other templates
		$this->uses_dependencies=	false;

		// Set template tags
		$this->setType( $type );
	}

/**
 * Set template type
 * 
 * select a predefined template type
 * 
 * @param	string	$type	predefined template type, like "html" or "tex"
 * @access	public
 */
function	setType( $type = "" )
	{
	switch ( $type )
		{
		case "tex":
			$this->setTags( "<{", "}>" );
			break; 

		case "html":
		default:
			$this->setTags( patTEMPLATE_TAG_START, patTEMPLATE_TAG_END );
			break;
		}
	}
	
/**
 * Set template tags
 *
 * Sets the start and end tags of template variables
 *
 * @param	string	$start	start tag
 * @param	string	$end	end tag
 * @access	public
 */
	function	setTags( $start = patTEMPLATE_TAG_START, $end = patTEMPLATE_TAG_END )
	{
		$this->tag_start = $start;
		$this->tag_end = $end;

		$this->regex_get_all_vars = "/". $start ."([^a-z{}]+)". $end ."/" ;
	}
	
/**
* Set template directory
*
* Sets the directory where the template are stored.
* By default the engine looks in the directory where the original file is stored.
*
* @param	string	$basedir	directory of the templates
* @access	public
*/
	function	setBasedir( $basedir )
	{
		$this->basedir			=	$basedir;
	}
	
/**
* Check if a template exists
*
* @param	string	$name	name of the template
* @return	bool
* @access	public
*/
	function	exists( $name )
	{
		$name	=	strtoupper( $name );
		
		for( $i=0; $i<$this->cnt_templates; $i++ )
			if( $this->templates[$i] == $name )
				return	true;

		return	false;
	}

/**
* Add a template
*
* Adds a plain text/html to the template engine.
* The file has to be in the directory that has been set using setBaseDir
*
* @param	string	$name	name of the template
* @param	string	$filename	filename of the sourcetemplate
* @access	public
* @deprecated 2.4 2001/11/05
* @see		setBaseDir(), addTemplates()
*/
	function	addTemplate( $name, $filename )
	{
		$this->createTemplate( $name, array( 	"type"		=> "file",
												"filename"	=> $filename ) );
		//	Store the filename
		$this->filenames[$name]					=	$filename;
	}

/**
* Adds several templates
*
* Adds several templates to the template engine using an associative array.
* Names of the templates are stored in the keys, filenames are the values.
* The templates have to be in the directory set by setBaseDir().
*
* @param	array	$templates	associative Array with name/filename pairs
* @access	public
* @deprecated 2.4 2001/11/05
* @see		setBaseDir(), addTemplate()
*/
	function	addTemplates( $templates )
	{
		while	( list( $name, $file ) = each( $templates ) )
			$this->addTemplate( $name, $file );
	}


/**
* creates a new template
*
* creates all needed variables
*
* @param	string	$name	name of the template
* @param	array	$source	data regarding the source of the template
* @access	private
*/
	function	createTemplate( $name, $source )
	{
		$name	=	strtoupper( $name );

		//	Store the name of the template in index table
		$this->templates[$this->cnt_templates]	=	$name;
		$this->cnt_templates++;

		//	Store the source
		$this->source[$name]					=	$source;

		//	Init vars for the new Templates

		//	Store all attributes in Array
		$this->attributes[$name]				=	array(	"loop"			=>	1,
															"visibility"	=>	"visible",
															"unusedvars"	=>	"strip",
															"type"			=>	"STANDARD" );
		$this->iteration[$name]					=	0;
															
		//	No vars are set for this template
		$this->variables[$name]					=	array();
		//	No subtemplates have been specified
		$this->cnt_subtemplates[$name]			=	0;

		$this->varsConverted[$name]				=	false;
	}

/**
* Sets the type of the Template
*
* Template types can be STANDARD, CONDITION or ODDEVEN
* The type of the template can also be set using setAttribute()
*
* @param	string	$template	name of the template
* @param	string	$type	type of the template
* @access	private
* @see		setAttribute()
*/
	function	setTemplateType( $template, $type )
	{
		$template	=	strtoupper( $template );

		$this->setAttribute( $template, "type", $type );
	}

/**
* Sets the conditionvar of a condtion Template
*
* The type of the template has to be condition
*
* @param	string	$template	name of the template
* @param	string	$conditionvar	name of the conditionvariable
* @access	private
* @see		setTemplateType()
*/
	function	setConditionVar( $template, $conditionvar )
	{
		$template						=	strtoupper( $template );
		$conditionvar					=	strtoupper( $conditionvar );

		$this->conditionvars[$template]	=	$conditionvar;
	}
/**
* Sets an attribute of a template
*
* supported attributes: visibilty, loop, parse, unusedvars
*
* @param	string	$template	name of the template
* @param	string	$attribute	name of the attribute
* @param	mixed	$value	value of the attribute
* @access	public
* @see		setAttributes(),getAttribute(), clearAttribute()
*/
	function	setAttribute( $template, $attribute, $value )
	{
		$template								=	strtoupper( $template );
		$attribute								=	strtolower( $attribute );

		$this->attributes[$template][$attribute]=	$value;
	}

/**
* Sets several attribute of a template
*
* $attributes has to be a assotiative arrays containing attribute/value pairs
* supported attributes: visibilty, loop, parse, unusedvars
*
* @param	string	$template	name of the template
* @param	array	$attributes	attribute/value pairs
* @access	public
* @see		setAttribute(), getAttribute(), clearAttribute()
*/
	function	setAttributes( $template, $attributes )
	{
		if( !is_array( $attributes ) )
			return	false;

		$template								=	strtoupper( $template );

		while( list( $attribute, $value ) = each( $attributes ) )
		{
			$attribute								=	strtolower( $attribute );
			$this->attributes[$template][$attribute]=	$value;
		}
	}

/**
* Gets an attribute of a template
*
* supported attributes: visibilty, loop, parse, unusedvars
*
* @param	string	$template	name of the template
* @param	string	$attribute	name of the attribute
* @return	mixed	value of the attribute
* @access	public
* @see		setAttribute(), setAttributes(), clearAttribute()
*/

	function	getAttribute( $template, $attribute )
	{
		$template								=	strtoupper( $template );
		$attribute								=	strtolower( $attribute );

		return	$this->attributes[$template][$attribute];
	}

/**
* Clears an attribute of a template
*
* supported attributes: visibilty, loop, parse, unusedvars
*
* @param	string	$template	name of the template
* @param	string	$attribute	name of the attribute
* @access	public
* @see		setAttribute(), setAttributes(), getAttribute()
*/
	function	clearAttribute( $template, $attribute )
	{
		$template								=	strtoupper( $template );
		$attribute								=	strtolower( $attribute );

		unset( $this->attributes[$template][$attribute] );
	}

/**
* Adds a subtemplate for a condition or oddeven template
*
* template type has to be condition or oddeven
*
* @param	string	$template	name of the template
* @param	string	$condition	condition for this subtemplate
* @access	private
* @see		setTemplateType()
*/
	function	addSubTemplate( $template, $condition )
	{
		$template		=	strtoupper( $template );

		$this->subtemplates[$template][$condition]										=	"";
		$this->subtemplate_conditions[$template][$this->cnt_subtemplates[$template]]	=	$condition;
		
		$this->cnt_subtemplates[$template]++;
	}

/**
* Parses several templates from one patTemplate file
*
* Templates can be seperated using Tags
* The file has to be located in the directory that has been set using setBaseDir.
*
* @param	string	$file	filename
* @access	public
* @see		setBasedir()
*/
	function	readTemplatesFromFile( $file )
	{
		//	Tag depth
		$this->depth					=	-1;
		//	Names, extracted from the Tags
		$this->template_names			=	array();
		//	All HTML code, that is found between the tags
		$this->template_data			=	array();
		//	Attributes, extracted from tags
		$this->template_types			=	array();

		$this->last_opened				=	array();
		$this->last_keep				=	array();
		$this->whitespace				=	array();
		
		$this->createParser( $file );
	
		$open_tag	=	array_pop( $this->last_opened );
		if( $open_tag != NULL )
			die	( "Error in template '".$file."': &lt;/".$open_tag."&gt; still open at end of file."  );
	}

/**
*	parse a template file and call the appropriate handlers
*
*	@access	private
*	@param	string	$fname	filename of the template
*/
	function	createParser( $fname )
	{
		//	Store filename of the first file that has to be opened
		//	If basedir is set, prepend basedir
		$pname					=	$this->basedir!="" ? $this->basedir."/".$fname : $fname;

		//	open file for reading
		$fp						=	fopen( $pname, "r" );

		//	couldn't open the file => exit
		if( !$fp )
			die( "Couldn't open file '".$fname."' for reading!" );

		//	Read line for line from the template
		
		//	current linenumber in file, used for error messages
		$lineno		=	1;

		//	read until end of file
		while( !feof( $fp ) )
		{
			//	Read one line
			$line	=	fgets( $fp, 4096 );

			//	check, wether leading and trailing whitepaces should be stripped
			switch( $this->whitespace[( count( $this->whitespace )-1 )] )
			{
				case	"trim":
					$line	=	trim( $line );
					break;
				case	"ltrim":
					$line	=	ltrim( $line );
					break;
				case	"rtrim":
					$line	=	rtrim( $line );
					break;
			}
			
			//	========= [ OPEN TAG ] =========

			//	check for any <patTemplate:...> Tag by using RegExp
			if	( eregi( "<patTemplate:([[:alnum:]]+)[[:space:]]*(.*)>", $line, $regs ) )			
			{
				//	Get Tag name and attributes
				$tagname	=	strtolower( $regs[1] );
				$attributes	=	$this->parseAttributes( $regs[2] );

				if( $attributes[keep] > 0 )
				{
					//	create new attribute
					$newkeep	=	$attributes[keep] > 1 ? " keep=\"".($attributes[keep]-1)."\"" : "";

					//	replace old attribute with new attribute
					$newline	=	str_replace( " keep=\"".$attributes[keep]."\"", $newkeep, $line );

					//	use this line as data
					$this->dataHandler( $fname, $newline, $lineno );

					//	if the tag was not empty keep the closing tag, too
					if( substr( $regs[2], -1 ) != "/" )
						$this->last_keep[]				=	true;
				}
				else				
				{
					$this->last_keep[]					=	false;

					//	handle start Element
					$this->startElementHandler( $fname, $tagname, $attributes, $line, $lineno );
				
					if( substr( $regs[2], -1 ) == "/" )				
						$this->endElementHandler( $fname, $tagname, $line, $lineno );

					//	Store the name of the last opened tag				
					else
					{
						$this->last_opened[]	=	 $tagname;
					}
				}
			}

			//	========= [ CLOSING TAG ] =========

			//	Check if a closing <patTemplate:...> Tag has been found
			elseif	( eregi( "</patTemplate:([[:alnum:]]+)>", $line, $regs ) )
			{
				//	Yes => get the tagname
				$tagname	=	strtolower( $regs[1] );

				$keep		=	array_pop( $this->last_keep );
				if( !$keep )
				{
					$last_opened	=	array_pop( $this->last_opened );
					if( $last_opened == NULL )
						die	( "Error in template '".$fname."': no opening tag found for &lt;/".$tagname."&gt; in line ".$lineno );

					if( $tagname != $last_opened )
						die	( "Error in template '".$fname."': closing &lt;/".$tagname."&gt; does not match opened &lt;".$last_opened."&gt; in line ".$lineno );

					$this->endElementHandler( $fname, $tagname, $line, $lineno );
				}
				else
					$this->dataHandler( $fname, $line, $lineno );
			}

			//	========= [ CDATA SECTION ] =========
			
			//	No tag found => store the line
			else
				$this->dataHandler( $fname, $line, $lineno );

			//	goto next line
			$lineno++;
		}
	}
	
/**
*	handle a <patTemplate:...> start tag in template parser
*
*	@access	private
*	@param	string	$fname		name of the file where the tag was found (kind of parser id)
*	@param	string	$tagname	name of the start tag that was found
*	@param	array	$attributes	all attributes that were found
*	@param	string	$line		the complete line containing the tag
*	@param	integer	$lineno		lineno in the parse file (can be used for error messages
*/
	function	startElementHandler( $fname, $tagname, $attributes, $line, $lineno )
	{
		//	check for whitespace attribute
		if( $attributes[whitespace] )
			array_push( $this->whitespace, strtolower( $attributes[whitespace] ) );
		//	use whitepspace mode from last opened template
		else				
			array_push( $this->whitespace, $this->whitespace[( count( $this->whitespace )-1 )] );

		switch( $tagname )
		{
			//	Beginning of a template found
			case "tmpl":
				//	parse all attributes from a string into an associative array
				
				//	Check for name of template, which is a necessary attribute
				if( !$tmpl_name	=	strtoupper( $attributes[name] ) )
					die	( "Error in template '".$fname."': missing name for template in line ".$lineno );

				unset( $attributes[name] );

				//	Increment Tag Depth
				$this->depth++;

				//	Start with a blank template
				$this->template_data[$this->depth]			=	"";		

				//	and store the name
				$this->template_names[$this->depth]			=	$tmpl_name;
				
				//	Check, if attribute "type" was found
				if( $tmpl_type	=	strtoupper( $attributes[type] ) )
				{
					$this->template_types[$this->depth]		=	$tmpl_type;
					$attributes[type]						=	$tmpl_type;
				}
				//	No type found => this is a boring standard template
				else
				{
					$attributes[type]						=	"STANDARD";
					$this->template_types[$this->depth]		=	"STANDARD";
				}

				//	Check for src attribute => external file
				if( $attributes[src] )
				{
					//	Store the filename of the external file
					$filename						=	$attributes[src];

					//	Has the external file to be parsed
					if( $attributes[parse] == "on" )
						$this->createParser( $filename );

					//	No parsing, just take the whole content of the file
					else
					{
						//	Filename including full path
						$external		=	$this->basedir!="" ? $this->basedir."/".$filename : $filename;

						//	Open the file and read all the content
						if( !$tmp	=	@implode( "", @file( $external ) ) )
							die( "Couldn't open file '".$external."' for reading in template ".$fname." line ".$lineno );

						$this->template_data[$this->depth]	.=	$tmp;
					}

					//	Delete the src attribute, it hasn't to be stored
					unset( $attributes[src] );
				}
				//	No external file => the template is part of teh current file
				else
					$filename						=	"[part of ".$fname."]";
				
				//	add the template
				$this->addTemplate( $this->template_names[$this->depth], $filename );

				//	Set all remaining attributes
				$this->setAttributes( $this->template_names[$this->depth], $attributes );
				
				switch	( $this->template_types[$this->depth] )
				{
					//	Template type is "ODDEVEN", it contains two alternating subtemplates
					case "ODDEVEN":
						$this->setConditionVar( $this->template_names[$depth], "PAT_ROW_VAR mod 2" );
						break;	

					//	Template is a condition Tenplate => it needs a condition var	
					case "CONDITION":
						//	none found => there is an error
						if( !$conditionvar	=	$attributes[conditionvar] )
							die	( "Error in template '".$fname."': missing conditionvar for template in line ".$lineno );
							
						//	conditionvar was found => store it
						$this->setConditionVar( $this->template_names[$this->depth], $conditionvar );
						break;	

					//	Template is a simple condition Tenplate => it needs required vars
					case "SIMPLECONDITION":
						//	none found => there is an error
						if( $requiredvars = $attributes[requiredvars] )
							$this->setAttribute( $this->template_names[$this->depth], "requiredvars", explode( ",", $requiredvars ) );
						else
							die	( "Error in template '".$fname."': missing requiredvars attribute for simple condition template in line ".$lineno );
							
						break;	
						
				}
					
				//	if the template isn't the root( depth=0 ) template, a placeholder has
				//	to be put into the parent template
				if	( $this->depth > 0 )
				{
					//	Is there a placeholder attribute?
					if( $placeholder = strtoupper( $attributes[placeholder] ) )
					{
						//	placeholder="none" found => DO NOT PUT A PLACEHOLDER IN THE PARENT TEMPLATE!
						if( $placeholder != "NONE" )
							$this->template_data[($this->depth-1)]	.=	$this->tag_start. $placeholder .$this->tag_end;
					}
					//	No placeholder attribute found => standard placeholder
					else
					{
						$this->template_data[($this->depth-1)]	.=	$this->tag_start."TMPL:".$this->template_names[$this->depth].$this->tag_end."\n";
						//	Tell the parent template, that it has to parse the child template, before parsing
						//	itself 
						$this->addDependency( $this->template_names[($this->depth-1)], $this->template_names[$this->depth] );
					}
				}
			break;

			//	Found the beginning of a subtemplate
			case "sub":
				//	A subtemplate needs to have a "condition" attribute
				$condition	=	$attributes[condition];

				//	None found => error
				if( isset( $condition ) == 0 )
					die	( "Error in template '".$fname."': missing condition attribute for template in line ".$lineno );

				//	Everything is ok => add the subtemplate and store the condition
				$this->addSubTemplate( $this->template_names[$this->depth], $condition );

				//	Store the current condition
				$this->template_condition[$this->depth]		=	$condition;
			break;
			
			//	Found a link template
			case "link":
				$src		=	strtoupper( $attributes[src] );
				
				if( !$src )
					die	( "Error in template '".$fname."': missing src attribute for link in line ".$lineno );

				//	put a placeholder into the current template
				if	( $this->depth >= 0 )
				{
					$this->template_data[$this->depth]	.=	$this->tag_start."TMPL:".$src.$this->tag_end."\n";
					//	Tell the parent template, that it has to parse the child template, before parsing
					//	itself 
					$this->addDependency( $this->template_names[$this->depth], $src );
				}
			break;
			
			//	No valid Tag found =>
			default:
				die	( "Error in template '".$fname."': unkown Tag in line ".$lineno );
			break;
		}
		
	}

/**
*	handle a </patTemplate:...> end tag in template parser
*
*	@access	private
*	@param	string	$fname		name of the file where the tag was found (kind of parser id)
*	@param	string	$tagname	name of the start tag that was found
*	@param	string	$line		the complete line containing the tag
*/
	function	endElementHandler( $fname, $tagname, $line )
	{
		array_pop( $this->whitespace );

		switch( $tagname )
		{
			//	End of a template found
			case "tmpl":
				//	If the current template is a standard template, store all content
				//	found between <patTemplate> Tags
				if	( $this->template_types[$this->depth] == "STANDARD" || $this->template_types[$this->depth] == "SIMPLECONDITION" )
					$this->setPlainContent( $this->template_names[$this->depth], $this->template_data[$this->depth] );
					
				//	Decrease Tagdepth
				$this->depth--;

				break;

			//	End of a subtemplate found
			case "sub":
				//	Store alle content found between :sub Tags
				$this->setPlainContent( $this->template_names[$this->depth], $this->template_data[$this->depth], $this->template_condition[$this->depth] );

				//	clear all Data, to store the data of the next subtemplate
				$this->template_data[$this->depth]	=	"";
				break;

			//	End of a link found
			case "link":
				//	Just ignore this tag...
				break;

			//	No kown tag found
			default:
				die	( "Error in template '".$fname."': unkown closing tag in line ".$lineno );
			break;
		}
	}

/**
*	handle a CDATA in template parser
*
*	@access	private
*	@param	string	$fname		name of the file where the tag was found (kind of parser id)
*	@param	string	$data		all cdata that was found
*/
	function	DataHandler( $fname, $data )
	{
		$this->template_data[$this->depth]	.=	$data;
	}
	
/**
* Adds a variable to a template
*
* Each Template can have an unlimited amount of its own variables
*
* @param	string	$template	name of the template
* @param	string	$name	name of the variables
* @param	mixed	$value	value of the variable
* @access	public
* @see		addVars(), addRows(), addGlobalVar(), addGlobalVars()
*/
	function	addVar( $template, $name, $value )
	{
		$template	=	strtoupper( $template );
		$name		=	strtoupper( $name );

		if( !is_array( $value ) )
			$value	=	(string)$value;
		
		//	store the value and the name of the variable
		$this->variables[$template][$name]	=	$value;

		//	if the value is an array, the template has to be repeated
		if	( is_array( $value ) )
		{
			//	Check, how often the template has to be repeated
			if( $this->getAttribute( $template, "loop" ) < count( $value ) )
				$this->setAttribute( $template, "loop", count( $value ) );
		}
	}
	
/**
* Adds several variables to a template
*
* Each Template can have an unlimited amount of its own variables
* $variables has to be an assotiative array containing variable/value pairs
*
* @param	string	$template	name of the template
* @param	array	$variables	assotiative array of the variables
* @param	string	$prefix	prefix for all variable names
* @access	public
* @see		addVar(), addRows(), addGlobalVar(), addGlobalVars()
*/
	function	addVars( $template, $variables, $prefix="" )
	{
		//	Are there variables?
		if( !is_array( $variables ) )
			return	false;
			
		//	Add all vars
		while	( list( $name, $value ) = each( $variables ) )
		{
			if( !is_int( $name ) )
				$this->addVar( $template, $prefix.$name, $value );
		}
	}
	
/**
* Adds several rows of variables to a template
*
* Each Template can have an unlimited amount of its own variables
* Can be used to add a database result as variables to a template
*
* @param	string	$template	name of the template
* @param	array	$rows	array containing assotiative arrays with variable/value pairs
* @param	string	$prefix	prefix for all variable names
* @access	public
* @see		addVar(), addVars(), addGlobalVar(), addGlobalVars()
*/
	function	addRows( $template, $rows, $prefix="" )
	{
		//	Store the vars in this array
		$newvars	=	array();
		
		//	get amount of rows		
		$cnt_rows	=	count( $rows );

		if( $cnt_rows == 1 )
			$this->addVars( $template, $rows[0], $prefix );
		else	
		{
			for	( $i = 0; $i < $cnt_rows; $i++ )		
			{
				if( is_array( $rows[$i] ) )
				{
					//	Get key and value
					while( list( $key,$value ) = each( $rows[$i] ) )
					{
						//	check if the array key is an int value => skip it
						if ( !is_int( $key ) )
							//	prepend prefix and store the value
							$new_vars[$prefix.$key][$i]	=	$value;
					}
				}
			}
	
			//	add the vars to the template
			$this->addVars( $template, $new_vars );
		}
	}
	
/**
* Adds a global variable
*
* Global variables are valid in all templates of this object
*
* @param	string	$name	name of the global variable
* @param	string	$value	value of the variable
* @access	public
* @see		addGlobalVars(), addVar(), addVars(), addRows()
*/
	function	addGlobalVar( $name, $value )
	{
		$this->globals[strtoupper($name)]	=	(string)$value;
	}

/**
* Adds several global variables
*
* Global variables are valid in all templates of this object
* $variables is an assotiative array, containing name/value pairs of the variables
*
* @param	array	$variables	array containing the variables
* @param	string	$prefix		prefix for variable names
* @access	public
* @see		addGlobalVar(), addVar(), addVars(), addRows()
*/
	function	addGlobalVars( $variables, $prefix = "" )
	{
		while	( list( $variable, $value ) = each( $variables ) )
		{
			$this->globals[strtoupper( $prefix.$variable )]		=	(string)$value;
		}
	}
	
/**
* Creates a dependeny between two templates
*
* The Dependency tells a template, which templates have to be parsed before parsing the current template, because they are its children.
*
* @param	string	$container	the name of the template, that contains the other template
* @param	string	$child	the child of the container
* @access	private
*/
	function	addDependency( $container, $child )
	{
		$this->dependencies[strtoupper( $container )][]	=	strtoupper( $child );
		//	This template now uses dependencies
		$this->uses_dependencies			=	true;
	}

/**
* loads a template
*
* The template has to be defined using addTemplate() or addTemplates()
*
* @param	string	$name	name of the template that has to be loaded
* @access	private
* @deprecated 2.4 2001/11/05
* @see		addTemplate(), addTemplates();
*/
	function	loadTemplate( $name )
	{
		$name	=	strtoupper( $name );

		//	prepend basedirname, if it exists
		$fname	=	$this->basedir!="" ? $this->basedir."/".$this->source[$name][filename] : $this->source[$name][filename];

		if( stristr( $fname, "[part" ) )
			return	true;
			
		if( !$this->plain_templates[$name]	=	@implode( "", @file( $fname ) ) )
			die( "Couldn't open template '".$name."' (file: '".$fname."') for reading." );
	}
	
/**
* sets the content of a template
*
* This function should used, if a template is added using tags instead of defining it by a filename
*
* @param	string	$template	name of the template
* @param	string	$content	the content that has to be set
* @param	string	$sub	condition, for the subtemplate, if any
* @access	private
*/
	function	setPlainContent( $template, $content, $sub="" )
	{
		$template	=	strtoupper( $template );
	
		//	The content has to be set for a subtemplate
		if	( $sub!="" )
			$this->plain_templates[$template][$sub]		=	$content;
		//	content is meant for a template
		else
			$this->plain_templates[$template]			=	$content;
	}
	
/**
* parses a template
*
* Parses a template and stores the parsed content.
* mode can be "w" for write (delete already parsed content) or "a" for append (appends the
* new parsed content to the already parsed content)
*
*
* @param	string	$template	name of the template
* @param	string	$mode	mode for the parsing
* @access	public
* @see		parseStandardTemplate(), parseIterativeTemplate()
*/
	function	parseTemplate( $template, $mode="w" )
	{
		$template	=	strtoupper( $template );
		$this->iteration[$template]	=	0;

		
		//	The template has to be repeated
		if	( $this->getAttribute( $template, "loop" ) > 1 )
		{
			$this->parseIterativeTemplate( $template, $mode );
		}
		//	parse it once
		else
		{
			$this->parseStandardTemplate( $template, $mode );
		}
	}

/**
* parses a standard template
*
* Parses a template and stores the parsed content.
* mode can be "w" for write (delete already parsed content) or "a" for append (appends the
* new parsed content to the already parsed content)
*
*
* @param	string	$name	name of the template
* @param	string	$mode	mode for the parsing
* @access	private
* @see		parseTemplate(), parseIterativeTemplate()
*/
	function	parseStandardTemplate( $name, $mode="w" )
	{
		$name	=	strtoupper( $name );
	
		//	get a copy of the plain content

		$temp	=	$this->getTemplateContent( $name );

		$vars					=	$this->getVars( $name );
		$vars[$this->tag_start."PAT_ROW_VAR".$this->tag_end]	=	1;
		while( list( $tag, $value ) = each( $vars ) )
		{
			if( is_array( $value ) )
			{
				$value	=	$value[0];
			}
			$temp		=	str_replace( $tag, $value, $temp );
		}
			
		//	parse all global vars
		$this->parseGlobals( $name, $temp );
		
		//	parse child templates into this template
		$this->parseDependencies( $name, $temp, $mode );

		//	Strip unsused vars
		$this->stripUnusedVars( $name, $temp );

		if( $mode=="a" )
			$this->parsed_templates[$name]	.=	$temp;
		elseif( $mode=="w" )
			$this->parsed_templates[$name]	=	$temp;
	}
	
/**
* parses an iterative template
*
* Parses a template and stores the parsed content.
* mode can be "w" for write (delete already parsed content) or "a" for append (appends the
* new parsed content to the already parsed content)
*
*
* @param	string	$name	name of the template
* @param	string	$mode	mode for the parsing
* @access	private
* @see		parseTemplate(), parseStandardTemplate()
*/
	function	parseIterativeTemplate( $name, $mode )
	{
		$name	=	strtoupper( $name );

		$temp	=	"";
		
		//	repeat it template_loop[$name] times
		for	( $PAT_ROW_VAR = 0; $PAT_ROW_VAR < $this->getAttribute( $name, "loop" ); $PAT_ROW_VAR++ )
		{
			//	add the PAT_ROW_VAR variable to the template
			$this->variables[$name]["PAT_ROW_VAR"][$PAT_ROW_VAR]	=	$PAT_ROW_VAR + 1;
			$this->iteration[$name]		=	$PAT_ROW_VAR;
			
			//	get the content to be parsed (dependent on PAT_ROW_VAR or conditionvar)
			$current		=	$this->getTemplateContent( $name );
			
			$vars			=	$this->getVars( $name );
			while( list( $tag, $value ) = each( $vars ) )
				$current		=	str_replace( $tag, $value, $current );

			//	and the dependent Templates
			$this->parseDependencies( $name, $current, $mode );
				
			//	append this parsed to the repetition
			$temp		.=	$current;
		}

		//	after parsing repetitions, parse the Global Vars
		$this->parseGlobals( $name, $temp );

		//	Strip unsused vars
		$this->stripUnusedVars( $name, $temp );

		if( $mode=="a" )
			$this->parsed_templates[$name]	.=	$temp;
		elseif( $mode=="w" )
			$this->parsed_templates[$name]	=	$temp;

	}

/**
*	get variables for a template
*	if the templates uses the attribute 'varscope' these vars will be fetched, too
*
*	@access	private
*	@param	string	$template	name of the template
*	@return	array	$vars		array containign vars
*/
	function	getVars( $template )
	{
		$vars	=	array();
		//	parse all vars
		if( is_array( $this->variables[$template] ) )
		{
			//	Pointer im Array auf 0 setzen
			reset( $this->variables[$template] );
	
			while	( list( $variable, $value ) = each( $this->variables[$template] ) )
			{
				$tag	=	$this->tag_start.$variable.$this->tag_end;

				//	if the variable is an array, use the index
				if	( is_array( $value ) )
					$value	=	$value[$this->iteration[$template]];

				$vars[$tag]		=	$value;
			}
		}

		if( $scope = strtoupper( $this->getAttribute( $template, "varscope" ) ) )
		{
			$parentVars		=	$this->getVars( $scope );
			reset( $parentVars );
			while( list( $var, $value ) = each( $parentVars ) )
			{
				if( !$vars[$var] )
					$vars[$var]		=	$value;
			}
		}
		reset( $vars );
		return	$vars;
	}
	
/**
* parses the global variables in a template
*
* global variables are valid in all templates
*
* @param	string	$name	name of the template
* @param	string	&$temp	content of the parsed Template
* @access	private
* @see		parseTemplate(), addGlobalVar(), addGlobalVars()
*/
	function	parseGlobals( $name, &$temp )
	{
		$name	=	strtoupper( $name );

		//	check, if globals exist
		if( is_array( $this->globals ) )
		{
			reset( $this->globals );
			
			while	( list( $variable, $value ) = each( $this->globals ) )
			{
				$tag	=	$this->tag_start.$variable.$this->tag_end;
				$temp	=	str_replace( $tag, $value, $temp );
			}
		}
	}

/**
* handles unset variables
*
* either strips, comments, replaces or ignores them, depending on the unusedvars attribute
*
* @param	string	$name	name of the template
* @param	string	&$template	content of the parsed Template
* @access	private
* @see		setAttribute()
*/
	function	stripUnusedVars( $name, &$template )
	{
		switch( $this->getAttribute( $name, "unusedvars" ) )
		{
			case	"comment":
				$template	=	preg_replace( "/(".$this->tag_start."[^a-z{}]+".$this->tag_end.")/", "<!-- \\1 -->", $template );
				break;
			case	"strip":
				$template	=	preg_replace( "/(".$this->tag_start."[^a-z{}]+".$this->tag_end.")/", "", $template );
				break;
			case	"nbsp":
				$template	=	preg_replace( "/(".$this->tag_start."[^a-z{}]+".$this->tag_end.")/", "&nbsp;", $template );
				break;
			case	"ignore":
				break;
			default:
				$template	=	preg_replace( "/(".$this->tag_start."[^a-z{}]+".$this->tag_end.")/", $this->getAttribute( $name, "unusedvars" ), $template );
				break;
		}
	}

/**
* parses dependencies of a template
*
* parses child templates of a template and inserts their content
*
* @param	string	$name	name of the template
* @param	string	&$temp	content of the parsed Template
* @access	private
* @see		addDependency()
*/
	function	parseDependencies( $name, &$temp, $mode = "w" )
	{
		$name	=	strtoupper( $name );

		for( $i = 0; $i < count( $this->dependencies[$name] ); $i++ )
		{
			$type		=	$this->getAttribute( strtoupper( $this->dependencies[$name][$i] ), "type" );

			//	Templates placeholders have the prefix TMPL:
			$tag	=	$this->tag_start."TMPL:".$this->dependencies[$name][$i].$this->tag_end;
			//	Get the parsed child template and replace it
			$temp	=	str_replace( $tag, $this->getParsedTemplate( $this->dependencies[$name][$i] ), $temp );

			if( ( $type == patTEMPLATE_TYPE_CONDITION || $type == patTEMPLATE_TYPE_SIMPLECONDITION ) && $mode == "w" )
				unset( $this->parsed_templates[$this->dependencies[$name][$i]] );
		}
	}

/**
* returns a parsed Template
*
* If the template already has been parsed, it just returns the parsed template.
* If the template has not been loaded, it will be loaded.
*
* @param	string	$name	name of the template
* @return	string	$content	Content of the parsed template
* @access	public
* @see		displayParsedTemplate()
*/
	function	getParsedTemplate( $name="" )
	{
		$name	=	strtoupper( $name );

		//	if a name was given, parse only this template
		if	( $name!="" )
		{
			//	check, wther template was disabled
			if( $this->getAttribute( $name, "visibility" ) == "hidden" )
				return	false;
			
			//	check, if the template has already been parsed => just return it
			if	( !empty( $this->parsed_templates[$name] ) )
				return	$this->parsed_templates[$name];
	
			//	Check, if the template has been loaded, if not, load it
			if	( empty( $this->plain_templates[$name] ) )
				$this->loadTemplate( $name );
	
			//	Template is loaded, but not parsed then parse it!			
			$this->parseTemplate( $name );
			
			//	And return the parsed template
			return	$this->parsed_templates[$name];
		}

		//	No name given
		else
		{
			//	The template uses dependencies, then start with the root template
			if	( $this->uses_dependencies )
				return	$this->getParsedTemplate( $this->templates[0] );
			//	Only one template => parse and return it
			elseif( $this->cnt_templates==1 )
				return	$this->getParsedTemplate( $this->templates[0] );
			//	No dependencies, but more than one => return all parsed templates in an array
			else
			{
				for	( $i = 0; $i < $this->cnt_templates; $i++ )
					{
					$arr[$this->templates[$i]]	=	$this->getParsedTemplate( $this->templates[$i] );
					}
				return	$arr;
			}
		}
	}

/**
* displays a parsed Template
*
* If the template has not been loaded, it will be loaded.
*
* @param	string	$name	name of the template
* @access	public
* @see		getParsedTemplate()
*/
	function	displayParsedTemplate( $name="" )
	{
		$name	=	strtoupper( $name );

		//	if a name was given, parse and display it
		if	( $name )
			echo	$this->getParsedTemplate( $name );

		//	No name was given, display them all!
		else
		{
			//	if the template uses dependencies, start with the root template
			if	( $this->uses_dependencies )
				echo	$this->getParsedTemplate( $this->templates[0] );
			//	Only one template => parse and return it
			elseif( $this->cnt_templates==1 )
				echo	$this->getParsedTemplate( $this->templates[0] );
			//	parse and display them all
			else
			{
				$templates	=	$this->getParsedTemplate();
				for	( $i = 0; $i < $this->cnt_templates; $i++ )
					echo	$templates[$this->templates[$i]];
			}
		}
	}

/**
* returns an unparsed Template
*
* If the template has not been loaded, it will be loaded.
*
* @param	string	$name	name of the template
* @access	private
* @deprecated 2.4 2001/11/05
* @return	string $content	Unparsed content of the template
* @see		getPlainSubTemplate(), displayPlainTemplate()
*/
	function	getPlainTemplate( $name )
	{
		$name	=	strtoupper( $name );

		//	check, wether the template is already loaded
		if	( empty( $this->plain_templates[$name] ) )
			$this->loadTemplate( $name );

		//	return	it
		return	$this->plain_templates[$name];
	}
	
/**
* returns an unparsed Subtemplate
*
* The template of the template has to be set
*
* @param	string	$name	name of the template
* @param	string	$sub	condition for the subtemplate
* @access	private
* @deprecated 2.4 2001/11/05
* @return	string $content	Unparsed content of the template
* @see		getPlainTemplate(), displayPlainTemplate()
*/
	function	getPlainSubTemplate( $name, $sub )
	{
		$name	=	strtoupper( $name );

		return	$this->plain_templates[$name][$sub];
	}
	
/**
* displays an unparsed Template
*
* If the template has not been loaded, it will be loaded.
*
* @param	string	$name	name of the template
* @access	private
* @deprecated 2.4 2001/11/05
* @see		getPlainTemplate(), getPlainSubTemplate()
*/
	function	displayPlainTemplate( $name )
	{
		$name	=	strtoupper( $name );

		echo	$this->getPlainTemplate( $name );
	}

/**
* clears a parsed Template
*
* parsed Content, variables and the loop attribute are cleared
*
* @param	string	$name	name of the template
* @access	public
*/
	function	clearTemplate( $name )
	{
		$name	=	strtoupper( $name );

		unset( $this->parsed_templates[$name] );
		unset( $this->variables[$name] );
		$this->clearAttribute( $name, "loop" );
	} 

/**
* clears all templates
*
* @access	public
*/
	function	clearAllTemplates()
	{
		for( $i=0; $i<count( $this->templates ); $i++ )
			$this->clearTemplate( $this->templates[$i] );
	} 

/**
* parsed attributes from a string
*
* used for parsing <patTemplate> Tags
*
* @param	string	$string	string containing the attributes
* @return	array	$array	assotiative array, containing all attributes
* @access	private
*/
	function	parseAttributes( $string )
	{
		//	Check for trailing slash, if tag was an empty XML Tag
		if( substr( $string, -1 ) == "/" )
			$string	=	substr( $string, 0, strlen( $string )-1 );

		$pairs		=	explode( " ", $string );
		for	( $i = 0; $i < count($pairs); $i++ )
		{
			$pair					=	explode( "=", trim( str_replace( "\"", "", $pairs[$i] ) ) );

			if( count( $pair ) == 1 )
				$pair[1]			=	"yes";
			
			$attributes[strtolower( $pair[0]) ]	=	$pair[1];
		}
		return	$attributes;
	}

/**
* returns the plain content of a template
*
* return value depends on iteration value
*
* @param	string	$name		name of the template
* @param	int	$index			iteration number
* @return	string	$content	plain content of the template
* @access	private
*/
	function	getTemplateContent( $name )
	{
		$name	=	strtoupper( $name );
		$index	=	$this->iteration[$name];
		
		//	Is it a standard, oddeven or condition template
		switch	( $this->getAttribute( $name, "type" ) )
		{
			case patTEMPLATE_TYPE_ODDEVEN:
				$sub		=	( $index + 1 ) % 2 == 0 ? "even" : "odd";
				return	$this->plain_templates[$name][$sub];
				break;
			case patTEMPLATE_TYPE_CONDITION:
				$conditionval		=	$this->getVar( $name, $this->conditionvars[$name] );

				//	check, if conditionvalue is empty
				if( !isset( $conditionval ) || ( is_string( $conditionval ) && $conditionval == "" ) || $conditionval === false )
					$conditionval	=	"empty";

				//	check if condition was specified, otherwise use default
				$condition_found	=	false;
				for( $i = 0; $i < $this->cnt_subtemplates[$name]; $i++ )
				{
					if( $this->subtemplate_conditions[$name][$i]==$conditionval )
					{
						$condition_found	=	true;
						break;
					}
				}
				if( !$condition_found )
					$conditionval	=	"default";

				return	$this->plain_templates[$name][$conditionval];
				break;

			case patTEMPLATE_TYPE_SIMPLECONDITION:
				//	get required vars
				$requiredVars	=	$this->getAttribute( $name, "requiredvars" );

				//	check, if all are set
				for( $i = 0; $i < count( $requiredVars ); $i++ )
					if( !$this->getVar( $name, $requiredVars[$i] ) )
						return	"";

				return	$this->plain_templates[$name];
				break;

			default:
				return	$this->plain_templates[$name];
				break;
		}
	}

/**
*	get the value of a variable
*
*	@param	string	$template	name of the template
*	@param	string	$var		name of the variable
*	@param	integer	$index		no of repetition
*	@return	mixed	$value		value of the variable / false if it doesn't exist
*/
	function	getVar( $template, $var )
	{
		//	should the var from a different template be used 
		if( stristr( $var, "." ) )
			list( $template, $var )	=	explode( ".", $var );
			
		$var	=	strtoupper( $var );
		$index	=	$this->iteration[$template];
		if( $scope = $this->getAttribute( $template, "varscope" ) )
		{
			$val	=	$this->getVar( strtoupper( $scope ), $var );
		}
		else
			$val	=	$this->variables[$template][$var];

		//	check, if global var should be used
		if( !$val && $this->getAttribute( $template, "useglobals" ) == "yes" )
			$val	=	$this->globals[$var];

		if	( is_array( $val ) )
			$val	=	$val[$index];

		return	$val;
	}
	
	
/**
* displays useful information about all templates
*
* returns content, variables, attributes and unused variables
*
* @access	public
*/
	function	dump()
	{
		echo	"<style type=\"text/css\">\n";
		echo	".text		{font-family: Verdana, Arial, sans-serif; font-size: 10px; color: #000000}\n";
		echo	".mid		{font-family: Verdana, Arial, sans-serif; font-size: 12px; color: #000000}\n";
		echo	".head		{font-family: Verdana, Arial, sans-serif; font-size: 16px; font-weight: bold; color: #000000}\n";
		echo	"</style>\n";

		echo	"<table border=\"1\" cellpadding=\"2\" cellspacing=\"1\" bgcolor=\"White\" >\n";

		for	( $i = 0; $i < $this->cnt_templates; $i++ )
		{
			$name	=	$this->templates[$i];
			
			//	Template name
			echo	"	<tr bgcolor=\"#EEEEEE\">\n";
			echo	"		<td class=\"head\" valign=\"top\">Template</td>\n";
			echo	"		<td class=\"head\" valign=\"top\">".$name."</td>\n";
			echo	"	</tr>\n";

			$fname	=	$this->basedir!="" ? $this->basedir."/".$this->filenames[$name] : $this->filenames[$name];

			//	Template file
			echo	"	<tr>\n";
			echo	"		<td class=\"mid\" valign=\"top\"><b>Filename</b></td>\n";
			echo	"		<td class=\"text\" valign=\"top\">".$fname."</td>\n";
			echo	"	</tr>\n";

			//	Template Attributes

			echo	"	<tr>\n";
			echo	"		<td class=\"mid\" valign=\"top\"><b>Attributes</b></td>\n";
			echo	"		<td class=\"text\" valign=\"top\">\n";

			echo	"			<table border=\"0\" cellpadding=\"0\" cellspacing=\"1\">\n";

			//	Display all Attributes in table
			while( list( $key, $value ) = each( $this->attributes[$name] ) )
			{
				echo	"				<tr>\n";
				echo	"					<td class=\"text\"><b>".$key."</b></td>\n";
				echo	"					<td class=\"text\"> : </td>\n";
				echo	"					<td class=\"text\">".$value."</td>\n";
				echo	"				</tr>\n";

			}
			echo	"			</table>\n";
			
			echo	"		</td>\n";
			echo	"	</tr>\n";

			echo	"	<tr>\n";
			echo	"		<td class=\"text\" valign=\"top\" colspan=\"2\">&nbsp;</td>\n";
			echo	"	</tr>\n";

			if	( $this->cnt_subtemplates[$name] > 0 )
			{
				//	display template Data
				echo	"	<tr>\n";
				echo	"		<td class=\"mid\" valign=\"top\"><b>Template Data:</b></td>\n";
				echo	"		<td class=\"text\" valign=\"top\">Amount of subtemplates: ".$this->cnt_subtemplates[$name]."</td>\n";
				echo	"	</tr>\n";

				//	Display all Subtemplates
				for	( $j = 0; $j < $this->cnt_subtemplates[$name]; $j++ )
				{

					$condition		=	$this->subtemplate_conditions[$name][$j];
					echo	"				<tr bgcolor=\"#DDDDDD\">\n";
					echo	"					<td class=\"text\"><b>Condition</b></td>\n";
					echo	"					<td class=\"text\">".$condition."</td>\n";
					echo	"				</tr>\n";
				
					echo	"				<tr>\n";
					echo	"					<td class=\"text\" valign=\"top\"><b>Template Data</b></td>\n";
					echo	"					<td class=\"text\" valign=\"top\"><pre>".htmlspecialchars( $this->getPlainSubTemplate( $name, $condition ) )."</pre></td>\n";
					echo	"				</tr>\n";

					unset( $matches );
					//	Check for unset variables
					preg_match_all ( $this->regex_get_all_vars, $this->getPlainSubTemplate( $name, $condition ), $matches );
	
					//	Empty the array that stores the unused Vars
					unset( $unused );

					if( is_array( $matches[0] ) && count( $matches[0] ) > 0 )				
					{
						//	Check, wether variable is unused
						for( $k = 0; $k<=count( $matches[0] ); $k++ )
							if( $matches[1][$k]!="" && !isset( $this->variables[$name][$matches[1][$k]] ) && !isset( $this->globals[$matches[1][$k]] ) )
								$unused[]	=	$matches[0][$k];
					}
					
					if( is_array( $unused ) && count( $unused ) > 0 )
					{
						echo	"	<tr>\n";
						echo	"		<td class=\"mid\" valign=\"top\"><b>Unused variables</b></td>\n";
						echo	"		<td class=\"text\" valign=\"top\">\n";
			
						echo	"			<table border=\"0\" cellpadding=\"0\" cellspacing=\"1\">\n";
		
						//	Display all Variables in table
						for( $k = 0; $k<=count( $unused ); $k++ )
						{
							{
								echo	"				<tr>\n";
								echo	"					<td class=\"text\">".$unused[$k]."</td>\n";
								echo	"				</tr>\n";
							}
			
						}
						echo	"			</table>\n";
						
						echo	"		</td>\n";
						echo	"	</tr>\n";
					}

					echo	"				<tr>\n";
					echo	"					<td class=\"text\" colspan=\"2\">&nbsp;</td>\n";
					echo	"				</tr>\n";
				}
			
			}
			else
			{
				//	display template Data
				echo	"	<tr>\n";
				echo	"		<td class=\"mid\" valign=\"top\"><b>Template Data:</b></td>\n";
				echo	"		<td class=\"text\" valign=\"top\">\n";
				echo	"			<pre>".htmlspecialchars( $this->getPlainTemplate( $name ) )."</pre>\n";
				echo	"		</td>\n";
				echo	"	</tr>\n";

				unset( $matches );
				//	Check for unset variables
				preg_match_all ( $this->regex_get_all_vars, $this->getPlainTemplate( $name ), $matches );

				//	Empty the array that stores the unused Vars
				unset( $unused );
				
				if( is_array( $matches[0] ) && count( $matches[0] ) > 0 )				
				{
					//	Check, wether variable is unused
					for( $k = 0; $k<count( $matches[0] ); $k++ )
						if( $matches[1][$k]!="" && !isset( $this->variables[$name][$matches[1][$k]] ) && !isset( $this->globals[$matches[1][$k]] ) )
							$unused[]	=	$matches[0][$k];
				}
				
				if( is_array( $unused ) && count( $unused ) > 0 )
				{
					echo	"	<tr>\n";
					echo	"		<td class=\"mid\" valign=\"top\"><b>Unused variables</b></td>\n";
					echo	"		<td class=\"text\" valign=\"top\">\n";
		
					echo	"			<table border=\"0\" cellpadding=\"0\" cellspacing=\"1\">\n";
	
					//	Display all Variables in table
					for( $k = 0; $k<=count( $unused ); $k++ )
					{
						{
							echo	"				<tr>\n";
							echo	"					<td class=\"text\">".$unused[$k]."</td>\n";
							echo	"				</tr>\n";
						}
		
					}
					echo	"			</table>\n";
					
					echo	"		</td>\n";
					echo	"	</tr>\n";
				}
			}

			//	Display Variables

			if( is_array( $this->variables[$name] ) && count( $this->variables[$name] ) > 0  )
			{
				reset( $this->variables[$name] );

				echo	"	<tr>\n";
				echo	"		<td class=\"mid\" valign=\"top\"><b>Variables</b></td>\n";
				echo	"		<td class=\"text\" valign=\"top\">\n";
	
				echo	"			<table border=\"0\" cellpadding=\"0\" cellspacing=\"1\">\n";
	
				//	Display all Variables in table
				while( list( $key, $value ) = each( $this->variables[$name] ) )
				{
					if( is_array( $value ) )
						$value	=	implode( ", ", $value );
						
					echo	"				<tr>\n";
					echo	"					<td class=\"text\"><b>".$this->tag_start.$key.$this->tag_end."</b></td>\n";
					echo	"					<td class=\"text\"> => </td>\n";
					echo	"					<td class=\"text\">".$value."</td>\n";
					echo	"				</tr>\n";
	
				}
				echo	"			</table>\n";
				
				echo	"		</td>\n";
				echo	"	</tr>\n";
			}
	
			echo	"	<tr>\n";
			echo	"		<td class=\"text\" valign=\"top\" colspan=\"2\">&nbsp;</td>\n";
			echo	"	</tr>\n";

		}
		echo	"</table>";
	}
}
?>
