URL = "https://mof-league.com"

if (window.location.hostname == 'localhost') {
    API_URL = "";
    SHAREURL_PART = "http://localhost";
}
else {
    SHAREURL_PART = URL;
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
    $scope.enrichedIncomingTrades = []
    $scope.enrichedOutgoingTrades = []
    $scope.enrichedIncomingTradesPlanned = []
    $scope.enrichedOutgoingTradesPlanned = []
    
    $scope.initPool = function() {
        $scope.loadingPool = true;

        const regex = /\{(.*?)\}/gm;

        $http.get(API_URL + '/api/pool/pool.php/' + $scope.tournamentId).then( function ( response ) {
            $scope.pool = response.data.pool;

            $scope.incomingTrades = response.data.incomingTrades;
            $scope.outgoingTrades = response.data.outgoingTrades;

            $scope.incomingTradesPlanned = response.data.incomingTradesPlanned;
            $scope.outgoingTradesPlanned = response.data.outgoingTradesPlanned;

            $scope.shareStatus = response.data.shareStatus;
            $scope.enrichedPool = []

            if ($scope.shareStatus.poolPublic) {
                $scope.shareStatus.shareUrl = SHAREURL_PART + '/#!participantPool/' + $scope.tournamentId + '?accountId=' + $scope.shareStatus.accountId + '&pin=' + $scope.shareStatus.poolPinCode;
            }
            
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
                    rarityNumeric: card.cardRarityNumeric
                }

                $scope.enrichedPool.push(enrichedCard)
            });

            $scope.incomingTrades.forEach(function(card) {                
                var enrichedCard = {
                    name: card.cardName,
                    cardType: card.cardType,
                    imageUri: card.cardImageUri,
                    imageUriLow: card.cardImageUriLow,
                    imageUriBack: card.cardImageUriBack,
                    imageUriLowBack: card.cardImageUriLowBack,
                    rarity: card.cardRarity,
                    rarityNumeric: card.cardRarityNumeric
                }

                $scope.enrichedIncomingTrades.push(enrichedCard)
            });

            $scope.outgoingTrades.forEach(function(card) {
                var enrichedCard = {
                    name: card.cardName,
                    cardType: card.cardType,
                    imageUri: card.cardImageUri,
                    imageUriLow: card.cardImageUriLow,
                    imageUriBack: card.cardImageUriBack,
                    imageUriLowBack: card.cardImageUriLowBack,
                    rarity: card.cardRarity,
                    rarityNumeric: card.cardRarityNumeric
                }

                $scope.enrichedOutgoingTrades.push(enrichedCard)
            });

            $scope.incomingTradesPlanned.forEach(function(card) {                
                var enrichedCard = {
                    name: card.cardName,
                    cardType: card.cardType,
                    imageUri: card.cardImageUri,
                    imageUriLow: card.cardImageUriLow,
                    imageUriBack: card.cardImageUriBack,
                    imageUriLowBack: card.cardImageUriLowBack,
                    rarity: card.cardRarity,
                    rarityNumeric: card.cardRarityNumeric
                }

                $scope.enrichedIncomingTradesPlanned.push(enrichedCard)
            });

            $scope.outgoingTradesPlanned.forEach(function(card) {
                var enrichedCard = {
                    name: card.cardName,
                    cardType: card.cardType,
                    imageUri: card.cardImageUri,
                    imageUriLow: card.cardImageUriLow,
                    imageUriBack: card.cardImageUriBack,
                    imageUriLowBack: card.cardImageUriLowBack,
                    rarity: card.cardRarity,
                    rarityNumeric: card.cardRarityNumeric
                }

                $scope.enrichedOutgoingTradesPlanned.push(enrichedCard)
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

            if ($scope.shareStatus.poolPublic) {
                $scope.shareStatus.shareUrl = SHAREURL_PART + '/#!participantPool/' + $scope.tournamentId + '?accountId=' + $scope.shareStatus.accountId + '&pin=' + $scope.shareStatus.poolPinCode;
            }
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
        var LENGTH = 8;
        var characters = ['A','B','C','D','E','F','G','H', 'K','L','M','N','P','Q','R','S','T','U','V','W','X','Y','Z', '1', '2', '3', '4', '5', '6', '7', '8', '9'];
        var randomPin = '';

        for (var i=0; i<LENGTH; i++) {
            var rlet = Math.floor(Math.random()*characters.length);
            randomPin += characters[rlet];
        }
        
        $scope.poolSharing.pin = randomPin;
    }

    $scope.copyUrlToClipboard = function() {
        const el = document.createElement('textarea');
        el.value = $scope.shareStatus.shareUrl;
        el.setAttribute('readonly', '');
        el.style.position = 'absolute';
        el.style.left = '-9999px';
        document.body.appendChild(el);
        el.select();
        document.execCommand('copy');
        document.body.removeChild(el);
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

    $scope.closeAlert = function() {
        $scope.alertText = null;
    }

    $scope.initPool();
    $scope.randomizePin();
});
