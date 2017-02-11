<?php
namespace VGA;

class Utils
{
    /**
     * @param $seed
     * @param int $max_number
     * @return int
     */
    public static function randomNumber($seed, $max_number = 100)
    {
        //hash the seed to ensure enough random(ish) characters each time
        $hash = sha1($seed);

        //use the first x characters, and convert from hex to base 10 (this is where the random number is obtain)
        $rand = base_convert(substr($hash, 0, 6), 16, 10);

        //as a decimal percentage (ensures between 0 and max number)
        return (int)round($rand / 0xFFFFFF * $max_number);
    }

    public static function startsWith($haystack, $needle)
    {
        return substr($haystack, 0, strlen($needle)) === $needle;
    }
}

