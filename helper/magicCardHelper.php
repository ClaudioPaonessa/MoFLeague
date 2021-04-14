<?php

function rarityToNumber($cardRarity) {
    switch ($cardRarity) {
        case "common":
            return 0;
            break;
        case "uncommon":
            return 1;
            break;
        case "rare":
            return 2;
            break;
        case "mythic":
            return 3;
            break;
    }
}

function getCardType($card_type_line) {
    if (strpos($card_type_line, "Land") !== false) {
        return "Lands";
    }

    if (strpos($card_type_line, "Creature") !== false) {
        return "Creatures";
    }

    if (strpos($card_type_line, "Artifact") !== false) {
        return "Artifacts";
    }

    if (strpos($card_type_line, "Enchantment") !== false) {
        return "Enchantments";
    }

    if (strpos($card_type_line, "Instant") !== false) {
        return "Instants";
    }

    if (strpos($card_type_line, "Sorcery") !== false) {
        return "Sorceries";
    }

    return "Others";
}


?>