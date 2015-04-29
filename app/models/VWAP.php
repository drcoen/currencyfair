<?php

/**
 * Class for Volume Weighted Average Price
 * Represents VWAP for currency pairs on a given day
 *
 */
class VWAP {

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

  public static function get_todays_vwaps() {
    $db = Database::getInstance();
    $db->query('SELECT currency_from, currency_to, vwap FROM vwap WHERE TO_DAYS(date) = TO_DAYS(NOW())');
    return $db->fetchAll();
  }
}
?>