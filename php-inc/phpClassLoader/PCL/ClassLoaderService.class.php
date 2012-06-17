<?php

/**
 * ClassLoaderService<br />
 * ======================<br />
 * Provides Services for the ClassLoader<br />
 *
 * @package		PCL
 * @subpackage          ClassLoader
 * 
 *
 * @link		https://github.com/petershaw/PhpClassLoader/wiki/PhpClassLoader
 * @author		@peter_shaw
 *
 * @version		1.0.0
 * @since               1.0.0
 *
 */
class ClassLoaderService extends PCLWebService {

    /**
     * Clear cachefile. 
     * 
     * @access superadmin
     * @service clearcache
     * @final
     */
    final public function clearcache($args) {
        $this->restrictedForSuperUser();
        if (isset($args['cache']) == false) {
            return $this->sendError("No Cache is selected.");
        }
        if ($args['cache'] == 'psb') {
            PsbCache::ClearCache();
            return $this->sendSuccess();
        } elseif ($_REQUEST['cache'] == 'class') {
            ClassLoader::deleteCache();
            return $this->sendSuccess();
        } else {
            return $this->sendError("Cache " . $args['cache'] . " is unkown.");
        }
    }

}

class PCLWebService {
        /**
     * Return a success to the client and returns the content of a bean
     * @see Bean
     * 
     * @param object $bean
     * @return array with key 'success' is set to true.  
     */
    public function sendSuccessWithBean($bean) {
        return $this->sendSuccess($bean->toObject());
    }

    /**
     * Returns a error to the client. 
     * 
     * @param string $msg
     * @return array with key 'success' is set to false and a error message 
     * inside of $return['errors']['reason'] 
     */
    public function sendError($msg) {
        $return = array();
        $return['success'] = false;
        $return['errors'] = array();
        $return['errors']['reason'] = $msg;
        return $return;
    }
}
?>
