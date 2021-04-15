import numpy as np
import yaml
import random

def grid_is_valid(matches_grid, MATCHES_PER_ROUND):
    matches_per_player = matches_grid.sum(axis=1)
    
    for m in matches_per_player:
        if m != MATCHES_PER_ROUND:
            return False
    
    return True


def still_possible(matches_grid, valid_grid, MATCHES_PER_ROUND):
    pairings_left = valid_grid.sum(axis=1) - (MATCHES_PER_ROUND - matches_grid.sum(axis=1))
    return pairings_left.min() >= 0


def get_possible_opponents(p_id, valid_grid):
    possible_opponents = []
    
    for o_id, opponent in enumerate(valid_grid[p_id]):
        if opponent == 1:
            possible_opponents.append(o_id)
            
    return possible_opponents


def get_possible_random_opponent(p_id, valid_grid):
    ops = get_possible_opponents(p_id, valid_grid)
    return random.choice(ops)


def get_next_player(matches_grid, MATCHES_PER_ROUND):
    possible_next = []
    
    for p_idx, m in enumerate(matches_grid.sum(axis=1)):
        if m < MATCHES_PER_ROUND:
            possible_next.append(p_idx)
    
    return random.choice(possible_next)


def get_pairings(PLAYERS, GROUP_SIZE, MATCHES_PER_ROUND, ALREADY_PLAYED):    
    ranking = list(range(PLAYERS))

    c_group = 0
    c_group_size = 0
    groups = [[]]

    for player in ranking:
        c_group_size +=1
        groups[c_group].append(player)

        if c_group_size == GROUP_SIZE:
            c_group_size = 0
            c_group += 1
            groups.append([])

    matches_grid = np.zeros((PLAYERS, PLAYERS), dtype=int)
    valid_grid = np.ones((PLAYERS, PLAYERS))

    for g in groups:
        for p1 in g:
            for p2 in g:
                if p1 != p2:
                    matches_grid[p1, p2] = 1

                valid_grid[p1, p2] = 0
    
    success = True

    while(not grid_is_valid(matches_grid, MATCHES_PER_ROUND)):
        if (not still_possible(matches_grid, valid_grid, MATCHES_PER_ROUND)):
            success = False
            break

        p = get_next_player(matches_grid, MATCHES_PER_ROUND)
        o = get_possible_random_opponent(p, valid_grid)

        matches_grid[p, o] = 1
        matches_grid[o, p] = 1
        valid_grid[p, o] = 0
        valid_grid[o, p] = 0

        for p_idx, matches in enumerate(matches_grid.sum(axis=1)):
            if matches == MATCHES_PER_ROUND:
                valid_grid[p_idx, :] = 0
                valid_grid[:, p_idx] = 0
    
    pairings_rating = 0
    if success:
        pairings_rating = (ALREADY_PLAYED * matches_grid).sum()
    
    return success, matches_grid, pairings_rating


if __name__ == "__main__":
    with open(r'matchmaking_input.yml') as file:
        settings = yaml.load(file, Loader=yaml.FullLoader)

    player_dict = {}
    player_ids = []

    for idx, pid in enumerate(settings['ranking']):
        player_dict[pid] = idx
        player_ids.append(pid)

    PLAYERS = len(player_dict)
    GROUP_SIZE = settings['group_size']
    MATCHES_PER_ROUND = settings['matches_per_round']

    ALREADY_PLAYED = np.zeros((PLAYERS, PLAYERS), dtype=int)
    
    for m in settings['already_played']:
        p1, p2 = m.strip().split(';', maxsplit=2)
        p1 = int(p1.strip())
        p2 = int(p2.strip())

        ALREADY_PLAYED[player_dict[p1], player_dict[p2]] += 1
        ALREADY_PLAYED[player_dict[p2], player_dict[p1]] += 1

    successes = 0
    tries = 1000

    successfull_pairings = []
    ratings = []

    for i in range(tries):
        success, matches_grid, pairings_rating = get_pairings(PLAYERS, GROUP_SIZE, MATCHES_PER_ROUND, ALREADY_PLAYED)
        if success:
            successfull_pairings.append(matches_grid)
            ratings.append(pairings_rating)
            successes += 1

    print(f'{successes} / {tries}: {successes / tries *100}%')
    ratings = np.array(ratings)
    print(f'Mean: {ratings.mean()}, Min: {ratings.min()}, Max: {ratings.max()}')
    print(f'Min index: {ratings.argmin()}')
    
    print(f'\nBest matchmaking:')
    best_pairings = successfull_pairings[ratings.argmin()]

    for p1 in range(PLAYERS):
        for p2 in range(PLAYERS):
            if p1 < p2:
                if best_pairings[p1, p2] == 1:
                    print(f'{player_ids[p1]};{player_ids[p2]}')
