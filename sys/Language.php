<?php defined( '_EXEC' ) or die( 'Access denied' );
/**
 * Language Module stores dictionaries for used on site language and
 * tries to translate language variable to choosen or default language
 *
 * @author multifinger
 */

class Language {

  private $site = false;

  private $dictionary = array();

  private $lang = false;

  public function  __construct(Site & $site, $lang=false) {
    $this->site = $site;

    if(!$lang) $lang = current(Config::$languages);
    $this->loadGeneral($lang);
    $this->loadModule($this->site->url->getMod('name'),$lang);
    $this->lang = $lang;
  }

  public function loadGeneral($lang) {
    if (!isset($this->dictionary[$lang])) $this->dictionary[$lang] = array();
    $sql = "SHOW TABLES FROM ". Config::$db_name ." LIKE 'lang_{$lang}'";
    if($this->site->base->Q($sql)) {
      $res = $this->site->base->Q("SELECT * FROM `lang_{$lang}`");
      if (count($res)) {
        foreach ($res as $note) {
          $this->dictionary[$lang][$note['key']] = $note['val'];
        }
      }
    }
  }

  public function loadModule($module,$lang) {
    if (!isset($this->dictionary[$lang])) $this->dictionary[$lang] = array();
    $sql = "SHOW TABLES FROM ". Config::$db_name ." LIKE 'mod_{$module}_{$lang}'";
    if($this->site->base->Q($sql)) {
      $res = $this->site->base->Q("SELECT * FROM `mod_{$module}_{$lang}`");
      if (count($res)) {
        foreach ($res as $note) {
          $this->dictionary[$lang][$note['key']] = $note['val'];
        }
      }
    }
  }

  public function translate($str, $lang=false) {
    if (!$lang) $lang = $this->lang;
    if (isset($this->dictionary[$lang]) && isset($this->dictionary[$lang][$str])) {
        return $this->dictionary[$this->lang][$str];
      }
    return $str;
  }

  public function T($str, $lang=false) {
    return $this->translate($str, $lang=false);
  }

  public function _($str, $lang=false) {
    echo $this->T($str, $lang);
  }

  public function  __toString() {
    return $this->site->url->lang;
  }
}