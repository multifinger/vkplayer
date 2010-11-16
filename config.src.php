<?php defined( '_EXEC' ) or die( 'Access denied' );
/**
 * Database connection options & all stuff not configured from database
 */
class Config {

    public static $db_type   = "mysql";
    public static $db_host   = "localhost";
    public static $db_port   = "3306";
    public static $db_name   = "your_database_name";
    public static $db_user   = "root";
    public static $db_pass   = "";
    public static $db_chrst  = "utf8";

    public static $languages = array(
        "ru"=>"ru", // <= DEFAULT
      );

    public static $defaultModule    = "page";

    public static $defaultTemplate  = "default";

    /**
     * Disables the new Config() instanciating
     */
    private function __construct() {}

    /**
     * Includes config.php with __autoload()
     */
    public static function init() {}
}

$errarr = array(
        'display_errors' => 'On',
        'display_startup_errors' => 'On',
        'log_errors' => 'On',
        'html_errors' => 'On',
);
foreach($errarr as $inikey => $inival) {
    ini_set($inikey, $inival);
}

date_default_timezone_set('Europe/Moscow');
// configuring error tracking
//ErrorManager::SetLogFile('error.log');
ErrorManager::SetLogLevel(E_ALL | E_STRICT, false);
ErrorManager::SetDebug(true,false);
ErrorManager::SetDieLevel(0x11F5);
function _excHndl($e) {
  ErrorManager::LogException($e);
}
set_exception_handler("_excHndl");
function _errHndl($errno, $errmsg, $filename, $linenum, $vars) {
    ErrorManager::Log($errno, $errmsg, $filename, $linenum, $vars);
}
set_error_handler("_errHndl");

setlocale(LC_ALL, array('rus_rus', 'ru_RU.UTF-8'));