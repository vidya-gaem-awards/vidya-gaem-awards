<?php

namespace App\Entity;

use App\Repository\IpAddressRepository;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: IpAddressRepository::class)]
class IpAddress
{
    #[ORM\Id]
    #[ORM\Column]
    private string $ip;

    #[ORM\Column]
    private DateTimeImmutable $lastUpdated;

    #[ORM\Column]
    private bool $whitelisted;

    #[ORM\Column]
    private int $abuseScore;

    #[ORM\Column(nullable: true)]
    private ?string $countryCode;

    #[ORM\Column(nullable: true)]
    private ?string $usageType;

    #[ORM\Column(nullable: true)]
    private ?string $isp;

    #[ORM\Column]
    private int $reportCount;

    public function getIp(): string
    {
        return $this->ip;
    }

    public function setIp(string $ip): IpAddress
    {
        $this->ip = $ip;
        return $this;
    }

    public function getLastUpdated(): DateTimeImmutable
    {
        return $this->lastUpdated;
    }

    public function setLastUpdated(DateTimeImmutable $lastUpdated): IpAddress
    {
        $this->lastUpdated = $lastUpdated;
        return $this;
    }

    public function isWhitelisted(): bool
    {
        return $this->whitelisted;
    }

    public function setWhitelisted(bool $whitelisted): IpAddress
    {
        $this->whitelisted = $whitelisted;
        return $this;
    }

    public function getAbuseScore(): int
    {
        return $this->abuseScore;
    }

    public function setAbuseScore(int $abuseScore): IpAddress
    {
        $this->abuseScore = $abuseScore;
        return $this;
    }

    public function getCountryCode(): ?string
    {
        return $this->countryCode;
    }

    public function setCountryCode(?string $countryCode): IpAddress
    {
        $this->countryCode = $countryCode;
        return $this;
    }

    public function getUsageType(): ?string
    {
        return $this->usageType;
    }

    public function setUsageType(?string $usageType): IpAddress
    {
        $this->usageType = $usageType;
        return $this;
    }

    public function getIsp(): ?string
    {
        return $this->isp;
    }

    public function setIsp(?string $isp): IpAddress
    {
        $this->isp = $isp;
        return $this;
    }

    public function getReportCount(): int
    {
        return $this->reportCount;
    }

    public function setReportCount(int $reportCount): IpAddress
    {
        $this->reportCount = $reportCount;
        return $this;
    }
}
