<?php
namespace Coxis\Bundles\Value\Models;

class Value extends \Coxis\Core\Model {
	public static $stored = array();

	public function __toString() {
		return $this->key;
	}
	
	public static $properties = array(
		'key',
		'value'    => array(
			'required'    =>    false,
		),
	);
	
	public static $relationships = array();
	public static $behaviors = array();
	public static $meta = array();
	
	public static function fetch($name) {
		if(isset(static::$stored[$name]))
			$value = static::$stored[$name];
		else
			static::$stored[$name] = $value = static::loadByKey($name);
		if(!$value)
			static::$stored[$name] = $value = static::create(array('key'=>$name));
		
		return $value;
	}

	public static function val($name) {
		return static::fetch($name)->value;
	}

	public static function setVal($name, $val) {
		return static::fetch($name)->save(array('value'=>$val));
	}

	public static function rawVal($name) {
		return static::fetch($name)->raw('value');
	}
}