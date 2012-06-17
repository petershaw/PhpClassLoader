<?php

/**
 * info_cache
 * ======================
 * Helper tool to delete the cache file from the command line. 
 * 
 * @package		SCRoot
 * @subpackage          ClassLoader
 * 
 * 
 * @link		%projecturl%/%articles%/PHPCarpetRoot/ClassLoader/tools
 * @author		@peter_shaw and colleagues 2008
 *
 * @version		1.0.0
 * @since               1.0.0
 *
 */
require_once(dirname(__FILE__).'/../ClassLoader.class.php');

$cc = new ClassLoader();

echo "\n-------------------------------------------\n";

//list info
$i = '0';
if ( count($cc->getAllKnownClasses()) ) {
	foreach ($cc->getAllKnownClasses() as $classname=>$path)
		++$i;
}

echo "\n";
echo "Target directory: ".$cc->targetdir."\n";
echo "Cache file: ".ClassLoader::getCacheFile()."\n";
echo "File permissions: ".substr(sprintf('%o', fileperms($cc->targetdir.'/'.ClassLoader::getCacheFile())), -4)."\n";
echo "Number of classes and interfaces cached: $i\n";
echo "\n";

if ( unlink($cc->targetdir.'/'.ClassLoader::getCacheFile()) ) {
	$del_info = 'OK, deleted';
}
else {
	$del_info = '##ERR## file could not be deleted, no permission !';
}
echo "Cache file delete status: $del_info\n";

echo "\n-------------------------------------------\n";

?>