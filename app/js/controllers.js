'use strict';

/* Controllers */

angular.module('cardsApp.controllers', ['ngRoute'])
  .controller('CardsSearchCtrl', ['$scope', '$routeParams', '$location', 'Cards', '_', function($scope, $routeParams, $location, Cards, _) {
    // Update location on form submit
    $scope.search = function() {
      if ($scope.query) {
        $location.path('/search/' + $scope.query);
      }
    };

    // Perform the search query
    if ($routeParams.query) {
      var query = "name m "+$routeParams.query;
      $scope.cards = Cards.query({ method: 'search', q: query }, function() {
        // Group cards by name - avoid duplicates
        $scope.cards = _.chain($scope.cards)
          .groupBy('name')
          .toArray()
          // Now we want to reduce the object & group sets
          .map(function(groupedCards) {
            // set default cardSets property
            return _.chain(groupedCards)
              // sort by latest sets first
              .sortBy('cardSetId').reverse()
              // reduce & group sets
              .reduce(function(memo, card) {
                if (!('cardSets' in memo)) {
                  memo.cardSets = {};
                  memo.cardSets[memo.cardSetId] = memo.cardSetName;
                }

                memo.cardSets[card.cardSetId] = card.cardSetName;

                return memo;
              })
              .value();
          })
          .value();
      });
    }
  }])

  .controller('CardsDetailCtrl', ['$scope', '$routeParams', 'Cards', function($scope, $routeParams, Cards) {
    $scope.card = Cards.get({ query: $routeParams.card });
  }]);
