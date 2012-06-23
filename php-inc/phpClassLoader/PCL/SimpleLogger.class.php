<?php

require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . '../file/File.class.php';

/**
 * SimpleLogger<br />
 * ======================<br />
 * This Logger logs all messages to a file that is given, or a new temprary file<br />
 * 
 * @package		PCL
 * @subpackage          phpClassLoader
 * 
 *
 * @link		https://github.com/petershaw/PhpClassLoader/wiki/PhpClassLoader
 * @author		@peter_shaw
 *
 * @version		1.0.0
 * @since               1.0.0
 *
 */
class SimpleLogger {

    const INFO = "INFO";
    const WARN = "WARN";
    const ERROR = "ERROR";

    private $logfile;

    function __construct(File $logfile = null) {
        if ($logfile == null) {
            $logfile = new File(
                            File::getTemporaryDirectory() .
                            DIRECTORY_SEPARATOR .
                            date("Y-m-d") .
                            '-phpClassLoader.log'
            );
        }
        if (is_a($logfile, 'File') == false) {
            return null;
        }
        if ($logfile->isExists() == false) {
            $logfile->create();
        }

        $this->logfile = $logfile;
    }

    public function getLogFile() {
        return $this->logfile;
    }

    public function log($level, $message) {
        $logMessage = date("Y-m-d h:m") . ": " . $level . " | " . $message . "\n";
        $this->logfile->appendString($logMessage);
    }

    public function getLogContent() {
        $this->logfile->disableReadCache();
        return $this->logfile->getContent();
    }

}

?>
