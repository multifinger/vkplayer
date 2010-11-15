<?php defined( '_EXEC' ) or die( 'Access denied' );

/**
 * Implements site-level methods and properties
 *
 * @author  multifinger
 * @version
 */
class Site {

  /**
   * Parced url args, url like ~/lang/mod/act/args
   * @var Router $url
   */
  public $url  = null;

  /**
   * Access to actions of current module
   * @var Module $mod
   */
  public $mod  = null;

  /**
   * PDO based DB-connector
   * @var Database $base
   */
  public $base = null;

  /**
   * Handles translation of strings
   * @var Language $lang
   */
  public $lang = null;

  /**
   * Load & display template
   * @var Template  $view
   */
  public $view = null;

  /**
   * Stores texts for template like
   *  $templateName=>$stringTextValue
   * @var Array $data
   */
  public $data = array();

  /**
   *
   */
  public function __construct() {

    session_start();
    
    if (!defined('HOME'))  define('HOME', dirname($_SERVER["SCRIPT_FILENAME"]));
    if (!defined('DS'))    define('DS', DIRECTORY_SEPARATOR);

    try {
      $this->base = new Database(
        Config::$db_type, Config::$db_host, Config::$db_port,
        Config::$db_name, Config::$db_user, Config::$db_pass, Config::$db_chrst);
    } catch (DatabaseException $e) {
      ErrorManager::LogException($e);
    }

    try {
      $this->url  = new Router($this);
    } catch (RouterException $e) {
      if ($e->getMessage()=="Module not found") self::notFound(false,"<h1>404 Not found</h1>");
      else ErrorManager::LogException($e);
    }

    try {
      $chosenLng = $this->url->lang ?
        $this->url->lang :
        Config::$defaultLanguage ;
      $this->lang = new Language($this, $chosenLng);
    } catch (LanguageException $e) {
      ErrorManager::LogException($e);
    }

    try {
      $this->mod = new $this->url->mod["class"]($this);
    } catch (ModuleException $e) {
      ErrorManager::LogException($e);
    }

    try {
      $this->view = new Template($this, Config::$defaultTemplate, WWW.DS."tpl");
    } catch (TemplateException $e) {
      ErrorManager::LogException($e);
    }

    $act = $this->url->getAct(get_class_methods(get_class($this->mod)));
    if (!$act) self::notFound(false, "<h1>404 Not found</h1>");
    if (method_exists($this->mod, $act) /*&& $this->url->checkAccess($chosenMod,$act)*/) {
      try {
        $this->data = $this->mod->$act($this->url->args);
      } catch (ModuleError $e) {
        ErrorManager::LogException($e);
      }
    }
    else {
      self::notFound();
    }

    if ($this->mod->display) {
      $this->view->display((array)$this->data);
    }
  }

  public function  __destruct() {
    
  }

  private static function notFound($file=false, $msg=null) {
    header("HTTP/1.0 404 Not Found");
    //header( "Status: 404 Not Found" );
    if ($file && is_file(HOME.DS.$file)) include HOME.DS.$file;
    //elseif (is_file(HOME.DS.'404.html')) include HOME.DS.'404.html';
    //elseif (is_file(HOME.DS.'404.htm' )) include HOME.DS.'404.htm';
    //elseif (is_file(HOME.DS.'404.php' )) include HOME.DS.'404.php';
    die($msg);
  }
}

class SiteException extends Exception {}