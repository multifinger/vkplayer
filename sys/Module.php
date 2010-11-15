<?php defined( '_EXEC' ) or die( 'Access denied' );
/**
 * Module is responsible for action asked in URL
 * www.your.domain/module/action/params?else=params
 *
 * Module is an abstract class, initializing base site functional:
 * language, database, templating, etc... And asking module-developers
 * to implement default action.
 *
 * defaultAction() calls than no action is specified in URL
 * 
 * @author multifinger
 */
abstract class Module {

  /**
   * Page name variable
   */
  protected $name     = false;

  /**
   * Page title variable
   */
  protected $title    = false;

  /**
   * Module content variable
   */
  protected $content  = false;

  /**
   * Link to site object
   */
  protected $site     = false;

  /**
   * Display flag
   */
  protected $display  = false;


  /**
   * Provides access to sites methods
   */
  public function  __construct( Site & $site ) {
    $this->site = $site;
  }

  /**
   * Must be implemented in each module
   * Calls if no params or actions are specified in URL
   */
  abstract public function defaultAction( $argv );

  /**
   * Used with __autoload() to declare Class
   */
  public static function init() {}

  public function __get( $prop ) {
    $method = 'get'. strtoupper( substr( $prop, 0, 1 ) ) . substr( $prop, 1 );
    if ( method_exists( $this, $method ) ) return $this->$method();
    else return null;
  }

  /**
   * Safe method to get generated content
   */
  public function getContent() {
    return $this->content;
  }

  /**
   * Safe method to get display-flag
   */
  public function getDisplay() {
    return $this->display;
  }

  /**
   * Deprecated, use static Module::L() instead
   */
  public function _($str, $lang=false) {
    return $this->site->lang->_($str);
  }
  /**
   * Short synonym for Language::_($str [,$lang])
   * Translates language variable into choosen language
   */
  public static function L($str, $lang=false) {
    return $this->site->lang->_($str);
  }
}

class ModuleException extends Exception {}