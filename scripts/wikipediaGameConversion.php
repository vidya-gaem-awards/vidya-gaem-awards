<?php
use VGA\DependencyManager;
use VGA\Model\GameRelease;

require_once('../bootstrap.php');

if (!file_exists('games.csv')) {
    echo "Please create games.csv first.\n";
    exit(1);
}

$em = DependencyManager::getEntityManager();

// The second and third characters are non-breaking spaces
$search = [' ', "\xc2\xa0", "\xa0", 'Win', 'Mac', 'Lin', 'iOS', 'Droid', 'Android', 'WP', 'X360', 'XBO', 'PSVita', '3DS', 'HTCVive', 'OculusRift', 'PlayStationVR'];
$replace = ['', '', '', 'pc', 'pc', 'pc', 'mobile', 'mobile', 'mobile', 'mobile', 'x360', 'xb1', 'vita', 'n3ds', 'pc,vr', 'pc,vr', 'ps4,vr'];
$delete = ['PS2', 'NDS', 'DC', 'PSP', 'Fire', 'Ouya'];

$allPlatforms = [
    'pc', 'vr', 'ps3', 'ps4', 'vita', 'psn', 'x360', 'xb1', 'xbla', 'wii', 'wiiu', 'wiiware', 'n3ds', 'mobile'
];

$games = array();

$csv = file('games.csv');
foreach ($csv as $line) {
    $array = str_getcsv($line);
    $game = $array[0];
    if (!isset($games[$game])) {
        $games[$game] = array_fill_keys($allPlatforms, false);
    }

    $platforms = explode(",", trim(str_replace($search, $replace, $array[1])));
    $platforms = array_diff($platforms, $delete);
    foreach ($platforms as $platform) {
        $platform = ucfirst(strtolower($platform));
        $games[$game][$platform] = true;
    }
}

foreach ($games as $name => $platforms) {
    $release = new GameRelease($name);
    $platforms = array_keys(array_filter($platforms));
    echo "$name\n";
    foreach ($platforms as $platform) {
        if (method_exists($release, 'set' . $platform)) {
            $release->{'set'.$platform}(true);
        } else {
            echo "! Unknown platform: $platform\n";
        }
    }
    $em->persist($release);
}

$em->flush();

echo "All done.\n";
