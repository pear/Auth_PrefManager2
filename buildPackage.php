<?php
require_once 'PEAR/PackageFileManager.php';
$fm = new PEAR_PackageFileManager();
$options = array(
                'packagefile' => 'package.xml',
                'state' => 'alpha',
                'version' => '0.1',
                'notes' => 'Development snapshot, not for production use.',
                'filelistgenerator' => 'cvs',
                'baseinstalldir' => 'Auth',
                'package' => 'Auth_PrefManager2',
                'summary' => 'summary goes here',
                'description' => 'description',
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
