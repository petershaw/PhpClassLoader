<?php

require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'AbstractCacheBase.class.php';

/**
 * CacheBase for Flatfiles<br />
 * ======================<br />
 * This class represents the Cache and provide methods to modify it.<br />
 * 
 * @package		PCL
 * @subpackage          ClassLoader
 * 
 *
 * @link		https://github.com/petershaw/PhpClassLoader/wiki/PhpClassLoader
 * @author		@peter_shaw
 *
 * @version		1.0.0
 * @since               1.0.0
 *
 */
class CacheBaseSQLite extends AbstractCacheBase {

    public function __construct() {
        parent::__construct('sqlite');
    }
    
    /**
     * Creates the cachebase
     *
     * @param array $cache_roots_arr
     * @param array $exclude_dirs_arr
     * @return void
     */
    public function createCache($cache_roots_arr, $exclude_dirs_arr) {
        //build new cache and save it to the filesystem
        if (count($cache_roots_arr) > 0) {
            foreach ($cache_roots_arr as $path) {
                if (strlen($path) > 0) {
                    $this->buildCache($path, $exclude_dirs_arr);
                }
            }
        } else {
            throw new Exception('No directories to be scanned have been defined in property "cache_roots_arr".');
        }
        $this->writeSQLiteDB();
    }

    public function writeCache() {
        
    }

}

?>
