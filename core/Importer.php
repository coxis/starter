<?php
namespace {
	function from($from='') {
		return new \Coxis\Core\Importer($from);
	}
	function import($what, $into='') {
		return from()->import($what, $into);
	}
}

namespace Coxis\Core {
	class Importer {
		public $from = '';
		public static $preimported = array();

		public function __construct($from='') {
			$this->from = $from;
		}
		
		public function import($what, $into='') {
			$imports = explode(',', $what);
			foreach($imports as $import) {
				$import = trim($import);
				
				$class = $what;
				$alias = basename($class);
				$vals = explode(' as ', $import);
				if(isset($vals[1])) {
					$class = trim($vals[0]);
					$alias = trim($vals[1]);
				}
			
				$alias = preg_replace('/^\\\+/', '', $into.'\\'.$alias);
				$class = preg_replace('/^\\\+/', '', $this->from.'\\'.$class);
				static::$preimported[$alias] = $class;
				
				#import directly or preimport
				//~ if(!static::_import($this->from.'\\'.$class, array('as'=>$alias, 'into'=>$into)))
					//~ throw new \Exception($this->from.'\\'.$class.' not found!');
			}
		
			return $this;
		}
		
		public static function _import($class, $params=array()) {
			$class = preg_replace('/^\\\+/', '', $class);
			$alias = isset($params['as']) ? $params['as']:null;
			$intoNamespace= isset($params['into']) ? $params['into']:null;
			
			if($intoNamespace == '.')
				$intoNamespace = '';
		
			$path = static::class2path($class);
			
			if(isset(static::$preimported[$class])) {
				if(static::_import(static::$preimported[$class], array('as'=>false))) {
					$toload = static::$preimported[$class];
					
					#import as ..
					if($alias !== false) {
						if(!$alias)
							$alias = ($intoNamespace ? $intoNamespace.'\\':'').basename($class);
							
						if($toload != $alias)
							try {
								class_alias($toload, $alias);
							} catch(\Exception $e) {
								return false;
							}
					}
					
					return true;
				}
			}	
			
			if(static::loadClass($class)) {
				#import as ..
				if($alias !== false) {
					if(!$alias)
						$alias = ($intoNamespace ? $intoNamespace.'\\':'').basename($class);
					if($class != $alias)
						try {
							class_alias($class, $alias);
						} catch(\Exception $e) {
							return false;
						}
				}
					
				return true;
			}
			else {
				$dir = dirname($class);
				
				if($dir != '.') {
					if(dirname($dir) == '.')
						$next = basename($class);
					else
						$next = dirname($dir).'\\'.basename($class);
				
					return static::_import($next, array('into'=>$intoNamespace, 'as'=>$alias));
				}
			
				return false;
			}
		}
		
		public static function class2path($class) {
			$namespace = dirname($class);
			
			#remove vendor prefix
			$namespace = preg_replace('/^[a-zA-Z0-9]+\\\/', '', $namespace, -1, $count);
			if($count == 0)
				$namespace = preg_replace('/[a-zA-Z0-9]+_/', '', $namespace);
			
			$className = basename($class);

			if($namespace != '.')
				$path = str_replace('\\', DIRECTORY_SEPARATOR , $namespace).DIRECTORY_SEPARATOR ;
			else
				$path = '';
			$path .= str_replace('_', DIRECTORY_SEPARATOR , $className);
			
			return $path.'.php';
		}
		
		public static function loadClassFile($file) {
			$before = get_declared_classes();
			require_once $file;
			$after = get_declared_classes();
			
			$diff = array_diff($after, $before);
			foreach($diff as $class)		
				if(method_exists($class, '_autoload'))
					try {
						call_user_func(array($class, '_autoload'));
					} catch(\Exception $e) {
						d($e); #todo error report this exception cause autoloader does not let it bubble up
					}
			
			return get(array_values($diff), sizeof($diff)-1);
		}

		public static function loadClass($class) {
			#already loaded
			if(class_exists($class, false))
				return true;
			#file map
			elseif(isset(Autoloader::$map[$class])) {
				static::loadClassFile(Autoloader::$map[$class]);
				return true;
			}
			else {
				#directory map
				foreach(Autoloader::$directories as $prefix=>$dir) {
					if(preg_match('/^'.preg_quote($prefix).'/', $class)) {
						$rest = preg_replace('/^'.preg_quote($prefix).'\\\?/', '', $class);
						$path = $dir.DIRECTORY_SEPARATOR.static::class2path($rest);
						
						if(file_exists($path)) {
							static::loadClassFile($path);
							return true;
						}
					}
				}
				
				#to load
				if(file_exists(($path = static::class2path($class)))) {
					static::loadClassFile($path);
					if(class_exists($class, false))
						return true;
				}
				
				//~ d();#only to test importer
				
				#lookup for global classes
				if(dirname($class) == '.') {
					$classes = array();
					
					#check if there is any corresponding class already loaded
					foreach(get_declared_classes() as $v)
						if(strtolower(basename($class)) == strtolower(basename($v))) {
							class_alias($v, $class);
							return true;
						}
					
					#remove, only for testing class loading
					//~ return false;
					
					foreach(Autoloader::$preloaded as $v)
						if(strtolower(basename($class)) == $v[0])
							$classes[] = $v;
							
					if(sizeof($classes) == 1) {
						$before = get_declared_classes();
						static::loadClassFile($classes[0][1]);
						$after = get_declared_classes();
						
						$diff = array_diff($after, $before);
						
						if(class_exists(basename($class), false))
							return true;
						else {
							#maybe the loaded class uses another namespace?
							$res = array_values(preg_grep('/'.basename($class).'$/i', $diff));
							try {
								$loadedClass = $res[sizeof($res)-1];
								class_alias($loadedClass, $class);
								return true;
							} catch(\ErrorException $e) {
								return false;
							}
						}
					}
					#if no or multiple classes, don't load
					else
						return false;
				}
			}
			
			return false;
		}
	}
}