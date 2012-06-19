<?php

/**
 * PhpClassLoader<br />
 * ======================<br />
 * The PhpClassLoader includes files on demand when a constructor is called.<br />
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
 * 
 * This file is the entry point to include
 *
 * @package		PCL
 * @subpackage          PhpClassLoader
 *
 * @link		https://github.com/petershaw/PhpClassLoader/wiki/PhpClassLoader
 * @author		@peter_shaw
 *
 * @version		1.0.0
 * @since               1.0.0
 *
 */

// include internal sources
require_once dirname(__FILE__). DIRECTORY_SEPARATOR .'PCL'. DIRECTORY_SEPARATOR .'ClassLoader.class.php';
require_once dirname(__FILE__). DIRECTORY_SEPARATOR .'configuration'. DIRECTORY_SEPARATOR .'PCLConfiguration.class.php';
require_once dirname(__FILE__). DIRECTORY_SEPARATOR .'configuration'. DIRECTORY_SEPARATOR .'validation'. DIRECTORY_SEPARATOR .'XmlFileValidator.class.php';
require_once dirname(__FILE__). DIRECTORY_SEPARATOR .'configuration'. DIRECTORY_SEPARATOR .'validation'. DIRECTORY_SEPARATOR .'XmlStringValidator.class.php';
require_once dirname(__FILE__). DIRECTORY_SEPARATOR .'file'. DIRECTORY_SEPARATOR .'File.class.php';
require_once dirname(__FILE__). DIRECTORY_SEPARATOR .'file'. DIRECTORY_SEPARATOR .'FsInfo.class.php';
require_once dirname(__FILE__). DIRECTORY_SEPARATOR .'file'. DIRECTORY_SEPARATOR .'GuessMime.class.php';
require_once dirname(__FILE__). DIRECTORY_SEPARATOR .'file'. DIRECTORY_SEPARATOR .'IncFile.class.php';
require_once dirname(__FILE__). DIRECTORY_SEPARATOR .'xml'. DIRECTORY_SEPARATOR .'xmlSearch'. DIRECTORY_SEPARATOR .'XmlSearch.class.php';

// get the directory where the classloader is located.
if(defined("PhpClassLoader_RootDirectory") === false ){
    $pcl_pathArray = explode(DIRECTORY_SEPARATOR, Phar::running(false));
    array_pop($pcl_pathArray);
    define("PhpClassLoader_RootDirectory", implode(DIRECTORY_SEPARATOR, $pcl_pathArray));    
}

?>
