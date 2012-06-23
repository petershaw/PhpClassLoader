<?php

$equivalentSrcDir = str_replace("tests", "php-inc", dirname(__FILE__));
require_once $equivalentSrcDir . DIRECTORY_SEPARATOR . 'ClassLoader.class.php';

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
class ClassLoadernonExistingTest extends PHPUnit_Framework_TestCase implements PHPUnit_Framework_Test {

    /**
     * Classloader object
     * @var type 
     */
    protected $classcache;

    /**
     * Called by PHPUnit as kinda consturctor for all methods!
     */
    protected function setUp() {
        $this->classcache = ClassLoader::getInstance("classloadertest", dirname(__FILE__), true);
        $file = $this->classcache->getCacheFile();
        echo "created cache file: " . $file . "\n";
    }

    /**
     * after all tests. clearup.
     */
    protected function tearDown() {
        unset($this->classcache);
        if (file_exists(ClassLoader::getCacheFile())) {
            unlink(ClassLoader::getCacheFile());
        }
    }

    /**
     * Test: Load a Class
     */
    public function testLoadNonExistingClass() {
        echo "| test testLoadNonExistingClass \n";
        $throwExeption = false;
        $testObj = null;
        try {
            $testObj = new michgibteswirklichnicht();
        } catch(Exception $e){
            echo $e->getMessage() ."\n";
            $throwExeption = true;
        }
        $this->assertNull($testObj, 'michgibteswirklichnicht is not null.');
        $this->assertTrue($throwExeption, "No exception is thrown.");
    }

}