<?php
namespace Coxis\Core\Properties;

class DateProperty extends BaseProperty {
	public function getRules() {
		$rules = parent::getRules();
		$rules['date'] = true;

		return $rules;
	}

	public function _getDefault() {
		return new \Coxis\Core\Tools\Date(0);
	}

	public function serialize($obj) {
		if($obj == null)
			return null;
		$d = $obj->date();
		list($d, $m, $y) = explode('/', $d);
		$d = $y.'-'.$m.'-'.$d;
		return $d;
	}

	public function unserialize($str) {
		if($str == null)
			return \Coxis\Core\Tools\Date::fromDate(null);
		list($y, $m, $d) = explode('-', $str);
		$str = $d.'/'.$m.'/'.$y;
		return \Coxis\Core\Tools\Date::fromDate($str);
	}

	public function set($val) {
		if(!$val)
			return null;
		if(preg_match('/[0-9]{2}\/[0-9]{2}\/[0-9]{4}/', $val))
			return \Coxis\Core\Tools\Date::fromDate($val);
		else
			return $val;
	}

	public function getSQLType() {
		return 'date';
	}
}