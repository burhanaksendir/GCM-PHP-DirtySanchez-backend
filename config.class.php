<?php
// config.class.php
class configException extends Exception {}
 
class config {
 
	private static $config = array();
 
	public static function set($key, $val)
	{
 
		if(!isset(self::$config[$key])){
 
			self::$config[$key] = $val;
 
		} else {
 
			throw new configException("Key Already Exists!");
 
		}
 
	}
 
	public static function get($key)
	{
		if(self::$config[$key]) {
 
			return self::$config[$key];
 
		} else {
 
			throw new configException("Key does not exist!");
 
		}
	}
 
	public static function rename($old, $new)
	{
 
		if( self::$config[$old] ) {
 
			$val = config::get($old);
 
			self::$config[$new] = $val;		
		} else {
 
			throw new configException("Either the key does not exist or you have not provided a new key");
 
		}
 
		unset(self::$config[$old]);
	}
 
}