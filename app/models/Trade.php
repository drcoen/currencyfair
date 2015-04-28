<?php

class Trade {

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

  public function queue() {
    $pheanstalk = pheanstalk();
    $pheanstalk->useTube(TRADE_QUEUE)->put(json_encode($this));
  }

  public function save() {

    // need to map the JSON names to their DB equivalents
    $this_array = array();
    foreach(self::$fields as $json => $db) {
      $this_array[$db] = $this->$json;
    }

    $db = Database::getInstance();
    if ($db->query($db->buildInsert('trades', $this_array, array('created' => 'NOW()')))) {
      $this->id = $db->lastInsertId();
      derror('id: '.$this->id);
    }
    else {
      throw new Exception('Unable to save to database');
    }
  }

  /**
   * Validate the constructor parameters.
   * The data here can either be in userId (JSON) or user_id (DB) format, need to figure it out
   */
  private function _json_valid($json) {

    foreach (array_keys(self::$fields) as $field) {

      $value = $json->$field;

      // ensure not empty
      if (empty($value)) {
        throw new Exception('Missing value for '.$field);
      }

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
          if (!preg_match('/^[A-Z]{3}$/', $value)) {
            $exception = $field;
          }
          break;

        case 'amountFrom':
        case 'amountTo':
          if (!preg_match('/^[0-9]{1,6}\.[0-9]{2}$/', $value)) {
            $exception = $field;
          }
          break;

        case 'rate':
          if (!preg_match('/^[0-9]{1,6}\.[0-9]{1,4}$/', $value)) {
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

}

?>