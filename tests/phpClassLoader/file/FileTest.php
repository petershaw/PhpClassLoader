<?php

$equivalentSrcDir = str_replace("tests", "php-inc", dirname(__FILE__));
require_once $equivalentSrcDir . DIRECTORY_SEPARATOR . 'File.class.php';

/**
 * FileTest
 * ============================
 * Testing the File-Object 
 * 
 *
 * @package		unittests
 * @subpackage          PhpClassLoader
 *
 * @link		https://github.com/petershaw/PhpClassLoader/wiki/PCLTestsSuite
 * @author		@peter_shaw
 *
 * @version		1.1.0
 * @since               1.0.0
 * 
 * @ignore
 */
class FileTest extends PHPUnit_Framework_TestCase {

    /**
     * @var File
     */
    private $File;

    /**
     * Prepares the environment before running a test.
     */
    protected function setUp() {
        parent::setUp();
        $this->File = new File(sys_get_temp_dir() . DIRECTORY_SEPARATOR . "unittest" . md5(DATE_ISO8601));
    }

    /**
     * Cleans up the environment after running a test.
     */
    protected function tearDown() {
        if (isset($this->File) && is_file($this->File->toString())) {
            $this->File->delete();
        }
        $this->File = null;
        parent::tearDown();
    }

    /**
     * Tests File->create()
     */
    public function testCreate() {
        echo "| test testCreate \n";
        $this->assertTrue($this->File->create(), "The file '" . $this->File->getDirname() . "/" . $this->File->getFilename() . "' could not be created.");
        $result = null;
        try {
            $this->assertFalse($this->File->create(), "The file '" . $this->File->getDirname() . "/" . $this->File->getFilename() . "' should be exist and can not be create a second time.");
        } catch (FileException $e) {
            $result = $e;
        }
        $this->assertTrue(is_a($result, "FileException"), "Thrown exception is not a FileException.");
        $this->assertTrue($this->File->delete(), "The file '" . $this->File->getDirname() . "/" . $this->File->getFilename() . "' could not be erased.");
    }

    /**
     * Tests File->delete()
     */
    public function testDelete() {
        echo "| test testDelete \n";
        $this->assertTrue($this->File->create(), "The file '" . $this->File->getDirname() . "/" . $this->File->getFilename() . "' could not be created.");
        $this->assertTrue($this->File->delete(), "The file '" . $this->File->getDirname() . "/" . $this->File->getFilename() . "' could not be erased.");
    }

    /**
     * Tests File->deleteOnExit()
     */
    public function testDeleteOnExit() {
        echo "| test testDeleteOnExit \n";
        $fn = $this->File->getDirname() . "/" . $this->File->getFilename();
        $this->assertTrue($this->File->create(), "The file '" . $this->File->getDirname() . "/" . $this->File->getFilename() . "' could not be created.");
        $this->File->deleteOnExit();
        $this->File = null;
        //sleep(5);
        $this->assertFalse(is_file($fn), "The file '" . $fn . "' should be deleted after the object was destroyed.");
    }

    /**
     * Tests File->getDirname()
     */
    public function testGetDirname() {
        echo "| test testGetDirname \n";
        $check = new File($this->File->getDirname() . "/" . $this->File->getFilename());
        $this->assertEquals($this->File->getDirname(), $check->getDirname(), "The basename in new file not the same.");
    }

    /**
     * Tests File->getFilename()
     */
    public function testGetFilename() {
        echo "| test testGetFilename \n";
        $check = new File($this->File->getDirname() . "/" . $this->File->getFilename());
        $this->assertEquals($this->File->getFilename(), $check->getFilename(), "The filename in new file not the same.");
    }

    /**
     * Tests File->getFilename()
     */
    public function testToString() {
        echo "| test testToString \n";
        $this->assertEquals($this->File->getDirname() . DIRECTORY_SEPARATOR . $this->File->getFilename(), $this->File->toString(), "The toString function do not return basename_seperator_filename.");
    }

    /**
     * Tests File->getContent()
     */
    public function testGetContent() {
        echo "| test testGetContent \n";
        $content = "This is a test content.";
        file_put_contents($this->File->toString(), $content);
        $this->assertEquals($this->File->getContent(), $content, "Can not read content from file '" . $this->File->toString() . "'");
    }

    public function testGetMd5OfContent() {
        echo "| test testGetMd5OfContent \n";
        $content = "example";
        $check = "1a79a4d60de6718e8e5b326e338ae533";
        file_put_contents($this->File->toString(), $content);
        $this->assertEquals(strlen($this->File->getMd5OfContent()), 32, "Md5sum is not in length of 32 charactars");
        $this->assertEquals($this->File->getMd5OfContent(), $check, "Md5 content of the file '" . $this->File->toString() . "' is not like it should be.");
    }

    public function testIsExists() {
        echo "| test testIsExists \n";
        $this->File->create();
        $this->assertTrue($this->File->isExists(), "File does not exists.");
        $this->File->delete();
        $this->assertFalse($this->File->isExists(), "File does still exists.");
    }

    public function testCache() {
        echo "| test testCache \n";
        $testString1 = "This is a string that is cached b default.";
        $testString2 = "Second string.";
        $testString3 = "Third string.";
        $filename = sys_get_temp_dir() . DIRECTORY_SEPARATOR . "unittest" . md5(DATE_ISO8601);
        $this->File = new File($filename);
        if ($this->File->isExists()) {
            $this->File->delete();
        }

        $this->File->create();
        $this->assertEquals($this->File->getContent(), "", "String is not empty after file was created.");
        $this->File->appendString($testString1);
        $this->assertEquals($this->File->getContent(), "", "String is not empty after a file was created with cache.");

        $this->File = new File($filename);
        $this->assertEquals($this->File->getContent(), $testString1, "String should be filled after reopen a file.");
        $this->File->appendString($testString2);

        $this->File->disableReadCache();
        $this->File->appendString($testString3);

        $this->assertEquals($this->File->getContent(), $testString1 . $testString2 . $testString3, "String should have all Strings after disabled cache.");
        $this->File->deleteOnExit();
        unset($this->File);
    }

    public function testAppend() {
        echo "| test testAppend \n";
        $this->File->create();
        $c1 = $this->File->getContent();
        $this->assertEquals($c1, "", "String is not empty after a file was created.");

        $ok = $this->File->appendString("first string.");
        $c2 = $this->File->getContent();
        $this->assertTrue($ok, "Append to a file did not return true. Problems with byte2string?");
        $this->assertEquals($c2, "", "String schould be empty on the default caching in: " . $this->File->getDirname() . "/" . $this->File->getFilename());

        $ok = $this->File->appendString("second string.");
        $this->File->disableReadCache();
        $c3 = $this->File->getContent();
        $this->assertTrue($ok, "Append to a file did not return true. Problems with byte2string?");
        $this->assertEquals($c3, "first string.second string.", "String is not same as appended in: " . $this->File->getDirname() . "/" . $this->File->getFilename());

        $this->File->delete();
    }

    /**
     * Tests File->__destruct()
     */
    public function test__destruct() {
        echo "| test test__destruct \n";
        $this->testDeleteOnExit();
    }

}

