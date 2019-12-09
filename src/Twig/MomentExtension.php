<?php
namespace App\Twig;

use Moment\Moment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class MomentExtension extends AbstractExtension
{
    public function getFilters()
    {
        return [
            new TwigFilter('moment', [$this, 'momentFilter'])
        ];
    }

    public function momentfilter(\DateTime $dateTime): Moment
    {
        return new Moment($dateTime->format(\DateTime::ISO8601));
    }
}
