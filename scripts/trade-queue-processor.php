<?php

// Script to watch the trade queue and save trades to the database

$pheanstalk = pheanstalk();

$db = Database::getInstance();

while (1) {
  $job = $pheanstalk->watch(TRADE_QUEUE)->reserve();
  try {
    // watch the queue for a new job, decode it when one arrives
    $json = json_decode($job->getData());

    // remove from queue
    $pheanstalk->delete($job);

    $db->begin();

    // create new trade
    $trade = new Trade($json);

    // save to db
    $trade->save();

    // update VWAP for this trade's currency pair
    VWAP::add_trade($trade);

    $db->commit();
  }
  catch (Exception $e) {
    $db->rollback();
    error_log('Error: '.$e->getMessage())."\n";
  }
  usleep(100);
}

?>