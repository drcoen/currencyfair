define(['jquery', 'underscore', 'backbone', 'text!/templates/trade.tpl', 'color'],
  function($, _, Backbone, tradeTemplate) {
    var Trade = {};

    Trade.hightlightAndFade = function (trade) {
      $('#trade-' + trade.id).css({'background-color': '#DFF0D8'}).animate({'background-color': '#fff'}, 3000);
    };

    Trade.hightlightAndRemove = function (trade) {
      $('#trade-' + trade.id).css({'background-color': '#F2DEDE'}).fadeOut(3000, function () {
        $(this).remove();
      });
    };

    Trade.Model = Backbone.Model.extend({
    });

    Trade.Collection = Backbone.Collection.extend({
      url: '/trades',
      model: Trade.Model
    });

    Trade.Row = Backbone.View.extend({
      el: tradeTemplate,
      initialize: function() {
        this.model.on('remove', this.removeRow, this);
      },
      removeRow: function(trade) {
        Trade.hightlightAndRemove(trade);
      }
    });

    Trade.Table = Backbone.View.extend({
      initialize: function() {
        this.collection.on('add', this.prependTrade, this);
      },
      prependTrade: function(trade) {
        var row = new Trade.Row({model: trade}),
            html = _.template(row.$el.html());
        this.$el.prepend(html(trade.attributes));
        Trade.hightlightAndFade(trade);
      }
    });

    return Trade;
  }
);