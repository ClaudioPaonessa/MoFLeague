var API_URL = "https://mof-league.com";

if (window.location.hostname == 'localhost') {
    API_URL = "";
}

app.controller('AdminTournamentsController', function($scope, $http, DTOptionsBuilder, DTColumnBuilder){
    $scope.dtOptions = DTOptionsBuilder.newOptions().withDOM('Plfrtip');
    
    $scope.loading_tournaments = false;
    $scope.selected_tournament_id = -1;
    
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

    $scope.createTournament = function() {
        alert($scope.tournament.name);

        $http.post(API_URL + '/api/admin/CreateTournament.php', $scope.tournament).then( function ( response ) {
            $scope.initTournaments();
        }, function ( response ) {
            // TODO: handle the error somehow
        }).finally(function() {
            
        });
    }

    $scope.deleteSelectedTournament = function() {
        $http.get(API_URL + '/api/admin/DeleteTournament.php/' + $scope.selected_tournament_id).then( function ( response ) {
            $scope.initTournaments();
            $("#deleteConfirm").modal("hide");
        }, function ( response ) {
            // TODO: handle the error somehow
        }).finally(function() {
            
        });
    }

    $scope.deleteModalTournament = function(row) {
        $scope.selected_tournament_id = row.tournament_id;
        $scope.delete_modal_text = "Do you really want to delete the tournament " + row.tournament_name + "?";

        $("#deleteConfirm").modal("show");
    }

    $scope.initTournaments();
    
});