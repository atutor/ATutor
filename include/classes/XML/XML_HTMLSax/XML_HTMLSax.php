<?php
/* vim: set expandtab tabstop=4 shiftwidth=4: */
//
// +----------------------------------------------------------------------+
// | PHP Version 4                                                        |
// +----------------------------------------------------------------------+
// | Copyright (c) 1997-2002 The PHP Group                                |
// +----------------------------------------------------------------------+
// | This source file is subject to version 2.02 of the PHP license,      |
// | that is bundled with this package in the file LICENSE, and is        |
// | available at through the world-wide-web at                           |
// | http://www.php.net/license/3_0.txt.                                  |
// | If you did not receive a copy of the PHP license and are unable to   |
// | obtain it through the world-wide-web, please send a note to          |
// | license@php.net so we can mail you a copy immediately.               |
// +----------------------------------------------------------------------+
// | Authors: Alexander Zhukov <alex@veresk.ru> Original port from Python |
// | Authors: Harry Fuecks <hfuecks@phppatterns.com> Port to PEAR + more  |
// | Authors: Many @ Sitepointforums Advanced PHP Forums                  |
// +----------------------------------------------------------------------+
//
// $Id$
//
/**
* Main parser components
* @package XML_HTMLSax
* @version $Id$
*/
/**
* Required classes
*/
require_once(AT_INCLUDE_PATH.'classes/XML/XML_HTMLSax/PEAR.php');
if (!defined('XML_HTMLSAX')) {
    define('XML_HTMLSAX', AT_INCLUDE_PATH . 'classes/XML/XML_HTMLSax/'); /*  'XML/' had to be removed to work. joel/ATRC */
}
require_once(XML_HTMLSAX . 'HTMLSax/XML_HTMLSax_States.php');
require_once(XML_HTMLSAX . 'HTMLSax/XML_HTMLSax_Decorators.php');
/**
* Base State Parser
* @package XML_HTMLSax
* @access protected
* @abstract
*/
class XML_HTMLSax_StateParser {
    /**
    * Instance of user front end class to be passed to callbacks
    * @var XML_HTMLSax
    * @access private
    */
    var $htmlsax;
    /**
    * User defined object for handling elements
    * @var object
    * @access private
    */
    var $handler_object_element;
    /**
    * User defined open tag handler method
    * @var string
    * @access private
    */
    var $handler_method_opening;
    /**
    * User defined close tag handler method
    * @var string
    * @access private
    */
    var $handler_method_closing;
    /**
    * User defined object for handling data in elements
    * @var object
    * @access private
    */
    var $handler_object_data;
    /**
    * User defined data handler method
    * @var string
    * @access private
    */
    var $handler_method_data;
    /**
    * User defined object for handling processing instructions
    * @var object
    * @access private
    */
    var $handler_object_pi;
    /**
    * User defined processing instruction handler method
    * @var string
    * @access private
    */
    var $handler_method_pi;
    /**
    * User defined object for handling JSP/ASP tags
    * @var object
    * @access private
    */
    var $handler_object_jasp;
    /**
    * User defined JSP/ASP handler method
    * @var string
    * @access private
    */
    var $handler_method_jasp;
    /**
    * User defined object for handling XML escapes
    * @var object
    * @access private
    */
    var $handler_object_escape;
    /**
    * User defined XML escape handler method
    * @var string
    * @access private
    */
    var $handler_method_escape;
    /**
    * User defined handler object or NullHandler
    * @var object
    * @access private
    */
    var $handler_default;
    /**
    * Parser options determining parsing behavior
    * @var array
    * @access private
    */
    var $parser_options = array();
    /**
    * XML document being parsed
    * @var string
    * @access private
    */
    var $rawtext;
    /**
    * Position in XML document relative to start (0)
    * @var int
    * @access private
    */
    var $position;
    /**
    * Length of the XML document in characters
    * @var int
    * @access private
    */
    var $length;
    /**
    * Array of state objects
    * @var array
    * @access private
    */
    var $State = array();

