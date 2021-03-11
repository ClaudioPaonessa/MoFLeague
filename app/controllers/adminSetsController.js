var API_URL = "https://mof-league.com";

if (window.location.hostname == 'localhost') {
    API_URL = "";
}

app.controller('AdminSetsController', function($scope, $http, DTOptionsBuilder, DTColumnBuilder){
    $scope.dtOptions = DTOptionsBuilder.newOptions().withDOM('Plfrtip');
    
    $scope.loadingSets = false;
    $scope.refreshingSets = false;
    
    $scope.sets = [];
    
    $scope.initSets = function() {
        $scope.loadingSets = true;

        $http.get(API_URL + '/api/cards/sets.php').then( function ( response ) {
            $scope.result = response.data.records;
        }, function ( response ) {
            // TODO: handle the error somehow
        }).finally(function() {
            $scope.loadingSets = false;
        });
    }

    $scope.importSet = function(row) {
        $http.get(API_URL + '/api/admin/importSet.php/' + row.set_id).then( function ( response ) {
            $scope.initSets();
        }, function ( response ) {
            // TODO: handle the error somehow
        }).finally(function() {
            
        });
    }

    $scope.refreshSets = function(row) {
        $scope.refreshingSets = true;
        
        $http.get(API_URL + '/api/admin/refreshSets.php').then( function ( response ) {
            $scope.initSets();
        }, function ( response ) {
            // TODO: handle the error somehow
        }).finally(function() {
            $scope.refreshingSets = false;
        });
    }

    $scope.initSets();
    
});