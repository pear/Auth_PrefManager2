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
require_once('support/MockContainer.php');

/**
 * Test cases to ensure that the factory and singleton methods work.
 *
 * @author Jon Wood <jon@jellybob.co.uk>
 * @package Auth_PrefManager2
 * @version 0.1.0
 */
class CreationTests extends UnitTestCase
{
    /**
     * Constructor
     * 
     * @access public
     * @return void
     */
    function CreationTests()
    {
        $this->UnitTestCase();
    }
    
    /**
     * Test the factory method.
     *
     * @access public
     * @return void
     * @todo Switch to using a full container once one is done.
     */
    function testFactory()
    {
        $object =& Auth_PrefManager2::factory("Mock");
        $this->assertIsA($object, "Auth_PrefManager2_Mock");
    }
    
    /**
     * Test the factory method.
     *
     * @access public
     * @return void
     * @todo Switch to using a full container once one is done.
     */
    function testSingleton()
    {
        $object =& Auth_PrefManager2::singleton("Mock");
        $reference =& Auth_PrefManager2::singleton("Mock");   
        
        $this->assertReference($object, $reference);
    }
}
?>
