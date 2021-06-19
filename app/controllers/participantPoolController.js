var URL = "https://mof-league.com"

if (window.location.hostname == 'localhost') {
    API_URL = "";
}

app.controller("ParticipantPoolController", function($scope, $routeParams, $location, $http, $window) {
    
    $scope.tournamentId = $routeParams.tournamentId;
    $scope.accountId = $location.search().accountId;
    $scope.pin = $location.search().pin;

    $scope.loadingPool = true;
    $scope.alertText = null;
    $scope.pool = []
    $scope.enrichedPool = []
    
    $scope.initPool = function() {
        $scope.loadingPool = true;

        const regex = /\{(.*?)\}/gm;

        var params = {
            accountId: $scope.accountId,
            pin: $scope.pin
        }

        $http.post(API_URL + '/api/pool/poolParticipant.php/' + $scope.tournamentId, params).then( function ( response ) {
            $scope.pool = response.data.pool;
            $scope.displayName = response.data.displayName;
            $scope.enrichedPool = []
            
            var cardIdx = 0;

            $scope.pool.forEach(function(card) {
                if (card.numberOfCards < 1) {
                    return;
                }

                var manaRegex = card.cardManaCost.match(regex);
                
                var enrichedCard = {
                    name: card.cardName,
                    typeLine: card.cardTypeLine,
                    numberOfCards: card.numberOfCards,
                    mana: manaRegex ? manaRegex.map(m => m.slice(1, -1).replace('/', '')) : [],
                    colorIdentity: card.cardColorIdentity,
                    cardType: card.cardType,
                    imageUri: card.cardImageUri,
                    imageUriLow: card.cardImageUriLow,
                    imageUriBack: card.cardImageUriBack,
                    imageUriLowBack: card.cardImageUriLowBack,
                    rarity: card.cardRarity,
                    rarityNumeric: card.cardRarityNumeric,
                    marked: false,
                    idx: cardIdx++
                }

                $scope.enrichedPool.push(enrichedCard)
            });

        }, function ( response ) {
            if (response.status == 401) {
                $window.location.href = '/auth/login.php';
            }

            $scope.alertText = response.data.error;
        }).finally(function() {
            $scope.loadingPool = false;
        });
    }

    $scope.filterItemsRarity = {
        'common': false,
        'uncommon': false,
        'rare': false,
        'mythic': false
    };

    $scope.itemsRarity = [
        { name: 'common', displayName: 'Common' }, 
        { name: 'uncommon', displayName: 'Uncommon' }, 
        { name: 'rare', displayName: 'Rare' },
        { name: 'mythic', displayName: 'Mythic' }
    ];

    $scope.rarityFilter = function (card) {
        return Object.values($scope.filterItemsRarity).every(v => v === false) || $scope.filterItemsRarity[card.rarity];
    };

    $scope.toggleMarkCard = function(cardIdxSearch) {
        $scope.enrichedPool[cardIdxSearch].marked = !$scope.enrichedPool[cardIdxSearch].marked;
    }

    $scope.closeAlert = function() {
        $scope.alertText = null;
    }

    $scope.sum = function(items, prop) {
        return items.reduce( function(a, b){
            return parseInt(a) + parseInt(b[prop]);
        }, 0);
    }

    $scope.initPool();
});
