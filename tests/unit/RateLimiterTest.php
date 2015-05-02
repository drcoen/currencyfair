<?php

class RateLimiterTest extends PHPUnit_Framework_TestCase {

  private static $ip;

  public static function setUpBeforeClass() {
    self::$ip = '192.168.0.1';
  }

  public static function tearDownAfterClass() {
    $cache = new Cache();
    $cache->set(md5('rl-'.self::$ip), array(), -1);
  }

  public function testLimits() {
    $rl = new RateLimiter(self::$ip);

    for ($i=0; $i<60; $i++) {
      $this->assertTrue($rl->ok());
      usleep(100);
    }

    $this->assertNotTrue($rl->ok());
  }

}

?>