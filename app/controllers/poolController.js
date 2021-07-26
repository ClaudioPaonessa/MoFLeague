URL = "https://mof-league.com"

if (window.location.hostname == 'localhost') {
    API_URL = "";
    SHAREURL_PART = "http://localhost";
}
else {
    SHAREURL_PART = URL;
}

app.controller("PoolController", function($scope, $routeParams, $http, $window) {
    
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
    $scope.enrichedReceivedCardPacks = []

    $scope.copyMessage = "";

    $scope.reimport = false;
    
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
            $scope.enrichedIncomingTrades = []
            $scope.enrichedOutgoingTrades = []
            $scope.enrichedIncomingTradesPlanned = []
            $scope.enrichedOutgoingTradesPlanned = []
            $scope.enrichedReceivedCardPacks = []

            $scope.receivedCardPacks = response.data.receivedCardPacks;

            if ($scope.shareStatus.poolPublic) {
                $scope.shareStatus.shareUrl = SHAREURL_PART + '/#!participantPool/' + $scope.tournamentId + '?accountId=' + $scope.shareStatus.accountId + '&pin=' + $scope.shareStatus.poolPinCode;
            }
            
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
                    manaStr: card.cardManaCost,
                    colorIdentity: card.cardColorIdentity,
                    cardType: card.cardType,
                    imageUri: card.cardImageUri,
                    imageUriLow: card.cardImageUriLow,
                    imageUriBack: card.cardImageUriBack,
                    imageUriLowBack: card.cardImageUriLowBack,
                    rarity: card.cardRarity,
                    rarityNumeric: card.cardRarityNumeric,
                    marked: false,
                    idx: cardIdx++,
                    identityW: card.cardColorIdentity.includes("W"),
                    identityU: card.cardColorIdentity.includes("U"),
                    identityB: card.cardColorIdentity.includes("B"),
                    identityR: card.cardColorIdentity.includes("R"),
                    identityG: card.cardColorIdentity.includes("G"),
                    identityC: card.cardColorIdentity.length == 0
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

            $scope.receivedCardPacks.forEach(function(card) {
                var enrichedCard = {
                    roundName: card.roundName,
                    name: card.cardName,
                    cardType: card.cardType,
                    imageUri: card.cardImageUri,
                    imageUriLow: card.cardImageUriLow,
                    imageUriBack: card.cardImageUriBack,
                    imageUriLowBack: card.cardImageUriLowBack,
                    rarity: card.cardRarity,
                    rarityNumeric: card.cardRarityNumeric
                }

                $scope.enrichedReceivedCardPacks.push(enrichedCard)
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

    $scope.reimportAllowed = function() {
        $scope.reimport = true;
    }

    $scope.arenaExport = function() {
        $http.get(API_URL + '/api/pool/poolExport.php/' + $scope.tournamentId).then( function ( response ) {
            $scope.arenaString = response.data.arenaString;
            $scope.copyDeckToClipboard();
        }, function ( response ) {
            $scope.alertText = response.data.error;
        }).finally(function() {
            
        });
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

    $scope.copyDeckToClipboard = function() {
        if (navigator.userAgent.search("Safari") >= 0 && navigator.userAgent.search("Chrome") < 0) {
            navigator.clipboard.writeText($scope.arenaString).then(function() {
                $scope.copyMessage = "Deck copied to clipboard!";
            }, function() {
                $scope.copyMessage = "Failed to copy deck to clipboard!";
            });
        }
        else {
            const el = document.createElement('textarea');
            el.value = $scope.arenaString;
            el.setAttribute('readonly', '');
            el.style.position = 'absolute';
            el.style.left = '-9999px';
            document.body.appendChild(el);
            el.select();
            document.execCommand('copy');
            document.body.removeChild(el);

            $scope.copyMessage = "Deck copied to clipboard!";
        }
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

    $scope.filterItemsColorIdentity = {
        'W': false,
        'U': false,
        'B': false,
        'R': false,
        'G': false,
        'C': false
    };

    $scope.itemsColorIdentity = [
        { name: 'W', displayName: 'White' }, 
        { name: 'U', displayName: 'Blue' }, 
        { name: 'B', displayName: 'Black' },
        { name: 'R', displayName: 'Red' },
        { name: 'G', displayName: 'Green' },
        { name: 'C', displayName: 'None' }
    ];

    $scope.rarityFilter = function (card) {
        return Object.values($scope.filterItemsRarity).every(v => v === false) || $scope.filterItemsRarity[card.rarity];
    };

    $scope.colorIdentityFilter = function (card) {
        return Object.values($scope.filterItemsColorIdentity).every(v => v === false) || 
            (card.identityW && $scope.filterItemsColorIdentity["W"]) ||
            (card.identityU && $scope.filterItemsColorIdentity["U"]) ||
            (card.identityB && $scope.filterItemsColorIdentity["B"]) ||
            (card.identityR && $scope.filterItemsColorIdentity["R"]) ||
            (card.identityG && $scope.filterItemsColorIdentity["G"]) ||
            (card.identityC && $scope.filterItemsColorIdentity["C"]);
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
    $scope.randomizePin();
});
