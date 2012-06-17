<?php

/**
 * GuessMime<br />
 * ======================<br />
 * Class guessmime holds static methods to guess the mimetype of a file<br />
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
class GuessMime {

    /**
     * Default mimetype if none otehr is found.
     * @var string mimetype 
     */
    public $defaultmime = 'text/html';

    /**
     * The out of the box default mime types
     */
    private $knownTypes = Array(
        'gif' => 'image/gif',
        'jpg' => 'image/jpg',
        'css' => 'text/css',
        'js' => 'text/javascript',
        'php' => 'text/html'
    );

    /**
     * Get a list of known mimetypes.
     * 
     * @return string 
     */
    function getTypes() {
        /** unterstützte filetypen */
        return $this->knownTypes;
    }

    /**
     * Returns the mime-type of $filepath.
     *
     * @param  string $filepath.
     * @return string
     * @static
     */
    static function typeOfFile($filepath) {
        $me = new guessmime ( );
        $types = $me->getTypes();
        $endung = preg_replace("/.+\.(" . implode("|", array_keys($types)) . ")$/", "\\1", $filepath);
        if (array_key_exists($endung, $types)) {
            return $types [$endung];
        }
        return $me->defaultmime;
    }

}