'use strict';


// Declare app level module which depends on filters, and services
angular.module('cardsApp', [
  'ngRoute',
  // 'cardsApp.filters',
  'cardsApp.services',
  'cardsApp.directives',
  'cardsApp.controllers'
]).
config(['$routeProvider', function($routeProvider) {
  $routeProvider
  	.when('/', {
      templateUrl: 'partials/index.html',
      controller: 'CardsSearchCtrl'
    })
  	.when('/search/:query', {
  		templateUrl: 'partials/card-list.html', 
  		controller: 'CardsSearchCtrl'
  	})
  	.when('/cards/:card', {
      templateUrl: 'partials/card-detail.html',
      controller: 'CardsDetailCtrl'
    })
  	.otherwise({
  		redirectTo: '/'
  	});
}]);
