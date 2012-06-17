<?php

/**
 * ClassNotFoundException
 * ======================
 * Provides Services for the ClassLoader
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
class ClassLoaderService extends WebService {

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

?>
