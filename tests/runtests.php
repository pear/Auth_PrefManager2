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
 * Auth_PrefManager2 test suite runner.
 * @author Jon Wood <jon@jellybob.co.uk>
 */
 
/**
 * Base class for unit tests.
 */
require_once('simpletest/unit_tester.php');

/**
 * Reporter output classes.
 */
require_once('simpletest/reporter.php');

$test = &new GroupTest('All tests');

$test->run(new TextReporter());
?>
