<?php

require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'base' . DIRECTORY_SEPARATOR . 'CacheBaseFlatfile.class.php';
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'base' . DIRECTORY_SEPARATOR . 'CacheBaseSQLite.class.php';
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'CacheQuery.class.php';

/**
 * ClassLoader<br />
 * ======================<br />
 * The ClassLoader includes files on demand when a constructor is called.<br />
 * <br />
 * Include this file to activate the ClassLoader and register the spl_autoloader of it
 * for automatic 'lazy loading' of your classes and interfaces.
 * <br />
 * To rebuild the cache, delete the $cache_file. This is always necessary when new class or
 * interface declarations are added to the system. The $cache_file is saved to the systems
 * temp directory. To delete it you can unlink it manually or call the helper script
 * autoloadcache/empty_cache.php from the command line. You must have file privileges to do
 * that.
 * <br />
 * Another helper script autoloadcache/cache_info.php displays the current contents and mode
 * of the $cache_file. 
 * <br />
 * Only .php files are scanned.
 * <br />
 * You can define... <br />
 * <ul>
 * <li>- exclude-masks for directories to be skipped by the scanner (=method: defineRootDirs()).</li>
 * <li>- a list of nodes in your directory structure where the scanner starts revursive parsing (=method: defineExcludeList).</li>
 * <li>- a mode in which ClassLoader will work:
 * <ul>
 * <li>  - sqlite: ClassLoader queries each requested file from a prebuild SQLite-database (on big projects).</li>
 * <li>  - flatfile: ClassLoader loads a full prebuilt array from a file (projects <100 classes in codebase) </li>
 * </ul></il>
 * </ul>
 *
 * @package		PCL
 * @subpackage          ClassLoader
 * 
 * @static
 *
 * @link		https://github.com/petershaw/PhpClassLoader/wiki/PhpClassLoader
 * @author		@peter_shaw
 *
 * @version		1.0.0
 * @since               1.0.0
 *
 */
class ClassLoader {

    /**
     * ClassLoader Singelton
     *
     * @var ClassLoader 
     */
    public static $ClassLoader;

    /**
     * Filename of target file, the cached array is saved to and retrieved 
     * from. Declared public for testing. As default the cachefile is 
     * written to the same directory this class ClassLoader is situated. 
     * 
     * @var string cache_file
     */
    public static $cache_file;

    /**
     * The first-time initialisation class. (for conficloader used on rebuild)
     * 
     * @var string  
     */
    public static $custom_conf_class;

    /**
     * The first-time initialisation directory. (for conficloader used on 
     * rebuild)
     * 
     * @var string 
     */
    public static $custom_conf_dir;

    /**
     * Array holding list of directories from which the cache parser should 
     * start scanning. 
     * Declared public for testing.
     *
     * @var array cache_roots_arr
     */
    public $cache_roots_arr = array();

    /**
     * Array holding list of directories to be skippen by cache parser.
     *
     * @var unknown_type
     */
    public $exclude_dirs_arr = array();

    /**
     * Array of known classes and their filepaths
     * <code>
     *   array (
     * 	   ['Class123'] => '/var/www/libs/class123.inc.php',
     *     ['Class456'] => '/var/www/php-inc/Class_456.php'
     *   )
     * </code>
     *
     * @var array known_classes
     */
    public $known_classes;

    /**
     * Subclass instances
     */
    private static $cache_base;
    private static $cache_query;

    /**
     * Set mode of ClassLoader. Possible values:
     * - flatfile: Uses a flat file with an array of paths to classes.
     * - sqlite: Uses a SQLite DB-file and returnes class paths via single 
     *   queries. No need to keep whole array in memory.
     * - [more and planned. @see manual/roadmap.md for more details]
     *
     * @var string $mode
     */
    public static $mode;

    /**
     * Object holding autoloadcache configuration from xml file.
     * Layout:
     * <code>
     *   <?xml version="1.0" encoding="UTF-8"?>
     *   <configdata>
     *     <!-- Element names suffixed _list can contain more than one elements seperated by comma -->
     *     <autoloadcache>
     *     	<mode>flatfile</mode>
     *     	<!-- 2 posibilities: absolute path or 'system_tmp' to use native system tmp-dir -->
     *     	<cachefilepath>system_tmp</cachefilepath>
     * 		<rootdirs_list>/../,/../../customer/aok/</rootdirs_list>
     * 		<excludedirs_list>/.svn,/test'</excludedirs_list>
     *     </autoloadcache>   
     *   </configdata>
     * </code>
     *
     * @var ConfManager
     */
    public static $config;

    /**
     * Directory to save cache file to
     *
     * @var string path to directory
     */
    public $targetdir;

