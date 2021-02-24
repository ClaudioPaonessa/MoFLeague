'use strict';

/* Directives */
angular.module('cardsApp.directives', [])
  .directive('goBack', function($window) {
    return function($scope, $element) {
      $element.on('click', function() {
        $window.history.back();
      });
    }
  });
