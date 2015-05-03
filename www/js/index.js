requirejs([
  // These are path alias that we configured in our bootstrap
  'jquery', '/js/VWAP.js', '/js/trade.js'],
  function($, VWAP, Trade) {

    var vwaps = new VWAP.Collection();
    var vwap_table = new VWAP.Table({el: $('#vwaps > tbody'), collection: vwaps});

    var trades = new Trade.Collection();
    var trades_table = new Trade.Table({el: $('#trades > tbody'), collection: trades});

    vwaps.fetch();
    trades.fetch();

    setInterval(function() {
      vwaps.fetch();
      trades.fetch();
    }, 2000);

  });