    /**
     * Generates ClassLoader if it is not yet available. To Regenerate 
     * Classcache, delete generated file! The parameter in the constructor 
     * can only be used if spl_autoload_register() does not create the
     * object of this class. See Tests for how to do that and set custom 
     * config-XML.
     *
     * @param string $custom_conf_class optional class of cachefile-config. Default: uses system ClassLoader
     * @param string $custom_conf_dir optional directory of cachefile-config. 
     * @throws Exception on several no-go configurations
     */
    private function __construct($custom_conf_class = null, $custom_conf_dir = null) {
        // Set the classloader and the classdir static, in case of a rebuild from 
        // CacheBase.
        ClassLoader::$custom_conf_class = $custom_conf_class;
        ClassLoader::$custom_conf_dir = $custom_conf_dir;
        //import config
        require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . '../configuration/PCLConfiguration.class.php';
        if (isset($custom_conf_dir) && isset($custom_conf_class)) {
            self::$config = new PCLConfiguration($custom_conf_class, $custom_conf_dir);
        } else {
            self::$config = new PCLConfiguration ( ); //default in normal operations
        }

        //set mode
        if ((self::$config->getParameter('mode') == 'sqlite') ||
                (self::$config->getParameter('mode') == 'flatfile')) {
            self::$mode = self::$config->getParameter('mode');
        } else {
            throw new Exception('Unknown mode (' . self::$config->getParameter('mode') . ') defined in config.');
        }

        //set filename for cachefile
        // on Window System we have Pathnames like "C:\temp" => remove it
        $documentRoot = str_replace(":", "", $_SERVER['DOCUMENT_ROOT']);
        if (strlen($documentRoot) == 0) {
            // if docroot does not contains a name to build up a classpath,
            // use the username instead.
            // The Classpath is importend to seperate different instances 
            // on the same server that are driven by the same php-inc
            // or different inc's for projects. 
            $documentRoot = get_current_user();
        }
        switch (self::$mode) {
            //flatfile
            case 'flatfile' :
                ClassLoader::$cache_file = 'class_cache.' . str_replace("/", "_", $documentRoot) . '.tmp.php';
                break;
            case 'sqlite' :
                ClassLoader::$cache_file = 'class_cache.' . str_replace("/", "_", $documentRoot) . '.tmp.db';
                break;
            default :
                throw new Exception("Unknown mode '" . self::$mode . "' in " . __CLASS__);
        }

        //decide for temp or custom directory used to save the cache file
        if (self::$config->getParameter('cachefilepath') == 'system_tmp') {
            $this->targetdir = File::getTemporaryDirectory();
        } else {
            $this->targetdir = self::$config->getParameter('cachefilepath');
        }
        if (empty($this->targetdir) || !is_dir($this->targetdir)) {
            throw new Exception('Temp path to store cache file could not be assigned or is not a directory.');
        }

        //chdir to target dir to write cachecontent to
        chdir($this->targetdir);

        // reste cachefile to absolut filename
        ClassLoader::$cache_file = $this->targetdir . ClassLoader::$cache_file;

        //defines array of dirs to be scanned from config (leave here since it is called by infoscripts)
        $this->defineRootDirs();

        //defines exclude list of dirs from config (leave here since it is called by infoscripts)
        $this->defineExcludeList();

        //instanciate helpers
        if (self::$mode == 'flatfile') {
            ClassLoader::$cache_base = new CacheBaseFlatfile($this->cache_roots_arr, $this->exclude_dirs_arr);
        } elseif (self::$mode == 'sqlite') {
            ClassLoader::$cache_base = new CacheBaseSQLite($this->cache_roots_arr, $this->exclude_dirs_arr);
        } else {
            throw new Exception("Unknown mode '" . self::$mode . "' in " . __CLASS__);
        }
        ClassLoader::$cache_query = new CacheQuery(ClassLoader::$cache_base);
        ClassLoader::$ClassLoader = $this;
    }

    /**
     * Use the ClassLoader as a singleton is the preffered method to get a 
     * ClassLoader Instance.
     * 
     * @param @optional string $custom_conf_class
     * @param @optional string $custom_conf_dir
     * @param @optional bolean $force_rebuild
     * @return ClassLoader 
     * @static
     */
    public static function getInstance($custom_conf_class = null, $custom_conf_dir = null, $force_rebuild = false) {
        if (is_null(ClassLoader::$ClassLoader) || $force_rebuild == true) {
            if ($force_rebuild == false) {
                if ((isset($custom_conf_class) == false && isset(ClassLoader::$custom_conf_class) == true)) {
                    $custom_conf_class = ClassLoader::$custom_conf_class;
                }
                if ((isset($custom_conf_dir) == false && isset(ClassLoader::$custom_conf_dir) == true)) {
                    $custom_conf_dir = ClassLoader::$custom_conf_dir;
                }
            }
            ClassLoader::$ClassLoader = new ClassLoader($custom_conf_class, $custom_conf_dir);
        }

        //create cache
        if (!file_exists(ClassLoader::getCacheFile()) || $force_rebuild == true) {
            //create a new cache base according to mode
            ClassLoader::$cache_base->createCache();
            ClassLoader::$cache_base->rebuildCache();
            ClassLoader::$cache_query = new CacheQuery(ClassLoader::$cache_base);
        }
        return ClassLoader::$ClassLoader;
    }

