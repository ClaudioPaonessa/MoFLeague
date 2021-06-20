var URL = "https://mof-league.com"

if (window.location.hostname == 'localhost') {
    API_URL = "";
}

app.controller("ProfileController", function($scope, $routeParams, $http, $window) {
    
    $scope.accountId = $routeParams.accountId;
    $scope.loadingProfile = true;
    $scope.alertText = null;
    
    $scope.initProfile = function() {
        $scope.loadingProfile = true;

        $http.get(API_URL + '/api/profile/profile.php/' + $scope.accountId).then( function ( response ) {
            $scope.profile = response.data;
        }, function ( response ) {
            if (response.status == 401) {
                $window.location.href = '/auth/login.php';
            }

            $scope.alertText = response.data.error;
        }).finally(function() {
            $scope.loadingProfile = false;
        });
    }

    $scope.closeAlert = function() {
        $scope.alertText = null;
    }

    $scope.initProfile();
});
