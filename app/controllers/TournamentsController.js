app.controller("TournamentsController", function($scope, $http) {
    
    $scope.result = []
    
    $scope.init = function() {
        $http.get("https://api.magicthegathering.io/v1/cards")
        .then(function(response) {
            $scope.result = response.data.cards;
        });
    }

    $scope.init();
});
