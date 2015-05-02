<?php

class TradeTest extends PHPUnit_Framework_TestCase {

  private static $trade;

  public static function setUpBeforeClass() {
    self::$trade = json_decode(json_encode(array(
      'userId' => '1234',
      'currencyFrom'=> 'EUR',
      'currencyTo'=> 'GBP',
      'amountSell' => 1000,
      'amountBuy'=> 747.10,
      'rate'=> 0.7471,
      'timePlaced' => '24-JAN-15 10:27:44',
      'originatingCountry' => 'FR'
    )));
  }

  public function testConstructor() {
    $trade = new Trade(self::$trade);
    $this->assertInstanceOf('Trade', $trade);
  }

  /**
    * @expectedException         Exception
    * @expectedExceptionMessage  Missing value for userId
   */
  public function testMissingField() {
    $trade = clone self::$trade;
    unset($trade->userId);
    $trade = new Trade($trade);
  }

  /**
    * @expectedException         Exception
    * @expectedExceptionMessage  Invalid userId
   */
  public function testInvalidUserID() {
    $trade = clone self::$trade;
    $trade->userId = '123456789';
    $trade = new Trade($trade);
  }

  /**
    * @expectedException        Exception
    * @expectedExceptionMessage Invalid currencyFrom
   */
  public function testInvalidCurrency() {
    $trade = clone self::$trade;
    $trade->currencyFrom = '0.999';
    $trade = new Trade($trade);
  }

  /**
    * @expectedException         Exception
    * @expectedExceptionMessage  Invalid amountBuy
   */
  public function testInvalidAmountBuy() {
    $trade = clone self::$trade;
    $trade->amountBuy = '123456789';
    $trade = new Trade($trade);
  }

  /**
    * @expectedException         Exception
    * @expectedExceptionMessage  Invalid rate
   */
  public function testInvalidRate() {
    $trade = clone self::$trade;
    $trade->rate = '0.12345';
    $trade = new Trade($trade);
  }

  /**
    * @expectedException         Exception
    * @expectedExceptionMessage  Invalid timePlace
   */
  public function testInvalidTimePlaced() {
    $trade = clone self::$trade;
    $trade->timePlaced = '01-JAN-2017 00:00:00';
    $trade = new Trade($trade);
  }

  /**
    * @expectedException         Exception
    * @expectedExceptionMessage  amountBuy must equal amountSell * rate
   */
  public function testInvalidInvalidTradeAmounts() {
    $trade = clone self::$trade;
    $trade->amountBuy = 22;
    $trade = new Trade($trade);
  }

  public function testSave() {
    $trade = clone self::$trade;
    $db = Database::getInstance();
    $db->begin();

    $trade = new Trade($trade);
    $trade->save();

    $this->assertObjectHasAttribute('id', $trade);

    $db->rollback();
  }

  public function testQueue() {
    $trade = clone self::$trade;

    $trade = new Trade($trade);
    $id = $trade->queue();

    $this->assertNotEmpty($id);
  }

  public function testMostRecent() {
    $trades = Trade::most_recent();
    $this->assertNotEmpty($trades);
    $this->assertEquals(10, count($trades));
  }

}

?>