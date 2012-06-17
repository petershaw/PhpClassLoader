<?php

require_once dirname(__FILE__). DIRECTORY_SEPARATOR .'../xml/xmlSearch/XmlSearch.class.php';
require_once dirname(__FILE__). DIRECTORY_SEPARATOR .'validation/XmlStringValidator.class.php';

/**
 * Configuration<br />
 * ======================<br />
 * This Classs manages component configuration.  <br />
 * This part is taken from PHPCarpet's more generalized Validationsuite.
 * This Configuration is ONLY for the standalone version of the classloader.
 * Id does not support all features that the original configuratior provides, 
 * but it is far enough for the classloader.<br />
 * 
 * @package		PCL
 * @subpackage          Configuration
 * 
 * @link		https://github.com/petershaw/PhpClassLoader/wiki/PCLConfiguration
 * @author		@peter_shaw
 *
 * @version		1.0.0
 * @since               1.0.0
 *
 */
class PCLConfiguration {

    /**
     * Who called configuration
     */
    private $callerDir;
    private $callerClass;

    /**
     * The configuration file path
     * 
     * @var File configurationFile
     */
    private $configurationFile;

    /**
     * xpath object
     * 
     * @var xpath 
     */
    private $xsearch;

    /**
     * Creates a new Configuration-Object. The default (no parameters) will use 
     * the cnfiguration from the calling object. But is is possible to 
     * manipulate the file to use by pasing a dir and a class to the constructor. 
     *
     * @param @optional $callerClass
     * @param @optional $callerDir 
     */
    function __construct($callerClass = null, $callerDir = null) {
        $this->callerClass = $callerClass;
        $this->callerDir = $callerDir;
        $file = $this->getConfigurationFile(0);
        if ($this->isConfiguration()) {
            $this->xsearch = new XmlSearch($file);
        }
    }

    /**
     * Returns the File() of the configuration xml that will be used for initiating the component.
     *
     * @param int $stack
     * @return string
     */
    public function getConfigurationFile($stack = 0) {
        $index = 1 + $stack; // because if we call getConfigurationFilename $index has to 
        // be 1, if we use the constructor, $index has to be 2 
        if (isset($this->callerDir) == false || isset($this->callerClass) == null) {
            $backtrace = debug_backtrace();
            $this->callerDir = dirname($backtrace[$index]['file']);
            if (isset($this->callerClass) == false) {
                if (isset($backtrace[$index + 1]['class'])) {
                    $this->callerClass = $backtrace[$index + 1]['class']; // i know, it is strange... but this is php
                }
            }
        }
        //$this->configurationFile = new File($this->callerDir ."/". strtolower($this->callerClass) .".config.xml");
        $this->configurationFile = $this->callerDir . "/" . strtolower($this->callerClass) . ".config.xml";
        return $this->configurationFile;
    }

    /**
     * Returns the filename of the configuration xml.
     *
     * @return string
     */
    public function getConfigurationFilename() {
        return $this->configurationFile;
    }

    /**
     * Returns true if the configurationfile is present and readable.
     *
     * @return boolean
     */
    public function isConfiguration() {
        if (file_exists($this->configurationFile)) {
            return true;
        }
        return false;
    }

    /**
     * Get a Parameter from the configuration file.
     *
     * @param String $name  - Parameter-Key
     * @param Object $default optional - Value if Parameter-ey returns null 
     */
    public function getSetupParameter($name, $default = null) {
        if (isset($this->xsearch) == false) {
            throw new NilException('There is no configurationobject for this class loaded.');
        }
        $type = $this->xsearch->queryArray('/configuration/parameters/define[@variable="' . $name . '"]/@type');
        $type = array_shift($type);
        if ($type == null) {
            $type = 'string';
        }
        $value = $this->xsearch->queryArray('/configuration/parameters/define[@variable="' . $name . '"]/@value');
        $value = array_shift($value);
        if ($value == null) {
            $value = $this->xsearch->queryArray('/configuration/parameters/define[@variable="' . $name . '"]/text()');
            $value = trim(array_shift($value));
            if ($value == null) {
                return $default;
            }
        }
        if (strtolower($type) == 'boolean') {
            if (strtolower($value) == 'true') {
                $value = true;
            } elseif (strtolower($value) == 'false') {
                $value = false;
            }
        }
        return $value;
    }

    /**
     * Get all parameter Keys from the configuration
     * 
     * @return string value
     */
    public function getSetupParameterKeys() {
        $value = $this->xsearch->queryArray('/configuration/parameters/define/@variable');
        return $value;
    }

    /**
     * Get all parameters from the configuration
     * 
     * @return associative array of keys and values
     */
    public function getSetupParameters() {
        $value = array();
        foreach ($this->getSetupParameterKeys() as $key) {
            $value[$key] = $this->getSetupParameter($key);
        }
        return $value;
    }

}

?>
