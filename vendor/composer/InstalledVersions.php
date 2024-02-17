<?php











namespace Composer;

use Composer\Autoload\ClassLoader;
use Composer\Semver\VersionParser;






class InstalledVersions
{
private static $installed = array (
  'root' => 
  array (
    'pretty_version' => 'dev-master',
    'version' => 'dev-master',
    'aliases' => 
    array (
    ),
    'reference' => '8279bf1ebda457da87302b685755c841d25a034a',
    'name' => '__root__',
  ),
  'versions' => 
  array (
    '__root__' => 
    array (
      'pretty_version' => 'dev-master',
      'version' => 'dev-master',
      'aliases' => 
      array (
      ),
      'reference' => '8279bf1ebda457da87302b685755c841d25a034a',
    ),
    'facebook/graph-sdk' => 
    array (
      'pretty_version' => '5.7.0',
      'version' => '5.7.0.0',
      'aliases' => 
      array (
      ),
      'reference' => '2d8250638b33d73e7a87add65f47fabf91f8ad9b',
    ),
    'nesbot/carbon' => 
    array (
      'pretty_version' => '2.46.0',
      'version' => '2.46.0.0',
      'aliases' => 
      array (
      ),
      'reference' => '2fd2c4a77d58a4e95234c8a61c5df1f157a91bf4',
    ),
    'pelago/emogrifier' => 
    array (
      'pretty_version' => 'v3.1.0',
      'version' => '3.1.0.0',
      'aliases' => 
      array (
      ),
      'reference' => 'f6a5c7d44612d86c3901c93f1592f5440e6b2cd8',
    ),
    'stripe/stripe-php' => 
    array (
      'pretty_version' => 'v7.95.0',
      'version' => '7.95.0.0',
      'aliases' => 
      array (
      ),
      'reference' => 'ed372a1f6430b06dda408bb33b27d2be35ceaf07',
    ),
    'symfony/css-selector' => 
    array (
      'pretty_version' => 'v5.3.4',
      'version' => '5.3.4.0',
      'aliases' => 
      array (
      ),
      'reference' => '7fb120adc7f600a59027775b224c13a33530dd90',
    ),
    'symfony/deprecation-contracts' => 
    array (
      'pretty_version' => 'v2.4.0',
      'version' => '2.4.0.0',
      'aliases' => 
      array (
      ),
      'reference' => '5f38c8804a9e97d23e0c8d63341088cd8a22d627',
    ),
    'symfony/polyfill-mbstring' => 
    array (
      'pretty_version' => 'v1.23.1',
      'version' => '1.23.1.0',
      'aliases' => 
      array (
      ),
      'reference' => '9174a3d80210dca8daa7f31fec659150bbeabfc6',
    ),
    'symfony/polyfill-php80' => 
    array (
      'replaced' => 
      array (
        0 => '*',
      ),
    ),
    'symfony/translation' => 
    array (
      'pretty_version' => 'v5.3.7',
      'version' => '5.3.7.0',
      'aliases' => 
      array (
      ),
      'reference' => '4d595a6d15fd3a2c67f6f31d14d15d3b7356d7a6',
    ),
    'symfony/translation-contracts' => 
    array (
      'pretty_version' => 'v2.4.0',
      'version' => '2.4.0.0',
      'aliases' => 
      array (
      ),
      'reference' => '95c812666f3e91db75385749fe219c5e494c7f95',
    ),
    'symfony/translation-implementation' => 
    array (
      'provided' => 
      array (
        0 => '2.3',
      ),
    ),
    'twilio/sdk' => 
    array (
      'pretty_version' => '6.28.1',
      'version' => '6.28.1.0',
      'aliases' => 
      array (
      ),
      'reference' => '8d389e82b2bef32cad093cec362c5b190f7de58b',
    ),
  ),
);
private static $canGetVendors;
private static $installedByVendor = array();







public static function getInstalledPackages()
{
$packages = array();
foreach (self::getInstalled() as $installed) {
$packages[] = array_keys($installed['versions']);
}


if (1 === \count($packages)) {
return $packages[0];
}

return array_keys(array_flip(\call_user_func_array('array_merge', $packages)));
}









public static function isInstalled($packageName)
{
foreach (self::getInstalled() as $installed) {
if (isset($installed['versions'][$packageName])) {
return true;
}
}

return false;
}














public static function satisfies(VersionParser $parser, $packageName, $constraint)
{
$constraint = $parser->parseConstraints($constraint);
$provided = $parser->parseConstraints(self::getVersionRanges($packageName));

return $provided->matches($constraint);
}










public static function getVersionRanges($packageName)
{
foreach (self::getInstalled() as $installed) {
if (!isset($installed['versions'][$packageName])) {
continue;
}

$ranges = array();
if (isset($installed['versions'][$packageName]['pretty_version'])) {
$ranges[] = $installed['versions'][$packageName]['pretty_version'];
}
if (array_key_exists('aliases', $installed['versions'][$packageName])) {
$ranges = array_merge($ranges, $installed['versions'][$packageName]['aliases']);
}
if (array_key_exists('replaced', $installed['versions'][$packageName])) {
$ranges = array_merge($ranges, $installed['versions'][$packageName]['replaced']);
}
if (array_key_exists('provided', $installed['versions'][$packageName])) {
$ranges = array_merge($ranges, $installed['versions'][$packageName]['provided']);
}

return implode(' || ', $ranges);
}

throw new \OutOfBoundsException('Package "' . $packageName . '" is not installed');
}





public static function getVersion($packageName)
{
foreach (self::getInstalled() as $installed) {
if (!isset($installed['versions'][$packageName])) {
continue;
}

if (!isset($installed['versions'][$packageName]['version'])) {
return null;
}

return $installed['versions'][$packageName]['version'];
}

throw new \OutOfBoundsException('Package "' . $packageName . '" is not installed');
}





public static function getPrettyVersion($packageName)
{
foreach (self::getInstalled() as $installed) {
if (!isset($installed['versions'][$packageName])) {
continue;
}

if (!isset($installed['versions'][$packageName]['pretty_version'])) {
return null;
}

return $installed['versions'][$packageName]['pretty_version'];
}

throw new \OutOfBoundsException('Package "' . $packageName . '" is not installed');
}





public static function getReference($packageName)
{
foreach (self::getInstalled() as $installed) {
if (!isset($installed['versions'][$packageName])) {
continue;
}

if (!isset($installed['versions'][$packageName]['reference'])) {
return null;
}

return $installed['versions'][$packageName]['reference'];
}

throw new \OutOfBoundsException('Package "' . $packageName . '" is not installed');
}





public static function getRootPackage()
{
$installed = self::getInstalled();

return $installed[0]['root'];
}







public static function getRawData()
{
return self::$installed;
}



















public static function reload($data)
{
self::$installed = $data;
self::$installedByVendor = array();
}




private static function getInstalled()
{
if (null === self::$canGetVendors) {
self::$canGetVendors = method_exists('Composer\Autoload\ClassLoader', 'getRegisteredLoaders');
}

$installed = array();

if (self::$canGetVendors) {
foreach (ClassLoader::getRegisteredLoaders() as $vendorDir => $loader) {
if (isset(self::$installedByVendor[$vendorDir])) {
$installed[] = self::$installedByVendor[$vendorDir];
} elseif (is_file($vendorDir.'/composer/installed.php')) {
$installed[] = self::$installedByVendor[$vendorDir] = require $vendorDir.'/composer/installed.php';
}
}
}

$installed[] = self::$installed;

return $installed;
}
}
