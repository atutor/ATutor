<?php
/*
   phpCache v1.4.1 - PHP caching engine 
   Copyright (C) 2001 Nathan <nathan@0x00.org> 
   '.1' Bug Fix By Joel Kronenberg <joel.kronenberg@utoronto.ca>

   This program is free software; you can redistribute it and/or
   modify it under the terms of the GNU General Public License
   as published by the Free Software Foundation; either version 2
   of the License, or (at your option) any later version.

   This program is distributed in the hope that it will be useful,
   but WITHOUT ANY WARRANTY; without even the implied warranty of
   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
   GNU General Public License for more details.

   You should have received a copy of the GNU General Public License
   along with this program; if not, write to the Free Software
   Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
*/

if (defined('CACHE_DIR') && (CACHE_DIR != '')) {
	define('CACHE_ON', 1); /* enable caching */
} else {
	define('CACHE_ON', 0); /* disable caching */
}
if (!defined('CACHE_DIR')) {
	define('CACHE_DIR', '');
}


	$CACHE_DEBUG = 0;			/* Default: 0 - Turn debugging on/off */

	define('THIS_CACHE_DIR', CACHE_DIR . '/atutor_cache_' . DB_NAME);

	define('CACHE_GC', .10);	/* Default: .10 - Probability of garbage collection */
	define('CACHE_USE_STORAGE_HASH', 0);	/* Default: 1 - Use storage hashing.  This will increase peformance if you are caching many pages. */ 
	define('CACHE_STORAGE_CREATED', 0);	/* Default: 0 - This is a peformance tweak.  If you set this to 1, phpCache will not check if storage structures have been created.  Don't change this unles you are *SURE* the cache storage has been created. */
	define('CACHE_MAX_STORAGE_HASH', 23);	/* Don't touch this unless you know what you're doing */
	define('CACHE_STORAGE_PERM',	 0700);	/* Default: 0700 - Default permissions for storage directories. */
	define('CACHE_MAX_FILENAME_LEN', 250);	/* How long the cache storage filename can be before it will md5() the entire thing */

	$CACHE_HAS=array(	'ob_start'	=> function_exists('ob_start'),
						'realpath'	=> function_exists('realpath'),
						'crc32'		=> function_exists('crc32')
					);

	define('CACHE_VERSION', '1.4.1');
	define('CACHE_STORAGE_CHECKFILE',	THIS_CACHE_DIR 
										. '/.phpCache-storage-V'
										. CACHE_VERSION
										. '-HASH='
										. CACHE_USE_STORAGE_HASH);

	define('CACHE_INFO', 'phpCache v1.4.1 By nathan@0x00.org (.1 Bug Fix By joel.kronenberg@utoronto.ca)');	

	/* This resets the cache state */
	function cache_reset() {
		global $cache_pbufferlen, $cache_absfile, $cache_data, $cache_variables, $cache_headers, $cache_expire_cond, $cache_output_buffer;

		cache_debug(__LINE__ .': $cache_absfile -> '.$cache_absfile);

		$cache_pbufferlen = FALSE;
		$cache_absfile = NULL;
		$cache_data = array();
		$cache_fp = NULL;
		$cache_expire_cond = NULL;
		$cache_variables=array();
		$cache_headers=array();
		$cache_output_buffer='';

		cache_debug(__LINE__ .': $cache_absfile -> '.$cache_absfile);
	}

	/* Used to output to the cache output, should only be needed if you dont have output buffering (PHP3) */
	function cache_output($str) {
		global $cache_output_buffer;
		if (!$GLOBALS["CACHE_HAS"]['ob_start']) {
			$cache_output_buffer.=$str;
		}
		print $str;
	}

	/* Saves a header state between caching */
	function cache_header($header) {
		global $cache_headers;
		Header($header);
		cache_debug('Adding header '.$header);
		$cache_headers[]=$header;
	}

	/* This is a function used internally by phpCache to evaluate the conditional expiration.  This allows the eval() to have its own simulated namespace so it doesnt conflict with any others. */
	function cache_eval_expire($cond, &$vars) {
		extract($vars);
		$EXPIRE=FALSE;
		eval($cond);
		return !!$EXPIRE;
	}

	/* Call this function before a call to cache() to evaluate a dynamic expiration on cache_expire_variable()'s */
	function cache_expire_if($expr) {
		global $cache_expire_cond;
		$cache_expire_cond=$expr;
	}

	/* Call this function to add a variable to the expire variables store */
	function cache_expire_variable($vn) {
		cache_debug("Adding $vn to expire variable store");
		cache_variable($vn);
	}

	/* duh ? */
	function cache_debug($s) {
		global $CACHE_DEBUG;
		if ($CACHE_DEBUG) {
			print "Debug: $s<br>\n";
		}
	}

	/* Saves a variable state between caching */
	function cache_variable($vn) {
		global $cache_variables;
		cache_debug(__LINE__ . ": Adding $vn to the variable store");
		$cache_variables[] = $vn;
	}


	/* Returns the default key used by the helper functions */
	function cache_default_key() {
		global $HTTP_POST_VARS, $HTTP_GET_VARS, $QUERY_STRING;
		return md5("POST=" . serialize($HTTP_POST_VARS) . " GET=" . serialize($HTTP_GET_VARS) . "QS=" . $QUERY_STRING);
	}

	/* Returns the default object used by the helper functions */
	function cache_default_object() {
		global $REQUEST_URI, $SERVER_NAME, $SCRIPT_FILENAME;
		if ($GLOBALS["CACHE_HAS"]["realpath"]) {
			$sfn=realpath($SCRIPT_FILENAME);
		} else {
			$sfn=$SCRIPT_FILENAME;
		}
		$name="http://$SERVER_NAME/$sfn";
		return $name;
	}

	/* Caches the current page based on the page name and the GET/POST
		variables.  All must match or else it will not be fectched
		from the cache! */
	function cache_all($cachetime=120) {
		$key=cache_default_key();
		$object=cache_default_object();
		return cache($cachetime, $object, $key);
	}

	/* Same as cache_all() but it throws the session_id() into
		the equation */
	function cache_session($cachetime=120) {
		global $HTTP_POST_VARS, $HTTP_GET_VARS;
		$key=cache_default_key() . 'SESSIONID=' . session_id();
		$object=cache_default_object();
		return cache($cachetime, $object, $key);
	}

	/* Manually purge an item in the cache */
	function cache_purge($object, $key) {
		$thefile=cache_storage($object, $key);
		//cache_lock($thefile, TRUE);
		if (is_file($thefile)) {
			$ret=@unlink($thefile);
		}
		else {
			$ret = false;
		}
		//cache_lock($thefile, FALSE);
		return $ret;
	}

	/* Manually purge all items in the cache */
	function cache_purge_all() {
		return cache_gc(NULL, 1, TRUE);
	}

	/* Caches $object based on $key for $cachetime, will return 0 if the
		object has expired or the object does not exist. */
	function cache($cachetime, $object, $key=NULL) {
		global $cache_pbufferlen, $cache_absfile, $cache_file, $cache_data, $cache_expire_cond;
		cache_debug(__LINE__ .': $cache_absfile -> '.$cache_absfile);
		if (!CACHE_ON) {
			cache_debug('Not caching, CACHE_ON is off');
			return 0;
		}
		$curtime=time();
		cache_debug(__LINE__.': Caching based on <b>OBJECT</b>='.$object.' <b>KEY</b>='.$key);
		$cache_absfile=cache_storage($object, $key);
		cache_debug(__LINE__.': Got cache_storage: '.$cache_absfile);
		if (($buff=cache_read($cache_absfile))) {
			cache_debug('Opened the cache file');
			$cdata=unserialize($buff);
			if (is_array($cdata)) {
				$curco = $cdata['cache_object'];
				if ($curco!=$cache_absfile) {
					cache_debug("Holy shit that is not my cache file! why? got=$curco wanted=$cache_absfile");
				} else {
					$expireit = FALSE;
					if ($cache_expire_cond) {
						$expireit=cache_eval_expire($cache_expire_cond, $cdata['variables']);
					}
					if ($cdata['cachetime'] != $cachetime) {
						cache_debug('Expiring because cachetime changed');
						$expireit=TRUE;
					}
					if (!$expireit && ($cdata['cachetime']=="0" || $cdata['expire']>=$curtime)) {
						$expirein=$cdata['expire']-$curtime+1;
						cache_debug('Cache expires in '.$expirein);
						if (is_array($cdata['variables'])) {
							while (list($k,$v)=each($cdata['variables'])) {
								cache_debug("Restoring variable $k to value $v");
								$GLOBALS[$k]=$v;
							}
						}
						if (is_array($cdata['headers'])) {
							while(list(,$h)=each($cdata['headers'])) {
								cache_debug("Restoring header $h");
								Header("$h");
							}
						}
						print $cdata['content'];
						$ret=$expirein;
						if ($cdata['cachetime']=='0') $ret='INFINITE';
						cache_reset();
						return $ret; 
					}
				}
			}
		} else {
			cache_debug(__LINE__.': Failed to open previous cache of '.$cache_absfile);
		}
	
		$oldum = umask();
		umask(0077);
		/* readlink() is not supported on win32, changed to is_link */
		if (is_link($cache_absfile)) {
			cache_debug("$cache_absfile is a symlink! not caching!");
			$cache_absfile=NULL;
		} else {
			cache_debug(__LINE__.': not a symlink');
			cache_debug(__LINE__.': Got cache_storage: '.$cache_absfile);
			@touch($cache_absfile);
	
			/* cases probs on win32 */
			//cache_lock($cache_absfile, TRUE);
			/* */
		}
		umask($oldum);
		$cache_data['expire']	= $curtime + $cachetime;
		$cache_data['cachetime']= $cachetime;
		$cache_data['curtime']	= $curtime;
		$cache_data['version']	= CACHE_VERSION;
		$cache_data['key']		= $key;
		$cache_data['object']	= $object;

		if ($GLOBALS['CACHE_HAS']['ob_start']) {
			$cache_pbufferlen = ob_get_length();
			/* If ob_get_length() returns false, output buffering was not on.  turn it on. */
			if (cache_iftype($cache_pbufferlen, FALSE)) {
				ob_start();
			}
		} else {
			$cache_pbufferlen=FALSE;
		}
		cache_debug(__LINE__ .': $cache_absfile -> '.$cache_absfile);
		return 0;
	}

	/* This *MUST* be at the end of a cache() block or else the cache
		will not be stored! */ 
	function endcache($store=TRUE, $send_output = TRUE) {
		global $cache_pbufferlen, $cache_absfile, $cache_data, $cache_variables, $cache_headers, $cache_ob_handler, $cache_output_buffer;
		cache_debug(__LINE__ .': $cache_absfile -> '.$cache_absfile);
		if (!CACHE_ON) {
			cache_debug('Not caching, CACHE_ON is off');
			return 0;
		} /* else */

		if ($GLOBALS[CACHE_HAS]['ob_start']) {
			$content=ob_get_contents();
			if (cache_iftype($cache_pbufferlen,FALSE)) {
				/* Output buffering was off before this, we just need to turn it off again */

				/* JK's fix */
				if ($send_output) {
					ob_end_flush();
					cache_debug(__LINE__.': Content sent. flush()');
				} else {
					ob_end_clean();
					cache_debug(__LINE__.': Content ignored. clean()');
				}
				cache_debug(__LINE__ .': $cache_absfile -> '.$cache_absfile);
			} else {
				/* Output buffering was already on, so get our chunk of data for caching */
				$content=substr($content, $cache_pbufferlen);
			}
		} else {
			$content=$cache_output_buffer;
		}

		if (!$store) {
			$cache_absfile=NULL;
		}

		if ($cache_absfile != NULL) {
			$cache_data['content'] = $content;
			$variables = array();
			foreach ($cache_variables as $vn) {
			//while(list(,$vn)=each($cache_variables)) {
				cache_debug(__LINE__ . ': Found variable: <b>'.$vn.'</b>');
				if (isset($GLOBALS[$vn])) {
					$val=$GLOBALS[$vn];
					cache_debug(__LINE__ . ': Setting variable '.$vn.' to '.$val);
					$variables[$vn]=$val;
				}
			}
			$cache_data['cache_object'] = $cache_absfile;
			$cache_data['variables']	= $variables;
			$cache_data['headers']		= $cache_headers;
			$datas = serialize($cache_data);
			cache_write($cache_absfile, $datas);
		} else {
			cache_debug(__LINE__ .': no variables found');
			cache_debug($cache_variables[0]);
			cache_debug(__LINE__ .': $cache_absfile -> '.$cache_absfile);
		}
		/* casues probs on win32 */
		cache_lock($cache_absfile, FALSE);
		/* */
		cache_reset();

		cache_debug(__LINE__ .': $cache_absfile -> '.$cache_absfile);
		cache_debug(__LINE__. ': <b>Caching is done!</b><br>');
	}

	/* Obtain a lock on the cache storage, this can be stripped out
		and changed to a different handler like a database or
		whatever */
	function cache_lock($file, $open=TRUE) {
		static $fp;

		if ($open) {
			cache_debug('trying to lock '.$file);
			$fp  = @fopen($file, 'r');
			if ($fp) {
				$ret = @flock($fp, LOCK_SH); /* get a shared lock */
			}
		} else {
			cache_debug('trying to unlock '.$file);
			$ret = @flock($fp, LOCK_UN);
			@fclose($fp);
			$fp = NULL;
		}
		return $ret;
	}

	/* This is the function that writes out the cache */
	function cache_write($file, $data) {
		cache_debug(__LINE__.': Writing cache data to file: '.$file);
		
		$fp = fopen($file, 'wb+');
		@flock($fp, LOCK_EX); /* get a shared lock */
		if (!$fp) {
			cache_debug('Failed to open for write out to '.$file);
			return FALSE;
		}
		@fwrite($fp, $data, strlen($data));
		@flock($fp, LOCK_UN); /* get a shared lock */
		fclose($fp);

		return TRUE;
	}

	/* This function reads in the cache, duh */
	function cache_read($file) {
		$fp = @fopen($file, 'r');
		if (!$fp) {
			cache_debug(__LINE__.': Failed opening file '.realpath($file));
			return NULL;
		}
		flock($fp, 1);
		$buff='';
		while (($tmp=fread($fp, 4096))) {
			$buff.=$tmp;
		}
		fclose($fp);
		return $buff;
	}

	/* This function is called automatically by phpCache to create the cache directory structure */
	function cache_create_storage() {
		$failed = 0;
		$failed |= !@mkdir(THIS_CACHE_DIR, CACHE_STORAGE_PERM);
		if (CACHE_USE_STORAGE_HASH) {
			for ($a=0; $a<CACHE_MAX_STORAGE_HASH; $a++) {
				$thedir=THIS_CACHE_DIR . "/$a/";
				$failed|=!@mkdir($thedir, CACHE_STORAGE_PERM);
				for ($b=0; $b<CACHE_MAX_STORAGE_HASH; $b++) {
					$thedir=THIS_CACHE_DIR . "/$a/$b/";
					$failed|=!@mkdir($thedir, CACHE_STORAGE_PERM);
					for ($c=0; $c<CACHE_MAX_STORAGE_HASH; $c++) {
						$thedir=THIS_CACHE_DIR . "/$a/$b/$c/";
						$failed|=!@mkdir($thedir, CACHE_STORAGE_PERM);
					}
				}
			}
		}
		return TRUE;
	}

	/* This function hashes the cache object and places it in a cache dir.  This function also handles the GC probability (note that it is run on only *ONE* dir to save time. */
	function cache_storage($object, $key) {
		$newobject=eregi_replace("[^A-Z,0-9,=]", 'X', $object);
		$newkey=eregi_replace("[^A-Z,0-9,=]", 'X', $key);
		$temp="${newobject}=${newkey}";
		if (strlen($temp)>=CACHE_MAX_FILENAME_LEN) $temp="HUGE." . md5($temp);
		$cacheobject = 'phpCache.' . $temp;
		
		$thedir=THIS_CACHE_DIR . '/';

		if (CACHE_USE_STORAGE_HASH) {
			$chunksize=10;
			$ustr=md5($cacheobject);
			for ($i=0; $i<3; $i++) {
				if ($GLOBALS['CACHE_HAS']['crc32']) {
					$thenum=abs(crc32(substr($ustr,$i,4)))%CACHE_MAX_STORAGE_HASH;
				} else {
					$thenum=substr($ustr, $i, 4);
					$thenum=(ord($thenum[0]) . ord($thenum[1]) . ord($thenum[2]) . ord($thenum[3]))%CACHE_MAX_STORAGE_HASH;
				}
				$thedir.= $thenum . '/';
			}
		}
		if (CACHE_GC>0) {
			$precision=100000;
			$r=(mt_rand()%$precision)/$precision;
			if ($r<=(CACHE_GC/100)) {
				cache_gc($thedir);
			}
		}
		$theloc = $thedir . $cacheobject;

		return $theloc;
	}

	/* Cache garbage collection */
	function cache_gc($dir=NULL, $start=1, $purgeall=FALSE) {
		static $dirs=0, $files=0, $deleted=0, $ignored=0, $faileddelete=0, $empty=0;
		if ($start==1) {
			cache_debug("Running GC on $dir");
			if (!function_exists("getcwd")) {
				$cwd=substr(`pwd`, 0, -1);
			} else {
				$cwd=getcwd();
			}
			$dirs=$files=$deleted=$ignored=$faileddelete=$empty=0;
		}
		if (cache_iftype($dir, NULL)) $dir=THIS_CACHE_DIR;
		$dp=opendir($dir);
		if (!$dp) {
			cache_debug("Error opening $dir for cleanup");
			return FALSE;
		}
		chdir($dir);
		$dirs++;
		while (!cache_iftype(($de=readdir($dp)),FALSE)) {
			if (is_dir($de)) {
				if ($de=='.' || $de=='..') continue;
				cache_gc($de, 0, $purgeall);
				chdir('..');
				continue;
			}

			if (eregi("^phpCache.", $de)) {
				$files++;
				$absfile=$de;
				$cachestuff=cache_read($absfile);
				$thecache=unserialize($cachestuff);
				if (is_array($thecache)) {
					if ($purgeall || ($cdata["cachetime"]!="0" && $thecache["expire"]<=time())) {
						cache_lock($absfile, TRUE);
						if (@unlink($absfile)) {
							$deleted++;
							cache_debug("$dir Deleted $absfile");
						} else {
							$faileddelete++;
							cache_debug("$dir Failed to delete $absfile");
						}
						cache_lock($absfile, FALSE);
					} else {
						cache_debug("$dir $absfile expires in " . ($thecache["expire"]-time()));
					}
				} else {
					cache_debug("$dir $absfile is empty, being processed in another process?");
					$empty++;
				}
			} else {
				$ignored++;
			}
		}
		closedir($dp);
		if ($start==1) {
			$str="$dir GC Processed: $dirs/dirs	$files/files	$deleted/deleted	$ignored/ignored	$faileddelete/faileddelete	$empty/empty";
			cache_debug($str);
			chdir($cwd);
			return $str;
		}
	}

	function cache_iftype($a, $b) {
		if (gettype($a)==gettype($b) && $a==$b) return TRUE;
		return FALSE;
	}

	if (CACHE_ON && !CACHE_STORAGE_CREATED && !@stat(CACHE_STORAGE_CHECKFILE)) {
		cache_debug('Creating cache storage');
		cache_create_storage();
		if (!@touch(CACHE_STORAGE_CHECKFILE)) {
			global $msg;
		
			$msg->printErrors('CACHE_DIR_BAD');
			exit;
		}
	}

	mt_srand(time(NULL));
	cache_reset();

?>