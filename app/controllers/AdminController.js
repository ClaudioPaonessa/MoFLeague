var API_URL = "" //"https://mof-league.com"

app.controller('AdminController', function($scope, $http){
    $scope.loading = true;
    $scope.sets = [];
    
    $scope.initSets = function() {
        $http.get(API_URL + '/api/cards/Sets').then( function ( response ) {
            $scope.result = response.data.records;
        }, function ( response ) {
            // TODO: handle the error somehow
        }).finally(function() {
            $scope.loading = false;
        });
    }

    $scope.initSets();
    
});