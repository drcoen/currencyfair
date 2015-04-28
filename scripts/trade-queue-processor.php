<?php

// Script to watch the trade queue and save trades to the database

$pheanstalk = pheanstalk();

while(1) {
  $job = $pheanstalk->watch(TRADE_QUEUE)->reserve();
  try {
    $json = json_decode($job->getData());
    $pheanstalk->delete($job);
    $trade = new Trade($json);
    $trade->save();
  }
  catch (Exception $e) {
    error_log('Error: '.$e->getMessage())."\n";
  }
  usleep(100);
}

?>