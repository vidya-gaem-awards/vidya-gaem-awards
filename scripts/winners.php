#!/usr/bin/env php
<?php
use VGA\DependencyManager;
use VGA\Model\Category;
use VGA\Model\ResultCache;
use VGA\Model\Vote;
use VGA\ResultCalculator\Schulze;
use VGA\Timer;

require(__DIR__ . '/../vendor/autoload.php');

$timer = new Timer();
$em = DependencyManager::getEntityManager();
$voteRepo = $em->getRepository(Vote::class);

// Remove all existing data
$em->createQueryBuilder()->delete(ResultCache::class)->getQuery()->execute();

// Start by getting a list of categories and all the nominees.
$categories = $em->getRepository(Category::class)
    ->createQueryBuilder('c')
    ->where('c.enabled = true')
    ->orderBy('c.order', 'ASC')
    ->getQuery()
    ->getResult();

echo $timer->time() . ": categories loaded.\n";

$filters = [
    '00none' => false, // no filtering
    '01allofv' => 'BIT_AND(v.number, 4) > 0',  // 4chan
    '02voting+v' => 'BIT_AND(v.number, 4) > 0 AND BIT_AND(v.number, 1024) > 0', // 4chan AND voting code
    '03v' => 'BIT_AND(v.number, 4) > 0 AND NOT BIT_AND(v.number, 1024) > 0', // 4chan AND NOT voting code
    '04combined1' => 'BIT_AND(v.number, 4) > 0 OR BIT_AND(v.number, 2048) > 0', // 4chan OR null
    '05combined2' => 'BIT_AND(v.number, 4) > 0 OR (BIT_AND(v.number, 2048) > 0 AND BIT_AND(v.number, 1024) > 0)', // 4chan OR (null AND voting code)
    '06voting+null' => 'BIT_AND(v.number, 2048) > 0 AND BIT_AND(v.number, 1024) > 0', // null AND voting code
    '07null' => 'BIT_AND(v.number, 2048) > 0 AND NOT BIT_AND(v.number, 1024) > 0',  // null AND NOT voting code
    '08reddit' => 'BIT_AND(v.number, 1) > 0',
    '09tumblr' => 'BIT_AND(v.number, 2) > 0',
    // 4chan: 4
    '10sa' => 'BIT_AND(v.number, 8) > 0',
    '11neogaf' => 'BIT_AND(v.number, 16) > 0',
    '12facepunch' => 'BIT_AND(v.number, 32) > 0',
    '13infchan' => 'BIT_AND(v.number, 64) > 0',
    '14twitch' => 'BIT_AND(v.number, 128) > 0',
    '15facebook' => 'BIT_AND(v.number, 256) > 0',
    '16google' => 'BIT_AND(v.number, 512) > 0',
    '17code' => 'BIT_AND(v.number, 1024) > 0',
    '18null' => 'BIT_AND(v.number, 2048) > 0'
];

// Now we can start grabbing votes.
foreach ($filters as $filterName => $condition) {
    /** @var Category $category */
    foreach ($categories as $category) {

        $query = $voteRepo->createQueryBuilder('v')
            ->select('v.preferences')
            ->join('v.category', 'c')
            ->where('c.id = :category')
            ->setParameter('category', $category->getId());

        if ($condition) {
            $query->andWhere($condition);
        }

        $result = $query->getQuery()->getResult();
        $votes = array_filter(array_column($result, 'preferences'));

        $nominees = [];
        foreach ($category->getNominees() as $nominee) {
            $nominees[$nominee->getShortName()] = $nominee;
        }

        $resultCalculator = new Schulze($nominees, $votes);
        $result = $resultCalculator->calculateResults();

        $resultObject = new ResultCache();
        $resultObject
            ->setCategory($category)
            ->setFilter($filterName)
            ->setResults($result)
            ->setSteps($resultCalculator->getSteps())
            ->setWarnings($resultCalculator->getWarnings())
            ->setVotes(count($votes));
        $em->persist($resultObject);

        echo $timer->time() . ": [$filterName] Category complete: " . $category->getId() . "\n";
    }

    // Flush after each filter
    $em->flush();
}

echo $timer->time() . ": done.\n";
