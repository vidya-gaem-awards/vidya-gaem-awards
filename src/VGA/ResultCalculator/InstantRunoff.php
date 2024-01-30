<?php
namespace App\VGA\ResultCalculator;

use App\VGA\AbstractResultCalculator;

class InstantRunoff extends AbstractResultCalculator
{
    public function getAlgorithmId(): string
    {
        return 'irv';
    }

    public function calculateResults(): array
    {
        $candidates = $this->candidates;
        $votes = $this->votes;

        $firstPref = array_combine(array_keys($candidates), array_fill(0, count($candidates), 0));

        $roundsRequired = count($candidates) - 1;
        $currentRound = 0;

        foreach ($votes as $vote) {
            $firstPref[$vote[1]]++;
        }

        $steps = [];
        $warnings = [];

        while ($currentRound < $roundsRequired - 1) {

            $currentRound++;

            $voteCount = count($votes);
            arsort($firstPref);

            // In the event of a tie, not a single fuck is given. Take a pseudo-random one.
            $lowest = array_keys($firstPref, min($firstPref));
            if (count($lowest) > 1) {
                $warnings[] = "Warning: tie in round $currentRound";
            }
            $lowest = $lowest[0];

            $thisRound = array("VoteCount" => $voteCount, "Ranking" => array());
            foreach ($firstPref as $candidate => $muhVotes) {
                $thisRound["Ranking"][] = $candidates[$candidate]->getName() . ": $muhVotes (".round($voteCount > 0 ? ($muhVotes/$voteCount*100) : 0, 2)."%)";
            }
            $thisRound["Eliminated"] = $candidates[$lowest]->getName();

            $steps[] = $thisRound;

            unset($firstPref[$lowest]);

            foreach ($votes as $key => $vote) {
                $preferencesLeft = array_keys($vote);
                sort($preferencesLeft);
                $lowestKey = $preferencesLeft[0];
                if ($vote[$lowestKey] == $lowest) {

                    // Find the second-lowest preference still available
                    if (isset($preferencesLeft[1])) {
                        $secondLowestKey = $preferencesLeft[1];
                        $firstPref[$vote[$secondLowestKey]]++;
                    }
                }
                $wastedVote = array_search($lowest, $vote);
                unset($votes[$key][$wastedVote]);
                if (count($votes[$key]) === 0) {
                    unset($votes[$key]);
                }
            }
        }

        // Final round

        $currentRound++;

        $voteCount = count($votes);
        arsort($firstPref);

        $winner = array_keys($firstPref, max($firstPref));
        $winner = $winner[0];
        if (is_array($winner) && count($winner) > 1) {
            $warnings[] = "Warning: tie in round $currentRound";
        }

        $thisRound = array("VoteCount" => $voteCount, "Ranking" => array());
        foreach ($firstPref as $candidate => $votes) {
            $thisRound["Ranking"][] = $candidates[$candidate]->getName() . ": $votes (".round($voteCount > 0 ? ($votes/$voteCount*100) : 0, 2)."%)";
        }
        $steps[] = $thisRound;

        $this->warnings = $warnings;
        $this->steps = $steps;

        return [1 => $winner];
    }
}
