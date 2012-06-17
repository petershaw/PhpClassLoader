<?php

require_once 'SCRoot/ClassLoader/ClassLoader.class.php';
require_once 'SCRoot/XmlSearch/XmlSearch.class.php';
require_once 'SCRoot/Validation/XmlStringValidator.class.php';

/**
 * Configuration
 * ======================
 * This Classs manages component configuration.  
 * 
 *
 * @package		SCRoot
 * @subpackage          Configuration
 * 
 * @link		%projecturl%/%articles%/PHPCarpetRoot/Configuration
 * @author		@peter_shaw and colleagues
 *
 * @version		1.0.0
 * @since               1.0.0
 *
 */
class Configuration {

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
        $type = $this->xsearch->queryArray('/configuration/setup/define[@variable="' . $name . '"]/@type');
        $type = array_shift($type);
        if ($type == null) {
            $type = 'string';
        }
        $value = $this->xsearch->queryArray('/configuration/setup/define[@variable="' . $name . '"]/@value');
        $value = array_shift($value);
        if ($value == null) {
            $value = $this->xsearch->queryArray('/configuration/setup/define[@variable="' . $name . '"]/text()');
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
        $value = $this->xsearch->queryArray('/configuration/setup/define/@variable');
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

    /**
     * Returns a array of loggers that should be used (You can set the 
     * logger in the configuration file).
     *
     * @return string logger
     */
    public function getLoggers() {
        return $this->xsearch->queryElementAttributes('/configuration/log/logger');
    }

    /**
     * Returns a array of loggers that matched the $mode
     * 
     * @param string mode (Loggermode)
     * @return array of loggers
     */
    public function getModeFilterdLoggers($mode) {
        $logger = array();
        if (is_int($mode)) {
            $mode = LogLevel::getLevelName($mode);
        } try {
            if (isset($this->xsearch) && $this->xsearch != null) {
                foreach ($this->xsearch->queryElementAttributes('//log/logger[@mode="' . strtolower($mode) . '"]') as $val) {
                    array_push($logger, $val);
                }
                for ($n = (LogLevel::getLevelNumber($mode) - 1); $n >= 0; $n--) {
                    $level = LogLevel::getLevelName($n);
                    foreach ($this->xsearch->queryElementAttributes('//log/logger[@mode="' . strtolower($level) . '"]') as $val) {
                        array_push($logger, $val);
                    }
                }
            }
        } catch (Exception $e) {
            array_push($logger, "Logger::default");
        }
        return($logger);
    }

}

?>
