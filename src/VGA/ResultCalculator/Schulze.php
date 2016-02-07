<?php
namespace VGA\ResultCalculator;

use VGA\AbstractResultCalculator;

class Schulze extends AbstractResultCalculator
{
    /**
     * @return array
     */
    public function calculateResults()
    {
        $candidates = $this->candidates;
        $votes = $this->votes;

        $warnings = [];

        // create a matrix of pairwise preferences
        $pairwise = [];
        // for every nominee

        $candidates2 = $candidates;
        $candidates3 = $candidates2;

        foreach ($candidates as $candidateX => $xInfo) {
            // compare it to every other nominee
            foreach ($candidates2 as $candidateY => $yInfo) {
                //check you aren't comparing it to itself
                if ($candidateX != $candidateY) {
                    // set initial matrix value - not sure if this is required
                    $pairwise[$candidateX][$candidateY] = 0;
                    // now iterate through each voter
                    foreach ($votes as $key => $vote) {
                        // check each candidate was voted for and store it in 20 otherwise
                        if (array_search($candidateX, $vote) === false) {
                            $vote[20] = $candidateX;
                        }
                        if (array_search($candidateY, $vote) === false) {
                            $vote[20] = $candidateY;
                        }
                        // compare the ranks - don't know the data structure well enough to guess this
                        if (array_search($candidateX, $vote) < array_search($candidateY, $vote)) {
                            // increase the matrix value of candidateX preferred over candidateY
                            $pairwise[$candidateX][$candidateY]++;
                        }
                    }
                }
            }
        }

        // hopefully we should get a pairwise matrix that we can now compare strengths of strongest paths
        $strengths = array();
        foreach ($candidates as $i => $value) {
            foreach ($candidates2 as $j => $value2) {
                if ($i != $j) {
                    if ($pairwise[$i][$j] > $pairwise[$j][$i]) {
                        $strengths[$i][$j] = $pairwise[$i][$j];
                    } else {
                        $strengths[$i][$j] = 0;
                    }
                }
            }
        }
        foreach ($candidates as $i => $value) {
            foreach ($candidates2 as $j => $value2) {
                if ($i != $j) {
                    foreach ($candidates3 as $k => $value3) {
                        if (($i != $k) && ($j != $k)) {
                            $strengths[$j][$k] = max($strengths[$j][$k], min($strengths[$j][$i], $strengths[$i][$k]));
                        }
                    }
                }
            }
        }

        $result = $strengths;

        $rankings = array_fill(1, count($candidates), array());

        foreach ($result as $nominee => $row) {
            $counts = array_count_values($row);
            $position = (int)($counts[0] + 1);
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

        $this->warnings = $warnings;
        $this->steps = ['pairwise' => $pairwise, 'strengths' => $strengths];

        return $finalRankings;
    }
}
