/*****************************************************************

	TAEditor javascript class by Peter Bailey - Copyright (c) 2003
	Contact: me@peterbailey.net
	Website: http://www.peterbailey.net/site/dev/jsclasses/

	Main Features:
	-	Easy and powerful API for creating editing buttons
	-	Supports insert-at-cursor and selection wrapping (where 
		compatible)
	-	Degrades in most browsers that don't suppport advanced 

	Compatibility:
	-	IE5 and IE6 Win/PC- Ok
	-	IE other plaforms - no data
	-	Mozilla/Gecko, any version - Ok
	-	Safari - Ok but some bugs
	-	Opera 5,6 - degrades (editing buttons aren't added)
	-	Opera 7 - Works, but doesn't support insert-at-cursor or
		selection wrapping, only appending.
	-	All others - not tested or no data
	
	Note: This document was created with a tab-spacing of four (4)

******************************************************************/

// Constructor
function TAEditor( f, taName, btnContainerID, lDelimit, rDelimit )
{
	if ( typeof document.createElement == 'undefined' ) return;
	var dt          = this;
	this.form       = f;
	this.elem       = f.elements[taName];
	this.buttonElem = document.getElementById( btnContainerID );
	this.tagStack   = new Array();
	this.delimiters = { 'left': lDelimit, 'right': rDelimit };

	this.elem.onclick  = function() { dt.saveCaret() }
	this.elem.onkeyup  = function() { dt.saveCaret() }
	this.elem.onselect = function() { dt.saveCaret() }

	// Thanks to jkd for the element prototypes
	if ( typeof window.HTMLTextAreaElement != 'undefined' )
	{
		HTMLTextAreaElement.prototype.surroundSelection = function( left, right )
		{
			this.value = this.value.substring(0, this.selectionStart) + left + this.value.substring(this.selectionStart, this.selectionEnd) + right + this.value.substring(this.selectionEnd);
		}
		HTMLTextAreaElement.prototype.insertAtSelection = function( text )
		{
			this.value = this.value.substring(0, this.selectionStart) + text + this.value.substring(this.selectionStart);
		}
	}
	else
	{
		this.elem.surroundSelection = function( left, right )
		{
			var range = document.selection.createRange();
			if ( range.parentElement() != this ) return;
			range.text = left + range.text + right;
		}
		this.elem.insertAtSelection = function( txt )
		{
			if ( typeof this.caretPos == 'undefined' ) // Opera 7 ??
				this.value += txt;
			else
				this.caretPos.text = this.caretPos.text.charAt(this.caretPos.text.length - 1) == ' ' ? txt + ' ' : txt;
		}
	}
	
}

// Creates input type=button elements
TAEditor.prototype.createButton = function( label, tag, inline )
{
	var b = this.createHTMLElement( 'input', { 'type': 'button', 'value': ' ' + label + ' ', 'class': 'button sspace ' + tag } );
	b.tag     = tag;
	b.wType   = 'button';
	if ( typeof inline != 'undefined' ) b.inline = Boolean( inline );
	else b.pushed = false;
	return this.buttonElem.appendChild( b );
}

// For buttons without any extra user-entered data
TAEditor.prototype.basicButton = function( label, tag, inline )
{
	var self = this;
	var b = this.createButton( label, tag, inline );
	b.onclick = function() { self.clickHandler( this ) }
}

// For icons without any extra user-entered data
TAEditor.prototype.basicIcon = function( src, label, tag, inline )
{
	var self = this;

	var b = this.createHTMLElement( 'img', { 'src': src, 'alt': ' ' + label + ' ', 'tabindex': '-1', 'align': 'middle' } );
	b.tag     = tag;
	b.wType   = 'img';
	if ( typeof inline != 'undefined' ) b.inline = Boolean( inline );
	else b.pushed = false;
	b = this.buttonElem.appendChild( b );

	b.onclick = function() { self.smile( this ) }
}

TAEditor.prototype.smile = function( btn ) {
	if ( btn.inline ) {
		this.insert( btn.tag );
	} else {
		this.insert( btn.tag );
	}
	var ta = this.elem;
	ta.focus();
}

// For buttons with extra user-entered data
TAEditor.prototype.complexButton = function( label, tag, inline, propGetter, obj )
{
	var self = this;
	var b     = this.createButton( label, tag, inline );
	b.func    = ( propGetter ) ? propGetter : null;
	b.obj     = ( obj ) ? obj : window 
	b.onclick = function() { self.clickHandler( this ) }
}

// Create SELECT widgets
TAEditor.prototype.selector = function( tag, optionData )
{
	var self = this;
	var s = this.createHTMLElement( 'select' );
	for ( var i = 0; ( opt = optionData[i] ); i++ )
	{
		s.appendChild( this.createHTMLElement( 'option', { 'text': opt[0] , 'value': opt[1] } ) );
	}
	s.onchange = function() { self.changeHandler( this ) }
	s.tag      = tag;
	s.wType    = 'select';
	s.obj      = this;
	s.func     = 'selectGetter';
	this.buttonElem.appendChild( s );
	
	var b = this.createHTMLElement( 'input', { 'type': 'button', 'value': 'X', 'disabled': 'true', 'title': 'Click to close tag', 'class': 'submit' } );
	b.onclick = function() { self.changeHandler( s ) };
	s.closer = this.buttonElem.appendChild( b );
}

// Directs insertion to proper method
TAEditor.prototype.clickHandler = function( btn )
{
	if ( btn.inline )
		this.insert( btn.tag.toTag( this, 0 ) );
	else
		this.insertWrapper( btn );
}

