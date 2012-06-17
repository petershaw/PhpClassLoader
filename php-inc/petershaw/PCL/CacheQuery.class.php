<?php

require_once dirname(__FILE__). DIRECTORY_SEPARATOR .'ClassLoader.class.php';

/**
 * CacheQuery
 * ======================
 * The CacheQuery is used by the Classcache to get a filepath for a requested 
 * class by its construtor
 * 
 * @package		PCL
 * @subpackage          ClassLoader
 * 
 * @static
 *
 * @link		%projecturl%/%articles%/PHPCarpetRoot/ClassLoader
 * @author		@peter_shaw and colleagues
 *
 * @version		1.0.0
 * @since               1.0.0
 *
 */
class CacheQuery {

    /**
     * Single instance of class CacheQuery
     *
     * @var object CacheQuery
     * @static
     */
    private static $singleton;

    /**
     * Array of known classes
     *
     * @var array $known_classes
     */
    public $known_classes;

    /**
     * Mode (sqlite, flatfile, ...)
     *
     * @var string
     */
    private $mode;

    /**
     * Instance of CacheBase
     *
     * @var object CacheBase
     */
    private $cacheBase;

    /**
     * Actual class required but not found
     *
     * @var string
     */
    public $searched_classname;

    /**
     * Directory to save cache file to
     *
     * @var string path to directory
     */
    public $targetdir;

    /**
     * Set force_rebuild to rebuild the cache on the next touch.
     * 
     * @var boolean 
     */
    private static $force_rebuild;

    /**
     * Load the cachefile contents
     * 
     * @param string $mode
     */
    private function __construct($mode = '') {
        $this->mode = $mode;
        $this->cacheBase = new CacheBase ( );
        if (file_exists(ClassLoader::getCacheFile())) {
            //handle according to mode set in ClassLoader, load it
            switch ($this->mode) {
                //flatfile
                case 'flatfile' :
                    require_once (ClassLoader::getCacheFile());
                    if (CacheQuery::$force_rebuild == true) {
                        // Overload ! 
                        $this->known_classes = getCache::getCacheArray();
                    } // evnt. else... weil $this->known_classes bereits richtig gefüllt ist? 
                    $this->known_classes = getCache::getCacheArray(); //this class is written as static to the cachefile itself!
                    break;
                //SQLite make connection
                default :
                    $this->cacheBase->DBConnect(ClassLoader::getCacheFile());
            }
        }
    }

    /**
     * @todo needs documentation
     * @param type $dir 
     */
    function setTargetDir($dir) {
        $this->targetdir = $dir;
    }

    /**
     * Query flattfile-base for path
     *
     * @param string $classname
     * @return string $classpath
     */
    private function FlatfileQuery($classname) {
        if (isset($this->known_classes [$classname])) {
            return $this->known_classes [$classname];
        }
    }

    /**
     * Query SQLite-DB-base for one path
     *
     * @param string $classname
     * @return string $classpath
     */
    private function DBQueryOne($classname) {
        if (!isset(CacheBase::$sqlite_connect)) {
            $this->cacheBase->DBConnect(ClassLoader::getCacheFile());
        }
        $res = sqlite_query(CacheBase::$sqlite_connect, "SELECT path FROM ClassLoader WHERE classname='$classname'");
        $classpath = sqlite_fetch_single($res);
        return $classpath;
    }

    /**
     * Returns all classes that are handled by the classlaoder.
     *
     * @param void
     * @return array $known_classes
     */
    public function DBQueryAll() {
        if (!isset(CacheBase::$sqlite_connect)) {
            $this->cacheBase->DBConnect(ClassLoader::getCacheFile());
        }
        $known_classes = array();
        $result_arr = CacheBase::$sqlite_connect->query("SELECT * FROM classcache");
        foreach ($result_arr as $r) {
            $known_classes [$r ['classname']] = $r ['path'];
        }
        return $known_classes;
    }

    /**
     * Singleton of CacheQuery
     *
     * @return object CacheQuery
     * @static
     */
    public static function getInstance($mode = '', $force_rebuild = false) {
        CacheQuery::$force_rebuild = $force_rebuild;
        if (CacheQuery::$singleton == null || $force_rebuild == true) {
            CacheQuery::$singleton = new CacheQuery($mode);
        }
        return CacheQuery::$singleton;
    }

    /**
     * Return the path of a required class to autoload it
     *
     * @param string $classname
     * @return string path to include from base (flatfile or SQLite-DB)
     * @throws Exception if a class is not found.
     */
    public function getIncludepath($classname) {
        if ($this->mode == 'flatfile') {
            //flatfile
            $classpath = $this->FlatfileQuery($classname);
        } else {
            //SQLite
            $classpath = $this->DBQueryOne($classname);
        }
        //try one rebuild
        if (empty($classpath) && $this->cacheBase->build_counter == 0) {
            //save classname in singleton, it would otherwise be lost from this point
            $this->searched_classname = $classname;
            //log this event, only expose logger if it has already been loaded. This is not the case if no old index-file is yet existing.
            if (class_exists('Logger')) {
                Logger::log(new Configuration(dirname(__FILE__), "ClassLoader"), LogLevel::$LOG_LEVEL_INFO, 'Rebuild of classcache triggered caused by missing class "' . $this->searched_classname . '".');
            } else {
                // do not warn the user on frist rebuild.
                //	echo('<div class="error"><p class="info">'.'Rebuild of classcache triggered caused by missing class "'.$this->searched_classname.'".'.'</p></div>');
            }
            //trigger real error if rebuilding did not help
            if ($this->cacheBase->build_counter > 0) {
                if (class_exists('Logger')) {
                    Logger::log(new Configuration(dirname(__FILE__), "ClassLoader"), LogLevel::$LOG_LEVEL_ERROR, 'Despite rebuild of classcache the required class "' . $this->searched_classname . '" could not be found.');
                } else {
                    echo('<div class="error"><p class="error">' . 'Rebuild of classcache triggered caused by missing class "' . $this->searched_classname . '".' . '</p></div>');
                }
            }
            //recal build-process once
            ClassLoader::$ClassLoader = ClassLoader::getInstance(ClassLoader::$custom_conf_class, ClassLoader::$custom_conf_dir, true);
            $this->cacheBase->build_counter++;
            //read result from array in memory from rebuild
            if (isset(ClassLoader::$ClassLoader->cache_base->known_classes[$this->searched_classname])) {
                $classpath = ClassLoader::$ClassLoader->cache_base->known_classes[$this->searched_classname];
            }
        }
        //handle case when no path can be returned
        if (!isset($classpath)) {
            eval("class $classname{};"); //hack to make autoload throw a regular exception
            throw new Exception('Could not find required class ("' . $classname . '") in ClassLoader.' . "\n" . '[1] If new classes have been added to the system, ClassLoader must be rebuilt! Please delete the cache file ' . $this->targetdir . '/' . ClassLoader::getCacheFile() . ' to rebuild the cache and reload.' . "\n" . '[2] The required class lies in an excluded directory. Check ClassLoader::defineExcludeList() and rebuild cache on any changes.' . "\n" . 'Called from:\n');
            die('Execution stopped.');
        }
        return $classpath;
    }

}

?>