<?php

$router = new Router();

$router->map('/', 'main#index', array('methods' => 'GET', 'name' => 'index'));

$router->map('/trade', 'main#trade', array('methods' => 'POST', 'name' => 'trade'));

?>