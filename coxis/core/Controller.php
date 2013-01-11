<?php
namespace Coxis\Core;

class Controller extends Viewable {
	public $request;
	public $response;

	public static function fetchRoutes() {
		$routes = array();
		$class = get_called_class();
		$reflection = new \ReflectionAnnotatedClass($class);
		
		if($reflection->getAnnotation('Prefix'))
			$prefix = \Coxis\Core\Router::formatRoute($reflection->getAnnotation('Prefix')->value);
		else
			$prefix = '';
		
		$methods = get_class_methods($class);
		foreach($methods as $method) {
			if(!preg_match('/Action$/i', $method))
				continue;
			$method_reflection = new \ReflectionAnnotatedMethod($class, $method);
		
			if($method_reflection->getAllAnnotations('Route')) {
				foreach($method_reflection->getAllAnnotations('Route') as $annotation) {
					$route = \Coxis\Core\Router::formatRoute($prefix.'/'.$annotation->value);

					$routes[] = array(
						'route'	=>	$route,
						'controller'		=>	\Coxis\Core\Router::formatControllerName($class), 
						'action'			=>	\Coxis\Core\Router::formatActionName($method),
						'requirements'	=>	$method_reflection->getAnnotation('Route')->requirements,
						'method'	=>	$method_reflection->getAnnotation('Route')->method,
						'name'	=>	isset($method_reflection->getAnnotation('Route')->name) ? $method_reflection->getAnnotation('Route')->name:null
					);
				}
			}
		}

		return $routes;
	}

	public function addFilter($filter) {
		$filter->setController($this);
		if(method_exists($filter, 'before')) 
			$this->hook('before', array($filter, 'before'), $filter->getBeforePriority());
		if(method_exists($filter, 'after'))
			$this->hook('after', array($filter, 'after'), $filter->getAfterPriority());
	}
	
	public static function url_for($action, $params=array(), $relative=false) {
		return \URL::url_for(array(static::getControllerName(), $action), $params, $relative);
	}
	
	public static function getControllerName() {
		#todo what for?
		return preg_replace('/Controller$/', '', get_called_class());
	}

	public static function run($controllerShortname, $actionShortname, $request=null, $response=null) {
		if($request === null)
			$request = new Request;
		if($response === null)
			$response = new Response;

		$controllerClassName = $controllerShortname.'Controller';
		$actionName = $actionShortname.'Action';
		$controller = new $controllerClassName();

		$request->route = array('controller'=>$controllerShortname, 'action'=>$actionShortname);
		$controller->request = $request;
		$controller->response = $response;

		#todo move stuff in construct after having moved hooks out from controllers
		\Hook::trigger('controller_configure', array($controller));

		if(method_exists($controller, 'configure'))
			if($res = $controller->doRun('configure', array($request), false))
				return $res;

		if(!$result = $controller->trigger('before', array($controller))) {
			$result = $controller->doRun($actionName, array($request));
			$controller->trigger('after', array($controller, &$result));
		}

		if($result !== null) {
			if(is_string($result))
				return $controller->response->setContent($result);
			elseif($result instanceof \Coxis\Core\Response)
				return $result;
			else
				throw new \Exception('Controller response is invalid.');
		}
		else
			return $controller->response;
	}
}