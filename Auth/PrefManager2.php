<?php
/**
 * Used for error handling.
 */
require_once('PEAR/ErrorStack.php');

/**#@+
 * Error codes
 */
define('AUTH_PREFMANAGER2_CONTAINER_NOT_FOUND', -1);
define('AUTH_PREFMANAGER2_NOT_IMPLEMENTED', -2);
define('AUTH_PREFMANAGER2_DB_NO_DSN', -3);
define('AUTH_PREFMANAGER2_DB_CONNECT_FAILED', -4);
define('AUTH_PREFMANAGER2_DB_QUERY_FAILED', -5);
/**#@-*/

/**
* Internationalised error messages
*/
if (!isset($GLOBALS['_Auth_PrefManager2']['err'])) {
    $GLOBALS['_Auth_PrefManager2']['err'] = array(
        'en' => array(
            AUTH_PREFMANAGER2_CONTAINER_NOT_FOUND => 'The container you requested could not be loaded.',
            AUTH_PREFMANAGER2_NOT_IMPLEMENTED => 'This container doesn\'t implement this method.',
            AUTH_PREFMANAGER2_DB_NO_DSN => 'You must provide a DSN to connect to.',
            AUTH_PREFMANAGER2_DB_CONNECT_FAILED => 'The database connection couldn\'t be established.',
            AUTH_PREFMANAGER2_DB_QUERY_FAILED => 'A database query failed.'
        )
    );
}

/**
 * Auth_PrefManager allows you to store and retrieve preferences from
 * data containers, selecting the default value if none exists for the
 * user you have specified.
 *
 * @author Jon Wood <jon@jellybob.co.uk>
 * @author Paul M. Jones <pmjones@ciaweb.net>
 * @package Auth_PrefManager2
 * @category Authentication
 * @version 0.1.0
 * @static
 * @todo Add caching support.
 */
class Auth_PrefManager2
{    
    /**
     * Creates an instance of PrefManager, with the options specified, and data
     * access being done by the specified container.
     *
     * If your using a custom container you can include it before calling
     * the factory method and it will be used without any further setup.
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
        $class = "Auth_PrefManager2_Container_${container}";
        $file = "Auth/PrefManager2/Container/${container}.php";
        
        if (!isset($options['debug'])) {
            $options['debug'] = false;
        }
        
        if (class_exists($class)) {
            $obj =& new $class($options);
            return $obj;
        } else {
            if ($options['debug']) {
                $include = include_once($file);
            } else {
                $include = @include_once($file);
            }
            
            if ($include) {
                $class = "Auth_PrefManager2_Container_${container}";
                if (class_exists($class)) {
                    $obj =& new $class($options);
                    return $obj;
                }
            }
        }
        
        // Something went wrong if we havn't returned by now.
        Auth_PrefManager2::_throwError(AUTH_PREFMANAGER2_CONTAINER_NOT_FOUND,
                          'error',
                          array('container' => $container,
                                'options' => $options));
    }
    
    /**
     * Returns a concrete PrefManager container, making use of an existing one if
     * available.
     *     
     * @param string $container The container to use.
     * @param array $options An associative array of options to pass to the 
     *                       container.
     * @return Auth_PrefManager2 A container, ready to use.
     * @access public
     * @static
     * @throws AUTH_PREFMANAGER2_CONTAINER_NOT_FOUND
     */
    function &singleton($container, $options = array())
    {
        static $instances;
        if (is_null($instances)) {
            $instances = array();
        }
        
        $hash = serialize(array($container, $options));
        if (!isset($instances[$hash])) {
            $instances[$hash] =& Auth_PrefManager2::factory($container, $options);
        }
        
        return $instances[$hash];
    }
    
    /**
     * Throws an error, using the current locale if it exists, or en if it doesn't.
     * 
     * @param int $code The error code.
     * @param string $level The level of the error.
     * @param array $params Any other information to include with the error.
     * @return void
     * @static
     * @access protected
     */
    function _throwError($code, $level = 'error', $params = array(), $repackage = null)
    {
        //$locale = isset($this->_errorMessages['_Auth_PrefManager2']['en']) ? $this->locale : 'en';
        $locale = 'en';
        PEAR_ErrorStack::staticPush('Auth_PrefManager2',
                                    $code, 
                                    $level,
                                    $params,
                                    $GLOBALS['_Auth_PrefManager2']['err'][$locale][$code],
                                    $repackage);
    }
}
?>
