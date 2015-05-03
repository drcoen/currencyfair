requirejs.config({
  'paths': {
    'jquery': '//cdnjs.cloudflare.com/ajax/libs/jquery/1.11.1/jquery.min',
    'underscore': '//cdnjs.cloudflare.com/ajax/libs/underscore.js/1.8.3/underscore-min',
    'backbone': '//cdnjs.cloudflare.com/ajax/libs/backbone.js/1.1.2/backbone-min',
    'text': '//cdnjs.cloudflare.com/ajax/libs/require-text/2.0.12/text',
    'color': '//cdnjs.cloudflare.com/ajax/libs/jquery-color/2.1.2/jquery.color.min'
  },
  'shim': {
    'backbone': {
      deps: ['underscore', 'jquery'],
      exports: 'Backbone'
    },
    'underscore': {
      exports: '_'
    },
    'jquery': {
      exports: '$'
    },
    'color': {
      deps: ['jquery'],
    }
  }
});