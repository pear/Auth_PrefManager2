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
 * Require Auth_PrefManager2 for testing.
 */
require_once('Auth/PrefManager2.php');

/**
 * Test cases to ensure that the logic for returning default prefs if no
 * specific ones are available.
 *
 * @author Jon Wood <jon@jellybob.co.uk>
 * @package Auth_PrefManager2
 * @version 0.1.0
 */
class CommonTests extends UnitTestCase
{
    /**
     * Constructor
     * 
     * @access public
     * @return void
     */
    function CommonTests()
    {
        $this->UnitTestCase();
    }
    
    /**
     * Test the setting and retrieval of a basic preference.
     *
     * @access public
     * @return void
     */
    function testUserPref()
    {
        $object =& Auth_PrefManager2::factory("Array");
        
        $this->assertTrue($object->setPref('email', 'test@example.com', 'test'));
        $this->assertEqual($object->getPref('email', 'test'), 'test@example.com');
    }
    
    /**
     * Test the setting and retrieval of a default preference.
     *
     * @access public
     * @return void
     */
    function testDefaultPref()
    {
        $object =& Auth_PrefManager2::factory("Array");
        
        $this->assertTrue($object->setPref('email', 'test@example.com'));
        $this->assertEqual($object->getPref('email', 'test'), 'test@example.com');
    }
    
    /**
     * Test the use of applications.
     *
     * @access public
     * @return void
     */
    function testApplicationPref()
    {
        $object =& Auth_PrefManager2::factory("Array");
        
        $this->assertTrue($object->setPref('email', 'test@example.com', 'test'));
        $this->assertTrue($object->setPref('email', 'test-lists@example.com', 'test', 'mailinglist'));
        $this->assertEqual($object->getPref('email', 'test'), 'test@example.com');
        $this->assertEqual($object->getPref('email', 'test', 'mailinglist'), 'test-lists@example.com');
    }
}
?>
