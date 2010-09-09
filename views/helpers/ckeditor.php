<?php
/**
 * Ckeditor Helper
 * 
 * PHP Version 5
 * 
 * @category Helper
 * @package GeecktecCkeditor
 * @version 1.0
 * @author Léo Haddad (scoup001@gmail.com)
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link http://blog.geecktec.com
 *
 */
class CkeditorHelper extends AppHelper {
	
	public $name = 'GeecktecCkeditor';
/**
 * Other helpers used by this helper
 *
 * @var array
 * @access public
 */
	public $helpers = array(
		'Html',
		'Js',
		'Form'
	);
	
/**
 * Actions
 *
 * Format: ControllerName/action_name => settings
 *
 * @var array
 */
    public $actions = array();
    
	/**
	 * Config of select for change the text for select box
	 * @var array
	 */
	public $adminConfig = array(
		'admin/settings/prefix/GeecktecCkeditor' => array(
            array(
                'elements' => 'NodeBody',
            ),
		)
	);
	/**
	 * Folders of files of configuration
	 * @var array
	 */
	public $config = array(
		'skin' => 'geecktec_ckeditor/webroot/js/ckeditor/skins',
		'lang' => 'geecktec_ckeditor/webroot/js/ckeditor/lang',
		'styles' => 'geecktec_ckeditor/webroot/js/ckeditor/_source/plugins/styles/styles',
		'templates' => 'geecktec_ckeditor/webroot/js/ckeditor/plugins/templates/templates'
	);
			
    
	/**
	 * Action for the gallery
	 * Null if will use the geecktec_gallery or 'plugin/controller/action' if you use another one.
	 * @var string
	 */
	public $filebrowserBrowseUrl = null;

	public function beforeRender(){
		if(is_array(Configure::read('GeecktecCkeditor.actions'))){
			$this->actions = Set::merge($this->actions, Configure::read('GeecktecCkeditor.actions'));
		}
		$action = Inflector::camelize($this->params['controller']).'/'.$this->params['action'];
		if (Configure::read('Writing.wysiwyg') && isset($this->actions[$action]) && ClassRegistry::getObject('view')) {
			$this->_initConfig();
			$this->Html->scriptBlock($this->fileBrowserCallBack(), array('inline' => false));
		}
		
		$action = $this->params['url']['url'];
		if(isset($this->adminConfig[$action]) && ClassRegistry::getObject('view')){
			$this->View =& ClassRegistry::getObject('view');
			$this->_generateJs($this->View->viewVars['settings']);
		}
		
		if ($this->params['controller'] == 'attachments' && $this->params['action'] == 'admin_browse') {
			$this->Html->scriptBlock($this->selectURL(), array('inline' => false));
		}
		
	}
	
	/**
	 * Init the config to work on edit/add nodes
	 */
	private function _initConfig(){
		$this->Html->script('/geecktec_ckeditor/js/ckeditor/ckeditor', array('inline' => false));
		$this->Html->script('/geecktec_ckeditor/js/ckeditor/adapters/jquery', array('inline' => false));
		
		$defaultConfig = Configure::read('GeecktecCkeditor.config');
		
		App::Import('Model', 'Setting');
		$this->Setting = new Setting();
		$config = $this->Setting->find('all', array(
			'conditions' => array(
				'Setting.key LIKE' => $this->name . '.%',
			)
		));

		$config = Set::combine($config, '{n}.Setting.key', '{n}.Setting.value');
		$this->config = array(
			'toolbar' => $defaultConfig['toolbar'][$config['GeecktecCkeditor.toolbar']],
			'skin' => $config['GeecktecCkeditor.skin'],
			'lang' => $config['GeecktecCkeditor.lang'],
			'styles' => $config['GeecktecCkeditor.styles'],
			'templates' => $config['GeecktecCkeditor.styles'],
			'filebrowserBrowseUrl' => $config['GeecktecCkeditor.filebrowserBrowseUrl'],
		);
		
		$plugins = Configure::read('Hook.bootstraps');
		$plugins = explode(',', $plugins);
		if(in_array('geecktec_filemanager', $plugins) && $this->config['filebrowserBrowseUrl'] == 'default'){
			$this->config['filebrowserBrowseUrl'] = 'admin/geecktec_filemanager/geecktec_filemanager_folders';
		}
	}
	
