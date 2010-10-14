<?php
/**
 * Configuration
 */
    Configure::write('GeecktecCkeditor.actions', array(
        'Nodes/admin_add' => array(
            array(
                'elements' => 'NodeBody',
            ),
        ),
        'Nodes/admin_edit' => array(
            array(
                'elements' => 'NodeBody',
            ),
        ),
        'Translate/admin_edit' => array(
            array(
                'elements' => 'NodeBody',
            ),
        ),
    ));
    
	/**
	 * Config default of ckeditor
	 * For more options: http://docs.cksource.com/CKEditor_3.x/Developers_Guide
	 * You can create new toolbars here
	 * The another options you'll have to see bellow
	 * @var array
	 */    
    Configure::write('GeecktecCkeditor.config', array(
    	'toolbar' => array(
			'default' => array(
	        	array('Source', '-', 'Save', 'NewPage', 'Preview', '-', 'Templates'),
	        	array('Cut', 'Copy', 'Paste','PasteText', 'PasteFromWord', '-', 'Print', 'SpellChecker', 'Scayt'),
	        	array('Undo', 'Redo', '-', 'Find', 'Replace', '-', 'SelectAll', 'RemoveFormat'),
	        	array('Bold', 'Italic', 'Underline', 'Strike', '-', 'Subscript', 'Superscript'),
	        	array('NumberedList', 'BulletedList', '-', 'Outdent', 'Indent', 'Blockquote'),
	        	array('JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock'),
		array('Link', 'Unlink', 'Anchor'),
	        	array('Image', 'Flash', 'Table', 'HorizontalRule', 'SpecialChar'),
	        	array('TextColor', 'Maximize', 'ShowBlocks'),
	        	array('Styles', 'Format', 'Font', 'FontSize')	
			),
			'basic' => array(
				array('Bold', 'Italic', '-', 'NumberedList', 'BulletedList', '-', 'Link', 'Unlink', '-', 'About')
			)
		),
		/**
		 * For another skin, put it in ckeditor/skins
		 */		
		'skin' => 'kama',
		/**
		 * For more languages put it in ckeditor/lang
		 */
		'lang' => 'en',
		/**
		 * For another style, put it in ckeditor/_source/plugins/styles/styles/
		 * For more information: http://docs.cksource.com/CKEditor_3.x/Developers_Guide/Styles	
		 */
		'styles' => null,
		/**
		 * For another templates, put it in ckeditor/plugins/templates/templates/
		 * For more information: http://docs.cksource.com/CKEditor_3.x/Developers_Guide/Templates	
		 */		
		'templates' => null,
		/**
		 * This option is for intagration for GeecktecFilemanager
		 * If you want to integrate with ckfinder or another filemanager you'll have to change manually on database
		 * You'll have to change the table settings, GeecktecCkeditor.filebrowserBrowseUrl for your plugin/controoller/action
		 */
		'filebrowserBrowseUrl' => null
    ));
    
/**
 * Hook helper
 */
    foreach (Configure::read('GeecktecCkeditor.actions') AS $action => $settings) {
        $actionE = explode('/', $action);
        Croogo::hookHelper($actionE['0'], 'GeecktecCkeditor.Ckeditor');
    }
    Croogo::hookHelper('Attachments', 'GeecktecCkeditor.Ckeditor');    
	Croogo::hookHelper('Settings', 'GeecktecCkeditor.Ckeditor');
    
/**
 * Routes
 *
 * example_routes.php will be loaded in main app/config/routes.php file.
 */
//    Croogo::hookRoutes('Example');
/**
 * Behavior
 *
 * This plugin's Example behavior will be attached whenever Node model is loaded.
 */
//    Croogo::hookBehavior('Node', 'Example.Example', array());
/**
 * Component
 *
 * This plugin's Example component will be loaded in ALL controllers.
 */
    Croogo::hookComponent('Settings', 'GeecktecCkeditor.Ckeditor');
/**
 * Helper
 *
 * This plugin's Example helper will be loaded via NodesController.
 */
//    Croogo::hookHelper('*', 'Aulas.aulas');
/**
 * Admin menu (navigation)
 *
 * This plugin's admin_menu element will be rendered in admin panel under Extensions menu.
 */
    Croogo::hookAdminMenu('GeecktecCkeditor');
/**
 * Admin row action
 *
 * When browsing the content list in admin panel (Content > List),
 * an extra link called 'Example' will be placed under 'Actions' column.
 */
//    Croogo::hookAdminRowAction('Nodes/admin_index', 'Example', 'plugin:example/controller:example/action:index/:id');
/**
 * Admin tab
 *
 * When adding/editing Content (Nodes),
 * an extra tab with title 'Example' will be shown with markup generated from the plugin's admin_tab_node element.
 *
 * Useful for adding form extra form fields if necessary.
 */
//    Croogo::hookAdminTab('Nodes/admin_add', 'Example', 'example.admin_tab_node');
//    Croogo::hookAdminTab('Nodes/admin_edit', 'Example', 'example.admin_tab_node');
?>