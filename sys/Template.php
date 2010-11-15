<?php defined( '_EXEC' ) or die( 'Access denied' );
/**
 * Simple Template
 * for single-template sites with high load
 *
 * @author multifinger
 */

class Template {

  private $template = false;

  private $site     = false;

  private $dataKeys = false;

  private $path     = false;

  public function  __construct( $site, $name=false, $path=false ) {
    $this->site = $site;
    if( $name&&$path ) $this->load( $name, $path );
  }

  public function load( $name, $path ) {
    $this->template = file_get_contents( $path.DIRECTORY_SEPARATOR.$name.DIRECTORY_SEPARATOR."index.php" );
    $this->path = DIRECTORY_SEPARATOR.'tpl'.DS.$name.DIRECTORY_SEPARATOR;
    //ob_start();
    //require_once $path.DIRECTORY_SEPARATOR.$name.DIRECTORY_SEPARATOR."index.php";
    //if (isset($dataKeys) && is_array($dataKeys)) $this->dataKeys = $dataKeys;
    //$this->template = ob_get_contents();
    //ob_end_clean();
  }

  // deprecated for string search
  private function replace( $var, $content ) {
    $this->template = str_replace( "#$var#", $content, $this->template );
  }

  /**
   * Prints template with data inserted
   *
   * @param array $data array()
   * @return void
   */
  public function display(array $data=array()) {
    //foreach ( $this->dataKeys as $key ) if (!isset($data[$key])) {
    //  throw new TemplateException("Nessesary variable \${$key} not defined before rendering template", E_ERROR);
    //}
    $lang = $this->site->lang;
    extract($data);
    //ErrorManager::Trace($data);
    eval( "?>". $this->template );
  }

  public function getPath() {
    return $this->path;
  }
}

class TemplateException extends Exception {}