	/**
	 * Generate the javascript (jquery) code to shows the ckeditor on nodes
	 * @return string
	 */
	private function fileBrowserCallBack(){
		$filebrowserBrowseUr = $this->config['filebrowserBrowseUrl'] != 'default' ? "filebrowserBrowseUrl: '{$this->webroot}{$this->config['filebrowserBrowseUrl']}'" : "filebrowserBrowseUrl: '{$this->webroot}admin/attachments/browse',";
		
		$styles = $this->config['styles'] != 'default' ? "styles: '{$this->webroot}/{$this->name}/js/ckeditor/_source/plugins/styles/styles/{$this->config['styles']}," : null;
		$templates = $this->config['templates'] != 'default' ? "templates_file: '{$this->webroot}/{$this->name}/js/ckeditor/plugins/templates/templates/{$this->config['templates']}," : null;
				
		$retorno = 
			"
			$(function(){
				$('#NodeBody').ckeditor(function(){},
					{
						{$templates}
						{$styles}
						skin: '{$this->config['skin']}',
						language: '{$this->config['lang']}',
						toolbar: {$this->Js->object($this->config['toolbar'])},
						{$filebrowserBrowseUr}
					}
				);
			});";
		return $retorno;
	}
	
	function selectUrl(){
		$output = "
		$.urlParam = function(name){
			var results = new RegExp('[\\?&]' + name + '=([^&#]*)').exec(window.location.href);
			if(!results){
				return 1;
			}
			return results[1] || 0;
		}
		function selectURL(url) {
			url = '".Router::url('/uploads/', true)."' + url;
			window.opener.CKEDITOR.tools.callFunction($.urlParam('CKEditorFuncNum'), url);
			window.close();
        }";
		return $output;
	}
	
	/**
	 * Changes the text of configuration for select
	 */
	
	/**
	 * Generate of jquery code for replace the text for select
	 * @param array $settings
	 */
	private function _generateJs($settings){
		App::Import('Core', array('File', 'Folder'));
		$this->Folder =& new Folder();
		$options = $this->_generateOptions();
		
		foreach($settings as $key => $setting){
			$tmp = str_replace('GeecktecCkeditor.', '', $setting['Setting']['key']);
			$html = '<div class="setting">';
			$html .= str_replace("\n","", $this->Form->input("Setting.{$key}.id", array('value' => $setting['Setting']['id'])));
			$html .= str_replace("\n","", $this->Form->input("Setting.{$key}.key", array('type' => 'hidden', 'value' => $setting['Setting']['key'])));
			$html .= str_replace("\n","", $this->Form->input("Setting.{$key}.value", array(
				'label' => $setting['Setting']['title'],
				'class' => 'slug ui-corner-all', 
				'type' => 'select',
				'rel' => $setting['Setting']['description'],
				'selected' => $setting['Setting']['value'],		
				'options' => $options[$tmp]
			)));
			$html .= '</div>'; 
			$output = "
				$(function(){
					var html = '{$html}';
					$('#SettingAdminPrefixForm > fieldset').append(html);
					$('#Setting{$key}Id').parent().remove();
				});
			";
			$this->Html->scriptBlock($output, array('inline' => false));
		}
	}
	
	
	/**
	 * Config to works with windows and unix/linux plataform
	 */
	private function _setConfig(){
		foreach($this->config as &$config){
			$config = str_replace('/', DS, $config);
		}
	}

	/**
	 * Read the folders of options and return a array of options for form helper
	 * @return array
	 */
	private function _generateOptions(){
		$options = $tmp = array();
		foreach($this->config as $name => $config){
			$this->Folder->cd(APP.'plugins'.DS.$config);
			$tmp[$name] = $this->Folder->read();
		}
		$toolbar = Configure::read('GeecktecCkeditor.config');
		$toolbar = array_keys($toolbar['toolbar']);
		$options['toolbar'] = array_combine($toolbar, $toolbar);
		
		$options['skin'] = array_combine($tmp['skin'][0], $tmp['skin'][0]);
		
		$options['lang'] = $this->_removeExt($tmp['lang'][1]);
		$options['styles'] = $this->_removeExt($tmp['styles'][1]);
		$options['templates'] = $this->_removeExt($tmp['templates'][1]);
		return $options;
	}
	
	/**
	 * Remove the .js of files
	 * @param array $configs
	 * @return mixed
	 */
	private function _removeExt($configs = array()){
		$return = array();
		foreach($configs as $config){
			$tmp = str_replace('.js', '', $config);
			if($tmp.'.js' == $config && substr($config, 0, 1) != '_'){
				$return[$tmp] = $tmp;
			}
		}
		return $return;
	}	
	
}
?>