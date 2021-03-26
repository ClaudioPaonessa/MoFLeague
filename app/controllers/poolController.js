var URL = "https://mof-league.com"

if (window.location.hostname == 'localhost') {
    API_URL = "";
}

app.controller("PoolController", function($scope, $routeParams, $http) {
    
    $scope.tournamentId = $routeParams.tournamentId;
    $scope.loadingPool = true;
    $scope.importing = false;
    $scope.ranking = [];
    $scope.alertText = null;
    $scope.pool = []
    
    $scope.initPool = function() {
        
    }

    $scope.importInitialCardPool = function() {
        $scope.importing = true;

        var params = {
            cardPoolString: $scope.pool.cardPoolString
        }

        $http.post(API_URL + '/api/pool/poolImport.php/' + $scope.tournamentId, params).then( function ( response ) {
            $scope.initPool();

            if (response.data.importErrors.length > 0) {
                $scope.alertText = response.data.importErrors;
            }

            $scope.message = response.data.message;
        }, function ( response ) {
            $scope.alertText = response.data.error;
        }).finally(function() {
            $scope.importing = false;
        });
    }

    $scope.closeAlert = function() {
        $scope.alertText = null;
    }

    $scope.initPool();
});
