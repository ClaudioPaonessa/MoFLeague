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
    $scope.poolSharing = []
    $scope.enrichedPool = []
    
    $scope.initPool = function() {
        $scope.loadingPool = true;

        const regex = /\{(.*?)\}/gm;

        $http.get(API_URL + '/api/pool/pool.php/' + $scope.tournamentId).then( function ( response ) {
            $scope.pool = response.data.pool;
            $scope.shareStatus = response.data.shareStatus;
            $scope.enrichedPool = []
            
            $scope.pool.forEach(function(card) {
                var manaRegex = card.cardManaCost.match(regex);
                
                var enrichedCard = {
                    name: card.cardName,
                    typeLine: card.cardTypeLine,
                    numberOfCards: card.numberOfCards,
                    mana: manaRegex ? manaRegex.map(m => m.slice(1, -1).replace('/', '')) : [],
                    colorIdentity: card.cardColorIdentity,
                    cardType: card.cardType,
                    imageUri: card.cardImageUri,
                    rarity: card.cardRarity,
                    rarityNumeric: card.cardRarityNumeric
                }

                $scope.enrichedPool.push(enrichedCard)
            });

        }, function ( response ) {
            $scope.alertText = response.data.error;
        }).finally(function() {
            $scope.loadingPool = false;
        });
    }

    $scope.initPoolShareStatus = function() {
        $http.get(API_URL + '/api/pool/poolShareStatus.php/' + $scope.tournamentId).then( function ( response ) {
            $scope.shareStatus = response.data.shareStatus;
        }, function ( response ) {
            $scope.alertText = response.data.error;
        }).finally(function() {
            
        });
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

    $scope.sharePool = function() {
        var params = {
            poolPinCode: $scope.poolSharing.pin
        }

        $http.post(API_URL + '/api/pool/poolShare.php/' + $scope.tournamentId, params).then( function ( response ) {
            $scope.initPoolShareStatus();
        }, function ( response ) {
            $scope.alertText = response.data.error;
        }).finally(function() {
            
        });
    }

    $scope.stopSharePool = function() {
        $http.post(API_URL + '/api/pool/poolStopShare.php/' + $scope.tournamentId).then( function ( response ) {
            $scope.initPoolShareStatus();
        }, function ( response ) {
            $scope.alertText = response.data.error;
        }).finally(function() {
            
        });
    }

    $scope.randomizePin = function() {
        var characters = ['A','B','C','D','E','F','G','H', 'K','L','M','N','P','Q','R','S','T','U','V','W','X','Y','Z', '1', '2', '3', '4', '5', '6', '7', '8', '9'];
        var randomPin = '';

        for (var i=0; i<4; i++) {
            var rlet = Math.floor(Math.random()*characters.length);
            randomPin += characters[rlet];
        }
        
        $scope.poolSharing.pin = randomPin;
    }

    $scope.closeAlert = function() {
        $scope.alertText = null;
    }

    $scope.initPool();
    $scope.randomizePin();
});
