<?php defined( '_EXEC' ) or die( 'Access denied' );
/**
 * Parsing URL & get params
 *
 * @author multifinger
 */

class Router {

  private $url   = array();

  private $sUrl  = false;

  private $args  = array();

  private $sArgs = false;

  private $lang  = false;

  private $mod   = false;

  private $act   = false;

  private $site  = false;

  private $domain      = false;

  private $subdomain   = false;

  private $serverName  = false;

  private $defaultLang = false;


  /**
   * Implements URL-parcing logic, searches for default module & action or check
   * if requested operation is supported by system.
   *
   * TODO: implement user authorization & access to requested operation
   *
   * Router truth:
   * 
   * 1) DEFAULT_MODULE && DEFUALT_ACTION WORKS ONLY FOR EMPTY URL
   * 
   * 2) DEFUALT_ACTION WORKS ONLY WITH NO PARAMS
   *
   * 3) PARAM KEY CAN'T BE NUMERIC OR MATCH WITH OTHER KEY
   *
   * 4) PARAM IN URL-PATH GOES LIKE ../KEY1:VALUE1/KEY2/KEY3:VALUE3/..
   *
   * 5) PARAM IN GET-PATH GOES LIKE ..?KEY1=VALUE1&KEY2&KEY3=VALUE3
   *
   * 6) PARAM WITH NO VALUE PARCED LIKE PARAM WITH VALUE OF KEY //was int(1)
   *
   * 7) DEFAULT LANGUAGE IS THE FIRTS VALUE IN CONFIG ARRAY
   */
  public function __construct( Site & $site ) {
    // site
    $this->site = $site;
    
    if (isset($_SERVER['REQUEST_URI'])) {
      $s = explode("?", $_SERVER['REQUEST_URI']);
      // sUrl sArgs
      $this->sUrl = $s[0];
      if (isset($s[1])&&$s[1]) $this->sArgs = $s[1];
    }

    // url
    // $u = array_diff(explode('/', $this->sUrl), array(''));       // original key order
    $u = preg_split('@/@', $this->sUrl, NULL, PREG_SPLIT_NO_EMPTY); // new key order [0,1,..]
    if (count($u)) foreach ($u as $r) {
      $this->insertUrl($r);
    }
    reset($this->url);


    // domain
    if(isset($_SERVER['HTTP_HOST'])) {
      $this->domain = $_SERVER['HTTP_HOST'];
    }

    // serverName
    if(isset($_SERVER['HTTP_HOST'])) {
      $this->serverName = @$_SERVER['SERVER_NAME'];
    }

    // subdomain
    $this->subdomain = substr(
      str_replace($this->domain, '', $this->serverName),
      0, -1 );

    // defaultLang
    $zone = explode(".", $this->domain);
    $zone = end($zone);
    $this->defaultLang = isset(Config::$languages[$zone]) ?
      Config::$languages[$zone]:
      current(Config::$languages);
    // lang
    if (isset(Config::$languages[$this->subdomain])) {
      // by subdomain
      $this->lang = $this->subdomain;
    } elseif (isset(Config::$languages[current($this->url)])) {
      // by first url record
      $this->lang = current($this->url);
      next($this->url);
    } else {
      // default
      $this->lang = current(Config::$languages);
    }

    // mod
    // by current url record
    $sql = "SELECT `id`, `name` FROM `modules`WHERE `name`='". current($this->url) ."'";
    $res = $site->base->Q($sql);
    if (count($res)!=1) {
      // mod not in url => default module
      // requires empty url&&args or action in default module
      $defModClass = self::getModClass(Config::$defaultModule);
      require_once HOME.DS.'mod'.DS.Config::$defaultModule.DS."index.php";
      $defModAndAct = is_callable(array(new $defModClass($this->site), current($this->url)));
      $empty = count($this->url) + count($this->args);
      if ($defModAndAct || !$empty) {
        $sql = "SELECT `id`, `name` FROM `modules` WHERE `name`='". Config::$defaultModule ."'";
        $res = $site->base->Q($sql);
      }
    } else {
      // mod in url, moving to next url record
      next($this->url);
    }
    if (count($res)!=1) {
      throw new RouterException("Module not found", E_ERROR);
    }
    $this->mod = current( $res );
    $this->mod['class'] = strtoupper(substr($this->mod['name'], 0, 1)) . substr($this->mod['name'], 1) ."Module";
    // Include mod.file and look for action
    if (file_exists(HOME.DS."mod".DS.$this->mod['name'].DS."index.php")) {
      require_once HOME.DS."mod".DS.$this->mod['name'].DS."index.php";
    }
    else {
      throw new RouterException("Module files not found in ".DS."mod".DS."{$this->mod['name']}", E_ERROR);
    }
    
    // act (default if no params)
    if (!current($this->url)) {
      $this->act = "defaultAction";
    } elseif (is_callable(array(new $this->mod['class']($this->site), current($this->url)))) {
      $this->act = current($this->url);
      next($this->url);
    } else {
      throw new RouterException("Action ". current($this->url) ." not found in class ".$this->mod['class'], E_ERROR);
    }

    // args form url
    while (current($this->url)) {
      $p = explode(":", current($this->url));
      $this->insertArgs($p);
      next($this->url);
    }

    // args from GET
    if ($this->sArgs) {
      $a = preg_split('@&@', $this->sArgs, NULL, PREG_SPLIT_NO_EMPTY);
      if (count($a)) foreach ($a as $p) {
        $p = explode("=", $p);
        $this->insertArgs($p);
      }
    }
    
  }

