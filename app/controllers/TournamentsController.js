var URL = "https://mof-league.com"

app.controller('TournamentsController', function($scope, $http){
 
    $scope.result = [];
    
    $scope.init = function(){
        $http({
            url: URL + '/api/account/register.php',
            method: 'POST',
            data: $scope.form
        }).then(function(data){
            $scope.result.push(data.data);
        });
    }
     
  });