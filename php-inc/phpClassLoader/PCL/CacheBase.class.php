<?php

/**
 * CacheBase<br />
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
class CacheBase {

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

    /**
     * Mode (sqlite, flatfile, ...)
     *
     * @var string
     */
    private $mode;

    /**
     * SQLite static connection handle
     *
     * @var ressource
     */
    public static $sqlite_connect;

    /**
     * Import $mode
     *
     * @param unknown_type $mode
     */
    function __construct($mode = '') {
        $this->mode = $mode;
    }

    /**
     * Walk through project directories recursively to build ClassCache.
     *
     * @param string $path
     * @param array $excluded_dir_arr masks with directories to skip
     */
    public function buildCache($path, $excluded_dir_arr) {

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
    private function parseFile($fname) {

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

    /**
     * Writes cache file to filesystem in a flatfile.
     *
     * @return boolean result
     */
    public function writeFlatfile() {
        $printed_array = var_export($this->known_classes, true);
        $file_content = <<<CONT
<?php class getCache { public static function getCacheArray() { return $printed_array; }} ?>
CONT;
        $result = file_put_contents(ClassLoader::getCacheFile(), $file_content);
        if ($result !== false) {
            chmod(ClassLoader::getCacheFile(), 0777); //make it deletable and executable for other users
            return true;
        } else {
            return false;
        }
    }

    /**
     * Connect to or create no existing SQLite-DB file
     * 
     * @param string $dbfile filename
     * @return db handle of connection
     */
    public function DBConnect($dbfile) {
        if (!isset(CacheBase::$sqlite_connect)) {
            $sqliteerror = null;
            // CacheBase::$sqlite_connect = sqlite_open ( ClassLoader::getCacheFile (), 0777, $sqliteerror ) ;
            //if (PHP_MAJOR_VERSION == 5 && PHP_MINOR_VERSION < 3) {
            echo('sqlite:' . ClassLoader::getCacheFile() . "\n");
            CacheBase::$sqlite_connect = new PDO('sqlite:' . ClassLoader::getCacheFile());
            //} else {
            //    CacheBase::$sqlite_connect = new SQLite3(ClassLoader::getCacheFile());
            //}
            if (!empty($sqliteerror) || CacheBase::$sqlite_connect == null) {
                throw new Exception('DB-file could not be opened: ' . $dbfile . '. SQLite-error:' . $sqliteerror);
            }
        }
    }

    /**
     * Writes cache to filesystem into sqlite-db
     * 
     * @return void
     */
    public function writeSQLiteDB() {
        if (!isset(CacheBase::$sqlite_connect)) {
            $this->DBConnect(ClassLoader::getCacheFile());
        }
        CacheBase::$sqlite_connect->exec('CREATE TABLE classcache ( classname varchar(60) PRIMARY KEY, path varchar(200) );')
                or new Exception('Can not create the Classcache-Table');
        foreach ($this->known_classes as $classname => $path) {
            CacheBase::$sqlite_connect->exec("INSERT INTO classcache VALUES ('$classname', '$path')");
        }
        if(file_exists(ClassLoader::getCacheFile())){ 
	chmod(ClassLoader::getCacheFile(), 0777); //make it deletable and executable for other users
	}
    }

    /**
     * Handle creation of base according to mode
     *
     * @param array $cache_roots_arr
     * @param array $exclude_dirs_arr
     * @return void
     */
    public function create($cache_roots_arr, $exclude_dirs_arr) {
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
        switch ($this->mode) {
            //flatfile
            case 'flatfile' :
                $this->writeFlatfile();
                break;
            default :
                // default is sqlite
                $this->writeSQLiteDB();
        }
    }

}

?>
