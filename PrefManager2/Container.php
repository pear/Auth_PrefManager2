<?php
require_once('Auth/PrefManager2.php');

class Auth_PrefManager2_Container {

    /**
     * An array of options to use.
     *
     * @var array
     * @access protected
     * @since 0.1.0
     */
    var $_options = array();
    
    /**
     * The PEAR_ErrorStack object to use for error handling.
     *
     * @var PEAR_ErrorStack
     * @access protected
     * @since 0.1.0
     */
    var $_errorStack = null;
    
    /**
     * Constructor
     *
     * Applications should never call this constructor directly, instead 
     * create a container with the factory method.
     *
     * @access protected
     * @param array $options An associative array of options.
     * @return void
     * @see Auth_PrefManager2::&factory()
     */
    function Auth_PrefManager2_Container($options = array())
    {
        $this->_errorStack =& PEAR_ErrorStack::singleton('Auth_PrefManager2');
        $this->_parseOptions($options);
    }
    
    /**
     * Gets a preference for the specified owner and application.
     *
     * @param string $preference The name of the preference to retrieve.
     * @param string $owner The owner to retrieve the preference for.
     * @param string $application The application to retrieve from, if left as 
     *                            null the default application will be used.
     * @param bool $returnDefaults Should a default value be returned if no 
     *                             user preference is available?
     * @return mixed|null The value, or null of no value was available.
     * @access public
     */
    function getPref($preference, $owner = null, $application = null, $returnDefaults = true)
    {
        if (is_null($owner)) {
            $owner = $this->_options['default_owner'];
        }
        
        if (is_null($application)) {
            $application = $this->_options['default_app'];
        }
        
        if (!is_null($value = $this->_get($owner, $preference, $application))) {
            return $this->_decodeValue($value);
        } else {
            if ($returnDefaults && $this->_options['return_defaults'] && ($owner != $this->_options['default_app'])) {
                return $this->getPref($preference, null, $application);
            }
        }
    }
    
    /**
     * Sets a preference for the specified owner and application.
     *
     * @param string $preference The name of the preference to retrieve.
     * @param mixed $value The value to set the preference to.
     * @param string $owner The owner to retrieve the preference for.
     * @param string $application The application to retrieve from, if left as 
     *                            null the default application will be used.
     * @return bool Success/failure
     * @access public
     */
    function setPref($preference, $value, $owner = null, $application = null)
    {
        if (is_null($owner)) {
            $owner = $this->_options['default_owner'];
        }
        
        if (is_null($application)) {
            $application = $this->_options['default_app'];
        }
        
        return $this->_set($owner, $preference, $this->_encodeValue($value), $application);
    }
    
    /**
     * Deletes a preference for the specified owner and application.
     *
     * @param string $preference The name of the preference to delete.
     * @param string $owner The owner to delete the preference for.
     * @param string $application The application to delete from, if left as 
     *                            null the default application will be used.
     * @return bool Success/failure
     * @access public
     */
    function deletePref($preference, $owner = null, $application = null)
    {
        if (is_null($owner)) {
            $owner = $this->_options['default_owner'];
        }
        
        if (is_null($application)) {
            $application = $this->_options['default_app'];
        }
        
        return $this->_delete($owner, $preference, $application);
    }
    
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
        $this->_throwError(AUTH_PREFMANAGER2_NOT_IMPLEMENTED);
        return false;
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
        $this->_throwError(AUTH_PREFMANAGER2_NOT_IMPLEMENTED);
        return false;
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
        $this->_throwError(AUTH_PREFMANAGER2_NOT_IMPLEMENTED);
        return false;
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
        $this->_throwError(AUTH_PREFMANAGER2_NOT_IMPLEMENTED);
        return null;
    }
    
    /**
     * Prepares a value for saving in the data container.
     * Containers that override this method should always call
     * parent::_encodeValue() to do serialization.
     *
     * @param mixed $value The value to prepare.
     * @return mixed The prepared value.
     * @access protected
     */
    function _encodeValue($value)
    {
        if ($this->_options['serialize']) {
            return serialize($value);
        }
        
        return $value;
    }
    
    /**
     * Reverts any preparation that was done to store the value.
     * Containers that override this method should always call 
     * parent::_decodeValue() to do unserialization.
     * 
     * @param mixed $value The value to decode.
     * @return mixed The unprepared value.
     * @access protected
     */
    function _decodeValue($value)
    {
        if ($this->_options['serialize']) {
            return unserialize($value);
        }
    
        return $value;
    }
    
    /**
     * Reads the options array, and sets default values for anything
     * which isn't set.
     *
     * Container classes should override this method, set any defaults
     * that they need, and then pass the options to parent::_parseOptions().
     *
     * @param array $options An array of options.
     * @return void
     * @access protected
     */
    function _parseOptions($options)
    {
        if (!isset($options['default_owner'])) {
            $options['default_owner'] = 'default';
        }
        
        if (!isset($options['default_app'])) {
            $options['default_app'] = 'default';
        }
        
        if (!isset($options['serialize'])) {
            $options['serialize'] = true;
        }
        
        if (!isset($options['cache'])) {
            $options['cache'] = true;
        }
        
        if (!isset($options['cache_key'])) {
            $options['cache_key'] = '_prefmanager2';
        }
        
        if (!isset($options['debug'])) {
            $options['debug'] = false;
        }
        
        if (!isset($options['locale'])) {
            $options['locale'] = 'en';
        }
        
        if (!isset($options['return_defaults'])) {
            $options['return_defaults'] = true;
        }
        
        $this->_options = $options;
    }
    
    /**
     * Throws an error, using the current locale if it exists, or en if it doesn't.
     * 
     * @param int $code The error code.
     * @param string $level The level of the error.
     * @param array $params Any other information to include with the error.
     * @return void
     * @access protected
     */
    function _throwError($code, $level = 'error', $params = array(), $repackage = null)
    {
        $locale = isset($this->_errorMessages['_Auth_PrefManager2'][$this->_options['locale']])
            ? $this->_options['locale']
            : 'en';
            
        var_dump($this->_errorStack->push($code, 
                                 $level,
                                 $params,
                                 $GLOBALS['_Auth_PrefManager2']['err'][$locale][$code],
                                 $repackage));
    }

}

?>