    /**
    * Constructs XML_HTMLSax_StateParser setting up states
    * @var XML_HTMLSax instance of user front end class
    * @access protected
    */
    function XML_HTMLSax_StateParser (& $htmlsax) {
        $this->htmlsax = & $htmlsax;
        $this->State[XML_HTMLSAX_STATE_START] = new XML_HTMLSax_StartingState();

        $this->State[XML_HTMLSAX_STATE_CLOSING_TAG] = new XML_HTMLSax_ClosingTagState();
        $this->State[XML_HTMLSAX_STATE_TAG] = new XML_HTMLSax_TagState();
        $this->State[XML_HTMLSAX_STATE_OPENING_TAG] = new XML_HTMLSax_OpeningTagState();

        $this->State[XML_HTMLSAX_STATE_PI] = new XML_HTMLSax_PiState();
        $this->State[XML_HTMLSAX_STATE_JASP] = new XML_HTMLSax_JaspState();
        $this->State[XML_HTMLSAX_STATE_ESCAPE] = new XML_HTMLSax_EscapeState();
    }

    /**
    * Moves the position back one character
    * @access protected
    * @return void
    */
    function unscanCharacter() {
        $this->position -= 1;
    }

    /**
    * Moves the position forward one character
    * @access protected
    * @return void
    */
    function ignoreCharacter() {
        $this->position += 1;
    }

    /**
    * Returns the next character from the XML document or void if at end
    * @access protected
    * @return mixed
    */
    function scanCharacter() {
        if ($this->position < $this->length) {
            return $this->rawtext{$this->position++};
        }
    }

    /**
    * Returns a string from the current position to the next occurance
    * of the supplied string
    * @param string string to search until
    * @access protected
    * @return string
    */
    function scanUntilString($string) {
        $start = $this->position;
        $this->position = @strpos($this->rawtext, $string, $start);
        if ($this->position === FALSE) {
            $this->position = $this->length;
        }
        return substr($this->rawtext, $start, $this->position - $start);
    }

    /**
    * Returns a string from the current position until the first instance of
    * one of the characters in the supplied string argument
    * @param string string to search until
    * @access protected
    * @return string
    * @abstract
    */
    function scanUntilCharacters($string) {}

    /**
    * Moves the position forward past any whitespace characters
    * @access protected
    * @return void
    * @abstract
    */
    function ignoreWhitespace() {}

    /**
    * Begins the parsing operation, setting up any decorators, depending on
    * parse options invoking _parse() to execute parsing
    * @param string XML document to parse
    * @access protected
    * @return void
    */
    function parse($data) {
        if ($this->parser_options['XML_OPTION_TRIM_DATA_NODES']==1) {
            $decorator = new XML_HTMLSax_Trim(
                $this->handler_object_data,
                $this->handler_method_data);
            $this->handler_object_data =& $decorator;
            $this->handler_method_data = 'trimData';
        }
        if ($this->parser_options['XML_OPTION_CASE_FOLDING']==1) {
            $open_decor = new XML_HTMLSax_CaseFolding(
                $this->handler_object_element,
                $this->handler_method_opening,
                $this->handler_method_closing);
            $this->handler_object_element =& $open_decor;
            $this->handler_method_opening ='foldOpen';
            $this->handler_method_closing ='foldClose';
        }
        if ($this->parser_options['XML_OPTION_LINEFEED_BREAK']==1) {
            $decorator = new XML_HTMLSax_Linefeed(
                $this->handler_object_data,
                $this->handler_method_data);
            $this->handler_object_data =& $decorator;
            $this->handler_method_data = 'breakData';
        }
        if ($this->parser_options['XML_OPTION_TAB_BREAK']==1) {
            $decorator = new XML_HTMLSax_Tab(
                $this->handler_object_data,
                $this->handler_method_data);
            $this->handler_object_data =& $decorator;
            $this->handler_method_data = 'breakData';
        }
        if ($this->parser_options['XML_OPTION_ENTITIES_UNPARSED']==1) {
            $decorator = new XML_HTMLSax_Entities_Unparsed(
                $this->handler_object_data,
                $this->handler_method_data);
            $this->handler_object_data =& $decorator;
            $this->handler_method_data = 'breakData';
        }
        if ($this->parser_options['XML_OPTION_ENTITIES_PARSED']==1) {
            $decorator = new XML_HTMLSax_Entities_Parsed(
                $this->handler_object_data,
                $this->handler_method_data);
            $this->handler_object_data =& $decorator;
            $this->handler_method_data = 'breakData';
        }
        $this->rawtext = $data;
        $this->length = strlen($data);
        $this->position = 0;
        $this->_parse();
    }

