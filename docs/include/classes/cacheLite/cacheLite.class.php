<?php 

require_once('Lite.php');

class cacheLite {
//	public static $CAHCE_AGE_MINUTE = 60;
//	public static $CAHCE_AGE_HOUR = 3600; // 60 * 60
//	public static $CAHCE_AGE_DAY = 86400; // 3600 * 24
//	public static $CAHCE_AGE_WEEK = 604800; //  3600 * 24 * 7
//	public static $CAHCE_AGE_MONTH = 2592000; //  3600 * 24 * 0
	
	private static $options = array(
		'cacheDir' => CACHE_DIR,
    	'lifeTime' => 86400
	);
    
	private static $cache_Lite;
	
	public static function get($id, $group = 'default', $doNotTestCacheValidity = false) {
		// turn off cache if CACHE_DIR is not defined
		if (CACHE_DIR == '') return false;
		
		if(empty(cacheLite::$cache_Lite)) {
			cacheLite::$cache_Lite = new Cache_Lite(cacheLite::$options);
		}
		
		return cacheLite::$cache_Lite->get($id, $group, $doNotTestCacheValidity);
	}
	
	public static function save($data, $id = NULL, $group = 'default') {
		// turn off cache if CACHE_DIR is not defined
		if (CACHE_DIR == '') return false;
		
		if(empty(cacheLite::$cache_Lite)) {
			cacheLite::$cache_Lite = new Cache_Lite(cacheLite::$options);
		}
		
		return cacheLite::$cache_Lite->save($data, $id, $group);
	}
	
	public static function clean($group = false, $mode = 'ingroup') {
		// turn off cache if CACHE_DIR is not defined
		if (CACHE_DIR == '') return false;
		
		if(empty(cacheLite::$cache_Lite)) {
			cacheLite::$cache_Lite = new Cache_Lite(cacheLite::$options);
		}
		
		return cacheLite::$cache_Lite->clean($group, $mode);
	}
}
?>