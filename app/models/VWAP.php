<?php

/**
 * Class for Volume Weighted Average Price
 * Represents VWAP for currency pairs on a given day
 *
 */
class VWAP {

  const CACHE_TIME = 20; // cache for 20 seconds

  /**
   * Update the current day's VWAP for a currency pair with a given trade
   *
   * @param  Trade  $trade  A trade submitted bu the user
   *
   * @return  Bool              Returns 'true' if we're successful
   *
   * @throws  Exception         If any of the fields fail to validate
   *
   */
  public static function add_trade($trade) {
    if (empty($trade->id)) {
      throw new Exception('Can\'t save VWAP for unsaved trade');
    }

    $db = Database::getInstance();
    $table = 'vwap';
    $date = date('Y-m-d', strtotime($trade->created));

    $fields = array(
      'date' => $date,
      'currency_from' => $trade->currencyFrom,
      'currency_to' => $trade->currencyTo,
      'vwap' => $trade->rate,
      'volume' => $trade->amountSell
    );
    $sql = $db->buildInsert($table, $fields);

    $sql .= ' ON DUPLICATE KEY ';

    $db_fields = array(
      'vwap' => '((vwap * volume) + ('.($trade->amountSell * $trade->rate).')) / (volume + '.$trade->amountSell.')',
      'volume' => 'volume + '.$trade->amountSell
    );
    $where = ''; // don't need a WHERE clause for ON DUPLICATE KEY
    $sql .= $db->buildUpdate($table, array(), $db_fields, $where);
    $sql = str_replace(' UPDATE '.$table.' SET ', ' UPDATE ', $sql);

    if (!$db->query($sql)) {
      throw new Exeption('Unable to update VWAP');
    }

    return true;

  }

  /**
   * Get VWAPs for each of today's unique currency pairs/trades and cache for a fixed amount of time
   *
   * @return  Array    2-D array of keyed array, each containing currency_from, currency_to and vwap
   *
   */
  public static function todays_vwaps() {
    $db = Database::getInstance();
    $key = 'vwap-'.date('Y-m-d');
    $cache = new Cache();
    if (!$vwaps = $cache->get($key)) {
      $db->query('SELECT currency_from, currency_to, vwap FROM vwap WHERE TO_DAYS(date) = TO_DAYS(NOW())');
      while ($vwap = $db->fetch()) {
        $vwap['id'] = md5($vwap['currency_from'].'-'.$vwap['currency_to']); // give each one a unique id
        $vwaps[] = $vwap;
      }
      $cache->set($key, $vwaps, self::CACHE_TIME);
    }
    return $vwaps;
  }
}
?>