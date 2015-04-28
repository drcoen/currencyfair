<?php

class Database
{

  private static $instance;
  protected static $mysqli;
  private static $result;
  private static $debug = false;
  private static $begun = false;

  private function __construct() {
    self::$mysqli = new mysqli(CF_DB_HOST, CF_DB_USER, CF_DB_PW, CF_DB_NAME);
    if (self::$mysqli->connect_error) {
      die('Connect Error (' . self::$mysqli->connect_errno . ') ' . self::$mysqli->connect_error);
    }
  }

  public function __destruct() {
    self::$mysqli->close();
  }

  public static function getInstance() {
    if (!isset(self::$instance)) {
      self::$instance = new Database();
    }
    return self::$instance;
  }

  public function query($sql) {

    if (isset($this->per_page)) {
      $sql .= ' LIMIT '.$this->per_page;
      if (isset($this->page)) {
        $sql .= ' OFFSET '.($this->page * $this->per_page);
        unset($this->page);
      }
      unset ($this->per_page);
    }

    if (isset($this->fetch_total)) {
      $sql = preg_replace('/SELECT /i', 'SELECT SQL_CALC_FOUND_ROWS ', $sql, 1); // 1 here to just replace first instance
      unset($this->fetch_total);
    }

    if (self::$debug) {
      // echo $sql."\n";
      derror($sql);
    }

    self::$result = self::$mysqli->query($sql);
    if (isset(self::$mysqli->errno) && self::$mysqli->errno > 0) {
      $error = "Error with db\nDetails: ".self::$mysqli->error."\n";
      if (self::$begun) {
        $this->rollback();
      }
      die($error);
    }
    return self::$result;
  }

  public function queryTotal() {
    $total = self::$mysqli->query('SELECT FOUND_ROWS()')->fetch_row();
    return $total[0];
  }

  public function fetch($associative = true) {
    $function = ($associative) ? 'fetch_assoc' : 'fetch_row';
    return self::$result->$function();
  }

  public function fetchAll() {
    self::$result->data_seek(0);
    for ($ret = array(); $temp = self::$result->fetch_assoc();) {
      $ret[] = $temp;
    }
    return $ret;
  }

  public function fetchSingle() {
    $assoc = false;
    $val = $this->fetch($assoc);
    return $val[0];
  }

  public function numRows() {
    return self::$mysqli->affected_rows;
  }

  public function buildInsert($table, $fields, $db_fields = array()) {
    foreach ($fields as $key => &$val) {
      if (is_string($val)) {
        $val = self::$mysqli->real_escape_string($val);
      }
      elseif ($val === null) {
        $db_fields[$key] = 'NULL';
        unset($fields[$key]);
      }
    }

    $separator = $quote = '';
    if (count($fields)) {
      $quote = "'";
      if (count($db_fields)) {
        $separator = ', ';
      }
    }

    $sql = 'INSERT INTO ' . $table . ' (' .
      implode(", ", array_keys($fields)) . $separator .
      implode(', ', array_keys($db_fields)) .
      ') VALUES (' .
      $quote . implode('\', \'', array_values($fields)) . $quote . $separator .
      implode(', ', array_values($db_fields)) .
      ')';

    return $sql;
  }

  public function buildUpdate($table, $fields, $db_fields, $where) {
    $sql = 'UPDATE '.$table.' SET ';
    foreach ($fields as $key => $val) {
      if ($val === null) {
        $sql .= $key . " = NULL, ";
      }
      else {
        $sql .= $key . " = '" . $this->realEscapeString($val) . "', ";
      }
    }
    foreach ($db_fields as $key => $val) {
      $sql .= $key . " = " . $this->realEscapeString($val) . ", ";
    }
    $sql = substr($sql, 0, -2).$where;
    return $sql;
  }

  public function buildWhere($fields, $db_fields) {
    $sql = ' WHERE ';
    foreach ($fields as $key => $val) {
      $sql .= $key.' = \''.self::$mysqli->real_escape_string($val).'\' AND ';
    }
    foreach ($db_fields as $key => $val) {
      $sql .= $key.' = '.$val.' AND ';
    }
    return substr($sql, 0, -5);
  }

  public function buildWhereFromArray($where) {
    return implode(' AND ', $where);
  }

  public function insert($table, $fields, $db_fields) {
    return $this->query(
      $this->buildInsert($table, $fields, $db_fields)
    );
  }

  public function update($table, $fields, $db_fields, $where) {
    return $this->query(
      $this->buildUpdate($table, $fields, $db_fields, $where)
    );
  }

  public function begin() {
    self::$mysqli->query('BEGIN');
    self::$begun = true;
  }

  public function commit() {
    self::$mysqli->query('COMMIT');
    self::$begun = false;
  }

  public function rollback() {
    if (self::$begun) {
      self::$mysqli->query('ROLLBACK');
    }
  }

  public function setDebug($debug) {
    self::$debug = $debug;
  }

  public function realEscapeString($str) {
    return str_replace('\n', "\n", self::$mysqli->real_escape_string($str));
  }

  public function selectDb($db) {
    self::$mysqli->select_db($db);
  }

  public function setResult($result) {
    self::$result = $result;
  }

  public function lastInsertId() {
    return self::$mysqli->insert_id;
  }

  public function setPage($page) {
    $this->page = $page;
  }

  public function setPerPage($per_page) {
    $this->per_page = $per_page;
  }

}
?>