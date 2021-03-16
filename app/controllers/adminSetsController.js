var API_URL = "https://mof-league.com";

if (window.location.hostname == 'localhost') {
    API_URL = "";
}

app.controller('AdminSetsController', function($scope, $http, DTOptionsBuilder, DTColumnBuilder){
    $scope.dtOptions = DTOptionsBuilder.newOptions().withDOM('Plfrtip');
    
    $scope.loadingSets = false;
    $scope.refreshingSets = false;
    $scope.alertText = null;
    
    $scope.sets = [];
    
    $scope.initSets = function() {
        $scope.loadingSets = true;

        $http.get(API_URL + '/api/cards/sets.php').then( function ( response ) {
            $scope.result = response.data.records;
        }, function ( response ) {
            $scope.alertText = response.data.error;
        }).finally(function() {
            $scope.loadingSets = false;
        });
    }

    $scope.importSet = function(row) {
        $http.get(API_URL + '/api/admin/importSet.php/' + row.setId).then( function ( response ) {
            $scope.initSets();
        }, function ( response ) {
            $scope.alertText = response.data.error;
        }).finally(function() {
            
        });
    }

    $scope.refreshSets = function(row) {
        $scope.refreshingSets = true;
        
        $http.get(API_URL + '/api/admin/refreshSets.php').then( function ( response ) {
            $scope.initSets();
        }, function ( response ) {
            $scope.alertText = response.data.error;
        }).finally(function() {
            $scope.refreshingSets = false;
        });
    }

    $scope.closeAlert = function() {
        $scope.alertText = null;
    }

    $scope.initSets();
    
});