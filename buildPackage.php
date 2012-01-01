<?php
require_once 'PEAR/PackageFileManager2.php';
$fm = new PEAR_PackageFileManager2();
$options = array(
                'packagefile' => 'package.xml',
                'filelistgenerator' => 'svn',
                'baseinstalldir' => 'Auth',
                'packagedirectory' => dirname(__FILE__),
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
$fm->setPackage('Auth_PrefManager2');
$fm->setPackageType('php');
$fm->setSummary('Preferences management class');

$fm->setDescription('Preference Manager is a class to handle user preferences in a web application, looking them up in a table
using a combination of their userid, and the preference name to get a value, and (optionally) returning
a default value for the preference if no value could be found for that user.

Auth_PrefManager2 supports data containers to allow reading/writing with different sources, currently PEAR DB and a simple array based container are supported, although support is planned for an LDAP container as well. If you don\'t need support for different sources, or setting preferences for multiple applications you should probably use Auth_PrefManager instead.');

$fm->setChannel('pear.php.net');
$fm->setReleaseVersion('2.0.0dev1');
$fm->setReleaseStability('alpha');
$fm->setAPIVersion('2.0.0dev1');
$fm->setAPIStability('alpha');
$fm->setNotes('Initial development release.');

$fm->addRelease();
$fm->setPhpDep('4.2.0');
$fm->setPearinstallerDep('1.4.0a12');

$fm->addMaintainer('lead', 'jellybob', 'Jon Wood', 'jon@jellybob.co.uk');
$fm->addMaintainer('developer', 'pmjones', 'Paul M. Jones', 'pmjones@ciaweb.net');
$fm->setLicense('PHP License');

$fm->generateContents();

//$fm->debugPackageFile();

$e = $fm->writePackageFile();
if (PEAR::isError($e)) {
    echo $e->getMessage();
    die();
}
?>
