<?php

/**
 * ClassNotFoundException
 * ======================
 * This exception occure when a class is not found inside the classlaoder. 
 *
 * @package		PCL
 * @subpackage          ClassLoader
 * 
 *
 * @link		%projecturl%/%articles%/PHPCarpetRoot/ClassLoader
 * @author		@peter_shaw
 *
 * @version		1.0.0
 * @since               1.0.0
 *
 */
class ClassNotFoundException extends Exception {

    /**
     * Errorcode 
     * @var int $code
     */
    protected $code = 0;

    /**
     * Construct a new ClassNotFoundException with a individual message.
     * 
     * @param string $message
     * @param @optional class $class 
     * @param @optional Exception $e 
     */
    public function __construct($message, $class = null, $e = null) {
        if ($e != null) {
            $message .= "\n" . $e->getMessage();
        }
        parent::__construct($message, $this->code);
    }

}

?>
