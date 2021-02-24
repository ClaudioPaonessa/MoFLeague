'use strict';

/* Services */


// Demonstrate how to register services
// In this case it is a simple value service.
var cardServices = angular.module('cardsApp.services', ['ngResource'])
  .config(function($httpProvider){delete $httpProvider.defaults.headers.common['X-Requested-With'];})
  
  .factory('Cards', ['$resource', function($resource){
    return $resource('https://api.magicthegathering.io/v1/:method/:query', {method: 'cards', query: '', limit: 100}, {
      // custom query methods
    });
  }])

  .factory('_', function() {
    return window._;
  });
