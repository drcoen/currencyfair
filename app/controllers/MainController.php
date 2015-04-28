<?php

class MainController extends Controller {

  public function index() {
    $title = 'Index';
    include view('header');
    $trade = new Trade(json_decode('{"userId": "134256", "currencyFrom": "EUR", "currencyTo": "GBP", "amountSell": 1000, "amountBuy": 747.10, "rate": 0.7471, "timePlaced" : "24-JAN-15 10:27:44", "originatingCountry" : "FR"}'));
    pre_print_r($trade);
?>
      <div class="starter-template">
        <h1>Bootstrap starter template</h1>
        <p class="lead">Use this document as a way to quickly start any new project.<br> All you get is this text and a mostly barebones HTML document.</p>
      </div>
<?php
    include view('footer');
  }

  public function trade() {
    try {
      $json = json_decode(file_get_contents('php://input'));
      if (empty($json)) {
        throw new Exception('Invalid request data');
      }
      $trade = new Trade($json);
      $trade->queue();
      $response = array('ok' => 1);
    }
    catch (Exception $e) {
      $response = array('error' => $e->getMessage());
    }
    header('Content-Type: application/json');
    echo json_encode($response);
  }

}
?>