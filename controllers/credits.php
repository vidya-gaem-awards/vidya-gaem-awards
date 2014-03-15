<?php
$tpl->set("title", "Credits");

$cast = <<<CAST
CAST;

$skits = <<<SKITS
SKITS;

$vPlays = <<<VPLAYS
VPLAYS;

$crew = <<<CREW
beatstar
producer

Clamburger
website developer
github/clamburger

F4ceMeltor
did a Nighthood

iAxX
streamer

monoframe
video editor

PhoneEatingBear
producer + video editor

Segab
web designer + video editor

Smith
writer
CREW;

$media = <<<MEDIA
MEDIA;

$thanks = <<<THANKS
THANKS;

function parseCredits($input) {

  $sites = array(
    "youtube" => "http://www.youtube.com/",
    "twitter" => "https://twitter.com/",
    "steam" => "http://steamcommunity.com/id/",
    "audioboo.fm" => "http://audioboo.fm/",
    "website" => "http://",
    "twitch" => "http://www.twitch.tv/",
    "github" => "http://github.com/"
  );
  
  $input = explode("\n\n", $input);

  foreach ($input as &$member) {
    $member = explode("\n", $member);
    
    $last = count($member) - 1;
    
    //if (count($member) === 3) {
    if (strpos($member[$last], "/") !== false) {
      $site = explode("/", $member[$last]);
      $member[$last] = "<a href=\"".($sites[$site[0]] . $site[1])."\">{$site[0]}</a>";
    }
    
    foreach ($member as &$item) {
      $item = "<li>$item</li>";
    }
    $member = implode("\n", $member);
  }
  
  return $input;
}

$tpl->set("cast", parseCredits($cast));
$tpl->set("skits", parseCredits($skits));
$tpl->set("vPlays", parseCredits($vPlays));
$tpl->set("crew", parseCredits($crew));
$tpl->set("media", parseCredits($media));
$tpl->set("thanks", parseCredits($thanks));
?>