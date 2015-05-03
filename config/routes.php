<?php

$router = new Router();

$router->map('/', 'main#index', array('methods' => 'GET', 'name' => 'index'));

$router->map('/trade', 'main#trade', array('methods' => 'POST', 'name' => 'trade'));

$router->map('/vwap', 'main#vwap', array('methods' => 'GET', 'name' => 'vwap'));
$router->map('/trades', 'main#trades', array('methods' => 'GET', 'name' => 'trades'));

?>