<?php

class MainController extends Controller {

  public function index() {
    $title = 'Index';

    include view('header');
    include view('index');
    include view('footer');
  }

  public function trade() {
    try {

      // first check rate limit
      $rl = new RateLimiter($_SERVER['REMOTE_ADDR']);
      if (!$rl->ok()) {
        throw new Exception('Rate limit exceeded');
      }

      // next, get input
      $json = json_decode(file_get_contents('php://input'));
      if (empty($json)) {
        throw new Exception('Invalid request data');
      }

      // this will validate the input
      $trade = new Trade($json);

      // all ok => add to processing queue
      $trade->queue();

      $response = array('ok' => 1);
    }
    catch (Exception $e) {
      $response = array('error' => $e->getMessage());
    }
    header('Content-Type: application/json');
    echo json_encode($response);
  }

  public function vwap() {
    header('Content-Type: application/json');
    echo json_encode(VWAP::todays_vwaps());
  }

  public function trades() {
    header('Content-Type: application/json');
    echo json_encode(Trade::most_recent());
  }

}
?>