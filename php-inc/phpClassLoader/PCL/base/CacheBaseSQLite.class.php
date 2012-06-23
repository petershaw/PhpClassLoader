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

    /**
     * SQLite static connection handle
     *
     * @var ressource
     */
    private static $sqlite_connect;

    public function __construct($cache_roots_arr, $exclude_dirs_arr) {
        parent::__construct('sqlite');
        $this->cache_roots_arr = $cache_roots_arr;
        $this->exclude_dirs_arr = $exclude_dirs_arr;
        if (file_exists(ClassLoader::getCacheFile())) {
            $this->DBConnect(ClassLoader::getCacheFile());
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
        if(file_exists(ClassLoader::getCacheFile())){
            //unlink(ClassLoader::getCacheFile());
        }
        $this->createCache();
        $this->writeCache();
    }

    public function writeCache() {
        if (!isset(CacheBaseSQLite::$sqlite_connect)) {
            $this->DBConnect(ClassLoader::getCacheFile());
        }
        CacheBaseSQLite::$sqlite_connect->exec('CREATE TABLE classcache ( classname varchar(60) PRIMARY KEY, path varchar(200) );')
                or new Exception('Can not create the Classcache-Table');
        foreach ($this->known_classes as $classname => $path) {
            CacheBaseSQLite::$sqlite_connect->exec("INSERT INTO classcache VALUES ('$classname', '$path')");
        }
        if (file_exists(ClassLoader::getCacheFile())) {
            chmod(ClassLoader::getCacheFile(), 0777); //make it deletable and executable for other users
            return true;
        }
        return false;
    }

    /**
     * Connect to or create no existing SQLite-DB file
     * 
     * @param string $dbfile filename
     * @return db handle of connection
     */
    private function DBConnect($dbfile) {
        $sqliteerror = null;
        // CacheBase::$sqlite_connect = sqlite_open ( ClassLoader::getCacheFile (), 0777, $sqliteerror ) ;
        //if (PHP_MAJOR_VERSION == 5 && PHP_MINOR_VERSION < 3) {
        CacheBaseSQLite::$sqlite_connect = new PDO('sqlite:' . ClassLoader::getCacheFile());
        //} else {
        //    CacheBase::$sqlite_connect = new SQLite3(ClassLoader::getCacheFile());
        //}
        if (!empty($sqliteerror) || CacheBaseSQLite::$sqlite_connect == null) {
            throw new Exception('DB-file could not be opened: ' . $dbfile . '. SQLite-error:' . $sqliteerror);
        }
    }

    public function query($classname) {

        $result_arr = CacheBaseSQLite::$sqlite_connect->query("SELECT path FROM classcache WHERE classname='" . $classname . "'");
        foreach ($result_arr as $r) {
            return $r['path'];
        }
    }

    public function getKnownClasses() {
        $known_classes = array();
        $result_arr = CacheBaseSQLite::$sqlite_connect->query("SELECT * FROM classcache");
        foreach ($result_arr as $r) {
            $known_classes [$r['classname']] = $r['path'];
        }
        return $known_classes;
    }

}

?>