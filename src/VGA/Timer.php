<?php
namespace App\VGA;

class Timer
{
    private float $startTime;

    public function __construct()
    {
        $this->startTime = microtime(true);
    }

    public function time(): float
    {
        $time = microtime(true);
        return round($time - $this->startTime, 2);
    }
}
