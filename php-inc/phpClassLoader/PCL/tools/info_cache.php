<?php

/**
 * info_cache<br />
 * ======================<br />
 * displays the current contents and mode of the $cache_file on the command line.<br />
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
require_once(dirname(__FILE__) . '/../ClassLoader.class.php');
$cc = new ClassLoader();

echo "\n-------------------------------------------\n";

//list info
$i = '0';
if (count($cc->getAllKnownClasses())) {

    foreach ($cc->getAllKnownClasses() as $classname => $path) {
        ++$i;
        echo "\n$i: [$classname] => $path\n";
    }
} else {
    echo "Problem: No \$known_classes available.\n";
}

echo "\n-------------------------------------------\n";
echo "\n";
echo "Mode: " . $cc->mode . "\n";
echo "Target directory: " . $cc->targetdir . "\n";
echo "Cache file: " . ClassLoader::getCacheFile() . "\n";

if (is_writable($cc->targetdir . '/' . ClassLoader::getCacheFile())) {
    $wr_info = 'OK';
} else {
    $wr_info = '##ERR## not granted';
}
echo "Write permissions: $wr_info\n";
echo "File permissions: " . substr(sprintf('%o', fileperms($cc->targetdir . '/' . ClassLoader::getCacheFile())), -4) . "\n";

echo "Number of classes and interfaces currently cached: $i\n";
echo "\n";
echo "Dir nodes used as root nodes for caching:\n";
if (count($cc->cache_roots_arr)) {
    foreach ($cc->cache_roots_arr as $dir)
        echo " - $dir\n";
} else {
    echo "##ERR## No root nodes defined.\n";
}
echo "\n";
echo "Defined exclude masks:\n";
if (count($cc->exclude_dirs_arr)) {
    foreach ($cc->exclude_dirs_arr as $dir)
        echo " - $dir\n";
} else {
    echo " + No exclude masks defined.\n";
}

echo "\n-------------------------------------------\n";
?>
