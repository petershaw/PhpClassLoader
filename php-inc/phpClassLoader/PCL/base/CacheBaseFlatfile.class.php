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
class CacheBaseFlatfile extends AbstractCacheBase {

    public function __construct($cache_roots_arr, $exclude_dirs_arr) {
        parent::__construct('flatfile');
        $this->cache_roots_arr = $cache_roots_arr;
        $this->exclude_dirs_arr = $exclude_dirs_arr;
        if (file_exists(ClassLoader::getCacheFile())) {
            require_once(ClassLoader::getCacheFile());
            $this->known_classes = getCache::getCacheArray();
        }
    }

    /**
     * Creates the cachebase
     *
     * @param array $cache_roots_arr
     * @param array $exclude_dirs_arr
     * @return void
     */
    public function createCache() {
        //build new cache and save it to the filesystem
        if (count($this->cache_roots_arr) > 0) {
            foreach ($this->cache_roots_arr as $path) {
                if (strlen($path) > 0) {
                    $this->buildCache($path, $this->exclude_dirs_arr);
                }
            }
        } else {
            throw new Exception('No directories to be scanned have been defined in property "cache_roots_arr".');
        }
        $this->writeCache();
    }

    public function rebuildCache() {
        ;
    }
    
    public function writeCache() {
        $printed_array = var_export($this->known_classes, true);
        $file_content = <<<CONT
<?php class getCache { public static function getCacheArray() { return $printed_array; }} ?>
CONT;
        $result = file_put_contents(ClassLoader::getCacheFile(), $file_content);
        if ($result !== false) {
            chmod(ClassLoader::getCacheFile(), 0777); //make it deletable and executable for other users
            return true;
        }
        return false;
    }
    
    public function query($classname){
        
        if( isset($this->known_classes) && array_key_exists($classname, $this->known_classes) ){
            return $this->known_classes[$classname];
        }
    }
    
    public function getKnownClasses(){
        return $this->known_classes;
    }

}

?>
