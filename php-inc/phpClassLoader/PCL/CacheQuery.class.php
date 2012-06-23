<?php

require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'ClassLoader.class.php';
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'SimpleLogger.class.php';

/**
 * CacheQuery<br />
 * ======================<br />
 * The CacheQuery is used by the Classcache to get a filepath for a requested 
 * class by its construtor
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
class CacheQuery {

    /**
     * Array of known classes
     *
     * @var array $known_classes
     */
    public $known_classes;

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
    private $logger;

    /**
     * Load the cachefile contents
     * 
     * @param string $mode
     */
    public function __construct(CacheBaseInterface $cachebase) {
        $this->cacheBase = $cachebase;
        $this->logger = new SimpleLogger();
    }

    /**
     * Return the path of a required class to autoload it
     *
     * @param string $classname
     * @return string path to include from base (flatfile or SQLite-DB)
     * @throws Exception if a class is not found.
     */
    public function getIncludepath($classname) {
        $classpath = null;
        $classpath = $this->cacheBase->query($classname);

        //try one rebuild
        if (empty($classpath) && $this->cacheBase->build_counter == 0) {
            //save classname in singleton, it would otherwise be lost from this point
            $this->searched_classname = $classname;
            $this->logger->log(SimpleLogger::INFO, 'Rebuild of classcache triggered caused by missing class "' . $this->searched_classname . '".');
            //trigger real error if rebuilding did not help
            if ($this->cacheBase->build_counter > 0) {
                $this->logger->log(SimpleLogger::ERROR, 'Despite rebuild of classcache (' . $this->mode . ') the required class "' . $this->searched_classname . '" could not be found.');
            }
            //recal build-process once
            ClassLoader::$ClassLoader = ClassLoader::getInstance(ClassLoader::$custom_conf_class, ClassLoader::$custom_conf_dir, true);
            $this->cacheBase->build_counter++;
            //read result from array in memory from rebuild
            /* if (isset(ClassLoader::$ClassLoader->cache_base->known_classes[$this->searched_classname])) {
              $classpath = ClassLoader::$ClassLoader->cache_base->known_classes[$this->searched_classname];
              } */
            return $this->getIncludepath($classname);
        }
        //handle case when no path can be returned
        if (!isset($classpath)) {
            eval("class $classname{};"); //hack to make autoload throw a regular exception
            throw new Exception('Could not find required class ("' . $classname . '") in ClassLoader.' . "\n" . '[1] If new classes have been added to the system, ClassLoader must be rebuilt! Please delete the cache file ' . ClassLoader::getCacheFile() . ' to rebuild the cache and reload.' . "\n" . '[2] The required class lies in an excluded directory. Check ClassLoader::defineExcludeList() and rebuild cache on any changes.' . "\n" . 'Called from:\n');
            die('Execution stopped.');
        }
        return $classpath;
    }

}

?>