var ingot_canvas = false,
	ingot_get_config_object,
	ingot_record_change,
	ingot_canvas_reset,
	ingot_canvas_init,
	ingot_rebuild_canvas,
	ingot_add_node,
	ingot_get_default_setting,
	ingot_code_editor,
	ingot_handle_save,	
	ingot_rebuild_magics,
	ingot_init_magic_tags,
	ingot_config_object = {},
	ingot_magic_tags = [];

jQuery( function($){


	ingot_handle_save = function( obj ){

		var notice;

		if( obj.data.success ){
			notice = $('.updated_notice_box');
		}else{
			notice = $('.error_notice_box');
		}

		notice.stop().animate({top: -5}, 200, function(){
			setTimeout( function(){
				notice.stop().animate({top: -75}, 200);
			}, 2000);
		});

	}


	ingot_init_magic_tags = function(){
		//init magic tags
		var magicfields = jQuery('.magic-tag-enabled');

		magicfields.each(function(k,v){
			var input = jQuery(v);
			
			if(input.hasClass('magic-tag-init-bound')){
				var currentwrapper = input.parent().find('.magic-tag-init');
				if(!input.is(':visible')){
					currentwrapper.hide();
				}else{
					currentwrapper.show();
				}
				return;			
			}
			var magictag = jQuery('<span class="dashicons dashicons-editor-code magic-tag-init"></span>'),
				wrapper = jQuery('<span style="position:relative;display:inline-block; width:100%;"></span>');

			if(input.is('input')){
				magictag.css('borderBottom', 'none');
			}

			if(input.hasClass('ingot-conditional-value-field')){
				wrapper.width('auto');
			}

			//input.wrap(wrapper);
			magictag.insertAfter(input);
			input.addClass('magic-tag-init-bound');
			if(!input.is(':visible')){
				magictag.hide();
			}else{
				magictag.show();
			}
		});

	}

	// internal function declarationas
	ingot_get_config_object = function(el){
		// new sync first
		$('#ingot-id').trigger('change');
		var clicked 	= $(el),
			config 		= $('#ingot-live-config').val(),
			required 	= $('[required]'),
			clean		= true;

		for( var input = 0; input < required.length; input++ ){
			if( required[input].value.length <= 0 && $( required[input] ).is(':visible') ){
				$( required[input] ).addClass('ingot-input-error');
				clean = false;
			}else{
				$( required[input] ).removeClass('ingot-input-error');
			}
		}
		if( clean ){
			ingot_canvas = config;
		}
		clicked.data( 'config', config );
		return clean;
	}

	ingot_record_change = function(){
		// hook and rebuild the fields list
		jQuery(document).trigger('record_change');
		jQuery('#ingot-id').trigger('change');
		if( ingot_config_object ){
			jQuery('#ingot-field-sync').trigger('refresh');
		}
	}

	ingot_canvas_reset = function(el, ev){
		// handy to add things before builing/rebuilding the canvas
		// return false to stop.

		// remove editors and quicktags
		if ( typeof tinymce !== 'undefined' ) {
			for ( ed in tinymce.editors ) {
				tinymce.editors[ed].remove();
			}
		}
		if ( typeof QTags !== 'undefined' ) {
			QTags.buttonsInitDone = false;
			QTags.instances = {};
		}

		return true
	}
	
	ingot_canvas_init = function(){

		if( !ingot_canvas ){
			// bind changes
			jQuery('#ingot-main-canvas').on('keydown keyup change','input, select, textarea', function(e) {
				ingot_config_object = jQuery('#ingot-main-form').formJSON(); // perhaps load into memory to keep it live.
				jQuery('#ingot-live-config').val( JSON.stringify( ingot_config_object ) ).trigger('change');
			});
			// bind editor
			ingot_init_editor();
			ingot_canvas = jQuery('#ingot-live-config').val();
			ingot_config_object = JSON.parse( ingot_canvas ); // perhaps load into memory to keep it live.
			// wp_editor
			if ( typeof tinymce !== 'undefined' ) {
				tinymce.on('AddEditor', function(e) {

					e.editor.on('keyup', function (e) { 
						this.save();
						jQuery( this.targetElm ).trigger('keyup');
					});
					e.editor.on('change', function (e) { 
						this.save();
						jQuery( this.targetElm ).trigger('change');
					});
				});
			}
		}
		if( $('.color-field').length ){
			$('.color-field').wpColorPicker({
				change: function(obj){
					
					var trigger = $(this);
					if( trigger.data('target') ){
						$( trigger.data('target') ).css( trigger.data('style'), trigger.val() );
					}
					
				}
			});
		}
		if( $('.ingot-group-wrapper').length ){
			$( ".ingot-group-wrapper" ).sortable({
				handle: ".sortable-item",
				start: function(ev, ui){
					ui.item.data('moved', true);
					ui.placeholder.css( 'borderWidth', ui.item.css( 'borderTopWidth') );
					ui.placeholder.css( 'margin', ui.item.css( 'marginTop') );					
				},
				update: function(ev, ui){
					jQuery('#ingot-id').trigger('change');
				}
			});
			$( ".ingot-fields-list" ).sortable({
				handle: ".sortable-item",
				update: function(){
					jQuery('#ingot-id').trigger('change');
				}
			});
		}

		//wp_editor refresh
		ingot_init_wp_editors();

		// live change init
		$('[data-init-change]').trigger('change');
		$('[data-auto-focus]').focus().select();

		// rebuild tags
		ingot_rebuild_magics();
		jQuery(document).trigger('canvas_init');
	}
	
	ingot_add_node = function(node, node_default){
		var id = 'nd' + Math.round(Math.random() * 99866) + Math.round(Math.random() * 99866),
			newnode = { "_id" : id },
			nodes = node.split('.'),
			node_point_record = nodes.join('.') + '.' + id,
			node_defaults = JSON.parse( '{ "_id" : "' + id + '", "_node_point" : "' + node_point_record + '" }' );

		if( node_default && typeof node_default === 'object' ){				
			$.extend( true, node_defaults, node_default );
		}			
		var node_string = '{ "' + nodes.join( '": { "') + '" : { "' + id + '" : ' + JSON.stringify( node_defaults );
		for( var cls = 0; cls <= nodes.length; cls++){
			node_string += '}';
		}
		var new_nodes = JSON.parse( node_string );
		$.extend( true, ingot_config_object, new_nodes );
	};

	ingot_get_default_setting = function(obj){

		var id = 'nd' + Math.round(Math.random() * 99866) + Math.round(Math.random() * 99866),
			trigger = ( obj.trigger ? obj.trigger : obj.params.trigger ),
			sub_id = ( trigger.data('group') ? trigger.data('group') : 'nd' + Math.round(Math.random() * 99766) + Math.round(Math.random() * 99866) ),
			nodes;

		
		// add simple node
		if( trigger.data('addNode') ){
			// new node? add one
			ingot_add_node( trigger.data('addNode'), trigger.data('nodeDefault') );
		}
		// remove simple node (all)
		if( trigger.data('removeNode') ){
			// new node? add one
			if( ingot_config_object[trigger.data('removeNode')] ){
				delete ingot_config_object[trigger.data('removeNode')];
			}

		}

		switch( trigger.data('script') ){
			case "add-to-object":
				// add to core object
				//ingot_config_object.entry_name = obj.data.value; // ajax method

				break;
			case "add-field-node":
				// add to core object
				if( !ingot_config_object[trigger.data('slug')][trigger.data('group')].field ){
					ingot_config_object[trigger.data('slug')][trigger.data('group')].field = {};
				}
				ingot_config_object[trigger.data('slug')][trigger.data('group')].field[id] = { "_id": id, 'name': 'new field', 'slug': 'new_field' };
				ingot_config_object.open_field = id;
				break;				
		}

		ingot_rebuild_canvas();

	};

	ingot_rebuild_canvas = function(){
		jQuery('#ingot-live-config').val( JSON.stringify( ingot_config_object ) );
		jQuery('#ingot-field-sync').trigger('refresh');	
	};
	// sutocomplete category
	$.widget( "custom.catcomplete", $.ui.autocomplete, {
		_create: function() {
			this._super();
			this.widget().menu( "option", "items", "> :not(.ui-autocomplete-category)" );
		},
		_renderMenu: function( ul, items ) {
			var that = this,
			currentCategory = "";
			$.each( items, function( index, item ) {
				var li;
				if ( item.category != currentCategory ) {
					ul.append( "<li class='ui-autocomplete-category'>" + item.category + "</li>" );
					currentCategory = item.category;
				}
				li = that._renderItemData( ul, item );
				if ( item.category ) {
					li.attr( "aria-label", item.category + " : " + item.label );
				}
			});
		}
	});
	ingot_rebuild_magics = function(){

		function split( val ) {
			return val.split( / \s*/ );
		}
		function extractLast( term ) {
			return split( term ).pop();
		}
		$( ".magic-tag-enabled" ).bind( "keydown", function( event ) {
			if ( event.keyCode === $.ui.keyCode.TAB && $( this ).catcomplete( "instance" ).menu.active ) {
				event.preventDefault();
			}
		}).catcomplete({
			minLength: 0,
			source: function( request, response ) {
				// delegate back to autocomplete, but extract the last term
				ingot_magic_tags = [];
				var category = '',
					tags = $('.ingot-magic-tags-definitions');

				if( tags.length ){

					for( var tag_set = 0; tag_set < tags.length; tag_set++ ){

						var magic_tags;
						
						category = 'Magic Tags';

						if( $( tags[ tag_set ] ).data('category') ){
							category = $( tags[ tag_set ] ).data('category');
						}
						// set internal tags
						try{
							magic_tags = JSON.parse( tags[ tag_set ].value );
						} catch (e) {
							magic_tags = [ $(tags[ tag_set ]).data('tag') ];
						}

						var display_label;
						for( f = 0; f < magic_tags.length; f++ ){
							display_label = magic_tags[f].split( '*' );
							if( display_label[1] ){
								display_label = display_label[0] + '*';
							}
							ingot_magic_tags.push( { label: '{' + display_label + '}',value: '{' + magic_tags[f] + '}', category: category }  );
						}
					}

				}
				
				response( $.ui.autocomplete.filter( ingot_magic_tags, extractLast( request.term ) ) );
			},
			focus: function() {
				// prevent value inserted on focus
				return false;
			},
			select: function( event, ui ) {
				var terms = split( this.value );
				// remove the current input
				terms.pop();
				// add the selected item
				terms.push( ui.item.value );
				// add placeholder to get the comma-and-space at the end
				//terms.push( "" );
				this.value = terms.join( " " );
				return false;
			}
		});
	}

	ingot_init_wp_editors = function(){

		if( typeof tinyMCEPreInit === 'undefined'){
			return;
		}

		var ed, init, edId, qtId, firstInit, wrapper;

		if ( typeof tinymce !== 'undefined' ) {

			for ( edId in tinyMCEPreInit.mceInit ) {

				if ( firstInit ) {
					init = tinyMCEPreInit.mceInit[edId] = tinymce.extend( {}, firstInit, tinyMCEPreInit.mceInit[edId] );
				} else {
					init = firstInit = tinyMCEPreInit.mceInit[edId];
				}

				wrapper = tinymce.DOM.select( '#wp-' + edId + '-wrap' )[0];

				if ( ( tinymce.DOM.hasClass( wrapper, 'tmce-active' ) || ! tinyMCEPreInit.qtInit.hasOwnProperty( edId ) ) &&
					! init.wp_skip_init ) {

					try {
						tinymce.init( init );

						if ( ! window.wpActiveEditor ) {
							window.wpActiveEditor = edId;
						}
					} catch(e){}
				}
			}
		}
		
		for ( qtId in tinyMCEPreInit.qtInit ) {
			try {
				quicktags( tinyMCEPreInit.qtInit[qtId] );

				if ( ! window.wpActiveEditor ) {
					window.wpActiveEditor = qtId;
				}
			} catch(e){};
		}

		jQuery('.wp-editor-wrap').on( 'click.wp-editor', function() {

			if ( this.id ) {
				window.wpActiveEditor = this.id.slice( 3, -5 );
			}
		});


	}

	// trash 
	$(document).on('click', '.ingot-card-actions .confirm a', function(e){
		e.preventDefault();
		var parent = $(this).closest('.ingot-card-content');
			actions = parent.find('.row-actions');

		actions.slideToggle(300);
	});

	// bind slugs
	$(document).on('keyup change', '[data-format="slug"]', function(e){

		var input = $(this);

		if( input.data('master') && input.prop('required') && this.value.length <= 0 && e.type === "change" ){
			this.value = $(input.data('master')).val().replace(/[^a-z0-9]/gi, '_').toLowerCase();
			if( this.value.length ){
				input.trigger('change');
			}
			return;
		}

		this.value = this.value.replace(/[^a-z0-9]/gi, '_').toLowerCase();
	});
	
	// bind label update
	$(document).on('keyup change', '[data-sync]', function(){
		var input = $(this),
			syncs = $(input.data('sync'));
		
		syncs.each(function(){
			var sync = $(this);

			if( sync.is('input') ){
				sync.val( input.val() ).trigger('change');
			}else{
				sync.text(input.val());
			}
		});
	});
	// bind toggles
	$(document).on('click', '[data-toggle]', function(){
		
		var toggle = $(this).data('toggle'),
			target = $(toggle);
		
		target.each(function(){
			var tog = $(this);
			if( tog.is(':checkbox') || tog.is(':radio') ){
				if( tog.prop('checked') ){
					tog.prop('checked', false);
				}else{
					tog.prop('checked', true);
				}
				ingot_record_change();
			}else{
				tog.toggle();
			}
		});

	});	

	// bind tabs
	$(document).on('click', '.ingot-nav-tabs a', function(e){
		
		e.preventDefault();
		var clicked 	= $(this),
			tab_id 		= clicked.attr('href'),
			required 	= $('[required]'),
			clean		= true;

		for( var input = 0; input < required.length; input++ ){
			if( required[input].value.length <= 0 && $( required[input] ).is(':visible') ){
				$( required[input] ).addClass('ingot-input-error');
				clean = false;
			}else{
				$( required[input] ).removeClass('ingot-input-error');
			}
		}
		if( !clean ){
			return;
		}

		if( ingot_code_editor ){
			ingot_code_editor.toTextArea();
			ingot_code_editor = false;
		}

		if( $( tab_id ).find('.ingot-code-editor').length ){

			ingot_init_editor( $( tab_id ).find('.ingot-code-editor').prop('id') );
			ingot_code_editor.refresh();
			ingot_code_editor.focus();
		}

		jQuery('#ingot-active-tab').val(tab_id).trigger('change');
		ingot_record_change();
	});

	// row remover global neeto
	$(document).on('click', '[data-remove-parent]', function(e){
		var clicked = $(this),
			parent = clicked.closest(clicked.data('removeParent'));
		if( clicked.data('confirm') ){
			if( !confirm(clicked.data('confirm')) ){
				return;
			}
		}
		parent.remove();
		ingot_record_change();
	});
	
	// row remover global neeto
	$(document).on('click', '[data-remove-element]', function(e){
		var clicked = $(this),
			elements = $(clicked.data('removeElement'));
		if( clicked.data('confirm') ){
			if( !confirm(clicked.data('confirm')) ){
				return;
			}
		}
		elements.remove();
		ingot_record_change();
	});

	// init tags
	$('body').on('click', '.magic-tag-init', function(e){
		var clicked = $(this),
			input = clicked.prev();

		input.focus().trigger('init.magic');

	});
	
	// initialize live sync rebuild
	$(document).on('change', '[data-live-sync]', function(e){
		ingot_record_change();
	});

	// initialise baldrick triggers
	$('.wp-baldrick').baldrick({
		request     : ajaxurl,
		method      : 'POST',
		before		: function(el){
			
			var tr = $(el);

			if( tr.data('addNode') && !tr.data('request') ){
				tr.data('request', 'ingot_get_default_setting');
			}
		}
	});


	window.onbeforeunload = function(e) {

		if( ingot_canvas && ingot_canvas !== jQuery('#ingot-live-config').val() ){
			return true;
		}
	};


});







