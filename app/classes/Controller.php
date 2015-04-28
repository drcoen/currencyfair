<?php

class Controller {

  protected $parameters;
  
  public function __construct() {

  }

  public function action($view, $params=array()) {
    $this->{$view}($params);
  }

  public function setParameters($parameters) {
    unset($parameters['uri']); // not needed
    $this->parameters = $parameters;
  }
}

?>