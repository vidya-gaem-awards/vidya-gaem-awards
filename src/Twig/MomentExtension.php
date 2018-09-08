<?php
namespace App\Twig;

use Moment\Moment;

class MomentExtension extends \Twig_Extension
{
    public function getFilters()
    {
        return [
            new \Twig_SimpleFilter('moment', [$this, 'momentFilter'])
        ];
    }

    public function momentfilter(\DateTime $dateTime): Moment
    {
        return new Moment($dateTime->format(\DateTime::ISO8601));
    }
}
