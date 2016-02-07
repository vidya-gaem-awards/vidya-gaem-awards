<?php
namespace VGA;

class Timer
{
    private $startTime;

    public function __construct()
    {
        $this->startTime = microtime(true);
    }

    /**
     * @return float
     */
    public function time()
    {
        $time = microtime(true);
        return round($time - $this->startTime, 2);
    }
}
