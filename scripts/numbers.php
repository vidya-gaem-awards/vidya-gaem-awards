#!/usr/bin/env php
<?php
use VGA\DependencyManager;
use VGA\Model\Access;
use VGA\Model\Vote;
use VGA\Model\VotingCodeLog;
use VGA\Timer;
use VGA\Utils;

require(__DIR__ . '/../bootstrap.php');

$timer = new Timer();
$em = DependencyManager::getEntityManager();
$voteRepo = $em->getRepository(Vote::class);

// Step 1. Get a list of voters
$result = $voteRepo->createQueryBuilder('v')
    ->select('DISTINCT (v.cookieID) as id')
    ->getQuery()
    ->getResult();

$voters = [];

foreach ($result as $row) {
    $voters[$row['id']] = [
        'codes' => [],
        'notes' => [],
        'referrers' => []
    ];
}

echo "Step 1 (create array) complete: " . $timer->time() . "\n";

// Step 2. Check voting codes
$codeLogRepo = $em->getRepository(VotingCodeLog::class);

$result = $codeLogRepo->findAll();

/** @var VotingCodeLog $row */
foreach ($result as $row) {
    if (isset($voters[$row->getCookieID()])) {
        $voters[$row->getCookieID()]['codes'][] = $row->getCode();
    }
}

echo "Step 2 (get voting codes) complete: " . $timer->time() . "\n";

// Step 3. Check referrers
$accessRepo = $em->getRepository(Access::class);
$result = $accessRepo->createQueryBuilder('a')
    ->select('a')
    ->where("a.referer NOT LIKE 'https://____.vidyagaemawards.com%'")
    ->orWhere('a.referer IS NULL')
    ->orderBy('a.timestamp', 'ASC')
    ->getQuery()
    ->getResult();

/** @var Access $access */
foreach ($result as $access) {
    if (!isset($voters[$access->getCookieID()])) {
        continue;
    }

    $referer = preg_replace('{https?://(www\.)?}', '', $access->getReferer());
    $voters[$access->getCookieID()]['referrers'][] = $referer;
}

echo "Step 3 (get referrers) complete: " . $timer->time() . "\n";

// Step 4. Begin the processing

$sites = [
    'reddit.com' => 2 ** 0,
    't.co' => 2 ** 1,
    'boards.4chan.org' => 2 ** 2,
    'sys.4chan.org' => 2 ** 2,
    'forums.somethingawful.com' => 2 ** 3,
    'neogaf.com' => 2 ** 4,
    'facepunch.com' => 2 ** 5,
    '8ch.net' => 2 ** 6,
    'twitch.tv' => 2 ** 7,
    'facebook.com' => 2 ** 8,
    'm.facebook.com' => 2 ** 8,
    'l.facebook.com' => 2 ** 8,
    'google.' => 2 ** 9,
    // voting code: 2 ** 10
    // no referer: 2 ** 11
];

foreach ($voters as $id => &$info) {
    $number = 0;
  
    // If user has a voting code
    if (count($info['codes']) > 0) {
        $number += 2 ** 10;
        $info['notes'][] = "Has voting code";
    }

    $referers = array_unique($info['referrers']);

    // It's possible to have multiple unique referrers for one site.
    // To avoid messing up the bitmask, only count each site once.
    $used_bits = [];

    foreach ($referers as $referer) {
        foreach ($sites as $site => $value) {
            if (Utils::startsWith($referer, $site) && !in_array($value, $used_bits, true)) {
                $info['notes'][] = $site;
                $used_bits[] = $value;
                $number += $value;
            }
        }

        if ($referer == '') {
            $number += 2 ** 11;
        }
    }

    $info['number'] = $number;
}

$numberTotals = [];
foreach ($voters as $info) {
    if (!isset($numberTotals[$info['number']])) {
        $numberTotals[$info['number']] = 0;
    }
    $numberTotals[$info['number']]++;
}

echo "Step 4 (assign numbers) complete: " . $timer->time() . "\n";

// Step 5. Update the values in the database
$baseQuery = $em->createQueryBuilder()
    ->update(Vote::class, 'v')
    ->where('v.cookieID = :id');

$count = 0;
foreach ($voters as $id => $info) {
    $count++;

    $query = clone $baseQuery;
    $query
        ->set('v.number', $info['number'])
        ->setParameter('id', $id)
        ->getQuery()
        ->execute();

    if ($count % 1000 == 0) {
        echo "Processing record $count... " . $timer->time() . "\n";
    }
}

echo "Step 5 (update database) complete: " . $timer->time() . "\n\n";