    /**
    * Performs the parsing itself, delegating calls to a specific parser
    * state
    * @param constant state object to parse with
    * @access protected
    * @return void
    */
    function _parse($state = XML_HTMLSAX_STATE_START) {
        do {
            $state = $this->State[$state]->parse($this);
        } while ($state != XML_HTMLSAX_STATE_STOP &&
                    $this->position < $this->length);
    }
}

/**
* Parser for PHP Versions below 4.3.0. Uses a slower parsing mechanism than
* the equivalent PHP 4.3.0+  subclass of StateParser
* @package XML_HTMLSax
* @access protected
* @see XML_HTMLSax_StateParser_Gtet430
*/
class XML_HTMLSax_StateParser_Lt430 extends XML_HTMLSax_StateParser {
    /**
    * Constructs XML_HTMLSax_StateParser_Lt430 defining available
    * parser options
    * @var XML_HTMLSax instance of user front end class
    * @access protected
    */
    function XML_HTMLSax_StateParser_Lt430(& $htmlsax) {
        parent::XML_HTMLSax_StateParser($htmlsax);
        $this->parser_options['XML_OPTION_TRIM_DATA_NODES'] = 0;
        $this->parser_options['XML_OPTION_CASE_FOLDING'] = 0;
        $this->parser_options['XML_OPTION_LINEFEED_BREAK'] = 0;
        $this->parser_options['XML_OPTION_TAB_BREAK'] = 0;
        $this->parser_options['XML_OPTION_ENTITIES_PARSED'] = 0;
        $this->parser_options['XML_OPTION_ENTITIES_UNPARSED'] = 0;
        $this->parser_options['XML_OPTION_FULL_ESCAPES'] = 0;
    }

    /**
    * Returns a string from the current position until the first instance of
    * one of the characters in the supplied string argument
    * @param string string to search until
    * @access protected
    * @return string
    */
    function scanUntilCharacters($string) {
        $startpos = $this->position;
        while ($this->position < $this->length && strpos($string, $this->rawtext{$this->position}) === FALSE) {
            $this->position++;
        }
        return substr($this->rawtext, $startpos, $this->position - $startpos);
    }

    /**
    * Moves the position forward past any whitespace characters
    * @access protected
    * @return void
    */
    function ignoreWhitespace() {
        while ($this->position < $this->length && 
            strpos(" \n\r\t", $this->rawtext{$this->position}) !== FALSE) {
            $this->position++;
        }
    }

    /**
    * Begins the parsing operation, setting up the unparsed XML entities
    * decorator if necessary then delegating further work to parent
    * @param string XML document to parse
    * @access protected
    * @return void
    */
    function parse($data) {
        parent::parse($data);
    }
}

/**
* Parser for PHP Versions equal to or greater than 4.3.0. Uses a faster
* parsing mechanism than the equivalent PHP < 4.3.0 subclass of StateParser
* @package XML_HTMLSax
* @access protected
* @see XML_HTMLSax_StateParser_Lt430
*/
class XML_HTMLSax_StateParser_Gtet430 extends XML_HTMLSax_StateParser {
    /**
    * Constructs XML_HTMLSax_StateParser_Gtet430 defining available
    * parser options
    * @var XML_HTMLSax instance of user front end class
    * @access protected
    */
    function XML_HTMLSax_StateParser_Gtet430(& $htmlsax) {
        parent::XML_HTMLSax_StateParser($htmlsax);
        $this->parser_options['XML_OPTION_TRIM_DATA_NODES'] = 0;
        $this->parser_options['XML_OPTION_CASE_FOLDING'] = 0;
        $this->parser_options['XML_OPTION_LINEFEED_BREAK'] = 0;
        $this->parser_options['XML_OPTION_TAB_BREAK'] = 0;
        $this->parser_options['XML_OPTION_ENTITIES_PARSED'] = 0;
        $this->parser_options['XML_OPTION_ENTITIES_UNPARSED'] = 0;
        $this->parser_options['XML_OPTION_FULL_ESCAPES'] = 0;
    }
    /**
    * Returns a string from the current position until the first instance of
    * one of the characters in the supplied string argument.
    * @param string string to search until
    * @access protected
    * @return string
    */
    function scanUntilCharacters($string) {
        $startpos = $this->position;
        $length = strcspn($this->rawtext, $string, $startpos);
        $this->position += $length;
        return substr($this->rawtext, $startpos, $length);
    }

