<?php
namespace App\VGA\ResultCalculator;

use App\VGA\AbstractResultCalculator;

class Schulze extends AbstractResultCalculator
{
    protected array $pairwise = [];
    protected array $strengths = [];

    public function getAlgorithmId(): string
    {
        return 'schulze';
    }

    protected function populatePairwiseArray(): void
    {
        $candidates = array_keys($this->candidates);

        foreach ($candidates as $candidateX) {
            // compare it to every other nominee
            foreach ($candidates as $candidateY) {
                // Check you aren't comparing the candidate to itself
                if ($candidateX === $candidateY) {
                    continue;
                }

                if (!isset($this->pairwise[$candidateX][$candidateY])) {
                    $this->pairwise[$candidateX][$candidateY] = 0;
                }

                // Iterate through each voter
                foreach ($this->votes as $vote) {
                    // Check if the user voted for the candidates we're comparing
                    $noVoteForX = !in_array($candidateX, $vote);
                    $noVoteForY = !in_array($candidateY, $vote);

                    /*
                     * If the user didn't vote for either of the candidates we are comparing, we can assume that they
                     * dislike them equally. As such, we don't make any change to the pairwise matrix.
                     *
                     * If the user only voted for one of the candidates, we put the other one at index 1000 to indicate
                     * that they liked them less than all the other candidates they voted for.
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
                        $this->pairwise[$candidateX][$candidateY]++;
                    }
                }
            }
        }
    }

    protected function populateStrengths(): void
    {
        $candidates = array_keys($this->candidates);

        foreach ($candidates as $candidate) {
            $this->strengths[$candidate] = [];

            foreach ($candidates as $otherCandidate) {
                if ($candidate !== $otherCandidate) {
                    $this->strengths[$candidate][$otherCandidate] = 0;
                }
            }
        }

        foreach ($candidates as $candidateX) {
            foreach ($candidates as $candidateY) {
                if ($candidateX === $candidateY) {
                    continue;
                }

                if ($this->pairwise[$candidateX][$candidateY] > $this->pairwise[$candidateY][$candidateX]) {
                    $this->strengths[$candidateX][$candidateY] = $this->pairwise[$candidateX][$candidateY];
                } else {
                    $this->strengths[$candidateX][$candidateY] = 0;
                }
            }
        }

        foreach ($candidates as $candidateX) {
            foreach ($candidates as $candidateY) {
                if ($candidateX === $candidateY) {
                    continue;
                }

                foreach ($candidates as $candidateZ) {
                    if ($candidateX === $candidateZ || $candidateY === $candidateZ) {
                        continue;
                    }

                    $this->strengths[$candidateY][$candidateZ] =
                        max(
                            $this->strengths[$candidateY][$candidateZ],
                            min(
                                $this->strengths[$candidateY][$candidateX],
                                $this->strengths[$candidateX][$candidateZ]
                            )
                        );
                }
            }
        }
    }

    protected function calculateRankings(): array
    {
        $result = [];
        $done = [];
        $rank = 1;

        while (count($done) < count($this->candidates)) {
            $to_done = [];

            foreach ($this->strengths as $candidateX => $challengers) {
                if (in_array($candidateX, $done)) {
                    continue;
                }

                $winner = true;

                foreach ($challengers as $candidateY => $strength) {
                    if (in_array($candidateY, $done)) {
                        continue;
                    }

                    if ($strength < $this->strengths[$candidateY][$candidateX]) {
//                        echo "- Strength of $candidateX to $candidateY [$strength] is less than strength of $candidateY to $candidateX [{$this->strongestPaths[$candidateY][$candidateX]}]\n";
                        $winner = false;
                    } else {
//                        echo "+ Strength of $candidateX to $candidateY [$strength] is SUPERIOR to strength of $candidateY to $candidateX [{$this->strongestPaths[$candidateY][$candidateX]}]\n";
                    }
                }

                if ($winner) {
//                    echo "Found rank $rank: $candidateX\n";
                    $result[$rank][] = $candidateX;
                    $to_done[] = $candidateX;
                    break;
                }
            }

            array_push($done, ...$to_done);
            $rank++;
        }

        return $result;
    }

    public function calculateResults(): array
    {
        $candidates = $this->candidates;

        if (count($candidates) === 1) {
            $candidate = array_keys($candidates)[0];
            $this->warnings = [];
            $this->steps = ['pairwise' => [], 'strengths' => [], 'sweepPoints' => [$candidate => 0]];

            return [1 => $candidate];
        }

        $this->populatePairwiseArray();
        $this->populateStrengths();
        $rankings = $this->calculateRankings();

        $warnings = [];

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
            $comparison = $this->pairwise[$nominee][$otherNominee] - $this->pairwise[$otherNominee][$nominee];
            $sweepPoints[$nominee] = $sweepPoints[$otherNominee] + $comparison;
        }

        // Now we have the raw sweep points, but we only want the first five (which we then want to scale accordingly)
        // We take 6 instead of 5, because the 6th is going to be our baseline (it will be set to zero)
        $sweepPoints = array_slice($sweepPoints, -7);

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
        $this->steps = ['pairwise' => $this->pairwise, 'strengths' => $this->strengths, 'sweepPoints' => $sweepPoints];

        return $finalRankings;
    }
}
