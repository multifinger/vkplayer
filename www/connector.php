<?php
/**
 * Entry point of server-side
 */
define('_EXEC', 1);
define('DS', DIRECTORY_SEPARATOR);
define('WWW', dirname(__FILE__));
define('HOME', dirname(__FILE__) . DS . '..');

function __autoload($sClassName) {
  $bool = require_safe(HOME . DS . 'lib' . DS . $sClassName . '.php');
  if (!$bool)
    $bool = require_safe(HOME . DS . 'sys' . DS . $sClassName . '.php');
  if (!$bool)
    $bool = require_safe(HOME . DS . strtolower($sClassName) . '.php');
  if (!$bool)
    throw new Exception("File {$sClassName}.php not found, class {$sClassName} uninitialized.\n", E_ERROR);
  if (!class_exists($sClassName, false))
    throw new Exception("File {$sClassName}.php found, BUT class {$sClassName} uninitialized.\n", E_ERROR);
}

function require_safe($sPath) {
  if (file_exists($sPath))
    return require_once $sPath;
  else
    return false;
}

try {
  Config::init();
} catch (Exception $e) {
  echo "config file not found";
}
$site = new Site;

exit;