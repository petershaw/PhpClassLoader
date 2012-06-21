<?php

/**
 * FsInfo<br />
 * ======================<br />
 * Encapsulate metainforamtion about a file.<br />
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
 * @abstract
 * @lagacy 
 */
abstract class FsInfo {

    /**
     * The name of the file that is in question.
     * @var string 
     */
    protected $filename;

    /**
     * Returns path systems temporary directory.
     *
     * @param string special_tmp_path optional predefined path
     * @return string path or null
     */
    public static function getTemporaryDirectory() {
        //1st use php internal sys_get_temp_dir()
        if (function_exists('sys_get_temp_dir')) {
            $sys_get_temp_dir = sys_get_temp_dir();
            if (strrchr($sys_get_temp_dir, DIRECTORY_SEPARATOR) == strlen($sys_get_temp_dir)) {
                $sys_get_temp_dir = substr($sys_get_temp_dir, -1);
            }
            return $sys_get_temp_dir . DIRECTORY_SEPARATOR;
        }
        //2nd make assumptions, try to get from environment variables
        if (!empty($_ENV ['TMP'])) {
            return realpath($_ENV ['TMP']);
        } elseif (!empty($_ENV ['TMPDIR'])) {
            return realpath($_ENV ['TMPDIR']);
        } elseif (!empty($_ENV ['TEMP'])) {
            return realpath($_ENV ['TEMP']);
        } else { //detect by creating a temporary file
            //use system's temporary directory
            $temp_file = tempnam(md5(uniqid(rand(), true)), '');
            if ($temp_file) {
                $temp_dir = realpath(dirname($temp_file));
                if (strrchr($temp_dir, DIRECTORY_SEPARATOR) == strlen($temp_dir)) {
                    $temp_dir = substr($temp_dir, -1);
                }
                unlink($temp_file);
                return $temp_dir . DIRECTORY_SEPARATOR;
            } else {
                return null;
            }
        }
    }

}

?>