TAEditor.prototype.changeHandler = function( s )
{
	this.insertWrapper( s );
}

TAEditor.prototype.selectGetter = function( s )
{	
	
	var value = '=' + s.options[s.selectedIndex].value;
	s.selectedIndex = 0;

	return this.createTagMaker( null, value );
}

// For inserting wrapping tags (that have a open and closing tag)
TAEditor.prototype.insertWrapper = function( widget, block, data )
{
	var tag = widget.tag;
	var ta = this.elem;
	var openTag = tag;

	if ( widget.func != null && !widget.pushed )
	{
		if ( !data ) data = widget.obj[widget.func]( widget );
		if ( data == null ) return;
		if ( data.attr == null ) data.attr = '';
		if ( data.text == null ) data.text = '';
		openTag += data.attr;
	}

	if ( ta.surroundSelection && this.wrapped() )
	{
		ta.surroundSelection( openTag.toTag( this, 0 ), tag.toTag( this, 1 ) )
	}
	else
	{
		if ( widget.pushed )
		{
			if ( this.closeTag( tag ) )
			{
				this.updateWidget( widget, 'close' );
				this.insert( ( ( block ) ? "\n" : "" ) + tag.toTag( this, 1 ) );
				ta.focus();				
			}
			else
				alert( 'Closing this tag now will cause improper nesting!\nPlease close the '  + this.tagStack.last().toTag(this,0) + ' tag first' );
		}
		else
		{	
			if ( widget.func && widget.func != 'selectGetter' )
			{
				this.insert( openTag.toTag( this, 0 ) + data.text + tag.toTag( this, 1 ) );
			}
			else
			{
				this.updateWidget( widget, 'open' );
				this.insert( openTag.toTag( this, 0 ) + ( ( block ) ? "\n" : "" ) );
				this.tagStack.push( tag );				
			}				
		}
		ta.focus();
	}	
}

// Update UI widgets when tag has been inserted
TAEditor.prototype.updateWidget = function( w, action )
{
	if ( action == 'open' )
	{
		w.pushed                  = true;
		switch( w.wType )
		{
			case 'button':
				w.value          += "*";
				break;
			case 'select':
				w.pushed          = true;
				w.closer.disabled = false;
				w.selectedIndex   = 0;
				break;
		}
	}
	else
	{
		w.pushed                  = false;
		switch( w.wType )
		{
			case 'button':
				w.value           = w.value.trim( 1 );
				break;
			case 'select':				
				w.closer.disabled = true;
				break;
		}
	}	
}

TAEditor.prototype.getCaretPos = function () {
	var ta = this.elem;
	return ta.caretPos;
}

// Returns TRUE if there is a selection for wrapping
TAEditor.prototype.wrapped = function()
{
	var ta = this.elem
	if ( typeof ta.selectionStart != 'undefined' ) 
		return ( ta.selectionStart != ta.selectionEnd )
	if ( typeof ta.caretPos != 'undefined' )
		return ( ta.caretPos.text != '' );
	return false;
}

// Handles text-insertions into textarea
TAEditor.prototype.insert = function( txt )
{
	if ( this.elem.insertAtSelection )
	{
		this.elem.insertAtSelection( txt )
	}
	else
	{
		this.elem.value  += txt;
	}
}

// Removes full wrapped tags from the stack
TAEditor.prototype.closeTag = function( tag )
{
	if ( this.tagStack[this.tagStack.length - 1] == tag )
	{
		this.tagStack.pop();
		return true;
	}
	return false;
}

// Saves caret position (empty TextRange) for IE
TAEditor.prototype.saveCaret = function()
{
	var ta = this.elem;
	if ( ta.createTextRange )
		ta.caretPos = document.selection.createRange().duplicate();
}



// Returns a TagMaker object
TAEditor.prototype.createTagMaker = function( t, a )
{
	if ( t == null && a == null ) return null;
	return ( t || a ) ? { 'text': t, 'attr': a } : null;
}

// Appends HTML elements to button area
TAEditor.prototype.addHTMLElement = function( elemName, attribs )
{
	this.buttonElem.appendChild( this.createHTMLElement( elemName, attribs ) );
}

// Creates HTML elements
TAEditor.prototype.createHTMLElement = function( elemName, attribs )
{
	var elem = document.createElement( elemName );
	if ( typeof attribs != 'undefined' )
	{
		for ( var i in attribs )
		{
			switch ( true )
			{
				case ( i == 'text' )  : elem.appendChild( document.createTextNode( attribs[i] ) ); break;
				case ( i == 'class' ) : elem.className = attribs[i]; break;
				default : elem.setAttribute( i, '' ); elem[i] = attribs[i];
			}
		}
	}
	return elem;	
}

/* Non-Class methods used as assistants. */

// Converts string into tag format using the class delimiters
String.prototype.toTag = function( dt, code )
{
	var d = dt.delimiters;
	switch( code )
	{
		case 0 : return d.left + this + d.right;
		case 1 : return d.left + "/" + this + d.right;
		case 2 : return d.left + this + "/" + d.right;
		case 3 : return d.left + this + attribs + d.right;
	}
}

// Trims characters from end of string.
String.prototype.trim = function( count )
{
	return this.substring( 0, this.length - count );
}

// Prototypes push() for all browsers
Array.prototype.push = function( val )
{
	this[this.length] = val;
}

// Prototypes pop() for all browsers
Array.prototype.pop = function()
{
	var end = this.length - 1;
	var r   = this[end]
	delete this[end];
	this.length = end;
	return r;
}

// Returns last element of array
Array.prototype.last = function()
{
	return this[this.length - 1];
}
// EOF