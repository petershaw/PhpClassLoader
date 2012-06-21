<?php

/**
 * ClassLoaderSQLiteTest
 * ============================
 * This suite tests the ClassLoader in sqlite mode.
 * The sqlite mode is for productive while it is not possible to use a in 
 * memory storage. 
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
class ClassLoaderSQLiteTest extends PHPUnit_Framework_TestCase implements PHPUnit_Framework_Test {

    /**
     * Classloader object
     * @var type 
     */
    protected $classcache;

    /**
     * Called by PHPUnit as kinda consturctor for all methods!
     */
    protected function setUp() {
        $this->classcache =  ClassLoader::getInstance(__CLASS__, dirname(__FILE__), true);
        ClassLoader::$mode = 'sqllite';
        $this->classcache->clear();
    }

    /**
     * Called by PHPUnit as kinda desturctor for all methods!
     */
    protected function tearDown() {
        //unset($this->classcache);
    }

    /**
     * Test: Load a Class
     */
    public function testSQLLoad() {
        echo "| test testSQLLoad\n";
        
        echo "&&&&& ". $this->classcache->getCacheFile()."\n";
        echo "&&&&& ". ClassLoader::$mode ."\n";
        echo "&&&&& ". ClassLoader::$custom_conf_class ."\n";
        echo "&&&&& ". ClassLoader::$custom_conf_dir ."\n";
        echo "&&&&& ". ClassLoader::$cache_file ."\n";
        
        $testObj = new yyyxxxgibtsnicht();
        $this->assertEquals("for testing", $testObj->reason, "Could not get public property from testcalss-");
        
        $result = $this->classcache->getAllKnownClasses();
        $this->assertTrue(array_key_exists("yyyxxxgibtsnicht", $result), 'Testclass not found.');
        unset($testObj);
    }

}