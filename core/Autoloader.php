<?php
namespace Coxis\Core;

require_once 'core/Importer.php';

class Autoloader {
	public static $map = array(
		//~ 'Somewhere'	=>	'there/somewhere.php',
	);
	public static $directories = array(
		//~ 'foo\bar'	=>	'there',
		//~ 'swift_'	=>	'swift',
	);
	public static $preloaded = array(
		//~ array('Somewhere', 'there/somewhere.php'),
	);
	
	public static function map() {
	}
	
	public static function dir() {
	}
	
	public static function preloadClass($class, $file) {
		static::$preloaded[] = array(strtolower($class), $file);
	}
	
	public static function preloadDir($file) {
		if(is_dir($file))
			foreach(glob($file.'/*') as $sub_file)
				static::preloadDir($sub_file);
		else {
			list($class) = explode('.', basename($file));
			static::preloadClass($class, $file);
		}
	}
	
	public static function loadClass($class) {
		$dir = dirname($class);
		Importer::_import($class, array('into'=>$dir));
	}
}