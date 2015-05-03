define(['jquery', 'underscore', 'backbone', 'text!/templates/vwap.tpl', 'color'],
  function($, _, Backbone, vwapTemplate) {
    var VWAP = {};

    VWAP.hightlightAndFade = function (vwap) {
      $('#vwap-' + vwap.id).css({'background-color': '#DFF0D8'}).animate({'background-color': '#fff'}, 3000);
    };

    VWAP.Model = Backbone.Model.extend({
    });

    VWAP.Collection = Backbone.Collection.extend({
      url: '/vwap',
      model: VWAP.Model
    });

    VWAP.Row = Backbone.View.extend({
      el: vwapTemplate,
      initialize: function() {
        this.model.on('change', this.updateRow, this);
      },
      updateRow: function(vwap) {
        var template = _.template(this.$el.html());
        $('#vwap-' + vwap.id).replaceWith(template(vwap.attributes));
        VWAP.hightlightAndFade(vwap);
      }
    });

    VWAP.Table = Backbone.View.extend({
      initialize: function() {
        this.collection.on('add', this.appendVWAP, this);
      },
      appendVWAP: function(vwap) {
        var row = new VWAP.Row({model: vwap}),
            html = _.template(row.$el.html());
        this.$el.append(html(vwap.attributes));
        VWAP.hightlightAndFade(vwap);
      }
    });

    return VWAP;
  }
);