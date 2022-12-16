/**
 * @license Copyright (c) 2003-2019, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see https://ckeditor.com/legal/ckeditor-oss-license
 * 
 * ATENÇÃO: MEXEU AQUI! LIMPAR O CACHE DO NAVEGADOR!
 */

CKEDITOR.editorConfig = function( config ) {
	// Define changes to default configuration here. For example:
	// config.uiColor = '#AADC6E';
	
	config.language = 'pt-br';
	
	//Ajusta a altura da árvore de tópicos e do editor de texto
	var altura_window = $(window).height();		
	var altura_cabecalho = $('#conteudo_cabecalho').outerHeight();
	var altura = altura_window - (altura_cabecalho + 20);
	config.height = altura * 0.70;	
	$('#jstree').css('max-height', altura);
	$('#jstree').css('min-height', altura);
	
	config.extraPlugins = 'filebrowser';
	config.filebrowserImageUploadUrl = '/tutorial/upload.php';
	
	//config.allowedContent = true;
	config.extraAllowedContent = 'style;*[id,rel](*){*}';
	
	//config.filebrowserImageUploadUrl = '/teste/s3upload/upload.php?type=Files&CKEditor=editortexto&CKEditorFuncNum=66&langCode=pt-br';
	//filebrowserUploadUrl: '/teste/s3upload/upload.php'	
	
	//config.extraPlugins = 'autogrow';
	//config.autoGrow_minHeight = altura;	
	//config.autoGrow_maxHeight = altura;	
	//config.autoGrow_bottomSpace = 50;
	
	//config.extraPlugins = 'resize';
	//config.resize_minWidth = 150;
	//config.resize_dir = 'both';
	
	
	config.toolbarGroups = [
		{ name: 'document', groups: [ 'mode', 'document', 'doctools' ] },
		{ name: 'clipboard', groups: [ 'clipboard', 'undo' ] },
		{ name: 'editing', groups: [ 'find', 'selection', 'spellchecker', 'editing' ] },
		{ name: 'forms', groups: [ 'forms' ] },
		'/',
		{ name: 'basicstyles', groups: [ 'basicstyles', 'cleanup' ] },
		{ name: 'paragraph', groups: [ 'list', 'indent', 'blocks', 'align', 'bidi', 'paragraph' ] },
		{ name: 'links', groups: [ 'links' ] },
		{ name: 'insert', groups: [ 'insert' ] },
		'/',
		{ name: 'styles', groups: [ 'styles' ] },
		{ name: 'colors', groups: [ 'colors' ] },
		{ name: 'tools', groups: [ 'tools' ] },
		{ name: 'others', groups: [ 'others' ] },
		{ name: 'about', groups: [ 'about' ] }
	];

	config.removeButtons = 'Templates,NewPage,Form,Checkbox,Radio,TextField,Textarea,Select,Button,ImageButton,HiddenField,CreateDiv,Language,BidiRtl,BidiLtr,Flash,Iframe,PageBreak,ShowBlocks';
	
};
