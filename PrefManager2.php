<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker: */
// +----------------------------------------------------------------------+
// | Copyright (c) 2004 Jon Wood                                          |
// +----------------------------------------------------------------------+
// | This source file is subject to version 2.02 of the PHP license,      |
// | that is bundled with this package in the file LICENSE, and is        |
// | available at through the world-wide-web at                           |
// | http://www.php.net/license/2_02.txt.                                 |
// | If you did not receive a copy of the PHP license and are unable to   |
// | obtain it through the world-wide-web, please send a note to          |
// | license@php.net so we can mail you a copy immediately.               |
// +----------------------------------------------------------------------+
// | Authors: Jon Wood <jon@jellybob.co.uk>                               |
// |          Paul M. Jones <pmjones@ciaweb.net>                          |
// +----------------------------------------------------------------------+
//
// $Id$

/**
 * Used for error handling.
 */
require_once('PEAR/ErrorStack.php');

/**#@+
 * Error codes
 */
define('AUTH_PREFMANAGER2_CONTAINER_NOT_FOUND', -1);
define('AUTH_PREFMANAGER2_NOT_IMPLEMENTED', -2);
/**#@-*/

/**
 * Auth_PrefManager allows you to store and retrieve preferences from
 * data containers, selecting the default value if none exists for the
 * user you have specified.
 *
 * @author Jon Wood <jon@jellybob.co.uk>
 * @author Paul M. Jones <pmjones@ciaweb.net
 * @package Auth_PrefManager2
 * @category Authentication
 * @version 0.1.0
 * @abstract
 * @todo Add caching support.
 * @todo Write some containers.
 */
class Auth_PrefManager2
{
    /**
     * The language to use for error messages.<br />
     *
     * Possible values<br />
     * <ul>
     *     <li><em>en:</em> English</li>
     * </ul>
     *
     * @var string
     * @access public
     * @since 0.1.0
     */
    var $locale = 'en';

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
     * Error messages.
     *
     * @var array
     * @access protected
     * @since 0.1.0
     * @todo Add an en_US translation :P
     */
    var $_errorMessages = array(
        'en' => array(
            
        )
    );
    
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
    function Auth_PrefManager2($options = array())
    {
        $this->_errorStack =& PEAR_ErrorStack::singleton('Auth_PrefManager2');
        
        if (!isset($options['default_user'])) $options['default_user'] = 'default';
        if (!isset($options['default_app'])) $options['default_app'] = 'default';
        if (!isset($options['serialize'])) $options['serialize'] = true;
        if (!isset($options['cache'])) $options['cache'] = true;
        if (!isset($options['cache_key'])) $options['cache_key'] = '_prefmanager2';
        if (!isset($options['debug'])) $options['debug'] = false;
        
        $this->_options = $options;
    }
    
    /**
     * Creates an instance of PrefManager, with the options specified, and data
     * access being done by the specified container.
     *
     * @param string $container The container to use.
     * @param array $options An associative array of options to pass to the 
     *                       container.
     * @return Auth_PrefManager2 A container, ready to use.
     * @access public
     * @static
     * @throws AUTH_PREFMANAGER2_CONTAINER_NOT_FOUND
     */
    function &factory($container, $options = array())
    {
        $file = "Auth/PrefManager2/${container}.php";

        if ($options['debug']) {
            $include = include_once($file);
        } else {
            $include = @include_once($file);
        }
        
        if ($include) {
            $class = "Auth_PrefManager2_${container}";
            if (class_exists($class)) {
                return new $class($options);
            }
        }
        
        // Something went wrong if we havn't returned by now.
        $this->_throwError(AUTH_PREFMANAGER2_CONTAINER_NOT_FOUND,
                          'error',
                          array('container' => $container,
                                'options' => $options));
    }
    
    /**
     * Returns a reference to an Auth_PrefManager2 object.
     *
     * A new object will only be created if one doesn't already exist with the 
     * same container and options.
     * 
     * @param string $container The container to use.
     * @param array $options An associative array of options to pass to the container.
     * @return Auth_PrefManager2 A container, ready to use.
     * @access public
     * @static
     * @throws AUTH_PREFMANAGER2_CONTAINER_NOT_FOUND
     */
    function &singleton($container, $options = array())
    {
        static $instances;
        if (!isset($instances)) {
            $instances = array();
        }
        
        $key = serialize(array($container, $options));
        if (!isset($instances[$key])) {
            $instances[$key] =& Auth_PrefManager2::singleton($container, $options);
        }
        
        return $instances[$key];
    }
    
