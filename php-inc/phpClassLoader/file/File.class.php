<?php

require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . "FsInfo.class.php";
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . "FileException.class.php";

/**
 * File<br />
 * ======================<br />
 * File is a wrapper around a File on the Filesystem. It provides usefull 
 * low-level functions.<br />
 * 
 * @package		PCL
 * @subpackage          File
 * 
 * @link		https://github.com/petershaw/PhpClassLoader/wiki/File
 * @author		@peter_shaw
 *
 * @version		1.1.0
 * @since               1.0.0
 *
 */
class File extends FsInfo {

    /** The filehandle */
    private $fh;

    /** Content of the file */
    private $content = null;

    /** The pure name of the file without the directory */
    protected $filename;

    /** The directory of the file */
    private $basename;
    private $stat;

    /** If this is true, the file will be deleted while destruction */
    private $isDeleteOnExit = false;

    /** If the fiel existes, this variuable is true */
    private $isExist = false;

    /** 
     * Switch the readcache off to get the content of a file without caching
     * @var type 
     */
    private $useReadCache = true;
    
    /**
     * Get a new filehandler on $file
     * 
     * @param string $file
     */
    function __construct($file) {
        $this->basename = dirname($file);
        $this->filename = basename($file);
        if (is_file($file) && file_exists($file)) {
            $this->isExist = true;
        }
        @$this->stat = stat($this->basename . $this->getDirectorySeperator() . $this->filename);
    }

    /**
     * Close the filehandler and do some cleanup tasks
     */
    function __destruct() {
        // is file still open?
        if (isset($this->fh)) {
            fclose($this->fh);
        }
        // delete file?
        if ($this->isDeleteOnExit === true) {
            if (!unlink($this->basename . $this->getDirectorySeperator() . $this->filename)) {
                throw new FileException($this, "File '" . $this->basename . $this->getDirectorySeperator() . $this->filename . "' can't be deleted.");
            }
        }
    }

    public function disableReadCache(){
        $this->useReadCache = false;
    }

    /**
     * reset the file to new location
     * 
     * @param string $newPathToFile
     */
    public function reSet($newPathToFile) {
        $this->basename = dirname($newPathToFile);
        $this->filename = basename($newPathToFile);
        if (is_file($newPathToFile) && file_exists($newPathToFile)) {
            $this->isExist = true;
        }
        @$this->stat = stat($this->basename . $this->getDirectorySeperator() . $this->filename);
    }

    /**
     * Get systemdepended directory seperator
     */
    public static function getDirectorySeperator() {
        return DIRECTORY_SEPARATOR;
    }

    /**
     * Create the file if it is not exist.
     *
     * @return boolean
     */
    public function create() {
        if ($this->isExist === false) {
            $this->fh = fopen($this->basename . $this->getDirectorySeperator() . $this->filename, "a");
            if (isset($this->fh) && is_file($this->basename . $this->getDirectorySeperator() . $this->filename)) {
                $this->isExist = true;
                return true;
            }
        } else {
            throw new FileException($this, "File '" . $this->basename . $this->getDirectorySeperator() . $this->filename . "' can't be created, because it's allready exists.");
        }
        return false;
    }
    
        /**
     * Open the file.
     *
     * @return boolean
     */
    private function open() {
        if ($this->isExist) {
            $this->fh = fopen($this->basename . $this->getDirectorySeperator() . $this->filename, "a");
            if (isset($this->fh) ) {
                return true;
            }
        } else {
            throw new FileException($this, "File '" . $this->basename . $this->getDirectorySeperator() . $this->filename . "' does not exists.");
        }
        return false;
    }

    /**
     * Removes the file.
     *
     * @return boolean
     */
    public function delete() {
        $bol = unlink($this->basename . $this->getDirectorySeperator() . $this->filename);
        if ($bol == true) {
            $this->isExist = false;
        }
        return $bol;
    }

    /**
     * Deletes the file automaticls after Script is running.
     * Very usefull for temporay files. 
     */
    public function deleteOnExit() {
        $this->isDeleteOnExit = true;
    }

    /**
     * Get the name of the corresponding file.
     * 
     * @return string 
     */
    public function getFilename() {
        return $this->filename;
    }

    /**
     * Get the directory of the corresponding file.
     * 
     * @return string 
     */
    public function getDirname() {
        return $this->basename;
    }

    /**
     * Returns project-url for given file, if file is in subfolder of 
     * web_root/project_root.
     *
     * @return string url or null
     */
    public function getUrl() {
        $page = Page::getPageInstance();
        $uriToFile = str_replace($page->getRootdir(), "", $this->getDirname());
        return pathUtil::cleanupPath($page->getInstalledFolder() . "/" . $uriToFile . "/" . $this->getFilename());
    }

    /**
     * Returns the aboslute Filename as string
     * @see __toString
     * 
     * @return string
     */
    public function toString() {
        return $this->getDirname() . $this->getDirectorySeperator() . $this->getFilename();
    }

    /**
     * Returns the aboslute Filename as string
     * same as toString;
     * 
     * @return string
     */
    public function __toString() {
        return $this->toString();
    }

    /**
     * Returns the content of the corresponding File.
     * 
     * @return string
     */
    public function getContent() {
        if ($this->content === null || $this->useReadCache === false) {
            $this->content = file_get_contents($this->toString());
        }
        return $this->content;
    }

    /**
     * Appends a string to the file. It returns true if the written length 
     * is equivalent to the strig length. 
     * 
     * @param string $string
     * @return boolean 
     */
    public function appendString($string){
         if (isset($this->fh) == false) {
             $this->open();
         }
         $originalLength = mb_strlen($string);
         $byteWritten = fwrite($this->fh, $string);
         if($byteWritten == $originalLength){
             return true;
         } 
         return false;
    }
    
    /**
     * Returns the md5 checksum of the content of the corresponding File.
     * 
     * @return string md5 checksum
     */
    public function getMd5OfContent() {
        return md5($this->getContent());
    }

    /**
     * Returns the date when the file was modified.
     * 
     * @return date 
     */
    public function getLastModified() {
        return $this->stat['mtime'];
    }

    /**
     * Returns the date when the file was created.
     * 
     * @return date 
     */
    public function getCreateTime() {
        return $this->stat['ctime'];
    }

    /**
     * Save the $content into the file.
     * 
     * @return int The function returns the number of bytes that were 
     *         written to the file, or false on failure. 
     */
    public function setContent($content) {
        return file_put_contents($this->toString(), $content);
    }

    /**
     * Returns true if the file is exists.
     *
     * @return boolean
     */
    public function isExists() {
        return $this->isExist;
    }

    /**
     * Returns the directory where temporaray files should placed.
     *
     * @return string
     */
    public static function getTemporaryDirectory() {
        if (function_exists('sys_get_temp_dir')) {
            return sys_get_temp_dir();
        }
        // Try to get from environment variable
        if (!empty($_ENV['TMP'])) {
            return realpath($_ENV['TMP']);
        } else if (!empty($_ENV['TMPDIR'])) {
            return realpath($_ENV['TMPDIR']);
        } else if (!empty($_ENV['TEMP'])) {
            return realpath($_ENV['TEMP']);
        } else {
            // Detect by creating a temporary file
            // Try to use system's temporary directory
            // as random name shouldn't exist
            $temp_file = tempnam(md5(uniqid(rand(), TRUE)), '');
            if ($temp_file) {
                $temp_dir = realpath(dirname($temp_file));
                unlink($temp_file);
                return $temp_dir;
            } else {
                return FALSE;
            }
        }
    }

}

?>