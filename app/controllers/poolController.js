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
    $scope.enrichedPool = []
    
    $scope.initPool = function() {
        $scope.loadingPool = true;

        const regex = /\{(.*?)\}/gm;

        $http.get(API_URL + '/api/pool/pool.php/' + $scope.tournamentId).then( function ( response ) {
            $scope.pool = response.data.pool;
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
