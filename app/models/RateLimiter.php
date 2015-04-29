<?php

/**
 * Class to handle rate limiting of requests
 *
 */

class RateLimiter {

  private $ip; // incoming IP address

  private $cache; // Cache object, to store keys

  // 60 requests in 60 seconds for a given IP
  const REQUESTS = 60;
  const TIME = 60;

  /**
   * Constructor
   *
   * @param  String  $ip  IP address
   */
  public function __construct($ip) {
    $this->ip = $ip;
    $this->cache = new Cache();
  }

  /**
   * Check and increment the number of requests in the last TIME seconds for the IP address.
   * Returns true if within the limits
   *
   * @return  Bool    Whether or not the limit has been exceeded
   */
  public function ok() {
    $key = $this->_key();
    if (!$limit = $this->cache->get($key)) {
      $limit = array('time' => time(), 'count' => 0);
    }
    $limit['count']++;
    $time_remaining = self::TIME - (time() - $limit['time']);
    $this->cache->set($key, $limit, $time_remaining);

    return $limit['count'] <= SELF::REQUESTS;
  }

  private function _key() {
    return 'rl-'.md5($this->ip);
  }
}

?>