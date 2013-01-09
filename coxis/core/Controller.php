<?php
namespace Coxis\Core;

class Controller extends Hookable {
	public $action;

	public function addFilter($filter) {
		if(method_exists($filter, 'before')) 
			$this->hook('before', array($filter, 'before'), $filter->getBeforePriority());
		if(method_exists($filter, 'after'))
			$this->hook('after', array($filter, 'after'), $filter->getAfterPriority());
	}

	public function forward404($msg = 'Not found') {
		throw new NotFoundException($msg);
	}
	
	public static function url_for($action, $params=array(), $relative=false) {
		return \URL::url_for(array(static::getControllerName(), $action), $params, $relative);
	}
	
	public static function getControllerName() {
		return preg_replace('/Controller$/', '', get_called_class());
	}
	
	public function canonical($canonical, $relative=true, $redirect=true) {
		if($relative)
			$uri = \URL::get();
		else
			$uri = \URL::current();
		
		if($redirect && $canonical != $uri)
			throw new ControllerException('Page not found', \Response::setCode(301)->redirect($canonical, $relative));
		if($relative)
			HTML::code('<link rel="canonical" href="'.\URL::to($canonical).'">');
		else
			HTML::code('<link rel="canonical" href="'.$canonical.'">');
	}

	public static function addHooks($hooks) {
		foreach($hooks as $name=>$hooks)
			foreach($hooks as $hook)
				static::addControllerHook($name, $hook);
	}

	public static function addControllerHook($hookName, $hook) {
		\Hook::hookOn($hookName, function($chain, $arg1=null, $arg2=null, $arg3=null, $arg4=null,
			$arg5=null, $arg6=null, $arg7=null, $arg8=null, $arg9=null, $arg10=null) use($hook) {
			$args = array(&$arg1, &$arg2, &$arg3, &$arg4, &$arg5, &$arg6, &$arg7, &$arg8, &$arg9, &$arg10);
			return Controller::runHook($hook, $args);
		});
	}

	public static function runHook($hook, $args=array()) {
		if(!is_array($args))
			$args = array($args);
		$controller = $hook[0];
		$method = $hook[1];
		$controller = new $controller;
		return $controller->run($method, $args);
	}

	public function run($action, $params=array(), $showView=true) {
		$this->view = null;
	
		if(!is_array($params))
			$params = array($params);

		ob_start();
		$result = call_user_func_array(array($this, $action), $params);
		$controllerBuffer =  ob_get_clean();

		if($result !== null)
			return $result;
		if($controllerBuffer)
			return $controllerBuffer;
		elseif(!$showView) #todo still necessary?
			return null;
		elseif($this->view !== false) {
			$action = preg_replace('/Action$/', '', $action);
			if($this->view === null)
				if(!$this->setRelativeView($action.'.php'))
					return null;
			return $this->render($this->view, $this);
		}
		return null;
	}
	
	protected function component($controller, $action, $args=array()) {
		echo Router::run($controller, $action, $args);
	}

	public function noView() {
		$this->view = false;
	}
	
	public function setRelativeView($view) {
		$reflection = new \ReflectionObject($this);
		$dir = dirname($reflection->getFileName());
		$this->setView($dir.'/../views/'.strtolower(preg_replace('/Controller$/i', '', \Coxis\Core\NamespaceUtils::basename(get_class($this)))).'/'.$view);
		return file_exists($dir.'/../views/'.strtolower(preg_replace('/Controller$/i', '', \Coxis\Core\NamespaceUtils::basename(get_class($this)))).'/'.$view);
	}
	
	public function setView($view) {
		$this->view = $view;
	}
	
	public function render($_view, $_args=array()) {
		$reflection = new \ReflectionObject($this);	
		$dir = dirname($reflection->getFileName());

		foreach($_args as $_key=>$_value)
			$$_key = $_value;#TODO, watchout keywords

		ob_start();
		\Memory::set('in_view', true);
		include($_view);
		\Memory::set('in_view', false);
		return ob_get_clean();
	}
}