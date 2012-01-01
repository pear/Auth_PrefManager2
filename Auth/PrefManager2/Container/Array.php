<?php
require_once('Auth/PrefManager2/Container.php');

class Auth_PrefManager2_Container_Array extends Auth_PrefManager2_Container
{
    var $_prefs = array();
    
    /**
     * Sets a value with the container.
     * This method should be overridden by container classes to do whatever 
     * needs doing.
     *
     * @param string $owner The owner to set the preference for.
     * @param string $preference The name of the preference to set.
     * @param string $application The application to set for.
     * @return bool Success/failure
     * @access protected
     * @abstract
     */
    function _set($owner, $preference, $value, $application)
    {
        $hash = $this->_hash($application, $owner);
        
        $this->_prefs[$hash][$preference] = $value;
        
        return true;
    }
    
    /**
     * Gets a value from the container.
     * This method should be overridden by container classes to do whatever 
     * needs doing.
     *
     * @param string $owner The owner to set the preference for.
     * @param string $preference The name of the preference to set.
     * @param mixed $value The value to set the preference to.
     * @param string $application The application to set for.
     * @return bool Success/failure
     * @access protected
     * @abstract
     */
    function _get($owner, $preference, $application)
    {
        $hash = $this->_hash($application, $owner);
        if (isset($this->_prefs[$hash][$preference])) {
            return $this->_prefs[$hash][$preference];
        }
        
        return null;
    }
    
    /**
     * Deletes a value from the container.
     * This method should be overridden by container classes to do whatever 
     * needs doing.
     *
     * @param string $owner The owner to delete the preference for.
     * @param string $preference The name of the preference to delete.
     * @param string $application The application to delete from.
     * @return bool Success/failure
     * @access protected
     * @abstract
     */
    function _delete($owner, $preference, $application)
    {
        $hash = $this->_hash($application, $owner);
        if (isset($this->_prefs[$hash][$preference])) {
            unset($this->_prefs[$hash][$preference]);
        }
        
        return true;
    }
    
    /**
     * Checks if the specified preference exists in the data container.
     * This method should be overridden by container classes to do whatever
     * needs doing.
     *
     * Returns null if an error occurs.
     *
     * @param string $owner The owner to delete the preference for.
     * @param string $preference The name of the preference to delete.
     * @param string $application The application to delete from.
     * @return bool Does the pref exist?
     * @access protected
     * @abstract
     */
    function _exists($owner, $preference, $application)
    {
        $hash = $this->_hash($application, $owner);
        return isset($this->_prefs[$hash][$preference]);
    }
    
    /**
     * Creates a hash key for the specified owner and application.
     * 
     * If the owner/application pair hasn't been used before a key will be created
     * for them.
     *
     * @param string $owner The owner.
     * @param application $application The application.
     * @return string The hash.
     */
    function _hash($owner, $application)
    {
        $hash = serialize(array($owner, $application));
        if (!isset($this->_prefs[$hash])) {
            $this->_prefs[$hash] = array();
        }
        
        return $hash;
    }
}

?>
