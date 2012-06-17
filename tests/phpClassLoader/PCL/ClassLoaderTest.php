<?php

/**
 * ClassLoaderTest
 * ============================
 * This suite tests the ClassLoader itself.
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
class ClassLoaderTest extends PHPUnit_Framework_TestCase implements PHPUnit_Framework_Test {

    /**
     * Classloader object
     * @var type 
     */
    protected $classcache;

    /**
     * Called by PHPUnit as kinda consturctor for all methods!
     */
    protected function setUp() {
        $this->classcache = new ClassLoader("classloadertest", dirname(__FILE__));
        // we use the flatfile, because it is the easiest and well tested one.
        $this->classcache->mode = 'flatfile';
    }

    /**
     * Called by PHPUnit as kinda desturctor for all methods!
     */
    protected function tearDown() {
        unset($this->classcache);
    }

    /**
     * Test: Load a Class
     */
    public function testClassLoad() {
        echo "| test testClassLoad \n";
        $file = new File($this->classcache->getCacheFile());
        echo "\nDelete " . $file->getDirname() . "/" . $file->getFilename() . "\n";
        if ($file->delete()) {
            echo "Ok.\n";
        } else {
            echo "hmm, can not delete.\n";
            if ($file->isExists()) {
                echo "File still exists.\n";
                $this->assertTrue(false);
            } else {
                echo "But all is file, because file does not exists.\n";
                $this->assertTrue(true);
            }
        }

        $this->classcache = new ClassLoader("classloadertest", dirname(__FILE__));
        $this->classcache->mode = 'flatfile';
        $file = $this->classcache->getCacheFile();
        echo "USING: " . ClassLoader::getCacheFile() ."\n\n";

        $testObj = new uuuxxxgibtsnicht();
        $result = $this->classcache->getAllKnownClasses();
        $this->assertTrue(array_key_exists("uuuxxxgibtsnicht", $result), 'Testclass not found.');

        unset($testObj);
    }

    /**
     * Test: Load a Class
     * The second time. At first time the file is deleted befor the load is 
     * triggerd. At the second time, the file still exists.
     */
    public function testSecondLoad() {
        echo "| test testSecondLoad\n";
        $testObj = new uuuxxxgibtsnicht();
        $result = $this->classcache->getAllKnownClasses();
        $this->assertTrue(array_key_exists("uuuxxxgibtsnicht", $result), 'Testclass not found.');
        unset($testObj);
    }

}