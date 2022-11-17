<?php
namespace App\Twig;

use DateTime;
use Moment\Moment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class MomentExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            new TwigFilter('moment', [$this, 'momentFilter'])
        ];
    }

    public function momentfilter(DateTime $dateTime): Moment
    {
        return new Moment($dateTime->format(DateTime::ATOM));
    }
}