    /**
     * Gets a preference for the specified owner and application.
     *
     * @param string $owner The owner to retrieve the preference for.
     * @param string $preference The name of the preference to retrieve.
     * @param string $application The application to retrieve from, if left as 
     *                            null the default application will be used.
     * @param bool $returnDefaults Should a default value be returned if no 
     *                             user preference is available?
     * @return mixed|null The value, or null of no value was available.
     * @access public
     */
    function getPref($owner, $preference, $application = null, $returnDefaults = true)
    {
        if (is_null($application)) {
            $application = $this->_options['default_app'];
        }
        
        if (!is_null($value = $this->_get($owner, $preference, $application))) {
            return $this->_unprepare($value);
        } else {
            if ($returnDefaults && $options['return_defaults']) {
                return $this->getDefaultPref($preference, $application);
            }
        }
    }
    
    /**
     * Gets the default value for the specified preference.
     *
     * @param string $preference The name of the preference to retrieve.
     * @param string $application The application to retrieve from, if left as 
     *                            null the default application will be used.
     * @return mixed|null The value, or null of no value was available.
     * @access public
     */
    function getDefaultPref($preference, $application = null)
    {
        if (is_null($application)) {
            $application = $this->_options['default_app'];
        }
        
        return $this->_unprepare($this->_get($this->_options['default_owner'], $preference, $application));
    }
    
    /**
     * Sets a preference for the specified owner and application.
     *
     * @param string $owner The owner to retrieve the preference for.
     * @param string $preference The name of the preference to retrieve.
     * @param mixed $value The value to set the preference to.
     * @param string $application The application to retrieve from, if left as 
     *                            null the default application will be used.
     * @return bool Success/failure
     * @access public
     */
    function setPref($owner, $preference, $value, $application = null)
    {
        if (is_null($application)) {
            $application = $this->_options['default_app'];
        }
        
        return $this->_set($owner, $preference, $this->_prepare($value), $application);
    }
    
    /**
     * Gets the default value for the specified preference.
     * 
     * @param string $preference The name of the preference to set.
     * @param mixed $value The value to set to.
     * @param string $application The application to set for, if left as 
     *                            null the default application will be used.
     * @return mixed|null The value, or null of no value was available.
     * @access public
     */
    function setDefaultPref($preference, $value, $application = null)
    {
        if (is_null($application)) {
            $application = $this->_options['default_app'];
        }
        
        return $this->_set($this->_options['default_owner'], $preference, $this->_prepare($value), $application);
    }
    
    /**
     * Deletes a preference for the specified owner and application.
     *
     * @param string $owner The owner to delete the preference for.
     * @param string $preference The name of the preference to delete.
     * @param string $application The application to delete from, if left as 
     *                            null the default application will be used.
     * @return bool Success/failure
     * @access public
     */
    function deletePref($owner, $preference, $application = null)
    {
        if (is_null($application)) {
            $application = $this->_options['default_app'];
        }
        
        return $this->_delete($owner, $preference, $application = null);
    }
    
    /**
     * Deletes a preference for the default user.
     *
     * @param string $preference The preference to delete.
     * @param string $application The application to delete from, if left as
     *                            null the default application will be used.
     * @return bool Success/failure
     * @access public
     */
    function deleteDefaultPref($preference, $application = null)
    {
        if (is_null($application))
        {
            $application = $this->_options['default_app'];
        }
        
        return $this->_delete($this->_options['default_owner'], $preference, $application);
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
     * Throws an error, using the current locale if it exists, or en if it doesn't.
     * 
     * @param int $code The error code.
     * @param string $level The level of the error.
     * @param array $params Any other information to include with the error.
     * @return void
     * @access protected
     */
    function _throwError($code, $level = 'error', $params = array())
    {
        $locale = isset($this->_errorMessages[$this->locale]) ? $this->locale : 'en';
        
        $this->_errorStack->push($code, 
                                 'notice',
                                 array('name' => $name,
                                       'value' => $value),
                                 $this->_errorMessages[$locale][$code]);
    }
}
?>
