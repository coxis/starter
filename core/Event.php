<?php
namespace Coxis\Core;

class Event {
	static $filters_table = array();
	static $hooks_table = array();
	
	public static function addHooks($hooks) {
		foreach($hooks as $hook=>$callbacks)
			foreach($callbacks as $callback)
				static::addHook($hook, $callback);
	}
	
	public static function addHook($hook, $callback) {
		static::$hooks_table[strtolower($hook)][] = $callback;
	}
	
	public static function addFilters($filters) {
		foreach($filters as $filter=>$callbacks)
			foreach($callbacks as $callback)
				static::addFilter($filter, $callback);
	}
	
	public static function addFilter($filter, $callback) {
		static::$filters_table[strtolower($filter)][] = $callback;
	}
	
	public static function filter($filterName, $p, $args=array()) {
		$filterName = strtolower($filterName);
		if(isset(static::$filters_table[$filterName]))
			foreach(static::$filters_table[$filterName] as $filter)
				#should not only check is_array
				if(is_array($filter))
					$p = Router::run($filter['controller'], $filter['action'], array($p, $args));
				elseif(is_callable($filter))
					call_user_func_array($filter, array($p, $args));
			
		return $p;
	}

	public static function trigger($hookName, $args=null) {
		$hookName = strtolower($hookName);
		if(isset(static::$hooks_table[$hookName]))
			foreach(static::$hooks_table[$hookName] as $hook)
				#todo should not only check is_array
				if(is_array($hook))
					Router::run($hook['controller'], $hook['action'], $args, null, false);
				elseif(is_callable($hook))
					call_user_func_array($hook, array($args));
	}
	
	public static function trigger_show($hookName, $args=null) {
		$hookName = strtolower($hookName);
		if(isset(static::$hooks_table[$hookName]))
			foreach(static::$hooks_table[$hookName] as $hook)
				echo Router::run($hook['controller'], $hook['action'], $args);
	}
}