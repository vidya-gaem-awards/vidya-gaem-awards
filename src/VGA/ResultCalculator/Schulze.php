<?php
namespace App\VGA\ResultCalculator;

use App\VGA\AbstractResultCalculator;

class Schulze extends AbstractResultCalculator
{
    /**
     * @return array
     */
    public function calculateResults()
    {
        $candidates = $this->candidates;
        $votes = $this->votes;

        if (count($candidates) === 1) {
            $this->warnings = [];
            $this->steps = ['pairwise' => [], 'strengths' => [], 'sweepPoints' => 0];

            return [$candidates[0]];
        }

        $warnings = [];

        // create a matrix of pairwise preferences
        $pairwise = [];
        // for every nominee

//        echo "Number of candidates: " . count($candidates) . "\n";
//        echo "Number of votes: " . count($votes) . "\n";

        $candidateKeys = array_keys($candidates);

        foreach ($candidateKeys as $candidateX) {
            // compare it to every other nominee
            foreach ($candidateKeys as $candidateY) {
                // Check you aren't comparing the candidate to itself
                if ($candidateX == $candidateY) {
                    continue;
                }

                // Set initial matrix value
                $pairwise[$candidateX][$candidateY] = 0;

                // Iterate through iterate through each voter
                foreach ($votes as $key => $vote) {
                    // Check if the user voted for the candidates we're comparing
                    $noVoteForX = (array_search($candidateX, $vote) === false);
                    $noVoteForY = (array_search($candidateY, $vote) === false);

                    /*
                     * If the user didn't vote for either of the candidates we are comparing, we can assume that they
                     * dislike them equally. As such, we don't make any change to the pairwise matrix.
                     *
                     * If the user only voted for one of the candidates, we put the other one at index 1000 to indicate
                     * that they liked them less than all of the other candidates they voted for.
                     */
                    if ($noVoteForX && $noVoteForY) {
                        continue;
                    } elseif ($noVoteForX) {
                        $vote[1000] = $candidateX;
                    } elseif ($noVoteForY) {
                        $vote[1000] = $candidateY;
                    }

                    // Check if the user prefers candidateX to candidateY, and incremenet the pairwise value if so.
                    if (array_search($candidateX, $vote) < array_search($candidateY, $vote)) {
                        $pairwise[$candidateX][$candidateY]++;
                    }
                }
            }
        }

        // These next two blocks are a PHP implementation of this psuedocode
        // https://en.wikipedia.org/wiki/Schulze_method#Implementation

        $strengths = [];

        foreach ($candidateKeys as $i) {
            foreach ($candidateKeys as $j) {
                if ($i == $j) {
                    continue;
                }

                if ($pairwise[$i][$j] > $pairwise[$j][$i]) {
                    $strengths[$i][$j] = $pairwise[$i][$j];
                } else {
                    $strengths[$i][$j] = 0;
                }
            }
        }

        foreach ($candidateKeys as $i) {
            foreach ($candidateKeys as $j) {
                if ($i == $j) {
                    continue;
                }

                foreach ($candidateKeys as $k) {
                    if (($i != $k) && ($j != $k)) {
                        $strengths[$j][$k] = max($strengths[$j][$k], min($strengths[$j][$i], $strengths[$i][$k]));
                    }
                }
            }
        }

        $rankings = array_fill(1, count($candidates), []);

        foreach ($strengths as $nominee => $row) {
            $position = count($candidates) - count(array_filter($row));
            $rankings[$position][] = $nominee;
        }

        $finalRankings = array();
        foreach ($rankings as $position => $nominees) {
            if (count($nominees) > 1) {
                $warnings[] = "Tie at position $position: ".implode(", ", $nominees);
            } elseif (count($nominees) == 0) {
                $warnings[] = "Gap at position $position";
            }
            foreach ($nominees as $nominee) {
                $finalRankings[] = $nominee;
            }
        }

        $finalRankings = array_combine(range(1, count($candidates)), $finalRankings);

        $reversed = array_reverse($finalRankings);
        $sweepPoints = [];

        foreach ($reversed as $index => $nominee) {
            if ($index === 0) {
                $sweepPoints[$nominee] = 0;
                continue;
            }

            $otherNominee = $reversed[$index - 1];
            $comparison = $pairwise[$nominee][$otherNominee] - $pairwise[$otherNominee][$nominee];
            $sweepPoints[$nominee] = $sweepPoints[$otherNominee] + $comparison;
        }

        // Now we have the raw sweep points, but we only want the first five (which we then want to scale accordingly)
        // We take 6 instead of 5, because the 6th is going to be our baseline (it will be set to zero)
        $sweepPoints = array_slice($sweepPoints, -6);

        $min = min($sweepPoints);
        $max = max($sweepPoints);

        // Scaling formula from http://stackoverflow.com/a/31687097
        foreach ($sweepPoints as &$point) {
            if ($max - $min === 0) {
                $point = 0;
            } else {
                $point = $max * ($point - $min) / ($max - $min);
            }
        }

        $this->warnings = $warnings;
        $this->steps = ['pairwise' => $pairwise, 'strengths' => $strengths, 'sweepPoints' => $sweepPoints];

        return $finalRankings;
    }
}