function ingot_init_editor(el){
	if( !jQuery('#' + el).length ){
		return;
	}	
	// custom modes
	var mustache = function(ingot_init, state) {

		var ch;

		if (ingot_init.match("{{")) {
			while ((ch = ingot_init.next()) != null){
				if (ch == "}" && ingot_init.next() == "}") break;
			}
			ingot_init.eat("}");
			return "mustache";
		}
		/*
		if (ingot_init.match("{")) {
			while ((ch = ingot_init.next()) != null)
				if (ch == "}") break;
			ingot_init.eat("}");
			return "mustacheinternal";
		}*/
		if (ingot_init.match("%")) {
			while ((ch = ingot_init.next()) != null)
				if (ch == "%") break;
			ingot_init.eat("%");
			return "command";
		}

		/*
		if (ingot_init.match("[[")) {
			while ((ch = ingot_init.next()) != null)
				if (ch == "]" && ingot_init.next() == "]") break;
			ingot_init.eat("]");
			return "include";
		}*/
		while (ingot_init.next() != null && 
			//!ingot_init.match("{", false) && 
			!ingot_init.match("{{", false) && 
			!ingot_init.match("%", false) ) {}
			return null;
	};

	var options = {
		lineNumbers: true,
		matchBrackets: true,
		tabSize: 2,
		indentUnit: 2,
		indentWithTabs: true,
		enterMode: "keep",
		tabMode: "shift",
		lineWrapping: true,
		extraKeys: {"Ctrl-Space": "autocomplete"},
		};
	// base mode

	CodeMirror.defineMode("mustache", function(config, parserConfig) {
		var mustacheOverlay = {
			token: mustache
		};
		return CodeMirror.overlayMode(CodeMirror.getMode(config, parserConfig.backdrop || 'text/html' ), mustacheOverlay);
	});
	options.mode = jQuery('#' + el).data('mode') ? jQuery('#' + el).data('mode') : "mustache";

	ingot_code_editor = CodeMirror.fromTextArea(document.getElementById(el), options);
	ingot_code_editor.on('keyup', tagFields);
	ingot_code_editor.on('blur', function(cm){
		cm.save();
		jQuery( cm.getInputField() ).trigger('change');
	});

	return ingot_code_editor;

}
(function() {
	"use strict";

	if( typeof CodeMirror === 'undefined' || ingot_canvas === false ){
		return;
	}

	var Pos         = CodeMirror.Pos;

	function getFields(cm, options) {

		var cur = cm.getCursor(), token = cm.getTokenAt(cur),
		result = [],
		fields = options.fields;

		if( cm.getMode().name === 'sqlmustache' ){
			options.mode = 'sqlmustache';
		}
		switch (options.mode){
			case 'mustache':
			var wrap = {start: "{{", end: "}}"},
			prefix = token.string.slice(2);
			break;
			case 'command':
			var wrap = {start: "%", end: "%"},
			prefix = token.string.slice(1);
			break;
			default:
			var wrap = {start: "", end: "}}"},
			prefix = token.string;
			break;
		}
		for( var field in fields){			
			if (field.indexOf(prefix) == 0 || prefix === '{' || fields[field].indexOf(prefix) == 0){
				if(prefix === '{'){
					wrap.start = '{';
				}
				result.push({text: wrap.start + field + wrap.end, displayText: fields[field]});
			}
		};

		return {
			list: result,
			from: Pos(cur.line, token.start),
			to: Pos(cur.line, token.end)
		};
	}
	CodeMirror.registerHelper("hint", "elementfield", getFields);
})();

