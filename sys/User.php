<?php defined( '_EXEC' ) or die( 'Access denied' );
/**
 * Description of User
 *
 * @author multifinger
 */
class User {
  
  private $id   = 0;

  private $site = false;

  public function  __construct(Site & $site) {
    $this->site = $site;
    
  }
}
?>
