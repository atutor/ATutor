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
* Decorators for dealing with parser options
* @package XML_HTMLSax
* @version $Id$
* @see XML_HTMLSax::set_option
*/
/**
* Trims the contents of element data from whitespace at start and end
* @package XML_HTMLSax
* @access protected
*/
class XML_HTMLSax_Trim {
    /**
    * Original handler object
    * @var object
    * @access private
    */
    var $orig_obj;
    /**
    * Original handler method
    * @var string
    * @access private
    */
    var $orig_method;
    /**
    * Constructs XML_HTMLSax_Trim
    * @param object handler object being decorated
    * @param string original handler method
    * @access protected
    */
    function XML_HTMLSax_Trim(&$orig_obj, $orig_method) {
        $this->orig_obj =& $orig_obj;
        $this->orig_method = $orig_method;
    }
    /**
    * Trims the data
    * @param XML_HTMLSax
    * @param string element data
    * @access protected
    */
    function trimData(&$parser, $data) {
        $data = trim($data);
        if ($data != '') {
            $this->orig_obj->{$this->orig_method}($parser, $data);
        }
    }
}
/**
* Coverts tag names to upper case
* @package XML_HTMLSax
* @access protected
*/
class XML_HTMLSax_CaseFolding {
    /**
    * Original handler object
    * @var object
    * @access private
    */
    var $orig_obj;
    /**
    * Original open handler method
    * @var string
    * @access private
    */
    var $orig_open_method;
    /**
    * Original close handler method
    * @var string
    * @access private
    */
    var $orig_close_method;
    /**
    * Constructs XML_HTMLSax_CaseFolding
    * @param object handler object being decorated
    * @param string original open handler method
    * @param string original close handler method
    * @access protected
    */
    function XML_HTMLSax_CaseFolding(&$orig_obj, $orig_open_method, $orig_close_method) {
        $this->orig_obj =& $orig_obj;
        $this->orig_open_method = $orig_open_method;
        $this->orig_close_method = $orig_close_method;
    }
    /**
    * Folds up open tag callbacks
    * @param XML_HTMLSax
    * @param string tag name
    * @param array tag attributes
    * @access protected
    */
    function foldOpen(&$parser, $tag, $attrs=array(), $empty = FALSE) {
        $this->orig_obj->{$this->orig_open_method}($parser, strtoupper($tag), $attrs, $empty);
    }
    /**
    * Folds up close tag callbacks
    * @param XML_HTMLSax
    * @param string tag name
    * @access protected
    */
    function foldClose(&$parser, $tag, $empty = FALSE) {
        $this->orig_obj->{$this->orig_close_method}($parser, strtoupper($tag), $empty);
    }
}
/**
* Breaks up data by linefeed characters, resulting in additional
* calls to the data handler
* @package XML_HTMLSax
* @access protected
*/
class XML_HTMLSax_Linefeed {
    /**
    * Original handler object
    * @var object
    * @access private
    */
    var $orig_obj;
    /**
    * Original handler method
    * @var string
    * @access private
    */
    var $orig_method;
    /**
    * Constructs XML_HTMLSax_LineFeed
    * @param object handler object being decorated
    * @param string original handler method
    * @access protected
    */
    function XML_HTMLSax_LineFeed(&$orig_obj, $orig_method) {
        $this->orig_obj =& $orig_obj;
        $this->orig_method = $orig_method;
    }
    /**
    * Breaks the data up by linefeeds
    * @param XML_HTMLSax
    * @param string element data
    * @access protected
    */
    function breakData(&$parser, $data) {
        $data = explode("\n",$data);
        foreach ( $data as $chunk ) {
            $this->orig_obj->{$this->orig_method}($parser, $chunk);
        }
    }
}
/**
* Breaks up data by tab characters, resulting in additional
* calls to the data handler
* @package XML_HTMLSax
* @access protected
*/
class XML_HTMLSax_Tab {
    /**
    * Original handler object
    * @var object
    * @access private
    */
    var $orig_obj;
    /**
    * Original handler method
    * @var string
    * @access private
    */
    var $orig_method;
    /**
    * Constructs XML_HTMLSax_Tab
    * @param object handler object being decorated
    * @param string original handler method
    * @access protected
    */
    function XML_HTMLSax_Tab(&$orig_obj, $orig_method) {
        $this->orig_obj =& $orig_obj;
        $this->orig_method = $orig_method;
    }
    /**
    * Breaks the data up by linefeeds
    * @param XML_HTMLSax
    * @param string element data
    * @access protected
    */
    function breakData(&$parser, $data) {
        $data = explode("\t",$data);
        foreach ( $data as $chunk ) {
            $this->orig_obj->{$this->orig_method}($this, $chunk);
        }
    }
}
/**
* Breaks up data by XML entities and parses them with html_entity_decode(),
* resulting in additional calls to the data handler<br />
* Requires PHP 4.3.0+
* @package XML_HTMLSax
* @access protected
*/
class XML_HTMLSax_Entities_Parsed {
    /**
    * Original handler object
    * @var object
    * @access private
    */
    var $orig_obj;
    /**
    * Original handler method
    * @var string
    * @access private
    */
    var $orig_method;
    /**
    * Constructs XML_HTMLSax_Entities_Parsed
    * @param object handler object being decorated
    * @param string original handler method
    * @access protected
    */
    function XML_HTMLSax_Entities_Parsed(&$orig_obj, $orig_method) {
        $this->orig_obj =& $orig_obj;
        $this->orig_method = $orig_method;
    }
    /**
    * Breaks the data up by XML entities
    * @param XML_HTMLSax
    * @param string element data
    * @access protected
    */
    function breakData(&$parser, $data) {
        $data = preg_split('/(&.+?;)/',$data,-1,PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);
        foreach ( $data as $chunk ) {
            $chunk = html_entity_decode($chunk,ENT_NOQUOTES);
            $this->orig_obj->{$this->orig_method}($this, $chunk);
        }
    }
}
/**
* Compatibility with older PHP versions
*/
if (version_compare(phpversion(), '4.3', '<') && !function_exists('html_entity_decode') ) {
    function html_entity_decode($str, $style=ENT_NOQUOTES) {
        return strtr($str,
            array_flip(get_html_translation_table(HTML_ENTITIES,$style)));
    }
}
/**
* Breaks up data by XML entities but leaves them unparsed,
* resulting in additional calls to the data handler<br />
* @package XML_HTMLSax
* @access protected
*/
class XML_HTMLSax_Entities_Unparsed {
    /**
    * Original handler object
    * @var object
    * @access private
    */
    var $orig_obj;
    /**
    * Original handler method
    * @var string
    * @access private
    */
    var $orig_method;
    /**
    * Constructs XML_HTMLSax_Entities_Unparsed
    * @param object handler object being decorated
    * @param string original handler method
    * @access protected
    */
    function XML_HTMLSax_Entities_Unparsed(&$orig_obj, $orig_method) {
        $this->orig_obj =& $orig_obj;
        $this->orig_method = $orig_method;
    }
    /**
    * Breaks the data up by XML entities
    * @param XML_HTMLSax
    * @param string element data
    * @access protected
    */
    function breakData(&$parser, $data) {
        $data = preg_split('/(&.+?;)/',$data,-1,PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);
        foreach ( $data as $chunk ) {
            $this->orig_obj->{$this->orig_method}($this, $chunk);
        }
    }
}
?>