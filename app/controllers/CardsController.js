var API_URL = "https://api.magicthegathering.io/v1/"

app.controller('CardsController', function($scope, $http){
    $scope.loading = true;
    $scope.result = [];
    
    $scope.init = function() {
        $http.get(API_URL + 'sets?page=' + 1 + '&pageSize=10').then( function ( response ) {
            $scope.result = response.data.sets;
        }, function ( response ) {
            // TODO: handle the error somehow
        }).finally(function() {
            $scope.loading = false;
        });
    }

    $scope.init();
    
});