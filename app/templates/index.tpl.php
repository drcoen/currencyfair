

<script type="text/javascript" data-main="/js/index.js?<?php echo time(); ?>" src="//cdnjs.cloudflare.com/ajax/libs/require.js/2.1.17/require.min.js"></script>
<script type="text/javascript" src="/js/requirejs.config.js"></script>

<div class="jumbotron">
  <div class="container">
    <h1>Trade Processor</h1>
  </div>
</div>

<div class="container">
  <h2>Today's VWAPs</h2>
  <table class="table" id="vwaps">
    <thead>
      <tr>
        <th>From</th>
        <th>To</th>
        <th>VWAP</th>
      </tr>
    </thead>
    <tbody>
      <!-- data loaded here -->
    </tbody>
  </table>
</div>

<div class="container">
  <h2>Last 10 trades</h2>
  <table class="table" id="trades">
    <thead>
      <tr>
        <th>User ID</th>
        <th>From</th>
        <th>To</th>
        <th>Buy</th>
        <th>Sell</th>
        <th>Rate</th>
        <th>Origin</th>
        <th>Time</th>
      </tr>
    </thead>
    <tbody>
      <!-- data loaded here -->
    </tbodu>
  </table>
</div>