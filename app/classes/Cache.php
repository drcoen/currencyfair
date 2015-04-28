<?php

class Cache {

  private static $instance;
  private static $mc;

  public function __construct() {
    self::$mc = new Memcache;
    self::$mc->connect('127.0.0.1', 11211);
  }

  public function __destruct() {
    self::$mc->close();
  }

  public static function getInstance() {
    if (!isset(self::$instance)) {
      self::$instance = new Cache();
    }
    return self::$instance;
  }

  public function get($key) {
    return self::$mc->get($key);
  }

  public function set($key, $value, $ttl = 0) {
    return self::$mc->set($key, $value, 0, $ttl);
  }

}
?>