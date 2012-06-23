<?php

require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'CacheBase.interface.php';

/**
 * Abstract CacheBase<br />
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
abstract class AbstractCacheBase implements CacheBaseInterface {

    /**
     * Array of known classes
     *
     * @var array $known_classes
     */
    public $known_classes;

    /**
     * This counter should make sure rebuild of class cache is done only once if a class could not be found in existing cache.
     * This makes also sure that a new cache file is generated via sqlite-connect in sqlite-mode.
     *
     * @var integer
     */
    public $build_counter = 0;
    protected $cache_roots_arr;
    protected $exclude_dirs_arr;

    /**
     * Mode (sqlite, flatfile, ...)
     *
     * @var string
     */
    private $mode;

    /**
     * Import $mode
     *
     * @param unknown_type $mode
     */
    function __construct($mode = null) {
        $this->mode = $mode;
    }

    public function getMode() {
        return $this->mode;
    }

    /**
     * Walk through project directories recursively to build ClassCache.
     *
     * @param string $path
     * @param array $excluded_dir_arr masks with directories to skip
     */
    public final function buildCache($path, $excluded_dir_arr) {
        $dir = new DirectoryIterator($path);
        foreach ($dir as $item) {
            if ($item->isDot()) {
                continue;
            }
            if ($item->isDir()) {
                //respect exclude list
                if (count($excluded_dir_arr) > 0) {
                    foreach ($excluded_dir_arr as $e_dir) {
                        if (strpos($item->getPathname(), $e_dir) !== false) {
                            continue 2;
                        }
                    }
                }
                $this->buildCache($item->getPathname(), $excluded_dir_arr);
                continue;
            }
            if ($item->isFile() && strpos($item->getPathname(), '.php')) {
                $this->parseFile($item->getPathname());
            }
        }
    }

    /**
     * Parse tokens of a file and collect class and interface declarations in known_classes
     *
     * @param string $fname filepath of file to be parsed
     * @return void
     */
    private final function parseFile($fname) {
        $tokens = token_get_all(file_get_contents($fname));
        $is_relevant = false;
        foreach ($tokens as $bucket) {
            if (!is_array($bucket)) {
                continue;
            }
            //only collect interface and class declarations
            if ($bucket [0] == T_INTERFACE || $bucket [0] == T_CLASS) { //tokens #353 or #352
                $is_relevant = true;
                continue;
            }
            //extract and save interfact-/class-name and its filepath
            if ($is_relevant && ($bucket [0] == T_STRING)) { //token #307
                $classname = $bucket [1];
                $this->known_classes [$classname] = $fname;
                $is_relevant = false;
            }
        }
    }

}

?>
