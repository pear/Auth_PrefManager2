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
 * Require the base class.
 */
require_once('Auth/PrefManager2.php');

/**
 * A mock container for testing the factory and singleton methods.
 *
 * @author Jon Wood <jon@jellybob.co.uk>
 * @package Auth_PrefManager2
 * @category Authentication
 * @version 0.1.0
 */
class Auth_PrefManager2_Mock extends Auth_PrefManager2
{
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
    function Auth_PrefManager2_Mock($options = array())
    {
        $this->Auth_PrefManager2($options);
    }
}
?>
