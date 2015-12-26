<?php
//require_once("includes/mersenne_twister.php");
//use mersenne_twister\twister;
$tpl->set("title", "Voting code");

$date = date("M d Y, g A");
$tpl->set("currentTime", $date);

function random_number($seed, $max_number = 100)
{

    //make sure there won't be any deadspace where random numbers will never fill
    if ($max_number > 0xFFFFFF) {
        trigger_error("Max random number was to high. Maximum number of " . 0xFFFFFF . " allowed. Defaulting to maximum number.", E_USER_WARNING);
        $max_number = 0xFFFFFF;
    }

    //hash the seed to ensure enough random(ish) characters each time
    $hash = sha1($seed);

    //use the first x characters, and convert from hex to base 10 (this is where the random number is obtain)
    $rand = base_convert(substr($hash, 0, 6), 16, 10);

    //as a decimal percentage (ensures between 0 and max number)
    return round($rand / 0xFFFFFF * $max_number);

}

$characters = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";

$string = "";
for ($i = 0; $i < 4; $i++) {
    $string .= $characters[random_number($date.$i, strlen($characters) - 1)];
}

$tpl->set("votingCode", $string);