    /**
     * Include classfile from ClassLoader, registered SPL autoload function
     *
     * @param string $classname
     * @return void
     */
    public function autoload($classname) {
        //include file
        include_once (ClassLoader::$cache_query->getIncludepath($classname));
        if (!class_exists($classname)) {
            throw Excepton("Can not include classfile for class " . $classname);
        }
    }

    /**
     * Array of dirs to be scanned for cachable files is defined here.
     * 
     */
    public function defineRootDirs() {
        //init
        $this->cache_roots_arr = array();
        if (self::$config->getParameter('rootdirmode') == 'relative') {
            //set dirs relative to $targetdir
            /* if PhpClassLoader_RootDirectory is not set, get a directory above 
             * the filelocation. PhpClassLoader_RootDirectory will set by Phar 
             * handler.
             */
            $mydirarr = explode(DIRECTORY_SEPARATOR, dirname(__FILE__));
            array_pop($mydirarr);
            array_pop($mydirarr);
            $mydir = implode(DIRECTORY_SEPARATOR, $mydirarr);
            if (defined("PhpClassLoader_RootDirectory")) {
                // than use the defined one.
                $mydir = PhpClassLoader_RootDirectory;
            }
            // fix path 
            $seperator = "\\" . DIRECTORY_SEPARATOR;
            if (preg_match("/$seperator$/", $mydir) == false) {
                $mydir = $mydir . DIRECTORY_SEPARATOR;
            }

            // list or single value
            if (is_array(self::$config->getParameter('include'))) {
                foreach (self::$config->getParameter('include') as $d) {
                    $this->cache_roots_arr [] = realpath($mydir . $d);
                }
            } else {
                $this->cache_roots_arr [] = realpath($mydir . self::$config->getParameter('include'));
            }
        } elseif (self::$config->getParameter('rootdirmode') == 'absolute') {
            //list or single value
            if (is_array(self::$config->getParameter('include'))) {
                foreach (self::$config->getParameter('include') as $d) {
                    $this->cache_roots_arr [] = $d;
                }
            } else {
                $this->cache_roots_arr [] = realpath(self::$config->getParameter('include'));
            }
        } else {
            throw new Exception('Unknown rootdirmode (' . self::$config->getParameter('rootdirmode') . ') set in config.');
        }
        $returnArray = array();
        foreach ($this->cache_roots_arr as $dir) {
            if (strlen($dir) > 0) {
                array_push($returnArray, $dir);
            }
        }
        return $returnArray;
    }

    /**
     * Array of dirnames to be skippen by the parser (/.svn, /test etc).
     * Tests are done via strpos().
     */
    public function defineExcludeList() {
        //init
        $this->exclude_dirs_arr = array();

        $exList = self::$config->getParameter('exclude');
        if (isset($exList)) {
            //list or single value
            if (is_array($exList)) {
                foreach ($exList as $e) {
                    $this->exclude_dirs_arr [] = $e;
                }
            } else {
                $this->exclude_dirs_arr [] = self::$config->getParameter('exclude');
            }
        }
        return $this->exclude_dirs_arr;
    }

    /**
     * Getter for known_classes according to mode
     *
     * @return array of all known_classes
     */
    public function getAllKnownClasses() {
        return ClassLoader::$cache_base->getKnownClasses();
    }

    /**
     * Static method to return the cache file name
     * 
     * @return string $cache_file
     */
    public static function getCacheFile() {
        return ClassLoader::$cache_file;
    }

    /**
     * Clears the cache 
     * @tutorial Attention: do not use this in a reqular code. The rebuild
     * of a cache can be take a lot of time.
     * 
     */
    public static function deleteCache() {
        if (file_exists(ClassLoader::$cache_file)) {
            unlink(ClassLoader::$cache_file);
        }
    }

    /**
     * Clears the cache 
     * @tutorial Attention: do not use this in a reqular code. The rebuild
     * of a cache can be take a lot of time.
     * 
     */
    public function clear() {
        if (file_exists(ClassLoader::$cache_file)) {
            unlink(ClassLoader::$cache_file);
        }
        ClassLoader::$cache_base->createCache();
        ClassLoader::$cache_base->rebuildCache();
        ClassLoader::$cache_query = new CacheQuery(ClassLoader::$cache_base);
    }

    /**
     * Returns the age of the cache. in days. 
     * 
     * @return int days
     */
    public static function getCacheDate() {
        return round(( ( ( (time() - filectime(ClassLoader::$cache_file)) / 60) / 60) / 24), 2);
    }

}

//------------------------------------------------------------------------------
//register ClassLoader::autoload as autoload-handler
spl_autoload_register(array(ClassLoader::getInstance(ClassLoader::$custom_conf_class, ClassLoader::$custom_conf_dir, false), 'autoload'));


