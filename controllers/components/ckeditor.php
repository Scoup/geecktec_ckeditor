<?php
class CkeditorComponent extends Object {
	
	public $components = array('RequestHandler');
	
	public $actions = array(
		'admin/settings/prefix/GeecktecCkeditor' => array(
            array(
                'elements' => 'NodeBody',
            ),
		)
	);
	
	function startup(&$controller){
		$this->Controller = &$controller;
//		debug($controller->data);
	}
	
	function beforeFilter(&$controller){
		$this->Controller = &$controller;
		$action = $this->Controller->params['url']['url'];
//		debug($controller->data);
		if(isset($this->actions[$action]) && !empty($controller->data) && $this->RequestHandler->isPost()){
			debug('ok');
			debug($controller->data);
			exit;
		}else{
			debug('false');
		}
	}
}
?>