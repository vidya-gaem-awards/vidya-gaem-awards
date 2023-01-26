<?php

namespace App\Service;

use App\Entity\IpAddress;
use App\Repository\IpAddressRepository;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class AbuseIpdbService
{
    const BASE_URI = 'https://api.abuseipdb.com/api/v2/';

    public function __construct(
        private readonly string $apiKey,
        private readonly HttpClientInterface $client,
        private readonly IpAddressRepository $repo,
    ) {
    }

    public function getIpInformation(string $ipAddress)
    {
        return $this->client->request('GET', self::BASE_URI . 'check', [
            'query' => ['ipAddress' => $ipAddress],
            'headers' => [
                'Key' => $this->apiKey,
                'Accept' => 'application/json'
            ]
        ])->toArray()['data'];
    }

    public function updateIpInformation(string $ipAddress)
    {
        $ip = $this->repo->find($ipAddress);

        if (!$ip) {
            $ip = new IpAddress();
            $ip->setIp($ipAddress);
        }

        $info = $this->getIpInformation($ipAddress);

        $ip->setLastUpdated(new \DateTimeImmutable());
        $ip->setWhitelisted($info['isWhitelisted']);
        $ip->setAbuseScore($info['abuseConfidenceScore']);
        $ip->setCountryCode($info['countryCode']);
        $ip->setUsageType($info['usageType']);
        $ip->setIsp($info['isp']);
        $ip->setReportCount($info['totalReports']);
        $ip->setDomain($info['domain']);

        $this->repo->save($ip);
    }
}
