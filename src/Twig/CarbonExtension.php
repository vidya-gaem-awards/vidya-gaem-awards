<?php
namespace App\Twig;

use Carbon\CarbonImmutable;
use DateTime;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class CarbonExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            new TwigFilter('carbon', [$this, 'carbonFilter'])
        ];
    }

    public function carbonFilter(DateTime $dateTime): CarbonImmutable
    {
        return new CarbonImmutable($dateTime);
    }
}
