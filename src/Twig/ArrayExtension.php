<?php

namespace App\Twig;

use Traversable;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class ArrayExtension extends AbstractExtension
{
    public function getFilters()
    {
        return [
            new TwigFilter('shuffle', [$this, 'shuffleFilter'])
        ];
    }

    public function shuffleFilter($array)
    {
        if ($array instanceof Traversable) {
            $array = iterator_to_array($array, false);
        }

        shuffle($array);

        return $array;
    }
}
