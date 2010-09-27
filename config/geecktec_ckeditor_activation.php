<?php
class GeecktecCkeditorActivation {
	
	public $name = 'GeecktecCkeditor';
	
	/**
	 * Configurações que podem ser modificadas no plugin
	 * @var array
	 */
	public $configs = array(
		array(
			'key' => 'GeecktecCkeditor.lang',
			'value' => 'en',
			'title' => 'Lang',
			'description' => 'Choose your default language',
			'input_type' => 'text',
			'editable' => 1,
			'weight' => 0,
		),
		array(
			'key' => 'GeecktecCkeditor.skin',
			'value' => 'kama',
			'title' => 'Skin',
			'description' => 'Choose your skin',
			'input_type' => 'text',
			'editable' => 1,
			'weight' => 1
		),		
		array(
			'key' => 'GeecktecCkeditor.toolbar',
			'value' => 'default',
			'title' => 'Toolbar',
			'description' => 'Choose your tools',
			'input_type' => 'text',
			'editable' => 1,
			'weight' => 2,
		),
		array(
			'key' => 'GeecktecCkeditor.styles',
			'value' => 'default',
			'title' => 'Styles',
			'description' => 'Choose your style (CSS)',
			'input_type' => 'text',
			'editable' => 1,
			'weight' => 3,
		),
		array(
			'key' => 'GeecktecCkeditor.output',
			'value' => 'default',
			'title' => 'Output',
			'description' => 'Choose your output (HTML)',
			'input_type' => 'text',
			'editable' => 0,
			'weight' => 4,
		),		
		array(
			'key' => 'GeecktecCkeditor.templates',
			'value' => 'default',
			'title' => 'Default Template',
			'description' => 'Choose your default template',
			'input_type' => 'text',
			'editable' => 1,
			'weight' => 5
		),	
		array(
			'key' => 'GeecktecCkeditor.filebrowserBrowseUrl',
			'value' => 'default',
			'title' => 'Filebrowser Browser Url',
			'description' => 'Choose your default filebrowser Browser url',
			'input_type' => 'text',
			'editable' => 0,
			'weight' => 6
		),						
	);
	
/**
 * onActivate will be called if this returns true
 *
 * @param  object $controller Controller
 * @return boolean
 */
    public function beforeActivation(&$controller) {
        return true;
    }
    	
	/**
	 * onActivation of plugin
	 * @param Object $controller
	 */
	public function onActivation(&$controller){
		$this->_initConfig();
	}
	
/**
 * onDeactivate will be called if this returns true
 *
 * @param  object $controller Controller
 * @return boolean
 */
    public function beforeDeactivation(&$controller) {
        return true;
    }
    	
	public function onDeactivation(&$controller){
	}
	
	/**
 	*	Init the SQL to config the plugin
 	*	All configurations are save in Setting Model
 	*	@author Léo Haddad
 	*	@version 1.0 
 	*/
	private function _initConfig(){
		App::import('Model', 'Setting');
		$this->Setting = new Setting();
		
		$plugins = $this->Setting->find('all', array(
			'order' => 'Setting.weight ASC',
			'conditions' => array(
				'Setting.key LIKE' => $this->name . '.%',
				'Setting.editable' => 1,
			)
		));
		
		$plugins = Set::extract('/Setting/key', $plugins);
		foreach($this->configs as $config){
			if(!in_array($config['key'], $plugins)){
				$this->Setting->create();
				$this->Setting->save($config);
			}
		}
	}
}
?>