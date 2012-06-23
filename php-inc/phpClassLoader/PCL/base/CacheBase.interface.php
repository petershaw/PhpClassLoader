<?php

/**
 * CacheBase Interface<br />
 * ======================<br />
 * This class describes the methods of a Cache<br />
 * Every Cache needs a base and a query. The base genereates the filebase.
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
interface CacheBaseInterface {

    /**
     * Handle creation of base according to mode
     *
     * @param array $cache_roots_arr
     * @param array $exclude_dirs_arr
     * @return void
     */
    public function createCache();

    /**
     * Walk through project directories recursively to build ClassCache.
     *
     * @param string $path
     * @param array $excluded_dir_arr masks with directories to skip
     */
    public function buildCache($path, $excluded_dir_arr);

    /**
     * Rebuild the classcache
     */
    public function rebuildCache();

    /**
     * Writes cache file to filesystem in a flatfile.
     *
     * @return boolean result
     */
    public function writeCache();

    /**
     * queries a classname. returns a filepath.
     *
     * @return string result
     */
    public function query($classname);

    /**
     * returns a list of all known classes.
     *
     * @return string result
     */
    public function getKnownClasses();
}

?>
