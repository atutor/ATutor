(function(){
/*
 * jQuery 1.2.3 - New Wave Javascript
 *
 * Copyright (c) 2008 John Resig (jquery.com)
 * Dual licensed under the MIT (MIT-LICENSE.txt)
 * and GPL (GPL-LICENSE.txt) licenses.
 *
 * $Date: 2008-02-06 00:21:25 -0500 (Wed, 06 Feb 2008) $
 * $Rev: 4663 $
 */

// Map over jQuery in case of overwrite
if ( window.jQuery )
	var _jQuery = window.jQuery;

var jQuery = window.jQuery = function( selector, context ) {
	// The jQuery object is actually just the init constructor 'enhanced'
	return new jQuery.prototype.init( selector, context );
};

// Map over the $ in case of overwrite
if ( window.$ )
	var _$ = window.$;
	
// Map the jQuery namespace to the '$' one
window.$ = jQuery;

// A simple way to check for HTML strings or ID strings
// (both of which we optimize for)
var quickExpr = /^[^<]*(<(.|\s)+>)[^>]*$|^#(\w+)$/;

// Is it a simple selector
var isSimple = /^.[^:#\[\.]*$/;

jQuery.fn = jQuery.prototype = {
	init: function( selector, context ) {
		// Make sure that a selection was provided
		selector = selector || document;

		// Handle $(DOMElement)
		if ( selector.nodeType ) {
			this[0] = selector;
			this.length = 1;
			return this;

		// Handle HTML strings
		} else if ( typeof selector == "string" ) {
			// Are we dealing with HTML string or an ID?
			var match = quickExpr.exec( selector );

			// Verify a match, and that no context was specified for #id
			if ( match && (match[1] || !context) ) {

				// HANDLE: $(html) -> $(array)
				if ( match[1] )
					selector = jQuery.clean( [ match[1] ], context );

				// HANDLE: $("#id")
				else {
					var elem = document.getElementById( match[3] );

					// Make sure an element was located
					if ( elem )
						// Handle the case where IE and Opera return items
						// by name instead of ID
						if ( elem.id != match[3] )
							return jQuery().find( selector );

						// Otherwise, we inject the element directly into the jQuery object
						else {
							this[0] = elem;
							this.length = 1;
							return this;
						}

					else
						selector = [];
				}

			// HANDLE: $(expr, [context])
			// (which is just equivalent to: $(content).find(expr)
			} else
				return new jQuery( context ).find( selector );

		// HANDLE: $(function)
		// Shortcut for document ready
		} else if ( jQuery.isFunction( selector ) )
			return new jQuery( document )[ jQuery.fn.ready ? "ready" : "load" ]( selector );

		return this.setArray(
			// HANDLE: $(array)
			selector.constructor == Array && selector ||

			// HANDLE: $(arraylike)
			// Watch for when an array-like object, contains DOM nodes, is passed in as the selector
			(selector.jquery || selector.length && selector != window && !selector.nodeType && selector[0] != undefined && selector[0].nodeType) && jQuery.makeArray( selector ) ||

			// HANDLE: $(*)
			[ selector ] );
	},
	
	// The current version of jQuery being used
	jquery: "1.2.3",

	// The number of elements contained in the matched element set
	size: function() {
		return this.length;
	},
	
	// The number of elements contained in the matched element set
	length: 0,

	// Get the Nth element in the matched element set OR
	// Get the whole matched element set as a clean array
	get: function( num ) {
		return num == undefined ?

			// Return a 'clean' array
			jQuery.makeArray( this ) :

			// Return just the object
			this[ num ];
	},
	
	// Take an array of elements and push it onto the stack
	// (returning the new matched element set)
	pushStack: function( elems ) {
		// Build a new jQuery matched element set
		var ret = jQuery( elems );

		// Add the old object onto the stack (as a reference)
		ret.prevObject = this;

		// Return the newly-formed element set
		return ret;
	},
	
	// Force the current matched set of elements to become
	// the specified array of elements (destroying the stack in the process)
	// You should use pushStack() in order to do this, but maintain the stack
	setArray: function( elems ) {
		// Resetting the length to 0, then using the native Array push
		// is a super-fast way to populate an object with array-like properties
		this.length = 0;
		Array.prototype.push.apply( this, elems );
		
		return this;
	},

	// Execute a callback for every element in the matched set.
	// (You can seed the arguments with an array of args, but this is
	// only used internally.)
	each: function( callback, args ) {
		return jQuery.each( this, callback, args );
	},

	// Determine the position of an element within 
	// the matched set of elements
	index: function( elem ) {
		var ret = -1;

		// Locate the position of the desired element
		this.each(function(i){
			if ( this == elem )
				ret = i;
		});

		return ret;
	},

	attr: function( name, value, type ) {
		var options = name;
		
		// Look for the case where we're accessing a style value
		if ( name.constructor == String )
			if ( value == undefined )
				return this.length && jQuery[ type || "attr" ]( this[0], name ) || undefined;

			else {
				options = {};
				options[ name ] = value;
			}
		
		// Check to see if we're setting style values
		return this.each(function(i){
			// Set all the styles
			for ( name in options )
				jQuery.attr(
					type ?
						this.style :
						this,
					name, jQuery.prop( this, options[ name ], type, i, name )
				);
		});
	},

	css: function( key, value ) {
		// ignore negative width and height values
		if ( (key == 'width' || key == 'height') && parseFloat(value) < 0 )
			value = undefined;
		return this.attr( key, value, "curCSS" );
	},

	text: function( text ) {
		if ( typeof text != "object" && text != null )
			return this.empty().append( (this[0] && this[0].ownerDocument || document).createTextNode( text ) );

		var ret = "";

		jQuery.each( text || this, function(){
			jQuery.each( this.childNodes, function(){
				if ( this.nodeType != 8 )
					ret += this.nodeType != 1 ?
						this.nodeValue :
						jQuery.fn.text( [ this ] );
			});
		});

		return ret;
	},

	wrapAll: function( html ) {
		if ( this[0] )
			// The elements to wrap the target around
			jQuery( html, this[0].ownerDocument )
				.clone()
				.insertBefore( this[0] )
				.map(function(){
					var elem = this;

					while ( elem.firstChild )
						elem = elem.firstChild;

					return elem;
				})
				.append(this);

		return this;
	},

	wrapInner: function( html ) {
		return this.each(function(){
			jQuery( this ).contents().wrapAll( html );
		});
	},

	wrap: function( html ) {
		return this.each(function(){
			jQuery( this ).wrapAll( html );
		});
	},

	append: function() {
		return this.domManip(arguments, true, false, function(elem){
			if (this.nodeType == 1)
				this.appendChild( elem );
		});
	},

	prepend: function() {
		return this.domManip(arguments, true, true, function(elem){
			if (this.nodeType == 1)
				this.insertBefore( elem, this.firstChild );
		});
	},
	
	before: function() {
		return this.domManip(arguments, false, false, function(elem){
			this.parentNode.insertBefore( elem, this );
		});
	},

	after: function() {
		return this.domManip(arguments, false, true, function(elem){
			this.parentNode.insertBefore( elem, this.nextSibling );
		});
	},

	end: function() {
		return this.prevObject || jQuery( [] );
	},

	find: function( selector ) {
		var elems = jQuery.map(this, function(elem){
			return jQuery.find( selector, elem );
		});

		return this.pushStack( /[^+>] [^+>]/.test( selector ) || selector.indexOf("..") > -1 ?
			jQuery.unique( elems ) :
			elems );
	},

	clone: function( events ) {
		// Do the clone
		var ret = this.map(function(){
			if ( jQuery.browser.msie && !jQuery.isXMLDoc(this) ) {
				// IE copies events bound via attachEvent when
				// using cloneNode. Calling detachEvent on the
				// clone will also remove the events from the orignal
				// In order to get around this, we use innerHTML.
				// Unfortunately, this means some modifications to 
				// attributes in IE that are actually only stored 
				// as properties will not be copied (such as the
				// the name attribute on an input).
				var clone = this.cloneNode(true),
					container = document.createElement("div");
				container.appendChild(clone);
				return jQuery.clean([container.innerHTML])[0];
			} else
				return this.cloneNode(true);
		});

		// Need to set the expando to null on the cloned set if it exists
		// removeData doesn't work here, IE removes it from the original as well
		// this is primarily for IE but the data expando shouldn't be copied over in any browser
		var clone = ret.find("*").andSelf().each(function(){
			if ( this[ expando ] != undefined )
				this[ expando ] = null;
		});
		
		// Copy the events from the original to the clone
		if ( events === true )
			this.find("*").andSelf().each(function(i){
				if (this.nodeType == 3)
					return;
				var events = jQuery.data( this, "events" );

				for ( var type in events )
					for ( var handler in events[ type ] )
						jQuery.event.add( clone[ i ], type, events[ type ][ handler ], events[ type ][ handler ].data );
			});

		// Return the cloned set
		return ret;
	},

	filter: function( selector ) {
		return this.pushStack(
			jQuery.isFunction( selector ) &&
			jQuery.grep(this, function(elem, i){
				return selector.call( elem, i );
			}) ||

			jQuery.multiFilter( selector, this ) );
	},

	not: function( selector ) {
		if ( selector.constructor == String )
			// test special case where just one selector is passed in
			if ( isSimple.test( selector ) )
				return this.pushStack( jQuery.multiFilter( selector, this, true ) );
			else
				selector = jQuery.multiFilter( selector, this );

		var isArrayLike = selector.length && selector[selector.length - 1] !== undefined && !selector.nodeType;
		return this.filter(function() {
			return isArrayLike ? jQuery.inArray( this, selector ) < 0 : this != selector;
		});
	},

	add: function( selector ) {
		return !selector ? this : this.pushStack( jQuery.merge( 
			this.get(),
			selector.constructor == String ? 
				jQuery( selector ).get() :
				selector.length != undefined && (!selector.nodeName || jQuery.nodeName(selector, "form")) ?
					selector : [selector] ) );
	},

	is: function( selector ) {
		return selector ?
			jQuery.multiFilter( selector, this ).length > 0 :
			false;
	},

	hasClass: function( selector ) {
		return this.is( "." + selector );
	},
	
	val: function( value ) {
		if ( value == undefined ) {

			if ( this.length ) {
				var elem = this[0];

				// We need to handle select boxes special
				if ( jQuery.nodeName( elem, "select" ) ) {
					var index = elem.selectedIndex,
						values = [],
						options = elem.options,
						one = elem.type == "select-one";
					
					// Nothing was selected
					if ( index < 0 )
						return null;

					// Loop through all the selected options
					for ( var i = one ? index : 0, max = one ? index + 1 : options.length; i < max; i++ ) {
						var option = options[ i ];

						if ( option.selected ) {
							// Get the specifc value for the option
							value = jQuery.browser.msie && !option.attributes.value.specified ? option.text : option.value;
							
							// We don't need an array for one selects
							if ( one )
								return value;
							
							// Multi-Selects return an array
							values.push( value );
						}
					}
					
					return values;
					
				// Everything else, we just grab the value
				} else
					return (this[0].value || "").replace(/\r/g, "");

			}

			return undefined;
		}

		return this.each(function(){
			if ( this.nodeType != 1 )
				return;

			if ( value.constructor == Array && /radio|checkbox/.test( this.type ) )
				this.checked = (jQuery.inArray(this.value, value) >= 0 ||
					jQuery.inArray(this.name, value) >= 0);

			else if ( jQuery.nodeName( this, "select" ) ) {
				var values = value.constructor == Array ?
					value :
					[ value ];

				jQuery( "option", this ).each(function(){
					this.selected = (jQuery.inArray( this.value, values ) >= 0 ||
						jQuery.inArray( this.text, values ) >= 0);
				});

				if ( !values.length )
					this.selectedIndex = -1;

			} else
				this.value = value;
		});
	},
	
	html: function( value ) {
		return value == undefined ?
			(this.length ?
				this[0].innerHTML :
				null) :
			this.empty().append( value );
	},

	replaceWith: function( value ) {
		return this.after( value ).remove();
	},

	eq: function( i ) {
		return this.slice( i, i + 1 );
	},

	slice: function() {
		return this.pushStack( Array.prototype.slice.apply( this, arguments ) );
	},

	map: function( callback ) {
		return this.pushStack( jQuery.map(this, function(elem, i){
			return callback.call( elem, i, elem );
		}));
	},

	andSelf: function() {
		return this.add( this.prevObject );
	},

	data: function( key, value ){
		var parts = key.split(".");
		parts[1] = parts[1] ? "." + parts[1] : "";

		if ( value == null ) {
			var data = this.triggerHandler("getData" + parts[1] + "!", [parts[0]]);
			
			if ( data == undefined && this.length )
				data = jQuery.data( this[0], key );

			return data == null && parts[1] ?
				this.data( parts[0] ) :
				data;
		} else
			return this.trigger("setData" + parts[1] + "!", [parts[0], value]).each(function(){
				jQuery.data( this, key, value );
			});
	},

	removeData: function( key ){
		return this.each(function(){
			jQuery.removeData( this, key );
		});
	},
	
	domManip: function( args, table, reverse, callback ) {
		var clone = this.length > 1, elems; 

		return this.each(function(){
			if ( !elems ) {
				elems = jQuery.clean( args, this.ownerDocument );

				if ( reverse )
					elems.reverse();
			}

			var obj = this;

			if ( table && jQuery.nodeName( this, "table" ) && jQuery.nodeName( elems[0], "tr" ) )
				obj = this.getElementsByTagName("tbody")[0] || this.appendChild( this.ownerDocument.createElement("tbody") );

			var scripts = jQuery( [] );

			jQuery.each(elems, function(){
				var elem = clone ?
					jQuery( this ).clone( true )[0] :
					this;

				// execute all scripts after the elements have been injected
				if ( jQuery.nodeName( elem, "script" ) ) {
					scripts = scripts.add( elem );
				} else {
					// Remove any inner scripts for later evaluation
					if ( elem.nodeType == 1 )
						scripts = scripts.add( jQuery( "script", elem ).remove() );

					// Inject the elements into the document
					callback.call( obj, elem );
				}
			});

			scripts.each( evalScript );
		});
	}
};

// Give the init function the jQuery prototype for later instantiation
jQuery.prototype.init.prototype = jQuery.prototype;

function evalScript( i, elem ) {
	if ( elem.src )
		jQuery.ajax({
			url: elem.src,
			async: false,
			dataType: "script"
		});

	else
		jQuery.globalEval( elem.text || elem.textContent || elem.innerHTML || "" );

	if ( elem.parentNode )
		elem.parentNode.removeChild( elem );
}

jQuery.extend = jQuery.fn.extend = function() {
	// copy reference to target object
	var target = arguments[0] || {}, i = 1, length = arguments.length, deep = false, options;

	// Handle a deep copy situation
	if ( target.constructor == Boolean ) {
		deep = target;
		target = arguments[1] || {};
		// skip the boolean and the target
		i = 2;
	}

	// Handle case when target is a string or something (possible in deep copy)
	if ( typeof target != "object" && typeof target != "function" )
		target = {};

	// extend jQuery itself if only one argument is passed
	if ( length == 1 ) {
		target = this;
		i = 0;
	}

	for ( ; i < length; i++ )
		// Only deal with non-null/undefined values
		if ( (options = arguments[ i ]) != null )
			// Extend the base object
			for ( var name in options ) {
				// Prevent never-ending loop
				if ( target === options[ name ] )
					continue;

				// Recurse if we're merging object values
				if ( deep && options[ name ] && typeof options[ name ] == "object" && target[ name ] && !options[ name ].nodeType )
					target[ name ] = jQuery.extend( target[ name ], options[ name ] );

				// Don't bring in undefined values
				else if ( options[ name ] != undefined )
					target[ name ] = options[ name ];

			}

	// Return the modified object
	return target;
};

var expando = "jQuery" + (new Date()).getTime(), uuid = 0, windowData = {};

// exclude the following css properties to add px
var exclude = /z-?index|font-?weight|opacity|zoom|line-?height/i;

jQuery.extend({
	noConflict: function( deep ) {
		window.$ = _$;

		if ( deep )
			window.jQuery = _jQuery;

		return jQuery;
	},

	// See test/unit/core.js for details concerning this function.
	isFunction: function( fn ) {
		return !!fn && typeof fn != "string" && !fn.nodeName && 
			fn.constructor != Array && /function/i.test( fn + "" );
	},
	
	// check if an element is in a (or is an) XML document
	isXMLDoc: function( elem ) {
		return elem.documentElement && !elem.body ||
			elem.tagName && elem.ownerDocument && !elem.ownerDocument.body;
	},

	// Evalulates a script in a global context
	globalEval: function( data ) {
		data = jQuery.trim( data );

		if ( data ) {
			// Inspired by code by Andrea Giammarchi
			// http://webreflection.blogspot.com/2007/08/global-scope-evaluation-and-dom.html
			var head = document.getElementsByTagName("head")[0] || document.documentElement,
				script = document.createElement("script");

			script.type = "text/javascript";
			if ( jQuery.browser.msie )
				script.text = data;
			else
				script.appendChild( document.createTextNode( data ) );

			head.appendChild( script );
			head.removeChild( script );
		}
	},

	nodeName: function( elem, name ) {
		return elem.nodeName && elem.nodeName.toUpperCase() == name.toUpperCase();
	},
	
	cache: {},
	
	data: function( elem, name, data ) {
		elem = elem == window ?
			windowData :
			elem;

		var id = elem[ expando ];

		// Compute a unique ID for the element
		if ( !id ) 
			id = elem[ expando ] = ++uuid;

		// Only generate the data cache if we're
		// trying to access or manipulate it
		if ( name && !jQuery.cache[ id ] )
			jQuery.cache[ id ] = {};
		
		// Prevent overriding the named cache with undefined values
		if ( data != undefined )
			jQuery.cache[ id ][ name ] = data;
		
		// Return the named cache data, or the ID for the element	
		return name ?
			jQuery.cache[ id ][ name ] :
			id;
	},
	
	removeData: function( elem, name ) {
		elem = elem == window ?
			windowData :
			elem;

		var id = elem[ expando ];

		// If we want to remove a specific section of the element's data
		if ( name ) {
			if ( jQuery.cache[ id ] ) {
				// Remove the section of cache data
				delete jQuery.cache[ id ][ name ];

				// If we've removed all the data, remove the element's cache
				name = "";

				for ( name in jQuery.cache[ id ] )
					break;

				if ( !name )
					jQuery.removeData( elem );
			}

		// Otherwise, we want to remove all of the element's data
		} else {
			// Clean up the element expando
			try {
				delete elem[ expando ];
			} catch(e){
				// IE has trouble directly removing the expando
				// but it's ok with using removeAttribute
				if ( elem.removeAttribute )
					elem.removeAttribute( expando );
			}

			// Completely remove the data cache
			delete jQuery.cache[ id ];
		}
	},

	// args is for internal usage only
	each: function( object, callback, args ) {
		if ( args ) {
			if ( object.length == undefined ) {
				for ( var name in object )
					if ( callback.apply( object[ name ], args ) === false )
						break;
			} else
				for ( var i = 0, length = object.length; i < length; i++ )
					if ( callback.apply( object[ i ], args ) === false )
						break;

		// A special, fast, case for the most common use of each
		} else {
			if ( object.length == undefined ) {
				for ( var name in object )
					if ( callback.call( object[ name ], name, object[ name ] ) === false )
						break;
			} else
				for ( var i = 0, length = object.length, value = object[0]; 
					i < length && callback.call( value, i, value ) !== false; value = object[++i] ){}
		}

		return object;
	},
	
	prop: function( elem, value, type, i, name ) {
			// Handle executable functions
			if ( jQuery.isFunction( value ) )
				value = value.call( elem, i );
				
			// Handle passing in a number to a CSS property
			return value && value.constructor == Number && type == "curCSS" && !exclude.test( name ) ?
				value + "px" :
				value;
	},

	className: {
		// internal only, use addClass("class")
		add: function( elem, classNames ) {
			jQuery.each((classNames || "").split(/\s+/), function(i, className){
				if ( elem.nodeType == 1 && !jQuery.className.has( elem.className, className ) )
					elem.className += (elem.className ? " " : "") + className;
			});
		},

		// internal only, use removeClass("class")
		remove: function( elem, classNames ) {
			if (elem.nodeType == 1)
				elem.className = classNames != undefined ?
					jQuery.grep(elem.className.split(/\s+/), function(className){
						return !jQuery.className.has( classNames, className );	
					}).join(" ") :
					"";
		},

		// internal only, use is(".class")
		has: function( elem, className ) {
			return jQuery.inArray( className, (elem.className || elem).toString().split(/\s+/) ) > -1;
		}
	},

	// A method for quickly swapping in/out CSS properties to get correct calculations
	swap: function( elem, options, callback ) {
		var old = {};
		// Remember the old values, and insert the new ones
		for ( var name in options ) {
			old[ name ] = elem.style[ name ];
			elem.style[ name ] = options[ name ];
		}

		callback.call( elem );

		// Revert the old values
		for ( var name in options )
			elem.style[ name ] = old[ name ];
	},

	css: function( elem, name, force ) {
		if ( name == "width" || name == "height" ) {
			var val, props = { position: "absolute", visibility: "hidden", display:"block" }, which = name == "width" ? [ "Left", "Right" ] : [ "Top", "Bottom" ];
		
			function getWH() {
				val = name == "width" ? elem.offsetWidth : elem.offsetHeight;
				var padding = 0, border = 0;
				jQuery.each( which, function() {
					padding += parseFloat(jQuery.curCSS( elem, "padding" + this, true)) || 0;
					border += parseFloat(jQuery.curCSS( elem, "border" + this + "Width", true)) || 0;
				});
				val -= Math.round(padding + border);
			}
		
			if ( jQuery(elem).is(":visible") )
				getWH();
			else
				jQuery.swap( elem, props, getWH );
			
			return Math.max(0, val);
		}
		
		return jQuery.curCSS( elem, name, force );
	},

	curCSS: function( elem, name, force ) {
		var ret;

		// A helper method for determining if an element's values are broken
		function color( elem ) {
			if ( !jQuery.browser.safari )
				return false;

			var ret = document.defaultView.getComputedStyle( elem, null );
			return !ret || ret.getPropertyValue("color") == "";
		}

		// We need to handle opacity special in IE
		if ( name == "opacity" && jQuery.browser.msie ) {
			ret = jQuery.attr( elem.style, "opacity" );

			return ret == "" ?
				"1" :
				ret;
		}
		// Opera sometimes will give the wrong display answer, this fixes it, see #2037
		if ( jQuery.browser.opera && name == "display" ) {
			var save = elem.style.outline;
			elem.style.outline = "0 solid black";
			elem.style.outline = save;
		}
		
		// Make sure we're using the right name for getting the float value
		if ( name.match( /float/i ) )
			name = styleFloat;

		if ( !force && elem.style && elem.style[ name ] )
			ret = elem.style[ name ];

		else if ( document.defaultView && document.defaultView.getComputedStyle ) {

			// Only "float" is needed here
			if ( name.match( /float/i ) )
				name = "float";

			name = name.replace( /([A-Z])/g, "-$1" ).toLowerCase();

			var getComputedStyle = document.defaultView.getComputedStyle( elem, null );

			if ( getComputedStyle && !color( elem ) )
				ret = getComputedStyle.getPropertyValue( name );

			// If the element isn't reporting its values properly in Safari
			// then some display: none elements are involved
			else {
				var swap = [], stack = [];

				// Locate all of the parent display: none elements
				for ( var a = elem; a && color(a); a = a.parentNode )
					stack.unshift(a);

				// Go through and make them visible, but in reverse
				// (It would be better if we knew the exact display type that they had)
				for ( var i = 0; i < stack.length; i++ )
					if ( color( stack[ i ] ) ) {
						swap[ i ] = stack[ i ].style.display;
						stack[ i ].style.display = "block";
					}

				// Since we flip the display style, we have to handle that
				// one special, otherwise get the value
				ret = name == "display" && swap[ stack.length - 1 ] != null ?
					"none" :
					( getComputedStyle && getComputedStyle.getPropertyValue( name ) ) || "";

				// Finally, revert the display styles back
				for ( var i = 0; i < swap.length; i++ )
					if ( swap[ i ] != null )
						stack[ i ].style.display = swap[ i ];
			}

			// We should always get a number back from opacity
			if ( name == "opacity" && ret == "" )
				ret = "1";

		} else if ( elem.currentStyle ) {
			var camelCase = name.replace(/\-(\w)/g, function(all, letter){
				return letter.toUpperCase();
			});

			ret = elem.currentStyle[ name ] || elem.currentStyle[ camelCase ];

			// From the awesome hack by Dean Edwards
			// http://erik.eae.net/archives/2007/07/27/18.54.15/#comment-102291

			// If we're not dealing with a regular pixel number
			// but a number that has a weird ending, we need to convert it to pixels
			if ( !/^\d+(px)?$/i.test( ret ) && /^\d/.test( ret ) ) {
				// Remember the original values
				var style = elem.style.left, runtimeStyle = elem.runtimeStyle.left;

				// Put in the new values to get a computed value out
				elem.runtimeStyle.left = elem.currentStyle.left;
				elem.style.left = ret || 0;
				ret = elem.style.pixelLeft + "px";

				// Revert the changed values
				elem.style.left = style;
				elem.runtimeStyle.left = runtimeStyle;
			}
		}

		return ret;
	},
	
	clean: function( elems, context ) {
		var ret = [];
		context = context || document;
		// !context.createElement fails in IE with an error but returns typeof 'object'
		if (typeof context.createElement == 'undefined') 
			context = context.ownerDocument || context[0] && context[0].ownerDocument || document;

		jQuery.each(elems, function(i, elem){
			if ( !elem )
				return;

			if ( elem.constructor == Number )
				elem = elem.toString();
			
			// Convert html string into DOM nodes
			if ( typeof elem == "string" ) {
				// Fix "XHTML"-style tags in all browsers
				elem = elem.replace(/(<(\w+)[^>]*?)\/>/g, function(all, front, tag){
					return tag.match(/^(abbr|br|col|img|input|link|meta|param|hr|area|embed)$/i) ?
						all :
						front + "></" + tag + ">";
				});

				// Trim whitespace, otherwise indexOf won't work as expected
				var tags = jQuery.trim( elem ).toLowerCase(), div = context.createElement("div");

				var wrap =
					// option or optgroup
					!tags.indexOf("<opt") &&
					[ 1, "<select multiple='multiple'>", "</select>" ] ||
					
					!tags.indexOf("<leg") &&
					[ 1, "<fieldset>", "</fieldset>" ] ||
					
					tags.match(/^<(thead|tbody|tfoot|colg|cap)/) &&
					[ 1, "<table>", "</table>" ] ||
					
					!tags.indexOf("<tr") &&
					[ 2, "<table><tbody>", "</tbody></table>" ] ||
					
				 	// <thead> matched above
					(!tags.indexOf("<td") || !tags.indexOf("<th")) &&
					[ 3, "<table><tbody><tr>", "</tr></tbody></table>" ] ||
					
					!tags.indexOf("<col") &&
					[ 2, "<table><tbody></tbody><colgroup>", "</colgroup></table>" ] ||

					// IE can't serialize <link> and <script> tags normally
					jQuery.browser.msie &&
					[ 1, "div<div>", "</div>" ] ||
					
					[ 0, "", "" ];

				// Go to html and back, then peel off extra wrappers
				div.innerHTML = wrap[1] + elem + wrap[2];
				
				// Move to the right depth
				while ( wrap[0]-- )
					div = div.lastChild;
				
				// Remove IE's autoinserted <tbody> from table fragments
				if ( jQuery.browser.msie ) {
					
					// String was a <table>, *may* have spurious <tbody>
					var tbody = !tags.indexOf("<table") && tags.indexOf("<tbody") < 0 ?
						div.firstChild && div.firstChild.childNodes :
						
						// String was a bare <thead> or <tfoot>
						wrap[1] == "<table>" && tags.indexOf("<tbody") < 0 ?
							div.childNodes :
							[];
				
					for ( var j = tbody.length - 1; j >= 0 ; --j )
						if ( jQuery.nodeName( tbody[ j ], "tbody" ) && !tbody[ j ].childNodes.length )
							tbody[ j ].parentNode.removeChild( tbody[ j ] );
					
					// IE completely kills leading whitespace when innerHTML is used	
					if ( /^\s/.test( elem ) )	
						div.insertBefore( context.createTextNode( elem.match(/^\s*/)[0] ), div.firstChild );
				
				}
				
				elem = jQuery.makeArray( div.childNodes );
			}

			if ( elem.length === 0 && (!jQuery.nodeName( elem, "form" ) && !jQuery.nodeName( elem, "select" )) )
				return;

			if ( elem[0] == undefined || jQuery.nodeName( elem, "form" ) || elem.options )
				ret.push( elem );

			else
				ret = jQuery.merge( ret, elem );

		});

		return ret;
	},
	
	attr: function( elem, name, value ) {
		// don't set attributes on text and comment nodes
		if (!elem || elem.nodeType == 3 || elem.nodeType == 8)
			return undefined;

		var fix = jQuery.isXMLDoc( elem ) ?
			{} :
			jQuery.props;

		// Safari mis-reports the default selected property of a hidden option
		// Accessing the parent's selectedIndex property fixes it
		if ( name == "selected" && jQuery.browser.safari )
			elem.parentNode.selectedIndex;
		
		// Certain attributes only work when accessed via the old DOM 0 way
		if ( fix[ name ] ) {
			if ( value != undefined )
				elem[ fix[ name ] ] = value;

			return elem[ fix[ name ] ];

		} else if ( jQuery.browser.msie && name == "style" )
			return jQuery.attr( elem.style, "cssText", value );

		else if ( value == undefined && jQuery.browser.msie && jQuery.nodeName( elem, "form" ) && (name == "action" || name == "method") )
			return elem.getAttributeNode( name ).nodeValue;

		// IE elem.getAttribute passes even for style
		else if ( elem.tagName ) {

			if ( value != undefined ) {
				// We can't allow the type property to be changed (since it causes problems in IE)
				if ( name == "type" && jQuery.nodeName( elem, "input" ) && elem.parentNode )
					throw "type property can't be changed";

				// convert the value to a string (all browsers do this but IE) see #1070
				elem.setAttribute( name, "" + value );
			}

			if ( jQuery.browser.msie && /href|src/.test( name ) && !jQuery.isXMLDoc( elem ) ) 
				return elem.getAttribute( name, 2 );

			return elem.getAttribute( name );

		// elem is actually elem.style ... set the style
		} else {
			// IE actually uses filters for opacity
			if ( name == "opacity" && jQuery.browser.msie ) {
				if ( value != undefined ) {
					// IE has trouble with opacity if it does not have layout
					// Force it by setting the zoom level
					elem.zoom = 1; 
	
					// Set the alpha filter to set the opacity
					elem.filter = (elem.filter || "").replace( /alpha\([^)]*\)/, "" ) +
						(parseFloat( value ).toString() == "NaN" ? "" : "alpha(opacity=" + value * 100 + ")");
				}
	
				return elem.filter && elem.filter.indexOf("opacity=") >= 0 ?
					(parseFloat( elem.filter.match(/opacity=([^)]*)/)[1] ) / 100).toString() :
					"";
			}

			name = name.replace(/-([a-z])/ig, function(all, letter){
				return letter.toUpperCase();
			});

			if ( value != undefined )
				elem[ name ] = value;

			return elem[ name ];
		}
	},
	
	trim: function( text ) {
		return (text || "").replace( /^\s+|\s+$/g, "" );
	},

	makeArray: function( array ) {
		var ret = [];

		// Need to use typeof to fight Safari childNodes crashes
		if ( typeof array != "array" )
			for ( var i = 0, length = array.length; i < length; i++ )
				ret.push( array[ i ] );
		else
			ret = array.slice( 0 );

		return ret;
	},

	inArray: function( elem, array ) {
		for ( var i = 0, length = array.length; i < length; i++ )
			if ( array[ i ] == elem )
				return i;

		return -1;
	},

	merge: function( first, second ) {
		// We have to loop this way because IE & Opera overwrite the length
		// expando of getElementsByTagName

		// Also, we need to make sure that the correct elements are being returned
		// (IE returns comment nodes in a '*' query)
		if ( jQuery.browser.msie ) {
			for ( var i = 0; second[ i ]; i++ )
				if ( second[ i ].nodeType != 8 )
					first.push( second[ i ] );

		} else
			for ( var i = 0; second[ i ]; i++ )
				first.push( second[ i ] );

		return first;
	},

	unique: function( array ) {
		var ret = [], done = {};

		try {

			for ( var i = 0, length = array.length; i < length; i++ ) {
				var id = jQuery.data( array[ i ] );

				if ( !done[ id ] ) {
					done[ id ] = true;
					ret.push( array[ i ] );
				}
			}

		} catch( e ) {
			ret = array;
		}

		return ret;
	},

	grep: function( elems, callback, inv ) {
		var ret = [];

		// Go through the array, only saving the items
		// that pass the validator function
		for ( var i = 0, length = elems.length; i < length; i++ )
			if ( !inv && callback( elems[ i ], i ) || inv && !callback( elems[ i ], i ) )
				ret.push( elems[ i ] );

		return ret;
	},

	map: function( elems, callback ) {
		var ret = [];

		// Go through the array, translating each of the items to their
		// new value (or values).
		for ( var i = 0, length = elems.length; i < length; i++ ) {
			var value = callback( elems[ i ], i );

			if ( value !== null && value != undefined ) {
				if ( value.constructor != Array )
					value = [ value ];

				ret = ret.concat( value );
			}
		}

		return ret;
	}
});

var userAgent = navigator.userAgent.toLowerCase();

// Figure out what browser is being used
jQuery.browser = {
	version: (userAgent.match( /.+(?:rv|it|ra|ie)[\/: ]([\d.]+)/ ) || [])[1],
	safari: /webkit/.test( userAgent ),
	opera: /opera/.test( userAgent ),
	msie: /msie/.test( userAgent ) && !/opera/.test( userAgent ),
	mozilla: /mozilla/.test( userAgent ) && !/(compatible|webkit)/.test( userAgent )
};

var styleFloat = jQuery.browser.msie ?
	"styleFloat" :
	"cssFloat";
	
jQuery.extend({
	// Check to see if the W3C box model is being used
	boxModel: !jQuery.browser.msie || document.compatMode == "CSS1Compat",
	
	props: {
		"for": "htmlFor",
		"class": "className",
		"float": styleFloat,
		cssFloat: styleFloat,
		styleFloat: styleFloat,
		innerHTML: "innerHTML",
		className: "className",
		value: "value",
		disabled: "disabled",
		checked: "checked",
		readonly: "readOnly",
		selected: "selected",
		maxlength: "maxLength",
		selectedIndex: "selectedIndex",
		defaultValue: "defaultValue",
		tagName: "tagName",
		nodeName: "nodeName"
	}
});

jQuery.each({
	parent: function(elem){return elem.parentNode;},
	parents: function(elem){return jQuery.dir(elem,"parentNode");},
	next: function(elem){return jQuery.nth(elem,2,"nextSibling");},
	prev: function(elem){return jQuery.nth(elem,2,"previousSibling");},
	nextAll: function(elem){return jQuery.dir(elem,"nextSibling");},
	prevAll: function(elem){return jQuery.dir(elem,"previousSibling");},
	siblings: function(elem){return jQuery.sibling(elem.parentNode.firstChild,elem);},
	children: function(elem){return jQuery.sibling(elem.firstChild);},
	contents: function(elem){return jQuery.nodeName(elem,"iframe")?elem.contentDocument||elem.contentWindow.document:jQuery.makeArray(elem.childNodes);}
}, function(name, fn){
	jQuery.fn[ name ] = function( selector ) {
		var ret = jQuery.map( this, fn );

		if ( selector && typeof selector == "string" )
			ret = jQuery.multiFilter( selector, ret );

		return this.pushStack( jQuery.unique( ret ) );
	};
});

jQuery.each({
	appendTo: "append",
	prependTo: "prepend",
	insertBefore: "before",
	insertAfter: "after",
	replaceAll: "replaceWith"
}, function(name, original){
	jQuery.fn[ name ] = function() {
		var args = arguments;

		return this.each(function(){
			for ( var i = 0, length = args.length; i < length; i++ )
				jQuery( args[ i ] )[ original ]( this );
		});
	};
});

jQuery.each({
	removeAttr: function( name ) {
		jQuery.attr( this, name, "" );
		if (this.nodeType == 1) 
			this.removeAttribute( name );
	},

	addClass: function( classNames ) {
		jQuery.className.add( this, classNames );
	},

	removeClass: function( classNames ) {
		jQuery.className.remove( this, classNames );
	},

	toggleClass: function( classNames ) {
		jQuery.className[ jQuery.className.has( this, classNames ) ? "remove" : "add" ]( this, classNames );
	},

	remove: function( selector ) {
		if ( !selector || jQuery.filter( selector, [ this ] ).r.length ) {
			// Prevent memory leaks
			jQuery( "*", this ).add(this).each(function(){
				jQuery.event.remove(this);
				jQuery.removeData(this);
			});
			if (this.parentNode)
				this.parentNode.removeChild( this );
		}
	},

	empty: function() {
		// Remove element nodes and prevent memory leaks
		jQuery( ">*", this ).remove();
		
		// Remove any remaining nodes
		while ( this.firstChild )
			this.removeChild( this.firstChild );
	}
}, function(name, fn){
	jQuery.fn[ name ] = function(){
		return this.each( fn, arguments );
	};
});

jQuery.each([ "Height", "Width" ], function(i, name){
	var type = name.toLowerCase();
	
	jQuery.fn[ type ] = function( size ) {
		// Get window width or height
		return this[0] == window ?
			// Opera reports document.body.client[Width/Height] properly in both quirks and standards
			jQuery.browser.opera && document.body[ "client" + name ] || 
			
			// Safari reports inner[Width/Height] just fine (Mozilla and Opera include scroll bar widths)
			jQuery.browser.safari && window[ "inner" + name ] ||
			
			// Everyone else use document.documentElement or document.body depending on Quirks vs Standards mode
			document.compatMode == "CSS1Compat" && document.documentElement[ "client" + name ] || document.body[ "client" + name ] :
		
			// Get document width or height
			this[0] == document ?
				// Either scroll[Width/Height] or offset[Width/Height], whichever is greater
				Math.max( 
					Math.max(document.body["scroll" + name], document.documentElement["scroll" + name]), 
					Math.max(document.body["offset" + name], document.documentElement["offset" + name]) 
				) :

				// Get or set width or height on the element
				size == undefined ?
					// Get width or height on the element
					(this.length ? jQuery.css( this[0], type ) : null) :

					// Set the width or height on the element (default to pixels if value is unitless)
					this.css( type, size.constructor == String ? size : size + "px" );
	};
});

var chars = jQuery.browser.safari && parseInt(jQuery.browser.version) < 417 ?
		"(?:[\\w*_-]|\\\\.)" :
		"(?:[\\w\u0128-\uFFFF*_-]|\\\\.)",
	quickChild = new RegExp("^>\\s*(" + chars + "+)"),
	quickID = new RegExp("^(" + chars + "+)(#)(" + chars + "+)"),
	quickClass = new RegExp("^([#.]?)(" + chars + "*)");

jQuery.extend({
	expr: {
		"": function(a,i,m){return m[2]=="*"||jQuery.nodeName(a,m[2]);},
		"#": function(a,i,m){return a.getAttribute("id")==m[2];},
		":": {
			// Position Checks
			lt: function(a,i,m){return i<m[3]-0;},
			gt: function(a,i,m){return i>m[3]-0;},
			nth: function(a,i,m){return m[3]-0==i;},
			eq: function(a,i,m){return m[3]-0==i;},
			first: function(a,i){return i==0;},
			last: function(a,i,m,r){return i==r.length-1;},
			even: function(a,i){return i%2==0;},
			odd: function(a,i){return i%2;},

			// Child Checks
			"first-child": function(a){return a.parentNode.getElementsByTagName("*")[0]==a;},
			"last-child": function(a){return jQuery.nth(a.parentNode.lastChild,1,"previousSibling")==a;},
			"only-child": function(a){return !jQuery.nth(a.parentNode.lastChild,2,"previousSibling");},

			// Parent Checks
			parent: function(a){return a.firstChild;},
			empty: function(a){return !a.firstChild;},

			// Text Check
			contains: function(a,i,m){return (a.textContent||a.innerText||jQuery(a).text()||"").indexOf(m[3])>=0;},

			// Visibility
			visible: function(a){return "hidden"!=a.type&&jQuery.css(a,"display")!="none"&&jQuery.css(a,"visibility")!="hidden";},
			hidden: function(a){return "hidden"==a.type||jQuery.css(a,"display")=="none"||jQuery.css(a,"visibility")=="hidden";},

			// Form attributes
			enabled: function(a){return !a.disabled;},
			disabled: function(a){return a.disabled;},
			checked: function(a){return a.checked;},
			selected: function(a){return a.selected||jQuery.attr(a,"selected");},

			// Form elements
			text: function(a){return "text"==a.type;},
			radio: function(a){return "radio"==a.type;},
			checkbox: function(a){return "checkbox"==a.type;},
			file: function(a){return "file"==a.type;},
			password: function(a){return "password"==a.type;},
			submit: function(a){return "submit"==a.type;},
			image: function(a){return "image"==a.type;},
			reset: function(a){return "reset"==a.type;},
			button: function(a){return "button"==a.type||jQuery.nodeName(a,"button");},
			input: function(a){return /input|select|textarea|button/i.test(a.nodeName);},

			// :has()
			has: function(a,i,m){return jQuery.find(m[3],a).length;},

			// :header
			header: function(a){return /h\d/i.test(a.nodeName);},

			// :animated
			animated: function(a){return jQuery.grep(jQuery.timers,function(fn){return a==fn.elem;}).length;}
		}
	},
	
	// The regular expressions that power the parsing engine
	parse: [
		// Match: [@value='test'], [@foo]
		/^(\[) *@?([\w-]+) *([!*$^~=]*) *('?"?)(.*?)\4 *\]/,

		// Match: :contains('foo')
		/^(:)([\w-]+)\("?'?(.*?(\(.*?\))?[^(]*?)"?'?\)/,

		// Match: :even, :last-chlid, #id, .class
		new RegExp("^([:.#]*)(" + chars + "+)")
	],

	multiFilter: function( expr, elems, not ) {
		var old, cur = [];

		while ( expr && expr != old ) {
			old = expr;
			var f = jQuery.filter( expr, elems, not );
			expr = f.t.replace(/^\s*,\s*/, "" );
			cur = not ? elems = f.r : jQuery.merge( cur, f.r );
		}

		return cur;
	},

	find: function( t, context ) {
		// Quickly handle non-string expressions
		if ( typeof t != "string" )
			return [ t ];

		// check to make sure context is a DOM element or a document
		if ( context && context.nodeType != 1 && context.nodeType != 9)
			return [ ];

		// Set the correct context (if none is provided)
		context = context || document;

		// Initialize the search
		var ret = [context], done = [], last, nodeName;

		// Continue while a selector expression exists, and while
		// we're no longer looping upon ourselves
		while ( t && last != t ) {
			var r = [];
			last = t;

			t = jQuery.trim(t);

			var foundToken = false;

			// An attempt at speeding up child selectors that
			// point to a specific element tag
			var re = quickChild;
			var m = re.exec(t);

			if ( m ) {
				nodeName = m[1].toUpperCase();

				// Perform our own iteration and filter
				for ( var i = 0; ret[i]; i++ )
					for ( var c = ret[i].firstChild; c; c = c.nextSibling )
						if ( c.nodeType == 1 && (nodeName == "*" || c.nodeName.toUpperCase() == nodeName) )
							r.push( c );

				ret = r;
				t = t.replace( re, "" );
				if ( t.indexOf(" ") == 0 ) continue;
				foundToken = true;
			} else {
				re = /^([>+~])\s*(\w*)/i;

				if ( (m = re.exec(t)) != null ) {
					r = [];

					var merge = {};
					nodeName = m[2].toUpperCase();
					m = m[1];

					for ( var j = 0, rl = ret.length; j < rl; j++ ) {
						var n = m == "~" || m == "+" ? ret[j].nextSibling : ret[j].firstChild;
						for ( ; n; n = n.nextSibling )
							if ( n.nodeType == 1 ) {
								var id = jQuery.data(n);

								if ( m == "~" && merge[id] ) break;
								
								if (!nodeName || n.nodeName.toUpperCase() == nodeName ) {
									if ( m == "~" ) merge[id] = true;
									r.push( n );
								}
								
								if ( m == "+" ) break;
							}
					}

					ret = r;

					// And remove the token
					t = jQuery.trim( t.replace( re, "" ) );
					foundToken = true;
				}
			}

			// See if there's still an expression, and that we haven't already
			// matched a token
			if ( t && !foundToken ) {
				// Handle multiple expressions
				if ( !t.indexOf(",") ) {
					// Clean the result set
					if ( context == ret[0] ) ret.shift();

					// Merge the result sets
					done = jQuery.merge( done, ret );

					// Reset the context
					r = ret = [context];

					// Touch up the selector string
					t = " " + t.substr(1,t.length);

				} else {
					// Optimize for the case nodeName#idName
					var re2 = quickID;
					var m = re2.exec(t);
					
					// Re-organize the results, so that they're consistent
					if ( m ) {
						m = [ 0, m[2], m[3], m[1] ];

					} else {
						// Otherwise, do a traditional filter check for
						// ID, class, and element selectors
						re2 = quickClass;
						m = re2.exec(t);
					}

					m[2] = m[2].replace(/\\/g, "");

					var elem = ret[ret.length-1];

					// Try to do a global search by ID, where we can
					if ( m[1] == "#" && elem && elem.getElementById && !jQuery.isXMLDoc(elem) ) {
						// Optimization for HTML document case
						var oid = elem.getElementById(m[2]);
						
						// Do a quick check for the existence of the actual ID attribute
						// to avoid selecting by the name attribute in IE
						// also check to insure id is a string to avoid selecting an element with the name of 'id' inside a form
						if ( (jQuery.browser.msie||jQuery.browser.opera) && oid && typeof oid.id == "string" && oid.id != m[2] )
							oid = jQuery('[@id="'+m[2]+'"]', elem)[0];

						// Do a quick check for node name (where applicable) so
						// that div#foo searches will be really fast
						ret = r = oid && (!m[3] || jQuery.nodeName(oid, m[3])) ? [oid] : [];
					} else {
						// We need to find all descendant elements
						for ( var i = 0; ret[i]; i++ ) {
							// Grab the tag name being searched for
							var tag = m[1] == "#" && m[3] ? m[3] : m[1] != "" || m[0] == "" ? "*" : m[2];

							// Handle IE7 being really dumb about <object>s
							if ( tag == "*" && ret[i].nodeName.toLowerCase() == "object" )
								tag = "param";

							r = jQuery.merge( r, ret[i].getElementsByTagName( tag ));
						}

						// It's faster to filter by class and be done with it
						if ( m[1] == "." )
							r = jQuery.classFilter( r, m[2] );

						// Same with ID filtering
						if ( m[1] == "#" ) {
							var tmp = [];

							// Try to find the element with the ID
							for ( var i = 0; r[i]; i++ )
								if ( r[i].getAttribute("id") == m[2] ) {
									tmp = [ r[i] ];
									break;
								}

							r = tmp;
						}

						ret = r;
					}

					t = t.replace( re2, "" );
				}

			}

			// If a selector string still exists
			if ( t ) {
				// Attempt to filter it
				var val = jQuery.filter(t,r);
				ret = r = val.r;
				t = jQuery.trim(val.t);
			}
		}

		// An error occurred with the selector;
		// just return an empty set instead
		if ( t )
			ret = [];

		// Remove the root context
		if ( ret && context == ret[0] )
			ret.shift();

		// And combine the results
		done = jQuery.merge( done, ret );

		return done;
	},

	classFilter: function(r,m,not){
		m = " " + m + " ";
		var tmp = [];
		for ( var i = 0; r[i]; i++ ) {
			var pass = (" " + r[i].className + " ").indexOf( m ) >= 0;
			if ( !not && pass || not && !pass )
				tmp.push( r[i] );
		}
		return tmp;
	},

	filter: function(t,r,not) {
		var last;

		// Look for common filter expressions
		while ( t && t != last ) {
			last = t;

			var p = jQuery.parse, m;

			for ( var i = 0; p[i]; i++ ) {
				m = p[i].exec( t );

				if ( m ) {
					// Remove what we just matched
					t = t.substring( m[0].length );

					m[2] = m[2].replace(/\\/g, "");
					break;
				}
			}

			if ( !m )
				break;

			// :not() is a special case that can be optimized by
			// keeping it out of the expression list
			if ( m[1] == ":" && m[2] == "not" )
				// optimize if only one selector found (most common case)
				r = isSimple.test( m[3] ) ?
					jQuery.filter(m[3], r, true).r :
					jQuery( r ).not( m[3] );

			// We can get a big speed boost by filtering by class here
			else if ( m[1] == "." )
				r = jQuery.classFilter(r, m[2], not);

			else if ( m[1] == "[" ) {
				var tmp = [], type = m[3];
				
				for ( var i = 0, rl = r.length; i < rl; i++ ) {
					var a = r[i], z = a[ jQuery.props[m[2]] || m[2] ];
					
					if ( z == null || /href|src|selected/.test(m[2]) )
						z = jQuery.attr(a,m[2]) || '';

					if ( (type == "" && !!z ||
						 type == "=" && z == m[5] ||
						 type == "!=" && z != m[5] ||
						 type == "^=" && z && !z.indexOf(m[5]) ||
						 type == "$=" && z.substr(z.length - m[5].length) == m[5] ||
						 (type == "*=" || type == "~=") && z.indexOf(m[5]) >= 0) ^ not )
							tmp.push( a );
				}
				
				r = tmp;

			// We can get a speed boost by handling nth-child here
			} else if ( m[1] == ":" && m[2] == "nth-child" ) {
				var merge = {}, tmp = [],
					// parse equations like 'even', 'odd', '5', '2n', '3n+2', '4n-1', '-n+6'
					test = /(-?)(\d*)n((?:\+|-)?\d*)/.exec(
						m[3] == "even" && "2n" || m[3] == "odd" && "2n+1" ||
						!/\D/.test(m[3]) && "0n+" + m[3] || m[3]),
					// calculate the numbers (first)n+(last) including if they are negative
					first = (test[1] + (test[2] || 1)) - 0, last = test[3] - 0;
 
				// loop through all the elements left in the jQuery object
				for ( var i = 0, rl = r.length; i < rl; i++ ) {
					var node = r[i], parentNode = node.parentNode, id = jQuery.data(parentNode);

					if ( !merge[id] ) {
						var c = 1;

						for ( var n = parentNode.firstChild; n; n = n.nextSibling )
							if ( n.nodeType == 1 )
								n.nodeIndex = c++;

						merge[id] = true;
					}

					var add = false;

					if ( first == 0 ) {
						if ( node.nodeIndex == last )
							add = true;
					} else if ( (node.nodeIndex - last) % first == 0 && (node.nodeIndex - last) / first >= 0 )
						add = true;

					if ( add ^ not )
						tmp.push( node );
				}

				r = tmp;

			// Otherwise, find the expression to execute
			} else {
				var fn = jQuery.expr[ m[1] ];
				if ( typeof fn == "object" )
					fn = fn[ m[2] ];

				if ( typeof fn == "string" )
					fn = eval("false||function(a,i){return " + fn + ";}");

				// Execute it against the current filter
				r = jQuery.grep( r, function(elem, i){
					return fn(elem, i, m, r);
				}, not );
			}
		}

		// Return an array of filtered elements (r)
		// and the modified expression string (t)
		return { r: r, t: t };
	},

	dir: function( elem, dir ){
		var matched = [];
		var cur = elem[dir];
		while ( cur && cur != document ) {
			if ( cur.nodeType == 1 )
				matched.push( cur );
			cur = cur[dir];
		}
		return matched;
	},
	
	nth: function(cur,result,dir,elem){
		result = result || 1;
		var num = 0;

		for ( ; cur; cur = cur[dir] )
			if ( cur.nodeType == 1 && ++num == result )
				break;

		return cur;
	},
	
	sibling: function( n, elem ) {
		var r = [];

		for ( ; n; n = n.nextSibling ) {
			if ( n.nodeType == 1 && (!elem || n != elem) )
				r.push( n );
		}

		return r;
	}
});

/*
 * A number of helper functions used for managing events.
 * Many of the ideas behind this code orignated from 
 * Dean Edwards' addEvent library.
 */
jQuery.event = {

	// Bind an event to an element
	// Original by Dean Edwards
	add: function(elem, types, handler, data) {
		if ( elem.nodeType == 3 || elem.nodeType == 8 )
			return;

		// For whatever reason, IE has trouble passing the window object
		// around, causing it to be cloned in the process
		if ( jQuery.browser.msie && elem.setInterval != undefined )
			elem = window;

		// Make sure that the function being executed has a unique ID
		if ( !handler.guid )
			handler.guid = this.guid++;
			
		// if data is passed, bind to handler 
		if( data != undefined ) { 
			// Create temporary function pointer to original handler 
			var fn = handler; 

			// Create unique handler function, wrapped around original handler 
			handler = function() { 
				// Pass arguments and context to original handler 
				return fn.apply(this, arguments); 
			};

			// Store data in unique handler 
			handler.data = data;

			// Set the guid of unique handler to the same of original handler, so it can be removed 
			handler.guid = fn.guid;
		}

		// Init the element's event structure
		var events = jQuery.data(elem, "events") || jQuery.data(elem, "events", {}),
			handle = jQuery.data(elem, "handle") || jQuery.data(elem, "handle", function(){
				// returned undefined or false
				var val;

				// Handle the second event of a trigger and when
				// an event is called after a page has unloaded
				if ( typeof jQuery == "undefined" || jQuery.event.triggered )
					return val;
		
				val = jQuery.event.handle.apply(arguments.callee.elem, arguments);
		
				return val;
			});
		// Add elem as a property of the handle function
		// This is to prevent a memory leak with non-native
		// event in IE.
		handle.elem = elem;
			
			// Handle multiple events seperated by a space
			// jQuery(...).bind("mouseover mouseout", fn);
			jQuery.each(types.split(/\s+/), function(index, type) {
				// Namespaced event handlers
				var parts = type.split(".");
				type = parts[0];
				handler.type = parts[1];

				// Get the current list of functions bound to this event
				var handlers = events[type];

				// Init the event handler queue
				if (!handlers) {
					handlers = events[type] = {};
		
					// Check for a special event handler
					// Only use addEventListener/attachEvent if the special
					// events handler returns false
					if ( !jQuery.event.special[type] || jQuery.event.special[type].setup.call(elem) === false ) {
						// Bind the global event handler to the element
						if (elem.addEventListener)
							elem.addEventListener(type, handle, false);
						else if (elem.attachEvent)
							elem.attachEvent("on" + type, handle);
					}
				}

				// Add the function to the element's handler list
				handlers[handler.guid] = handler;

				// Keep track of which events have been used, for global triggering
				jQuery.event.global[type] = true;
			});
		
		// Nullify elem to prevent memory leaks in IE
		elem = null;
	},

	guid: 1,
	global: {},

	// Detach an event or set of events from an element
	remove: function(elem, types, handler) {
		// don't do events on text and comment nodes
		if ( elem.nodeType == 3 || elem.nodeType == 8 )
			return;

		var events = jQuery.data(elem, "events"), ret, index;

		if ( events ) {
			// Unbind all events for the element
			if ( types == undefined || (typeof types == "string" && types.charAt(0) == ".") )
				for ( var type in events )
					this.remove( elem, type + (types || "") );
			else {
				// types is actually an event object here
				if ( types.type ) {
					handler = types.handler;
					types = types.type;
				}
				
				// Handle multiple events seperated by a space
				// jQuery(...).unbind("mouseover mouseout", fn);
				jQuery.each(types.split(/\s+/), function(index, type){
					// Namespaced event handlers
					var parts = type.split(".");
					type = parts[0];
					
					if ( events[type] ) {
						// remove the given handler for the given type
						if ( handler )
							delete events[type][handler.guid];
			
						// remove all handlers for the given type
						else
							for ( handler in events[type] )
								// Handle the removal of namespaced events
								if ( !parts[1] || events[type][handler].type == parts[1] )
									delete events[type][handler];

						// remove generic event handler if no more handlers exist
						for ( ret in events[type] ) break;
						if ( !ret ) {
							if ( !jQuery.event.special[type] || jQuery.event.special[type].teardown.call(elem) === false ) {
								if (elem.removeEventListener)
									elem.removeEventListener(type, jQuery.data(elem, "handle"), false);
								else if (elem.detachEvent)
									elem.detachEvent("on" + type, jQuery.data(elem, "handle"));
							}
							ret = null;
							delete events[type];
						}
					}
				});
			}

			// Remove the expando if it's no longer used
			for ( ret in events ) break;
			if ( !ret ) {
				var handle = jQuery.data( elem, "handle" );
				if ( handle ) handle.elem = null;
				jQuery.removeData( elem, "events" );
				jQuery.removeData( elem, "handle" );
			}
		}
	},

	trigger: function(type, data, elem, donative, extra) {
		// Clone the incoming data, if any
		data = jQuery.makeArray(data || []);

		if ( type.indexOf("!") >= 0 ) {
			type = type.slice(0, -1);
			var exclusive = true;
		}

		// Handle a global trigger
		if ( !elem ) {
			// Only trigger if we've ever bound an event for it
			if ( this.global[type] )
				jQuery("*").add([window, document]).trigger(type, data);

		// Handle triggering a single element
		} else {
			// don't do events on text and comment nodes
			if ( elem.nodeType == 3 || elem.nodeType == 8 )
				return undefined;

			var val, ret, fn = jQuery.isFunction( elem[ type ] || null ),
				// Check to see if we need to provide a fake event, or not
				event = !data[0] || !data[0].preventDefault;
			
			// Pass along a fake event
			if ( event )
				data.unshift( this.fix({ type: type, target: elem }) );

			// Enforce the right trigger type
			data[0].type = type;
			if ( exclusive )
				data[0].exclusive = true;

			// Trigger the event
			if ( jQuery.isFunction( jQuery.data(elem, "handle") ) )
				val = jQuery.data(elem, "handle").apply( elem, data );

			// Handle triggering native .onfoo handlers
			if ( !fn && elem["on"+type] && elem["on"+type].apply( elem, data ) === false )
				val = false;

			// Extra functions don't get the custom event object
			if ( event )
				data.shift();

			// Handle triggering of extra function
			if ( extra && jQuery.isFunction( extra ) ) {
				// call the extra function and tack the current return value on the end for possible inspection
				ret = extra.apply( elem, val == null ? data : data.concat( val ) );
				// if anything is returned, give it precedence and have it overwrite the previous value
				if (ret !== undefined)
					val = ret;
			}

			// Trigger the native events (except for clicks on links)
			if ( fn && donative !== false && val !== false && !(jQuery.nodeName(elem, 'a') && type == "click") ) {
				this.triggered = true;
				try {
					elem[ type ]();
				// prevent IE from throwing an error for some hidden elements
				} catch (e) {}
			}

			this.triggered = false;
		}

		return val;
	},

	handle: function(event) {
		// returned undefined or false
		var val;

		// Empty object is for triggered events with no data
		event = jQuery.event.fix( event || window.event || {} ); 

		// Namespaced event handlers
		var parts = event.type.split(".");
		event.type = parts[0];

		var handlers = jQuery.data(this, "events") && jQuery.data(this, "events")[event.type], args = Array.prototype.slice.call( arguments, 1 );
		args.unshift( event );

		for ( var j in handlers ) {
			var handler = handlers[j];
			// Pass in a reference to the handler function itself
			// So that we can later remove it
			args[0].handler = handler;
			args[0].data = handler.data;

			// Filter the functions by class
			if ( !parts[1] && !event.exclusive || handler.type == parts[1] ) {
				var ret = handler.apply( this, args );

				if ( val !== false )
					val = ret;

				if ( ret === false ) {
					event.preventDefault();
					event.stopPropagation();
				}
			}
		}

		// Clean up added properties in IE to prevent memory leak
		if (jQuery.browser.msie)
			event.target = event.preventDefault = event.stopPropagation =
				event.handler = event.data = null;

		return val;
	},

	fix: function(event) {
		// store a copy of the original event object 
		// and clone to set read-only properties
		var originalEvent = event;
		event = jQuery.extend({}, originalEvent);
		
		// add preventDefault and stopPropagation since 
		// they will not work on the clone
		event.preventDefault = function() {
			// if preventDefault exists run it on the original event
			if (originalEvent.preventDefault)
				originalEvent.preventDefault();
			// otherwise set the returnValue property of the original event to false (IE)
			originalEvent.returnValue = false;
		};
		event.stopPropagation = function() {
			// if stopPropagation exists run it on the original event
			if (originalEvent.stopPropagation)
				originalEvent.stopPropagation();
			// otherwise set the cancelBubble property of the original event to true (IE)
			originalEvent.cancelBubble = true;
		};
		
		// Fix target property, if necessary
		if ( !event.target )
			event.target = event.srcElement || document; // Fixes #1925 where srcElement might not be defined either
				
		// check if target is a textnode (safari)
		if ( event.target.nodeType == 3 )
			event.target = originalEvent.target.parentNode;

		// Add relatedTarget, if necessary
		if ( !event.relatedTarget && event.fromElement )
			event.relatedTarget = event.fromElement == event.target ? event.toElement : event.fromElement;

		// Calculate pageX/Y if missing and clientX/Y available
		if ( event.pageX == null && event.clientX != null ) {
			var doc = document.documentElement, body = document.body;
			event.pageX = event.clientX + (doc && doc.scrollLeft || body && body.scrollLeft || 0) - (doc.clientLeft || 0);
			event.pageY = event.clientY + (doc && doc.scrollTop || body && body.scrollTop || 0) - (doc.clientTop || 0);
		}
			
		// Add which for key events
		if ( !event.which && ((event.charCode || event.charCode === 0) ? event.charCode : event.keyCode) )
			event.which = event.charCode || event.keyCode;
		
		// Add metaKey to non-Mac browsers (use ctrl for PC's and Meta for Macs)
		if ( !event.metaKey && event.ctrlKey )
			event.metaKey = event.ctrlKey;

		// Add which for click: 1 == left; 2 == middle; 3 == right
		// Note: button is not normalized, so don't use it
		if ( !event.which && event.button )
			event.which = (event.button & 1 ? 1 : ( event.button & 2 ? 3 : ( event.button & 4 ? 2 : 0 ) ));
			
		return event;
	},
	
	special: {
		ready: {
			setup: function() {
				// Make sure the ready event is setup
				bindReady();
				return;
			},
			
			teardown: function() { return; }
		},
		
		mouseenter: {
			setup: function() {
				if ( jQuery.browser.msie ) return false;
				jQuery(this).bind("mouseover", jQuery.event.special.mouseenter.handler);
				return true;
			},
		
			teardown: function() {
				if ( jQuery.browser.msie ) return false;
				jQuery(this).unbind("mouseover", jQuery.event.special.mouseenter.handler);
				return true;
			},
			
			handler: function(event) {
				// If we actually just moused on to a sub-element, ignore it
				if ( withinElement(event, this) ) return true;
				// Execute the right handlers by setting the event type to mouseenter
				arguments[0].type = "mouseenter";
				return jQuery.event.handle.apply(this, arguments);
			}
		},
	
		mouseleave: {
			setup: function() {
				if ( jQuery.browser.msie ) return false;
				jQuery(this).bind("mouseout", jQuery.event.special.mouseleave.handler);
				return true;
			},
		
			teardown: function() {
				if ( jQuery.browser.msie ) return false;
				jQuery(this).unbind("mouseout", jQuery.event.special.mouseleave.handler);
				return true;
			},
			
			handler: function(event) {
				// If we actually just moused on to a sub-element, ignore it
				if ( withinElement(event, this) ) return true;
				// Execute the right handlers by setting the event type to mouseleave
				arguments[0].type = "mouseleave";
				return jQuery.event.handle.apply(this, arguments);
			}
		}
	}
};

jQuery.fn.extend({
	bind: function( type, data, fn ) {
		return type == "unload" ? this.one(type, data, fn) : this.each(function(){
			jQuery.event.add( this, type, fn || data, fn && data );
		});
	},
	
	one: function( type, data, fn ) {
		return this.each(function(){
			jQuery.event.add( this, type, function(event) {
				jQuery(this).unbind(event);
				return (fn || data).apply( this, arguments);
			}, fn && data);
		});
	},

	unbind: function( type, fn ) {
		return this.each(function(){
			jQuery.event.remove( this, type, fn );
		});
	},

	trigger: function( type, data, fn ) {
		return this.each(function(){
			jQuery.event.trigger( type, data, this, true, fn );
		});
	},

	triggerHandler: function( type, data, fn ) {
		if ( this[0] )
			return jQuery.event.trigger( type, data, this[0], false, fn );
		return undefined;
	},

	toggle: function() {
		// Save reference to arguments for access in closure
		var args = arguments;

		return this.click(function(event) {
			// Figure out which function to execute
			this.lastToggle = 0 == this.lastToggle ? 1 : 0;
			
			// Make sure that clicks stop
			event.preventDefault();
			
			// and execute the function
			return args[this.lastToggle].apply( this, arguments ) || false;
		});
	},

	hover: function(fnOver, fnOut) {
		return this.bind('mouseenter', fnOver).bind('mouseleave', fnOut);
	},
	
	ready: function(fn) {
		// Attach the listeners
		bindReady();

		// If the DOM is already ready
		if ( jQuery.isReady )
			// Execute the function immediately
			fn.call( document, jQuery );
			
		// Otherwise, remember the function for later
		else
			// Add the function to the wait list
			jQuery.readyList.push( function() { return fn.call(this, jQuery); } );
	
		return this;
	}
});

jQuery.extend({
	isReady: false,
	readyList: [],
	// Handle when the DOM is ready
	ready: function() {
		// Make sure that the DOM is not already loaded
		if ( !jQuery.isReady ) {
			// Remember that the DOM is ready
			jQuery.isReady = true;
			
			// If there are functions bound, to execute
			if ( jQuery.readyList ) {
				// Execute all of them
				jQuery.each( jQuery.readyList, function(){
					this.apply( document );
				});
				
				// Reset the list of functions
				jQuery.readyList = null;
			}
		
			// Trigger any bound ready events
			jQuery(document).triggerHandler("ready");
		}
	}
});

var readyBound = false;

function bindReady(){
	if ( readyBound ) return;
	readyBound = true;

	// Mozilla, Opera (see further below for it) and webkit nightlies currently support this event
	if ( document.addEventListener && !jQuery.browser.opera)
		// Use the handy event callback
		document.addEventListener( "DOMContentLoaded", jQuery.ready, false );
	
	// If IE is used and is not in a frame
	// Continually check to see if the document is ready
	if ( jQuery.browser.msie && window == top ) (function(){
		if (jQuery.isReady) return;
		try {
			// If IE is used, use the trick by Diego Perini
			// http://javascript.nwbox.com/IEContentLoaded/
			document.documentElement.doScroll("left");
		} catch( error ) {
			setTimeout( arguments.callee, 0 );
			return;
		}
		// and execute any waiting functions
		jQuery.ready();
	})();

	if ( jQuery.browser.opera )
		document.addEventListener( "DOMContentLoaded", function () {
			if (jQuery.isReady) return;
			for (var i = 0; i < document.styleSheets.length; i++)
				if (document.styleSheets[i].disabled) {
					setTimeout( arguments.callee, 0 );
					return;
				}
			// and execute any waiting functions
			jQuery.ready();
		}, false);

	if ( jQuery.browser.safari ) {
		var numStyles;
		(function(){
			if (jQuery.isReady) return;
			if ( document.readyState != "loaded" && document.readyState != "complete" ) {
				setTimeout( arguments.callee, 0 );
				return;
			}
			if ( numStyles === undefined )
				numStyles = jQuery("style, link[rel=stylesheet]").length;
			if ( document.styleSheets.length != numStyles ) {
				setTimeout( arguments.callee, 0 );
				return;
			}
			// and execute any waiting functions
			jQuery.ready();
		})();
	}

	// A fallback to window.onload, that will always work
	jQuery.event.add( window, "load", jQuery.ready );
}

jQuery.each( ("blur,focus,load,resize,scroll,unload,click,dblclick," +
	"mousedown,mouseup,mousemove,mouseover,mouseout,change,select," + 
	"submit,keydown,keypress,keyup,error").split(","), function(i, name){
	
	// Handle event binding
	jQuery.fn[name] = function(fn){
		return fn ? this.bind(name, fn) : this.trigger(name);
	};
});

// Checks if an event happened on an element within another element
// Used in jQuery.event.special.mouseenter and mouseleave handlers
var withinElement = function(event, elem) {
	// Check if mouse(over|out) are still within the same parent element
	var parent = event.relatedTarget;
	// Traverse up the tree
	while ( parent && parent != elem ) try { parent = parent.parentNode; } catch(error) { parent = elem; }
	// Return true if we actually just moused on to a sub-element
	return parent == elem;
};

// Prevent memory leaks in IE
// And prevent errors on refresh with events like mouseover in other browsers
// Window isn't included so as not to unbind existing unload events
jQuery(window).bind("unload", function() {
	jQuery("*").add(document).unbind();
});
jQuery.fn.extend({
	load: function( url, params, callback ) {
		if ( jQuery.isFunction( url ) )
			return this.bind("load", url);

		var off = url.indexOf(" ");
		if ( off >= 0 ) {
			var selector = url.slice(off, url.length);
			url = url.slice(0, off);
		}

		callback = callback || function(){};

		// Default to a GET request
		var type = "GET";

		// If the second parameter was provided
		if ( params )
			// If it's a function
			if ( jQuery.isFunction( params ) ) {
				// We assume that it's the callback
				callback = params;
				params = null;

			// Otherwise, build a param string
			} else {
				params = jQuery.param( params );
				type = "POST";
			}

		var self = this;

		// Request the remote document
		jQuery.ajax({
			url: url,
			type: type,
			dataType: "html",
			data: params,
			complete: function(res, status){
				// If successful, inject the HTML into all the matched elements
				if ( status == "success" || status == "notmodified" )
					// See if a selector was specified
					self.html( selector ?
						// Create a dummy div to hold the results
						jQuery("<div/>")
							// inject the contents of the document in, removing the scripts
							// to avoid any 'Permission Denied' errors in IE
							.append(res.responseText.replace(/<script(.|\s)*?\/script>/g, ""))

							// Locate the specified elements
							.find(selector) :

						// If not, just inject the full result
						res.responseText );

				self.each( callback, [res.responseText, status, res] );
			}
		});
		return this;
	},

	serialize: function() {
		return jQuery.param(this.serializeArray());
	},
	serializeArray: function() {
		return this.map(function(){
			return jQuery.nodeName(this, "form") ?
				jQuery.makeArray(this.elements) : this;
		})
		.filter(function(){
			return this.name && !this.disabled && 
				(this.checked || /select|textarea/i.test(this.nodeName) || 
					/text|hidden|password/i.test(this.type));
		})
		.map(function(i, elem){
			var val = jQuery(this).val();
			return val == null ? null :
				val.constructor == Array ?
					jQuery.map( val, function(val, i){
						return {name: elem.name, value: val};
					}) :
					{name: elem.name, value: val};
		}).get();
	}
});

// Attach a bunch of functions for handling common AJAX events
jQuery.each( "ajaxStart,ajaxStop,ajaxComplete,ajaxError,ajaxSuccess,ajaxSend".split(","), function(i,o){
	jQuery.fn[o] = function(f){
		return this.bind(o, f);
	};
});

var jsc = (new Date).getTime();

jQuery.extend({
	get: function( url, data, callback, type ) {
		// shift arguments if data argument was ommited
		if ( jQuery.isFunction( data ) ) {
			callback = data;
			data = null;
		}
		
		return jQuery.ajax({
			type: "GET",
			url: url,
			data: data,
			success: callback,
			dataType: type
		});
	},

	getScript: function( url, callback ) {
		return jQuery.get(url, null, callback, "script");
	},

	getJSON: function( url, data, callback ) {
		return jQuery.get(url, data, callback, "json");
	},

	post: function( url, data, callback, type ) {
		if ( jQuery.isFunction( data ) ) {
			callback = data;
			data = {};
		}

		return jQuery.ajax({
			type: "POST",
			url: url,
			data: data,
			success: callback,
			dataType: type
		});
	},

	ajaxSetup: function( settings ) {
		jQuery.extend( jQuery.ajaxSettings, settings );
	},

	ajaxSettings: {
		global: true,
		type: "GET",
		timeout: 0,
		contentType: "application/x-www-form-urlencoded",
		processData: true,
		async: true,
		data: null,
		username: null,
		password: null,
		accepts: {
			xml: "application/xml, text/xml",
			html: "text/html",
			script: "text/javascript, application/javascript",
			json: "application/json, text/javascript",
			text: "text/plain",
			_default: "*/*"
		}
	},
	
	// Last-Modified header cache for next request
	lastModified: {},

	ajax: function( s ) {
		var jsonp, jsre = /=\?(&|$)/g, status, data;

		// Extend the settings, but re-extend 's' so that it can be
		// checked again later (in the test suite, specifically)
		s = jQuery.extend(true, s, jQuery.extend(true, {}, jQuery.ajaxSettings, s));

		// convert data if not already a string
		if ( s.data && s.processData && typeof s.data != "string" )
			s.data = jQuery.param(s.data);

		// Handle JSONP Parameter Callbacks
		if ( s.dataType == "jsonp" ) {
			if ( s.type.toLowerCase() == "get" ) {
				if ( !s.url.match(jsre) )
					s.url += (s.url.match(/\?/) ? "&" : "?") + (s.jsonp || "callback") + "=?";
			} else if ( !s.data || !s.data.match(jsre) )
				s.data = (s.data ? s.data + "&" : "") + (s.jsonp || "callback") + "=?";
			s.dataType = "json";
		}

		// Build temporary JSONP function
		if ( s.dataType == "json" && (s.data && s.data.match(jsre) || s.url.match(jsre)) ) {
			jsonp = "jsonp" + jsc++;

			// Replace the =? sequence both in the query string and the data
			if ( s.data )
				s.data = (s.data + "").replace(jsre, "=" + jsonp + "$1");
			s.url = s.url.replace(jsre, "=" + jsonp + "$1");

			// We need to make sure
			// that a JSONP style response is executed properly
			s.dataType = "script";

			// Handle JSONP-style loading
			window[ jsonp ] = function(tmp){
				data = tmp;
				success();
				complete();
				// Garbage collect
				window[ jsonp ] = undefined;
				try{ delete window[ jsonp ]; } catch(e){}
				if ( head )
					head.removeChild( script );
			};
		}

		if ( s.dataType == "script" && s.cache == null )
			s.cache = false;

		if ( s.cache === false && s.type.toLowerCase() == "get" ) {
			var ts = (new Date()).getTime();
			// try replacing _= if it is there
			var ret = s.url.replace(/(\?|&)_=.*?(&|$)/, "$1_=" + ts + "$2");
			// if nothing was replaced, add timestamp to the end
			s.url = ret + ((ret == s.url) ? (s.url.match(/\?/) ? "&" : "?") + "_=" + ts : "");
		}

		// If data is available, append data to url for get requests
		if ( s.data && s.type.toLowerCase() == "get" ) {
			s.url += (s.url.match(/\?/) ? "&" : "?") + s.data;

			// IE likes to send both get and post data, prevent this
			s.data = null;
		}

		// Watch for a new set of requests
		if ( s.global && ! jQuery.active++ )
			jQuery.event.trigger( "ajaxStart" );

		// If we're requesting a remote document
		// and trying to load JSON or Script with a GET
		if ( (!s.url.indexOf("http") || !s.url.indexOf("//")) && s.dataType == "script" && s.type.toLowerCase() == "get" ) {
			var head = document.getElementsByTagName("head")[0];
			var script = document.createElement("script");
			script.src = s.url;
			if (s.scriptCharset)
				script.charset = s.scriptCharset;

			// Handle Script loading
			if ( !jsonp ) {
				var done = false;

				// Attach handlers for all browsers
				script.onload = script.onreadystatechange = function(){
					if ( !done && (!this.readyState || 
							this.readyState == "loaded" || this.readyState == "complete") ) {
						done = true;
						success();
						complete();
						head.removeChild( script );
					}
				};
			}

			head.appendChild(script);

			// We handle everything using the script element injection
			return undefined;
		}

		var requestDone = false;

		// Create the request object; Microsoft failed to properly
		// implement the XMLHttpRequest in IE7, so we use the ActiveXObject when it is available
		var xml = window.ActiveXObject ? new ActiveXObject("Microsoft.XMLHTTP") : new XMLHttpRequest();

		// Open the socket
		xml.open(s.type, s.url, s.async, s.username, s.password);

		// Need an extra try/catch for cross domain requests in Firefox 3
		try {
			// Set the correct header, if data is being sent
			if ( s.data )
				xml.setRequestHeader("Content-Type", s.contentType);

			// Set the If-Modified-Since header, if ifModified mode.
			if ( s.ifModified )
				xml.setRequestHeader("If-Modified-Since",
					jQuery.lastModified[s.url] || "Thu, 01 Jan 1970 00:00:00 GMT" );

			// Set header so the called script knows that it's an XMLHttpRequest
			xml.setRequestHeader("X-Requested-With", "XMLHttpRequest");

			// Set the Accepts header for the server, depending on the dataType
			xml.setRequestHeader("Accept", s.dataType && s.accepts[ s.dataType ] ?
				s.accepts[ s.dataType ] + ", */*" :
				s.accepts._default );
		} catch(e){}

		// Allow custom headers/mimetypes
		if ( s.beforeSend )
			s.beforeSend(xml);
			
		if ( s.global )
			jQuery.event.trigger("ajaxSend", [xml, s]);

		// Wait for a response to come back
		var onreadystatechange = function(isTimeout){
			// The transfer is complete and the data is available, or the request timed out
			if ( !requestDone && xml && (xml.readyState == 4 || isTimeout == "timeout") ) {
				requestDone = true;
				
				// clear poll interval
				if (ival) {
					clearInterval(ival);
					ival = null;
				}
				
				status = isTimeout == "timeout" && "timeout" ||
					!jQuery.httpSuccess( xml ) && "error" ||
					s.ifModified && jQuery.httpNotModified( xml, s.url ) && "notmodified" ||
					"success";

				if ( status == "success" ) {
					// Watch for, and catch, XML document parse errors
					try {
						// process the data (runs the xml through httpData regardless of callback)
						data = jQuery.httpData( xml, s.dataType );
					} catch(e) {
						status = "parsererror";
					}
				}

				// Make sure that the request was successful or notmodified
				if ( status == "success" ) {
					// Cache Last-Modified header, if ifModified mode.
					var modRes;
					try {
						modRes = xml.getResponseHeader("Last-Modified");
					} catch(e) {} // swallow exception thrown by FF if header is not available
	
					if ( s.ifModified && modRes )
						jQuery.lastModified[s.url] = modRes;

					// JSONP handles its own success callback
					if ( !jsonp )
						success();	
				} else
					jQuery.handleError(s, xml, status);

				// Fire the complete handlers
				complete();

				// Stop memory leaks
				if ( s.async )
					xml = null;
			}
		};
		
		if ( s.async ) {
			// don't attach the handler to the request, just poll it instead
			var ival = setInterval(onreadystatechange, 13); 

			// Timeout checker
			if ( s.timeout > 0 )
				setTimeout(function(){
					// Check to see if the request is still happening
					if ( xml ) {
						// Cancel the request
						xml.abort();
	
						if( !requestDone )
							onreadystatechange( "timeout" );
					}
				}, s.timeout);
		}
			
		// Send the data
		try {
			xml.send(s.data);
		} catch(e) {
			jQuery.handleError(s, xml, null, e);
		}
		
		// firefox 1.5 doesn't fire statechange for sync requests
		if ( !s.async )
			onreadystatechange();

		function success(){
			// If a local callback was specified, fire it and pass it the data
			if ( s.success )
				s.success( data, status );

			// Fire the global callback
			if ( s.global )
				jQuery.event.trigger( "ajaxSuccess", [xml, s] );
		}

		function complete(){
			// Process result
			if ( s.complete )
				s.complete(xml, status);

			// The request was completed
			if ( s.global )
				jQuery.event.trigger( "ajaxComplete", [xml, s] );

			// Handle the global AJAX counter
			if ( s.global && ! --jQuery.active )
				jQuery.event.trigger( "ajaxStop" );
		}
		
		// return XMLHttpRequest to allow aborting the request etc.
		return xml;
	},

	handleError: function( s, xml, status, e ) {
		// If a local callback was specified, fire it
		if ( s.error ) s.error( xml, status, e );

		// Fire the global callback
		if ( s.global )
			jQuery.event.trigger( "ajaxError", [xml, s, e] );
	},

	// Counter for holding the number of active queries
	active: 0,

	// Determines if an XMLHttpRequest was successful or not
	httpSuccess: function( r ) {
		try {
			// IE error sometimes returns 1223 when it should be 204 so treat it as success, see #1450
			return !r.status && location.protocol == "file:" ||
				( r.status >= 200 && r.status < 300 ) || r.status == 304 || r.status == 1223 ||
				jQuery.browser.safari && r.status == undefined;
		} catch(e){}
		return false;
	},

	// Determines if an XMLHttpRequest returns NotModified
	httpNotModified: function( xml, url ) {
		try {
			var xmlRes = xml.getResponseHeader("Last-Modified");

			// Firefox always returns 200. check Last-Modified date
			return xml.status == 304 || xmlRes == jQuery.lastModified[url] ||
				jQuery.browser.safari && xml.status == undefined;
		} catch(e){}
		return false;
	},

	httpData: function( r, type ) {
		var ct = r.getResponseHeader("content-type");
		var xml = type == "xml" || !type && ct && ct.indexOf("xml") >= 0;
		var data = xml ? r.responseXML : r.responseText;

		if ( xml && data.documentElement.tagName == "parsererror" )
			throw "parsererror";

		// If the type is "script", eval it in global context
		if ( type == "script" )
			jQuery.globalEval( data );

		// Get the JavaScript object, if JSON is used.
		if ( type == "json" )
			data = eval("(" + data + ")");

		return data;
	},

	// Serialize an array of form elements or a set of
	// key/values into a query string
	param: function( a ) {
		var s = [];

		// If an array was passed in, assume that it is an array
		// of form elements
		if ( a.constructor == Array || a.jquery )
			// Serialize the form elements
			jQuery.each( a, function(){
				s.push( encodeURIComponent(this.name) + "=" + encodeURIComponent( this.value ) );
			});

		// Otherwise, assume that it's an object of key/value pairs
		else
			// Serialize the key/values
			for ( var j in a )
				// If the value is an array then the key names need to be repeated
				if ( a[j] && a[j].constructor == Array )
					jQuery.each( a[j], function(){
						s.push( encodeURIComponent(j) + "=" + encodeURIComponent( this ) );
					});
				else
					s.push( encodeURIComponent(j) + "=" + encodeURIComponent( a[j] ) );

		// Return the resulting serialization
		return s.join("&").replace(/%20/g, "+");
	}

});
jQuery.fn.extend({
	show: function(speed,callback){
		return speed ?
			this.animate({
				height: "show", width: "show", opacity: "show"
			}, speed, callback) :
			
			this.filter(":hidden").each(function(){
				this.style.display = this.oldblock || "";
				if ( jQuery.css(this,"display") == "none" ) {
					var elem = jQuery("<" + this.tagName + " />").appendTo("body");
					this.style.display = elem.css("display");
					// handle an edge condition where css is - div { display:none; } or similar
					if (this.style.display == "none")
						this.style.display = "block";
					elem.remove();
				}
			}).end();
	},
	
	hide: function(speed,callback){
		return speed ?
			this.animate({
				height: "hide", width: "hide", opacity: "hide"
			}, speed, callback) :
			
			this.filter(":visible").each(function(){
				this.oldblock = this.oldblock || jQuery.css(this,"display");
				this.style.display = "none";
			}).end();
	},

	// Save the old toggle function
	_toggle: jQuery.fn.toggle,
	
	toggle: function( fn, fn2 ){
		return jQuery.isFunction(fn) && jQuery.isFunction(fn2) ?
			this._toggle( fn, fn2 ) :
			fn ?
				this.animate({
					height: "toggle", width: "toggle", opacity: "toggle"
				}, fn, fn2) :
				this.each(function(){
					jQuery(this)[ jQuery(this).is(":hidden") ? "show" : "hide" ]();
				});
	},
	
	slideDown: function(speed,callback){
		return this.animate({height: "show"}, speed, callback);
	},
	
	slideUp: function(speed,callback){
		return this.animate({height: "hide"}, speed, callback);
	},

	slideToggle: function(speed, callback){
		return this.animate({height: "toggle"}, speed, callback);
	},
	
	fadeIn: function(speed, callback){
		return this.animate({opacity: "show"}, speed, callback);
	},
	
	fadeOut: function(speed, callback){
		return this.animate({opacity: "hide"}, speed, callback);
	},
	
	fadeTo: function(speed,to,callback){
		return this.animate({opacity: to}, speed, callback);
	},
	
	animate: function( prop, speed, easing, callback ) {
		var optall = jQuery.speed(speed, easing, callback);

		return this[ optall.queue === false ? "each" : "queue" ](function(){
			if ( this.nodeType != 1)
				return false;

			var opt = jQuery.extend({}, optall);
			var hidden = jQuery(this).is(":hidden"), self = this;
			
			for ( var p in prop ) {
				if ( prop[p] == "hide" && hidden || prop[p] == "show" && !hidden )
					return jQuery.isFunction(opt.complete) && opt.complete.apply(this);

				if ( p == "height" || p == "width" ) {
					// Store display property
					opt.display = jQuery.css(this, "display");

					// Make sure that nothing sneaks out
					opt.overflow = this.style.overflow;
				}
			}

			if ( opt.overflow != null )
				this.style.overflow = "hidden";

			opt.curAnim = jQuery.extend({}, prop);
			
			jQuery.each( prop, function(name, val){
				var e = new jQuery.fx( self, opt, name );

				if ( /toggle|show|hide/.test(val) )
					e[ val == "toggle" ? hidden ? "show" : "hide" : val ]( prop );
				else {
					var parts = val.toString().match(/^([+-]=)?([\d+-.]+)(.*)$/),
						start = e.cur(true) || 0;

					if ( parts ) {
						var end = parseFloat(parts[2]),
							unit = parts[3] || "px";

						// We need to compute starting value
						if ( unit != "px" ) {
							self.style[ name ] = (end || 1) + unit;
							start = ((end || 1) / e.cur(true)) * start;
							self.style[ name ] = start + unit;
						}

						// If a +=/-= token was provided, we're doing a relative animation
						if ( parts[1] )
							end = ((parts[1] == "-=" ? -1 : 1) * end) + start;

						e.custom( start, end, unit );
					} else
						e.custom( start, val, "" );
				}
			});

			// For JS strict compliance
			return true;
		});
	},
	
	queue: function(type, fn){
		if ( jQuery.isFunction(type) || ( type && type.constructor == Array )) {
			fn = type;
			type = "fx";
		}

		if ( !type || (typeof type == "string" && !fn) )
			return queue( this[0], type );

		return this.each(function(){
			if ( fn.constructor == Array )
				queue(this, type, fn);
			else {
				queue(this, type).push( fn );
			
				if ( queue(this, type).length == 1 )
					fn.apply(this);
			}
		});
	},

	stop: function(clearQueue, gotoEnd){
		var timers = jQuery.timers;

		if (clearQueue)
			this.queue([]);

		this.each(function(){
			// go in reverse order so anything added to the queue during the loop is ignored
			for ( var i = timers.length - 1; i >= 0; i-- )
				if ( timers[i].elem == this ) {
					if (gotoEnd)
						// force the next step to be the last
						timers[i](true);
					timers.splice(i, 1);
				}
		});

		// start the next in the queue if the last step wasn't forced
		if (!gotoEnd)
			this.dequeue();

		return this;
	}

});

var queue = function( elem, type, array ) {
	if ( !elem )
		return undefined;

	type = type || "fx";

	var q = jQuery.data( elem, type + "queue" );

	if ( !q || array )
		q = jQuery.data( elem, type + "queue", 
			array ? jQuery.makeArray(array) : [] );

	return q;
};

jQuery.fn.dequeue = function(type){
	type = type || "fx";

	return this.each(function(){
		var q = queue(this, type);

		q.shift();

		if ( q.length )
			q[0].apply( this );
	});
};

jQuery.extend({
	
	speed: function(speed, easing, fn) {
		var opt = speed && speed.constructor == Object ? speed : {
			complete: fn || !fn && easing || 
				jQuery.isFunction( speed ) && speed,
			duration: speed,
			easing: fn && easing || easing && easing.constructor != Function && easing
		};

		opt.duration = (opt.duration && opt.duration.constructor == Number ? 
			opt.duration : 
			{ slow: 600, fast: 200 }[opt.duration]) || 400;
	
		// Queueing
		opt.old = opt.complete;
		opt.complete = function(){
			if ( opt.queue !== false )
				jQuery(this).dequeue();
			if ( jQuery.isFunction( opt.old ) )
				opt.old.apply( this );
		};
	
		return opt;
	},
	
	easing: {
		linear: function( p, n, firstNum, diff ) {
			return firstNum + diff * p;
		},
		swing: function( p, n, firstNum, diff ) {
			return ((-Math.cos(p*Math.PI)/2) + 0.5) * diff + firstNum;
		}
	},
	
	timers: [],
	timerId: null,

	fx: function( elem, options, prop ){
		this.options = options;
		this.elem = elem;
		this.prop = prop;

		if ( !options.orig )
			options.orig = {};
	}

});

jQuery.fx.prototype = {

	// Simple function for setting a style value
	update: function(){
		if ( this.options.step )
			this.options.step.apply( this.elem, [ this.now, this ] );

		(jQuery.fx.step[this.prop] || jQuery.fx.step._default)( this );

		// Set display property to block for height/width animations
		if ( this.prop == "height" || this.prop == "width" )
			this.elem.style.display = "block";
	},

	// Get the current size
	cur: function(force){
		if ( this.elem[this.prop] != null && this.elem.style[this.prop] == null )
			return this.elem[ this.prop ];

		var r = parseFloat(jQuery.css(this.elem, this.prop, force));
		return r && r > -10000 ? r : parseFloat(jQuery.curCSS(this.elem, this.prop)) || 0;
	},

	// Start an animation from one number to another
	custom: function(from, to, unit){
		this.startTime = (new Date()).getTime();
		this.start = from;
		this.end = to;
		this.unit = unit || this.unit || "px";
		this.now = this.start;
		this.pos = this.state = 0;
		this.update();

		var self = this;
		function t(gotoEnd){
			return self.step(gotoEnd);
		}

		t.elem = this.elem;

		jQuery.timers.push(t);

		if ( jQuery.timerId == null ) {
			jQuery.timerId = setInterval(function(){
				var timers = jQuery.timers;
				
				for ( var i = 0; i < timers.length; i++ )
					if ( !timers[i]() )
						timers.splice(i--, 1);

				if ( !timers.length ) {
					clearInterval( jQuery.timerId );
					jQuery.timerId = null;
				}
			}, 13);
		}
	},

	// Simple 'show' function
	show: function(){
		// Remember where we started, so that we can go back to it later
		this.options.orig[this.prop] = jQuery.attr( this.elem.style, this.prop );
		this.options.show = true;

		// Begin the animation
		this.custom(0, this.cur());

		// Make sure that we start at a small width/height to avoid any
		// flash of content
		if ( this.prop == "width" || this.prop == "height" )
			this.elem.style[this.prop] = "1px";
		
		// Start by showing the element
		jQuery(this.elem).show();
	},

	// Simple 'hide' function
	hide: function(){
		// Remember where we started, so that we can go back to it later
		this.options.orig[this.prop] = jQuery.attr( this.elem.style, this.prop );
		this.options.hide = true;

		// Begin the animation
		this.custom(this.cur(), 0);
	},

	// Each step of an animation
	step: function(gotoEnd){
		var t = (new Date()).getTime();

		if ( gotoEnd || t > this.options.duration + this.startTime ) {
			this.now = this.end;
			this.pos = this.state = 1;
			this.update();

			this.options.curAnim[ this.prop ] = true;

			var done = true;
			for ( var i in this.options.curAnim )
				if ( this.options.curAnim[i] !== true )
					done = false;

			if ( done ) {
				if ( this.options.display != null ) {
					// Reset the overflow
					this.elem.style.overflow = this.options.overflow;
				
					// Reset the display
					this.elem.style.display = this.options.display;
					if ( jQuery.css(this.elem, "display") == "none" )
						this.elem.style.display = "block";
				}

				// Hide the element if the "hide" operation was done
				if ( this.options.hide )
					this.elem.style.display = "none";

				// Reset the properties, if the item has been hidden or shown
				if ( this.options.hide || this.options.show )
					for ( var p in this.options.curAnim )
						jQuery.attr(this.elem.style, p, this.options.orig[p]);
			}

			// If a callback was provided, execute it
			if ( done && jQuery.isFunction( this.options.complete ) )
				// Execute the complete function
				this.options.complete.apply( this.elem );

			return false;
		} else {
			var n = t - this.startTime;
			this.state = n / this.options.duration;

			// Perform the easing function, defaults to swing
			this.pos = jQuery.easing[this.options.easing || (jQuery.easing.swing ? "swing" : "linear")](this.state, n, 0, 1, this.options.duration);
			this.now = this.start + ((this.end - this.start) * this.pos);

			// Perform the next step of the animation
			this.update();
		}

		return true;
	}

};

jQuery.fx.step = {
	scrollLeft: function(fx){
		fx.elem.scrollLeft = fx.now;
	},

	scrollTop: function(fx){
		fx.elem.scrollTop = fx.now;
	},

	opacity: function(fx){
		jQuery.attr(fx.elem.style, "opacity", fx.now);
	},

	_default: function(fx){
		fx.elem.style[ fx.prop ] = fx.now + fx.unit;
	}
};
// The Offset Method
// Originally By Brandon Aaron, part of the Dimension Plugin
// http://jquery.com/plugins/project/dimensions
jQuery.fn.offset = function() {
	var left = 0, top = 0, elem = this[0], results;
	
	if ( elem ) with ( jQuery.browser ) {
		var parent       = elem.parentNode, 
		    offsetChild  = elem,
		    offsetParent = elem.offsetParent, 
		    doc          = elem.ownerDocument,
		    safari2      = safari && parseInt(version) < 522 && !/adobeair/i.test(userAgent),
		    fixed        = jQuery.css(elem, "position") == "fixed";
	
		// Use getBoundingClientRect if available
		if ( elem.getBoundingClientRect ) {
			var box = elem.getBoundingClientRect();
		
			// Add the document scroll offsets
			add(box.left + Math.max(doc.documentElement.scrollLeft, doc.body.scrollLeft),
				box.top  + Math.max(doc.documentElement.scrollTop,  doc.body.scrollTop));
		
			// IE adds the HTML element's border, by default it is medium which is 2px
			// IE 6 and 7 quirks mode the border width is overwritable by the following css html { border: 0; }
			// IE 7 standards mode, the border is always 2px
			// This border/offset is typically represented by the clientLeft and clientTop properties
			// However, in IE6 and 7 quirks mode the clientLeft and clientTop properties are not updated when overwriting it via CSS
			// Therefore this method will be off by 2px in IE while in quirksmode
			add( -doc.documentElement.clientLeft, -doc.documentElement.clientTop );
	
		// Otherwise loop through the offsetParents and parentNodes
		} else {
		
			// Initial element offsets
			add( elem.offsetLeft, elem.offsetTop );
			
			// Get parent offsets
			while ( offsetParent ) {
				// Add offsetParent offsets
				add( offsetParent.offsetLeft, offsetParent.offsetTop );
			
				// Mozilla and Safari > 2 does not include the border on offset parents
				// However Mozilla adds the border for table or table cells
				if ( mozilla && !/^t(able|d|h)$/i.test(offsetParent.tagName) || safari && !safari2 )
					border( offsetParent );
					
				// Add the document scroll offsets if position is fixed on any offsetParent
				if ( !fixed && jQuery.css(offsetParent, "position") == "fixed" )
					fixed = true;
			
				// Set offsetChild to previous offsetParent unless it is the body element
				offsetChild  = /^body$/i.test(offsetParent.tagName) ? offsetChild : offsetParent;
				// Get next offsetParent
				offsetParent = offsetParent.offsetParent;
			}
		
			// Get parent scroll offsets
			while ( parent && parent.tagName && !/^body|html$/i.test(parent.tagName) ) {
				// Remove parent scroll UNLESS that parent is inline or a table to work around Opera inline/table scrollLeft/Top bug
				if ( !/^inline|table.*$/i.test(jQuery.css(parent, "display")) )
					// Subtract parent scroll offsets
					add( -parent.scrollLeft, -parent.scrollTop );
			
				// Mozilla does not add the border for a parent that has overflow != visible
				if ( mozilla && jQuery.css(parent, "overflow") != "visible" )
					border( parent );
			
				// Get next parent
				parent = parent.parentNode;
			}
		
			// Safari <= 2 doubles body offsets with a fixed position element/offsetParent or absolutely positioned offsetChild
			// Mozilla doubles body offsets with a non-absolutely positioned offsetChild
			if ( (safari2 && (fixed || jQuery.css(offsetChild, "position") == "absolute")) || 
				(mozilla && jQuery.css(offsetChild, "position") != "absolute") )
					add( -doc.body.offsetLeft, -doc.body.offsetTop );
			
			// Add the document scroll offsets if position is fixed
			if ( fixed )
				add(Math.max(doc.documentElement.scrollLeft, doc.body.scrollLeft),
					Math.max(doc.documentElement.scrollTop,  doc.body.scrollTop));
		}

		// Return an object with top and left properties
		results = { top: top, left: left };
	}

	function border(elem) {
		add( jQuery.curCSS(elem, "borderLeftWidth", true), jQuery.curCSS(elem, "borderTopWidth", true) );
	}

	function add(l, t) {
		left += parseInt(l) || 0;
		top += parseInt(t) || 0;
	}

	return results;
};
})();
/*
Copyright 2007 University of Toronto

Licensed under the GNU General Public License or the MIT license.
You may not use this file except in compliance with one these
Licenses.

You may obtain a copy of the GPL and MIT License at
https://source.fluidproject.org/svn/sandbox/tabindex/trunk/LICENSE.txt
*/

// Tabindex normalization
(function ($) {
    // -- Private functions --
    
    var normalizeTabindexName = function () {
	    return $.browser.msie ? "tabIndex" : "tabindex";
	};

	var getValue = function (elements) {
        if (elements.length <= 0) {
            return undefined;
        }

		if (!elements.hasTabindexAttr ()) {
		    return canHaveDefaultTabindex (elements) ? Number (0) : undefined;
		}

        // Get the attribute (.attr () doesn't work for tabIndex in IE) and return it as a number value.
		var value = elements[0].getAttribute (normalizeTabindexName ());
		return Number (value);
	};

	var setValue = function (elements, toIndex) {
		return elements.each (function (i, item) {
			$ (item).attr (normalizeTabindexName (), toIndex);
		});
	};

	var canHaveDefaultTabindex = function (elements) {
       if (elements.length <= 0) {
           return false;
       }

	   return jQuery (elements[0]).is ("a, input, button, select, area, textarea, object");
	};
    
    // -- Public API --
    
    /**
     * Gets the value of the tabindex attribute for the first item, or sets the tabindex value of all elements
     * if toIndex is specified.
     * 
     * @param {String|Number} toIndex
     */
    $.fn.tabindex = function (toIndex) {
		if (toIndex !== null && toIndex !== undefined) {
			return setValue (this, toIndex);
		} else {
			return getValue (this);
		}
	};

    /**
     * Removes the tabindex attribute altogether from each element.
     */
	$.fn.removeTabindex = function () {
		return this.each(function (i, item) {
			$ (item).removeAttr (normalizeTabindexName ());
		});
	};

    /**
     * Determines if an element actually has a tabindex attribute present.
     */
	$.fn.hasTabindexAttr = function () {
	    if (this.length <= 0) {
	        return false;
	    }

	    var attributeNode = this[0].getAttributeNode (normalizeTabindexName ());
        return attributeNode ? attributeNode.specified : false;
	};

    /**
     * Determines if an element either has a tabindex attribute or is naturally tab-focussable.
     */
	$.fn.hasTabindex = function () {
        return this.hasTabindexAttr () || canHaveDefaultTabindex (this);
	};
})(jQuery);


// Keyboard navigation
(function ($) {    
    // Public, static constants needed by the rest of the library.
    $.a11y = $.a11y || {};

    $.a11y.keys = {
        UP: 38,
        DOWN: 40,
        LEFT: 37,
        RIGHT: 39,
        SPACE: 32,
        ENTER: 13,
        TAB: 9,
        CTRL: 17,
        SHIFT: 16,
        ALT: 18
    };

    $.a11y.orientation = {
        HORIZONTAL: 0,
        VERTICAL: 1,
        BOTH: 2
    };

    // Private constants.
    var NAMESPACE_KEY = "keyboard-a11y";
    var CONTEXT_KEY = "selectionContext";
    var HANDLERS_KEY = "userHandlers";
    var ACTIVATE_KEY = "defaultActivate";

    var UP_DOWN_KEYMAP = {
        next: $.a11y.keys.DOWN,
        previous: $.a11y.keys.UP
    };

    var LEFT_RIGHT_KEYMAP = {
        next: $.a11y.keys.RIGHT,
        previous: $.a11y.keys.LEFT
    };

    // Private functions.
    var unwrap = function (element) {
        return (element.jquery) ? element[0] : element; // Unwrap the element if it's a jQuery.
    };

    var cleanUpWhenLeavingContainer = function (userHandlers, selectionContext, shouldRememberSelectionState) {
        if (userHandlers.willLeaveContainer) {
            userHandlers.willLeaveContainer (selectionContext.activeItem);
        } else if (userHandlers.willUnselect) {
            userHandlers.willUnselect (selectionContext.activeItem);
        }

        if (!shouldRememberSelectionState) {
            selectionContext.activeItem = null;
        }
    };

    var checkForModifier = function (binding, evt) {
        // If no modifier was specified, just return true.
        if (!binding.modifier) {
            return true;
        }

        var modifierKey = binding.modifier;
        var isCtrlKeyPresent = (modifierKey && evt.ctrlKey);
        var isAltKeyPresent = (modifierKey && evt.altKey);
        var isShiftKeyPresent = (modifierKey && evt.shiftKey);

        return (isCtrlKeyPresent || isAltKeyPresent || isShiftKeyPresent);
    };

    var activationHandler = function (binding) {
        return function (evt) {
            if (evt.which === binding.key && binding.activateHandler && checkForModifier (binding, evt)) {
                binding.activateHandler (evt.target, evt);
                evt.preventDefault ();
            }
        };
    };

    /**
     * Does the work of selecting an element and delegating to the client handler.
     */
    var drawSelection = function (elementToSelect, handler) {
        if (handler) {
            handler (elementToSelect);
        }
    };

    /**
     * Does does the work of unselecting an element and delegating to the client handler.
     */
    var eraseSelection = function (selectedElement, handler) {
        if (handler) {
            handler (selectedElement);
        }
    };

    var unselectElement = function (selectedElement, selectionContext, userHandlers) {
        eraseSelection (selectedElement, userHandlers.willUnselect);
    };

    var selectElement = function (elementToSelect, selectionContext, userHandlers) {
        // It's possible that we're being called programmatically, in which case we should clear any previous selection.
        if (selectionContext.activeItem) {
            unselectElement (selectionContext.activeItem, selectionContext, userHandlers);
        }

        elementToSelect = unwrap (elementToSelect);

        // Next check if the element is a known selectable. If not, do nothing.
        if (selectionContext.selectables.index(elementToSelect) === -1) {
           return;
        }

        // Select the new element.
        selectionContext.activeItem = elementToSelect;
        drawSelection (elementToSelect, userHandlers.willSelect);
    };

    var selectableFocusHandler = function (selectionContext, userHandlers) {
        return function (evt) {
            selectElement (evt.target, selectionContext, userHandlers);

            // Force focus not to bubble on some browsers.
            return evt.stopPropagation ();
        };
    };

    var selectableBlurHandler = function (selectionContext, userHandlers) {
        return function (evt) {
            unselectElement (evt.target, selectionContext, userHandlers);

            // Force blur not to bubble on some browsers.
            return evt.stopPropagation ();
        };
    };

    var focusNextElement = function (selectionContext) {
        var elements = selectionContext.selectables;
        var activeItem = selectionContext.activeItem;

        var currentSelectionIdx = (!activeItem) ? -1 : elements.index (activeItem);
        var nextIndex = currentSelectionIdx + 1;
        nextIndex = (nextIndex >= elements.length) ? nextIndex = 0 : nextIndex; // Wrap around to the beginning if needed.

        elements.eq (nextIndex).focus ();
    };

    var focusPreviousElement = function (selectionContext) {
        var elements = selectionContext.selectables;
        var activeItem = selectionContext.activeItem;

        var currentSelectionIdx = (!activeItem) ? 0 : elements.index (activeItem);
        var previousIndex = currentSelectionIdx - 1;
        previousIndex = (previousIndex < 0) ? elements.length - 1 : previousIndex; // Wrap around to the end if necessary.

        elements.eq (previousIndex).focus ();
    };

    var arrowKeyHandler = function (selectionContext, keyMap, userHandlers) {
        return function (evt) {
            if (evt.which === keyMap.next) {
                focusNextElement (selectionContext);
                evt.preventDefault ();
            } else if (evt.which === keyMap.previous) {
                focusPreviousElement (selectionContext);
                evt.preventDefault ();
            }
        };
    };

    var getKeyMapForDirection = function (direction) {
        // Determine the appropriate mapping for next and previous based on the specified direction.
        var keyMap;
        if (direction === $.a11y.orientation.HORIZONTAL) {
            keyMap = LEFT_RIGHT_KEYMAP;
        } else {
            // Assume vertical in any other case.
            keyMap = UP_DOWN_KEYMAP;
        }

        return keyMap;
    };

    var containerFocusHandler = function (selectionContext, container, shouldAutoSelectFirstChild) {
        return function (evt) {
            var shouldSelect = (shouldAutoSelectFirstChild.constructor === Function) ? shouldAutoSelectFirstChild () : shouldAutoSelectFirstChild;

            // Override the autoselection if we're on the way out of the container.
            if (selectionContext.focusIsLeavingContainer) {
                shouldSelect = false;
            }

            // This target check works around the fact that sometimes focus bubbles, even though it shouldn't.
            if (shouldSelect && evt.target === container.get(0)) {
                if (!selectionContext.activeItem) {
                    focusNextElement (selectionContext);
                } else {
                    jQuery (selectionContext.activeItem).focus ();
                }
            }

           // Force focus not to bubble on some browsers.
           return evt.stopPropagation ();
        };
    };

    var containerBlurHandler = function (selectionContext) {
        return function (evt) {
            selectionContext.focusIsLeavingContainer = false;

            // Force blur not to bubble on some browsers.
            return evt.stopPropagation ();
        };
    };

    var makeElementsTabFocussable = function (elements) {
        // If each element doesn't have a tabindex, or has one set to a negative value, set it to 0.
        elements.each (function (idx, item) {
            item = $ (item);
            if (!item.hasTabindex () || (item.tabindex () < 0)) {
                item.tabindex (0);
            }
        });
    };

    var makeElementsActivatable = function (elements, onActivateHandler, defaultKeys, options) {
        // Create bindings for each default key.
        var bindings = [];
        $ (defaultKeys).each (function (index, key) {
            bindings.push ({
                modifier: null,
                key: key,
                activateHandler: onActivateHandler
            });
        });

        // Merge with any additional key bindings.
        if (options && options.additionalBindings) {
            bindings = bindings.concat (options.additionalBindings);
        }

        // Add listeners for each key binding.
        for (var i = 0; i < bindings.length; i = i + 1) {
            var binding = bindings[i];
            elements.keydown (activationHandler (binding));
        }
    };

    var tabKeyHandler = function (userHandlers, selectionContext, shouldRememberSelectionState) {
        return function (evt) {
            if (evt.which !== $.a11y.keys.TAB) {
                return;
            }

            cleanUpWhenLeavingContainer (userHandlers, selectionContext, shouldRememberSelectionState);

            // Catch Shift-Tab and note that focus is on its way out of the container.
            if (evt.shiftKey) {
                selectionContext.focusIsLeavingContainer = true;
            }
        };
    };

    var makeElementsSelectable = function (container, selectableElements, handlers, defaults, options) {
        // Create empty an handlers and use default options where not specified.
        handlers = handlers || {};
        var mergedOptions = $.extend ({}, defaults, options);

        var keyMap = getKeyMapForDirection (mergedOptions.direction);

        // Context stores the currently active item (undefined to start) and list of selectables.
        var selectionContext = {
            activeItem: undefined,
            selectables: selectableElements,
            focusIsLeavingContainer: false
        };

        // Add various handlers to the container.
        container.keydown (arrowKeyHandler (selectionContext, keyMap, handlers));
        container.keydown (tabKeyHandler (handlers, selectionContext, mergedOptions.rememberSelectionState));
        container.focus (containerFocusHandler (selectionContext, container, mergedOptions.autoSelectFirstItem));
        container.blur (containerBlurHandler (selectionContext));

        // Remove selectables from the tab order and add focus/blur handlers
        selectableElements.tabindex(-1);
        selectableElements.focus (selectableFocusHandler (selectionContext, handlers));
        selectableElements.blur (selectableBlurHandler (selectionContext, handlers));

        return selectionContext;
    };

    var createDefaultActivationHandler = function (activatables, userActivateHandler) {
        return function (elementToActivate) {
            if (!userActivateHandler) {
                return;
            }

            elementToActivate = unwrap (elementToActivate);
            if (activatables.index (elementToActivate) === -1) {
                return;
            }

            userActivateHandler (elementToActivate);
        };
    };

    /**
     * Gets stored state from the jQuery instance's data map.
     */
    var getData = function (aJQuery, key) {
        var data = aJQuery.data (NAMESPACE_KEY);
        return data ? data[key] : undefined;
    };

    /**
     * Stores state in the jQuery instance's data map.
     */
    var setData = function (aJQuery, key, value) {
        var data = aJQuery.data (NAMESPACE_KEY) || {};
        data[key] = value;
        aJQuery.data (NAMESPACE_KEY, data);
    };

    // Public API.
    /**
     * Makes all matched elements available in the tab order by setting their tabindices to "0".
     */
    $.fn.tabbable = function () {
        makeElementsTabFocussable (this);
        return this;
    };

    /**
     * Makes all matched elements selectable with the arrow keys.
     * Supply your own handlers object with willSelect: and willUnselect: properties for custom behaviour.
     * Options provide configurability, including direction: and autoSelectFirstItem:
     * Currently supported directions are jQuery.a11y.directions.HORIZONTAL and VERTICAL.
     */
    $.fn.selectable = function (container, handlers, options) {
        var ctx = makeElementsSelectable ($ (container), this, handlers, this.selectable.defaults, options);
        setData (this, CONTEXT_KEY, ctx);
        setData (this, HANDLERS_KEY, handlers);
        return this;
    };

    /**
     * Makes all matched elements activatable with the Space and Enter keys.
     * Provide your own hanlder function for custom behaviour.
     * Options allow you to provide a list of additionalActivationKeys.
     */
    $.fn.activatable = function (fn, options) {
        makeElementsActivatable (this, fn, this.activatable.defaults.keys, options);
        setData (this, ACTIVATE_KEY, createDefaultActivationHandler (this, fn));
        return this;
    };

    /**
     * Selects the specified element.
     */
    $.fn.select = function (elementToSelect) {
        elementToSelect.focus ();
        return this;
    };

    /**
     * Selects the next matched element.
     */
    $.fn.selectNext = function () {
        focusNextElement (getData (this, CONTEXT_KEY));
        return this;
    };

    /**
     * Selects the previous matched element.
     */
    $.fn.selectPrevious = function () {
        focusPreviousElement (getData (this, CONTEXT_KEY));
        return this;
    };

    /**
     * Returns the currently selected item wrapped as a jQuery object.
     */
    $.fn.currentSelection = function () {
        return $ (getData (this, CONTEXT_KEY).activeItem);
    };

    /**
     * Activates the specified element.
     */
    $.fn.activate = function (elementToActivate) {
        var handler = getData (this, ACTIVATE_KEY);
        handler (elementToActivate);
        return this;
    };

    // Public Defaults.
    $.fn.activatable.defaults = {
        keys: [$.a11y.keys.ENTER, $.a11y.keys.SPACE]
    };

    $.fn.selectable.defaults = {
        direction: this.VERTICAL,
        autoSelectFirstItem: true,
        rememberSelectionState: true
    };
}) (jQuery);
/***
 Copyright 2007 Chris Hoffman
 
 This software is dual licensed under the MIT (MIT-LICENSE.txt)
 and GPL (GPL-LICENSE.txt) licenses.

 You may obtain a copy of the GPL License at 
 https://source.fluidproject.org/svn/fluid/components/trunk/src/webapp/fluid-components/js/jquery/GPL-LICENSE.txt

 You may obtain a copy of the MIT License at 
 https://source.fluidproject.org/svn/fluid/components/trunk/src/webapp/fluid-components/js/jquery/MIT-LICENSE.txt
***/

/******************************
  Commonly used variable names

  args - an array of function arguments
  attr - an attribute name
  el   - a DOM element
  i    - an array index
  jq   - a jQuery object
  val  - a value
 ******************************/

(function($){

var ariaStatesNS = "http://www.w3.org/2005/07/aaa";

var xhtmlRoles = [
	"main",
	"secondary",
	"navigation",
	"banner",
	"contentinfo",
	"statements",
	"note",
	"seealso",
	"search"
];

var xhtmlRolesRegex = new RegExp("^" + xhtmlRoles.join("|") + "$");

var isFF2 = $.browser.mozilla && (parseFloat($.browser.version) < 1.9);

var ariaStateAttr = (function() {
	if (isFF2) {
		// Firefox < v3, so use States & Properties namespace.
		return function(jq, attr, val) {
			if (typeof val != "undefined") {
				jq.each(function(i, el) {
					el.setAttributeNS(ariaStatesNS, attr, val);
				});
  			} else {
 				return jq.get(0).getAttributeNS(ariaStatesNS, attr);
			}
		};
	} else {
		// Use the aria- attribute form.
		return function(jq, attr, val) {
			if (typeof val != "undefined") {
				jq.each(function(i, el) {
					$(el).attr("aria-" + attr, val);
				});
			} else {
				return jq.attr("aria-" + attr);
			}
		};
	}
})();
  
$.fn.extend({  
	ariaRole : function(role){
		var jq = this;
		if (role) {

			// Add the role: prefix, unless it's one of the XHTML Role Module roles

			role = (xhtmlRolesRegex.test(role) || !isFF2) ? role : "wairole:" + role;

			jq.each(function(i, el) {
				$(el).attr("role", role);
			});
			return jq;
		} else {
			var role = jq.eq(0).attr("role");
			if (role) {
				role = role.replace(/^wairole:/, "");
			}
			return role;
		}
	},

	ariaState : function() {
		var args = arguments;
		var jq = this;
		if (args.length == 2) {

			// State and value were given as separate arguments.

			jq.each(function(i, el) {
				ariaStateAttr($(el), args[0], args[1]);
			});
			return jq;
		} else {
			if (typeof args[0] == "string") {

				// Just a state was supplied, so return a value.

				return ariaStateAttr(jq.eq(0), args[0]);
			} else {

				// An object was supplied. Set states and values based on the keys/values.

				jq.each(function(i, el){
					$.each(args[0], function(state, val) {
						$(el).ariaState(state, val);
					});
				});
				return jq;
			}
 		}
  	}
});

// Add :ariaRole(role) and :ariaState(state[=value]) filters.

$.extend($.expr[':'], {
	// a is the element being tested, m[3] is the argument to the selector.

	ariaRole : "jQuery(a).ariaRole()==m[3]",
	ariaState : "jQuery(a).ariaState(m[3].split(/=/)[0])==(/=/.test(m[3])?m[3].split(/=/)[1]:'true')"
});

})(jQuery);
/* Copyright (c) 2007 Paul Bakaus (paul.bakaus@googlemail.com) and Brandon Aaron (brandon.aaron@gmail.com || http://brandonaaron.net)
 * Dual licensed under the MIT (http://www.opensource.org/licenses/mit-license.php)
 * and GPL (http://www.opensource.org/licenses/gpl-license.php) licenses.
 *
 * $LastChangedDate: 2008-02-28 05:49:55 -0500 (Thu, 28 Feb 2008) $
 * $Rev: 4841 $
 *
 * Version: @VERSION
 *
 * Requires: jQuery 1.2+
 */

(function($){
	
$.dimensions = {
	version: '@VERSION'
};

// Create innerHeight, innerWidth, outerHeight and outerWidth methods
$.each( [ 'Height', 'Width' ], function(i, name){
	
	// innerHeight and innerWidth
	$.fn[ 'inner' + name ] = function() {
		if (!this[0]) return;
		
		var torl = name == 'Height' ? 'Top'    : 'Left',  // top or left
		    borr = name == 'Height' ? 'Bottom' : 'Right'; // bottom or right
		
		return this.css('display') != 'none' ? this[0]['client' + name] : num( this, name.toLowerCase() ) + num(this, 'padding' + torl) + num(this, 'padding' + borr);
	};
	
	// outerHeight and outerWidth
	$.fn[ 'outer' + name ] = function(options) {
		if (!this[0]) return;
		
		var torl = name == 'Height' ? 'Top'    : 'Left',  // top or left
		    borr = name == 'Height' ? 'Bottom' : 'Right'; // bottom or right
		
		options = $.extend({ margin: false }, options || {});
		
		var val = this.css('display') != 'none' ? 
				this[0]['offset' + name] : 
				num( this, name.toLowerCase() )
					+ num(this, 'border' + torl + 'Width') + num(this, 'border' + borr + 'Width')
					+ num(this, 'padding' + torl) + num(this, 'padding' + borr);
		
		return val + (options.margin ? (num(this, 'margin' + torl) + num(this, 'margin' + borr)) : 0);
	};
});

// Create scrollLeft and scrollTop methods
$.each( ['Left', 'Top'], function(i, name) {
	$.fn[ 'scroll' + name ] = function(val) {
		if (!this[0]) return;
		
		return val != undefined ?
		
			// Set the scroll offset
			this.each(function() {
				this == window || this == document ?
					window.scrollTo( 
						name == 'Left' ? val : $(window)[ 'scrollLeft' ](),
						name == 'Top'  ? val : $(window)[ 'scrollTop'  ]()
					) :
					this[ 'scroll' + name ] = val;
			}) :
			
			// Return the scroll offset
			this[0] == window || this[0] == document ?
				self[ (name == 'Left' ? 'pageXOffset' : 'pageYOffset') ] ||
					$.boxModel && document.documentElement[ 'scroll' + name ] ||
					document.body[ 'scroll' + name ] :
				this[0][ 'scroll' + name ];
	};
});

$.fn.extend({
	position: function() {
		var left = 0, top = 0, elem = this[0], offset, parentOffset, offsetParent, results;
		
		if (elem) {
			// Get *real* offsetParent
			offsetParent = this.offsetParent();
			
			// Get correct offsets
			offset       = this.offset();
			parentOffset = offsetParent.offset();
			
			// Subtract element margins
			offset.top  -= num(elem, 'marginTop');
			offset.left -= num(elem, 'marginLeft');
			
			// Add offsetParent borders
			parentOffset.top  += num(offsetParent, 'borderTopWidth');
			parentOffset.left += num(offsetParent, 'borderLeftWidth');
			
			// Subtract the two offsets
			results = {
				top:  offset.top  - parentOffset.top,
				left: offset.left - parentOffset.left
			};
		}
		
		return results;
	},
	
	offsetParent: function() {
		var offsetParent = this[0].offsetParent;
		while ( offsetParent && (!/^body|html$/i.test(offsetParent.tagName) && $.css(offsetParent, 'position') == 'static') )
			offsetParent = offsetParent.offsetParent;
		return $(offsetParent);
	}
});

function num(el, prop) {
	return parseInt($.curCSS(el.jquery?el[0]:el,prop,true))||0;
};

})(jQuery);/*
 * jQuery UI @VERSION
 *
 * Copyright (c) 2008 Paul Bakaus (ui.jquery.com)
 * Dual licensed under the MIT (MIT-LICENSE.txt)
 * and GPL (GPL-LICENSE.txt) licenses.
 *
 * http://docs.jquery.com/UI
 *
 * $Date: 2008-03-30 23:47:11 -0400 (Sun, 30 Mar 2008) $
 * $Rev: 5146 $
 */
;(function($) {

	//If the UI scope is not available, add it
	$.ui = $.ui || {};
	
	//Add methods that are vital for all mouse interaction stuff (plugin registering)
	$.extend($.ui, {
		plugin: {
			add: function(module, option, set) {
				var proto = $.ui[module].prototype;
				for(var i in set) {
					proto.plugins[i] = proto.plugins[i] || [];
					proto.plugins[i].push([option, set[i]]);
				}
			},
			call: function(instance, name, arguments) {
				var set = instance.plugins[name]; if(!set) return;
				for (var i = 0; i < set.length; i++) {
					if (instance.options[set[i][0]]) set[i][1].apply(instance.element, arguments);
				}
			}	
		},
		cssCache: {},
		css: function(name) {
			if ($.ui.cssCache[name]) return $.ui.cssCache[name];
			var tmp = $('<div class="ui-resizable-gen">').addClass(name).css({position:'absolute', top:'-5000px', left:'-5000px', display:'block'}).appendTo('body');
			
			//if (!$.browser.safari)
				//tmp.appendTo('body'); 
			
			//Opera and Safari set width and height to 0px instead of auto
			//Safari returns rgba(0,0,0,0) when bgcolor is not set
			$.ui.cssCache[name] = !!(
				(!/auto|default/.test(tmp.css('cursor')) || (/^[1-9]/).test(tmp.css('height')) || (/^[1-9]/).test(tmp.css('width')) || 
				!(/none/).test(tmp.css('backgroundImage')) || !(/transparent|rgba\(0, 0, 0, 0\)/).test(tmp.css('backgroundColor')))
			);
			try { $('body').get(0).removeChild(tmp.get(0));	} catch(e){}
			return $.ui.cssCache[name];
		},
		disableSelection: function(e) {
			e.unselectable = "on";
			e.onselectstart = function() {	return false; };
			if (e.style) e.style.MozUserSelect = "none";
		},
		enableSelection: function(e) {
			e.unselectable = "off";
			e.onselectstart = function() { return true; };
			if (e.style) e.style.MozUserSelect = "";
		},
		hasScroll: function(e, a) {
      		var scroll = /top/.test(a||"top") ? 'scrollTop' : 'scrollLeft', has = false;
      		if (e[scroll] > 0) return true; e[scroll] = 1;
      		has = e[scroll] > 0 ? true : false; e[scroll] = 0;
      		return has; 
    	}
	});

	/******* fn scope modifications ********/

	$.each( ['Left', 'Top'], function(i, name) {
		if(!$.fn['scroll'+name]) $.fn['scroll'+name] = function(v) {
			return v != undefined ?
				this.each(function() { this == window || this == document ? window.scrollTo(name == 'Left' ? v : $(window)['scrollLeft'](), name == 'Top'  ? v : $(window)['scrollTop']()) : this['scroll'+name] = v; }) :
				this[0] == window || this[0] == document ? self[(name == 'Left' ? 'pageXOffset' : 'pageYOffset')] || $.boxModel && document.documentElement['scroll'+name] || document.body['scroll'+name] : this[0][ 'scroll' + name ];
		};
	});

	var _remove = $.fn.remove;
	$.fn.extend({
		position: function() {
			var offset       = this.offset();
			var offsetParent = this.offsetParent();
			var parentOffset = offsetParent.offset();

			return {
				top:  offset.top - num(this[0], 'marginTop')  - parentOffset.top - num(offsetParent, 'borderTopWidth'),
				left: offset.left - num(this[0], 'marginLeft')  - parentOffset.left - num(offsetParent, 'borderLeftWidth')
			};
		},
		offsetParent: function() {
			var offsetParent = this[0].offsetParent;
			while ( offsetParent && (!/^body|html$/i.test(offsetParent.tagName) && $.css(offsetParent, 'position') == 'static') )
				offsetParent = offsetParent.offsetParent;
			return $(offsetParent);
		},
		mouseInteraction: function(o) {
			return this.each(function() {
				new $.ui.mouseInteraction(this, o);
			});
		},
		removeMouseInteraction: function(o) {
			return this.each(function() {
				if($.data(this, "ui-mouse"))
					$.data(this, "ui-mouse").destroy();
			});
		},
		remove: function() {
			this.trigger("remove");
			return _remove.apply(this, arguments );
		}
	});
	
	function num(el, prop) {
		return parseInt($.curCSS(el.jquery?el[0]:el,prop,true))||0;
	};
	
	
	/********** Mouse Interaction Plugin *********/
	
	$.ui.mouseInteraction = function(element, options) {
	
		var self = this;
		this.element = element;

		$.data(this.element, "ui-mouse", this);
		this.options = $.extend({}, options);
		
		$(element).bind('mousedown.draggable', function() { return self.click.apply(self, arguments); });
		if($.browser.msie) $(element).attr('unselectable', 'on'); //Prevent text selection in IE
		
		// prevent draggable-options-delay bug #2553
		$(element).mouseup(function() {
			if(self.timer) clearInterval(self.timer);
		});
	};
	
	$.extend($.ui.mouseInteraction.prototype, {
		
		destroy: function() { $(this.element).unbind('mousedown.draggable'); },
		trigger: function() { return this.click.apply(this, arguments); },
		click: function(e) {
			
			if(
				   e.which != 1 //only left click starts dragging
				|| $.inArray(e.target.nodeName.toLowerCase(), this.options.dragPrevention || []) != -1 // Prevent execution on defined elements
				|| (this.options.condition && !this.options.condition.apply(this.options.executor || this, [e, this.element])) //Prevent execution on condition
			) return true;
				
			var self = this;
			var initialize = function() {
				self._MP = { left: e.pageX, top: e.pageY }; // Store the click mouse position
				$(document).bind('mouseup.draggable', function() { return self.stop.apply(self, arguments); });
				$(document).bind('mousemove.draggable', function() { return self.drag.apply(self, arguments); });
				
				if(!self.initalized && Math.abs(self._MP.left-e.pageX) >= self.options.distance || Math.abs(self._MP.top-e.pageY) >= self.options.distance) {				
					if(self.options.start) self.options.start.call(self.options.executor || self, e, self.element);
					if(self.options.drag) self.options.drag.call(self.options.executor || self, e, this.element); //This is actually not correct, but expected
					self.initialized = true;
				}
			};

			if(this.options.delay) {
				if(this.timer) clearInterval(this.timer);
				this.timer = setTimeout(initialize, this.options.delay);
			} else {
				initialize();
			}
				
			return false;
			
		},
		stop: function(e) {			
			
			var o = this.options;
			if(!this.initialized) return $(document).unbind('mouseup.draggable').unbind('mousemove.draggable');

			if(this.options.stop) this.options.stop.call(this.options.executor || this, e, this.element);
			$(document).unbind('mouseup.draggable').unbind('mousemove.draggable');
			this.initialized = false;
			return false;
			
		},
		drag: function(e) {

			var o = this.options;
			if ($.browser.msie && !e.button) return this.stop.apply(this, [e]); // IE mouseup check
			
			if(!this.initialized && (Math.abs(this._MP.left-e.pageX) >= o.distance || Math.abs(this._MP.top-e.pageY) >= o.distance)) {				
				if(this.options.start) this.options.start.call(this.options.executor || this, e, this.element);
				this.initialized = true;
			} else {
				if(!this.initialized) return false;
			}

			if(o.drag) o.drag.call(this.options.executor || this, e, this.element);
			return false;
			
		}
	});
	
})(jQuery);
 ;(function($) {
	
	//If the UI scope is not available, add it
	$.ui = $.ui || {};

	$.fn.extend({
		dialog: function(options, data) {
			var args = Array.prototype.slice.call(arguments, 1);

			return this.each(function() {
				if (typeof options == "string") {
					var dialog = $.data(this, "ui-dialog") ||
						$.data($(this).parents(".ui-dialog:first").find(".ui-dialog-content")[0], "ui-dialog");
					dialog[options].apply(dialog, args);

				// INIT with optional options
				} else if (!$(this).is(".ui-dialog-content"))
					new $.ui.dialog(this, options);
			});
		}
	});

	$.ui.dialog = function(el, options) {
		
		this.options = options = $.extend({},
			$.ui.dialog.defaults,
			options && options.modal ? {resizable: false} : {},
			options);
		this.element = el;
		var self = this; //Do bindings

		$.data(this.element, "ui-dialog", this);
		
		$(el).bind("setData.dialog", function(event, key, value){
			options[key] = value;
		}).bind("getData.dialog", function(event, key){
			return options[key];
		});

		var uiDialogContent = $(el).addClass('ui-dialog-content');

		if (!uiDialogContent.parent().length) {
			uiDialogContent.appendTo('body');
		}
		uiDialogContent
			.wrap(document.createElement('div'))
			.wrap(document.createElement('div'));
		var uiDialogContainer = uiDialogContent.parent().addClass('ui-dialog-container').css({position: 'relative'});
		var uiDialog = this.uiDialog = uiDialogContainer.parent().hide()
			.addClass('ui-dialog')
			.css({position: 'absolute', width: options.width, height: options.height, overflow: 'hidden'}); 

		var classNames = uiDialogContent.attr('className').split(' ');

		// Add content classes to dialog, to inherit theme at top level of element
		$.each(classNames, function(i, className) {
			if (className != 'ui-dialog-content')
				uiDialog.addClass(className);
		});
		
		if (options.resizable && $.fn.resizable) {
			uiDialog.append('<div class="ui-resizable-n ui-resizable-handle"></div>')
				.append('<div class="ui-resizable-s ui-resizable-handle"></div>')
				.append('<div class="ui-resizable-e ui-resizable-handle"></div>')
				.append('<div class="ui-resizable-w ui-resizable-handle"></div>')
				.append('<div class="ui-resizable-ne ui-resizable-handle"></div>')
				.append('<div class="ui-resizable-se ui-resizable-handle"></div>')
				.append('<div class="ui-resizable-sw ui-resizable-handle"></div>')
				.append('<div class="ui-resizable-nw ui-resizable-handle"></div>');
			uiDialog.resizable({ maxWidth: options.maxWidth, maxHeight: options.maxHeight, minWidth: options.minWidth, minHeight: options.minHeight });
		}

		uiDialogContainer.prepend('<div class="ui-dialog-titlebar"></div>');
		var uiDialogTitlebar = $('.ui-dialog-titlebar', uiDialogContainer);
		var title = (options.title) ? options.title : (uiDialogContent.attr('title')) ? uiDialogContent.attr('title') : '';
		uiDialogTitlebar.append('<span class="ui-dialog-title">' + title + '</span>');
		uiDialogTitlebar.append('<a href="#" class="ui-dialog-titlebar-close"><span>X</span></a>');
		this.uiDialogTitlebarClose = $('.ui-dialog-titlebar-close', uiDialogTitlebar)
			.hover(function() { $(this).addClass('ui-dialog-titlebar-close-hover'); }, 
			       function() { $(this).removeClass('ui-dialog-titlebar-close-hover'); })
			.mousedown(function(ev) {
				ev.stopPropagation();
			})
			.click(function() {
				self.close();
				return false;
			})
			.keydown(function(ev) {
				var ESC = 27;
				ev.keyCode && ev.keyCode == ESC && self.close(); 
			});

		var l = 0;
		$.each(options.buttons, function() { l = 1; return false; });
		if (l == 1) {
			uiDialog.append('<div class="ui-dialog-buttonpane"></div>');
			var uiDialogButtonPane = $('.ui-dialog-buttonpane', uiDialog);
			$.each(options.buttons, function(name, value) {
				var btn = $(document.createElement('button')).text(name).click(value);
				uiDialogButtonPane.append(btn);
			});
		}
	
		if (options.draggable && $.fn.draggable) {
			uiDialog.draggable({
				handle: '.ui-dialog-titlebar',
				start: function() {
					self.activate();
				}
			});
		}
		uiDialog.mousedown(function() {
			self.activate();
		});
		uiDialogTitlebar.click(function() {
			self.activate();
		});
		
		// TODO: determine if this is necessary for modal dialogs
		options.bgiframe && $.fn.bgiframe && uiDialog.bgiframe();
		
		this.open = function() {
			options.modal && overlay.show(self, options.overlay);
			uiDialog.appendTo('body');
			var wnd = $(window), doc = $(document), top = doc.scrollTop(), left = doc.scrollLeft();
			if (options.position.constructor == Array) {
				// [x, y]
				top += options.position[1];
				left += options.position[0];
			} else {
				switch (options.position) {
					case 'center':
						top += (wnd.height() / 2) - (uiDialog.height() / 2);
						left += (wnd.width() / 2) - (uiDialog.width() / 2);
						break;
					case 'top':
						top += 0;
						left += (wnd.width() / 2) - (uiDialog.width() / 2);
						break;
					case 'right':
						top += (wnd.height() / 2) - (uiDialog.height() / 2);
						left += (wnd.width()) - (uiDialog.width());
						break;
					case 'bottom':
						top += (wnd.height()) - (uiDialog.height());
						left += (wnd.width() / 2) - (uiDialog.width() / 2);
						break;
					case 'left':
						top += (wnd.height() / 2) - (uiDialog.height() / 2);
						left += 0;
						break;
					default:
						//center
						top += (wnd.height() / 2) - (uiDialog.height() / 2);
						left += (wnd.width() / 2) - (uiDialog.width() / 2);
				}
			}
			top = top < doc.scrollTop() ? doc.scrollTop() : top;
			uiDialog.css({top: top, left: left});
			uiDialog.show();
			self.activate();

			// CALLBACK: open
			var openEV = null;
			var openUI = {
				options: options
			};
			this.uiDialogTitlebarClose.focus();
			$(this.element).triggerHandler("dialogopen", [openEV, openUI], options.open);
		};

		this.activate = function() {
			var maxZ = 0;
			$('.ui-dialog:visible').each(function() {
				maxZ = Math.max(maxZ, parseInt($(this).css("z-index"),10));
			});
			overlay.$el && overlay.$el.css('z-index', ++maxZ);
			uiDialog.css("z-index", ++maxZ);
		};

		this.close = function() {
			options.modal && overlay.hide();
			uiDialog.hide();

			// CALLBACK: close
			var closeEV = null;
			var closeUI = {
				options: options
			};
			$(this.element).triggerHandler("dialogclose", [closeEV, closeUI], options.close);
		};
		
		if (options.autoOpen)
			this.open();
	};
	
	$.extend($.ui.dialog, {
		defaults: {
			autoOpen: true,
			bgiframe: false,
			buttons: [],
			draggable: true,
			height: 200,
			minHeight: 100,
			minWidth: 150,
			modal: false,
			overlay: {},
			position: 'center',
			resizable: true,
			width: 300
		}
	});
	
	// This is a port of relevant pieces of Mike Alsup's blockUI plugin (http://www.malsup.com/jquery/block/)
	// duplicated here for minimal overlay functionality and no dependency on a non-UI plugin
	var overlay = {
		$el: null,
		events: $.map('focus,mousedown,mouseup,keydown,keypress,click'.split(','),
			function(e) { return e + '.ui-dialog-overlay'; }).join(' '),
		
		show: function(dialog, css) {
			if (this.$el) return;
			
			this.dialog = dialog;
			this.selects = this.ie6 && $('select:visible').css('visibility', 'hidden');
			var width = this.width();
			var height = this.height();
			this.$el = $('<div/>').appendTo(document.body)
				.addClass('ui-dialog-overlay').css($.extend({
					borderWidth: 0, margin: 0, padding: 0,
					position: 'absolute', top: 0, left: 0,
					width: width,
					height: height
				}, css));
			
			// prevent use of anchors and inputs
			$('a, :input').bind(this.events, function() {
				if ($(this).parents('.ui-dialog').length == 0) {
					dialog.uiDialogTitlebarClose.focus();
					return false;
				}
			});
			
			// allow closing by pressing the escape key
			$(document).bind('keydown.ui-dialog-overlay', function(e) {
				var ESC = 27;
				e.keyCode && e.keyCode == ESC && dialog.close(); 
			});
			
			// handle window resizing
			$overlay = this.$el;
			function resize() {
				// If the dialog is draggable and the user drags it past the
				// right edge of the window, the document becomes wider so we
				// need to stretch the overlay.  If the user then drags the
				// dialog back to the left, the document will become narrower,
				// so we need to shrink the overlay to the appropriate size.
				// This is handled by resetting the overlay to its original
				// size before setting it to the full document size.
				$overlay.css({
					width: width,
					height: height
				}).css({
					width: overlay.width(),
					height: overlay.height()
				});
			};
			$(window).bind('resize.ui-dialog-overlay', resize);
			dialog.uiDialog.is('.ui-draggable') && dialog.uiDialog.data('stop.draggable', resize);
			dialog.uiDialog.is('.ui-resizable') && dialog.uiDialog.data('stop.resizable', resize);
		},
		
		hide: function() {
			$('a, :input').add([document, window]).unbind('.ui-dialog-overlay');
			this.ie6 && this.selects.css('visibility', 'visible');
			this.$el = null;
			$('.ui-dialog-overlay').remove();
		},
		
		height: function() {
			var height;
			if (this.ie6
				// body is smaller than window
				&& ($(document.body).height() < $(window).height())
				// dialog is above the fold
				&& !(document.documentElement.scrollTop
					|| (this.dialog.uiDialog.offset().top
						+ this.dialog.uiDialog.height())
						> $(window).height())) {
				height = $(window).height();
			} else {
				height = $(document).height();
			}
			return height + 'px';
		},
		
		width: function() {
			var width;
			if (this.ie6
				// body is smaller than window
				&& ($(document.body).width() < $(window).width())
				// dialog is off to the right
				&& !(document.documentElement.scrollLeft
					|| (this.dialog.uiDialog.offset().left
						+ this.dialog.uiDialog.width())
						> $(window).width())) {
				width = $(window).width();
			} else {
				width = $(document).width();
			}
			return width + 'px';
		},
		
		// IE 6 compatibility
		ie6: $.browser.msie && $.browser.version < 7,
		selects: null
	};

})(jQuery);
/*
 * jQuery UI Draggable
 *
 * Copyright (c) 2008 Paul Bakaus
 * Dual licensed under the MIT (MIT-LICENSE.txt)
 * and GPL (GPL-LICENSE.txt) licenses.
 * 
 * http://docs.jquery.com/UI/Draggables
 *
 * Depends:
 *   ui.base.js
 *
 * Revision: $Id: ui.draggable.js 5154 2008-03-31 14:46:15Z rdworth $
 */
;(function($) {

	$.fn.extend({
		draggable: function(options) {
			var args = Array.prototype.slice.call(arguments, 1);
			
			return this.each(function() {
				if (typeof options == "string") {
					var drag = $.data(this, "draggable");
					if(drag) drag[options].apply(drag, args);

				} else if(!$.data(this, "draggable"))
					new $.ui.draggable(this, options);
			});
		}
	});
	
	$.ui.draggable = function(element, options) {
		//Initialize needed constants
		var self = this;
		
		this.element = $(element);
		
		$.data(element, "draggable", this);
		this.element.addClass("ui-draggable");
		
		//Prepare the passed options
		this.options = $.extend({}, options);
		var o = this.options;
		$.extend(o, {
			helper: o.ghosting == true ? 'clone' : (o.helper || 'original'),
			handle : o.handle ? ($(o.handle, element)[0] ? $(o.handle, element) : this.element) : this.element,
			appendTo: o.appendTo || 'parent'		
		});
		
		$(element).bind("setData.draggable", function(event, key, value){
			self.options[key] = value;
		}).bind("getData.draggable", function(event, key){
			return self.options[key];
		});
		
		//Initialize mouse events for interaction
		$(o.handle).mouseInteraction({
			executor: this,
			delay: o.delay,
			distance: o.distance || 1,
			dragPrevention: o.cancel || o.cancel === '' ? o.cancel.toLowerCase().split(',') : ['input','textarea','button','select','option'],
			start: this.start,
			stop: this.stop,
			drag: this.drag,
			condition: function(e) { return !(e.target.className.indexOf("ui-resizable-handle") != -1 || this.options.disabled); }
		});
		
		//Position the node
		if(o.helper == 'original' && (this.element.css('position') == 'static' || this.element.css('position') == ''))
			this.element.css('position', 'relative');
			
		//Prepare cursorAt
		if(o.cursorAt && o.cursorAt.constructor == Array)
			o.cursorAt = { left: o.cursorAt[0], top: o.cursorAt[1] };
		
	};
	
	$.extend($.ui.draggable.prototype, {
		plugins: {},
		ui: function(e) {
			return {
				helper: this.helper,
				position: this.position,
				absolutePosition: this.positionAbs,
				instance: this,
				options: this.options,
				element: this.element				
			};
		},
		propagate: function(n,e) {
			$.ui.plugin.call(this, n, [e, this.ui()]);
			return this.element.triggerHandler(n == "drag" ? n : "drag"+n, [e, this.ui()], this.options[n]);
		},
		destroy: function() {
			if(!$.data(this.element[0], 'draggable')) return;
			this.options.handle.removeMouseInteraction();
			this.element
				.removeClass("ui-draggable ui-draggable-disabled")
				.removeData("draggable")
				.unbind(".draggable");
		},
		enable: function() {
			this.element.removeClass("ui-draggable-disabled");
			this.options.disabled = false;
		},
		disable: function() {
			this.element.addClass("ui-draggable-disabled");
			this.options.disabled = true;
		},
		setContrains: function(minLeft,maxLeft,minTop,maxTop) {
			this.minLeft = minLeft; this.maxLeft = maxLeft;
			this.minTop = minTop; this.maxTop = maxTop;
			this.constrainsSet = true;
		},
		checkConstrains: function() {
			if(!this.constrainsSet) return;
			if(this.position.left < this.minLeft) this.position.left = this.minLeft;
			if(this.position.left > this.maxLeft - this.helperProportions.width) this.position.left = this.maxLeft - this.helperProportions.width;
			if(this.position.top < this.minTop) this.position.top = this.minTop;
			if(this.position.top > this.maxTop - this.helperProportions.height) this.position.top = this.maxTop - this.helperProportions.height;
		},
		recallOffset: function(e) {

			var elementPosition = { left: this.elementOffset.left - this.offsetParentOffset.left, top: this.elementOffset.top - this.offsetParentOffset.top };
			var r = this.helper.css('position') == 'relative';

			//Generate the original position
			this.originalPosition = {
				left: (r ? parseInt(this.helper.css('left'),10) || 0 : elementPosition.left + (this.offsetParent[0] == document.body ? 0 : this.offsetParent[0].scrollLeft)),
				top: (r ? parseInt(this.helper.css('top'),10) || 0 : elementPosition.top + (this.offsetParent[0] == document.body ? 0 : this.offsetParent[0].scrollTop))
			};
			
			//Generate a flexible offset that will later be subtracted from e.pageX/Y
			this.offset = {left: this._pageX - this.originalPosition.left, top: this._pageY - this.originalPosition.top };
			
		},
		start: function(e) {
			var o = this.options;
			if($.ui.ddmanager) $.ui.ddmanager.current = this;
			
			//Create and append the visible helper
			this.helper = typeof o.helper == 'function' ? $(o.helper.apply(this.element[0], [e])) : (o.helper == 'clone' ? this.element.clone().appendTo((o.appendTo == 'parent' ? this.element[0].parentNode : o.appendTo)) : this.element);
			if(this.helper[0] != this.element[0]) this.helper.css('position', 'absolute');
			if(!this.helper.parents('body').length) this.helper.appendTo((o.appendTo == 'parent' ? this.element[0].parentNode : o.appendTo));
			
			
			//Find out the next positioned parent
			this.offsetParent = (function(cp) {
				while(cp) {
					if(cp.style && (/(absolute|relative|fixed)/).test($.css(cp,'position'))) return $(cp);
					cp = cp.parentNode ? cp.parentNode : null;
				}; return $("body");		
			})(this.helper[0].parentNode);
			
			//Prepare variables for position generation
			this.elementOffset = this.element.offset();
			this.offsetParentOffset = this.offsetParent.offset();
			var elementPosition = { left: this.elementOffset.left - this.offsetParentOffset.left, top: this.elementOffset.top - this.offsetParentOffset.top };
			this._pageX = e.pageX; this._pageY = e.pageY;
			this.clickOffset = { left: e.pageX - this.elementOffset.left, top: e.pageY - this.elementOffset.top };
			var r = this.helper.css('position') == 'relative';

			//Generate the original position
			this.originalPosition = {
				left: (r ? parseInt(this.helper.css('left'),10) || 0 : elementPosition.left + (this.offsetParent[0] == document.body ? 0 : this.offsetParent[0].scrollLeft)),
				top: (r ? parseInt(this.helper.css('top'),10) || 0 : elementPosition.top + (this.offsetParent[0] == document.body ? 0 : this.offsetParent[0].scrollTop))
			};
			
			//If we have a fixed element, we must subtract the scroll offset again
			if(this.element.css('position') == 'fixed') {
				this.originalPosition.top -= this.offsetParent[0] == document.body ? $(document).scrollTop() : this.offsetParent[0].scrollTop;
				this.originalPosition.left -= this.offsetParent[0] == document.body ? $(document).scrollLeft() : this.offsetParent[0].scrollLeft;
			}
			
			//Generate a flexible offset that will later be subtracted from e.pageX/Y
			this.offset = {left: e.pageX - this.originalPosition.left, top: e.pageY - this.originalPosition.top };
			
			//Substract margins
			if(this.element[0] != this.helper[0]) {
				this.offset.left += parseInt(this.element.css('marginLeft'),10) || 0;
				this.offset.top += parseInt(this.element.css('marginTop'),10) || 0;
			}
			
			//Call plugins and callbacks
			this.propagate("start", e);

			this.helperProportions = { width: this.helper.outerWidth(), height: this.helper.outerHeight() };
			if ($.ui.ddmanager && !o.dropBehaviour) $.ui.ddmanager.prepareOffsets(this, e);
			
			//If we have something in cursorAt, we'll use it
			if(o.cursorAt) {
				if(o.cursorAt.top != undefined || o.cursorAt.bottom != undefined) {
					this.offset.top -= this.clickOffset.top - (o.cursorAt.top != undefined ? o.cursorAt.top : (this.helperProportions.height - o.cursorAt.bottom));
					this.clickOffset.top = (o.cursorAt.top != undefined ? o.cursorAt.top : (this.helperProportions.height - o.cursorAt.bottom));
				}
				if(o.cursorAt.left != undefined || o.cursorAt.right != undefined) {
					this.offset.left -= this.clickOffset.left - (o.cursorAt.left != undefined ? o.cursorAt.left : (this.helperProportions.width - o.cursorAt.right));
					this.clickOffset.left = (o.cursorAt.left != undefined ? o.cursorAt.left : (this.helperProportions.width - o.cursorAt.right));
				}
			}

			return false;

		},
		clear: function() {
			if($.ui.ddmanager) $.ui.ddmanager.current = null;
			this.helper = null;
		},
		stop: function(e) {

			//If we are using droppables, inform the manager about the drop
			if ($.ui.ddmanager && !this.options.dropBehaviour)
				$.ui.ddmanager.drop(this, e);
				
			//Call plugins and trigger callbacks
			this.propagate("stop", e);
			
			if(this.cancelHelperRemoval) return false;			
			if(this.options.helper != 'original') this.helper.remove();
			this.clear();

			return false;
		},
		drag: function(e) {

			//Compute the helpers position
			this.position = { top: e.pageY - this.offset.top, left: e.pageX - this.offset.left };
			this.positionAbs = { left: e.pageX - this.clickOffset.left, top: e.pageY - this.clickOffset.top };

			//Call plugins and callbacks
			this.checkConstrains();			
			this.position = this.propagate("drag", e) || this.position;
			this.checkConstrains();
			
			$(this.helper).css({ left: this.position.left+'px', top: this.position.top+'px' }); // Stick the helper to the cursor
			if($.ui.ddmanager) $.ui.ddmanager.drag(this, e);
			return false;
			
		}
	});
	
/*
 * Draggable Extensions
 */
	 
	$.ui.plugin.add("draggable", "cursor", {
		start: function(e, ui) {
			var t = $('body');
			if (t.css("cursor")) ui.options._cursor = t.css("cursor");
			t.css("cursor", ui.options.cursor);
		},
		stop: function(e, ui) {
			if (ui.options._cursor) $('body').css("cursor", ui.options._cursor);
		}
	});

	$.ui.plugin.add("draggable", "zIndex", {
		start: function(e, ui) {
			var t = $(ui.helper);
			if(t.css("zIndex")) ui.options._zIndex = t.css("zIndex");
			t.css('zIndex', ui.options.zIndex);
		},
		stop: function(e, ui) {
			if(ui.options._zIndex) $(ui.helper).css('zIndex', ui.options._zIndex);
		}
	});

	$.ui.plugin.add("draggable", "opacity", {
		start: function(e, ui) {
			var t = $(ui.helper);
			if(t.css("opacity")) ui.options._opacity = t.css("opacity");
			t.css('opacity', ui.options.opacity);
		},
		stop: function(e, ui) {
			if(ui.options._opacity) $(ui.helper).css('opacity', ui.options._opacity);
		}
	});


	$.ui.plugin.add("draggable", "revert", {
		stop: function(e, ui) {
			var self = ui.instance, helper = $(self.helper);
			self.cancelHelperRemoval = true;
			
			$(ui.helper).animate({ left: self.originalPosition.left, top: self.originalPosition.top }, parseInt(ui.options.revert, 10) || 500, function() {
				if(ui.options.helper != 'original') helper.remove();
				if (!helper) self.clear();
			});
		}
	});

	$.ui.plugin.add("draggable", "iframeFix", {
		start: function(e, ui) {

			var o = ui.options;
			if(ui.instance.slowMode) return; // Make clones on top of iframes (only if we are not in slowMode)
			
			if(o.iframeFix.constructor == Array) {
				for(var i=0;i<o.iframeFix.length;i++) {
					var co = $(o.iframeFix[i]).offset({ border: false });
					$('<div class="DragDropIframeFix"" style="background: #fff;"></div>').css("width", $(o.iframeFix[i])[0].offsetWidth+"px").css("height", $(o.iframeFix[i])[0].offsetHeight+"px").css("position", "absolute").css("opacity", "0.001").css("z-index", "1000").css("top", co.top+"px").css("left", co.left+"px").appendTo("body");
				}		
			} else {
				$("iframe").each(function() {					
					var co = $(this).offset({ border: false });
					$('<div class="DragDropIframeFix" style="background: #fff;"></div>').css("width", this.offsetWidth+"px").css("height", this.offsetHeight+"px").css("position", "absolute").css("opacity", "0.001").css("z-index", "1000").css("top", co.top+"px").css("left", co.left+"px").appendTo("body");
				});							
			}

		},
		stop: function(e, ui) {
			if(ui.options.iframeFix) $("div.DragDropIframeFix").each(function() { this.parentNode.removeChild(this); }); //Remove frame helpers	
		}
	});
	
	$.ui.plugin.add("draggable", "containment", {
		start: function(e, ui) {

			var o = ui.options;
			var self = ui.instance;
			if((o.containment.left != undefined || o.containment.constructor == Array) && !o._containment) return;
			if(!o._containment) o._containment = o.containment;

			if(o._containment == 'parent') o._containment = this[0].parentNode;
			if(o._containment == 'document') {
				o.containment = [
					0,
					0,
					$(document).width(),
					($(document).height() || document.body.parentNode.scrollHeight)
				];
			} else { //I'm a node, so compute top/left/right/bottom

				var ce = $(o._containment)[0];
				var co = $(o._containment).offset();

				o.containment = [
					co.left,
					co.top,
					co.left+(ce.offsetWidth || ce.scrollWidth),
					co.top+(ce.offsetHeight || ce.scrollHeight)
				];
			}
			
			var c = o.containment;
			ui.instance.setContrains(
				c[0] - (self.offset.left - self.clickOffset.left), //min left
				c[2] - (self.offset.left - self.clickOffset.left), //max left
				c[1] - (self.offset.top - self.clickOffset.top), //min top
				c[3] - (self.offset.top - self.clickOffset.top) //max top
			);

		}
	});

	$.ui.plugin.add("draggable", "grid", {
		drag: function(e, ui) {
			var o = ui.options;
			var newLeft = ui.instance.originalPosition.left + Math.round((e.pageX - ui.instance._pageX) / o.grid[0]) * o.grid[0];
			var newTop = ui.instance.originalPosition.top + Math.round((e.pageY - ui.instance._pageY) / o.grid[1]) * o.grid[1];
			
			ui.instance.position.left = newLeft;
			ui.instance.position.top = newTop;

		}
	});

	$.ui.plugin.add("draggable", "axis", {
		drag: function(e, ui) {
			var o = ui.options;
			if(o.constraint) o.axis = o.constraint; //Legacy check
			switch (o.axis) {
				case 'x' : ui.instance.position.top = ui.instance.originalPosition.top; break;
				case 'y' : ui.instance.position.left = ui.instance.originalPosition.left; break;
			}
		}
	});

	$.ui.plugin.add("draggable", "scroll", {
		start: function(e, ui) {
			var o = ui.options;
			o.scrollSensitivity	= o.scrollSensitivity || 20;
			o.scrollSpeed		= o.scrollSpeed || 20;

			ui.instance.overflowY = function(el) {
				do { if(/auto|scroll/.test(el.css('overflow')) || (/auto|scroll/).test(el.css('overflow-y'))) return el; el = el.parent(); } while (el[0].parentNode);
				return $(document);
			}(this);
			ui.instance.overflowX = function(el) {
				do { if(/auto|scroll/.test(el.css('overflow')) || (/auto|scroll/).test(el.css('overflow-x'))) return el; el = el.parent(); } while (el[0].parentNode);
				return $(document);
			}(this);
		},
		drag: function(e, ui) {
			
			var o = ui.options;
			var i = ui.instance;

			if(i.overflowY[0] != document && i.overflowY[0].tagName != 'HTML') {
				if(i.overflowY[0].offsetHeight - (ui.position.top - i.overflowY[0].scrollTop + i.clickOffset.top) < o.scrollSensitivity)
					i.overflowY[0].scrollTop = i.overflowY[0].scrollTop + o.scrollSpeed;
				if((ui.position.top - i.overflowY[0].scrollTop + i.clickOffset.top) < o.scrollSensitivity)
					i.overflowY[0].scrollTop = i.overflowY[0].scrollTop - o.scrollSpeed;				
			} else {
				//$(document.body).append('<p>'+(e.pageY - $(document).scrollTop())+'</p>');
				if(e.pageY - $(document).scrollTop() < o.scrollSensitivity)
					$(document).scrollTop($(document).scrollTop() - o.scrollSpeed);
				if($(window).height() - (e.pageY - $(document).scrollTop()) < o.scrollSensitivity)
					$(document).scrollTop($(document).scrollTop() + o.scrollSpeed);
			}
			
			if(i.overflowX[0] != document && i.overflowX[0].tagName != 'HTML') {
				if(i.overflowX[0].offsetWidth - (ui.position.left - i.overflowX[0].scrollLeft + i.clickOffset.left) < o.scrollSensitivity)
					i.overflowX[0].scrollLeft = i.overflowX[0].scrollLeft + o.scrollSpeed;
				if((ui.position.top - i.overflowX[0].scrollLeft + i.clickOffset.left) < o.scrollSensitivity)
					i.overflowX[0].scrollLeft = i.overflowX[0].scrollLeft - o.scrollSpeed;				
			} else {
				if(e.pageX - $(document).scrollLeft() < o.scrollSensitivity)
					$(document).scrollLeft($(document).scrollLeft() - o.scrollSpeed);
				if($(window).width() - (e.pageX - $(document).scrollLeft()) < o.scrollSensitivity)
					$(document).scrollLeft($(document).scrollLeft() + o.scrollSpeed);
			}
			
			ui.instance.recallOffset(e);

		}
	});
	
	$.ui.plugin.add("draggable", "snap", {
		start: function(e, ui) {
			
			ui.instance.snapElements = [];
			$(ui.options.snap === true ? '.ui-draggable' : ui.options.snap).each(function() {
				var $t = $(this); var $o = $t.offset();
				if(this != ui.instance.element[0]) ui.instance.snapElements.push({
					item: this,
					width: $t.outerWidth(),
					height: $t.outerHeight(),
					top: $o.top,
					left: $o.left
				});
			});
			
		},
		drag: function(e, ui) {

			var d = ui.options.snapTolerance || 20;
			var x1 = ui.absolutePosition.left, x2 = x1 + ui.instance.helperProportions.width,
			    y1 = ui.absolutePosition.top, y2 = y1 + ui.instance.helperProportions.height;

			for (var i = ui.instance.snapElements.length - 1; i >= 0; i--){

				var l = ui.instance.snapElements[i].left, r = l + ui.instance.snapElements[i].width, 
				    t = ui.instance.snapElements[i].top,  b = t + ui.instance.snapElements[i].height;

				//Yes, I know, this is insane ;)
				if(!((l-d < x1 && x1 < r+d && t-d < y1 && y1 < b+d) || (l-d < x1 && x1 < r+d && t-d < y2 && y2 < b+d) || (l-d < x2 && x2 < r+d && t-d < y1 && y1 < b+d) || (l-d < x2 && x2 < r+d && t-d < y2 && y2 < b+d))) continue;

				if(ui.options.snapMode != 'inner') {
					var ts = Math.abs(t - y2) <= 20;
					var bs = Math.abs(b - y1) <= 20;
					var ls = Math.abs(l - x2) <= 20;
					var rs = Math.abs(r - x1) <= 20;
					if(ts) ui.position.top = t - ui.instance.offset.top + ui.instance.clickOffset.top - ui.instance.helperProportions.height;
					if(bs) ui.position.top = b - ui.instance.offset.top + ui.instance.clickOffset.top;
					if(ls) ui.position.left = l - ui.instance.offset.left + ui.instance.clickOffset.left - ui.instance.helperProportions.width;
					if(rs) ui.position.left = r - ui.instance.offset.left + ui.instance.clickOffset.left;
				}
				
				if(ui.options.snapMode != 'outer') {
					var ts = Math.abs(t - y1) <= 20;
					var bs = Math.abs(b - y2) <= 20;
					var ls = Math.abs(l - x1) <= 20;
					var rs = Math.abs(r - x2) <= 20;
					if(ts) ui.position.top = t - ui.instance.offset.top + ui.instance.clickOffset.top;
					if(bs) ui.position.top = b - ui.instance.offset.top + ui.instance.clickOffset.top - ui.instance.helperProportions.height;
					if(ls) ui.position.left = l - ui.instance.offset.left + ui.instance.clickOffset.left;
					if(rs) ui.position.left = r - ui.instance.offset.left + ui.instance.clickOffset.left - ui.instance.helperProportions.width;
				}

			};
		}
	});

	//TODO: wrapHelper

})(jQuery);

/*
 * jQuery UI Droppable
 *
 * Copyright (c) 2008 Paul Bakaus
 * Dual licensed under the MIT (MIT-LICENSE.txt)
 * and GPL (GPL-LICENSE.txt) licenses.
 * 
 * http://docs.jquery.com/UI/Droppables
 *
 * Depends:
 *   ui.base.js
 *   ui.draggable.js
 *
 * Revision: $Id: ui.droppable.js 5149 2008-03-31 10:51:18Z rdworth $
 */
;(function($) {

	$.fn.extend({
		droppable: function(options) {
			var args = Array.prototype.slice.call(arguments, 1);
			
			return this.each(function() {
				if (typeof options == "string") {
					var drop = $.data(this, "droppable");
					if(drop) drop[options].apply(drop, args);

				} else if(!$.data(this, "droppable"))
					new $.ui.droppable(this, options);
			});
		}
	});
	
	$.ui.droppable = function(element, options) {

		//Initialize needed constants			
		this.element = $(element);
		$.data(element, "droppable", this);
		this.element.addClass("ui-droppable");		
		
		//Prepare the passed options
		var o = this.options = options = $.extend({}, $.ui.droppable.defaults, options);
		var accept = o.accept;
		o = $.extend(o, {
			accept: o.accept && o.accept.constructor == Function ? o.accept : function(d) {
				return $(d).is(accept);	
			}
		});
		
		$(element).bind("setData.droppable", function(event, key, value){
			o[key] = value;
		}).bind("getData.droppable", function(event, key){
			return o[key];
		});
		
		//Store the droppable's proportions
		this.proportions = { width: this.element.outerWidth(), height: this.element.outerHeight() };
		
		// Add the reference and positions to the manager
		$.ui.ddmanager.droppables.push({ item: this, over: 0, out: 1 });
			
	};
	
	$.extend($.ui.droppable, {
		defaults: {
			disabled: false,
			tolerance: 'intersect'
		}
	});

	$.extend($.ui.droppable.prototype, {
		plugins: {},
		ui: function(c) {
			return {
				instance: this,
				draggable: (c.currentItem || c.element),
				helper: c.helper,
				position: c.position,
				absolutePosition: c.positionAbs,
				options: this.options,
				element: this.element	
			};		
		},
		destroy: function() {
			var drop = $.ui.ddmanager.droppables;
			for ( var i = 0; i < drop.length; i++ )
				if ( drop[i].item == this )
					drop.splice(i, 1);
			
			this.element
				.removeClass("ui-droppable ui-droppable-disabled")
				.removeData("droppable")
				.unbind(".droppable");
		},
		enable: function() {
			this.element.removeClass("ui-droppable-disabled");
			this.options.disabled = false;
		},
		disable: function() {
			this.element.addClass("ui-droppable-disabled");
			this.options.disabled = true;
		},
		over: function(e) {

			var draggable = $.ui.ddmanager.current;
			if (!draggable || (draggable.currentItem || draggable.element)[0] == this.element[0]) return; // Bail if draggable and droppable are same element
			
			if (this.options.accept.call(this.element,(draggable.currentItem || draggable.element))) {
				$.ui.plugin.call(this, 'over', [e, this.ui(draggable)]);
				this.element.triggerHandler("dropover", [e, this.ui(draggable)], this.options.over);
			}
			
		},
		out: function(e) {

			var draggable = $.ui.ddmanager.current;
			if (!draggable || (draggable.currentItem || draggable.element)[0] == this.element[0]) return; // Bail if draggable and droppable are same element

			if (this.options.accept.call(this.element,(draggable.currentItem || draggable.element))) {
				$.ui.plugin.call(this, 'out', [e, this.ui(draggable)]);
				this.element.triggerHandler("dropout", [e, this.ui(draggable)], this.options.out);
			}
			
		},
		drop: function(e,custom) {

			var draggable = custom || $.ui.ddmanager.current;
			if (!draggable || (draggable.currentItem || draggable.element)[0] == this.element[0]) return; // Bail if draggable and droppable are same element
			
			var childrenIntersection = false;
			this.element.find(".ui-droppable").each(function() {
				var inst = $.data(this, 'droppable');
				if(inst.options.greedy && $.ui.intersect(draggable, { item: inst, offset: inst.element.offset() }, inst.options.tolerance)) { 
					childrenIntersection = true; return false;
				}
			});
			if(childrenIntersection) return;
			
			if(this.options.accept.call(this.element,(draggable.currentItem || draggable.element))) {
				$.ui.plugin.call(this, 'drop', [e, this.ui(draggable)]);
				this.element.triggerHandler("drop", [e, this.ui(draggable)], this.options.drop);
			}
			
		},
		activate: function(e) {

			var draggable = $.ui.ddmanager.current;
			$.ui.plugin.call(this, 'activate', [e, this.ui(draggable)]);
			if(draggable) this.element.triggerHandler("dropactivate", [e, this.ui(draggable)], this.options.activate);
				
		},
		deactivate: function(e) {
			
			var draggable = $.ui.ddmanager.current;
			$.ui.plugin.call(this, 'deactivate', [e, this.ui(draggable)]);
			if(draggable) this.element.triggerHandler("dropdeactivate", [e, this.ui(draggable)], this.options.deactivate);
			
		}
	});
	
	$.ui.intersect = function(draggable, droppable, toleranceMode) {

		if (!droppable.offset) return false;

		var x1 = draggable.positionAbs.left, x2 = x1 + draggable.helperProportions.width,
		    y1 = draggable.positionAbs.top, y2 = y1 + draggable.helperProportions.height;
		var l = droppable.offset.left, r = l + droppable.item.proportions.width, 
		    t = droppable.offset.top,  b = t + droppable.item.proportions.height;

		switch (toleranceMode) {
			case 'fit':
				
				if(!((y2-(draggable.helperProportions.height/2) > t && y1 < t) || (y1 < b && y2 > b) || (x2 > l && x1 < l) || (x1 < r && x2 > r))) return false;
				
				if(y2-(draggable.helperProportions.height/2) > t && y1 < t) return 1; //Crosses top edge
				if(y1 < b && y2 > b) return 2; //Crosses bottom edge
				if(x2 > l && x1 < l) return 1; //Crosses left edge
				if(x1 < r && x2 > r) return 2; //Crosses right edge
				
				//return (   l < x1 && x2 < r
				//	&& t < y1 && y2 < b);
				break;
			case 'intersect':
				return (   l < x1 + (draggable.helperProportions.width  / 2)        // Right Half
					&&     x2 - (draggable.helperProportions.width  / 2) < r    // Left Half
					&& t < y1 + (draggable.helperProportions.height / 2)        // Bottom Half
					&&     y2 - (draggable.helperProportions.height / 2) < b ); // Top Half
				break;
			case 'pointer':
				return (   l < (draggable.positionAbs.left + draggable.clickOffset.left) && (draggable.positionAbs.left + draggable.clickOffset.left) < r
					&& t < (draggable.positionAbs.top + draggable.clickOffset.top) && (draggable.positionAbs.top + draggable.clickOffset.top) < b);
				break;
			case 'touch':
				return ( (y1 >= t && y1 <= b) ||	// Top edge touching
						 (y2 >= t && y2 <= b) ||	// Bottom edge touching
						 (y1 < t && y2 > b)		// Surrounded vertically
						 ) && (
						 (x1 >= l && x1 <= r) ||	// Left edge touching
						 (x2 >= l && x2 <= r) ||	// Right edge touching
						 (x1 < l && x2 > r)		// Surrounded horizontally
						);
				break;
			default:
				return false;
				break;
			}
		
	};
	
	/*
		This manager tracks offsets of draggables and droppables
	*/
	$.ui.ddmanager = {
		current: null,
		droppables: [],
		prepareOffsets: function(t, e) {

			var m = $.ui.ddmanager.droppables;
			var type = e ? e.type : null; // workaround for #2317
			for (var i = 0; i < m.length; i++) {
				
				if(m[i].item.options.disabled || (t && !m[i].item.options.accept.call(m[i].item.element,(t.currentItem || t.element)))) continue;
				m[i].offset = $(m[i].item.element).offset();
				m[i].item.proportions = { width: m[i].item.element.outerWidth(), height: m[i].item.element.outerHeight() };
				
				if(type == "dragstart") m[i].item.activate.call(m[i].item, e); //Activate the droppable if used directly from draggables
			}
			
		},
		drop: function(draggable, e) {
			
			$.each($.ui.ddmanager.droppables, function() {
				
				if (!this.item.options.disabled && $.ui.intersect(draggable, this, this.item.options.tolerance))
					this.item.drop.call(this.item, e);
					
				if (!this.item.options.disabled && this.item.options.accept.call(this.item.element,(draggable.currentItem || draggable.element))) {
					this.out = 1; this.over = 0;
					this.item.deactivate.call(this.item, e);
				}
				
			});
			
		},
		drag: function(draggable, e) {
			
			//If you have a highly dynamic page, you might try this option. It renders positions every time you move the mouse.
			if(draggable.options.refreshPositions) $.ui.ddmanager.prepareOffsets(draggable, e);
		
			//Run through all droppables and check their positions based on specific tolerance options
			$.each($.ui.ddmanager.droppables, function() {

				if(this.item.disabled || this.greedyChild) return; 
				var intersects = $.ui.intersect(draggable, this, this.item.options.tolerance);

				var c = !intersects && this.over == 1 ? 'out' : (intersects && this.over == 0 ? 'over' : null);
				if(!c) return;

				var instance = $.data(this.item.element[0], 'droppable'); 
				if (instance.options.greedy) { 
				    this.item.element.parents('.ui-droppable').each(function() { 
				        var parent = this; 
				        $.each($.ui.ddmanager.droppables, function() { 
				            if (this.item.element[0] != parent) return; 
				            this[c] = 0; 
				            this[c == 'out' ? 'over' : 'out'] = 1; 
				            this.greedyChild = (c == 'over' ? 1 : 0); 
				            this.item[c == 'out' ? 'over' : 'out'].call(this.item, e); 
				            return false; 
				        }); 
				    }); 
				} 

				this[c] = 1; this[c == 'out' ? 'over' : 'out'] = 0;
				this.item[c].call(this.item, e);
					
			});
			
		}
	};
	
/*
 * Droppable Extensions
 */

	$.ui.plugin.add("droppable", "activeClass", {
		activate: function(e, ui) {
			$(this).addClass(ui.options.activeClass);
		},
		deactivate: function(e, ui) {
			$(this).removeClass(ui.options.activeClass);
		},
		drop: function(e, ui) {
			$(this).removeClass(ui.options.activeClass);
		}
	});

	$.ui.plugin.add("droppable", "hoverClass", {
		over: function(e, ui) {
			$(this).addClass(ui.options.hoverClass);
		},
		out: function(e, ui) {
			$(this).removeClass(ui.options.hoverClass);
		},
		drop: function(e, ui) {
			$(this).removeClass(ui.options.hoverClass);
		}
	});	

})(jQuery);
/*
Copyright 2007 University of Cambridge
Copyright 2007-2008 University of Toronto

Licensed under the Educational Community License (ECL), Version 2.0 or the New
BSD license. You may not use this file except in compliance with one these
Licenses.

You may obtain a copy of the ECL 2.0 License and BSD License at
https://source.fluidproject.org/svn/LICENSE.txt
*/

// Declare dependencies.
/*global jQuery*/

/*global fluid*/
var fluid = fluid || {};

(function (jQuery, fluid) {
    fluid.keys = {
        TAB: 9,
        ENTER: 13,
        SHIFT: 16,
        CTRL: 17,
        ALT: 18,
        META: 19,
        SPACE: 32,
        LEFT: 37,
        UP: 38,
        RIGHT: 39,
        DOWN: 40,
        i: 73,
        j: 74,
        k: 75,
        m: 77
    };
    
    /**
     * These roles are used to add ARIA roles to orderable items. This list can be extended as needed,
     * but the values of the container and item roles must match ARIA-specified roles.
     */  
    fluid.roles = {
        GRID: { container: "grid", item: "gridcell" },
        LIST: { container: "list", item: "listitem" },
        REGIONS: { container: "main", item: "article" }
    };
    
    fluid.orientation = {
    	HORIZONTAL: "horiz",
    	VERTICAL: "vert"
    };
    
    /**
     * This is the position, relative to a given drop target, that a dragged item should be dropped.
     */
    fluid.position = {
    	BEFORE: 0, 
    	AFTER: 1,
    	INSIDE: 2,
    	USE_LAST_KNOWN: 3,  // given configuration meaningless, use last known drop target
        DISALLOWED: -1      // cannot drop in given configuration
    };
    
    /**
     * For incrementing/decrementing a count or index.
     */
    fluid.direction = {
        NEXT: 1,
        PREVIOUS: -1
    };
    
    fluid.defaultKeysets = [{
        modifier : function (evt) {
	        return evt.ctrlKey;
        },
        up : fluid.keys.UP,
        down : fluid.keys.DOWN,
        right : fluid.keys.RIGHT,
        left : fluid.keys.LEFT
    },
    {
        modifier : function (evt) {
	        return evt.ctrlKey;
        },
        up : fluid.keys.i,
        down : fluid.keys.m,
        right : fluid.keys.k,
        left : fluid.keys.j
    }];
    
    fluid.mixin = function (target, args) {
        for (var arg in args) {
            if (args.hasOwnProperty(arg)) {
                target[arg] = args[arg];
            }
        }
    };
    
    fluid.wrap = function (obj) {
        return ((!obj || obj.jquery) ? obj : jQuery(obj)); 
    };
    
    fluid.unwrap = function (obj) {
        return obj.jquery ? obj[0] : obj; // Unwrap the element if it's a jQuery.
    };
    
    /*
     * Utilities object for providing various general convenience functions
     */
    fluid.utils = {};
    
    // Custom query method seeks all tags descended from a given root with a 
    // particular tag name, whose id matches a regex. The Dojo query parser
    // is broken http://trac.dojotoolkit.org/ticket/3520#preview, this is all
    // it might do anyway, and this will be plenty fast.
    fluid.utils.seekNodesById = function (rootnode, tagname, idmatch) {
        var inputs = rootnode.getElementsByTagName(tagname);
        var togo = [];
        for (var i = 0; i < inputs.length; i += 1) {
            var input = inputs[i];
            var id = input.id;
            if (id && id.match(idmatch)) {
                togo.push(input);
            }
        }
        return togo;
    };
          
    fluid.utils.escapeSelector = function (id) {
        return id.replace(/\:/g, "\\:");
    };
      
    fluid.utils.findForm = function (element) {
        while (element) {
            if (element.nodeName.toLowerCase() === "form") {
                return element;
            }
            element = element.parentNode;
        }
    };
    
    /**
     * Adapt 'findItems' object given either a 'findItems' object or a 'findMovables' function 
     **/
    fluid.utils.adaptFindItems = function (finder) {
        var finderFn = function () {};
        var findItems = {};
        
        if (typeof finder === 'function') {
            finderFn = finder;
        } else {
            findItems = finder;
        }
    
        findItems.movables = findItems.movables || finderFn;
        findItems.selectables = findItems.selectables || findItems.movables;
        findItems.dropTargets = findItems.dropTargets || findItems.movables;
        findItems.grabHandle = findItems.grabHandle ||
            function (item) {
                return item;
            };
            
        return findItems;
    };
    
    /**
     * Returns a jQuery object given the id of a DOM node
     */
    fluid.utils.jById = function (id) {
        var el = jQuery("[id=" + id + "]");
        if (el[0] && el[0].id === id) {
            return el;        
        }       
        
        return null;
    };

    fluid.utils.debug = function (str) {
    	if (window.console) {
            if (console.debug) {
                console.debug(str);
            } else {
                console.log(str);
            }
    	}
    };

	fluid.utils.derivePercent = function (num, total) {
		return Math.round((num * 100) / total);
	};

	// simple function for return kbytes and megabytes from a number of bytes
	// probably should do something fancy that shows MBs if the number is huge
	fluid.utils.filesizeStr = function (bytes) {
		/*
		if (bytes < 1024){
			return bytes + " bytes";
		} else
		*/
		if (typeof bytes === "number") {
			if (bytes === 0) {
				return "0.0 KB";
			} else if (bytes > 0) {
				if (bytes < 1048576) {
					return (Math.ceil(bytes / 1024 * 10) / 10).toFixed(1) + ' KB';
				}
				else {
					return (Math.ceil(bytes / 1048576 * 10) / 10).toFixed(1) + ' MB';
				}
			}
		}
		return '';
	};
	
    fluid.utils.initCssClassNames = function (defaultNames, classNames) {
        if (!classNames) {
            return defaultNames;
        }
        var cssClassNames = {};
        for (var className in defaultNames) {
            if (defaultNames.hasOwnProperty(className)) {
                cssClassNames[className] = classNames[className] || defaultNames[className];
            }
        }

        return cssClassNames;
    };
	
    /**
     * Simple string template system. 
     * Takes a template string containing tokens in the form of "%value".
     * Returns a new string with the tokens replaced by the specified values.
     * Keys and values can be of any data type that can be coerced into a string. Arrays will work here as well.
     * 
     * @param {String}	template	a string (can be HTML) that contains tokens embedded into it
     * @param {object}	values		a collection of token keys and values
	 */
    fluid.utils.stringTemplate = function (template, values) {
	    var newString = template;
		for (var key in values) {
            if (values.hasOwnProperty(key)) {
    			var searchStr = "%" + key;
                newString = newString.replace(searchStr, values[key]);
            }
		}
		return newString;
	};

    /**
     * Finds the ancestor of the element that passes the test
     * @param {Element} element DOM element
     * @param {Function} test A function which takes an element as a parameter and return true or false for some test
     */
    fluid.utils.findAncestor = function (element, test) {
        return test(element) ? element : jQuery.grep(jQuery(element).parents(), test)[0];
    };
    
})(jQuery, fluid);
/*
Copyright 2007 - 2008 University of Toronto
Copyright 2007 University of Cambridge

Licensed under the Educational Community License (ECL), Version 2.0 or the New
BSD license. You may not use this file except in compliance with one these
Licenses.

You may obtain a copy of the ECL 2.0 License and BSD License at
https://source.fluidproject.org/svn/LICENSE.txt
*/

// Declare dependencies.
/*global jQuery*/
/*global fluid*/

fluid = fluid || {};

(function (jQuery, fluid) {
    var defaultContainerRole = fluid.roles.LIST;
    var defaultInstructionMessageId = "message-bundle:";
    
    var defaultCssClassNames = {
        defaultStyle: "orderable-default",
        selected: "orderable-selected",
        dragging: "orderable-dragging",
        mouseDrag: "orderable-dragging",
        hover: "orderable-hover",
        dropMarker: "orderable-drop-marker",
        avatar: "orderable-avatar"
    };
    
    var defaultAvatarCreator = function(item, cssClass, dropWarning) {
        var avatar = jQuery (item).clone ();
        avatar.removeAttr ("id");
        jQuery ("[id]", avatar).removeAttr ("id");
        jQuery (":hidden", avatar).remove();
        jQuery ("input", avatar).attr ("disabled", "true");
// dropping in the same column fails if the avatar is considered a droppable.
// droppable ("destroy") should take care of this, but it doesn't seem to remove
// the class, which is what is checked, so we remove it manually
// (see http://dev.jquery.com/ticket/2599)
// 2008-05-12: 2599 has been fixed now in trunk
//                    avatar.droppable ("destroy");
        avatar.removeClass ("ui-droppable");
        avatar.addClass (cssClass);
        
        if (dropWarning) {
            // Will a 'div' always be valid in this position?
            var avatarContainer = jQuery (document.createElement("div"));
            avatarContainer.append(avatar);
            avatarContainer.append(dropWarning);
            return avatarContainer;
        } else {
            return avatar;
        }
    };   
    
    function firstSelectable (findItems) {
        var selectables = fluid.wrap (findItems.selectables());
        if (selectables.length <= 0) {
            return null;
        }
        return selectables[0];
    }
    
    function bindHandlersToContainer (container, focusHandler, keyDownHandler, keyUpHandler, mouseMoveHandler) {
        container.focus (focusHandler);
        container.keydown (keyDownHandler);
        container.keyup (keyUpHandler);
        container.mousemove (mouseMoveHandler);
        // FLUID-143. Disable text selection for the reorderer.
        // ondrag() and onselectstart() are Internet Explorer specific functions.
        // Override them so that drag+drop actions don't also select text in IE.
        if (jQuery.browser.msie) {
            container[0].ondrag = function () { return false; }; 
            container[0].onselectstart = function () { return false; };
        } 
    }
    
    function addRolesToContainer (container, findItems, role) {
        var first = firstSelectable(findItems);
        if (first) {
            container.ariaState ("activedescendent", first.id);
        }
        container.ariaRole (role.container);
        container.ariaState ("multiselectable", "false");
        container.ariaState ("readonly", "false");
        container.ariaState ("disabled", "false");
    }
    
    function changeSelectedToDefault (jItem, cssClasses) {
        jItem.removeClass (cssClasses.selected);
        jItem.addClass (cssClasses.defaultStyle);
        jItem.ariaState("selected", "false");
    }
    
    // This is the start of refactoring the drag and drop code out into its own space. 
    // These are the private stateless functions.
    var dndFunctions = {};
    dndFunctions.findTarget = function (element, dropTargets, avatarId, lastTarget) {
        var isAvatar = function (el) {
            return (el && el.id === avatarId);
        };
            
        var isTargetOrAvatar = function (el) {
            return ((dropTargets.index (el) > -1) || isAvatar (el));
        };

        var target = fluid.utils.findAncestor(element, isTargetOrAvatar);
        
        // If the avatar was the target of the event, use the last known drop target instead.
        if (isAvatar(target)) {
            target = lastTarget;        
        }
        return target;
    };
    dndFunctions.createAvatarId = function (parentId) {
        // Generating the avatar's id to be containerId_avatar
        // This is safe since there is only a single avatar at a time
        return parentId + "_avatar";
    };
    
    var setupKeysets = function (defaultKeysets, userKeysets) {
        // Check if the user has given us an array of keysets or a single keyset.
        if (userKeysets && !(userKeysets instanceof Array)) {
            userKeysets = [userKeysets];    
        }
        return userKeysets || defaultKeysets;
    };
    
    /**
     * @param container - the root node of the Reorderer.
     * @param findItems - a function that returns all of the movable elements in the container OR
     *        findItems - an object containing the functions:
     *                    movables - a function that returns all of the movable elements in the container
     *                    selectables (optional) - a function that returns all of the selectable elements
     *                    dropTargets (optional) - a function that returns all of the elements that can be used as drop targets
     *                    grabHandle (optional) - a function that returns the element within the given movable that is to be used as a 'handle' for the mouse-based drag and drop of the movable. 
     * @param layoutHandler - an instance of a Layout Handler.
     * @param options - an object containing any of the available options:
     *                  role - indicates the role, or general use, for this instance of the Reorderer
     *                  instructionMessageId - the ID of the element containing any instructional messages
     *                  keysets - an object containing sets of keycodes to use for directional navigation. Must contain:
     *                            modifier - a function that returns a boolean, indicating whether or not the required modifier(s) are activated
     *                            up
     *                            down
     *                            right
     *                            left
     *                  cssClassNames - an object containing class names for styling the Reorderer
     *                                  defaultStyle
     *                                  selected
     *                                  dragging
     *                                  hover
     *                                  dropMarker
     *                                  mouseDrag
     *                                  avatar
     *                  avatarCreator - a function that returns a valid DOM node to be used as the dragging avatar
     */
    fluid.Reorderer = function (container, findItems, layoutHandler, options) {
        // Reliable 'this'.
        var thisReorderer = this;
        
        // Basic setup
        this.domNode = jQuery (container);
        this.activeItem = undefined;
        findItems = fluid.utils.adaptFindItems (findItems); // For backwards API compatibility

        // Configure default properties.
        options = options || {};
        var role = options.role || defaultContainerRole;
        var instructionMessageId = options.instructionMessageId || defaultInstructionMessageId;
        var keysets = setupKeysets(fluid.defaultKeysets, options.keysets);
        this.cssClasses = fluid.utils.initCssClassNames (defaultCssClassNames, options.cssClassNames);
        var avatarCreator = options.avatarCreator || defaultAvatarCreator;
        var kbDropWarning = fluid.utils.jById(options.dropWarningId);
        var mouseDropWarning;
        if (kbDropWarning) {
            mouseDropWarning = kbDropWarning.clone();
        }
        
        this.focusActiveItem = function (evt) {
            // If the active item has not been set yet, set it to the first selectable.
            if (!thisReorderer.activeItem) {
                var first = firstSelectable(findItems);
                if (!first) {  
                    return evt.stopPropagation();
                }
                jQuery(first).focus ();
            } else {
                jQuery (thisReorderer.activeItem).focus ();
            }
            return evt.stopPropagation();
        };

        var isMove = function (evt) {
            for (var i = 0; i < keysets.length; i++) {
                if (keysets[i].modifier(evt)) {
                    return true;
                }
            }
            return false;
        };
        
        var isActiveItemMovable = function () {
            return (jQuery.inArray (thisReorderer.activeItem, findItems.movables()) >= 0);
        };
        
        var setDropEffects = function (value) {
            var dropTargets = fluid.wrap (findItems.dropTargets());
            dropTargets.ariaState ("dropeffect", value);
        };
        
        this.handleKeyDown = function (evt) {
            if (!thisReorderer.activeItem || (thisReorderer.activeItem !== evt.target)) {
                return true;
            }
            // If the key pressed is ctrl, and the active item is movable we want to restyle the active item.
            var jActiveItem = jQuery (thisReorderer.activeItem);
            if (!jActiveItem.hasClass(thisReorderer.cssClasses.dragging) && isMove(evt)) {
               // Don't treat the active item as dragging unless it is a movable.
                if (isActiveItemMovable ()) {
                    jActiveItem.removeClass (thisReorderer.cssClasses.selected);
                    jActiveItem.addClass (thisReorderer.cssClasses.dragging);
                    jActiveItem.ariaState ("grab", "true");
                    setDropEffects("move");
                }
                return false;
            }
            // The only other keys we listen for are the arrows.
            return thisReorderer.handleDirectionKeyDown(evt);
        };

        this.handleKeyUp = function (evt) {
            if (!thisReorderer.activeItem || (thisReorderer.activeItem !== evt.target)) {
                return true;
            }
            var jActiveItem = jQuery (thisReorderer.activeItem);
            
            // Handle a key up event for the modifier
            if (jActiveItem.hasClass(thisReorderer.cssClasses.dragging) && !isMove(evt)) {
                if (kbDropWarning) {
                    kbDropWarning.hide();
                }
                jActiveItem.removeClass (thisReorderer.cssClasses.dragging);
                jActiveItem.addClass (thisReorderer.cssClasses.selected);
                jActiveItem.ariaState ("grab", "supported");
                setDropEffects("none");
                return false;
            }
            
            return false;
        };

        var moveItem = function (moveFunc){
            if (isActiveItemMovable ()) {
                moveFunc(thisReorderer.activeItem);
                // refocus on the active item because moving places focus on the body
                thisReorderer.activeItem.focus();
                jQuery(thisReorderer.activeItem).removeClass(thisReorderer.cssClasses.selected);
            }
        };
        
        var noModifier = function (evt) {
            return (!evt.ctrlKey && !evt.altKey && !evt.shiftKey && !evt.metaKey);
        };
        
        var moveItemForKeyCode = function (keyCode, keyset, layoutHandler) {
            var didMove = false;
            switch (keyCode) {
                case keyset.up:
                    moveItem (layoutHandler.moveItemUp);
                    didMove = true;
                    break;
                case keyset.down:
                    moveItem (layoutHandler.moveItemDown);
                    didMove = true;
                    break;
                case keyset.left:
                    moveItem (layoutHandler.moveItemLeft);
                    didMove = true;
                    break;
                case keyset.right:
                    moveItem (layoutHandler.moveItemRight);
                    didMove = true;
                    break;
            }
            
            return didMove;
        };
        
        var focusItemForKeyCode = function(keyCode, keyset, layoutHandler, activeItem){
            var didFocus = false;
            var item;
            switch (keyCode) {
                case keyset.up:
                    item = layoutHandler.getItemAbove (activeItem);
                    didFocus = true;
                    break;
                case keyset.down:
                    item = layoutHandler.getItemBelow (activeItem);
                    didFocus = true;
                    break;
                case keyset.left:
                    item = layoutHandler.getLeftSibling (activeItem);
                    didFocus = true;
                    break;
                case keyset.right:
                    item = layoutHandler.getRightSibling (activeItem);
                    didFocus = true;
                    break;
            }
            jQuery (item).focus ();
            
            return didFocus;
        };
        
        this.handleDirectionKeyDown = function (evt) {
            if (!thisReorderer.activeItem) {
                return true;
            }
            
            for (var i = 0; i < keysets.length; i++) {
                var keyset = keysets[i];
                var didProcessKey = false;
                if (keyset.modifier (evt)) {
                    if (kbDropWarning) {
                        kbDropWarning.hide();
                    }
                    didProcessKey = moveItemForKeyCode (evt.keyCode, keyset, layoutHandler);
            
                } else if (noModifier(evt)) {
                    didProcessKey = focusItemForKeyCode (evt.keyCode, keyset, layoutHandler, thisReorderer.activeItem);
                }
                
                // We got the right key press. Bail right away by swallowing the event.
                if (didProcessKey) {
                    return false;
                }
            }
            
            return true;
        };

        // Drag and drop setup code starts here. This needs to be refactored to be better contained.
        var dropMarker;

        var createDropMarker = function (tagName) {
            var dropMarker = jQuery(document.createElement (tagName));
            dropMarker.addClass (thisReorderer.cssClasses.dropMarker);
            dropMarker.hide();
            return dropMarker;
        };

        // Storing the last target that gets an 'over' event to work around the issue where
        // the avatar is below the mouse pointer and blocks events
        var targetOver;
        // Storing the most recent valid target and drop position to implement correct behaviour for locked modules
        var validTargetAndPos;
        
        /**
         * Creates an event handler for mouse move events that moves, shows and hides the drop marker accordingly
         * @param {Object} dropTargets    a list of valid drop targets
         */
        var createTrackMouse = function (dropTargets){
            dropTargets = fluid.wrap (dropTargets);
            var avatarId = dndFunctions.createAvatarId(thisReorderer.domNode.id);
           
            return function (evt){
                // Bail if we are not over a target
                if (!targetOver) {
                    return;
                }
                
                var target = dndFunctions.findTarget (evt.target, dropTargets, avatarId, targetOver);
                
                if (target) {
                    var position = layoutHandler.dropPosition(target, thisReorderer.activeItem, evt.clientX, evt.pageY);
                    if (position === fluid.position.DISALLOWED) {
                        if (mouseDropWarning) {
                            mouseDropWarning.show();
                        }
                    } 
                    else {
                        if (mouseDropWarning) {
                            mouseDropWarning.hide();
                        }
                        if (position !== fluid.position.USE_LAST_KNOWN) {
                            validTargetAndPos = {
                                target: target,
                                position: position
                            };
                            if (validTargetAndPos.position === fluid.position.BEFORE) {
                                jQuery(target).before(dropMarker);
                            }
                            else if (validTargetAndPos.position === fluid.position.AFTER) {
                                jQuery(target).after(dropMarker);
                            }
                            else if (validTargetAndPos.position === fluid.position.INSIDE) {
                                jQuery(target).append(dropMarker);
                            }
                        }
                        dropMarker.show();
                    }
                }
                else {
                    dropMarker.hide();
                    if (mouseDropWarning) {
                        mouseDropWarning.hide();
                    }
                }
            };
        };

        /**
         * Takes a jQuery object and adds 'movable' functionality to it
         */
        function initMovable (item) {
            item.addClass (thisReorderer.cssClasses.defaultStyle);
            item.ariaState ("grab", "supported");

            item.mouseover ( 
                function () {
                    var handle = jQuery (findItems.grabHandle (item[0]));
                    handle.addClass (thisReorderer.cssClasses.hover);
                }
            );
        
            item.mouseout (  
                function () {
                    var handle = jQuery (findItems.grabHandle (item[0]));
                    handle.removeClass (thisReorderer.cssClasses.hover);
                }
            );
        
            item.draggable ({
                refreshPositions: true,
                scroll: true,
                helper: function () {
                    var dropWarningEl;
                    if (mouseDropWarning) {
                        dropWarningEl = mouseDropWarning[0];
                    }
                    var avatar = jQuery (avatarCreator (item[0], thisReorderer.cssClasses.avatar, dropWarningEl));
                    avatar.attr("id", dndFunctions.createAvatarId(thisReorderer.domNode.id));
                    return avatar;
                },
                start: function (e, ui) {
                    item.focus ();
                    item.removeClass (thisReorderer.cssClasses.selected);
                    item.addClass (thisReorderer.cssClasses.mouseDrag);
                    item.ariaState ("grab", "true");
                    setDropEffects ("move");
                },
                stop: function(e, ui) {
                    item.removeClass (thisReorderer.cssClasses.mouseDrag);
                    item.addClass (thisReorderer.cssClasses.selected);
                    jQuery (thisReorderer.activeItem).ariaState ("grab", "supported");
                    dropMarker.hide();
                    ui.helper = null;
                    targetOver = null;
                    validTargetAndPos = null;
                    setDropEffects ("none");
                    
                    // refocus on the active item because moving places focus on the body
                    thisReorderer.activeItem.focus();
                },
                handle: findItems.grabHandle (item[0])
            });
        }   

        /**
         * Takes a jQuery object and a selector that matches movable items
         */
        function initDropTarget (item, selector) {
            item.ariaState ("dropeffect", "none");

            item.droppable ({
                accept: selector,
                greedy: true,
                tolerance: "pointer",
                over: function (e, ui) {
                    // Store the last target for the case when the avatar gets the mouse move instead of the droppable below it.
                    // We do not want to store the value if the position is 'USE_LAST_KNOWN'
                    var position = layoutHandler.dropPosition(item[0], ui.draggable[0], e.clientX, e.pageY);
                    if (position !== fluid.position.USE_LAST_KNOWN) {
                        targetOver = ui.element[0];
                    }
                },
                drop: function (e, ui) {
                    if (validTargetAndPos) {
                        layoutHandler.mouseMoveItem(ui.draggable[0], validTargetAndPos.target, e.clientX, e.pageY, validTargetAndPos.position);
                    }
                }
            });
        }
   
        var initSelectables = function (selectables) {
            var handleBlur = function (evt) {
                changeSelectedToDefault (jQuery(this), thisReorderer.cssClasses);
                return evt.stopPropagation();
            };
        
            var handleFocus = function (evt) {
                thisReorderer.selectItem (this);
                return evt.stopPropagation();
            };
        
            // set up selectables 
            // Remove the selectables from the taborder
            for (var i = 0; i < selectables.length; i++) {
                var item = jQuery(selectables[i]);
                item.tabindex ("-1");
                item.blur (handleBlur);
                item.focus (handleFocus);
            
                item.ariaRole (role.item);
                item.ariaState ("selected", "false");
                item.ariaState ("disabled", "false");
            }
        };
    
        var initItems = function () {
            var i;
            var movables = fluid.wrap (findItems.movables());
            var dropTargets = fluid.wrap (findItems.dropTargets());
            initSelectables (fluid.wrap (findItems.selectables ()));
        
            // Setup movables
            for (i = 0; i < movables.length; i++) {
                var item = movables[i];
                initMovable (jQuery (item));
            }

            // In order to create valid html, the drop marker is the same type as the node being dragged.
            // This creates a confusing UI in cases such as an ordered list. 
            // drop marker functionality should be made pluggable. 
            if (movables.length > 0) {
                dropMarker = createDropMarker(movables[0].tagName);
            }

            // Create a simple predicate function that will identify items that can be dropped.
            var droppablePredicate = function (potentialDroppable) {
                return (movables.index(potentialDroppable[0]) > -1);    
            };
        
            // Setup dropTargets
            for (i = 0; i < dropTargets.length; i++) {
                initDropTarget (jQuery (dropTargets[i]), droppablePredicate);
            }         
        };

        // Final initialization of the Reorderer at the end of the construction process 
        if (this.domNode) {
            bindHandlersToContainer (this.domNode, 
                thisReorderer.focusActiveItem,
                thisReorderer.handleKeyDown,
                thisReorderer.handleKeyUp,
                createTrackMouse (findItems.dropTargets()));
            addRolesToContainer (this.domNode, findItems, role);
            // ensure that the Reorderer container is in the tab order
            if (!this.domNode.hasTabindex() || (this.domNode.tabindex() < 0)) {
                this.domNode.tabindex("0");
            }
            initItems();
        }
    };
    
    fluid.Reorderer.prototype.selectItem = function (anItem) {
        // Set the previous active item back to its default state.
        if (this.activeItem && this.activeItem !== anItem) {
            changeSelectedToDefault (jQuery (this.activeItem), this.cssClasses);
        }
        // Then select the new item.
        this.activeItem = anItem;
        var jItem = jQuery(anItem);
        jItem.removeClass (this.cssClasses.defaultStyle);
        jItem.addClass (this.cssClasses.selected);
        jItem.ariaState ("selected", "true");
        this.domNode.ariaState ("activedescendent", anItem.id);
    };
    
    var buildFnFromSelector = function (selector, container) {
        return function () {
            return jQuery(selector, container);
        };
    };
    
    var buildFindItems = function (itemSelectors, container) {
        // If a single selector has been passed in we just need to wrap it in a function.
        if (typeof itemSelectors === 'string') {
            return buildFnFromSelector(itemSelectors, container);
        } 

        // This code is very similar to fluid.utils.adaptFindItems. 
        // It would be nice if adaptFindItems could take in either functions or selectors. 
        var findItems = {};
        // TODO: We should check if there is no movable and throw an error.
        findItems.movables = buildFnFromSelector(itemSelectors.movables, container);
        if (itemSelectors.selectables) {
            findItems.selectables = buildFnFromSelector(itemSelectors.selectables, container);
        }
        if (itemSelectors.dropTargets) {
            findItems.dropTargets = buildFnFromSelector(itemSelectors.dropTargets, container);
        }
        if (itemSelectors.grabHandle) {
            findItems.grabHandle = function (item) {
                return jQuery(itemSelectors.grabHandle, item);
            };
        }
        return findItems;
    };
    
    // Simplified API for reordering lists and grids.
    var simpleInit = function (containerSelector, itemSelector, layoutHandlerFn, orderChangedCallback, options) {
        var container = jQuery(containerSelector);
        var itemFinder = buildFindItems(itemSelector, container);
        
        var lOptions = options || {};
        lOptions.orderChangedCallback = orderChangedCallback;
        var layoutHandler = new layoutHandlerFn(itemFinder, lOptions);
        
        return new fluid.Reorderer(container, itemFinder, layoutHandler, options);
    };
    
    fluid.reorderList = function (containerSelector, itemSelector, orderChangedCallback, options) {
        return simpleInit(containerSelector, itemSelector, fluid.ListLayoutHandler, orderChangedCallback, options);
    };
    
    fluid.reorderGrid = function (containerSelector, itemSelector, orderChangedCallback, options) {
        return simpleInit(containerSelector, itemSelector, fluid.GridLayoutHandler, orderChangedCallback, options); 
    };
}) (jQuery, fluid);

/*******************
 * Layout Handlers *
 *******************/
(function (jQuery, fluid) {
    // Shared private functions.
    var moveItem = function (item, relatedItemInfo, position, wrappedPosition) {
        var itemPlacement = position;
        if (relatedItemInfo.hasWrapped) {
            itemPlacement = wrappedPosition;
        }
        
        if (itemPlacement === fluid.position.AFTER) {
            jQuery (relatedItemInfo.item).after (item);
        } else {
            jQuery (relatedItemInfo.item).before (item);
        } 
    };
    
    /**
     * For drag-and-drop during the drag:  is the mouse over the "before" half
     * of the droppable?  In the case of a vertically oriented set of orderables,
     * "before" means "above".  For a horizontally oriented set, "before" means
     * "left of".
     */
    var mousePosition = function (droppableEl, orientation, x, y) {    	
        var mid;
        var isBefore;
        if (orientation === fluid.orientation.VERTICAL) {
            mid = jQuery (droppableEl).offset().top + (droppableEl.offsetHeight / 2);
            isBefore = y < mid;
        } else {
            mid = jQuery (droppableEl).offset().left + (droppableEl.offsetWidth / 2);
            isBefore = x < mid;
        }
        
        return (isBefore ? fluid.position.BEFORE : fluid.position.AFTER);
    };    
    
    var itemInfoFinders = {
        /*
         * A general get{Left|Right}SiblingInfo() given an item, a list of orderables and a direction.
         * The direction is encoded by either a +1 to move right, or a -1 to
         * move left, and that value is used internally as an increment or
         * decrement, respectively, of the index of the given item.
         */
        getSiblingInfo: function (item, orderables, /* NEXT, PREVIOUS */ direction) {
            var index = jQuery (orderables).index (item) + direction;
            var hasWrapped = false;
                
            // Handle wrapping to 'before' the beginning. 
            if (index === -1) {
                index = orderables.length - 1;
                hasWrapped = true;
            }
            // Handle wrapping to 'after' the end.
            else if (index === orderables.length) {
                index = 0;
                hasWrapped = true;
            } 
            // Handle case where the passed-in item is *not* an "orderable"
            // (or other undefined error).
            //
            else if (index < -1 || index > orderables.length) {
                index = 0;
            }
            
            return {item: orderables[index], hasWrapped: hasWrapped};
        },

        /*
         * Returns an object containing the item that is to the right of the given item
         * and a flag indicating whether or not the process has 'wrapped' around the end of
         * the row that the given item is in
         */
        getRightSiblingInfo: function (item, orderables) {
            return this.getSiblingInfo (item, orderables, fluid.direction.NEXT);
        },
        
        /*
         * Returns an object containing the item that is to the left of the given item
         * and a flag indicating whether or not the process has 'wrapped' around the end of
         * the row that the given item is in
         */
        getLeftSiblingInfo: function (item, orderables) {
            return this.getSiblingInfo (item, orderables, fluid.direction.PREVIOUS);
        },
        
        /*
         * Returns an object containing the item that is below the given item in the current grid
         * and a flag indicating whether or not the process has 'wrapped' around the end of
         * the column that the given item is in. The flag is necessary because when an image is being
         * moved to the resulting item location, the decision of whether or not to insert before or
         * after the item changes if the process wrapped around the column.
         */
        getItemInfoBelow: function (inItem, orderables) {
            var curCoords = jQuery (inItem).offset();
            var i, iCoords;
            var firstItemInColumn, currentItem;
            
            for (i = 0; i < orderables.length; i++) {
                currentItem = orderables [i];
                iCoords = jQuery (orderables[i]).offset();
                if (iCoords.left === curCoords.left) {
                    firstItemInColumn = firstItemInColumn || currentItem;
                    if (iCoords.top > curCoords.top) {
                        return {item: currentItem, hasWrapped: false};
                    }
                }
            }
    
            firstItemInColumn = firstItemInColumn || orderables [0];
            return {item: firstItemInColumn, hasWrapped: true};
        },
        
        /*
         * Returns an object containing the item that is above the given item in the current grid
         * and a flag indicating whether or not the process has 'wrapped' around the end of
         * the column that the given item is in. The flag is necessary because when an image is being
         * moved to the resulting item location, the decision of whether or not to insert before or
         * after the item changes if the process wrapped around the column.
         */
         getItemInfoAbove: function (inItem, orderables) {
            var curCoords = jQuery (inItem).offset();
            var i, iCoords;
            var lastItemInColumn, currentItem;
            
            for (i = orderables.length - 1; i > -1; i--) {
                currentItem = orderables [i];
                iCoords = jQuery (orderables[i]).offset();
                if (iCoords.left === curCoords.left) {
                    lastItemInColumn = lastItemInColumn || currentItem;
                    if (curCoords.top > iCoords.top) {
                        return {item: currentItem, hasWrapped: false};
                    }
                }
            }
    
            lastItemInColumn = lastItemInColumn || orderables [0];
            return {item: lastItemInColumn, hasWrapped: true};
        }
    
    };
    
    // Public layout handlers.
    fluid.ListLayoutHandler = function (findItems, options) {
        findItems = fluid.utils.adaptFindItems (findItems);
        var orderChangedCallback = function () {};
        var orientation = fluid.orientation.VERTICAL;
        if (options) {
            orderChangedCallback = options.orderChangedCallback || orderChangedCallback;
            orientation = options.orientation || orientation;
        }
                
        this.getRightSibling = function (item) {
            return itemInfoFinders.getRightSiblingInfo (item, findItems.selectables ()).item;
        };
        
        this.moveItemRight = function (item) {
        	var rightSiblingInfo = itemInfoFinders.getRightSiblingInfo (item, findItems.movables ());
            moveItem (item, rightSiblingInfo, fluid.position.AFTER, fluid.position.BEFORE);
            orderChangedCallback();
        };
    
        this.getLeftSibling = function (item) {
            return itemInfoFinders.getLeftSiblingInfo(item, findItems.selectables ()).item;
        };
    
        this.moveItemLeft = function (item) {
         	var leftSiblingInfo = itemInfoFinders.getLeftSiblingInfo (item, findItems.movables ());
            moveItem (item, leftSiblingInfo, fluid.position.BEFORE, fluid.position.AFTER);
            orderChangedCallback();
        };
    
        this.getItemBelow = this.getRightSibling;
    
        this.getItemAbove = this.getLeftSibling;
        
        this.moveItemUp = this.moveItemLeft;
        
        this.moveItemDown = this.moveItemRight;
    
        this.dropPosition = function (target, moving, x, y) {
            return mousePosition (target, orientation, x, y);
        };
        
        this.mouseMoveItem = function (moving, target, x, y) {
            var whereTo = this.dropPosition (target, moving, x, y);
            if (whereTo === fluid.position.BEFORE) {
                jQuery (target).before (moving);
            } else if (whereTo === fluid.position.AFTER) {
                jQuery (target).after (moving);
            }
            orderChangedCallback();
        };
        
    }; // End ListLayoutHandler
    
	/*
	 * Items in the Lightbox are stored in a list, but they are visually presented as a grid that
	 * changes dimensions when the window changes size. As a result, when the user presses the up or
	 * down arrow key, what lies above or below depends on the current window size.
	 * 
	 * The GridLayoutHandler is responsible for handling changes to this virtual 'grid' of items
	 * in the window, and of informing the Lightbox of which items surround a given item.
	 */
	fluid.GridLayoutHandler = function (findItems, options) {
        fluid.ListLayoutHandler.call (this, findItems, options);

        findItems = fluid.utils.adaptFindItems (findItems);
        
        var orderChangedCallback = function () {};
        if (options) {
            orderChangedCallback = options.orderChangedCallback || orderChangedCallback;
        }
        
        var orientation = fluid.orientation.HORIZONTAL;
                
	    this.getItemBelow = function(item) {
	        return itemInfoFinders.getItemInfoBelow (item, findItems.selectables ()).item;
	    };
	
	    this.moveItemDown = function (item) {
	    	var itemBelow = itemInfoFinders.getItemInfoBelow (item, findItems.movables ());
	        moveItem (item, itemBelow, fluid.position.AFTER, fluid.position.BEFORE);
            orderChangedCallback(); 
	    };
	            
	    this.getItemAbove = function (item) {
	        return itemInfoFinders.getItemInfoAbove (item, findItems.selectables ()).item;   
	    }; 
	    
	    this.moveItemUp = function (item) {
	    	var itemAbove = itemInfoFinders.getItemInfoAbove (item, findItems.movables ());
	        moveItem (item, itemAbove, fluid.position.BEFORE, fluid.position.AFTER);
            orderChangedCallback(); 
	    };
	                
	    // We need to override ListLayoutHandler.dropPosition to ensure that the local private
	    // orientation is used.
        this.dropPosition = function (target, moving, x, y) {
            return mousePosition (target, orientation, x, y);
        };
        
	}; // End of GridLayoutHandler
    
    var defaultWillShowKBDropWarning = function (item, dropWarning) {
        if (dropWarning) {
            var offset = jQuery(item).offset();
            dropWarning = jQuery(dropWarning);
            dropWarning.css("position", "absolute");
            dropWarning.css("top", offset.top);
            dropWarning.css("left", offset.left);
        }
    };

    /*
     * Module Layout Handler for reordering content modules.
     * 
     * General movement guidelines:
     * 
     * - Arrowing sideways will always go to the top (moveable) module in the column
     * - Moving sideways will always move to the top available drop target in the column
     * - Wrapping is not necessary at this first pass, but is ok
     */
    fluid.ModuleLayoutHandler = function (layout, targetPerms, options) {
        var orientation = fluid.orientation.VERTICAL;
        
        // Configure optional parameters
        targetPerms = targetPerms || fluid.moduleLayout.buildEmptyPerms(layout);
        options = options || {};
        var orderChangedCallback = options.orderChangedCallback || function () {};
        if (options.orderChangedCallbackUrl) {
            // Create the orderChangedCallback function
            orderChangedCallback = function () {
                jQuery.post (options.orderChangedCallbackUrl, 
                    JSON.stringify (layout),
                    function (data, textStatus) { 
                        targetPerms = data; 
                    }, 
                    "json");
            };
        } 
        var dropWarning = fluid.utils.jById(options.dropWarningId);
        var willShowKBDropWarning = options.willShowKBDropWarning || defaultWillShowKBDropWarning;
        
        // Private Methods.
        /*
	     * Find an item's sibling in the vertical direction based on the
	     * layout.  This assumes that there is no wrapping the top and
	     * bottom of the columns, and returns the given item if at top
	     * and seeking the previous item, or at the bottom and seeking
	     * the next item.
	     */
	    var getVerticalSibling = function (item, /* NEXT, PREVIOUS */ direction) {
	    	var siblingId = fluid.moduleLayout.itemAboveBelow (item.id, direction, layout);
            return fluid.utils.jById (siblingId)[0];
	    };
	
	    /*
	     * Find an item's sibling in the horizontal direction based on the
	     * layout.  This assumes that there is no wrapping the ends of
	     * the rows, and returns the given item if left most and
	     * seeking the previous item, or if right most and seeking
	     * the next item.
	     */
	    var getHorizontalSibling = function (item, /* NEXT, PREVIOUS */ direction) {
	        var itemId = fluid.moduleLayout.firstItemInAdjacentColumn (item.id, direction, layout);
	        return fluid.utils.jById (itemId)[0];
        };
	    	    
        // This should probably be part of the public API so it can be configured.
        var move = function (item, relatedItem, position /* BEFORE, AFTER or INSIDE */) {
            if (!item || !relatedItem) {
                return;
            }           
            if (position === fluid.position.BEFORE) {
                jQuery(relatedItem).before(item);
            } else if (position === fluid.position.AFTER) {
                jQuery(relatedItem).after(item);
            } else if (position === fluid.position.INSIDE) {
                jQuery(relatedItem).append(item);
            }  // otherwise it's either DISALLOWED or USE_LAST_KNOWN
            
            fluid.moduleLayout.updateLayout (item.id, relatedItem.id, position, layout);
            orderChangedCallback (); 
        };
        
        var moveHorizontally = function (item, direction /* PREVIOUS, NEXT */) {
            var targetInfo = fluid.moduleLayout.findTarget (item.id, direction, layout, targetPerms);
            var targetItem = fluid.utils.jById (targetInfo.id)[0];
            move (item, targetItem, targetInfo.position);
        };
        
        var moveVertically = function (item, targetFunc) {
            var targetAndPos = targetFunc(item.id, layout, targetPerms);
            var target = fluid.utils.jById(targetAndPos.id)[0]; 
            if (targetAndPos.position === fluid.position.DISALLOWED) {
                if (dropWarning) {
                    willShowKBDropWarning(item, dropWarning[0]);
                    dropWarning.show();
                }
            } else if (targetAndPos.position !== fluid.position.USE_LAST_KNOWN) {
                move(item, target, targetAndPos.position);
            }
        };
        
        // Public Methods
	    this.getRightSibling = function (item) {
	        return getHorizontalSibling (item, fluid.direction.NEXT);
	    };
	    
	    this.moveItemRight = function (item) {
	    	moveHorizontally (item, fluid.direction.NEXT);
	    };
	
	    this.getLeftSibling = function (item) {
	        return getHorizontalSibling (item, fluid.direction.PREVIOUS);
	    };
	
	    this.moveItemLeft = function (item) {
            moveHorizontally (item, fluid.direction.PREVIOUS);
	    };
	
	    this.getItemAbove = function (item) {
	    	return getVerticalSibling (item, fluid.direction.PREVIOUS);
	    };
	    
	    this.moveItemUp = function (item) {
            moveVertically(item, fluid.moduleLayout.targetAndPositionAbove);
	    };
	        
	    this.getItemBelow = function (item) {
	    	return getVerticalSibling (item, fluid.direction.NEXT);
	    };
	
	    this.moveItemDown = function (item) {
            moveVertically(item, fluid.moduleLayout.targetAndPositionBelow);
	    };
	    
        this.dropPosition = function (target, moving, x, y) {
            if (fluid.moduleLayout.isColumn (target.id, layout)) {
                var lastItemInColId = fluid.moduleLayout.lastItemInCol(target.id, layout);
                if (lastItemInColId === undefined) {
                    return fluid.position.INSIDE;
                }
                var lastItem = fluid.utils.jById(lastItemInColId);
                var topOfEmptySpace = lastItem.offset().top + lastItem.height();
                
                if (y > topOfEmptySpace) {
                    return fluid.position.INSIDE;
                } else {
                    return fluid.position.USE_LAST_KNOWN;
                }
            }
            
            var position = mousePosition (target, orientation, x, y);
            var canDrop = fluid.moduleLayout.canMove (moving.id, target.id, position, layout, targetPerms);
	    	if (canDrop) {
                return position;
	    	}
	    	else {
	    		return fluid.position.DISALLOWED;
	    	}
        };

        this.mouseMoveItem = function (moving, target, x, y, position) {
            move(moving, target, position);
        };
        
    }; // End ModuleLayoutHandler
}) (jQuery, fluid);

fluid.moduleLayout = function (jQuery, fluid) {
    var internals = {
        layoutWalker: function (fn, layout) {
            for (var col = 0; col < layout.columns.length; col++) {
                var idsInCol = layout.columns[col].children;
                for (var i = 0; i < idsInCol.length; i++) {
                    var fnReturn = fn (idsInCol, i, col);
                    if (fnReturn) {
                        return fnReturn;
                    }
                }
            }
        },
        
        /**
         * Calculate the location of the item and the column in which it resides.
         * @return  An object with column index and item index (within that column) properties.
         *          These indices are -1 if the item does not exist in the grid.
         */
        findColumnAndItemIndices: function (itemId, layout) {
            var findIndices = function (idsInCol, index, col) {
                if (idsInCol[index] === itemId) {
                    return {columnIndex: col, itemIndex: index};
                }  
            };
            
            var indices = internals.layoutWalker (findIndices, layout);
            return indices || { columnIndex: -1, itemIndex: -1 };
        },
        
        findColIndex: function (colId, layout) {
            for (var col = 0; col < layout.columns.length; col++ ) {
                if (colId === layout.columns[col].id) {
                    return col;
                }
            }
            return -1;
        },
        
        findItemIndex: function (itemId, layout) {
            return internals.findColumnAndItemIndices (itemId, layout).itemIndex;
        },
        
        numColumns: function (layout) {
            return layout.columns.length;
        },
        
        numModules: function (layout) {
            var numModules = 0;
            for (var col = 0; col < layout.columns.length; col++) {
                numModules += layout.columns[col].children.length;
            }
            return numModules;
        },
        
        isColumnIndex: function (index, layout) {
            return (index < layout.columns.length) && (index >= 0);
        },
        
        /**
         * Returns targetIndex
         * This could have been written in two functions for clarity however it gets called a lot and 
         * the two functions were considerably less performant then this single function.
         * 
         * Item index is the row in the permissions object pertaining to the item.
         * Target index is the column in the permission object refering to the postion before or after the target.
         */
        findItemAndTargetIndices: function (itemId, targetId, position, layout) {
            var columns = layout.columns;
            
            // Default to not found.
            var foundIndices = {
                itemIndex: -1,
                targetIndex: -1
            };
            
            // If the ids are invalid, bail immediately.
            if (!itemId || !targetId) {            
                return foundIndices;
            }

            var itemIndexCounter = 0;
            var targetIndexCounter = position;
            
            for (var i = 0; i < columns.length; i++) {
                var idsInCol = columns[i].children;
                for (var j = 0; j < idsInCol.length; j++) {
                    var currId = idsInCol[j];                    
                    if (currId === itemId) {
                        foundIndices.itemIndex = itemIndexCounter; 
                    }
                    if (currId === targetId) {
                        foundIndices.targetIndex = targetIndexCounter; 
                    }
                    
                    // Check if we're done, and if so, bail early.
                    if (foundIndices.itemIndex >= 0 && foundIndices.targetIndex >= 0) {
                        return foundIndices;
                    }
                    
                    // Increment our index counters and keep searching.
                    itemIndexCounter++;
                    targetIndexCounter++;
                }
                
                // Make sure we account for the additional drop target at the end of a column.
                targetIndexCounter++;
            }

            return foundIndices;     
        },
        
        /**
         * Return the item in the given column (index) and at the given position (index)
         * in that column.  If either of the column or item index is out of bounds, this
         * returns null.
         */
        getItemAt: function (columnIndex, itemIndex, layout) {
            var itemId = null;
            var cols = layout.columns;
            
            if (columnIndex >= 0 && columnIndex < cols.length) {
                var idsInCol = cols[columnIndex].children;
                if (itemIndex >= 0 && itemIndex < idsInCol.length) {
                    itemId = idsInCol[itemIndex];
                }
            }
            
            return itemId;
        },
        
        canItemMove: function (itemIndex, perms) {
            var itemPerms = perms[itemIndex];
            for (var i = 0; i < itemPerms.length; i++) {
                if (itemPerms[i] === 1) {
                    return true;
                }
            }
            return false;
        }, 
        
        isDropTarget: function (beforeTargetIndex, perms) {
            for (var i = 0; i < perms.length; i++) {
                if (perms[i][beforeTargetIndex] === 1 || perms[i][beforeTargetIndex + 1] === 1) {
                    return true;
                }
            }
            return false;
        },
        
        targetAndPos: function(itemId, position, layout, perms){
            var inc = (position === fluid.position.BEFORE) ? -1 : 1;            
            var startCoords = internals.findColumnAndItemIndices (itemId, layout);
            var defaultTarg = {
                    id: itemId,
                    position: fluid.position.USE_LAST_KNOWN
                };
            
            // If invalid column, return USE_LAST_KNOWN
            if (startCoords.columnIndex < 0) {
                return defaultTarg;
            }
            
            // Loop thru the target column's items, starting with the item adjacent to the given item,
            // looking for an item that can be moved to.
            var idsInCol = layout.columns[startCoords.columnIndex].children;
            var firstTarg;
            for (var i = startCoords.itemIndex + inc; i > -1 && i < idsInCol.length; i = i + inc) {
                var targetId = idsInCol[i];
                if (fluid.moduleLayout.canMove (itemId, targetId, position, layout, perms)) {
                    // Found a valid move - return
                    return {
                        id: targetId,
                        position: position
                    };
                } else if (!firstTarg) {
                    firstTarg = { id: targetId, position: fluid.position.DISALLOWED};
                }
            }
        
            // Didn't find a valid move so return the first target
            return firstTarg || defaultTarg;                        
        },
            
        findPortletsInColumn: function (portlets, column) {
            var portletsForColumn = [];
            portlets.each(function (idx, portlet) {
                if (jQuery("[id=" + portlet.id + "]", column)[0]) {
                    portletsForColumn.push(portlet);
                }
            });
            
            return portletsForColumn;
        },
    
    	columnStructure: function (column, portletsInColumn) {
            var structure = {};
            structure.id = column.id;
            structure.children = [];
            jQuery(portletsInColumn).each(function (idx, portlet) {
                structure.children.push(portlet.id);
            });
            
            return structure;
        }

    };   
    
	// Public API.
    return {
        internals: internals,

        isColumn: function (id, layout) {
            var colIndex = internals.findColIndex(id, layout);
            return (colIndex > -1);
        },
        
       /**
        * Determine if a given item can move before or after the given target position, given the
        * permissions information.
        */
    	canMove: function (itemId, targetItemId, position, layout, perms) {
    	    if ((position === fluid.position.USE_LAST_KNOWN) || (position === fluid.position.DISALLOWED)) {
    	        return false;
    	    }
    	    if (position === fluid.position.INSIDE) {
    	        return true;
    	    }
    		var indices = internals.findItemAndTargetIndices (itemId, targetItemId, position, layout);
            return (!!perms[indices.itemIndex][indices.targetIndex]); 
        },
        
        /**
         * Given an item id, and a direction, find the top item in the next/previous column.
         */
        firstItemInAdjacentColumn: function (itemId, /* PREVIOUS, NEXT */ direction, layout) {
            var findItemInAdjacentCol = function (idsInCol, index, col) {
                var id = idsInCol[index];
                if (id === itemId) {
                    var adjacentCol = col + direction;
                    var adjacentItem = internals.getItemAt (adjacentCol, 0, layout);
                    // if there are no items in the adjacent column, keep checking further columns
                    while (!adjacentItem) {
                        adjacentCol = adjacentCol + direction;
                        if (internals.isColumnIndex(adjacentCol, layout)) {
                            adjacentItem = internals.getItemAt (adjacentCol, 0, layout);
                        } else {
                            adjacentItem = itemId;
                        }
                    }
                    return adjacentItem; 
                //    return internals.getItemAt (adjacentCol, 0, layout);
                }
            };
            
            return internals.layoutWalker (findItemInAdjacentCol, layout) || itemId; 
        }, 
        
        /**
         * Return the item above/below the given item within that item's column.  If at
         * bottom of column or at top, return the item itelf (no wrapping).
         */
        itemAboveBelow: function (itemId, /*PREVIOUS, NEXT*/ direction, layout) {
            var findItemAboveBelow = function (idsInCol, index) {
                if (idsInCol[index] === itemId) {
                    var siblingIndex = index + direction;
                    if ((siblingIndex < 0) || (siblingIndex >= idsInCol.length)) {
                        return itemId;
                    } else {
                        return idsInCol[siblingIndex];
                    }
                }
        	};

            return internals.layoutWalker (findItemAboveBelow, layout) || itemId;
        },
        
        /**
         * Move an item within the layout object. 
         */
        updateLayout: function (itemId, targetId, position, layout) {
            if (!itemId || !targetId || itemId === targetId) { 
                return; 
            }
            var itemIndices = internals.findColumnAndItemIndices (itemId, layout);
            layout.columns[itemIndices.columnIndex].children.splice (itemIndices.itemIndex, 1);
            var targetCol;
            if (position === fluid.position.INSIDE) {
                targetCol = layout.columns[internals.findColIndex (targetId, layout)].children;
                targetCol.splice (targetCol.length, 0, itemId);

            } else {
                var relativeItemIndices = internals.findColumnAndItemIndices (targetId, layout);
                targetCol = layout.columns[relativeItemIndices.columnIndex].children;
                targetCol.splice (relativeItemIndices.itemIndex + position, 0, itemId);
            }

        },
        
        /**
         * Find the first target that can be moved to in the given column, possibly moving to the end
         * of the column if there are no valid drop targets. 
         * @return Object containing id (the id of the target) and position (relative to the target)
         */
        findTarget: function (itemId, /* NEXT, PREVIOUS */ direction, layout, perms) {
            var targetColIndex = internals.findColumnAndItemIndices (itemId, layout).columnIndex + direction;
            var targetCol = layout.columns[targetColIndex];
			
            // If column is invalid, bail returning the current position.
            if (targetColIndex < 0 || targetColIndex >= internals.numColumns (layout)) {
                return { id: itemId, position: fluid.position.BEFORE };               
            }
            
            // Loop thru the target column's items, looking for the first item that can be moved to.
            var idsInCol = targetCol.children;
            for (var i = 0; (i < idsInCol.length); i++) {
                var targetId = idsInCol[i];
                if (fluid.moduleLayout.canMove (itemId, targetId, fluid.position.BEFORE, layout, perms)) {
                    return { id: targetId, position: fluid.position.BEFORE };
                }
                else if (fluid.moduleLayout.canMove (itemId, targetId, fluid.position.AFTER, layout, perms)) {
                    return { id: targetId, position: fluid.position.AFTER };
                }
            }
			
            // no valid modules found, so target is the column itself
            return { id: targetCol.id, position: fluid.position.INSIDE };
        },

        /**
         * Returns a valid drop target and position above the item being moved.
         * @param {Object} itemId The id of the item being moved
         * @param {Object} layout 
         * @param {Object} perms
         * @returns {Object} id: the target id, position: a 'fluid.position' value relative to the target
         */
        targetAndPositionAbove: function (itemId, layout, perms) {
            return internals.targetAndPos (itemId, fluid.position.BEFORE, layout, perms);
        },
        
        /**
         * Returns a valid drop target and position below the item being moved.
         * @param {Object} itemId The id of the item being moved
         * @param {Object} layout 
         * @param {Object} perms
         * @returns {Object} id: the target id, position: a 'fluid.position' value relative to the target
         */
        targetAndPositionBelow: function (itemId, layout, perms) {
            return internals.targetAndPos (itemId, fluid.position.AFTER, layout, perms);
        },
        
        /**
         * Determine the moveables, selectables, and drop targets based on the information
         * in the layout and permission objects.
         */
        createFindItems: function (layout, perms, grabHandle) {
            perms = perms || fluid.moduleLayout.buildEmptyPerms(layout);
            var findItems = {};
            findItems.grabHandle = grabHandle;
            
            var selectablesSelector;
            var movablesSelector;
            var dropTargets;
            
            var cols = layout.columns;
            for (var i = 0; i < cols.length; i++) {
                var idsInCol = cols[i].children;
                for (var j = 0; j < idsInCol.length; j++) {
                    var itemId = idsInCol[j];
                    var idSelector = "[id=" + itemId  + "]";
                    selectablesSelector = selectablesSelector ? selectablesSelector + "," + idSelector : idSelector;
                    
                    var indices = internals.findItemAndTargetIndices (itemId, itemId, fluid.position.BEFORE, layout);
                    if (internals.canItemMove (indices.itemIndex, perms)) {
                        movablesSelector = movablesSelector ? movablesSelector + "," + idSelector : idSelector; 
                    }
                    if (internals.isDropTarget (indices.targetIndex, perms)) {
                    	dropTargets = dropTargets ? dropTargets + "," + idSelector : idSelector;
                    }
                }
                // now add the column itself
                var colIdSelector = "[id=" + cols[i].id  + "]";
                dropTargets = dropTargets ? dropTargets + "," + colIdSelector : colIdSelector;
            }
            
            findItems.selectables = function () {
                return jQuery (selectablesSelector);
            };
            
            findItems.movables = function () {
                return jQuery (movablesSelector);
            };

            findItems.dropTargets = function() {
                return jQuery (dropTargets);
            };
                      
            return findItems;
        },
        
        containerId: function (layout) {
            return layout.id;
        },
        
        lastItemInCol: function (colId, layout) {
            var colIndex = internals.findColIndex(colId, layout);
            var col = layout.columns[colIndex];
            var numChildren = col.children.length;
            if (numChildren > 0) {
                return col.children[numChildren-1];                
            }
            return undefined;
        },
        
        /**
         * Builds a fake permission object stuffed with 1s.
         * @param {Object} layout
         */
        buildEmptyPerms: function (layout) {
            var numCols = internals.numColumns(layout);
            var numModules = internals.numModules(layout);
            
            var permsStructure = [];
            // Each column has a drop target at its top.
            // Each portlet has a drop target below it.
            var numItemsInBitmap = numCols + numModules;
            for (var i = 0; i < numModules; i++) {
                var rowForPortlet = [];
                // Stuff the whole structure with 1s to dispense with permissions altogether.
                for (var j = 0; j < numItemsInBitmap; j++) {
                    rowForPortlet.push(1);
                }
                permsStructure.push(rowForPortlet);                
            }
            
            return permsStructure;
        },
    
        /**
         * Builds a layout object from a set of columns and portlets.
         * @param {jQuery} container
         * @param {jQuery} columns
         * @param {jQuery} portlets
         */
        buildLayout: function (container, columns, portlets) {
            var layoutStructure = {};
            layoutStructure.id = container[0].id;
            layoutStructure.columns = [];
            columns.each(function (idx, column) {
                var portletsInColumn = internals.findPortletsInColumn(portlets, column);
                layoutStructure.columns.push(internals.columnStructure(column, portletsInColumn));
            });
            
            return layoutStructure;
        }
    };	
} (jQuery, fluid);
/*
Copyright 2007 - 2008 University of Toronto

Licensed under the Educational Community License (ECL), Version 2.0 or the New
BSD license. You may not use this file except in compliance with one these
Licenses.

You may obtain a copy of the ECL 2.0 License and BSD License at
https://source.fluidproject.org/svn/LICENSE.txt
*/

var fluid = fluid || {};

(function (fluid) {
    var createLayoutCustomizer = function (layout, perms, orderChangedCallbackUrl, options) {
        // Configure options
        options = options || {};
        var rOptions = options;
        rOptions.role = rOptions.role || fluid.roles.REGIONS;

        var lhOptions = {};
        lhOptions.orderChangedCallbackUrl = orderChangedCallbackUrl;
        lhOptions.orderChangedCallback = options.orderChangedCallback;
        lhOptions.dropWarningId = options.dropWarningId;

        var reordererRoot = fluid.utils.jById (fluid.moduleLayout.containerId (layout));
        var items = fluid.moduleLayout.createFindItems (layout, perms, rOptions.grabHandle);    
        var layoutHandler = new fluid.ModuleLayoutHandler (layout, perms, lhOptions);

        return new fluid.Reorderer (reordererRoot, items, layoutHandler, rOptions);
    };
    

    /**
     * Creates a layout customizer from a combination of a layout and permissions object.
     * @param {Object} layout a layout object. See http://wiki.fluidproject.org/x/FYsk for more details
     * @param {Object} perms a permissions data structure. See the above documentation
     */
    fluid.initLayoutCustomizer = function (layout, perms, orderChangedCallbackUrl, options) {        
        return createLayoutCustomizer (layout, perms, orderChangedCallbackUrl, options);
    };

    /**
     * Simple way to create a layout customizer.
     * @param {selector} a selector for the layout container
     * @param {Object} a map of selectors for columns and modules within the layout
     * @param {Function} a function to be called when the order changes 
     * @param {Object} additional configuration options
     */
    fluid.reorderLayout = function(containerSelector, layoutSelectors, orderChangedCallback, options) {
        options = options || {};
        options.orderChangedCallback = orderChangedCallback;
        
        var container = jQuery(containerSelector);
        var columns = jQuery(layoutSelectors.columns, container);
        var modules = jQuery(layoutSelectors.modules, container);
        
        var layout = fluid.moduleLayout.buildLayout(container, columns, modules);
        
        return fluid.initLayoutCustomizer(layout, null, null, options);
    };    
}) (fluid);
/*
Copyright 2007 - 2008 University of Toronto

Licensed under the Educational Community License (ECL), Version 2.0 or the New
BSD license. You may not use this file except in compliance with one these
Licenses.

You may obtain a copy of the ECL 2.0 License and BSD License at
https://source.fluidproject.org/svn/LICENSE.txt
*/

var fluid = fluid || {};

(function (jQuery, document) {
    var deriveLightboxCellBase = function (namebase, index) {
        return namebase + "lightbox-cell:" + index + ":";
    };
            
    var addThumbnailActivateHandler = function (lightboxContainer) {
        var enterKeyHandler = function (evt) {
            if (evt.which === fluid.keys.ENTER) {
                var thumbnailAnchors = jQuery ("a", evt.target);
                document.location = thumbnailAnchors.attr ('href');
            }
        };
        
        jQuery (lightboxContainer).keypress (enterKeyHandler);
    };
    
    var createItemFinder = function (parentNode, containerId) {
        // This orderable finder knows that the lightbox thumbnails are 'div' elements
        var lightboxCellNamePattern = "^" + deriveLightboxCellBase (containerId, "[0-9]+") +"$";
        
        return function () {
            return fluid.utils.seekNodesById (parentNode, "div", lightboxCellNamePattern);
        };
    };
    
    // Public Lightbox API
    fluid.lightbox = {
        /**
         * Returns the default Lightbox order change callback. This callback is used by the Lightbox
         * to send any changes in image order back to the server. It is implemented by nesting
         * a form and set of hidden fields within the Lightbox container which contain the order value
         * for each image displayed in the Lightbox. The default callback submits the form's default 
         * action via AJAX.
         * 
         * @param {Element} lightboxContainer The DOM element containing the form that is POSTed back to the server upon order change 
         */
        defaultOrderChangedCallback: function (lightboxContainer) {
            var reorderform = fluid.utils.findForm (lightboxContainer);
            
            return function () {
                var inputs = fluid.utils.seekNodesById(
                    reorderform, 
                    "input", 
                    "^" + deriveLightboxCellBase (lightboxContainer.id, "[^:]*") + "reorder-index$");
                
                for (var i = 0; i < inputs.length; i = i+1) {
                    inputs[i].value = i;
                }
            
                if (reorderform && reorderform.action) {
                    jQuery.post(reorderform.action, 
                    jQuery(reorderform).serialize(),
                    function (type, data, evt) { /* No-op response */ });
                }
            };
        },
    	
        /**
         * Creates a new Lightbox instance from the specified parameters, providing full control over how
         * the Lightbox is configured.
         * 
         * @param {Element} container The DOM element that represents the Lightbox
         * @param {Function} itemFinderFn A function that returns a list of orderable images
         * @param {Function} orderChangedFn A function that is called when the image order is changed by the user
         * @param {String} instructionMessageId The id of the DOM element containing instructional text for Lightbox users
         * @param {Object} options (optional) extra options for the Reorderer
         */
        createLightbox: function (container, itemFinderFn, options) {
            options = options || {};
            // Remove the anchors from the taborder.
            jQuery ("a", container).tabindex (-1);
            addThumbnailActivateHandler (container);
            
            var orderChangedFn = options.orderChangedCallback || fluid.lightbox.defaultOrderChangedCallback (fluid.unwrap(container));

            var layoutHandler = new fluid.GridLayoutHandler (itemFinderFn, {
                orderChangedCallback: orderChangedFn
            });

            var reordererOptions = {
                role : fluid.roles.GRID
            };            
            fluid.mixin (reordererOptions, options);
            
            return new fluid.Reorderer (container, itemFinderFn, layoutHandler, reordererOptions);
        },
        
        /**
         * Creates a new Lightbox by binding to element ids in the DOM.
         * This provides a convenient way of constructing a Lightbox with the default configuration.
         * 
         * @param {String} containerId The id of the DOM element that represents the Lightbox
         * @param {String} instructionMessageId The id of the DOM element containing instructional text for Lightbox users
         */
        createLightboxFromId: function (containerId, options) {
            var parentNode = document.getElementById (containerId);
            var itemFinder = createItemFinder(parentNode, containerId);
            
            return fluid.lightbox.createLightbox (parentNode, itemFinder, options);
        }
    };
}) (jQuery, document);
/* Fluid Multi-File Uploader Component
 * 
 * Built by The Fluid Project (http://www.fluidproject.org)
 * 
 * LEGAL
 * 
 * Copyright 2008 University of California, Berkeley
 * Copyright 2008 University of Toronto
 * 
 * Licensed under the Educational Community License (ECL), Version 2.0 or the New
 * BSD license. You may not use this file except in compliance with one these
 * Licenses.
 * 
 * You may obtain a copy of the ECL 2.0 License and BSD License at
 * https://source.fluidproject.org/svn/LICENSE.txt
 * 
 * DOCUMENTATION
 * Technical documentation is available at: http://wiki.fluidproject.org/x/d4ck
 * 
 */

/* TODO:
 * - handle duplicate file error
 * - make fields configurable
 *	   - strings (for i18n)
 * - refactor 'options' into more than one object as needed
 * - clean up debug code
 * - remove commented-out code
 * - use swfObj status to check states, etc. > drop our status obj
 */

/* ABOUT RUNNING IN LOCAL TEST MODE
 * To run locally using a fake upload, set uploadDefaults.uploadUrl to ''
 */

var fluid = fluid || {};

(function ($,fluid) {
	  
	/* these are the internal UI elements of the Uploader as defined in the 
	 * default HTML for the Fluid Uploader
	 */
	var defaultSelectors = {
		upload: ".fluid-uploader-upload",
		resume: ".fluid-uploader-resume",
		pause: ".fluid-uploader-pause",
		done: ".fluid-uploader-done",
		cancel: ".fluid-uploader-cancel",
		browse: ".fluid-uploader-browse",
		fluidUploader: ".fluid-uploader-queue-wrapper",
		fileQueue: ".fluid-uploader-queue",
		scrollingElement: ".fluid-scroller",
		emptyRow : ".fluid-uploader-row-placeholder",
		txtTotalFiles: ".fluid-uploader-totalFiles",
		txtTotalBytes: ".fluid-uploader-totalBytes",
		txtTotalFilesUploaded : ".fluid-uploader-num-uploaded",
		txtTotalBytesUploaded : ".fluid-uploader-bytes-uploaded",
		osModifierKey: ".fluid-uploader-modifierKey",
		txtFileStatus: ".removeFile",
		uploaderFooter : '.fluid-scroller-table-foot',
		qRowTemplate: '#queue-row-tmplt',
		qRowFileName: '.fileName',
		qRowFileSize: '.fileSize',
		qRowRemove: '.fileRemove',
		fileProgressor: '.file-progress',
		fileProgressText: ".file-progress-text",
		totalProgressor: '.total-progress',
		totalProgressText: ".fluid-scroller-table-foot .footer-total",
		debug: false
    };
	
    // Default configuration options.
	var uploadDefaults = {
		uploadUrl : "",
		flashUrl : "",
		fileSizeLimit : "20480",
		fileTypes : "*.*", 
		fileTypesText : "image files",
		fileUploadLimit : 0,
		fileQueueLimit : 0,
		elmUploaderControl: "",
		whenDone: "", // forces a refresh
		whenCancel: "", // forces a refresh
		whenFileUploaded: function(fileName, serverResponse) {},
		postParams: {},
		httpUploadElm: "",
		continueAfterUpload: true,
		continueDelay: 2000, //in milles
		queueListMaxHeight : 190,
        fragmentSelectors: defaultSelectors,
		// when to show the File browser
		// if false then the browser shows when the Browse button is clicked
		// if true
			// if using dialog then browser will show immediately
			// else browser will show as soon as dialog shows
		browseOnInit: false, 
		// dialog settings
		dialogDisplay: false,
		addFilesBtn: ".fluid-add-files-btn", // used in conjunction with dialog display to activate the Uploader
		debug: false
	};
	
	var dialog_settings = {
		title: "Upload Files", 
		width: 482,
		height: '', // left empty so that the dialog will auto-resize
		draggable: true, 
		modal: true, 
		resizable: false,
		autoOpen: false
	};
	
	var strings = {
		macControlKey: "Command",
		browseText: "Browse files",
		addMoreText: "Add more",
		fileUploaded: "File Uploaded",
		 	// tokens replaced by fluid.util.stringTemplate
		pausedLabel: "Paused at: %curFileN of %totalFilesN files (%currBytes of %totalBytes)",
		totalLabel: "Uploading: %curFileN of %totalFilesN files (%currBytes of %totalBytes)", 
		completedLabel: "Uploaded: %curFileN files (%totalCurrBytes)"
	};
		
	/* DOM Manipulation */
	
	/** 
	* adds a new file to the file queue in DOM
	* note: there are cases where a file will be added to the file queue but will not be in the actual queue 
	*/
	var addFileToQueue = function(uploaderContainer, file, fragmentSelectors, swfObj, status, maxHeight) {
		// make a new row
		var newQueueRow = $(fragmentSelectors.qRowTemplate).clone();
		// update the file name
		$(newQueueRow).children(fragmentSelectors.qRowFileName).text(file.name);
		// update the file size
		$(newQueueRow).children(fragmentSelectors.qRowFileSize).text(fluid.utils.filesizeStr(file.size));
		// update the file id and add the hover action
		newQueueRow.attr('id',file.id).css('display','none').addClass("ready row").hover(function(){
            if ($(this).hasClass('ready') && !$(this).hasClass('uploading')) {
                $(this).addClass('hover');
            }
        }, function(){
            if ($(this).hasClass('ready') && !$(this).hasClass('uploading')) {
                $(this).removeClass('hover');
            }
        });
        // insert the new row into the file queue
		$(fragmentSelectors.fileQueue, uploaderContainer).append(newQueueRow);
		
        // add remove action to the button
        $('#' + file.id, uploaderContainer).children(fragmentSelectors.qRowRemove).click(function(){
            removeRow(uploaderContainer, fragmentSelectors, $(this).parents('tr'), swfObj, status, maxHeight);  
        });
        
        // display the new row
        $('#' + file.id, uploaderContainer).fadeIn('slow');
	};


	/** 
	* removes the defined row from the file queue 
	* @param {jQuery} 	uploaderContainer
	* @param {Object} 	fragmentSelectors	collection of Uploader DOM selectors 
	* @param {jQuery} 	row					a jQuery object for the row
	* @param {SWFUpload} swfObj				the SWF upload object
	* @param {Object} 	status				the status object to be updated
	* @return {jQuery}	returns row			the same jQuery object
	*/
	var removeRow = function(uploaderContainer, fragmentSelectors, row, swfObj, status, maxHeight) {
		row.fadeOut('fast', function (){
			var fileId = row.attr('id');
			var file = swfObj.getFile(fileId);
			queuedBytes (status, -file.size);
			swfObj.cancelUpload(fileId);
			row.remove();
			updateQueueHeight($(fragmentSelectors.scrollingElement, uploaderContainer), maxHeight);
			updateNumFiles(uploaderContainer, fragmentSelectors.txtTotalFiles, fragmentSelectors.fileQueue, fragmentSelectors.emptyRow);
			updateTotalBytes(uploaderContainer, fragmentSelectors.txtTotalBytes, status);
			updateStateByState(uploaderContainer,fragmentSelectors.fileQueue);
			updateBrowseBtnText(uploaderContainer, fragmentSelectors.fileQueue, fragmentSelectors.browse, status);
		});
		return row;
	};
	
	var updateQueueHeight = function(scrollingElm, maxHeight){
		var overMaxHeight = (scrollingElm.children().eq(0).height() > maxHeight);
		var setHeight = (overMaxHeight) ? maxHeight : '';
		scrollingElm.height( setHeight ) ;
		return overMaxHeight;
	};
	
	var scrollBottom = function(scrollingElm){
		// cast potentially a jQuery obj to a regular obj
		scrollingElm = $(scrollingElm)[0];
		// set the scrollTop to the scrollHeight
		scrollingElm.scrollTop = scrollingElm.scrollHeight;
	};
	
	var scrollTo = function(scrollingElm,row){
		if ($(row).prev().length) {
			var nextRow = $(row).next();
			row = (nextRow.length === 0) ? row : nextRow ;
		}
		
		var rowPosTop = $(row)[0].offsetTop;
		var rowHeight = $(row).height();
		var containerScrollTop = $(scrollingElm)[0].scrollTop;
		var containerHeight = $(scrollingElm).height();
		
		// if the top of the row is ABOVE the view port move the row into position
		if (rowPosTop < containerScrollTop) {
			$(scrollingElm)[0].scrollTop = rowPosTop;
		}
		
		// if the bottom of the row is BELOW the viewport then scroll it into position
		if ((rowPosTop + rowHeight) > (containerScrollTop + containerHeight)) {
			$(scrollingElm)[0].scrollTop = (rowPosTop - containerHeight + rowHeight);
		}
		//$(scrollingElm)[0].scrollTop = $(row)[0].offsetTop;
	};
	
	/**
	 * Updates the total number of rows in the queue in the UI
	 */
	var updateNumFiles = function(uploaderContainer, totalFilesSelector, fileQueueSelector) {
		$(totalFilesSelector, uploaderContainer).text(numberOfRows(uploaderContainer, fileQueueSelector));
	};
	
	/**
	 * Updates the total number of bytes in the UI
	 */
	var updateTotalBytes = function(uploaderContainer, totalBytesSelector, status) {
		$(totalBytesSelector, uploaderContainer).text(fluid.utils.filesizeStr(queuedBytes(status)));
	};
	 
    /*
     * Figures out the state of the uploader based on 
     * the number of files in the queue, and the number of files uploaded, 
     * or have errored, or are still to be uploaded
     * @param {String} uploaderContainer    the uploader container
     * @param {String} fileQueueSelector    the file queue used to test numbers.
     */
	var updateStateByState = function(uploaderContainer, fileQueueSelector) {
		var totalRows = numberOfRows(uploaderContainer, fileQueueSelector);
		var rowsUploaded = numFilesUploaded(uploaderContainer, fileQueueSelector);
		var rowsReady = numFilesToUpload(uploaderContainer, fileQueueSelector);
		
		fluid.utils.debug(
			"totalRows = " + totalRows + 
			"\nrowsUploaded = " + rowsUploaded + 
			"\nrowsReady = " + rowsReady
		);
		if (rowsUploaded > 0) { // we've already done some uploads
			if (rowsReady === 0) {
				updateState(uploaderContainer, 'empty');
			} else {
				updateState(uploaderContainer, 'reloaded');
			}
		} else if (totalRows === 0) {
			updateState(uploaderContainer, 'start');
		} else {
			updateState(uploaderContainer, 'loaded');
		}
	};
	
    /*
     * Sets the state (using a css class) for the top level element
     * @param {String} uploaderContainer    the uploader container
     * @param {String} stateClass    the file queue used to test numbers.
     */
	var updateState = function(uploaderContainer, stateClass) {
		$(uploaderContainer).children("div:first").attr('className',stateClass);
	};
	
	var updateBrowseBtnText = function(uploaderContainer, fileQueueSelector, browseButtonSelector, status) {
		if (numberOfRows(uploaderContainer, fileQueueSelector) > 0) {
			$(browseButtonSelector, uploaderContainer).text(strings.addMoreText);
		} else {
			$(browseButtonSelector, uploaderContainer).text(strings.browseText);
		}
	};
	
	var markRowComplete = function(row, fileStatusSelector, removeBtnSelector) {
		// update the status of the row to "uploaded"
		rowChangeState(row, removeBtnSelector, fileStatusSelector, 'uploaded', strings.fileUploaded);
	};
	
	var markRowError = function(row, fileStatusSelector, removeBtnSelector, scrollingElm, maxHeight, humanError) {
		// update the status of the row to "error"
		rowChangeState(row, removeBtnSelector, fileStatusSelector, 'error', 'File Upload Error');
		
		updateQueueHeight(scrollingElm, maxHeight);
		
		if (humanError !== '') {
            displayHumanReableError(row, humanError);
        }	
	};
	
	/* rows can only go from ready to error or uploaded */
	var rowChangeState = function(row, removeBtnSelector, fileStatusSelector, stateClass, stateMessage) {
		
		// remove the ready status and add the new status
		row.removeClass('ready').addClass(stateClass);
		
		// remove click event on Remove button
		$(row).find(removeBtnSelector).unbind('click');
		
		// add text status
		$(row).find(fileStatusSelector).attr('title',stateMessage);
	};
	
	var displayHumanReableError = function(row, humanError) {
		var newErrorRow = $('#queue-error-tmplt').clone();
		$(newErrorRow).find('.queue-error').html(humanError);
		$(newErrorRow).removeAttr('id').insertAfter(row);
	};
		
	// UTILITY SCRIPTS
	/**
	 * displays URL/URI or runs provided function
	 * does not validate action, unknown what it would do with other types of input
	 * @param {String, Function} action
	 */
	var variableAction = function(action) {
		if (action !== undefined) {
			if (typeof action === "function") {
				action();
			}
			else {
				location.href = action;
			}
		}
	};
	
	// SWF Upload Callback Handlers

    /*
     * @param {String} uploaderContainer    the uploader container
     * @param {int} maxHeight    maximum height in pixels for the file queue before scrolling
     * @param {Object} status    
     */
	var createFileQueuedHandler = function (uploaderContainer, fragmentSelectors, maxHeight, status) {
        return function(file){
            var swfObj = this;
            try {
				// what have we got?
                fluid.utils.debug(file.name + " file.size = " + file.size); // DEBUG
                
                // add the file to the queue
				addFileToQueue(uploaderContainer, file, fragmentSelectors, swfObj, status, maxHeight);
				
				updateStateByState(uploaderContainer, fragmentSelectors.fileQueue);

				var scrollingElm = $(fragmentSelectors.scrollingElement, uploaderContainer);
                
				// scroll to the bottom to reviel element
				if (updateQueueHeight(scrollingElm, maxHeight)) {
					scrollBottom(scrollingElm);
				}
				
                // add the size of the file to the variable maintaining the total size
                queuedBytes(status, file.size);
                // update the UI
				updateNumFiles(uploaderContainer, fragmentSelectors.txtTotalFiles, fragmentSelectors.fileQueue, fragmentSelectors.emptyRow);
                updateTotalBytes(uploaderContainer, fragmentSelectors.txtTotalBytes, status);
                
            } 
            catch (ex) {
                fluid.utils.debug(ex);
            }
        };
	};
		
	var createSWFReadyHandler = function (browseOnInit, allowMultipleFiles, useDialog) {
		return function(){
			if (browseOnInit && !useDialog) {
				browseForFiles(this,allowMultipleFiles);
			}
		};
	};
	
	function browseForFiles(swfObj,allowMultipleFiles) {
		if (allowMultipleFiles) {
			swfObj.selectFiles();
		}
		else {
			swfObj.selectFile();
		}
	}

	var createFileDialogStartHandler = function(uploaderContainer){
		return function(){
			try {
				$(uploaderContainer).children("div:first").addClass('browsing');
			} 
			catch (ex) {
				fluid.utils.debug(ex);
			}
		};
	};

	var createFileDialogCompleteHandler = function(uploaderContainer, fragmentSelectors, status) {
        return function(numSelected, numQueued){
            try {
                updateBrowseBtnText(uploaderContainer, fragmentSelectors.fileQueue, fragmentSelectors.browse, status);
				$(uploaderContainer).children("div:first").removeClass('browsing');
                debugStatus(status);
            } 
            catch (ex) {
                fluid.utils.debug(ex);
            }
        };
	};

	function fileQueueError(file, error_code, message) {
		// surface these errors in the queue
		try {
			var error_name = "";
			switch (error_code) {
			case SWFUpload.QUEUE_ERROR.QUEUE_LIMIT_EXCEEDED:
				error_name = "QUEUE LIMIT EXCEEDED";
				break;
			case SWFUpload.QUEUE_ERROR.FILE_EXCEEDS_SIZE_LIMIT:
				error_name = "FILE EXCEEDS SIZE LIMIT";
				break;
			case SWFUpload.QUEUE_ERROR.ZERO_BYTE_FILE:
				error_name = "ZERO BYTE FILE";
				break;
			case SWFUpload.QUEUE_ERROR.INVALID_FILETYPE:
				error_name = "INVALID FILE TYPE";
				break;
			default:
				error_name = "UNKNOWN";
				break;
			}
			var error_string = error_name + ":File ID: " + (typeof(file) === "object" && file !== null ? file.id : "na") + ":" + message;
			fluid.utils.debug ('error_string = ' + error_string);
		} catch (ex) {
			fluid.utils.debug (ex);
		}
	}	

    var createUploadStartHandler = function (uploaderContainer, fragmentSelectors, progressBar, status) {
        return function (file) {
            uploadStart (file, uploaderContainer, fragmentSelectors, progressBar, status);
        };
    };
    
	var uploadStart = function(file, uploaderContainer, fragmentSelectors, progressBar, status) {
		fluid.utils.debug("Upload Start Handler");
		updateState(uploaderContainer,'uploading');
		status.currError = ''; // zero out the error so we can check it later
		$("#"+file.id,uploaderContainer).addClass("uploading");
		progressBar.init('#'+file.id);
		scrollTo($(fragmentSelectors.scrollingElement, uploaderContainer),$("#"+file.id, uploaderContainer));
		uploadProgress(progressBar, uploaderContainer, file, 0, file.size, fragmentSelectors, status);
		fluid.utils.debug (
			"Starting Upload: " + (file.index + 1) + ' (' + file.id + ')' + ' [' + file.size + ']' + ' ' + file.name
		);
	};

	
	/* File and Queue Upload Progress */

    var createUploadProgressHandler = function (progressBar, uploaderContainer, fragmentSelectors, status) {
        return function(file, bytes, totalBytes) {
            uploadProgress (progressBar, uploaderContainer, file, bytes, totalBytes, fragmentSelectors, status);
        };
    };
    
	/* File Upload Error */
	var createUploadErrorHandler = function (uploaderContainer, progressBar, fragmentSelectors, maxHeight, status, options) {
        return function(file, error_code, message){
            uploadError(file, error_code, message,uploaderContainer, progressBar, fragmentSelectors, maxHeight, status, options);
        };
	};
	
	var uploadError = function (file, error_code, message, uploaderContainer, progressBar, fragmentSelectors, maxHeight, status, options) {
		fluid.utils.debug("Upload Error Handler");
		status.currError = '';
		status.continueOnError = false;
		var humanErrorMsg = '';
		var markError = true;
        try {
            switch (error_code) {
                case SWFUpload.UPLOAD_ERROR.HTTP_ERROR:
                    status.currError = "Error Code: HTTP Error, File name: " + file.name + ", Message: " + message;
					humanErrorMsg = 'An error occurred on the server during upload. It could be that the file already exists on the server.' + 
						formatErrorCode(message);
					status.continueOnError = true;
                    break;
                case SWFUpload.UPLOAD_ERROR.UPLOAD_FAILED:
                    status.currError = "Error Code: Upload Failed, File name: " + file.name + ", File size: " + file.size + ", Message: " + message;
                    break;
                case SWFUpload.UPLOAD_ERROR.IO_ERROR:
                    status.currError = "Error Code: IO Error, File name: " + file.name + ", Message: " + message;
                    humanErrorMsg = 'An error occurred attempting to read the file from disk. The file was not uploaded.' + 
						formatErrorCode(message);
					status.continueOnError = true;
                    break;
                case SWFUpload.UPLOAD_ERROR.SECURITY_ERROR:
                    status.currError = "Error Code: Security Error, File name: " + file.name + ", Message: " + message;
                    break;
                case SWFUpload.UPLOAD_ERROR.UPLOAD_LIMIT_EXCEEDED:
                    status.currError = "Error Code: Upload Limit Exceeded, File name: " + file.name + ", File size: " + file.size + ", Message: " + message;
                    break;
                case SWFUpload.UPLOAD_ERROR.FILE_VALIDATION_FAILED:
                    status.currError = "Error Code: File Validation Failed, File name: " + file.name + ", File size: " + file.size + ", Message: " + message;
                    break;
                case SWFUpload.UPLOAD_ERROR.FILE_CANCELLED:
                    status.currError = "File cancelled by user";
					status.continueOnError = true;
                    markError = false;
                    break;
                case SWFUpload.UPLOAD_ERROR.UPLOAD_STOPPED:
                    status.currError = "Upload Stopped by user input";
					var pauseStrings = {
						curFileN: numFilesUploaded(uploaderContainer,fragmentSelectors.fileQueue), 
						totalFilesN: numberOfRows(uploaderContainer,fragmentSelectors.fileQueue), 
						currBytes: fluid.utils.filesizeStr(status.currBytes), 
						totalBytes: fluid.utils.filesizeStr(status.totalBytes)
					};
					var pausedString = fluid.utils.stringTemplate(strings.pausedLabel,pauseStrings);
					$(fragmentSelectors.totalProgressText, uploaderContainer).html(pausedString);

					updateState(uploaderContainer,'paused');
					
					markError = false;
                    break;
                default:
                    //				progress.SetStatus("Unhandled Error: " + error_code);
                    status.currError = "Error Code: " + error_code + ", File name: " + file.name + ", File size: " + file.size + ", Message: " + message;
                    break;
            }
							
			if (markError) {
                markRowError($('tr#' + file.id, uploaderContainer), fragmentSelectors.txtFileStatus, fragmentSelectors.qRowRemove, $(fragmentSelectors.scrollingElement, uploaderContainer), maxHeight, humanErrorMsg);
            }
            
			fluid.utils.debug(status.currError + '\n' + humanErrorMsg);
			
			// override continueAfterUpload
			options.continueAfterUpload = false;
        } 
        catch (ex) {
            fluid.utils.debug(ex);
        }		
	};
	
	var formatErrorCode = function(str) {
		return " (Error code: " + str + ")";
	};
	
	/* File Upload Success */
	
	var createUploadSuccessHandler =  function(uploaderContainer, progressBar, fragmentSelectors, whenFileUploaded, status){
		return function(file, server_data) {
			uploadSuccess(uploaderContainer, file, progressBar, fragmentSelectors, status, whenFileUploaded, server_data);
		};
	};	
	
	var uploadSuccess = function (uploaderContainer, file, progressBar, fragmentSelectors, status, whenFileUploaded, server_data){
		fluid.utils.debug("Upload Success Handler");
		
  		uploadProgress(progressBar, uploaderContainer, file, file.size, file.size, fragmentSelectors, status);
       	markRowComplete($('tr#' + file.id, uploaderContainer), fragmentSelectors.txtFileStatus, fragmentSelectors.qRowRemove);
		
 		try {
			whenFileUploaded(file.name, server_data);
		} 
		catch (ex) {
			 fluid.utils.debug(ex);
		}
	};
	
	
	/* File Upload Complete */
	
	var createUploadCompleteHandler = function (uploaderContainer, progressBar, fragmentSelectors, status, options, dialogObj) {
        return function(file){
			uploadComplete(this, file, uploaderContainer, progressBar, fragmentSelectors, status, options, dialogObj);
        };
	};
	
	var uploadComplete = function (swfObj, file, uploaderContainer, progressBar, fragmentSelectors, status, options, dialogObj) {
		fluid.utils.debug("Upload Complete Handler");
		
		$("#"+file.id,uploaderContainer).removeClass("uploading");
		
		var totalCount = numberOfRows(uploaderContainer, fragmentSelectors.fileQueue);
		
		var currStats = swfObj.getStats();
		
		fluid.utils.debug(
		"currStats.files_queued = " + currStats.files_queued + 
		"\ncurrStats.successful_uploads = " + currStats.successful_uploads + 
		"\ncurrStats.upload_errors = " + currStats.upload_errors
		);
				
    	if (currStats.files_queued === 0) {
            // we've completed all the files in this upload
            return fileQueueComplete(uploaderContainer, swfObj, options, progressBar, fragmentSelectors, dialogObj, status);
        }
       	else if (!status.currError || status.continueOnError) {
			// there was no error and there are still files to go
			uploadedBytes(status,file.size); // update the number of bytes that have actually be uploaded so far
            return swfObj.startUpload();  
        }
        else { 
			// there has been an error that we should stop on
        	// note: do not update the bytes because we didn't complete that last file
			return hideProgress(progressBar, true, $(fragmentSelectors.done, uploaderContainer));
        }
	};
	
	/* File Queue Complete */
	
	var fileQueueComplete = function(uploaderContainer, swfObj, options, progressBar, fragmentSelectors, dialogObj, status) {
		fluid.utils.debug("File Queue Complete Handler");
		
		updateState(uploaderContainer, 'done');
		var stats = swfObj.getStats();
		var newStrings = {
			curFileN: stats.successful_uploads,
			totalCurrBytes: fluid.utils.filesizeStr(status.totalBytes)
		};
		 
		$(fragmentSelectors.totalProgressText, uploaderContainer).html(fluid.utils.stringTemplate(strings.completedLabel,newStrings));
		hideProgress(progressBar, true, $(fragmentSelectors.done, uploaderContainer));
		options.continueDelay = (!options.continueDelay) ? 0 : options.continueDelay;
		if (options.continueAfterUpload) {
			setTimeout(function(){
				variableAction(options.whenDone);
			},options.continueDelay);
		}
	};
	
    /*
     * Return the queue size. If a number is passed in, increment the size first.
     */
	var queuedBytes = function (status, delta) {
		if (typeof delta === 'number') {
			status.totalBytes += delta;
		}
		return status.totalBytes;
	};
	
	var uploadedBytes = function (status, delta) {
		if (typeof delta === 'number') {
			status.currBytes += delta;
		}
		return status.currBytes;
	};
	
	function readyBytes(status) {
		return (status.totalBytes - status.currBytes);
	}

	
	function numberOfRows(uploaderContainer, fileQueueSelector) {
		return $(fileQueueSelector, uploaderContainer).find('.row').length ;
	}

	function numFilesToUpload(uploaderContainer, fileQueueSelector) {
		return $(fileQueueSelector, uploaderContainer).find('.ready').length ;
	}
	
	function numFilesUploaded(uploaderContainer, fileQueueSelector) {
		return $(fileQueueSelector, uploaderContainer).find('.uploaded').length;
	}
	
	/* PROGRESS
	 * 
	 */
	
	var uploadProgress = function(progressBar, uploaderContainer, file, fileBytes, totalFileBytes, fragmentSelectors, status) {
		fluid.utils.debug("Upload Progress Handler");
		
		fluid.utils.debug ('Upload Status : \n' + file.name + ' : ' + fileBytes + ' of ' + totalFileBytes + " bytes : \ntotal : " + (status.currBytes + fileBytes)  + ' of ' + queuedBytes(status) + " bytes");
		
		// update file progress
		var filePercent = fluid.utils.derivePercent(fileBytes,totalFileBytes);
		progressBar.updateProgress("file", filePercent, filePercent+"%");
		
		// update total 
		var totalQueueBytes = queuedBytes(status);
		var currQueueBytes = status.currBytes + fileBytes;
		var totalPercent = fluid.utils.derivePercent(currQueueBytes, totalQueueBytes);
		var fileIndex = file.index + 1;
		var numFilesInQueue = numberOfRows(uploaderContainer, fragmentSelectors.fileQueue);
		
		var totalHTML = totalStr(fileIndex,numFilesInQueue,currQueueBytes,totalQueueBytes);
		
		progressBar.updateProgress("total", totalPercent, totalHTML);		
	};
	
	function totalStr(fileIndex,numRows,bytes,totalBytes) {		
		var newStrings = {
			curFileN: fileIndex, 
			totalFilesN: numRows, 
			currBytes: fluid.utils.filesizeStr(bytes), 
			totalBytes: fluid.utils.filesizeStr(totalBytes)
		};
		
		return fluid.utils.stringTemplate(strings.totalLabel, newStrings);
	}
	
	var hideProgress = function(progressBar, dontPause, focusAfterHide) {
	 	progressBar.hide(dontPause);
        focusAfterHide.focus();
	};
	
	/* DIALOG
	 * 
	 */
	
 	var initDialog = function(uploaderContainer, addBtnSelector, browseOnInit, fileBrowseSelector) {
		dialogObj = uploaderContainer.dialog(dialog_settings).css('display','block');
		$(addBtnSelector).click(function(){
			$(dialogObj).dialog("open");
			if (browseOnInit) {
				$(fileBrowseSelector, uploaderContainer).click();
			}
		});

		return dialogObj;
	};
		
	var closeDialog = function(dialogObj) {
		$(dialogObj).dialog("close");
	};

	/* DEV CODE
	 * to be removed after beta or factored into unit tests
	 */
	
	function debugStatus(status) {
		fluid.utils.debug (
			"\n status.totalBytes = " + queuedBytes (status) + 
			"\n status.currCount = " + status.currCount + 
			"\n status.currBytes = " + status.currBytes + 
			"\n status.currError = " + status.currError +
			"\n status.continueOnError = " + status.continueOnError
			
		);
	}
	
	/* DEMO CODE
	 * this is code that fakes an upload with out a server
	 */

 
    // need to pass in current uploader
    
    var demoUpload = function (uploaderContainer, swfObj, progressBar, options, fragmentSelectors, status, dialogObj) {
		fluid.utils.debug("demoUpload Handler");
        var demoState = {};
		
		// used to break the demo upload into byte-sized chunks
		demoState.byteChunk = 200000; 
		
		// set up data
		demoState.row = $(fragmentSelectors.fileQueue + ' tbody tr:not(".fluid-uploader-placeholder"):not(".uploaded):not(".error)', uploaderContainer).eq(0);
		
		demoState.fileId = jQuery(demoState.row).attr('id');
		demoState.file = swfObj.getFile(demoState.fileId);
        
        fluid.utils.debug("num of ready files = " + numFilesToUpload(uploaderContainer, fragmentSelectors.fileQueue)); // check the current state 
        
		if (status.stop === true) { // we're pausing
			demoPause(swfObj, demoState.file, uploaderContainer, progressBar, fragmentSelectors, status, options, dialogObj, 0);
		} else if (numFilesToUpload(uploaderContainer, fragmentSelectors.fileQueue)) { // there are still files to upload
			status.stop = false;
			demoState.bytes = 0;
			demoState.totalBytes = demoState.file.size;
			demoState.numChunks = Math.ceil(demoState.totalBytes / demoState.byteChunk);
			fluid.utils.debug ('DEMO :: ' + demoState.fileId + ' :: totalBytes = ' 
                + demoState.totalBytes + ' numChunks = ' + demoState.numChunks);
			
			// start the demo upload
			uploadStart(demoState.file, uploaderContainer, fragmentSelectors, progressBar, status);
			
			// perform demo progress
			demoProgress(demoState, swfObj, uploaderContainer, progressBar, fragmentSelectors, status, options, dialogObj);
		} else { // no more files to upload close the display
			fileQueueComplete(uploaderContainer, swfObj, options, progressBar, fragmentSelectors, dialogObj, status);
		}

        function demoProgress(demoState, swfObj, uploaderContainer, progressBar, fragmentSelectors, status, options, dialogObj) {
			var timer;
			var delay = Math.floor(Math.random() * 1000 + 100);
   			if (status.stop === true) { // user paused the upload
   				// throw the pause error
 				demoPause(swfObj, demoState.file, uploaderContainer, progressBar, fragmentSelectors, status, options, dialogObj, delay);
    		} else {
				status.stop = false;
    			var tmpBytes = (demoState.bytes + demoState.byteChunk);
				
    			if (tmpBytes < demoState.totalBytes) { // we're still in the progress loop
    				fluid.utils.debug ('tmpBytes = ' + tmpBytes + ' totalBytes = ' + demoState.totalBytes);
    				uploadProgress(progressBar, uploaderContainer, demoState.file, tmpBytes, demoState.totalBytes, fragmentSelectors, status);
    				demoState.bytes = tmpBytes;
    				timer = setTimeout(function(){
						demoProgress(demoState, swfObj, uploaderContainer, progressBar, fragmentSelectors, status, options, dialogObj);
					}, delay);			
    			}
    			else { // progress is complete
    				// one last progress update just for nice
					uploadSuccess(uploaderContainer, demoState.file, progressBar, fragmentSelectors, status);
    				// change Stats here
    				timer = setTimeout(function(){
						uploadComplete(swfObj, demoState.file, uploaderContainer, progressBar, fragmentSelectors, status, options, dialogObj);
    				}, delay);
					// remove the file from the queue
					swfObj.cancelUpload(demoState.fileId);
				}
    		}  
			status.stop = false;
    	}
        
		function demoPause (swfObj, file, uploaderContainer, progressBar, fragmentSelectors, status, options, dialogObj, delay) {
			uploadError(file, -290, "", uploaderContainer, progressBar, fragmentSelectors, options.queueListMaxHeight, status, options);
    		uploadComplete(swfObj, file, uploaderContainer, progressBar, fragmentSelectors, status, options, dialogObj);
			status.stop = false;
		}
        
     };    

    function initSWFUpload(uploaderContainer, uploadURL, flashURL, progressBar, status, fragmentSelectors, options, allowMultipleFiles, dialogObj) {
		// Initialize the uploader SWF component
		// Check to see if SWFUpload is available
		if (typeof(SWFUpload) === "undefined") {
			return null;
		}
        
		var swf_settings = {
			// File Upload Settings
			upload_url: uploadURL,
			flash_url: flashURL,
            post_params: options.postParams,
			
			file_size_limit: options.fileSizeLimit,
			file_types: options.fileTypes,
			file_types_description: options.fileTypesDescription,
			file_upload_limit: options.fileUploadLimit,
			file_queue_limit: options.fileQueueLimit,
						
			// Event Handler Settings
			swfupload_loaded_handler : createSWFReadyHandler(options.browseOnInit, allowMultipleFiles, options.dialogDisplay),
			file_dialog_start_handler: createFileDialogStartHandler (uploaderContainer),
			file_queued_handler: createFileQueuedHandler (uploaderContainer, fragmentSelectors, options.queueListMaxHeight, status),
			file_queue_error_handler: fileQueueError,
			file_dialog_complete_handler: createFileDialogCompleteHandler (uploaderContainer, fragmentSelectors, status),
			upload_start_handler: createUploadStartHandler (uploaderContainer, fragmentSelectors, progressBar, status),
			upload_progress_handler: createUploadProgressHandler (progressBar, uploaderContainer, fragmentSelectors, status),
			upload_error_handler: createUploadErrorHandler (uploaderContainer, progressBar, fragmentSelectors, options.queueListMaxHeight, status, options),
			upload_success_handler: createUploadSuccessHandler (uploaderContainer, progressBar, fragmentSelectors, options.whenFileUploaded, status),
			upload_complete_handler: createUploadCompleteHandler (uploaderContainer, progressBar, fragmentSelectors, status, options, dialogObj),
			// debug_handler : debug_function, // a new event handler in swfUpload that we don't really know what to do with yet
			// Debug setting
			debug: options.debug
		}; 
		
        return new SWFUpload(swf_settings);
    }
    
    var whichOS = function () {
		if (navigator.appVersion.indexOf("Win") !== -1) {
            return "Windows";
        }
		if (navigator.appVersion.indexOf("Mac") !== -1) {
            return "MacOS";
        }
		if (navigator.appVersion.indexOf("X11") !== -1) {
            return "UNIX";
        }
		if (navigator.appVersion.indexOf("Linux") !== -1) {
            return "Linux";
        }
        else {
            return "unknown";
        }
	};
    
    var setKeyboardModifierString = function (uploaderContainer, modifierKeySelector) {
        // set the text difference for the instructions based on Mac or Windows
		if (whichOS() === 'MacOS') {
			$(modifierKeySelector, uploaderContainer).text(strings.macControlKey);
		}
    };
    
    var bindEvents = function (uploader, uploaderContainer, swfObj, allowMultipleFiles, whenDone, whenCancel) {

		// browse button
        var activateBrowse = function () {
            return (allowMultipleFiles) ? swfObj.selectFiles() : swfObj.selectFile();
		};
        
		$(uploader.fragmentSelectors.browse, uploaderContainer).click(activateBrowse).activatable(activateBrowse);
        
		// upload button
		$(uploader.fragmentSelectors.upload, uploaderContainer).click(function(){
			if ($(uploader.fragmentSelectors.upload, uploaderContainer).css('cursor') === 'pointer') {
				uploader.actions.beginUpload();
			}
		});
		
		// resume button
		$(uploader.fragmentSelectors.resume, uploaderContainer).click(function(){
			if ($(uploader.fragmentSelectors.resume, uploaderContainer).css('cursor') === 'pointer') {
				uploader.actions.beginUpload();
			}
		});
		
		// pause button
		$(uploader.fragmentSelectors.pause, uploaderContainer).click(function(){
			swfObj.stopUpload();
		});
		
		// done button
		$(uploader.fragmentSelectors.done, uploaderContainer).click(function(){
			variableAction(whenDone);
		});
		
		// cancel button
		$(uploader.fragmentSelectors.cancel, uploaderContainer).click(function(){
			variableAction(whenCancel);
		});
    };
    
    var enableDemoMode = function (uploaderContainer, swfObj, progressBar, options, fragmentSelectors, status, dialogObj) {
		// this is a local override to do a fake upload
		swfObj.startUpload = function(){
			demoUpload(uploaderContainer, swfObj, progressBar, options, fragmentSelectors, status, dialogObj);
		};
		swfObj.stopUpload = function(){
			status.stop = true;
		};
    };
    
	/* Public API */
	fluid.Uploader = function(uploaderContainerId, uploadURL, flashURL, settings){
        
        this.uploaderContainer = fluid.utils.jById(uploaderContainerId);
		
		// Mix user's settings in with our defaults.
        // temporarily public; to be made private after beta
		this.options = $.extend({}, uploadDefaults, settings);
        
        this.fragmentSelectors = this.options.fragmentSelectors;
        
        // Should the status object be more self-aware? Should various functions that operate on
        // it (and do little else) be encapsulated in it?
        this.status = {
    		totalBytes:0,
	    	currBytes:0,
		    currError:'',
			continueOnError: false,
		    stop: false
	    };
		
		var progressOptions = {
			progress: this.uploaderContainer,
			fileProgressor: this.fragmentSelectors.fileProgressor,
			fileText: this.fragmentSelectors.fileProgressText,
			totalProgressor: this.fragmentSelectors.totalProgressor,
			totalText: this.fragmentSelectors.totalProgressText,
			totalProgressContainer: this.fragmentSelectors.uploaderFooter
		};
		
		var progressBar = new fluid.Progress(progressOptions);
    				
		var allowMultipleFiles = (this.options.fileQueueLimit !== 1);

 		// displaying Uploader in a dialog
		if (this.options.dialogDisplay) {
			var dialogObj = initDialog(this.uploaderContainer, this.options.addFilesBtn, this.options.browseOnInit, this.fragmentSelectors.browse);
		}

        var swfObj = initSWFUpload(this.uploaderContainer, uploadURL, flashURL, progressBar, this.status, this.fragmentSelectors, this.options, allowMultipleFiles, dialogObj);
		
        this.actions = new fluid.SWFWrapper(swfObj);
        
        setKeyboardModifierString(this.uploaderContainer, this.fragmentSelectors.osModifierKey);
        
        // Bind all our event handlers.
        bindEvents(this, this.uploaderContainer, swfObj, allowMultipleFiles, this.options.whenDone, this.options.whenCancel);
		
        // If we've been given an empty URL, kick into demo mode.
        if (uploadURL === '') {
            enableDemoMode(this.uploaderContainer, swfObj, progressBar, this.options, this.fragmentSelectors, this.status, dialogObj);
        }
	};
	
    // temporary debuggin' code to be removed after beta
    // USE: call from the console to check the current state of the options and fragmentSelectors objects
    
	fluid.Uploader.prototype._test = function() {
		var str = "";
		for (key in options) {
            if (options.hasOwnProperty(key)) {
                str += key + ' = ' + options[key] + '\n';
            }
		}
		for (key in this.fragmentSelectors) {
           if (this.fragmentSelectors.hasOwnProperty(key)) {
               str += key + ' = ' + this.fragmentSelectors[key] + '\n';
           }
		}
		fluid.utils.debug (str);
	};
	
    fluid.SWFWrapper = function (swfObject) {
        this.swfObj = swfObject;
    };
	
    fluid.SWFWrapper.prototype.beginUpload = function() {
		this.swfObj.startUpload();
	};
    
})(jQuery,fluid);

/* PROGRESS
 *  
 */

(function ($) {
		 
	function animateToWidth(elm,width) {
		elm.animate({ 
			width: width,
			queue: false
		}, 200 );
	}
	
	var hideNow = function(which){
        $(which).fadeOut('slow');
    };      
    
	 /* Constructor */
	fluid.Progress = function (options) {
		this.minWidth = 5;
        this.progressContainer = options.progress;
  		this.fileProgressElm = $(options.fileProgressor, this.progressContainer);
		this.fileTextElm = $(options.fileText, this.progressContainer);
		this.totalProgressElm = $(options.totalProgressor, this.progressContainer);
		this.totalTextElm = $(options.totalText, this.progressContainer);
		this.totalProgressContainer = $(options.totalProgressContainer, this.progressContainer);
		
		this.totalProgressElm.width(this.minWidth);
		
		this.fileProgressElm.hide();
		this.totalProgressElm.hide();
	};
	
	fluid.Progress.prototype.init = function(fileRowSelector){
		
		this.currRowElm = $(fileRowSelector,this.progressContainer);
		
		// hide file progress in case it is showing
		this.fileProgressElm.width(this.minWidth);
		
		// set up the file row
		this.fileProgressElm.css('top',(this.currRowElm.position().top)).height(this.currRowElm.height()).width(this.minWidth);
		// here to make up for an IE6 bug
		if ($.browser.msie && $.browser.version < 7) {
			this.totalProgressElm.height(this.totalProgressElm.siblings().height());
		}	
		
		// show both
		this.totalProgressElm.show();
		this.fileProgressElm.show();
	};
	
	fluid.Progress.prototype.updateProgress = function(which, percent, text, dontAnimate) {
		if (which === 'file') {
			setProgress(percent, text, this.fileProgressElm, this.currRowElm, this.fileTextElm, dontAnimate);
		} else {
			setProgress(percent, text, this.totalProgressElm, this.totalProgressContainer, this.totalTextElm, dontAnimate);
		}
	};

    var setProgress = function(percent, text, progressElm, containerElm, textElm, dontAnimate) {
			
		var containerWidth = containerElm.width();	
		var currWidth = progressElm.width();
		var newWidth = ((percent * containerWidth)/100);
		
		// de-queue any left over animations
		progressElm.queue("fx", []); 
		
		textElm.html(text);
		
		if (percent === 0) {
			progressElm.width(this.minWidth);
		} else if (newWidth < currWidth || dontAnimate) {
			progressElm.width(newWidth);
		} else {
			animateToWidth(progressElm,newWidth);
		}
	};
        
    fluid.Progress.prototype.hide = function(dontPause) {
		var delay = 1600;
		if (dontPause) {
			hideNow(this.fileProgressElm);
			hideNow(this.totalProgressElm);
		} else {
			var timeOut = setTimeout(function(){
                hideNow(this.fileProgressElm);
				hideNow(this.totalProgressElm);
            }, delay);
		}
	};
	
    fluid.Progress.prototype.show = function() {
		this.progressContainer.fadeIn('slow');
	};
	
})(jQuery);




//fluid.Progress.update('.fluid-progress','.file-progress',40,"Label Change");


/* GRAVEYARD and SCRATCH
	
	// eventually used to create fileTypes sets.
	var fileTypes = {
		all: {
			ext: "*.*",
			desc: 'all files'
		},
		images: {
			ext: "*.gif;*.jpeg;*.jpg;*.png;*.tiff",
			desc: "image files"
		},
		text:"*.txt;*.text",
		Word:"*.doc;*.xdoc",
		Excel:"*.xls",
	}

	// for use in a better way of setting state to simplify structure
	states: "start uploading browse loaded reloaded paused empty done",

*/
/*
    json2.js
    2007-11-06

    Public Domain

    No warranty expressed or implied. Use at your own risk.

    See http://www.JSON.org/js.html

    This file creates a global JSON object containing two methods:

        JSON.stringify(value, whitelist)
            value       any JavaScript value, usually an object or array.

            whitelist   an optional that determines how object values are
                        stringified.

            This method produces a JSON text from a JavaScript value.
            There are three possible ways to stringify an object, depending
            on the optional whitelist parameter.

            If an object has a toJSON method, then the toJSON() method will be
            called. The value returned from the toJSON method will be
            stringified.

            Otherwise, if the optional whitelist parameter is an array, then
            the elements of the array will be used to select members of the
            object for stringification.

            Otherwise, if there is no whitelist parameter, then all of the
            members of the object will be stringified.

            Values that do not have JSON representaions, such as undefined or
            functions, will not be serialized. Such values in objects will be
            dropped, in arrays will be replaced with null. JSON.stringify()
            returns undefined. Dates will be stringified as quoted ISO dates.

            Example:

            var text = JSON.stringify(['e', {pluribus: 'unum'}]);
            // text is '["e",{"pluribus":"unum"}]'

        JSON.parse(text, filter)
            This method parses a JSON text to produce an object or
            array. It can throw a SyntaxError exception.

            The optional filter parameter is a function that can filter and
            transform the results. It receives each of the keys and values, and
            its return value is used instead of the original value. If it
            returns what it received, then structure is not modified. If it
            returns undefined then the member is deleted.

            Example:

            // Parse the text. If a key contains the string 'date' then
            // convert the value to a date.

            myData = JSON.parse(text, function (key, value) {
                return key.indexOf('date') >= 0 ? new Date(value) : value;
            });

    This is a reference implementation. You are free to copy, modify, or
    redistribute.

    Use your own copy. It is extremely unwise to load third party
    code into your pages.
*/

/*jslint evil: true */
/*extern JSON */

if (!this.JSON) {

    JSON = function () {

        function f(n) {    // Format integers to have at least two digits.
            return n < 10 ? '0' + n : n;
        }

        Date.prototype.toJSON = function () {

// Eventually, this method will be based on the date.toISOString method.

            return this.getUTCFullYear()   + '-' +
                 f(this.getUTCMonth() + 1) + '-' +
                 f(this.getUTCDate())      + 'T' +
                 f(this.getUTCHours())     + ':' +
                 f(this.getUTCMinutes())   + ':' +
                 f(this.getUTCSeconds())   + 'Z';
        };


        var m = {    // table of character substitutions
            '\b': '\\b',
            '\t': '\\t',
            '\n': '\\n',
            '\f': '\\f',
            '\r': '\\r',
            '"' : '\\"',
            '\\': '\\\\'
        };

        function stringify(value, whitelist) {
            var a,          // The array holding the partial texts.
                i,          // The loop counter.
                k,          // The member key.
                l,          // Length.
                r = /["\\\x00-\x1f\x7f-\x9f]/g,
                v;          // The member value.

            switch (typeof value) {
            case 'string':

// If the string contains no control characters, no quote characters, and no
// backslash characters, then we can safely slap some quotes around it.
// Otherwise we must also replace the offending characters with safe sequences.

                return r.test(value) ?
                    '"' + value.replace(r, function (a) {
                        var c = m[a];
                        if (c) {
                            return c;
                        }
                        c = a.charCodeAt();
                        return '\\u00' + Math.floor(c / 16).toString(16) +
                                                   (c % 16).toString(16);
                    }) + '"' :
                    '"' + value + '"';

            case 'number':

// JSON numbers must be finite. Encode non-finite numbers as null.

                return isFinite(value) ? String(value) : 'null';

            case 'boolean':
            case 'null':
                return String(value);

            case 'object':

// Due to a specification blunder in ECMAScript,
// typeof null is 'object', so watch out for that case.

                if (!value) {
                    return 'null';
                }

// If the object has a toJSON method, call it, and stringify the result.

                if (typeof value.toJSON === 'function') {
                    return stringify(value.toJSON());
                }
                a = [];
                if (typeof value.length === 'number' &&
                        !(value.propertyIsEnumerable('length'))) {

// The object is an array. Stringify every element. Use null as a placeholder
// for non-JSON values.

                    l = value.length;
                    for (i = 0; i < l; i += 1) {
                        a.push(stringify(value[i], whitelist) || 'null');
                    }

// Join all of the elements together and wrap them in brackets.

                    return '[' + a.join(',') + ']';
                }
                if (whitelist) {

// If a whitelist (array of keys) is provided, use it to select the components
// of the object.

                    l = whitelist.length;
                    for (i = 0; i < l; i += 1) {
                        k = whitelist[i];
                        if (typeof k === 'string') {
                            v = stringify(value[k], whitelist);
                            if (v) {
                                a.push(stringify(k) + ':' + v);
                            }
                        }
                    }
                } else {

// Otherwise, iterate through all of the keys in the object.

                    for (k in value) {
                        if (typeof k === 'string') {
                            v = stringify(value[k], whitelist);
                            if (v) {
                                a.push(stringify(k) + ':' + v);
                            }
                        }
                    }
                }

// Join all of the member texts together and wrap them in braces.

                return '{' + a.join(',') + '}';
            }
        }

        return {
            stringify: stringify,
            parse: function (text, filter) {
                var j;

                function walk(k, v) {
                    var i, n;
                    if (v && typeof v === 'object') {
                        for (i in v) {
                            if (Object.prototype.hasOwnProperty.apply(v, [i])) {
                                n = walk(i, v[i]);
                                if (n !== undefined) {
                                    v[i] = n;
                                }
                            }
                        }
                    }
                    return filter(k, v);
                }


// Parsing happens in three stages. In the first stage, we run the text against
// regular expressions that look for non-JSON patterns. We are especially
// concerned with '()' and 'new' because they can cause invocation, and '='
// because it can cause mutation. But just to be safe, we want to reject all
// unexpected forms.

// We split the first stage into 4 regexp operations in order to work around
// crippling inefficiencies in IE's and Safari's regexp engines. First we
// replace all backslash pairs with '@' (a non-JSON character). Second, we
// replace all simple value tokens with ']' characters. Third, we delete all
// open brackets that follow a colon or comma or that begin the text. Finally,
// we look to see that the remaining characters are only whitespace or ']' or
// ',' or ':' or '{' or '}'. If that is so, then the text is safe for eval.

                if (/^[\],:{}\s]*$/.test(text.replace(/\\./g, '@').
replace(/"[^"\\\n\r]*"|true|false|null|-?\d+(?:\.\d*)?(:?[eE][+\-]?\d+)?/g, ']').
replace(/(?:^|:|,)(?:\s*\[)+/g, ''))) {

// In the second stage we use the eval function to compile the text into a
// JavaScript structure. The '{' operator is subject to a syntactic ambiguity
// in JavaScript: it can begin a block or an object literal. We wrap the text
// in parens to eliminate the ambiguity.

                    j = eval('(' + text + ')');

// In the optional third stage, we recursively walk the new structure, passing
// each name/value pair to a filter function for possible transformation.

                    return typeof filter === 'function' ? walk('', j) : j;
                }

// If the text is not JSON parseable, then a SyntaxError is thrown.

                throw new SyntaxError('parseJSON');
            }
        };
    }();
}
