<?php

// script to post random trades to the server

$currencies = array(
  array('from' => 'GBP', 'to' => 'EUR', 'rate' => 1.3686),
  array('from' => 'GBP', 'to' => 'USD', 'rate' => 1.5360),
  array('from' => 'GBP', 'to' => 'INR', 'rate' => 97.6445),
  array('from' => 'GBP', 'to' => 'AUD', 'rate' => 1.9408),
  array('from' => 'GBP', 'to' => 'CAD', 'rate' => 1.8533),
  array('from' => 'GBP', 'to' => 'AED', 'rate' => 5.6406),
  array('from' => 'EUR', 'to' => 'GBP', 'rate' => 0.7305),
  array('from' => 'EUR', 'to' => 'USD', 'rate' => 1.1218),
  array('from' => 'EUR', 'to' => 'INR', 'rate' => 71.3053),
  array('from' => 'EUR', 'to' => 'AUD', 'rate' => 1.4180),
  array('from' => 'EUR', 'to' => 'CAD', 'rate' => 1.3540),
  array('from' => 'EUR', 'to' => 'AED', 'rate' => 4.1203),
/*  array('from' => 'USD', 'to' => 'GBP', 'rate' => 0.6513),
  array('from' => 'USD', 'to' => 'EUR', 'rate' => 0.8914),
  array('from' => 'USD', 'to' => 'INR', 'rate' => 63.5726),
  array('from' => 'USD', 'to' => 'AUD', 'rate' => 1.2643),
  array('from' => 'USD', 'to' => 'CAD', 'rate' => 1.2070),
  array('from' => 'USD', 'to' => 'AED', 'rate' => 3.6730),
  array('from' => 'INR', 'to' => 'GBP', 'rate' => 0.0102),
  array('from' => 'INR', 'to' => 'EUR', 'rate' => 0.0140),
  array('from' => 'INR', 'to' => 'USD', 'rate' => 0.0158),
  array('from' => 'INR', 'to' => 'AUD', 'rate' => 0.0199),
  array('from' => 'INR', 'to' => 'CAD', 'rate' => 0.0190),
  array('from' => 'INR', 'to' => 'AED', 'rate' => 0.0577),
  array('from' => 'AUD', 'to' => 'GBP', 'rate' => 0.5150),
  array('from' => 'AUD', 'to' => 'EUR', 'rate' => 0.7049),
  array('from' => 'AUD', 'to' => 'USD', 'rate' => 0.7910),
  array('from' => 'AUD', 'to' => 'INR', 'rate' => 50.2827),
  array('from' => 'AUD', 'to' => 'CAD', 'rate' => 0.9546),
  array('from' => 'AUD', 'to' => 'AED', 'rate' => 2.9049),
  array('from' => 'CAD', 'to' => 'GBP', 'rate' => 0.5397),
  array('from' => 'CAD', 'to' => 'EUR', 'rate' => 0.7386),
  array('from' => 'CAD', 'to' => 'USD', 'rate' => 0.8288),
  array('from' => 'CAD', 'to' => 'INR', 'rate' => 52.6815),
  array('from' => 'CAD', 'to' => 'AUD', 'rate' => 1.0479),
  array('from' => 'CAD', 'to' => 'AED', 'rate' => 3.0441),
  array('from' => 'AED', 'to' => 'GBP', 'rate' => 0.1773),
  array('from' => 'AED', 'to' => 'EUR', 'rate' => 0.2426),
  array('from' => 'AED', 'to' => 'USD', 'rate' => 0.2723),
  array('from' => 'AED', 'to' => 'INR', 'rate' => 17.3056),
  array('from' => 'AED', 'to' => 'AUD', 'rate' => 0.3442),
  array('from' => 'AED', 'to' => 'CAD', 'rate' => 0.3285)*/
);

$countries = array('IE', 'FR', 'NL', 'DE', 'GB', 'US', 'ES', 'PT', 'AU', 'IT', 'GR', 'BE', 'SE', 'AG', 'MX', 'CA', 'JP', 'IN', 'CN', 'HK');

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'http://cf.menucosm'.SERVER_BASE.'/trade');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));


while (1) {

  if (date('H') == 18) {
    exit;
  }

  $i = rand(0, 11);
  $country = rand(0, 19);
  $country = $countries[$country];
  $trade = array(
    'currencyFrom' => $currencies[$i]['from'],
    'currencyTo' => $currencies[$i]['to'],
    'amountSell' => (rand(0, 999) * 1000),
    'rate' => round(($currencies[$i]['rate'] * (rand(90, 110) / 100)), 4),
    'userId' => rand(1000, 999999),
    'timePlaced' => strtoupper(date('d-M-y H:i:s', time())),
    'originatingCountry' => $country
  );
  $amountBuy = $trade['amountSell'] * $trade['rate'];
  $trade['amountBuy'] = $amountBuy;
  post($trade, $ch);
  sleep(10);
}

function post($trade, $ch) {
  $trade = json_encode($trade);
  echo $trade."\n";
  curl_setopt($ch, CURLOPT_POSTFIELDS, $trade);
  $result = curl_exec($ch);
  echo $result."\n\n";
}

?>