var URL = "https://mof-league.com"

app.controller('AccountController', function($scope, $http){
 
    $scope.result = [];
    
    $scope.register = function(){
        $http({
            url: URL + '/api/account/register.php',
            method: 'POST',
            data: $scope.form
        }).then(function(data){
            $scope.result.push(data.data);
        });
    }
     
  });