<?php
require_once 'PEAR/PackageFileManager.php';
$fm = new PEAR_PackageFileManager();
$options = array(
                'packagefile' => 'package.xml',
                'state' => 'alpha',
                'version' => '2.0.0dev1',
                'notes' => 'Initial development release.',
                'filelistgenerator' => 'cvs',
                'baseinstalldir' => 'Auth',
                'package' => 'Auth_PrefManager2',
                'summary' => 'Preferences management class',
                'description' => 'Preference Manager is a class to handle user preferences in a web application, looking them up in a table
using a combination of their userid, and the preference name to get a value, and (optionally) returning
a default value for the preference if no value could be found for that user.

Auth_PrefManager2 supports data containers to allow reading/writing with different sources, currently PEAR DB and a simple array based container are supported, although support is planned for an LDAP container as well. If you don\'t need support for different sources, or setting preferences for multiple applications you should probably use Auth_PrefManager instead.',
                'doctype' => 'http://pear.php.net/dtd/package-1.0',
                'packagedirectory' => '/home/jon/pear/Auth_PrefManager2/',
                'license' => 'PHP License',
                'changelogoldtonew' => true,
                'roles' =>
                  array(
                      'php' => 'php',
                      'txt' => 'doc',
                      '*' => 'data',
                       ),
                'dir_roles' =>
                  array(
                      'sql' => 'data',
                      'examples' => 'doc',
                      'tests' => 'test',
                       )
                );
$e = $fm->setOptions($options);
if (PEAR::isError($e)) {
    echo $e->getMessage();
    die();
}
$fm->addMaintainer('jellybob', 'lead', 'Jon Wood', 'jon@jellybob.co.uk');
$fm->addMaintainer('pmjones', 'developer', 'Paul M. Jones', 'pmjones@ciaweb.net');
$e = $fm->writePackageFile();
if (PEAR::isError($e)) {
    echo $e->getMessage();
    die();
}
?>
