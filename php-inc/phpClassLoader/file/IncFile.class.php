<?php

require_once dirname(__FILE__) . DIRECTORY_SEPARATOR .'File.class.php';

/**
 * IncFile<br />
 * ======================<br />
 * IncFile is a extention on File. IncFile handles Files inside the php_inc 
 * structure. It will parse all folders to find, the file. <br />
 * <br />
 * @example
 * eg.: new  IncFile('/foo/bar/file.ext') and a inc path to: '/a/b:/c/d/e:./f' 
 * will search for the file in ./f/foo/bar/file.ext, than in 
 * /c/d/e/foo/bar/file.ext than in /a/b/foo/bar/file.ext. the first found 
 * returns the handle. 
 * <br />
 *  
 * @package		PCL
 * @subpackage          File
 * 
 * @link		https://github.com/petershaw/PhpClassLoader/wiki/File
 * @author		@peter_shaw
 *
 * @version		1.0.0
 * @since               1.0.0
 *
 */
class IncFile extends File {

    /**
     * The fielname 
     * @var string
     */
    private $incFile;

    /**
     * Handle to a file from the include directory.
     * 
     * @var String
     */
    function __construct($incFile) {
        $this->incFile = $incFile;
        $file = $incFile;
        $paths = array_reverse(explode(PATH_SEPARATOR, ini_get('include_path')));
        foreach ($paths as $path) {
            if (is_file($path . DIRECTORY_SEPARATOR . $incFile) && file_exists($path . DIRECTORY_SEPARATOR . $incFile)) {
                $file = $path . DIRECTORY_SEPARATOR . $incFile;
            }
        }
        parent::__construct($file);
    }

    /**
     * Operations while the object is destroyed.
     */
    function __destruct() {
        parent::__destruct();
    }

}

?>
