<?php

class VWAPTest extends PHPUnit_Framework_TestCase {

  private static $trade;

  public static function setUpBeforeClass() {
    self::$trade = new Trade(json_decode(json_encode(array(
      'userId' => '1234',
      'currencyFrom'=> 'EUR',
      'currencyTo'=> 'GBP',
      'amountSell' => 1000,
      'amountBuy'=> 747.10,
      'rate'=> 0.7471,
      'timePlaced' => '24-JAN-15 10:27:44',
      'originatingCountry' => 'FR'
    ))));
  }

  /**
    * @expectedException         Exception
    * @expectedExceptionMessage  Can't save VWAP for unsaved trade
   */
  public function testAddUnsavedTrade() {
    VWAP::add_trade(self::$trade);
  }

  public function testAddTrade() {
    // will throw an Exception if unsuccessful
    $trade = clone self::$trade;
    $trade->id = 1;
    $trade->created = date('Y-m-d H:i:s');
    VWAP::add_trade($trade);
  }

  public function testTodaysVWAPs() {
    $this->assertNotEmpty(VWAP::todays_vwaps());
  }

}

?>