function find_if_in_wrapper( open_entry, close_entry, cm ){
	in_entry = false;
	if( open_entry.findPrevious() ){
		


		// is entry. check if closed
		var open_pos  = open_entry.from();

		if( close_entry.findPrevious() ){
			// if closed after open then not in			
			var close_pos = close_entry.from();
			if( open_pos.line > close_pos.line ){
				// open is after close - on entry				
				in_entry = open_pos
			}else if( open_pos.line === close_pos.line ){
				// smame line - what point?
				if( open_pos.ch > close_pos.ch ){
					//after close - in entry
					in_entry = open_pos;
				}
			}else{
				
				open_entry 	= cm.getSearchCursor('{{#each ', open_pos);

				return find_if_in_wrapper( open_entry, close_entry, cm )
			}

		}else{
			
			in_entry = open_pos;
		}

	}

	// set the parent
	if( in_entry ){
		// find what tag is open
		var close_tag 	= cm.getSearchCursor( '}}', in_entry );
		if( close_tag.findNext() ){
			var close_pos	= close_tag.from();
				start_tag	= open_entry.to();
			
			in_entry = cm.getRange( start_tag, close_pos );

		}

	}

	return in_entry;
}

function tagFields(cm, e) {
	if( e.keyCode === 8 ){
		return; // no backspace.
	}
	//console.log( cm );
	var cur = cm.getCursor();

	// test search 
	var open_entry 	= cm.getSearchCursor('{{#each ', cur);
	var close_entry = cm.getSearchCursor('{{/each}}', cur);
	var open_if 	= cm.getSearchCursor('{{#if ', cur);
	var close_if 	= cm.getSearchCursor('{{/if', cur);	

	var in_entry 	= find_if_in_wrapper( open_entry, close_entry, cm );
	var in_if 		= false;





	if( open_if.findPrevious() ){
		// is if. check if closed
		var open_pos  = open_if.from();

		if( close_if.findPrevious() ){
			// if closed after open then not in			
			var close_pos = close_if.from();
			if( open_pos.line > close_pos.line ){
				// open is after close - on if
				in_if = true
			}else if( open_pos.line === close_pos.line ){
				// smame line - what point?
				if( open_pos.ch > close_pos.ch ){
					//after close - in if
					in_if = true;
				}
			}

		}else{
			in_if = true;
		}
	}

	if( in_if ){
		// find what tag is open
		var close_tag 	= cm.getSearchCursor( '}}', open_pos );
		if( close_tag.findNext() ){
			var close_pos	= close_tag.from();
				start_tag	= open_entry.to();
			
			in_if = cm.getRange( start_tag, close_pos );

		}

	}

	if (!cm.state.completionActive || e.keyCode === 18){
		var token = cm.getTokenAt(cur), prefix,
		prefix = token.string.slice(0);
		if(prefix){
			if(token.type){
				var fields = {};
				//console.log( token );
				if( token.type ){
					// only show fields within the entry
					if( in_entry ){
						
						if( !in_if ){
							// dont allow closing #each if in if
							fields = {
								"/each"			:	"/each"
							};
						}

						// ADD INDEX KEY
						fields['@key'] = "@key";

						jQuery('.ingot-autocomplete-in-entry-' + token.type).each(function(){
							var field = jQuery(this);

							if( !field.hasClass('parent-' + in_entry) && !field.hasClass('parant-all') ){
								return;
							}

							fields[field.data('slug')] = field.data('label');
							//fields["#each " + field.data('slug')] = "#each " + field.data('label');
							//if( !in_if ){
								if( field.data('label').indexOf('#') < 0 ){
									fields["#if " + field.data('slug')] = "#if " + field.data('label');
								}
							//}
							//fields["#unless " + field.data('slug')] = "#unless " + field.data('label');
						});
					}else{

						jQuery('.ingot-autocomplete-out-entry-' + token.type).each(function(){
							var field = jQuery(this);
							fields[field.data('slug')] = field.data('label');
							//fields["#each " + field.data('slug')] = "#each " + field.data('label');
							//if( !in_if ){
								if( field.data('label').indexOf('#') < 0 ){
									fields["#if " + field.data('slug')] = "#if " + field.data('label');
								}
							//}
							//fields["#unless " + field.data('slug')] = "#unless " + field.data('label');
						});

					}

					if( in_if ){
						fields['else'] = 'else';
						fields['/if'] = '/if';
					}
				}
				// sort hack
				var keys = [];
				var commands = [];
				var sorted_obj = {};

				for(var key in fields){
				    if(fields.hasOwnProperty(key)){
				    	if( key.indexOf('#') < 0 && key.indexOf('/') < 0 ){
				        	keys.push(key);
				    	}else{
				    		commands.push(key);
				    	}
				    }
				}

				// sort keys
				keys.sort();
				commands.sort();
				keys = keys.concat(commands);
				// create new array based on Sorted Keys
				jQuery.each(keys, function(i, key){
				    sorted_obj[key] = fields[key];
				});
				CodeMirror.showHint(cm, CodeMirror.hint.elementfield, {fields: sorted_obj, mode: token.type});

			}
		}
	}
	return;
}