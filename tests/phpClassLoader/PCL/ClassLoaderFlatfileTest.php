<?php

$equivalentSrcDir = str_replace("tests", "php-inc", dirname(__FILE__));
require_once  $equivalentSrcDir. DIRECTORY_SEPARATOR .'ClassLoader.class.php';

/**
 * ClassLoaderFlatfileTest
 * ============================
 * This suite tests the ClassLoader in flatfile mode.
 * The flatfile mode is for debugging and developing. 
 *
 * @package		unittests
 * @subpackage          PhpClassLoader
 *
 * @link		https://github.com/petershaw/PhpClassLoader/wiki/PCLTestsSuite
 * @author		@peter_shaw
 *
 * @version		1.0.0
 * @since               1.0.0
 * 
 * @ignore
 */
class ClassLoaderFlatfileTest extends PHPUnit_Framework_TestCase implements PHPUnit_Framework_Test {

    /**
     * Classloader object
     * @var type 
     */
    protected $classcache;

    /**
     * Called by PHPUnit as kinda consturctor for all methods!
     */
    protected function setUp() {
echo "SetUp: classloadertest, in ". dirname(__FILE__) ."\n";
        $this->classcache = ClassLoader::getInstance("classloadertest", dirname(__FILE__), true);
        if (file_exists(ClassLoader::getCacheFile())) {
            unlink(ClassLoader::getCacheFile());
        }
        //$this->classcache = ClassLoader::getInstance("classloadertest", dirname(__FILE__));
        ClassLoader::$mode = 'flatfile';
        $file = $this->classcache->getCacheFile();
    }

    /**
     * after all tests. clearup.
     */
    protected function tearDown() {
        unset($this->classcache);
    }

    /**
     * Test: Load a Class
     */
    public function testLoadOnFlatfile() {
        echo "| test testLoadOnFlatfile \n";
        $testObj = new uuuxxxgibtsnicht();
        $result = $this->classcache->getAllKnownClasses();
        $this->assertTrue(array_key_exists("uuuxxxgibtsnicht", $result), 'Testclass not found.');
        unset($testObj);
    }

}