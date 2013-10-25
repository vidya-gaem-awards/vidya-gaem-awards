<?php
$tpl->set("title", "Credits");

$cast = <<<CAST
StephanosRex
main host
youtube/StephanosRex

Sboss
hyperbole award
youtube/sboss

sixaxis
blunder of the year intro
website/vidyavidya.com

Stanry Roo
blunder of the year

JD Walsh
nature of a man award
twitter/Mr_Itch

Melissa Hutchinson
clementine

William
hamburger helper award
youtube/RiotsDub

BarackObeezy
stop it award
youtube/BarackObeezy

The Fourth Queen
not so rehash award

CurtDogg
kong award
twitter/curtisbonds

HyperBitHero
fuck you award
youtube/HyperBitHero

ThorkellTheTall 
press x to win the award
youtube/thorkellthetall

Mads “Kea” Poulsen
buzzword award
steam/thekea

Esper
intermission out

Nenbard Elnil
intermission in
steam/nenbardelnil

Instig8iveJournalism
lods of emone award
youtube/Instig8iveJournalism

Pato
sanic award
twitter/vergewaltigen

Pinkslee
gamebryoken award
youtube/Pinksleee

Whynne
meat and fish award
youtube/charleswhynne

Smith
stylish! award

ItsDuke
kotick award
audioboo.fm/ItsDuke

Diplo
ip twist award
youtube/Diplomacide

Barock O’blumpkin
malignance award
youtube/emptyhero

Kazuma/xBeelzebub
reddit award
youtube/kazumaplaysgames

Ods
/v/irgin award
steam/Ods25

DaCo
most hated award
steam/cdsand
CAST;

$skits = <<<SKITS
Anonymous
killme.wmv

Bazza
VGCW: /v/ vs Wii U
twitch/bazza87

ShrekisDreck
Almost Ssshakespearean

Lord Mandalore
forgive me
youtube/MandaloreGaming

GermanBro & Pato
/v/ vs /vg/
website/implyingrigged.com

joppu
girls and panzers

Anonymous
Social Justice

DiskSystemChronicles
Top of my Boat

MowtenDoo
DmC Gym
youtube/MowtenDoo

SpongeBrotha
Chadwarden /v/GAs
youtube/SpongeBrotha

SamuriFerret
Pooh Bear Rises
youtube/SamuriFerret92

Anonymous
Reggie Performs For One Last Time

TheHumanTrombone
Intermission
youtube/thehumantrombone

William
Persona 4: The Golden Dance

vidyamind
You Gotta Be Fast
youtube/vidyamind

/o/ Forza 4 Club
DARPa Wheelie Division

WAT
Racing Development

Anonymous
Indie Game Review

FineGameBitches
Lollipop Chainsaw
youtube/FineGameGirls

BarackObeezy
Tropes and the City
youtube/BarackObeezy

Anonymous
Shoujajojo

Fated Soul Blake
How To Make A Videogame
steam/fatedsoulblake

Fench
Robert Bopkins tells it like it is
website/hauntedbees.com

kid_dj
My Little Pony
youtube/kiddj2005

Anonymous
What I hate about video
games

SnoicHedghog
Snoic Geratons exclusive
trailer
youtube/SnoicHedghog
SKITS;

$vPlays = <<<VPLAYS
Adolon
Autism General
youtube/Adolon

Anthony
The Walking Dead
youtube/VaultBoyAnthony

Axfred
weird ass buttons

Aynert
Chivalry archer
youtube/Aynert

FuckingLautrec
Undead Flipping
youtube/FuckingLautrec

HolyGamer
CS:S
youtube/TheGreenBelgo

ObeseCatLord
Grezzo 2
youtube/ilikemuffinsandovals

Pinkslee
Eurotruck
youtube/Pinksleee

Tron Bonne
Slam Ops II

Willy Wanker
this isn't a game
youtube/TheWalrusOfLife

Anonymous
all others
VPLAYS;

$crew = <<<CREW
BarackObeezy
assistant producer, writer
youtube/barackobeezy

bunnyhop
writer, film editor
youtube/bunnyhopshow

Captain Falcon
film editor, graphics
youtube/KyaputenFarukon

Clamburger
website development
github/clamburger

Cluey
3D, film graphics
youtube/stiffupperjoystick

D. Jiko
writer
twitter/Dejikovidya

Dinotron
streamer

DiskSystemChronicles
film editor

Dr. Face Doctor
logo

gattocake
film editor
youtube/gattocake

Gene
music
youtube/10lettername

Grawly
music
twitter/Grawly

HolyGamer
vidyographer
youtube/TheGreenBelgo

Ice
guy

Instig8iveJournalism
writer
youtube/Instig8iveJournalism

Kung Fu Tango
music

Lord Mandalore
writer
youtube/MandaloreGaming

OneFourth
film graphics
youtube/FrameFactoryFilms

Ped
opinions
youtube/GamersWFE

PhoneEatingBear
graphic designer, film editor
youtube/phoneeatingbear

pu
producer, writer

Segab
motion graphics, website development
youtube/segab

Smith
writer

Spacebear
planning
steam/spacebear

Sugar Embargo
film graphics

ThorkellTheTall
general
youtube/thorkellthetall

Willy Wanker
film editor
youtube/TheWalrusOfLife

Zeyami
memes
CREW;

$media = <<<MEDIA
hamsteralliance
Gaben’s Watching
youtube/hamsteralliance

MisterSamurai
Gaben’s Watching
youtube/MisterSamurai

SFMTales
PigglyJuff
youtube/SFMTales
MEDIA;

$thanks = <<<THANKS
/v/
categories, nominations,
voting, other input

Manly Tears
permission
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
  
  $input = explode("\r\n\r\n", $input);

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