    /**
    * Moves the position forward past any whitespace characters
    * @access protected
    * @return void
    */
    function ignoreWhitespace() {
        $this->position += strspn($this->rawtext, " \n\r\t", $this->position);
    }

    /**
    * Begins the parsing operation, setting up the parsed and unparsed
    * XML entity decorators if necessary then delegating further work
    * to parent
    * @param string XML document to parse
    * @access protected
    * @return void
    */
    function parse($data) {
        parent::parse($data);
    }
}

/**
* Default NullHandler for methods which were not set by user
* @package XML_HTMLSax
* @access protected
*/
class XML_HTMLSax_NullHandler {
    /**
    * Generic handler method which does nothing
    * @access protected
    * @return void
    */
    function DoNothing() {
    }
}

/**
* User interface class. All user calls should only be made to this class
* @package XML_HTMLSax
* @access public
*/
class XML_HTMLSax extends Pear {
    /**
    * Instance of concrete subclass of XML_HTMLSax_StateParser
    * @var XML_HTMLSax_StateParser
    * @access private
    */
    var $state_parser;

    /**
    * Constructs XML_HTMLSax selecting concrete StateParser subclass
    * depending on PHP version being used as well as setting the default
    * NullHandler for all callbacks<br />
    * <b>Example:</b>
    * <pre>
    * $myHandler = new MyHandler();
    * $parser = new XML_HTMLSax();
    * $parser->set_object($myHandler);
    * $parser->set_option('XML_OPTION_CASE_FOLDING');
    * $parser->set_element_handler('myOpenHandler','myCloseHandler');
    * $parser->set_data_handler('myDataHandler');
    * $parser->parser($xml);
    * </pre>
    * @access public
    */
    function XML_HTMLSax() {
        if (version_compare(phpversion(), '4.3', 'ge')) {
            $this->state_parser = new XML_HTMLSax_StateParser_Gtet430($this);
        } else {
            $this->state_parser = new XML_HTMLSax_StateParser_Lt430($this);
        }
        $nullhandler = new XML_HTMLSax_NullHandler();
        $this->set_object($nullhandler);
        $this->set_element_handler('DoNothing', 'DoNothing');
        $this->set_data_handler('DoNothing');
        $this->set_pi_handler('DoNothing');
        $this->set_jasp_handler('DoNothing');
        $this->set_escape_handler('DoNothing');
    }

    /**
    * Sets the user defined handler object. Returns a PEAR Error
    * if supplied argument is not an object.
    * @param object handler object containing SAX callback methods
    * @access public
    * @return mixed
    */
    function set_object(&$object) {
        if ( is_object($object) ) {
            $this->state_parser->handler_default =& $object;
            return true;
        } else {
            return $this->raiseError('XML_HTMLSax::set_object requires '.
                'an object instance');
        }
    }

    /**
    * Sets a parser option. Returns a PEAR Error if option is invalid<br />
    * <b>Available options:</b>
    * <ul>
    * <li>XML_OPTION_TRIM_DATA_NODES: trim whitespace off the beginning
    * and end of data passed to the data handler</li>
    * <li>XML_OPTION_LINEFEED_BREAK: linefeeds result in additional data
    * handler calls</li>
    * <li>XML_OPTION_TAB_BREAK: tabs result in additional data handler
    * calls</li>
    * <li>XML_OPTION_ENTIES_UNPARSED: XML entities are returned as
    * seperate data handler calls in unparsed form</li>
    * <li>XML_OPTION_ENTIES_PARSED: (PHP 4.3.0+ only) XML entities are
    * returned as seperate data handler calls and are parsed with 
    * PHP's html_entity_decode() function</li>
    * </ul>
    * @param string name of parser option
    * @param int (optional) 1 to switch on, 0 for off
    * @access public
    * @return boolean
    */
    function set_option($name, $value=1) {
        if ( array_key_exists($name,$this->state_parser->parser_options) ) {
            $this->state_parser->parser_options[$name] = $value;
            return true;
        } else {
            return $this->raiseError('XML_HTMLSax::set_option('.$name.') illegal');
        }
    }

