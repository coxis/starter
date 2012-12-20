<?php
namespace Coxis\Core\Tools;

class FileManager {
	public static function getNewFileName($output) {
		$fileexts = explode('.', $output);
		$filename = implode('.', array_slice($fileexts, 0, -1));
		$ext = $fileexts[sizeof($fileexts)-1];
		$output = $filename.'.'.$ext;
		
		$i=1;
		while(file_exists(_DIR_.$output))
			$output = $filename.'_'.($i++).'.'.$ext;

		return $output;
	}

	public static function move($src, $output, $absolute=false) {
		$output = static::getNewFileName($output);
			
		static::mkdir(dirname($output), $absolute);
			
		if(!copy($src, $output))
			return false;
		else {
			unlink($src);
			return basename($output);
		}
	}

	public static function move_uploaded($src, $output) {
		$output = static::getNewFileName($output);
			
		static::mkdir(dirname($output));
			
		if(!move_uploaded_file($src, $output))
			return false;
		else
			return basename($output);
	}
	
	public static function isUploaded($file) {
		return (isset($file['tmp_name']) && !empty($file['tmp_name']));
	}
	
	public static function unlink($file) {
		if(file_exists(_DIR_.$file)) {
			unlink($file);
			return true;
		}
		else
			return false;
	}
	
	public static function mkdir($dir, $absolute=false) {
		if(!$absolute)
			$dir = _DIR_.$dir;
		if(!file_exists($dir))
			return mkdir($dir, 0777, true);
		return true;
	}

	public static function put($file, $content) {
		static::mkdir(dirname($file));
		file_put_contents($file, $content);
	}
}