  public function __get( $prop ) {
    $method = 'get'. strtoupper( substr( $prop, 0, 1 ) ) . substr( $prop, 1 );
    if ( method_exists( $this, $method ) ) return $this->$method();
    else return null;
  }

  public function getLang() {
    return $this->lang;
  }

  public function getMod($key=false) {
    if($key && in_array($key, array('id','name'))) {
      return $this->mod[$key];
    }
    return $this->mod;
  }

  public function getAct($methodList=array()) {
    if ($this->act) return $this->act;
    // default action with no params
    if (!current($this->url) && !count($this->args)) {
      return $this->act = "defaultAction";
    }
    // act in current url
    elseif (in_array(current($this->url), $methodList)) {
      $this->act = current( $this->url );
      next( $this->url );
      return $this->act;
    }
    // strict act initialization *
    if (isset($_GET['act']) && in_array($_GET['act'], $methodList)) {
      $this->act = $_GET['act'];
      return $this->act;
    }
    return $this->act;
  }

  public function getUrl() {
    return $this->url;
  }

  public function getSUrl() {
    return $this->sUrl;
  }

  public function getArgs() {
    return $this->args;
  }

  /**
   * Uses regular expression with preg_match to secure strings
   * Returns match or false
   *
   * @param String $regExp
   * @param String $str
   * @return String or False
   */
  public function secureString( $regExp, $str ) {
    if( preg_match($regExp, $str, $matches) ) {
      // Cyrillic chars corruption trace
      // ErrorManager::Trace($str);
      // ErrorManager::Trace("preg_match('$regExp', '$str', '\$matches')");
      // ErrorManager::Trace($matches);
      return(current($matches));
    }
    else return false;
  }

  /**
   * Returns module class name
   *
   * @param String $mod
   * @return String
   */
  public static function getModClass($mod) {
    return strtoupper(substr($mod, 0, 1)) . substr($mod, 1) . "Module";
  }

  /**
   * Check inserted arg & add into $this->args
   * Numeric keys && coinsedense keys will be thrown with error
   *
   * @param Array( "key' [, "val" ] ) $p
   */
  private function insertArgs($p) {
    if (is_array($p)&&count($p)) {
      //foreach ($p as $k=>$v) $p[$k] = $this->secureString('/^[a-zа-яё0-9_-]+$/is', urldecode($v));
      //foreach ($p as $k=>$v) $p[$k] = $this->secureString('/^[a-zA-Zа-яА-ЯёЁ0-9_-]+$/is', $v);
      foreach ($p as $k=>$v) $p[$k] = $this->stripSqlXss($v);
      if ($p[0]) {
        if (is_numeric($p[0])) {
          throw new RouterException("Value \"".$p[0]."\" can't be numeric", E_ERROR);
        }
        if (isset($this->args[$p[0]])) {
          throw new RouterException("Url arguments conflict with key [".$p[0]."]", E_ERROR);
        }
        // Добавление переменной прошедшей проверки
        if (isset($p[1])) $this->args[$p[0]] = $p[1];
        //else $this->args[$p[0]] = 1;
        else $this->args[$p[0]] = $p[0];
      }
    }
  }
  
  /**
   * Check inserted record & add into $this->url
   * /:a-z0-9_-/ pattern for url-record used
   *
   * @param String $p
   */
  private function insertUrl($p) {
    //$p = $this->secureString('/[:a-zA-Zа-яА-ЯёЁ0-9_-]+/D', urldecode($p));
    // Problems with cyrillic chars: output string corrupted
    if ($p) array_push($this->url, $p);
  }
  
  /**
   * Prevands XSS & SQL injections
   *
   * @param String $str
   * @return String
   */
  private function stripSqlXss($str) {
    $str = str_replace("'","", $str);
    $str = str_replace("&","", $str);
    $str = str_replace("+","", $str);
    $str = str_replace("/","", $str);
    $str = str_replace("*","", $str);
    $str = urldecode($str);
    $str = htmlspecialchars($str);
    return $str;
  }
}

class RouterException extends Exception {}