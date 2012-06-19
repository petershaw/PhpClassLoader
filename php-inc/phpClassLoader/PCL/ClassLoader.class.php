<?php

require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'CacheBase.class.php';
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
    public $cache_base;
    public $cache_query;

    /**
     * Set mode of ClassLoader. Possible values:
     * - flatfile: Uses a flat file with an array of paths to classes.
     * - sqlite: Uses a SQLite DB-file and returnes class paths via single 
     *   queries. No need to keep whole array in memory.
     * - [more and planned. @see manual/roadmap.md for more details]
     *
     * @var string $mode
     */
    public $mode;

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
    public $config;

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
     * @param $force_rebuild optional should be set to false, default is false. Only CacheQuery use this flag to reforce a second build
     * @throws Exception on several no-go configurations
     */
    public function __construct($custom_conf_class = null, $custom_conf_dir = null, $force_rebuild = false) {
        // Set the classloader and the classdir static, in case of a rebuild from 
        // CacheBase.
        ClassLoader::$custom_conf_class = $custom_conf_class;
        ClassLoader::$custom_conf_dir = $custom_conf_dir;
        //import config
        require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . '../configuration/PCLConfiguration.class.php';
        if (isset($custom_conf_dir) && isset($custom_conf_class)) {
            $this->config = new PCLConfiguration($custom_conf_class, $custom_conf_dir);
        } else {
            $this->config = new PCLConfiguration ( ); //default in normal operations
        }

        //set mode
        if (($this->config->getSetupParameter('mode') == 'sqlite') || 
            ($this->config->getSetupParameter('mode') == 'flatfile')) {
            $this->mode = $this->config->getSetupParameter('mode');
        } else {
            throw new Exception('Unknown mode (' . $this->config->getSetupParameter('mode') . ') defined in config.');
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
        switch ($this->mode) {
            //flatfile
            case 'flatfile' :
                ClassLoader::$cache_file = 'class_cache.' . str_replace("/", "_", $documentRoot) . '.tmp.php';
                break;
            default :
                // default is sqlite
                ClassLoader::$cache_file = 'class_cache.' . str_replace("/", "_", $documentRoot) . '.tmp.db';
        }

        //decide for temp or custom directory used to save the cache file
        if ($this->config->getSetupParameter('cachefilepath') == 'system_tmp') {
            $this->targetdir = self::getTempFilepath();
        } else {
            $this->targetdir = $this->config->getSetupParameter('cachefilepath');
        }
        if (empty($this->targetdir) || !is_dir($this->targetdir)) {
            throw new Exception('Temp path to store cache file could not be assigned or is not a directory.');
        }

        //chdir to target dir to write cachecontent to
        chdir($this->targetdir);

        // reste cachefile to absolut filename
        ClassLoader::$cache_file = $this->targetdir .ClassLoader::$cache_file;
        
        //instanciate helpers
        $this->cache_base = new CacheBase($this->mode);
        $this->cache_query = CacheQuery::getInstance($this->mode);
        $this->cache_query->setTargetDir($this->targetdir);

        //defines array of dirs to be scanned from config (leave here since it is called by infoscripts)
        $this->defineRootDirs();

        //defines exclude list of dirs from config (leave here since it is called by infoscripts)
        $this->defineExcludeList();

        //create cache
        if (!file_exists(ClassLoader::getCacheFile()) || $force_rebuild == true) {
            //create a new cache base according to mode
            $this->cache_base->create($this->cache_roots_arr, $this->exclude_dirs_arr, $this->mode);
            $this->cache_query = CacheQuery::getInstance($this->mode, true);
        }
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
            ClassLoader::$ClassLoader = new ClassLoader($custom_conf_class, $custom_conf_dir, $force_rebuild);
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
        include_once ($this->cache_query->getIncludepath($classname));
        if(!class_exists($classname)){
            throw Excepton("Can not include classfile for class ". $classname);
        }
    }

    /**
     * Array of dirs to be scanned for cachable files is defined here.
     * 
     */
    public function defineRootDirs() {
        //init
        $this->cache_roots_arr = array();
        if ($this->config->getSetupParameter('rootdirmode') == 'relative') {
            //set dirs relative to $targetdir
            /* if PhpClassLoader_RootDirectory is not set, get a directory above 
             * the filelocation. PhpClassLoader_RootDirectory will set by Phar 
             * handler.
             */
            $mydirarr = explode(DIRECTORY_SEPARATOR, dirname(__FILE__));
            array_pop($mydirarr); array_pop($mydirarr);
            $mydir = implode(DIRECTORY_SEPARATOR, $mydirarr);
            if(defined("PhpClassLoader_RootDirectory")){
                // than use the defined one.
                $mydir = PhpClassLoader_RootDirectory;
            }
            // fix path 
            $seperator = "\\".DIRECTORY_SEPARATOR;
            if(preg_match("/$seperator$/", $mydir) == false){
                $mydir = $mydir . DIRECTORY_SEPARATOR;
            }

            // list or single value
            if (is_array($this->config->getSetupParameter('include'))) {
                foreach ($this->config->getSetupParameter('include') as $d) {
                    $this->cache_roots_arr [] = realpath($mydir . $d);
                }
            } else {
                $this->cache_roots_arr [] = realpath($mydir . $this->config->getSetupParameter('include'));
            }
        } elseif ($this->config->getSetupParameter('rootdirmode') == 'absolute') {
            //list or single value
            if (is_array($this->config->getSetupParameter('include'))) {
                foreach ($this->config->getSetupParameter('include') as $d) {
                    $this->cache_roots_arr [] = $d;
                }
            } else {
                $this->cache_roots_arr [] = realpath($this->config->getSetupParameter('include'));
            }
        } else {
            throw new Exception('Unknown rootdirmode (' . $this->config->getSetupParameter('rootdirmode') . ') set in config.');
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

        $exList = $this->config->getSetupParameter('exclude');
        if (isset($exList)) {
            //list or single value
            if (is_array($exList)) {
                foreach ($exList as $e) {
                    $this->exclude_dirs_arr [] = $e;
                }
            } else {
                $this->exclude_dirs_arr [] = $this->config->getSetupParameter('exclude');
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
        switch ($this->mode) {
            //flatfile
            case 'flatfile' :
                return $this->cache_query->known_classes;
            default :
                return $this->cache_query->DBQueryAll();
        }
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
        $this->cache_base->create($this->cache_roots_arr, $this->exclude_dirs_arr, $this->mode);
        $this->cache_query = CacheQuery::getInstance($this->mode, true);
    }

    /**
     * Returns the age of the cache.
     * @return int minutes 
     */
    public static function getCacheDate() {
        return round(( ( ( (time() - filectime(ClassLoader::$cache_file)) / 60) / 60) / 24), 2);
    }

    /**
     * Returns path systems temporary directory.
     *
     * @param string special_tmp_path optional predefined path
     * @return string path or null
     */
    private static function getTempFilepath() {
        //1st use php internal sys_get_temp_dir()
        if (function_exists('sys_get_temp_dir')) {
            $sys_get_temp_dir = sys_get_temp_dir();
            if (strrchr($sys_get_temp_dir, DIRECTORY_SEPARATOR) == strlen($sys_get_temp_dir)) {
                $sys_get_temp_dir = substr($sys_get_temp_dir, -1);
            }
            return $sys_get_temp_dir;
        }
        //2nd make assumptions, try to get from environment variables
        if (!empty($_ENV ['TMP'])) {
            return realpath($_ENV ['TMP']);
        } elseif (!empty($_ENV ['TMPDIR'])) {
            return realpath($_ENV ['TMPDIR']);
        } elseif (!empty($_ENV ['TEMP'])) {
            return realpath($_ENV ['TEMP']);
        } else { //detect by creating a temporary file
            //use system's temporary directory
            $temp_file = tempnam(md5(uniqid(rand(), true)), '');
            if ($temp_file) {
                $temp_dir = realpath(dirname($temp_file));
                if (strrchr($temp_dir, DIRECTORY_SEPARATOR) == strlen($temp_dir)) {
                    $temp_dir = substr($temp_dir, -1);
                }
                unlink($temp_file);
                return $temp_dir . DIRECTORY_SEPARATOR;
            } else {
                return null;
            }
        }
    }

}

//------------------------------------------------------------------------------
//register ClassLoader::autoload as autoload-handler
spl_autoload_register(array(new ClassLoader ( ), 'autoload'));


