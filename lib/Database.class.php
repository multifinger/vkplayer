<?php defined( '_EXEC' ) or die( 'Access denied' );
/**
 * Description of Database
 *
 * @author multifinger
 */
class DatabaseException extends Exception {

}

class Database {

  private $connection;
  private $username;
  private $password;
  private $dsn;
  private $chrst;

  public function __construct($type,$host,$port,$name,$user,$pass,$chrst="utf-8") {
    $this->dsn      = "{$type}:dbname={$name};host={$host}";
    $this->username = $user;
    $this->password = $pass;
    $this->chrst    = $chrst;
    $this->connect();
  }

  public function  __destruct() {
    $this->connection = null;
  }

  public function setConnection($conn) {
    $this->connection = $conn;
  }

  public function getConnection() {
    return $this->connection;
  }
  /**
   * Make a connection with a database using PDO Object.
   * @param Boolean $errHandle
   */
  public function connect($errHandle=true) {
    if(!$errHandle) {
      try {
        $pdoConnect = new PDO($this->dsn, $this->username, $this->password);
        $this->connection = $pdoConnect;
      } catch (PDOException $e) {
        die("Error connecting database: Connection::connect(): ".$e->getMessage());
      }
    }else {
      try {
        $pdoConnect = new PDO($this->dsn, $this->username, $this->password);
        $this->connection = $pdoConnect;
      } catch (PDOException $e) {
        throw new DatabaseException($e->getMessage(),E_ERROR);
      }
    }
    $this->connection->query('SET NAMES '. $this->chrst);
    $this->connection->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
  }
  /**
   * Execute a DML
   *
   * @param String $query
   */
  public function executeDML($query) {
    if (!$this->getConnection()->query($query)) {
      throw new Error($this->getConnection()->errorInfo());
    } else {
      return true;
    }
  }
  /**
   * Execute a query
   *
   * @param String $query
   * @return PDO ResultSet Object
   */
  public function executeQuery($query) {
    $rs = null;
    if ($stmt = $this->getConnection()->prepare($query)) {
      //echo $query."<br />";
      if ($this->executePreparedStatement($stmt, $rs)) {
        return $rs;
      }
    } else {
      throw new Error($this->getConnection()->errorInfo());
    }
  }

  public function simpleQuery($query) {
    return $this->connection->query($query);
  }

  /**
   * Execute a prepared statement
   * it is used in executeQuery method
   *
   * @param PDOStatement Object $stmt
   * @param Array $row
   * @return boolean
   */
  private function executePreparedStatement($stmt, & $row = null) {
    $boReturn = false;
    if ($stmt->execute()) {
      if ($row = $stmt->fetchAll()) {
        $boReturn = true;
      } else {
        $boReturn = false;
      }
    } else {
      $boReturn = false;
    }
    return $boReturn;
  }

  /**
   * Init a PDO Transaction
   */
  public function beginTransaction() {
    if (!$this->getConnection()->beginTransaction()) {
      throw new Error($this->getConnection()->errorInfo());
    }
  }
  /**
   * Commit a transaction
   *
   */
  public function commit() {
    if (!$this->getConnection()->commit()) {
      throw new Error($this->getConnection()->errorInfo());
    }
  }
  /**
   * Rollback a transaction
   *
   */
  public function rollback() {
    if (!$this->getConnection()->rollback()) {
      throw new Error($this->getConnection()->errorInfo());
    }
  }

  /**
   * Short-named invoke
   *
   * @param  String $query
   * @return PDO ResultSet Object
   */
  public function Q($query) {
    return $this->executeQuery($query);
  }

  public function delete( $tbl, $id ) {

  }

  public function getById( $tbl, $id, $col="id" ) {
    $data = $this->Q( 'SELECT * FROM `'.$tbl.'` '.$where.' '.$else );
    if( count($data)==1 )return $data[0];
    else throw( new DatabaseException(""
              ."Wrong row-number in result: count ". count($data)
              .", expected 1") );
  }

  public function get( $tbl ) {
    $data = $this->Q( 'SELECT * FROM `'.$tbl.'` ' );
    return $data;
  }

  public function insert( $table, $a ) {
		if (is_array($a)) {
      $m="insert into ".$table."(";
      $m2="(";
      foreach($a as $key => $value) {
        $m.='`'.$key."`,";
        $m2.="'$value',";
      }
      $m=substr($m,0,-1);
      $m2=substr($m2,0,-1);
      $m=$m.") values".$m2.")";
      
      $this->simpleQuery($m);
      return $this->connection->lastInsertId();
    }
    return false;
  }

  public function update( $table, $a, $col="id" ) {
    if (is_array($a)) {

      if (!$a[$col]||$a[$col]=='') return false;
      $m="update ".$table." set ";
      foreach($a as $key => $value) {
        $m.='`'.$key."` = '".$value."', ";
      }
      $m=substr($m,0,-2);
      $m=$m." where $col=".$a[$col];
      $this->simpleQuery($m);
      return true;
    }
    return false;
  }
}