<?php
namespace Winnipass;


class YocoJackValidator {

    /**
     * Maximum card points rquired to win.
     *
     * @var string
     */
    const MAXIMUM_POINT = 21;

    /**
     * Default games path.
     *
     * @var string
     */
    protected $yJackGamesPath = 'https://s3-eu-west-1.amazonaws.com/yoco-testing/tests.json';

    /**
     * Card suits and their respective points.
     *
     * @var aray
     */
    protected $suits = [
        'C' => [
            '2C' => 2,
            '3C' => 3,
            '4C' => 4,
            '5C' => 5,
            '6C' => 6,
            '7C' => 7,
            '8C' => 8,
            '9C' => 9,
            '10C' => [
                'points' => 10,
                'rank' => 11,
                'isFace' => true,
            ],
            'JC' => [
                'points' => 10,
                'rank' => 12,
                'isFace' => true,
            ],
            'QC' => [
                'points' => 10,
                'rank' => 13,
                'isFace' => true,
            ],
            'KC' => [
                'points' => 10,
                'rank' => 14,
                'isFace' => true,
            ],
            'AC' => [
                'points' => 11,
                'rank' => 15,
                'isFace' => false,
            ],
        ],
        'D' => [
            '2D' => 2,
            '3D' => 3,
            '4D' => 4,
            '5D' => 5,
            '6D' => 6,
            '7D' => 7,
            '8D' => 8,
            '9D' => 9,
            '10D' => [
                'points' => 10,
                'rank' => 11,
                'isFace' => true,
            ],
            'JD' => [
                'points' => 10,
                'rank' => 12,
                'isFace' => true,
            ],
            'QD' => [
                'points' => 10,
                'rank' => 13,
                'isFace' => true,
            ],
            'KD' => [
                'points' => 10,
                'rank' => 14,
                'isFace' => true,
            ],
            'AD' => [
                'points' => 11,
                'rank' => 15,
                'isFace' => false,
            ],
        ],
        'H' => [
            '2H' => 2,
            '3H' => 3,
            '4H' => 4,
            '5H' => 5,
            '6H' => 6,
            '7H' => 7,
            '8H' => 8,
            '9H' => 9,
            '10H' => [
                'points' => 10,
                'rank' => 11,
                'isFace' => true,
            ],
            'JH' => [
                'points' => 10,
                'rank' => 12,
                'isFace' => true,
            ],
            'QH' => [
                'points' => 10,
                'rank' => 13,
                'isFace' => true,
            ],
            'KH' => [
                'points' => 10,
                'rank' => 14,
                'isFace' => true,
            ],
            'AH' => [
                'points' => 11,
                'rank' => 15,
                'isFace' => false,
            ],
        ],
        'S' => [
            '2S' => 2,
            '3S' => 3,
            '4S' => 4,
            '5S' => 5,
            '6S' => 6,
            '7S' => 7,
            '8S' => 8,
            '9S' => 9,
            '10S' => [
                'points' => 10,
                'rank' => 11,
                'isFace' => true,
            ],
            'JS' => [
                'points' => 10,
                'rank' => 12,
                'isFace' => true,
            ],
            'QS' => [
                'points' => 10,
                'rank' => 13,
                'isFace' => true,
            ],
            'KS' => [
                'points' => 10,
                'rank' => 14,
                'isFace' => true,
            ],
            'AS' => [
                'points' => 11,
                'rank' => 15,
                'isFace' => false,
            ],
        ],
        '0' => 0,
    ];

    /**
     * Card suits ranks.
     *
     * @var aray
     */
    protected $suitDifferentialSuits = [
        'S' => 4,
        'H' => 3,
        'C' => 2,
        'D' => 1
    ];

    /**
     * Card rank points.
     *
     * @var aray
     */
    protected $suitDifferentialRanks = [
        'K' => 4,
        'Q' => 3,
        'J' => 2,
        '10' => 1
    ];


    /**
     * Create a new YJackValidator instance.
     * 
     * @var string $gamesPath
     *
     * @return void
     */
    public function __construct(string $gamesPath = null)
    {
        if ($gamesPath) {
            $this->yJackGamesPath = $gamesPath;
        }
    }

    /**
     * Validates an array of yJack games.
     *
     * @return bool
     */
    public function validate(): bool 
    {
        $testCases = file_get_contents($this->yJackGamesPath);
        $games = json_decode($testCases, true);
        $allTestsPassed = true;
        foreach ($games as $key => $game) {
            [$message, $passed] = $this->verifyWinner($game);
            if (!$passed) {
                $allTestsPassed = false;
            }
            var_dump($message);
            echo "\n";
        }
        return $allTestsPassed;
    }

    /**
     * Validates a single yJack game.
     * 
     * @var array $game
     *
     * @return array
     */
    protected function verifyWinner(array $game): array
    {
        $sortedgame = $this->sortPlayerCardsByRanks($game);
        $winner = $this->isWinnerByTotalPoints($sortedgame);

        $victoriousPlayer = $winner ? 'playerA' : 'playerB';
        $expectedWinner = $game['playerAWins'] ? 'playerA' : 'playerB';

        $message = "GAME WINNER : $victoriousPlayer ". " EXPECTED WINNER : $expectedWinner";
        $passedTest =  $victoriousPlayer == $expectedWinner ? true : false;

        return [$message, $passedTest];
    }

