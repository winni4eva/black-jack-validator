<?php
namespace Winnipass;


class YocoJackValidator {

    protected $testCasesPath = 'https://s3-eu-west-1.amazonaws.com/yoco-testing/tests.json';
    protected $suits = [
        '0' => 0,
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
    ];

    const MAXIMUM_POINT = 21;

    public function validate(): void 
    {
        $testCases = file_get_contents($this->testCasesPath);
        $testCasesArray = json_decode($testCases, true);

        foreach ($testCasesArray as $key => $test) {
            $winner = $this->verifyWinner($test);
            var_dump($winner);
        }
    }

    protected function verifyWinner(array $game): string
    {
        [
            'playerAWins' => $playerAWins,
        ] = $game;
        $game = $this->sortPlayerCardsByRanks($game);
        $winner = $this->isWinnerByTotalPoints($game);
        $victoriousPlayer = $winner ? 'playerA' : 'playerB';
        $expectedWinner = $playerAWins ? 'playerA' : 'playerB';

        return "GAME WINNER : $victoriousPlayer ". " EXPECTED WINNER : $expectedWinner";
    }

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
        } else if ($playerATotalPoints < $playerBTotalPoints) {
            return true;
        } 
        return false;
    }

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