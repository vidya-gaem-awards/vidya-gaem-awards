<?php
namespace VGA;

class Utils
{
    public static function storeMessage($type, $string, $value = null)
    {
        $_SESSION['message'] = array($type, $string);
        if ($value !== null) {
            $_SESSION['message'][2] = $value;
        }
    }

    public static function action($action, $firstID = null, $secondID = null)
    {
        global $ID, $PAGE, $mysql;

        $query = "INSERT INTO `actions` (`UserID`, `Timestamp`, `Page`, `Action`,
            `SpecificID1`, `SpecificID2`) VALUES(?, NOW(), ?, ?, ?, ?)";
        $stmt = $mysql->prepare($query);
        $stmt->bind_param('sssss', $ID, $PAGE, $action, $firstID, $secondID);
        $stmt->execute();
    }

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
}

