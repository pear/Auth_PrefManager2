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
// +----------------------------------------------------------------------+
//
// $Id$

/**
 * Require the core class.
 */
require_once('Auth/PrefManager2/Container.php');

/**#@+
 * Error codes
 */
define('AUTH_PREFMANAGER2_DB_NO_DSN', -3);
define('AUTH_PREFMANAGER2_DB_CONNECT_FAILED', -4);
define('AUTH_PREFMANAGER2_DB_QUERY_FAILED', -5);
/**#@-*/

$_GLOBALS['_Auth_PrefManager2']['err'][AUTH_PREFMANAGER2_DB_NO_DSN] = 'You must provide a DSN to connect to.';
$_GLOBALS['_Auth_PrefManager2']['err'][AUTH_PREFMANAGER2_DB_CONNECT_FAILED] = 'A connection couldn\'t be established with the database.';
$_GLOBALS['_Auth_PrefManager2']['err'][AUTH_PREFMANAGER2_DB_QUERY_FAILED] = 'A database query failed.';

/**
 * The PEAR DB container for Auth_PrefManager2
 *
 * @author Jon Wood <jon@jellybob.co.uk>
 * @package Auth_PrefManager2
 * @category Authentication
 */
class Auth_PrefManager2_Container_DB extends Auth_PrefManager2_Container
{
    /**
     * The DB object being used for data access.
     * 
     * @access private
     * @var DB
     */
    var $_db = null;
        
    /**
     * Sets a value with the container.
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
        if ($this->_exists($owner, $preference, $application)) {
            // If the preference already exists update its value.
            $query = sprintf('UPDATE %s SET %s=%s WHERE %s=%s AND %s=%s AND %s=%s',
                             $this->_options['table'],
                             $this->_options['value_column'],
                             $this->_encodeValue($value),
                             $this->_options['owner_column'],
                             DB::quoteSmart($owner),
                             $this->_options['preference_column'],
                             DB::quoteSmart($preference),
                             $this->_options['application_column'],
                             DB::quoteSmart($application));
        } else {
            // Otherwise insert a new row.
            $query = sprintf('INSERT INTO %s (%s, %s, %s, %s) VALUES(%s, %s, %s, %s)'
                             $this->_options['table'],
                             $this->_options['value_column'],
                             $this->_options['owner_column'],
                             $this->_options['preference_column'],
                             $this->_options['application_column'],
                             $this->_encodeValue($value),
                             DB::quoteSmart($owner),
                             DB::quoteSmart($preference),
                             DB::quoteSmart($application));
        }
        
        $result = $this->_runQuery($query);
        
        if (!is_null($result)) {
            return true;
        }
        
        return false;
    }
    
    /**
     * Gets a value from the container.
     *
     * @param string $owner The owner to set the preference for.
     * @param string $preference The name of the preference to set.
     * @param mixed $value The value to set the preference to.
     * @param string $application The application to set for.
     * @return mixed|null The value, or null if none is set.
     * @access protected
     * @abstract
     */
    function _get($owner, $preference, $application)
    {
        if ($this->_exists($owner, $preference, $application)) {
            // If the preference already exists update its value.
            $query = sprintf('SELECT %s FROM %s WHERE %s=%s AND %s=%s AND %s=%s',
                             $this->_options['value_column',
                             $this->_options['table'],
                             $this->_options['owner_column'],
                             DB::quoteSmart($owner),
                             $this->_options['preference_column'],
                             DB::quoteSmart($preference),
                             $this->_options['application_column'],
                             DB::quoteSmart($application));
        } else {
            return null;
        }
        
        $result = $this->_runQuery($query);
        
        if (!is_null($result)) {
            $row = $result->fetchRow();
            return $row[0];
        }
        
        return null;
    }
    
    /**
     * Deletes a value from the container.
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
        if ($this->_exists($owner, $preference, $application)) {
            // If the preference already exists update its value.
            $query = sprintf('DELETE FROM %s WHERE %s=%s AND %s=%s AND %s=%s',
                             $this->_options['table'],
                             $this->_options['owner_column'],
                             DB::quoteSmart($owner),
                             $this->_options['preference_column'],
                             DB::quoteSmart($preference),
                             $this->_options['application_column'],
                             DB::quoteSmart($application));
        } else {
            // Should this be returning true, since the value is no longer
            // there, or false, because no delete has been done?
            return true;
        }
        
        $result = $this->_runQuery($query);
        
        if (!is_null($result)) {
            return true;
        }
        
        return false;
    }
    
    /**
     * Checks if the specified preference exists in the data container.
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
        $query = sprintf('SELECT COUNT(%s) FROM $s WHERE %s=%s AND %s=%s AND %s=%s',
                         $this->_options['owner_column'],
                         $this->_options['table'],
                         $this->_options['owner_column'],
                         DB::smartQuote($owner),
                         $this->_options['preference_column'],
                         DB::smartQuote($preference),
                         $this->_options['application_column'],
                         DB::smartQuote($application));
                         
        $result = $this->_runQuery($query);
        
        if (!is_null($result)) {
            $count = $result->fetchRow();
            return (bool)$count[0];
        }
        
        return null;
    }
    
    /**
     * Connects to the DSN provided in the options array.
     *
     * @return bool Success/failure
     * @throws AUTH_PREFMANAGER2_DB_CONNECTION_FAILED
     * @access private
     */
    function _connect()
    {
        $db =& DB::connect($this->_options['dsn']);
        
        if (PEAR::isError($db)) {
            $this->_throwError(AUTH_PREFMANAGER2_DB_CONNECT_FAILED, 'error', array('dsn' => $this->_options['dsn']), $db);
            return false;
        }
        
        $this->_db = $db;
        return true;
    }
    
    /**
     * Runs a query on the database.
     *
     * Returns null on error.
     *
     * @param string $query The query to run.
     * @return DB_Result|null The result object for the query.
     * @throws AUTH_PREFMANAGER2_DB_QUERY_FAILED
     * @access private
     * @todo Improve the connection handling here.
     */
    function &_runQuery($query)
    {
        if (is_null($this->_db)) {
            $this->_connect();
        }
        
        if (!is_null($this->_db)) {
            $result = $this->_db->query($query);
            if (DB::isError($result)) {
                $this->_throwError(AUTH_PREFMANAGER2_DB_QUERY_FAILED. 'error', array('query' => $query), $result);
                return null;
            }
            
            return $result;
        }
        
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
        return parent::_encodeValue($value);
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
        return parent::_decodeValue($value)
    }
    
    /**
     * Reads the options array, and sets default values for anything
     * which isn't set.
     *
     * @param array $options An array of options.
     * @return void
     * @access protected
     */
    function _parseOptions($options)
    {
        if (!isset($options['table'])) {
            $options['table'] = 'preferences';
        }
        
        if (!isset($options['owner_column'])) {
            $options['owner_column'] = 'owner';
        }
        
        if (!isset($options['application_column'])) {
            $options['application_column'] = 'application';
        }
        
        if (!isset($options['preference_column'])) {
            $options['preference_column'] = 'name';
        }
        
        if (!isset($options['value_column'])) {
            $options['value_column'] = 'value';
        }
        
        if (!isset($options['dsn'])) {
            $this->_throwError(AUTH_PREFMANAGER2_DB_NO_DSN);
        }
        
        parent::_parseOptions($options);
    }
}
?>