    /**
    * Sets the data handler method which deals with the contents of XML
    * elements.<br />
    * The handler method must accept two arguments, the first being an
    * instance of XML_HTMLSax and the second being the contents of an
    * XML element e.g.
    * <pre>
    * function myDataHander(& $parser,$data){}
    * </pre>
    * @param string name of method
    * @access public
    * @return void
    * @see set_object
    */
    function set_data_handler($data_method) {
        $this->state_parser->handler_object_data =& $this->state_parser->handler_default;
        $this->state_parser->handler_method_data = $data_method;
    }

    /**
    * Sets the open and close tag handlers
    * <br />The open handler method must accept three arguments; the parser,
    * the tag name and an array of attributes e.g.
    * <pre>
    * function myOpenHander(& $parser,$tagname,$attrs=array()){}
    * </pre>
    * The close handler method must accept two arguments; the parser and
    * the tag name e.g.
    * <pre>
    * function myCloseHander(& $parser,$tagname){}
    * </pre>
    * @param string name of open method
    * @param string name of close method
    * @access public
    * @return void
    * @see set_object
    */
    function set_element_handler($opening_method, $closing_method) {
        $this->state_parser->handler_object_element =& $this->state_parser->handler_default;
        $this->state_parser->handler_method_opening = $opening_method;
        $this->state_parser->handler_method_closing = $closing_method;
    }

    /**
    * Sets the processing instruction handler method e.g. for PHP open
    * and close tags<br />
    * The handler method must accept three arguments; the parser, the
    * PI target and data inside the PI
    * <pre>
    * function myPIHander(& $parser,$target, $data){}
    * </pre>
    * @param string name of method
    * @access public
    * @return void
    * @see set_object
    */
    function set_pi_handler($pi_method) {
        $this->state_parser->handler_object_pi =& $this->state_parser->handler_default;
        $this->state_parser->handler_method_pi = $pi_method;
    }

    /**
    * Sets the XML escape handler method e.g. for comments and doctype
    * declarations<br />
    * The handler method must accept two arguments; the parser and the
    * contents of the escaped section
    * <pre>
    * function myEscapeHander(& $parser, $data){}
    * </pre>
    * @param string name of method
    * @access public
    * @return void
    * @see set_object
    */
    function set_escape_handler($escape_method) {
        $this->state_parser->handler_object_escape =& $this->state_parser->handler_default;
        $this->state_parser->handler_method_escape = $escape_method;
    }

    /**
    * Sets the JSP/ASP markup handler<br />
    * The handler method must accept two arguments; the parser and
    * body of the JASP tag
    * <pre>
    * function myJaspHander(& $parser, $data){}
    * </pre>
    * @param string name of method
    * @access public
    * @return void
    * @see set_object
    */
    function set_jasp_handler ($jasp_method) {
        $this->state_parser->handler_object_jasp =& $this->state_parser->handler_default;
        $this->state_parser->handler_method_jasp = $jasp_method;
    }

    /**
    * Returns the current string position of the "cursor" inside the XML
    * document
    * <br />Intended for use from within a user defined handler called
    * via the $parser reference e.g.
    * <pre>
    * function myDataHandler(& $parser,$data) {
    *     echo( 'Current position: '.$parser->get_current_position() );
    * }
    * </pre>
    * @access public
    * @return int
    * @see get_length
    */
    function get_current_position() {
        return $this->state_parser->position;
    }

    /**
    * Returns the string length of the XML document being parsed
    * @access public
    * @return int
    */
    function get_length() {
        return $this->state_parser->length;
    }

    /**
    * Start parsing some XML
    * @param string XML document
    * @access public
    * @return void
    */
    function parse($data) {
        $this->state_parser->parse($data);
    }
}
?>