    /**
     * Calculates yJack game points for players.
     * 
     * @var array $game
     *
     * @return bool
     */
    protected function isWinnerByTotalPoints(array $game): bool
    {
        [
            'playerAWins' => $playerAWins,
            'playerA' => $playerA, 
            'playerB' => $playerB
        ] = $game;
        
        $playerATotalPoints = $this->getGameTotalPoints($playerA);
        $playerBTotalPoints = $this->getGameTotalPoints($playerB);
        
        if ($playerATotalPoints > self::MAXIMUM_POINT) {
            return false;
        } else if($playerBTotalPoints > self::MAXIMUM_POINT) {
            return true;
        } else if ($playerATotalPoints > $playerBTotalPoints) {
            return true;
        } else if ($playerBTotalPoints > $playerATotalPoints) {
            return false;
        } else if ($playerATotalPoints === $playerBTotalPoints) {
            $playerAHighestRank = $this->getHighestRank($playerA);
            $playerBHighestRank = $this->getHighestRank($playerB);
            
            if ($playerAHighestRank > $playerBHighestRank) {
                return true;
            } else if ($playerBHighestRank > $playerAHighestRank) {
                return false;
            } else {
                $playerAHighest = $this->getHighestHands($playerA);
                $playerBHighest = $this->getHighestHands($playerB);
                if ($playerAHighest > $playerBHighest) {
                    return true;
                } else if ($playerBHighest > $playerAHighest) {
                    return false;
                } else {
                    return true;
                }
            }
        } 
        return false;
    }

    /**
     * Gets the highest ranked card from a set of game cards.
     * 
     * @var array $game
     *
     * @return int
     */
    protected function getHighestRank(array $game): int
    {
        $game = array_reverse($this->sortByHighestRank($game));
        [$highestRank] = $game;

        return $highestRank;
    }

    /**
     * Sorts a deck of cards by rank.
     * 
     * @var array $cards
     *
     * @return array
     */
    protected function sortByHighestRank(array $cards): array
    {
        $myCardRanks = [];
        foreach ($cards as $card) {
            [$rank, $suit] = strlen($card) == 3 ? str_split($card, 2) : str_split($card, 1);
            $suitRank = $this->suitDifferentialRanks[$rank] ?? 0;
            array_push($myCardRanks, ['suit' => $card, 'rank' => $suitRank]);
        }

        usort($myCardRanks, function ($card1, $card2) {
            return $card1['rank'] <=> $card2['rank'];
        });

        $sortedCard = [];
        foreach ($myCardRanks as $ranked) {
            array_push($sortedCard, $ranked['rank']);
        }

        return $sortedCard;
    }

    /**
     * Gets the highest ranked hand from a set of cards.
     * 
     * @var array $game
     *
     * @return int
     */
    protected function getHighestHands(array $game): int
    {
        $game = array_reverse($this->sortByHighestSuit($game));
        [$highestRank] = $game;

        return $highestRank;
    }

    /**
     * Sorts a deck of cards by suit.
     * 
     * @var array $game
     *
     * @return array
     */
    protected function sortByHighestSuit(array $cards): array
    {
        $myCardRanks = [];
        foreach ($cards as $card) {
            [$rank, $suit] = strlen($card) == 3 ? str_split($card, 2) : str_split($card, 1);
            $suitRank = $this->suitDifferentialSuits[$suit];
            array_push($myCardRanks, ['suit' => $card, 'rank' => $suitRank]);
        }

        usort($myCardRanks, function ($card1, $card2) {
            return $card1['rank'] <=> $card2['rank'];
        });

        $sortedCard = [];
        foreach ($myCardRanks as $ranked) {
            array_push($sortedCard, $ranked['rank']);
        }
    
        return $sortedCard;
    }

    /**
     * Calculates the total points in a game.
     * 
     * @var array $cards
     *
     * @return int
     */
    protected function getGameTotalPoints(array $cards) : int
    {
        $totalPoints = 0;
        foreach ($cards as $card) {
            [$rank, $suit] = strlen($card) == 3 ? str_split($card, 2) : str_split($card, 1);
            $suitDetails = $this->suits[$suit];
            if (is_array($suitDetails[$card])) {
                $totalPoints += $suitDetails[$card]['points'];
            } else {
                $totalPoints += $suitDetails[$card];
            }
        }

        return $totalPoints;
    }

    /**
     * Sorts a games cards by rank.
     * 
     * @var array $game
     *
     * @return array
     */
    protected function sortPlayerCardsByRanks(array $game): array
    {
        $players = [
            'playerA' => $game['playerA'], 
            'playerB' => $game['playerB']
        ];
        foreach ($players as $key => $player) {
            $game[$key] = $this->sortCard($player);
        }
        
        return $game;
    }

    /**
     * Sorts a single cards by rank.
     * 
     * @var array $game
     *
     * @return array
     */
    protected function sortCard(array $cards): array
    {
        $myCardRanks = [];
        foreach ($cards as $card) {
            [$rank, $suit] = strlen($card) == 3 ? str_split($card, 2) : str_split($card, 1);
            $suitDetails = $this->suits[$suit];
            if (is_array($suitDetails[$card])) {
                array_push($myCardRanks, ['suit' => $card, 'rank' => $suitDetails[$card]['rank']]);
            } else {
                array_push($myCardRanks, ['suit' => $card, 'rank' => $suitDetails[$card]]);
            }
        }

        usort($myCardRanks, function ($card1, $card2) {
            return $card1['rank'] <=> $card2['rank'];
        });

        $sortedCard = [];
        foreach ($myCardRanks as $ranked) {
            array_push($sortedCard, $ranked['suit']);
        }

        return $sortedCard;
    }
    
}