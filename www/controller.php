<?php
include config('routes');

if($route = $router->matchCurrentRequest()) {

  $controller = new $route->controller();
  $action = $route->action;
  $parameters = $route->getParameters();

  if (!empty($parameters)) {
    $controller->setParameters($parameters);
  }
  
  $controller->$action();

}
else {

  _404();

}

/*$routes = array(
  'index' => array(
    'regex' => '/$|^/index', // '/' or '/index' or '/index/'
    'controller' => 'IndexController',
    'action' => 'index',
    'method' => 'GET'
    // have 'params' => array(1 => 'key_1', 3 => 'key_2') etc., when matched parameters in regex
  ),
  'signup' => array(
    'regex' => '/signup',
    'controller' => 'SignUpController',
    'action' => 'new',
    'method' => 'POST'
  ),
  'contact' => array(
    'regex' => '/contact',
    'controller' => 'ContactController',
    'action' => 'show',
    'method' => 'GET'
  )
);

$route = new Route($_REQUEST['uri'], $_SERVER['REQUEST_METHOD']);
$found = false;

foreach ($routes as $name => $r) {
  Route::definePath($name);

  $param_names = isset($r['params']) ? $r['params'] : array();

  if ($route->match($r['regex'], $r['method'], $param_names)) {
    $controller = new $r['controller']();
    $action = $r['action'];
    $found = true;
  }

}

if ($found) {
  if (count($route->params)) {
    $controller->action($action, $route->params);
  }
  else {
    $controller->action($action);
  }
}
else {
  _404();
}*/

/*switch($_REQUEST['uri']) {
  case INDEX_URL:
    $controller = new IndexController();
    $controller->action('index');
    break;
}*/
?>