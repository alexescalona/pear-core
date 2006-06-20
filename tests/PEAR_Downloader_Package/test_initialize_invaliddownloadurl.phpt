--TEST--
PEAR_Downloader_Package->initialize() with invalid downloadable package.tgz
--SKIPIF--
<?php
if (!getenv('PHP_PEAR_RUNTESTS')) {
    echo 'skip';
}
?>
--FILE--
<?php
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'setup.php.inc';
$pathtopackagexml = dirname(__FILE__)  . DIRECTORY_SEPARATOR .
    'test_initialize_invaliddownloadurl'. DIRECTORY_SEPARATOR . 'test-1.0.tgz';
$GLOBALS['pearweb']->addHtmlConfig('http://www.example.com/test-1.0.tgz', $pathtopackagexml);
$dp = &newDownloaderPackage(array());
$phpunit->assertNoErrors('after create');
$result = $dp->initialize('http://www.example.com/test-1.1.tgz');
$phpunit->assertErrors(array(
    array('package' => 'PEAR_Error',
          'message' => 'Could not download from "http://www.example.com/test-1.1.tgz"'),
    array('package' => 'PEAR_Error',
          'message' => 'Invalid or missing remote package file'),
), 'expected errors');

$dd_dir = $dp->_downloader->getDownloadDir();

if (!empty($dd_dir) && is_dir($dd_dir)) {
    $phpunit->assertEquals(array (
  0 => 
  array (
    0 => 0,
    1 => 'Could not download from "http://www.example.com/test-1.1.tgz"',
  ),
), $fakelog->getLog(), 'log messages');

} else {
    $phpunit->assertEquals(array (
  0 => 
  array (
    0 => 3,
    1 => '+ tmp dir created at ' . $dp->_downloader->getDownloadDir(),
  ),
  1 => 
  array (
    0 => 0,
    1 => 'Could not download from "http://www.example.com/test-1.1.tgz"',
  ),
), $fakelog->getLog(), 'log messages');
}

$phpunit->assertEquals(array (
  0 => 
  array (
    0 => 'setup',
    1 => 'self',
  ),
), $fakelog->getDownload(), 'download callback messages');
$phpunit->assertIsa('PEAR_Error', $result, 'after initialize');
$phpunit->assertNull($dp->getPackageFile(), 'downloadable test');


$result = $dp->initialize('http://www.example.com/test-1.0.tgz');
$phpunit->assertErrors(array(
    array('package' => 'PEAR_Error',
          'message' => 'Download of "http://www.example.com/test-1.0.tgz" succeeded, but it is not a valid package archive'),
    array('package' => 'PEAR_Error',
          'message' => 'Invalid or missing remote package file'),
), 'expected errors');
$phpunit->assertEquals(array (
  0 => 
  array (
    0 => 1,
    1 => 'downloading test-1.0.tgz ...',
  ),
  1 =>
  array (
    0 => 1,
    1 => 'Starting to download test-1.0.tgz (213 bytes)',
  ),
  2 =>
  array (
    0 => 1,
    1 => '.',
  ),
  3 =>
  array (
    0 => 1,
    1 => '...done: 213 bytes',
  ),
  4 =>
  array (
    0 => 0,
    1 => 'could not extract the package.xml file from "' . $dp->_downloader->getDownloadDir() .
        DIRECTORY_SEPARATOR . 'test-1.0.tgz"',
  ),
  5 =>
  array (
    0 => 0,
    1 => 'Download of "http://www.example.com/test-1.0.tgz" succeeded, but it is not a valid package archive',
  ),
), $fakelog->getLog(), 'log messages');
$phpunit->assertEquals(array (
  0 => 
  array (
    0 => 'setup',
    1 => 'self',
  ),
  1 =>
  array (
    0 => 'saveas',
    1 => 'test-1.0.tgz',
  ),
  2 =>
  array (
    0 => 'start',
    1 =>
    array (
      0 => 'test-1.0.tgz',
      1 => '213',
    ),
  ),
  3 =>
  array (
    0 => 'bytesread',
    1 => 213,
  ),
  4 =>
  array (
    0 => 'done',
    1 => 213,
  ),
), $fakelog->getDownload(), 'download callback messages');
$phpunit->assertIsa('PEAR_Error', $result, 'after initialize');
$phpunit->assertNull($dp->getPackageFile(), 'downloadable test');
echo 'tests done';
?>
--CLEAN--
<?php
require_once dirname(__FILE__) . '/teardown.php.inc';
?>
--EXPECT--
tests done
