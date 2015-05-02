<?php

/**
 * Class representing an individual trade, coming from the user
 *
 */

class Trade {

  // array to convert from JSON input to DB equivalent fields
  private static $fields = array(
    'userId' => 'user_id',
    'currencyFrom' => 'currency_from',
    'currencyTo' => 'currency_to',
    'amountSell' => 'amount_sell',
    'amountBuy' => 'amount_buy',
    'rate' => 'rate',
    'timePlaced' => 'time_placed',
    'originatingCountry' => 'originating_country'
  );

  const CACHE_TIME = 20; // 20 seconds

  /**
   * Constructor for a new trade
   *
   * @param   StdClass  $json  Object containg each of the expected JSON fields
   */
  public function __construct($json) {
    if ($this->_json_valid($json)) {
      foreach ($json as $key => $value) {
        if ($key == 'timePlaced') {
          // want to convert this to 'YYYY-MM-DD HH::MM:SS';
          $this->$key = date('Y-m-d H:i:s', strtotime($value));
        }
        else {
          $this->$key = $value;
        }
      }
    }
  }

  /**
   * Add the current object to the processing queue
   *
   * @return  Integer    ID in queue (or null on failure)
   */
  public function queue() {
    $pheanstalk = pheanstalk();
    return $pheanstalk->useTube(TRADE_QUEUE)->put(json_encode($this));
  }

  /**
   * Save the current object to the database.
   * Adds 'id' and 'created' to the object
   *
   * @throws  Exception    If you can't save to the database
   */
  public function save() {

    // need to map the JSON names to their DB equivalents
    $this_array = array();
    foreach(self::$fields as $json => $db) {
      $this_array[$db] = $this->$json;
    }

    $db = Database::getInstance();
    $created = date('Y-m-d H:i:s');
    $this_array['created'] = $created;
    if ($db->query($db->buildInsert('trades', $this_array))) {
      $this->id = $db->lastInsertId();
      $this->created = $created;
    }
    else {
      throw new Exception('Unable to save to database');
    }
  }

  /**
   * Validate the constructor parameters.
   * The data here can either be in userId (JSON) or user_id (DB) format, need to figure it out
   *
   * @param   StdClass   $json  Object containg each of the expected JSON fields
   *
   * @return  Bool              Returns 'true' if we're successful
   *
   * @throws  Exception         If any of the fields fail to validate
   *
   */
  private function _json_valid($json) {

    foreach (array_keys(self::$fields) as $field) {

      // ensure not empty
      if (empty($json->$field)) {
        throw new Exception('Missing value for '.$field);
      }

      $value = $json->$field;

      // ensure valid format
      $exception = null;
      switch ($field) {

        case 'userId':
          if (!preg_match('/^[0-9]{1,6}$/', $value)) {
            $exception = 'userId';
          }
          break;

        case 'currencyFrom':
        case 'currencyTo':
          if (!preg_match('/^[A-Z]{3}$/', (string)$value)) {
            $exception = $field;
          }
          break;

        case 'amountBuy':
        case 'amountSell':
          if (!preg_match('/^[0-9]{1,6}(\.[0-9]{1,2})?$/', (string)$value)) {
            $exception = $field;
          }
          break;

        case 'rate':
          if (!preg_match('/^[0-9]{1,6}(\.[0-9]{1,4})?$/', (string)$value)) {
            $exception = 'rate';
          }
          break;

        case 'timePlaced':
          $time = strtotime($value);
          if (empty(strtotime($value)) || $time > time()) {
            $exception = 'timePlaced';
          }
          break;

        case 'originatingCountry':
          if (!preg_match('/^[A-Z]{2}$/', $value)) {
            $exception = 'originatingCountry';
          }
      }

      if (!empty($exception)) {
        throw new Exception('Invalid '.$exception);
      }
    }

    if ($json->amountBuy != ($json->amountSell * $json->rate)) {
      throw new Exception('amountBuy must equal amountSell * rate');
    }

    return true;
  }

  /**
   * Function to get the 10 most recent trades from today
   *
   * @return  Array    2-D array of keyed array, each containing all the fields from the table
   *
   */
  public static function most_recent() {
    $key = 'tr-most-recent';
    $cache = new Cache();
    $db = Database::getInstance();
    if (!$trades = $cache->get($key)) {
      $db->query('SELECT * FROM trades WHERE TO_DAYS(created) = TO_DAYS(NOW()) ORDER BY id DESC LIMIT 10');
      $trades = array_reverse($db->fetchAll());
      $cache->set($key, $trades, self::CACHE_TIME);
    }
    return $trades;
  }

}

?>