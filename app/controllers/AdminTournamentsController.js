var API_URL = "https://mof-league.com";

if (window.location.hostname == 'localhost') {
    API_URL = "";
}

app.controller('AdminTournamentsController', function($scope, $http, DTOptionsBuilder, DTColumnBuilder){
    $scope.dtOptions = DTOptionsBuilder.newOptions().withDOM('Plfrtip');
    
    $scope.loading_tournaments = false;
    
    $scope.initTournaments = function() {
        $scope.loading_tournaments = true;

        $http.get(API_URL + '/api/tournament/Tournaments').then( function ( response ) {
            $scope.result = response.data.records;
        }, function ( response ) {
            // TODO: handle the error somehow
        }).finally(function() {
            $scope.loading_tournaments = false;
        });
    }

    $scope.initTournaments();
    
});