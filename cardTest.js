var app = angular.module("demoApp", []);

app.controller("demoController", function($scope, $http) {
    $http.get("https://api.magicthegathering.io/v1/cards")
    .then(function(response) {
        $scope.result = response.data;
    });
});