<?php
namespace Coxis\Core\Tools;

class Time {
	public $timestamp = null;

	public function __construct($t=null) {
		#todo
		// if(!$t)
		// 	$t = time();
		$this->timestamp = $t;
	}

	public function iso() {
		return date('r', $this->timestamp);
	}
	
	public function datetime() {
		return $this->format('Y-d-m H:i:s');
	}
	
	public function date() {
		return $this->format('d/m/Y');
	}
	
	private static function escape($str) {
		return '\\'.implode('\\', str_split($str));
	}

	#todo put i18n somewhere else, SoC
	public function format($format) {
		if(!$this->timestamp)
			return '';
		$format = str_replace('F', static::escape(Tools::$months[date('F', $this->timestamp)]), $format);

		return date($format, $this->timestamp);
	}

	public static function dateToSQLFormat($date) {
		if($date=='')
			return '';
		list($d, $m, $y) = explode('/', $date);
		return $y.'-'.$m.'-'.$d;
	}
	
	public static function SQLFormatToDate($date) {
		if($date=='')
			return '';
		list($y, $m, $d) = explode('-', $date);
		return $d.'/'.$m.'/'.$y;
	}
	
	public static function toTimestamp($value) {
		try {
			list($d, $m, $y) = explode('/', $value);
			return mktime(0, 0, 0, $m, $d, $y);
		} catch(Exception $e) {
			return 0;
		}
	}
	
	public static function toDate($value) {
		return date('d/m/Y', $value);
	}
}