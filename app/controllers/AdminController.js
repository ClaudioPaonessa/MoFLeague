var API_URL = "https://mof-league.com";

app.controller('AdminController', function($scope, $http){
    $scope.loading_sets = false;
    $scope.refreshing_sets = false;
    
    $scope.sets = [];
    
    $scope.initSets = function() {
        $scope.loading_sets = true;

        $http.get(API_URL + '/api/cards/Sets').then( function ( response ) {
            $scope.result = response.data.records;
        }, function ( response ) {
            // TODO: handle the error somehow
        }).finally(function() {
            $scope.loading_sets = false;
        });
    }

    $scope.importSet = function(row) {
        $http.get(API_URL + '/api/admin/ImportSet.php/' + row.set_id).then( function ( response ) {
            $scope.initSets();
        }, function ( response ) {
            // TODO: handle the error somehow
        }).finally(function() {
            
        });
    }

    $scope.refreshSets = function(row) {
        $scope.refreshing_sets = true;
        
        $http.get(API_URL + '/api/admin/RefreshSets.php').then( function ( response ) {
            $scope.initSets();
        }, function ( response ) {
            // TODO: handle the error somehow
        }).finally(function() {
            $scope.refreshing_sets = false;
        });
    }

    $scope.initSets();
    
});